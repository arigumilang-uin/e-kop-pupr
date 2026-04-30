<?php

namespace App\Services;

use App\Models\PengeluaranKas;
use Illuminate\Support\Facades\DB;

/**
 * Service untuk menyusun Neraca (Laporan Posisi Keuangan) Koperasi Simpan Pinjam.
 *
 * AKTIVA (Aset):
 *   1. Kas & Setara Kas
 *   2. Piutang Pokok Pinjaman Anggota
 *
 * PASIVA:
 *   I.  Kewajiban (Hutang):
 *       - Simpanan Tahun 2025 (Pra-rilis)
 *       - Bonus SHU (Hasil distribusi SHU)
 *       - Cadangan Dana Resiko
 *
 *   II. Modal / Ekuitas:
 *       - Simpanan Pokok, Wajib, SWP
 *       - Laba Ditahan (Pendapatan Terealisasi - Beban)
 *
 * Persamaan: AKTIVA = KEWAJIBAN + MODAL
 */
class NeracaService
{
    public function __construct(
        private SaldoService $saldoService,
    ) {}

    private const KODE_MODAL = ['POKOK', 'WAJIB', 'SWP'];
    private const KODE_KEWAJIBAN = ['SIM2025', 'BONUS_SHU'];

    public function hitung(): array
    {
        // =============================================
        //  A K T I V A
        // =============================================

        $kas = $this->saldoService->saldoKoperasi();

        // Piutang = Sisa Pokok saja (cash basis: bunga diakui saat dibayar)
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

        $simpananNeto = collect();
        foreach ($simpananAll as $kode => $s) {
            $tarik = isset($penarikanAll[$kode]) ? (float) $penarikanAll[$kode]->total : 0;
            $s->total = (float) $s->total - $tarik;
            $simpananNeto->push($s);
        }

        $simpananKewajiban = $simpananNeto->filter(
            fn($s) => in_array($s->kode, self::KODE_KEWAJIBAN)
        );
        $totalSimpananKewajiban = (float) $simpananKewajiban->sum('total');

        $simpananModal = $simpananNeto->filter(
            fn($s) => in_array($s->kode, self::KODE_MODAL)
        );
        $totalSimpananModal = (float) $simpananModal->sum('total');

        // =============================================
        //  I. K E W A J I B A N
        // =============================================

        $danaResiko = (float) DB::table('pinjaman')
            ->whereIn('status', ['berjalan', 'lunas'])
            ->sum('potongan_dana_resiko');

        $totalKewajiban = $totalSimpananKewajiban + $danaResiko;

        // =============================================
        //  II. M O D A L / E K U I T A S
        // =============================================

        // Pendapatan TEREALISASI (sudah masuk kas secara riil):
        // 1. Bunga angsuran yang sudah dibayar (status lunas)
        $pendapatanBunga = (float) DB::table('angsuran')
            ->where('status', 'lunas')
            ->sum('nominal_bunga');

        // 2. Biaya admin dari pinjaman yang sudah dicairkan
        $pendapatanBiayaAdmin = (float) DB::table('pinjaman')
            ->whereIn('status', ['berjalan', 'lunas'])
            ->sum('potongan_biaya_admin');

        // Beban (pengeluaran kas manual)
        $totalBeban = (float) PengeluaranKas::sum('nominal');

        // Laba Ditahan = Pendapatan Terealisasi - Beban
        $labaDitahan = $pendapatanBunga + $pendapatanBiayaAdmin - $totalBeban;

        $totalModal = $totalSimpananModal + $labaDitahan;

        // =============================================
        //  P A S I V A
        // =============================================
        $totalPasiva = $totalKewajiban + $totalModal;

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
                'laba_ditahan' => $labaDitahan,
                'detail_laba' => [
                    'pendapatan_bunga' => $pendapatanBunga,
                    'pendapatan_biaya_admin' => $pendapatanBiayaAdmin,
                    'total_beban' => $totalBeban,
                ],
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
