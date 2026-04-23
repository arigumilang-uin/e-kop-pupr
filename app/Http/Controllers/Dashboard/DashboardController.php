<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\Pinjaman;
use App\Services\SaldoService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct(
        private SaldoService $saldo,
    ) {}

    public function index()
    {
        $user = Auth::user();

        $stats = $this->getStats();
        $pinjamanMenunggu = $this->getPinjamanMenunggu();

        return view('dashboard.index', compact('user', 'stats', 'pinjamanMenunggu'));
    }

    // === Private ===

    private function getStats(): array
    {
        $saldoKoperasi = $this->saldo->saldoKoperasi();
        
        // Data Pendukung Kas Modal Breakdown
        $simpanan_all = (float) \DB::table('simpanan')->sum('nominal');
        $angsuran_pokok_bunga = (float) \DB::table('angsuran')->where('status', 'lunas')->sum(\DB::raw('nominal_pokok + nominal_bunga'));
        $pendapatan_potongan = (float) \DB::table('pinjaman')->whereIn('status', ['berjalan', 'lunas'])
            ->sum(\DB::raw('potongan_dana_resiko + potongan_biaya_admin'));
        $dana_cair_pinjaman = (float) \DB::table('pinjaman')->whereIn('status', ['berjalan', 'lunas'])->sum('nominal_pinjaman');
        $tarik_simpanan = (float) \DB::table('penarikan_simpanan')->sum('nominal');

        // Data Pendukung Piutang Breakdown
        $totalBayarPinjamanAktif = (float) DB::table('pinjaman')->where('status', 'berjalan')->sum('total_bayar');
        $angsuranTerbayar = (float) DB::table('angsuran')
            ->join('pinjaman', 'angsuran.pinjaman_id', '=', 'pinjaman.id')
            ->where('pinjaman.status', 'berjalan')
            ->where('angsuran.status', 'lunas')
            ->sum('angsuran.nominal_total');
        $piutangBerjalan = $totalBayarPinjamanAktif - $angsuranTerbayar;

        // Data Pendukung Simpanan Breakdown
        $simpanan_per_jenis = DB::table('simpanan')
            ->join('jenis_simpanan', 'simpanan.jenis_simpanan_id', '=', 'jenis_simpanan.id')
            ->select('jenis_simpanan.nama', DB::raw('SUM(simpanan.nominal) as total'))
            ->groupBy('jenis_simpanan.nama')
            ->get();

        return [
            'total_anggota' => Anggota::aktif()->count(),
            'total_pinjaman_aktif' => Pinjaman::berjalan()->count(),
            'total_simpanan' => $simpanan_all,
            'saldo_koperasi' => $saldoKoperasi,
            'piutang_berjalan' => $piutangBerjalan,
            'total_aset' => $saldoKoperasi + $piutangBerjalan,

            // Breakdown
            'breakdown_kas' => [
                'masuk_simpanan' => $simpanan_all,
                'masuk_angsuran' => $angsuran_pokok_bunga,
                'masuk_fee' => $pendapatan_potongan,
                'keluar_pinjaman' => $dana_cair_pinjaman,
                'keluar_tarik' => $tarik_simpanan,
            ],
            'breakdown_piutang' => [
                'total_tagihan' => $totalBayarPinjamanAktif,
                'total_angsuran_masuk' => $angsuranTerbayar,
            ],
            'breakdown_simpanan' => $simpanan_per_jenis
        ];
    }

    private function getPinjamanMenunggu()
    {
        return Pinjaman::with('anggota')
            ->menunggu()
            ->latest('tanggal_pengajuan')
            ->take(5)
            ->get();
    }
}
