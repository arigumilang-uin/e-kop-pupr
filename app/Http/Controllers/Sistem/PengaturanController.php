<?php

namespace App\Http\Controllers\Sistem;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sistem\UpdatePengaturanRequest;
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
        $grouped = $pengaturans->groupBy(fn($p) => $p->kategori->label());
        $khusus = \App\Models\PengaturanKhusus::orderBy('tahun', 'desc')->orderBy('bulan', 'desc')->get();

        return view('pengaturan.index', compact('grouped', 'khusus'));
    }

    public function update(UpdatePengaturanRequest $request, Pengaturan $pengaturan)
    {
        $oldValue = $pengaturan->value;
        $newValue = $request->input('value');

        if ($oldValue === $newValue) {
            return back()->with('warning', 'Tidak ada perubahan nilai.');
        }

        $pengaturan->update(['value' => $newValue]);

        $this->pengaturanService->clearCache($pengaturan->key);

        $this->logger->log(
            'pengaturan_updated',
            "Pengaturan '{$pengaturan->key}' diubah dari '{$oldValue}' menjadi '{$newValue}'.",
            ['key' => $pengaturan->key, 'old' => $oldValue],
            ['key' => $pengaturan->key, 'new' => $newValue],
        );

        return back()->with('success', "Pengaturan '{$pengaturan->deskripsi}' berhasil diperbarui.");
    }

    public function storeKhusus(Request $request)
    {
        $request->validate([
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2020|max:2099',
            'value' => 'required|numeric|min:0',
        ]);

        \App\Models\PengaturanKhusus::updateOrCreate(
            [
                'key' => 'simpanan_wajib',
                'bulan' => $request->bulan,
                'tahun' => $request->tahun,
            ],
            [
                'value' => $request->value,
            ]
        );

        $this->logger->log(
            'pengaturan_khusus_added',
            "Pengaturan simpanan wajib khusus ditambahkan untuk periode {$request->bulan}/{$request->tahun} dengan nilai {$request->value}."
        );

        return back()->with('success', 'Pengaturan simpanan wajib bulanan (khusus) berhasil disimpan.');
    }

    public function destroyKhusus($id)
    {
        $khusus = \App\Models\PengaturanKhusus::findOrFail($id);
        
        $this->logger->log(
            'pengaturan_khusus_deleted',
            "Pengaturan simpanan wajib khusus dihapus untuk periode {$khusus->bulan}/{$khusus->tahun}."
        );
        
        $khusus->delete();

        return back()->with('success', 'Pengaturan khusus berhasil dihapus.');
    }
}
