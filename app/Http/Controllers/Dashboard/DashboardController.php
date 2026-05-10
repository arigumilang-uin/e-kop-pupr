<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct(
        private DashboardService $dashboard,
    ) {}

    public function index()
    {
        $user = Auth::user();
        $stats = $this->dashboard->getStats();
        $pinjamanMenunggu = $this->dashboard->pinjamanMenunggu();
        $monthlyFlow = $this->dashboard->getMonthlyFlow((int) date('Y'));
        $recentActivities = $this->dashboard->recentActivities();
        $bidangs = \App\Models\Bidang::orderBy('nama_bidang')->get();

        return view('dashboard.index', compact('user', 'stats', 'pinjamanMenunggu', 'monthlyFlow', 'recentActivities', 'bidangs'));
    }

    public function simpananData(Request $request)
    {
        $bidangId = $request->query('bidang_id');
        $golonganAsn = $request->query('golongan_asn');

        $query = DB::table('simpanan')
            ->join('jenis_simpanan', 'simpanan.jenis_simpanan_id', '=', 'jenis_simpanan.id')
            ->join('anggota', 'simpanan.anggota_id', '=', 'anggota.id')
            ->whereNull('simpanan.deleted_at')
            ->where(function ($q) { $q->where('simpanan.status', 'aktif')->orWhereNull('simpanan.status'); })
            ->select('jenis_simpanan.nama', DB::raw('SUM(simpanan.nominal) as total'));

        if ($bidangId) {
            $query->where('anggota.bidang_id', $bidangId);
        }

        if ($golonganAsn) {
            $query->where('anggota.golongan_asn', $golonganAsn);
        }

        $result = $query->groupBy('jenis_simpanan.nama')->get();

        return response()->json($result);
    }
}
