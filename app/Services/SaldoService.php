<?php

namespace App\Services;

use App\Models\PengeluaranKas;
use App\Models\Pinjaman;
use App\Models\PenarikanSimpanan;
use Illuminate\Support\Facades\DB;

/**
 * Service untuk menghitung saldo/kas koperasi secara real-time.
 * Saldo dihitung dari seluruh transaksi: simpanan masuk, angsuran, potongan, pencairan, pengembalian.
 */
class SaldoService
{
    /**
     * Hitung total saldo koperasi saat ini.
     */
    public function saldoKoperasi(): float
    {
        return $this->totalDanaMasuk() - $this->totalDanaKeluar();
    }

    /**
     * Total dana yang masuk ke kas koperasi.
     */
    public function totalDanaMasuk(): float
    {
        $simpanan = (float) DB::table('simpanan')
            ->whereNull('deleted_at')
            ->where(function ($q) {
                $q->where('status', 'aktif')->orWhereNull('status');
            })
            ->sum('nominal');

        $angsuranLunas = (float) DB::table('angsuran')
            ->where('status', 'lunas')
            ->sum(DB::raw('nominal_pokok + nominal_bunga'));

        $danaResiko = (float) DB::table('pinjaman')
            ->whereIn('status', ['berjalan', 'lunas'])
            ->sum('potongan_dana_resiko');

        $biayaAdmin = (float) DB::table('pinjaman')
            ->whereIn('status', ['berjalan', 'lunas'])
            ->sum('potongan_biaya_admin');

        return $simpanan + $angsuranLunas + $danaResiko + $biayaAdmin;
    }

    /**
     * Total dana yang keluar dari kas koperasi.
     */
    public function totalDanaKeluar(): float
    {
        // Pencairan = nominal_pinjaman BRUTO (bukan dana_diterima)
        // Karena di danaMasuk sudah menghitung SWP (simpanan), resiko, dan admin
        // sebagai arus masuk terpisah, maka arus keluar harus pakai nominal bruto
        // agar tidak terjadi double-counting potongan 5%.
        $pencairan = (float) Pinjaman::whereIn('status', ['berjalan', 'lunas'])
            ->sum('nominal_pinjaman');

        $penarikan = (float) PenarikanSimpanan::sum('nominal');

        $pengeluaran = (float) PengeluaranKas::where(function ($q) {
                $q->where('status', 'aktif')->orWhereNull('status');
            })
            ->sum('nominal');

        return $pencairan + $penarikan + $pengeluaran;
    }

    /**
     * Cek apakah saldo mencukupi untuk pinjaman tertentu.
     * Mengembalikan array [cukup, saldo_saat_ini, sisa_setelah].
     */
    public function cekKecukupanSaldo(float $nominalPinjaman): array
    {
        $saldo = $this->saldoKoperasi();
        $sisa = $saldo - ($nominalPinjaman * 0.95); // Yang keluar = 95% (dana diterima)

        return [
            'cukup' => $sisa >= 0,
            'saldo_saat_ini' => $saldo,
            'sisa_setelah_approve' => $sisa,
        ];
    }
}
