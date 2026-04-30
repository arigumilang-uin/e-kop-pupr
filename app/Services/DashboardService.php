<?php

namespace App\Services;

use App\Models\Anggota;
use App\Models\Pinjaman;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function __construct(
        private SaldoService $saldo,
    ) {}

    /**
     * Hitung seluruh statistik dashboard.
     */
    public function getStats(): array
    {
        $saldoKoperasi = $this->saldo->saldoKoperasi();

        $simpanan_all = (float) DB::table('simpanan')->sum('nominal');
        $angsuran_pokok = (float) DB::table('angsuran')->where('status', 'lunas')->sum('nominal_pokok');
        $angsuran_bunga = (float) DB::table('angsuran')->where('status', 'lunas')->sum('nominal_bunga');
        $angsuran_pokok_bunga = $angsuran_pokok + $angsuran_bunga;
        $pendapatan_potongan = (float) DB::table('pinjaman')->whereIn('status', ['berjalan', 'lunas'])
            ->sum(DB::raw('potongan_dana_resiko + potongan_biaya_admin'));
        $dana_cair_pinjaman = (float) DB::table('pinjaman')->whereIn('status', ['berjalan', 'lunas'])->sum('nominal_pinjaman');
        $tarik_simpanan = (float) DB::table('penarikan_simpanan')->sum('nominal');
        $keluar_pengeluaran_kas = (float) DB::table('pengeluaran_kas')->sum('nominal');

        $totalPokokPinjamanAktif = (float) DB::table('pinjaman')->where('status', 'berjalan')->sum('nominal_pinjaman');
        $angsuranPokokTerbayar = (float) DB::table('angsuran')
            ->join('pinjaman', 'angsuran.pinjaman_id', '=', 'pinjaman.id')
            ->where('pinjaman.status', 'berjalan')
            ->where('angsuran.status', 'lunas')
            ->sum('angsuran.nominal_pokok');
        $piutangBerjalan = $totalPokokPinjamanAktif - $angsuranPokokTerbayar;

        $simpanan_per_jenis = DB::table('simpanan')
            ->join('jenis_simpanan', 'simpanan.jenis_simpanan_id', '=', 'jenis_simpanan.id')
            ->select('jenis_simpanan.nama', DB::raw('SUM(simpanan.nominal) as total'))
            ->groupBy('jenis_simpanan.nama')
            ->get();

        $total_simpanan = $simpanan_all - $tarik_simpanan;
        $total_aset = $saldoKoperasi + $piutangBerjalan;

        $rasioLikuiditas = $total_simpanan > 0 ? ($saldoKoperasi / $total_simpanan) * 100 : 0;
        $rasioPiutang = $total_aset > 0 ? ($piutangBerjalan / $total_aset) * 100 : 0;
        $totalPendapatan = $angsuran_bunga + $pendapatan_potongan;

        return [
            'total_anggota' => Anggota::aktif()->count(),
            'total_pinjaman_aktif' => Pinjaman::berjalan()->count(),
            'total_simpanan' => $total_simpanan,
            'saldo_koperasi' => $saldoKoperasi,
            'piutang_berjalan' => $piutangBerjalan,
            'total_aset' => $total_aset,
            'total_pendapatan' => $totalPendapatan,
            'rasio_likuiditas' => $rasioLikuiditas,
            'rasio_piutang' => $rasioPiutang,
            'breakdown_kas' => [
                'masuk_simpanan' => $simpanan_all,
                'masuk_angsuran' => $angsuran_pokok_bunga,
                'masuk_fee' => $pendapatan_potongan,
                'keluar_pinjaman' => $dana_cair_pinjaman,
                'keluar_tarik' => $tarik_simpanan,
                'keluar_pengeluaran_kas' => $keluar_pengeluaran_kas,
            ],
            'breakdown_piutang' => [
                'total_pokok' => $totalPokokPinjamanAktif,
                'pokok_terbayar' => $angsuranPokokTerbayar,
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
        $pemasukan = [];
        $pengeluaran = [];

        for ($i = 1; $i <= 12; $i++) {
            // Pemasukan
            $masukSimpanan = (float) DB::table('simpanan')->whereYear('tanggal', $year)->whereMonth('tanggal', $i)->sum('nominal');
            $masukAngsuran = (float) DB::table('angsuran')->whereYear('tanggal_bayar', $year)->whereMonth('tanggal_bayar', $i)->where('status', 'lunas')->sum(DB::raw('nominal_pokok + nominal_bunga'));
            // Fee pendapatan langsung masuk ke bulan pinjaman saat di-approve
            $masukFee = (float) DB::table('pinjaman')->whereYear('tanggal_approval', $year)->whereMonth('tanggal_approval', $i)->whereIn('status', ['berjalan', 'lunas'])->sum(DB::raw('potongan_dana_resiko + potongan_biaya_admin'));
            
            // Pengeluaran
            $keluarPinjaman = (float) DB::table('pinjaman')->whereYear('tanggal_approval', $year)->whereMonth('tanggal_approval', $i)->whereIn('status', ['berjalan', 'lunas'])->sum('nominal_pinjaman');
            $keluarTarik = (float) DB::table('penarikan_simpanan')->whereYear('tanggal', $year)->whereMonth('tanggal', $i)->sum('nominal');
            $keluarKas = (float) DB::table('pengeluaran_kas')->whereYear('tanggal', $year)->whereMonth('tanggal', $i)->sum('nominal');

            $pemasukan[] = $masukSimpanan + $masukAngsuran + $masukFee;
            $pengeluaran[] = $keluarPinjaman + $keluarTarik + $keluarKas;
        }

        return [
            'pemasukan' => $pemasukan,
            'pengeluaran' => $pengeluaran,
        ];
    }
}
