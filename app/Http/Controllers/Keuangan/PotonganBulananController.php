<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\JenisSimpanan;
use App\Services\PengaturanService;
use App\Services\PotonganService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PotonganBulananController extends Controller
{
    public function __construct(
        private PengaturanService $pengaturan,
        private PotonganService $potonganService,
    ) {}

    public function index(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);
        $search = $request->input('q');
        $bidangId = $request->input('bidang');
        $jenisFilter = $request->input('jenis');

        $jenisPokok = JenisSimpanan::pokok();
        $jenisWajib = JenisSimpanan::wajib();

        $nominalPokok = $this->pengaturan->simpananPokok();
        $nominalWajib = $this->pengaturan->simpananWajib((int) $month, (int) $year);

        $query = Anggota::aktif()->with(['bidang'])->orderBy('nama');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%");
            });
        }

        if ($bidangId) {
            $query->where('bidang_id', $bidangId);
        }

        $anggotas = $query->get();

        $dataPotongan = collect();
        $totalKeseluruhan = 0;
        $totalPokok = 0;
        $totalWajib = 0;
        $totalPinjaman = 0;

        // --- OPTIMIZATION BATCH QUERIES ---
        // 1. Ambil array ID anggota yang sudah bayar pokok
        $paidPokokIds = [];
        if (!$jenisFilter || $jenisFilter === 'pokok') {
            $paidPokokIds = DB::table('simpanan')
                ->where('jenis_simpanan_id', $jenisPokok->id ?? 0)
                ->whereNull('deleted_at')
                ->where(function ($q) { $q->where('status', 'aktif')->orWhereNull('status'); })
                ->pluck('anggota_id')
                ->toArray();
        }

        // 1.5. Ambil array ID anggota yang memiliki simpanan 2025 (pokok dianggap lunas)
        $jenisSim2025 = \App\Models\JenisSimpanan::sim2025();
        $hasSim2025Ids = [];
        if ($jenisSim2025 && (!$jenisFilter || $jenisFilter === 'pokok')) {
            $hasSim2025Ids = DB::table('simpanan')
                ->where('jenis_simpanan_id', $jenisSim2025->id)
                ->whereNull('deleted_at')
                ->where(function ($q) { $q->where('status', 'aktif')->orWhereNull('status'); })
                ->pluck('anggota_id')
                ->toArray();
        }

        // 2. Ambil array ID anggota yang sudah bayar wajib bulan & tahun ini
        $paidWajibIds = [];
        if (!$jenisFilter || $jenisFilter === 'wajib') {
            $paidWajibIds = DB::table('simpanan')
                ->where('jenis_simpanan_id', $jenisWajib->id ?? 0)
                ->where('bulan_untuk', $month)
                ->where('tahun_untuk', $year)
                ->whereNull('deleted_at')
                ->where(function ($q) { $q->where('status', 'aktif')->orWhereNull('status'); })
                ->pluck('anggota_id')
                ->toArray();
        }

        // 3. Ambil seluruh data angsuran belum bayar untuk bulan & tahun ini, grouped by anggota_id
        $allPinjamanData = collect();
        if (!$jenisFilter || $jenisFilter === 'pinjaman') {
            $allPinjamanData = DB::table('angsuran')
                ->join('pinjaman', 'angsuran.pinjaman_id', '=', 'pinjaman.id')
                ->where('angsuran.status', 'belum')
                ->whereMonth('angsuran.tanggal_jatuh_tempo', $month)
                ->whereYear('angsuran.tanggal_jatuh_tempo', $year)
                ->select(
                    'pinjaman.anggota_id',
                    'angsuran.angsuran_ke as urutan_angsuran',
                    'pinjaman.tenor_bulan as lama_angsuran',
                    'angsuran.nominal_pokok',
                    'angsuran.nominal_bunga',
                    'angsuran.nominal_total',
                    DB::raw('"Pinjaman" as nama_pinjaman')
                )
                ->get()
                ->groupBy('anggota_id');
        }

        // PERHITUNGAN RAM LOKAL
        foreach ($anggotas as $anggota) {
            // Tentukan bulan mulai potongan TPP POKOK
            if ($anggota->tpp_mulai_pokok && $anggota->tpp_tahun_pokok) {
                $mulaiPotongPokok = Carbon::createFromDate($anggota->tpp_tahun_pokok, $anggota->tpp_mulai_pokok, 1);
            } else {
                $mulaiPotongPokok = $anggota->tanggal_masuk ? $anggota->tanggal_masuk->copy()->addMonth()->startOfMonth() : null;
            }

            // Tentukan bulan mulai potongan TPP WAJIB
            if ($anggota->tpp_mulai_wajib && $anggota->tpp_tahun_wajib) {
                $mulaiPotongWajib = Carbon::createFromDate($anggota->tpp_tahun_wajib, $anggota->tpp_mulai_wajib, 1);
            } else {
                $mulaiPotongWajib = $anggota->tanggal_masuk ? $anggota->tanggal_masuk->copy()->addMonth()->startOfMonth() : null;
            }

            $periodeFilter = Carbon::createFromDate($year, $month, 1);
            $belumWaktuPotongPokok = $mulaiPotongPokok && $periodeFilter->lt($mulaiPotongPokok);
            $belumWaktuPotongWajib = $mulaiPotongWajib && $periodeFilter->lt($mulaiPotongWajib);

            $potonganPokok = 0;
            if (!$jenisFilter || $jenisFilter === 'pokok') {
                $sudahBayarPokok = in_array($anggota->id, $paidPokokIds);
                $memilikiSim2025 = in_array($anggota->id, $hasSim2025Ids);
                $potonganPokok = ($sudahBayarPokok || $belumWaktuPotongPokok || $memilikiSim2025) ? 0 : $nominalPokok;
            }

            $potonganWajib = 0;
            if (!$jenisFilter || $jenisFilter === 'wajib') {
                $sudahBayarWajib = in_array($anggota->id, $paidWajibIds);
                $potonganWajib = ($sudahBayarWajib || $belumWaktuPotongWajib) ? 0 : $nominalWajib;
            }

            $potonganPinjaman = 0;
            $pinjamanPokok = 0;
            $pinjamanBunga = 0;
            $detailPinjaman = collect();

            if (!$jenisFilter || $jenisFilter === 'pinjaman') {
                if ($allPinjamanData->has($anggota->id)) {
                    $pinjamanData = $allPinjamanData->get($anggota->id);
                    $pinjamanPokok = $pinjamanData->sum('nominal_pokok');
                    $pinjamanBunga = $pinjamanData->sum('nominal_bunga');
                    $potonganPinjaman = $pinjamanData->sum('nominal_total');
                    $detailPinjaman = $pinjamanData;
                }
            }

            $totalPotongan = $potonganPokok + $potonganWajib + $potonganPinjaman;
            
            if ($totalPotongan > 0 || !$jenisFilter) {
                if ($jenisFilter && $totalPotongan <= 0) continue;

                $totalKeseluruhan += $totalPotongan;
                $totalPokok += $potonganPokok;
                $totalWajib += $potonganWajib;
                $totalPinjaman += $potonganPinjaman;

                $dataPotongan->push((object)[
                    'anggota' => $anggota,
                    'potongan_pokok' => $potonganPokok,
                    'potongan_wajib' => $potonganWajib,
                    'potongan_pinjaman' => $potonganPinjaman,
                    'pinjaman_pokok' => $pinjamanPokok,
                    'pinjaman_bunga' => $pinjamanBunga,
                    'detail_pinjaman' => $detailPinjaman,
                    'total_potongan' => $totalPotongan,
                ]);
            }
        }

        // Menambahkan pagination statis dari collection untuk tampilan efektif
        $page = $request->input('page', 1);
        $perPage = 15;
        $paginatedData = new \Illuminate\Pagination\LengthAwarePaginator(
            $dataPotongan->forPage($page, $perPage),
            $dataPotongan->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $months = [];
        for ($m = 1; $m <= 12; $m++) {
            $months[$m] = date('F', mktime(0, 0, 0, $m, 1));
        }
        $years = range(now()->year - 2, now()->year + 2);
        $bidangs = \App\Models\Bidang::orderBy('nama_bidang')->get();

        return view('potongan.index', compact(
            'paginatedData',
            'dataPotongan',
            'totalKeseluruhan',
            'totalPokok',
            'totalWajib',
            'totalPinjaman',
            'months',
            'years',
            'bidangs',
            'month',
            'year'
        ));
    }

    public function proses(Request $request)
    {
        $month = $request->input('month');
        $year = $request->input('year');

        if (!$month || !$year) {
            return back()->with('error', 'Bulan dan tahun tidak valid.');
        }

        try {
            $countProcessed = $this->potonganService->proses((int) $month, (int) $year, auth()->id());

            return back()->with('success', "Pemrosesan TPP periode $month/$year berhasil dibukukan. ($countProcessed anggota)");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memproses TPP: ' . $e->getMessage());
        }
    }

    public function exportExcel(Request $request)
    {
        $export = new \App\Exports\PotonganExport($request);
        return $export->download();
    }

    public function exportPdf(Request $request)
    {
        $export = new \App\Exports\PotonganExport($request);
        $data = $export->getData();

        // Cek arsip laporan
        $dataHash = md5('Potongan TPP' . 'PDF' . json_encode($data));
        $existings = \App\Models\ArsipLaporan::where('data_hash', $dataHash)->get();
        foreach ($existings as $existing) {
            if (\Illuminate\Support\Facades\Storage::exists($existing->file_path)) {
                return \Illuminate\Support\Facades\Storage::download($existing->file_path, $existing->nama_file);
            } else {
                $existing->delete();
            }
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.potongan-pdf', [
            'rows' => $data['rows'],
            'grandTotals' => $data['grandTotals'],
            'filterYear' => $data['filterYear'],
            'filterInfo' => $data['filterInfo'],
        ])->setPaper('a4', 'landscape');

        $filename = 'Laporan_Potongan_TPP_' . now()->format('Ymd_His') . '.pdf';
        $path = 'arsip_laporan/' . $filename;
        
        \Illuminate\Support\Facades\Storage::makeDirectory('arsip_laporan');
        \Illuminate\Support\Facades\Storage::put($path, $pdf->output());
        
        \App\Models\ArsipLaporan::create([
            'tipe_laporan' => 'Potongan TPP',
            'format' => 'PDF',
            'nama_file' => $filename,
            'file_path' => $path,
            'data_hash' => $dataHash,
            'filter_info' => $data['filterInfo'],
            'dibuat_oleh' => auth()->id(),
        ]);

        return \Illuminate\Support\Facades\Storage::download($path, $filename);
    }
}
