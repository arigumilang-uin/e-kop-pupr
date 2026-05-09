<?php

namespace App\Http\Controllers\Sistem;

use App\Http\Controllers\Controller;
use App\Models\ArsipLaporan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ArsipLaporanController extends Controller
{
    public function index(Request $request)
    {
        $query = ArsipLaporan::with('user')->latest();

        if ($request->filled('q')) {
            $query->where('nama_file', 'like', '%' . $request->q . '%')
                  ->orWhere('filter_info', 'like', '%' . $request->q . '%');
        }

        if ($request->filled('tipe')) {
            $query->where('tipe_laporan', $request->tipe);
        }

        $arsip = $query->paginate(20)->withQueryString();

        $tipes = ArsipLaporan::select('tipe_laporan')->distinct()->pluck('tipe_laporan');

        return view('pengaturan.arsip.index', compact('arsip', 'tipes'));
    }

    public function download(ArsipLaporan $arsip)
    {
        if (!Storage::exists($arsip->file_path)) {
            return back()->with('error', 'File fisik laporan tidak ditemukan di server.');
        }

        return Storage::download($arsip->file_path, $arsip->nama_file);
    }

    public function destroy(ArsipLaporan $arsip)
    {
        // Only super admin can delete archives (handled by route middleware or policy)
        if (Storage::exists($arsip->file_path)) {
            Storage::delete($arsip->file_path);
        }
        $arsip->delete();

        return back()->with('success', 'Arsip laporan berhasil dihapus secara permanen.');
    }
}
