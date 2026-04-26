<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Services\LaporanService;
use Illuminate\Http\Request;

class LaporanKeuanganController extends Controller
{
    public function __construct(
        private LaporanService $laporan,
    ) {}

    public function index(Request $request)
    {
        $tab = $request->input('tab', 'ringkasan');

        $ringkasan = $this->laporan->ringkasan();

        ['piutangAnggota' => $piutangAnggota, 'piutangGrouped' => $piutangGrouped] = $this->laporan->piutangAnggota();

        ['simpananGrouped' => $simpananGrouped, 'jenisKodes' => $jenisKodes] = $this->laporan->simpananAnggota();

        return view('keuangan.laporan', compact(
            'tab', 'ringkasan', 'piutangGrouped', 'piutangAnggota',
            'simpananGrouped', 'jenisKodes'
        ));
    }
}
