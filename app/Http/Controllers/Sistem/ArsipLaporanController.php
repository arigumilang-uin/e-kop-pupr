<?php

namespace App\Http\Controllers\Sistem;

use App\Http\Controllers\Controller;
use App\Models\ArsipLaporan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Services\ActivityLogService;

class ArsipLaporanController extends Controller
{
    public function __construct(private ActivityLogService $logService) {}
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

    public function togglePin(ArsipLaporan $arsip)
    {
        $arsip->update(['is_permanent' => !$arsip->is_permanent]);
        
        $status = $arsip->is_permanent ? 'dipin/dikunci' : 'dilepas pinnya';
        
        $this->logService->log(
            'arsip_toggle_pin',
            "Arsip laporan {$arsip->nama_file} berhasil {$status}.",
            dataBaru: ['arsip_id' => $arsip->id, 'is_permanent' => $arsip->is_permanent]
        );

        return back()->with('success', "Arsip laporan berhasil $status.");
    }

    public function destroy(ArsipLaporan $arsip)
    {
        $namaFile = $arsip->nama_file;
        $tipe = $arsip->tipe_laporan;

        if (Storage::exists($arsip->file_path)) {
            Storage::delete($arsip->file_path);
        }
        $arsip->delete();

        $this->logService->log(
            'arsip_deleted',
            "Arsip laporan fisik & data ({$tipe}: {$namaFile}) dihapus permanen secara manual.",
            dataLama: ['arsip_id' => $arsip->id, 'nama_file' => $namaFile]
        );

        return back()->with('success', 'Arsip laporan berhasil dihapus secara permanen.');
    }
}
