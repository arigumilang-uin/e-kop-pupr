<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\KategoriPengeluaran;
use App\Models\PengeluaranKas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengeluaranKasController extends Controller
{
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

    public function storeKategori(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100|unique:kategori_pengeluaran,nama',
            'deskripsi' => 'nullable|string',
        ]);

        KategoriPengeluaran::create($request->all());

        return back()->with('success', 'Kategori pengeluaran berhasil ditambahkan.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kategori_pengeluaran_id' => 'required|exists:kategori_pengeluaran,id',
            'nominal' => 'required|numeric|min:1',
            'tanggal' => 'required|date',
            'keterangan' => 'required|string',
        ]);

        DB::transaction(function () use ($request) {
            PengeluaranKas::create([
                'kategori_pengeluaran_id' => $request->kategori_pengeluaran_id,
                'nominal' => $request->nominal,
                'tanggal' => $request->tanggal,
                'keterangan' => $request->keterangan,
                'dicatat_oleh' => auth()->id(),
            ]);
        });

        return back()->with('success', 'Data pengeluaran kas berhasil dicatat.');
    }

    // Add delete feature just in case
    public function destroy(PengeluaranKas $pengeluaran)
    {
        $pengeluaran->delete();
        return back()->with('success', 'Data pengeluaran kas berhasil dihapus.');
    }
}
