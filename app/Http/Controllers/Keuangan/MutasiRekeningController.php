<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\MutasiRekening;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MutasiRekeningController extends Controller
{
    public function index(Request $request)
    {
        $query = MutasiRekening::orderByDesc('tanggal')->orderByDesc('id');

        if ($request->filled('q')) {
            $query->where('keterangan', 'like', '%' . $request->q . '%');
        }

        if ($request->filled('jenis_mutasi')) {
            $query->where('jenis_mutasi', $request->jenis_mutasi);
        }

        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal', $request->bulan);
        }

        if ($request->filled('tahun')) {
            $query->whereYear('tanggal', $request->tahun);
        }

        $mutasi = $query->paginate(20)->withQueryString();

        $totalKasKeBrk = MutasiRekening::where('jenis_mutasi', 'kas_ke_brk')->sum('nominal');
        $totalBrkKeKas = MutasiRekening::where('jenis_mutasi', 'brk_ke_kas')->sum('nominal');

        return view('keuangan.mutasi.index', compact('mutasi', 'totalKasKeBrk', 'totalBrkKeKas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'jenis_mutasi' => 'required|in:brk_ke_kas,kas_ke_brk',
            'nominal' => 'required|numeric|min:1',
            'keterangan' => 'required|string',
        ]);

        MutasiRekening::create($request->all());

        return back()->with('success', 'Mutasi rekening berhasil dicatat.');
    }

    public function destroy(MutasiRekening $mutasi_rekening)
    {
        $mutasi_rekening->delete();
        return back()->with('success', 'Catatan mutasi rekening berhasil dihapus.');
    }
}
