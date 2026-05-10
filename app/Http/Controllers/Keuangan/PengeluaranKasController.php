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
        private \App\Services\ActivityLogService $logger,
        private \App\Services\LedgerService $ledgerService,
    ) {}

    public function index(Request $request)
    {
        $kategori = KategoriPengeluaran::withCount('pengeluaranKas')->get();
        $nominals = PengeluaranKas::select('nominal')->distinct()->orderBy('nominal')->pluck('nominal');
        
        $pengeluaranQuery = PengeluaranKas::with(['kategori', 'pencatat'])
            ->where(function ($q) { $q->where('status', 'aktif')->orWhereNull('status'); })
            ->orderByDesc('tanggal')->orderByDesc('id');
        
        if ($request->filled('q')) {
            $pengeluaranQuery->where('keterangan', 'like', '%' . $request->q . '%');
        }

        if ($request->filled('kategori_id')) {
            $pengeluaranQuery->where('kategori_pengeluaran_id', $request->kategori_id);
        }

        if ($request->filled('nominal')) {
            $pengeluaranQuery->where('nominal', $request->nominal);
        }

        if ($request->filled('bulan')) {
            $pengeluaranQuery->whereMonth('tanggal', $request->bulan);
        }

        if ($request->filled('tahun')) {
            $pengeluaranQuery->whereYear('tanggal', $request->tahun);
        }

        $pengeluaran = $pengeluaranQuery->paginate(20)->withQueryString();

        $totalPengeluaran = PengeluaranKas::where(function ($q) {
                $q->where('status', 'aktif')->orWhereNull('status');
            })->sum('nominal');

        return view('keuangan.pengeluaran.index', compact('kategori', 'pengeluaran', 'totalPengeluaran', 'nominals'));
    }

    public function storeKategori(StoreKategoriPengeluaranRequest $request)
    {
        KategoriPengeluaran::create($request->validated());

        return back()->with('success', 'Kategori pengeluaran berhasil ditambahkan.');
    }

    public function store(StorePengeluaranRequest $request)
    {
        DB::transaction(function () use ($request) {
            foreach ($request->pengeluaran as $item) {
                $pengeluaran = PengeluaranKas::create([
                    'kategori_pengeluaran_id' => $item['kategori_pengeluaran_id'],
                    'nominal' => $item['nominal'],
                    'tanggal' => $request->tanggal,
                    'keterangan' => $item['keterangan'],
                    'dicatat_oleh' => auth()->id(),
                ]);

                // Tulis ke Ledger
                $this->ledgerService->catatPengeluaran(
                    $pengeluaran->nominal,
                    $pengeluaran->id,
                    $pengeluaran->kategori->nama ?? 'Lainnya',
                    $request->tanggal,
                );

                $this->logger->logPengeluaran(
                    $pengeluaran->kategori->nama ?? '-',
                    $pengeluaran->nominal,
                    $pengeluaran->no_referensi,
                );
            }
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
