<?php

namespace App\Services;

use App\Models\PengeluaranKas;
use Illuminate\Support\Facades\DB;

/**
 * Service untuk menyusun Neraca (Laporan Posisi Keuangan) Koperasi Simpan Pinjam.
 *
 * Berdasarkan Standar Akuntansi Koperasi:
 *
 * AKTIVA (Aset):
 *   1. Kas & Setara Kas
 *   2. Piutang Pokok Pinjaman Anggota
 *
 * PASIVA:
 *   I.  Kewajiban (Hutang):
 *       - Simpanan Sukarela (dapat ditarik sewaktu-waktu)
 *       - Cadangan Dana Resiko
 *
 *   II. Modal / Ekuitas:
 *       - Simpanan Pokok (tetap selama jadi anggota)
 *       - Simpanan Wajib (tetap selama jadi anggota)
 *       - Simpanan Wajib Pinjam / SWP (tetap selama jadi anggota)
 *       - SHU Tahun Berjalan (dari ShuService yang dinamis)
 *
 * Persamaan Dasar: AKTIVA = KEWAJIBAN + MODAL
 */
class NeracaService
{
    public function __construct(
        private SaldoService $saldoService,
        private ShuService $shuService,
    ) {}

    /**
     * Kode jenis simpanan yang masuk MODAL (bukan hutang).
     * Simpanan ini tidak bisa ditarik selama masih jadi anggota.
     */
    private const KODE_MODAL = ['POKOK', 'WAJIB', 'SWP'];

    /**
     * Kode jenis simpanan yang masuk KEWAJIBAN (hutang).
     * Simpanan ini secara prinsip bisa ditarik.
     */
    private const KODE_KEWAJIBAN = ['SUKARELA'];

    public function hitung(): array
    {
        // =============================================
        //  A K T I V A
        // =============================================

        $kas = $this->saldoService->saldoKoperasi();

        // Piutang = sisa POKOK pinjaman berjalan (bunga belum diakui)
        $totalPokokBerjalan = (float) DB::table('pinjaman')
            ->where('status', 'berjalan')
            ->sum('nominal_pinjaman');

        $totalPokokTerbayar = (float) DB::table('angsuran')
            ->join('pinjaman', 'angsuran.pinjaman_id', '=', 'pinjaman.id')
            ->where('pinjaman.status', 'berjalan')
            ->where('angsuran.status', 'lunas')
            ->sum('angsuran.nominal_pokok');

        $piutangPinjaman = $totalPokokBerjalan - $totalPokokTerbayar;

        $totalAktiva = $kas + $piutangPinjaman;

        // =============================================
        //  SIMPANAN — Pisahkan ke Kewajiban vs Modal
        // =============================================

        $simpananAll = DB::table('simpanan')
            ->join('jenis_simpanan', 'simpanan.jenis_simpanan_id', '=', 'jenis_simpanan.id')
            ->select(
                'jenis_simpanan.kode',
                'jenis_simpanan.nama',
                DB::raw('SUM(simpanan.nominal) as total')
            )
            ->groupBy('jenis_simpanan.kode', 'jenis_simpanan.nama')
            ->orderBy('jenis_simpanan.id')
            ->get()->keyBy('kode');

        $penarikanAll = DB::table('penarikan_simpanan')
            ->join('jenis_simpanan', 'penarikan_simpanan.jenis_simpanan_id', '=', 'jenis_simpanan.id')
            ->select(
                'jenis_simpanan.kode',
                DB::raw('SUM(penarikan_simpanan.nominal) as total')
            )
            ->groupBy('jenis_simpanan.kode')
            ->get()->keyBy('kode');

        // Kalkulasi Neto per jenis simpanan
        $simpananNeto = collect();
        foreach ($simpananAll as $kode => $s) {
            $tarik = isset($penarikanAll[$kode]) ? (float) $penarikanAll[$kode]->total : 0;
            $s->total = (float) $s->total - $tarik;
            $simpananNeto->push($s);
        }

        // Simpanan yang masuk KEWAJIBAN (Sukarela)
        $simpananKewajiban = $simpananNeto->filter(
            fn($s) => in_array($s->kode, self::KODE_KEWAJIBAN)
        );
        $totalSimpananKewajiban = (float) $simpananKewajiban->sum('total');

        // Simpanan yang masuk MODAL (Pokok, Wajib, SWP)
        $simpananModal = $simpananNeto->filter(
            fn($s) => in_array($s->kode, self::KODE_MODAL)
        );
        $totalSimpananModal = (float) $simpananModal->sum('total');

        // =============================================
        //  I. K E W A J I B A N
        // =============================================

        // Dana Resiko = potongan 1.5% yang dicadangkan
        $danaResiko = (float) DB::table('pinjaman')
            ->whereIn('status', ['berjalan', 'lunas'])
            ->sum('potongan_dana_resiko');

        $totalKewajiban = $totalSimpananKewajiban + $danaResiko;

        // =============================================
        //  II. M O D A L / E K U I T A S
        // =============================================

        // a) Simpanan Modal (Pokok + Wajib + SWP)
        // b) SHU Tahun Berjalan — dari ShuService (dinamis, sesuai konfigurasi pengurus)
        $shuData = $this->shuService->hitung((int) date('Y'));
        $shuBerjalan = $shuData['shu_bersih'];

        $totalModal = $totalSimpananModal + $shuBerjalan;

        // =============================================
        //  P A S I V A
        // =============================================
        $totalPasiva = $totalKewajiban + $totalModal;

        // Balance check
        $selisih = round($totalAktiva - $totalPasiva, 2);
        $isBalance = abs($selisih) < 0.01;

        return [
            'tanggal' => now()->toDateString(),

            // AKTIVA
            'aktiva' => [
                'kas' => $kas,
                'piutang_pinjaman' => $piutangPinjaman,
                'piutang_detail' => [
                    'total_pokok' => $totalPokokBerjalan,
                    'pokok_terbayar' => $totalPokokTerbayar,
                ],
                'total' => $totalAktiva,
            ],

            // KEWAJIBAN
            'kewajiban' => [
                'simpanan_items' => $simpananKewajiban,
                'total_simpanan_kewajiban' => $totalSimpananKewajiban,
                'dana_resiko' => $danaResiko,
                'total' => $totalKewajiban,
            ],

            // MODAL
            'modal' => [
                'simpanan_items' => $simpananModal,
                'total_simpanan_modal' => $totalSimpananModal,
                'shu_berjalan' => $shuBerjalan,
                'total' => $totalModal,
            ],

            // PASIVA
            'pasiva' => [
                'total' => $totalPasiva,
            ],

            // BALANCE CHECK
            'selisih' => $selisih,
            'is_balance' => $isBalance,
        ];
    }
}
