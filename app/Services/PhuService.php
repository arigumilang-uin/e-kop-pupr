<?php

namespace App\Services;

use App\Models\ParameterPhu;
use App\Models\PengeluaranKas;
use Illuminate\Support\Facades\DB;

/**
 * PhuService — Perhitungan Hasil Usaha (Laba/Rugi) Koperasi.
 *
 * Menggunakan arsitektur Dynamic Parameter (ParameterPhu).
 * Jika sumber_data = otomatis, maka akan diproses oleh resolver map.
 */
class PhuService
{
    /**
     * Hitung PHU untuk periode tertentu.
     *
     * @param int|null $tahun Tahun buku (default: tahun berjalan)
     */
    public function hitung(?int $tahun = null): array
    {
        $tahun = $tahun ?? now()->year;
        $startDate = "{$tahun}-01-01";
        $endDate   = "{$tahun}-12-31";

        // =================================================================
        //  I. P E N D A P A T A N
        // =================================================================
        $pendapatanParams = ParameterPhu::byTipe('pendapatan', $tahun);
        $pendapatanItems = [];
        $totalPendapatan = 0;

        foreach ($pendapatanParams as $param) {
            $nominal = $param->isManual() 
                ? (float) $param->nominal_manual 
                : $this->resolve($param->kode_otomatis, $tahun, $startDate, $endDate);

            $pendapatanItems[] = [
                'nama' => $param->nama,
                'nominal' => $nominal
            ];
            $totalPendapatan += $nominal;
        }

        // =================================================================
        //  II. B E B A N   O P E R A S I O N A L
        // =================================================================
        $bebanParams = ParameterPhu::byTipe('beban', $tahun);
        $bebanItems = [];
        $totalBeban = 0;

        foreach ($bebanParams as $param) {
            $nominal = $param->isManual() 
                ? (float) $param->nominal_manual 
                : $this->resolve($param->kode_otomatis, $tahun, $startDate, $endDate);

            $bebanItems[] = [
                'nama' => $param->nama,
                'nominal' => $nominal
            ];
            $totalBeban += $nominal;
        }

        // =================================================================
        //  S H U
        // =================================================================

        $shuSebelumPajak = $totalPendapatan - $totalBeban;
        $pajakShu = 0; // TODO: Jika pajak dikelola dinamis, tambahkan tipenya
        $shuBersih = $shuSebelumPajak - $pajakShu;

        return [
            'tahun' => $tahun,

            'pendapatan' => [
                'items' => $pendapatanItems,
                'total' => $totalPendapatan,
            ],

            'beban' => [
                'items' => $bebanItems,
                'total' => $totalBeban,
            ],

            'shu_sebelum_pajak' => $shuSebelumPajak,
            'pajak_shu'         => $pajakShu,
            'shu_bersih'        => $shuBersih,
        ];
    }

    /**
     * Map kode_otomatis ke perhitungan riil database.
     */
    private function resolve(?string $kode, int $tahun, string $startDate, string $endDate): float
    {
        if (!$kode) return 0;

        return match ($kode) {
            'PENDAPATAN_JASA_PINJAMAN' => (float) DB::table('angsuran')
                ->join('pinjaman', 'angsuran.pinjaman_id', '=', 'pinjaman.id')
                ->where('angsuran.status', 'lunas')
                ->whereBetween('angsuran.tanggal_bayar', [$startDate, $endDate])
                ->sum('angsuran.nominal_bunga'),

            'PENDAPATAN_PROVISI' => (float) DB::table('pinjaman')
                ->whereIn('status', ['berjalan', 'lunas'])
                ->whereYear('tanggal_approval', $tahun)
                ->sum('potongan_biaya_admin'),

            'PENDAPATAN_DANA_RESIKO' => (float) DB::table('pinjaman')
                ->whereIn('status', ['berjalan', 'lunas'])
                ->whereYear('tanggal_approval', $tahun)
                ->sum('potongan_dana_resiko'),

            'PHU_BEBAN_OPERASIONAL' => (float) DB::table('pengeluaran_kas')
                ->join('kategori_pengeluaran', 'pengeluaran_kas.kategori_pengeluaran_id', '=', 'kategori_pengeluaran.id')
                ->where('kategori_pengeluaran.jenis', 'beban')
                ->where(function ($q) {
                    $q->where('pengeluaran_kas.status', 'aktif')->orWhereNull('pengeluaran_kas.status');
                })
                ->whereBetween('pengeluaran_kas.tanggal', [$startDate, $endDate])
                ->sum('pengeluaran_kas.nominal'),

            default => 0,
        };
    }
}
