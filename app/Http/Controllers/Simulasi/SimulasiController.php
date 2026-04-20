<?php

namespace App\Http\Controllers\Simulasi;

use App\Http\Controllers\Controller;
use App\Services\PengaturanService;
use App\Services\PinjamanService;
use Illuminate\Http\Request;

class SimulasiController extends Controller
{
    public function __construct(
        private PinjamanService $pinjamanService,
        private PengaturanService $pengaturanService,
    ) {}

    /**
     * Tampilkan halaman simulasi (publik, tanpa login).
     */
    public function index()
    {
        $pengaturan = [
            'bunga_persen' => $this->pengaturanService->bungaPersen(),
            'swp_persen' => $this->pengaturanService->potonganSwpPersen(),
            'resiko_persen' => $this->pengaturanService->potonganDanaResikoPersen(),
            'admin_persen' => $this->pengaturanService->potonganBiayaAdminPersen(),
            'tenor_min' => $this->pengaturanService->tenorMinimal(),
            'batas_bulan' => $this->pengaturanService->batasBulanPelunasan(),
        ];

        return view('simulasi.index', compact('pengaturan'));
    }

    /**
     * API endpoint untuk kalkulasi simulasi (dipanggil via JS fetch).
     */
    public function hitung(Request $request)
    {
        $request->validate([
            'nominal' => 'required|numeric|min:100000',
            'tenor' => 'required|integer|min:1',
        ]);

        $hasil = $this->pinjamanService->hitungPinjaman(
            (float) $request->nominal,
            (int) $request->tenor
        );

        return response()->json($hasil);
    }
}
