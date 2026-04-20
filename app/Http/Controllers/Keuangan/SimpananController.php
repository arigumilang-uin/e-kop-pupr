<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Simpanan\StoreSimpananRequest;
use App\Models\Anggota;
use App\Models\JenisSimpanan;
use App\Models\Simpanan;
use App\Services\ActivityLogService;
use App\Services\SaldoService;
use Illuminate\Http\Request;

class SimpananController extends Controller
{
    public function __construct(
        private ActivityLogService $logger,
        private SaldoService $saldoService
    ) {}

    public function index(Request $request)
    {
        $query = Simpanan::with(['anggota', 'jenisSimpanan', 'pencatat'])->latest('tanggal')->latest('id');

        if ($request->filled('q')) {
            $query->whereHas('anggota', function ($q) use ($request) {
                $q->where('nama', 'like', "%{$request->q}%")
                  ->orWhere('nip', 'like', "%{$request->q}%");
            });
        }

        $simpanans = $query->paginate(15)->withQueryString();
        
        $totalSaldoKoperasi = $this->saldoService->saldoKoperasi();

        return view('simpanan.index', compact('simpanans', 'totalSaldoKoperasi'));
    }

    public function create()
    {
        $anggotas = Anggota::aktif()->orderBy('nama')->get();
        $jenisSimpanan = JenisSimpanan::orderBy('nama')->get();
        
        return view('simpanan.create', compact('anggotas', 'jenisSimpanan'));
    }

    public function store(StoreSimpananRequest $request)
    {
        $simpanan = Simpanan::create([
            ...$request->validated(),
            'dicatat_oleh' => auth()->id(),
        ]);

        $this->logger->log(
            'simpanan_created',
            "Setoran simpanan masuk sebesar Rp " . number_format($simpanan->nominal, 0, ',', '.') . " untuk {$simpanan->anggota->nama}"
        );

        return redirect()->route('simpanan.index')->with('success', 'Transaksi simpanan berhasil dicatat.');
    }
}
