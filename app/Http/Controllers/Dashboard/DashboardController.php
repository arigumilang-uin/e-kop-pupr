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
        return [
            'total_anggota' => Anggota::aktif()->count(),
            'total_pinjaman_aktif' => Pinjaman::berjalan()->count(),
            'total_simpanan' => (float) DB::table('simpanan')->sum('nominal'),
            'saldo_koperasi' => $this->saldo->saldoKoperasi(),
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
