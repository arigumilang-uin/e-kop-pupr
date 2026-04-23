<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\JenisSimpanan;
use App\Services\ActivityLogService;
use App\Services\PengaturanService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;

class PotonganBulananController extends Controller
{
    public function __construct(
        private ActivityLogService $logger,
        private PengaturanService $pengaturan,
    ) {}

    public function index(Request $request)
    {
        // Parameter Bulan dan Tahun (default ke bulan/tahun saat ini)
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        // Ambil pengaturan nominal dari PengaturanService (sinkron dengan halaman Pengaturan)
        $jenisPokok = JenisSimpanan::pokok();
        $jenisWajib = JenisSimpanan::wajib();

        $nominalPokok = $this->pengaturan->simpananPokok();
        $nominalWajib = $this->pengaturan->simpananWajib();

        // Ambil semua anggota aktif
        // Optimization: Pre-load all required data nicely
        $anggotas = Anggota::aktif()
            ->with(['bidang'])
            ->orderBy('nama')
            ->get();

        $dataPotongan = collect();
        $totalKeseluruhan = 0;

        foreach ($anggotas as $anggota) {
            // == Cek kelayakan periode: Anggota hanya dikenakan potongan
            // mulai bulan SETELAH tanggal_masuk mereka.
            // Contoh: masuk 15 Maret 2026 -> potongan mulai April 2026.
            $tanggalMasuk = $anggota->tanggal_masuk;
            $bulanMulaiPotongan = $tanggalMasuk ? $tanggalMasuk->copy()->addMonth()->startOfMonth() : null;
            $periodeFilter = Carbon::createFromDate($year, $month, 1);

            $belumWaktunyaDipotong = $bulanMulaiPotongan && $periodeFilter->lt($bulanMulaiPotongan);

            // 1. Hitung Potongan Pokok
            $sudahBayarPokok = DB::table('simpanan')
                ->where('anggota_id', $anggota->id)
                ->where('jenis_simpanan_id', $jenisPokok->id ?? 0)
                ->exists();

            $potonganPokok = ($sudahBayarPokok || $belumWaktunyaDipotong) ? 0 : $nominalPokok;
            
            // 2. Hitung Potongan Wajib
            $sudahBayarWajib = DB::table('simpanan')
                ->where('anggota_id', $anggota->id)
                ->where('jenis_simpanan_id', $jenisWajib->id ?? 0)
                ->where('bulan_untuk', $month)
                ->where('tahun_untuk', $year)
                ->exists();

            $potonganWajib = ($sudahBayarWajib || $belumWaktunyaDipotong) ? 0 : $nominalWajib;

            // 3. Hitung Potongan Pinjaman
            // Cari angsuran bulan ini yang BELUM lunas
            $potonganPinjaman = DB::table('angsuran')
                ->join('pinjaman', 'angsuran.pinjaman_id', '=', 'pinjaman.id')
                ->where('pinjaman.anggota_id', $anggota->id)
                ->where('angsuran.status', 'belum')
                ->whereMonth('angsuran.tanggal_jatuh_tempo', $month)
                ->whereYear('angsuran.tanggal_jatuh_tempo', $year)
                ->sum('angsuran.nominal_total');

            // 4. Kalkulasi Total per Anggota
            $totalPotongan = $potonganPokok + $potonganWajib + $potonganPinjaman;
            
            $totalKeseluruhan += $totalPotongan;

            $dataPotongan->push((object)[
                'anggota' => $anggota,
                'potongan_pokok' => $potonganPokok,
                'potongan_wajib' => $potonganWajib,
                'potongan_pinjaman' => $potonganPinjaman,
                'total_potongan' => $totalPotongan,
            ]);
        }

        // Generate Array of Months untuk dropdown
        $months = [];
        for ($m = 1; $m <= 12; $m++) {
            $months[$m] = date('F', mktime(0, 0, 0, $m, 1));
        }
        
        $years = range(now()->year - 2, now()->year + 2);

        return view('potongan.index', compact('dataPotongan', 'month', 'year', 'months', 'years', 'totalKeseluruhan'));
    }

    public function proses(Request $request)
    {
        $month = $request->input('month');
        $year = $request->input('year');

        if (!$month || !$year) {
            return back()->with('error', 'Bulan dan tahun tidak valid.');
        }

        try {
            DB::beginTransaction();

            $jenisPokok = JenisSimpanan::pokok();
            $jenisWajib = JenisSimpanan::wajib();
            $nominalPokok = $this->pengaturan->simpananPokok();
            $nominalWajib = $this->pengaturan->simpananWajib();

            $anggotas = Anggota::aktif()->get();
            $countProcessed = 0;

            foreach ($anggotas as $anggota) {
                // == Cek kelayakan periode berdasar tanggal masuk
                $tanggalMasuk = $anggota->tanggal_masuk;
                $bulanMulaiPotongan = $tanggalMasuk ? $tanggalMasuk->copy()->addMonth()->startOfMonth() : null;
                $periodeFilter = Carbon::createFromDate($year, $month, 1);
                $belumWaktunyaDipotong = $bulanMulaiPotongan && $periodeFilter->lt($bulanMulaiPotongan);

                // 1. Simpanan Pokok
                $sudahBayarPokok = DB::table('simpanan')
                    ->where('anggota_id', $anggota->id)
                    ->where('jenis_simpanan_id', $jenisPokok->id ?? 0)
                    ->exists();

                if (!$sudahBayarPokok && !$belumWaktunyaDipotong) {
                    \App\Models\Simpanan::create([
                        'anggota_id' => $anggota->id,
                        'jenis_simpanan_id' => $jenisPokok->id,
                        'nominal' => $nominalPokok,
                        'tanggal' => now(),
                        'bulan_untuk' => $month,
                        'tahun_untuk' => $year,
                        'keterangan' => "Potongan TPP Masal (Pokok) - Periode $month/$year",
                        'dicatat_oleh' => auth()->id(),
                    ]);
                }

                // 2. Simpanan Wajib
                $sudahBayarWajib = DB::table('simpanan')
                    ->where('anggota_id', $anggota->id)
                    ->where('jenis_simpanan_id', $jenisWajib->id ?? 0)
                    ->where('bulan_untuk', $month)
                    ->where('tahun_untuk', $year)
                    ->exists();

                if (!$sudahBayarWajib && !$belumWaktunyaDipotong) {
                    \App\Models\Simpanan::create([
                        'anggota_id' => $anggota->id,
                        'jenis_simpanan_id' => $jenisWajib->id,
                        'nominal' => $nominalWajib,
                        'tanggal' => now(),
                        'bulan_untuk' => $month,
                        'tahun_untuk' => $year,
                        'keterangan' => "Potongan TPP Masal (Wajib) - Periode $month/$year",
                        'dicatat_oleh' => auth()->id(),
                    ]);
                }

                // 3. Angsuran Pinjaman
                $angsurans = \App\Models\Angsuran::join('pinjaman', 'angsuran.pinjaman_id', '=', 'pinjaman.id')
                    ->where('pinjaman.anggota_id', $anggota->id)
                    ->where('angsuran.status', \App\Enums\StatusAngsuran::Belum)
                    ->whereMonth('angsuran.tanggal_jatuh_tempo', $month)
                    ->whereYear('angsuran.tanggal_jatuh_tempo', $year)
                    ->select('angsuran.*')
                    ->get();

                foreach ($angsurans as $angs) {
                    $angs->update([
                        'status' => \App\Enums\StatusAngsuran::Lunas,
                        'tanggal_bayar' => now(),
                    ]);

                    // Check if pinjaman should be set to lunas
                    $pinjaman = $angs->pinjaman;
                    $belumLunasCount = $pinjaman->angsuran()->where('status', \App\Enums\StatusAngsuran::Belum)->count();
                    if ($belumLunasCount === 0) {
                        $pinjaman->update(['status' => \App\Enums\StatusPinjaman::Lunas]);
                    }
                }
                
                $countProcessed++;
            }

            $this->logger->log(
                'tpp_processed',
                "Pemrosesan TPP Massal berhasil untuk periode $month/$year. Total $countProcessed anggota diproses.",
                ['month' => $month, 'year' => $year]
            );

            DB::commit();

            return back()->with('success', "Pemrosesan TPP periode $month/$year berhasil dibukukan.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses TPP: ' . $e->getMessage());
        }
    }
}
