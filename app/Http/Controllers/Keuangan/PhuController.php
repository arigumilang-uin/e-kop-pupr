<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Services\PhuService;
use Illuminate\Http\Request;

class PhuController extends Controller
{
    public function __construct(
        private PhuService $phuService,
    ) {}

    public function index(Request $request)
    {
        $tahun = $request->input('tahun', now()->year);
        $phu = $this->phuService->hitung((int) $tahun);

        return view('keuangan.phu', compact('phu'));
    }
}
