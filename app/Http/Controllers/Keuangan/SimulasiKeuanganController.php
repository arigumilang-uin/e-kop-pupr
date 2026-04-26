<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Services\SimulasiProyeksiService;
use Illuminate\Http\Request;

class SimulasiKeuanganController extends Controller
{
    public function __construct(
        private SimulasiProyeksiService $simulasi,
    ) {}

    public function index(Request $request)
    {
        $targetBulan = $request->input('bulan_target', now()->addMonths(3)->month);
        $targetTahun = $request->input('tahun_target', now()->addMonths(3)->year);

        $data = $this->simulasi->hitung((int) $targetBulan, (int) $targetTahun);

        return view('keuangan.simulasi', $data);
    }
}
