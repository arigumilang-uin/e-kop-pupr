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
     * Mengkalkulasikan semua dana masuk, dana keluar, DAN saldo awal dari Parameter Neraca (Master).
     */
    public function saldoKoperasi(): float
    {
        $paramBank = \App\Models\ParameterNeraca::where('kode_otomatis', 'SALDO_BANK_BRK')->first();
        $paramKas = \App\Models\ParameterNeraca::where('kode_otomatis', 'SALDO_KAS_TUNAI')->first();
        
        $saldoAwalBank = $paramBank ? (float) $paramBank->nominal_manual : 0;
        $saldoAwalKas = $paramKas ? (float) $paramKas->nominal_manual : 0;

        return $this->totalDanaMasuk() - $this->totalDanaKeluar() + $saldoAwalBank + $saldoAwalKas;
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

        $potongan = DB::table('pinjaman')
            ->whereIn('status', ['berjalan', 'lunas'])
            ->selectRaw('SUM(potongan_dana_resiko) as resiko, SUM(potongan_biaya_admin) as admin')
            ->first();

        $pembayaran_eksternal = (float) DB::table('pembayaran_piutang_eksternal')->sum('nominal');

        return $simpanan + $angsuranLunas + (float) ($potongan->resiko ?? 0) + (float) ($potongan->admin ?? 0) + $pembayaran_eksternal;
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

        $realisasiShu = (float) \Illuminate\Support\Facades\DB::table('shu_realisasi_kewajiban')->sum('nominal');

        return $pencairan + $penarikan + $pengeluaran + $realisasiShu;
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
