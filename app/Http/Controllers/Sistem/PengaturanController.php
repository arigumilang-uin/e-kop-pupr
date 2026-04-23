<?php

namespace App\Http\Controllers\Sistem;

use App\Http\Controllers\Controller;
use App\Models\Pengaturan;
use App\Services\ActivityLogService;
use App\Services\PengaturanService;
use Illuminate\Http\Request;

class PengaturanController extends Controller
{
    public function __construct(
        private PengaturanService $pengaturanService,
        private ActivityLogService $logger,
    ) {}

    public function index()
    {
        $pengaturans = Pengaturan::orderBy('kategori')->orderBy('key')->get();

        // Group by kategori
        $grouped = $pengaturans->groupBy(fn($p) => $p->kategori->label());

        return view('pengaturan.index', compact('grouped'));
    }

    public function update(Request $request, Pengaturan $pengaturan)
    {
        $request->validate([
            'value' => 'required|string',
        ]);

        $oldValue = $pengaturan->value;
        $newValue = $request->input('value');

        if ($oldValue === $newValue) {
            return back()->with('warning', 'Tidak ada perubahan nilai.');
        }

        $pengaturan->update(['value' => $newValue]);

        // Clear cache untuk key ini
        $this->pengaturanService->clearCache($pengaturan->key);

        $this->logger->log(
            'pengaturan_updated',
            "Pengaturan '{$pengaturan->key}' diubah dari '{$oldValue}' menjadi '{$newValue}'.",
            ['key' => $pengaturan->key, 'old' => $oldValue],
            ['key' => $pengaturan->key, 'new' => $newValue],
        );

        return back()->with('success', "Pengaturan '{$pengaturan->deskripsi}' berhasil diperbarui.");
    }
}
