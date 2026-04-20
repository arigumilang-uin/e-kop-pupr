<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\Pinjaman;
use App\Models\Simpanan;
use App\Models\Angsuran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Dashboard utama — redirect berdasarkan role.
     */
    public function index()
    {
        $user = Auth::user();

        // Statistik umum
        $stats = [
            'total_anggota' => Anggota::aktif()->count(),
            'total_pinjaman_aktif' => Pinjaman::berjalan()->count(),
            'total_simpanan' => DB::table('simpanan')->sum('nominal'),
            'total_angsuran_belum' => Angsuran::belumLunas()->count(),
            'saldo_koperasi' => $this->hitungSaldoKoperasi(),
        ];

        // Pinjaman menunggu approval
        $pinjamanMenunggu = Pinjaman::with('anggota')
            ->where('status', 'menunggu')
            ->latest('tanggal_pengajuan')
            ->take(5)
            ->get();

        return view('dashboard', compact('user', 'stats', 'pinjamanMenunggu'));
    }

    /**
     * Hitung saldo koperasi real-time.
     */
    private function hitungSaldoKoperasi(): float
    {
        // Dana masuk
        $totalSimpanan = (float) DB::table('simpanan')->sum('nominal');
        $totalAngsuranLunas = (float) DB::table('angsuran')
            ->where('status', 'lunas')
            ->sum(DB::raw('nominal_pokok + nominal_bunga'));
        $totalDanaResiko = (float) DB::table('pinjaman')
            ->whereIn('status', ['berjalan', 'lunas'])
            ->sum('potongan_dana_resiko');
        $totalBiayaAdmin = (float) DB::table('pinjaman')
            ->whereIn('status', ['berjalan', 'lunas'])
            ->sum('potongan_biaya_admin');

        $danamasuk = $totalSimpanan + $totalAngsuranLunas + $totalDanaResiko + $totalBiayaAdmin;

        // Dana keluar
        $totalPencairan = (float) DB::table('pinjaman')
            ->whereIn('status', ['berjalan', 'lunas'])
            ->sum('dana_diterima');
        $totalPenarikan = (float) DB::table('penarikan_simpanan')->sum('nominal');

        $danaKeluar = $totalPencairan + $totalPenarikan;

        return $danamasuk - $danaKeluar;
    }
}
