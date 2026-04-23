<?php

namespace App\Http\Controllers\Sistem;

use App\Http\Controllers\Controller;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;

class LogAktivitasController extends Controller
{
    public function index(Request $request)
    {
        $query = LogAktivitas::with('user')->latest('created_at');

        // Filter: Aktivitas
        if ($request->filled('aktivitas')) {
            $query->where('aktivitas', $request->aktivitas);
        }

        // Filter: Pencarian deskripsi
        if ($request->filled('q')) {
            $query->where('deskripsi', 'like', "%{$request->q}%");
        }

        $logs = $query->paginate(25)->withQueryString();

        // Ambil daftar unik aktivitas untuk dropdown filter
        $aktivitasList = LogAktivitas::select('aktivitas')->distinct()->orderBy('aktivitas')->pluck('aktivitas');

        return view('log.index', compact('logs', 'aktivitasList'));
    }
}
