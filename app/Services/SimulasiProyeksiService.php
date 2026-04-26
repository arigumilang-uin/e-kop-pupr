<?php

namespace App\Services;

use App\Models\Anggota;
use App\Models\Pinjaman;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SimulasiProyeksiService
{
    public function __construct(
        private SaldoService $saldo,
        private PengaturanService $pengaturan,
    ) {}

    /**
     * Hitung proyeksi keuangan bulanan dari bulan depan sampai target.
     */
    public function hitung(int $targetBulan, int $targetTahun): array
    {
        $bulanSekarang = now()->month;
        $tahunSekarang = now()->year;

        $targetDate = Carbon::create($targetTahun, $targetBulan, 1);
        $currentDate = Carbon::create($tahunSekarang, $bulanSekarang, 1);

        // Pastikan target di masa depan
        if ($targetDate->lte($currentDate)) {
            $targetDate = $currentDate->copy()->addMonth();
            $targetBulan = $targetDate->month;
            $targetTahun = $targetDate->year;
        }

        $kasSekarang = $this->saldo->saldoKoperasi();
        $jumlahAnggotaAktif = Anggota::aktif()->count();
        $nominalWajib = $this->pengaturan->simpananWajib();

        // Ambil pinjaman berjalan + angsuran belum lunas
        $pinjamanBerjalan = Pinjaman::with(['angsuran' => function ($q) {
            $q->where('status', 'belum')->orderBy('tanggal_jatuh_tempo');
        }, 'anggota'])
            ->where('status', 'berjalan')
            ->get();

        // === Proyeksi Bulanan ===
        $proyeksi = [];
        $kumulatifKas = $kasSekarang;
        $loopDate = $currentDate->copy()->addMonth();

        while ($loopDate->lte($targetDate)) {
            $bln = $loopDate->month;
            $thn = $loopDate->year;

            $pendapatanWajib = $jumlahAnggotaAktif * $nominalWajib;

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

        $avgPinjaman = (float) DB::table('pinjaman')
            ->whereIn('status', ['berjalan', 'lunas'])
            ->avg('nominal_pinjaman') ?: 5000000;
        $kapasitasPinjaman = $kasProyeksiAkhir > 0 ? floor($kasProyeksiAkhir / $avgPinjaman) : 0;

        $pinjamanAkanLunas = 0;
        foreach ($pinjamanBerjalan as $pinjaman) {
            $sisa = $pinjaman->angsuran->where('status.value', 'belum');
            if ($sisa->count() > 0) {
                $terakhir = $sisa->sortByDesc('tanggal_jatuh_tempo')->first();
                if (Carbon::parse($terakhir->tanggal_jatuh_tempo)->lt($targetDate)) {
                    $pinjamanAkanLunas++;
                }
            }
        }

        $summary = compact(
            'kasSekarang', 'kasProyeksiAkhir', 'jumlahAnggotaAktif', 'nominalWajib',
            'totalPendapatanWajib', 'totalPendapatanAngsuran', 'totalPemasukan',
            'kapasitasPinjaman', 'avgPinjaman', 'pinjamanAkanLunas'
        );

        return compact(
            'proyeksi', 'summary', 'targetBulan', 'targetTahun',
            'bulanSekarang', 'tahunSekarang', 'pinjamanBerjalan'
        );
    }
}
