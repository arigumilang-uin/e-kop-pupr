<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\Angsuran;
use App\Models\Pinjaman;
use App\Models\Simpanan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ArsipTransaksiController extends Controller
{
    public function index(Request $request)
    {
        // === Tentukan Range Tanggal ===
        $preset = $request->input('preset', 'bulan_ini');

        switch ($preset) {
            case 'minggu_ini':
                $dari = now()->startOfWeek();
                $sampai = now()->endOfWeek();
                break;
            case '3_bulan':
                $dari = now()->subMonths(2)->startOfMonth();
                $sampai = now()->endOfMonth();
                break;
            case '6_bulan':
                $dari = now()->subMonths(5)->startOfMonth();
                $sampai = now()->endOfMonth();
                break;
            case 'tahun_ini':
                $dari = now()->startOfYear();
                $sampai = now()->endOfYear();
                break;
            case 'custom':
                $dari = $request->filled('dari') ? Carbon::parse($request->input('dari'))->startOfDay() : now()->startOfMonth();
                $sampai = $request->filled('sampai') ? Carbon::parse($request->input('sampai'))->endOfDay() : now()->endOfMonth();
                break;
            default: // bulan_ini
                $dari = now()->startOfMonth();
                $sampai = now()->endOfMonth();
                break;
        }

        $tab = $request->input('tab', 'simpanan');

        // === 1. SIMPANAN ===
        $simpananQuery = Simpanan::with(['anggota', 'jenisSimpanan', 'pencatat'])
            ->aktif()
            ->whereBetween('tanggal', [$dari->toDateString(), $sampai->toDateString()])
            ->orderByDesc('tanggal')
            ->orderByDesc('id');

        $simpanans = $simpananQuery->get();

        $simpananPerJenis = $simpanans->groupBy(fn($s) => $s->jenisSimpanan->nama ?? 'Lainnya')
            ->map(fn($items, $key) => [
                'nama' => $key,
                'count' => $items->count(),
                'total' => $items->sum('nominal'),
            ])->values();

        $totalSimpanan = $simpanans->sum('nominal');

        // === 2. ANGSURAN LUNAS ===
        $angsuranQuery = Angsuran::with(['pinjaman.anggota'])
            ->where('status', 'lunas')
            ->whereBetween('tanggal_bayar', [$dari->toDateString(), $sampai->toDateString()])
            ->orderByDesc('tanggal_bayar')
            ->orderByDesc('id');

        $angsurans = $angsuranQuery->get();
        $totalAngsuranPokok = $angsurans->sum('nominal_pokok');
        $totalAngsuranBunga = $angsurans->sum('nominal_bunga');
        $totalAngsuran = $angsurans->sum('nominal_total');

        // === 3. PINJAMAN DISETUJUI/DICAIRKAN ===
        $pinjamanQuery = Pinjaman::with(['anggota'])
            ->whereIn('status', ['berjalan', 'lunas', 'disetujui'])
            ->whereNotNull('tanggal_approval')
            ->whereBetween('tanggal_approval', [$dari, $sampai])
            ->orderByDesc('tanggal_approval');

        $pinjamans = $pinjamanQuery->get();
        $totalPinjamanCair = $pinjamans->sum('nominal_pinjaman');
        $totalPinjamanDiterima = $pinjamans->sum('dana_diterima');

        // === 4. PENGELUARAN KAS (MANUAL) ===
        $pengeluaranQuery = \App\Models\PengeluaranKas::with(['kategori', 'pencatat'])
            ->where(function ($q) { $q->where('status', 'aktif')->orWhereNull('status'); })
            ->whereBetween('tanggal', [$dari->toDateString(), $sampai->toDateString()])
            ->orderByDesc('tanggal')
            ->orderByDesc('id');

        $pengeluarans = $pengeluaranQuery->get();
        $totalPengeluaranKas = $pengeluarans->sum('nominal');

        // === 5. PENARIKAN SIMPANAN ===
        $penarikanQuery = \App\Models\PenarikanSimpanan::with(['anggota', 'jenisSimpanan', 'pemroses'])
            ->whereBetween('tanggal', [$dari->toDateString(), $sampai->toDateString()])
            ->orderByDesc('tanggal')
            ->orderByDesc('id');

        $penarikans = $penarikanQuery->get();
        $totalPenarikanSimpanan = $penarikans->sum('nominal');

        // === RINGKASAN ===
        $grandTotal = $totalSimpanan + $totalAngsuran + $totalPinjamanCair + $totalPengeluaranKas + $totalPenarikanSimpanan;

        $ringkasan = compact(
            'totalSimpanan', 'totalAngsuranPokok', 'totalAngsuranBunga', 'totalAngsuran',
            'totalPinjamanCair', 'totalPinjamanDiterima', 'totalPengeluaranKas', 'totalPenarikanSimpanan', 'grandTotal'
        );

        return view('keuangan.arsip', compact(
            'preset', 'dari', 'sampai', 'tab',
            'simpanans', 'simpananPerJenis', 'angsurans', 'pinjamans', 'pengeluarans', 'penarikans', 'ringkasan'
        ));
    }
}
