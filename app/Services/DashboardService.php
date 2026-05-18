<?php

namespace App\Services;

use App\Models\Anggota;
use App\Models\Pinjaman;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function __construct(
        private SaldoService $saldo,
        private PiutangEksternalService $piutangEksternalService,
        private NeracaService $neracaService,
    ) {}

    /**
     * Hitung seluruh statistik dashboard.
     * Semua agregat dihitung sekali — tidak ada duplikasi query.
     */
    public function getStats(): array
    {
        // === Pemasukan (4 query) ===
        $simpanan_all = (float) DB::table('simpanan')
            ->whereNull('deleted_at')
            ->where(function ($q) { $q->where('status', 'aktif')->orWhereNull('status'); })
            ->sum('nominal');

        $angsuran = DB::table('angsuran')->where('status', 'lunas')
            ->selectRaw('SUM(nominal_pokok) as pokok, SUM(nominal_bunga) as bunga, SUM(nominal_pokok + nominal_bunga) as total')
            ->first();
        $angsuran_pokok = (float) ($angsuran->pokok ?? 0);
        $angsuran_bunga = (float) ($angsuran->bunga ?? 0);
        $angsuran_total = (float) ($angsuran->total ?? 0);

        $pembayaran_eksternal = (float) DB::table('pembayaran_piutang_eksternal')->sum('nominal');

        $potongan = DB::table('pinjaman')->whereIn('status', ['berjalan', 'lunas'])
            ->selectRaw('SUM(potongan_dana_resiko) as resiko, SUM(potongan_biaya_admin) as admin, SUM(potongan_dana_resiko + potongan_biaya_admin) as total, SUM(nominal_pinjaman) as pencairan')
            ->first();
        $pendapatan_potongan = (float) ($potongan->total ?? 0);
        $dana_cair_pinjaman = (float) ($potongan->pencairan ?? 0);

        // === Pengeluaran (2 query) ===
        $tarik_simpanan = (float) DB::table('penarikan_simpanan')->sum('nominal');
        $keluar_pengeluaran_kas_brk = (float) DB::table('pengeluaran_kas')
            ->whereNull('deleted_at')
            ->where(function ($q) { $q->where('status', 'aktif')->orWhereNull('status'); })
            ->where(function ($q) { $q->where('sumber_dana', 'brk')->orWhereNull('sumber_dana'); })
            ->sum('nominal');

        $keluar_pengeluaran_kas_tunai = (float) DB::table('pengeluaran_kas')
            ->whereNull('deleted_at')
            ->where(function ($q) { $q->where('status', 'aktif')->orWhereNull('status'); })
            ->where('sumber_dana', 'kas')
            ->sum('nominal');

        $keluar_pengeluaran_kas = $keluar_pengeluaran_kas_brk + $keluar_pengeluaran_kas_tunai;

        // === Mutasi Kas (Pindah Buku) ===
        $mutasi_brk_ke_kas = (float) DB::table('mutasi_rekening')->where('jenis_mutasi', 'brk_ke_kas')->sum('nominal');
        $mutasi_kas_ke_brk = (float) DB::table('mutasi_rekening')->where('jenis_mutasi', 'kas_ke_brk')->sum('nominal');

        // Parameter Manual (Saldo Awal)
        $paramBank = \App\Models\ParameterNeraca::where('kode_otomatis', 'SALDO_BANK_BRK')->first();
        $paramKas = \App\Models\ParameterNeraca::where('kode_otomatis', 'SALDO_KAS_TUNAI')->first();
        
        $saldoAwalBank = $paramBank ? (float) $paramBank->nominal_manual : 0;
        $saldoAwalKas = $paramKas ? (float) $paramKas->nominal_manual : 0;

        // === Saldo Koperasi (dihitung dari data di atas, 0 query tambahan) ===
        $totalMasuk = $simpanan_all + $angsuran_total + $pembayaran_eksternal + (float) ($potongan->resiko ?? 0) + (float) ($potongan->admin ?? 0);
        $totalKeluar = $dana_cair_pinjaman + $tarik_simpanan + $keluar_pengeluaran_kas;
        $saldoKoperasi = $totalMasuk - $totalKeluar + $saldoAwalBank + $saldoAwalKas;

        // Breakdown Kas
        $saldoBankBrk = $saldoAwalBank + $totalMasuk - ($dana_cair_pinjaman + $tarik_simpanan + $keluar_pengeluaran_kas_brk) - $mutasi_brk_ke_kas + $mutasi_kas_ke_brk;
        $saldoKasTunai = $saldoAwalKas + $mutasi_brk_ke_kas - $mutasi_kas_ke_brk - $keluar_pengeluaran_kas_tunai;

        // === Piutang (2 query) ===
        $totalPokokPinjamanAktif = (float) DB::table('pinjaman')->where('status', 'berjalan')->sum('nominal_pinjaman');
        $angsuranPokokTerbayar = (float) DB::table('angsuran')
            ->join('pinjaman', 'angsuran.pinjaman_id', '=', 'pinjaman.id')
            ->where('pinjaman.status', 'berjalan')
            ->where('angsuran.status', 'lunas')
            ->sum('angsuran.nominal_pokok');
        $piutangBerjalan = $totalPokokPinjamanAktif - $angsuranPokokTerbayar;
        $piutangLain = $this->piutangEksternalService->totalSisaPiutang();

        // === Breakdown Simpanan (1 query) ===
        $simpanan_per_jenis = DB::table('simpanan')
            ->join('jenis_simpanan', 'simpanan.jenis_simpanan_id', '=', 'jenis_simpanan.id')
            ->whereNull('simpanan.deleted_at')
            ->where(function ($q) { $q->where('simpanan.status', 'aktif')->orWhereNull('simpanan.status'); })
            ->select('jenis_simpanan.nama', DB::raw('SUM(simpanan.nominal) as total'))
            ->groupBy('jenis_simpanan.nama')
            ->get();

        // === Counts (2 query) ===
        $total_simpanan = $simpanan_all - $tarik_simpanan;
        
        $neraca = $this->neracaService->hitung(now()->year);
        $total_aset = $neraca['aktiva_total'];
        
        $rasioLikuiditas = $total_simpanan > 0 ? ($saldoKoperasi / $total_simpanan) * 100 : 0;
        $rasioPiutang = $total_aset > 0 ? ($piutangBerjalan / $total_aset) * 100 : 0;
        $totalPendapatan = $angsuran_bunga + $pendapatan_potongan;

        return [
            'total_anggota' => Anggota::aktif()->count(),
            'total_pinjaman_aktif' => Pinjaman::berjalan()->count(),
            'total_simpanan' => $total_simpanan,
            'saldo_koperasi' => $saldoKoperasi,
            'saldo_bank_brk' => $saldoBankBrk,
            'saldo_kas_tunai' => $saldoKasTunai,
            'piutang_berjalan' => $piutangBerjalan,
            'piutang_lain' => $piutangLain,
            'total_aset' => $total_aset,
            'total_pendapatan' => $totalPendapatan,
            'rasio_likuiditas' => $rasioLikuiditas,
            'rasio_piutang' => $rasioPiutang,
            'breakdown_kas' => [
                'masuk_simpanan' => $simpanan_all,
                'masuk_angsuran' => $angsuran_total,
                'masuk_piutang_lain' => $pembayaran_eksternal,
                'masuk_fee' => $pendapatan_potongan,
                'keluar_pinjaman' => $dana_cair_pinjaman,
                'keluar_tarik' => $tarik_simpanan,
                'keluar_pengeluaran_kas' => $keluar_pengeluaran_kas,
                'mutasi_brk_ke_kas' => $mutasi_brk_ke_kas,
                'mutasi_kas_ke_brk' => $mutasi_kas_ke_brk,
            ],
            'breakdown_piutang' => [
                'total_pokok' => $totalPokokPinjamanAktif,
                'pokok_terbayar' => $angsuranPokokTerbayar,
            ],
            'breakdown_aset' => [
                'harta_lancar' => $neraca['sections']['aktiva_lancar']['total'] ?? 0,
                'harta_tetap' => $neraca['sections']['harta_tetap']['total'] ?? 0,
                'penyertaan' => $neraca['sections']['penyertaan']['total'] ?? 0,
                'harta_lain' => $neraca['sections']['harta_lain']['total'] ?? 0,
            ],
            'breakdown_simpanan' => $simpanan_per_jenis
        ];
    }

    /**
     * Ambil 5 pinjaman menunggu terbaru.
     */
    public function pinjamanMenunggu(): \Illuminate\Database\Eloquent\Collection
    {
        return Pinjaman::with('anggota')
            ->menunggu()
            ->latest('tanggal_pengajuan')
            ->take(5)
            ->get();
    }

    /**
     * Ambil 5 aktivitas terbaru.
     */
    public function recentActivities(): \Illuminate\Database\Eloquent\Collection
    {
        return \App\Models\LogAktivitas::with('user')
            ->latest('created_at')
            ->take(5)
            ->get();
    }

    /**
     * Data pergerakan kas per bulan untuk grafik (Bar Chart).
     */
    public function getMonthlyFlow(int $year): array
    {
        // Inisialisasi array 12 bulan dengan 0
        $pemasukan = array_fill(0, 12, 0);
        $pengeluaran = array_fill(0, 12, 0);

        // Helper: convert grouped result ke array index 0-11
        $mapMonthly = function ($rows) {
            $result = array_fill(0, 12, 0);
            foreach ($rows as $row) {
                $result[$row->bulan - 1] = (float) $row->total;
            }
            return $result;
        };

        // 1 query: Simpanan per bulan
        $simpananBulanan = $mapMonthly(
            DB::table('simpanan')
                ->selectRaw('MONTH(tanggal) as bulan, SUM(nominal) as total')
                ->whereYear('tanggal', $year)
                ->whereNull('deleted_at')
                ->where(function ($q) { $q->where('status', 'aktif')->orWhereNull('status'); })
                ->groupByRaw('MONTH(tanggal)')
                ->get()
        );

        // 1 query: Angsuran per bulan
        $angsuranBulanan = $mapMonthly(
            DB::table('angsuran')
                ->selectRaw('MONTH(tanggal_bayar) as bulan, SUM(nominal_pokok + nominal_bunga) as total')
                ->whereYear('tanggal_bayar', $year)
                ->where('status', 'lunas')
                ->groupByRaw('MONTH(tanggal_bayar)')
                ->get()
        );

        // 1 query: Fee potongan per bulan (dari approval pinjaman)
        $feeBulanan = $mapMonthly(
            DB::table('pinjaman')
                ->selectRaw('MONTH(tanggal_approval) as bulan, SUM(potongan_dana_resiko + potongan_biaya_admin) as total')
                ->whereYear('tanggal_approval', $year)
                ->whereIn('status', ['berjalan', 'lunas'])
                ->groupByRaw('MONTH(tanggal_approval)')
                ->get()
        );

        // 1 query: Pembayaran piutang eksternal per bulan
        $piutangLainBulanan = $mapMonthly(
            DB::table('pembayaran_piutang_eksternal')
                ->selectRaw('MONTH(tanggal_bayar) as bulan, SUM(nominal) as total')
                ->whereYear('tanggal_bayar', $year)
                ->groupByRaw('MONTH(tanggal_bayar)')
                ->get()
        );

        // 1 query: Pencairan pinjaman per bulan
        $pencairanBulanan = $mapMonthly(
            DB::table('pinjaman')
                ->selectRaw('MONTH(tanggal_approval) as bulan, SUM(nominal_pinjaman) as total')
                ->whereYear('tanggal_approval', $year)
                ->whereIn('status', ['berjalan', 'lunas'])
                ->groupByRaw('MONTH(tanggal_approval)')
                ->get()
        );

        // 1 query: Penarikan simpanan per bulan
        $penarikanBulanan = $mapMonthly(
            DB::table('penarikan_simpanan')
                ->selectRaw('MONTH(tanggal) as bulan, SUM(nominal) as total')
                ->whereYear('tanggal', $year)
                ->groupByRaw('MONTH(tanggal)')
                ->get()
        );

        // 1 query: Pengeluaran kas per bulan
        $kasBulanan = $mapMonthly(
            DB::table('pengeluaran_kas')
                ->selectRaw('MONTH(tanggal) as bulan, SUM(nominal) as total')
                ->whereYear('tanggal', $year)
                ->whereNull('deleted_at')
                ->where(function ($q) { $q->where('status', 'aktif')->orWhereNull('status'); })
                ->groupByRaw('MONTH(tanggal)')
                ->get()
        );

        // Gabungkan per bulan
        for ($i = 0; $i < 12; $i++) {
            $pemasukan[$i] = $simpananBulanan[$i] + $angsuranBulanan[$i] + $feeBulanan[$i] + $piutangLainBulanan[$i];
            $pengeluaran[$i] = $pencairanBulanan[$i] + $penarikanBulanan[$i] + $kasBulanan[$i];
        }

        return [
            'pemasukan' => $pemasukan,
            'pengeluaran' => $pengeluaran,
        ];
    }
}
