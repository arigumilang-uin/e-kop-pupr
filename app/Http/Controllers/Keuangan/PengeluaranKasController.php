<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pengeluaran\StoreKategoriPengeluaranRequest;
use App\Http\Requests\Pengeluaran\StorePengeluaranRequest;
use App\Models\KategoriPengeluaran;
use App\Models\PengeluaranKas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengeluaranKasController extends Controller
{
    public function __construct(
        private \App\Services\ActivityLogService $logger
    ) {}

    public function index(Request $request)
    {
        $kategori = KategoriPengeluaran::withCount('pengeluaranKas')->get();
        
        $pengeluaranQuery = PengeluaranKas::with(['kategori', 'pencatat'])->orderByDesc('tanggal')->orderByDesc('id');
        
        if ($request->filled('kategori_id')) {
            $pengeluaranQuery->where('kategori_pengeluaran_id', $request->kategori_id);
        }

        if ($request->filled('bulan') && $request->filled('tahun')) {
            $pengeluaranQuery->whereMonth('tanggal', $request->bulan)
                             ->whereYear('tanggal', $request->tahun);
        }

        $pengeluaran = $pengeluaranQuery->paginate(20)->withQueryString();

        $totalPengeluaran = PengeluaranKas::sum('nominal');

        return view('keuangan.pengeluaran.index', compact('kategori', 'pengeluaran', 'totalPengeluaran'));
    }

    public function storeKategori(StoreKategoriPengeluaranRequest $request)
    {
        KategoriPengeluaran::create($request->validated());

        return back()->with('success', 'Kategori pengeluaran berhasil ditambahkan.');
    }

    public function store(StorePengeluaranRequest $request)
    {

        DB::transaction(function () use ($request) {
            $pengeluaran = PengeluaranKas::create([
                'kategori_pengeluaran_id' => $request->kategori_pengeluaran_id,
                'nominal' => $request->nominal,
                'tanggal' => $request->tanggal,
                'keterangan' => $request->keterangan,
                'dicatat_oleh' => auth()->id(),
            ]);

            $this->logger->logPengeluaran(
                $pengeluaran->kategori->nama ?? '-',
                $pengeluaran->nominal,
                $pengeluaran->no_referensi,
            );
        });

        return back()->with('success', 'Data pengeluaran kas berhasil dicatat.');
    }

    public function destroy(PengeluaranKas $pengeluaran)
    {
        $this->logger->logPengeluaranDeleted($pengeluaran->no_referensi, $pengeluaran->nominal);
        $pengeluaran->delete();
        return back()->with('success', 'Data pengeluaran kas berhasil dihapus.');
    }
}
