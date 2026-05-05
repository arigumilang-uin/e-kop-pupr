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
        $nominalWajib = $this->pengaturan->simpananWajib();

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
            $tanggalMasuk = $anggota->tanggal_masuk;
            $bulanMulaiPotongan = $tanggalMasuk ? $tanggalMasuk->copy()->addMonth()->startOfMonth() : null;
            $periodeFilter = Carbon::createFromDate($year, $month, 1);
            $belumWaktunyaDipotong = $bulanMulaiPotongan && $periodeFilter->lt($bulanMulaiPotongan);

            $potonganPokok = 0;
            if (!$jenisFilter || $jenisFilter === 'pokok') {
                $sudahBayarPokok = in_array($anggota->id, $paidPokokIds);
                $potonganPokok = ($sudahBayarPokok || $belumWaktunyaDipotong) ? 0 : $nominalPokok;
            }

            $potonganWajib = 0;
            if (!$jenisFilter || $jenisFilter === 'wajib') {
                $sudahBayarWajib = in_array($anggota->id, $paidWajibIds);
                $potonganWajib = ($sudahBayarWajib || $belumWaktunyaDipotong) ? 0 : $nominalWajib;
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

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.potongan-pdf', [
            'rows' => $data['rows'],
            'grandTotals' => $data['grandTotals'],
            'filterYear' => $data['filterYear'],
            'filterInfo' => $data['filterInfo'],
        ])->setPaper('a4', 'landscape');

        $filename = 'Laporan_Potongan_TPP_' . now()->format('Y-m-d_His') . '.pdf';
        return $pdf->download($filename);
    }
}
