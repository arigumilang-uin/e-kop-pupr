<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\Pinjaman;
use App\Services\PengaturanService;
use App\Services\SaldoService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SimulasiKeuanganController extends Controller
{
    public function __construct(
        private SaldoService $saldo,
        private PengaturanService $pengaturan,
    ) {}

    public function index(Request $request)
    {
        $bulanSekarang = now()->month;
        $tahunSekarang = now()->year;

        // Target bulan simulasi (default: 3 bulan ke depan)
        $targetBulan = $request->input('bulan_target', now()->addMonths(3)->month);
        $targetTahun = $request->input('tahun_target', now()->addMonths(3)->year);

        $targetDate = Carbon::create($targetTahun, $targetBulan, 1);
        $currentDate = Carbon::create($tahunSekarang, $bulanSekarang, 1);

        // Pastikan target di masa depan
        if ($targetDate->lte($currentDate)) {
            $targetDate = $currentDate->copy()->addMonth();
            $targetBulan = $targetDate->month;
            $targetTahun = $targetDate->year;
        }

        // === Data Saat Ini ===
        $kasSekarang = $this->saldo->saldoKoperasi();
        $jumlahAnggotaAktif = Anggota::aktif()->count();
        $nominalWajib = $this->pengaturan->simpananWajib();
        $nominalPokok = $this->pengaturan->simpananPokok();

        // Ambil semua pinjaman berjalan beserta angsuran yang belum lunas
        $pinjamanBerjalan = Pinjaman::with(['angsuran' => function ($q) {
            $q->where('status', 'belum')->orderBy('tanggal_jatuh_tempo');
        }, 'anggota'])
            ->where('status', 'berjalan')
            ->get();

        // === Proyeksi Bulanan ===
        $proyeksi = [];
        $kumulatifKas = $kasSekarang;
        $loopDate = $currentDate->copy()->addMonth(); // Mulai dari bulan depan

        while ($loopDate->lte($targetDate)) {
            $bln = $loopDate->month;
            $thn = $loopDate->year;

            // 1. Pendapatan Simpanan Wajib (semua anggota aktif × nominal wajib)
            $pendapatanWajib = $jumlahAnggotaAktif * $nominalWajib;

            // 2. Pendapatan Angsuran Pinjaman di bulan ini
            $pendapatanAngsuran = 0;
            $detailAngsuran = [];

            foreach ($pinjamanBerjalan as $pinjaman) {
                foreach ($pinjaman->angsuran as $angs) {
                    $jatuhTempo = Carbon::parse($angs->tanggal_jatuh_tempo);
                    if ($jatuhTempo->month === $bln && $jatuhTempo->year === $thn) {
                        $pendapatanAngsuran += (float) $angs->nominal_total;
                        $detailAngsuran[] = [
                            'anggota' => $pinjaman->anggota->nama ?? 'N/A',
                            'ref' => $pinjaman->no_referensi,
                            'angsuran_ke' => $angs->angsuran_ke,
                            'nominal' => (float) $angs->nominal_total,
                        ];
                    }
                }
            }

            // 3. Total pemasukan bulan ini
            $totalMasuk = $pendapatanWajib + $pendapatanAngsuran;
            $kumulatifKas += $totalMasuk;

            $proyeksi[] = [
                'bulan' => $loopDate->translatedFormat('F Y'),
                'bulan_raw' => $loopDate->copy(),
                'pendapatan_wajib' => $pendapatanWajib,
                'pendapatan_angsuran' => $pendapatanAngsuran,
                'detail_angsuran' => $detailAngsuran,
                'total_masuk' => $totalMasuk,
                'kumulatif_kas' => $kumulatifKas,
            ];

            $loopDate->addMonth();
        }

        // === Ringkasan Proyeksi ===
        $totalPendapatanWajib = collect($proyeksi)->sum('pendapatan_wajib');
        $totalPendapatanAngsuran = collect($proyeksi)->sum('pendapatan_angsuran');
        $totalPemasukan = $totalPendapatanWajib + $totalPendapatanAngsuran;
        $kasProyeksiAkhir = $kasSekarang + $totalPemasukan;

        // Estimasi: berapa pinjaman baru yang bisa difasilitasi (asumsi rata-rata)
        $avgPinjaman = (float) DB::table('pinjaman')
            ->whereIn('status', ['berjalan', 'lunas'])
            ->avg('nominal_pinjaman') ?: 5000000;
        $kapasitasPinjaman = $kasProyeksiAkhir > 0 ? floor($kasProyeksiAkhir / $avgPinjaman) : 0;

        // Total pinjaman yang akan lunas dalam rentang simulasi
        $pinjamanAkanLunas = 0;
        foreach ($pinjamanBerjalan as $pinjaman) {
            $sisa = $pinjaman->angsuran->where('status.value', 'belum');
            if ($sisa->count() > 0) {
                $terakhir = $sisa->sortByDesc('tanggal_jatuh_tempo')->first();
                $tglLunas = Carbon::parse($terakhir->tanggal_jatuh_tempo);
                if ($tglLunas->lte($targetDate)) {
                    $pinjamanAkanLunas++;
                }
            }
        }

        $summary = compact(
            'kasSekarang', 'kasProyeksiAkhir', 'jumlahAnggotaAktif', 'nominalWajib',
            'totalPendapatanWajib', 'totalPendapatanAngsuran', 'totalPemasukan',
            'kapasitasPinjaman', 'avgPinjaman', 'pinjamanAkanLunas'
        );

        return view('keuangan.simulasi', compact(
            'proyeksi', 'summary', 'targetBulan', 'targetTahun',
            'bulanSekarang', 'tahunSekarang', 'pinjamanBerjalan'
        ));
    }
}
