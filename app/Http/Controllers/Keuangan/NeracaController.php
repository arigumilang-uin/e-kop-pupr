<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Services\NeracaService;
use Illuminate\Http\Request;

class NeracaController extends Controller
{
    public function __construct(
        private NeracaService $neracaService,
    ) {}

    public function index(Request $request)
    {
        $neraca = $this->neracaService->hitung();

        return view('keuangan.neraca', compact('neraca'));
    }
}
