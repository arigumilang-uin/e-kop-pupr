<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\ShuKomponen;
use App\Models\ShuDistribusi;
use App\Models\Pinjaman;
use App\Services\ShuService;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;

class ShuController extends Controller
{
    public function __construct(
        private ShuService $shuService,
        private ActivityLogService $logger,
    ) {}

    /**
     * Halaman utama Simulasi SHU — perhitungan dinamis.
     */
    public function index(Request $request)
    {
        $tahun = $request->get('tahun', date('Y'));

        $shu = $this->shuService->hitung((int) $tahun);

        // Dropdown tahun
        $tahunTersedia = Pinjaman::selectRaw('YEAR(tanggal_approval) as tahun')
            ->whereNotNull('tanggal_approval')
            ->distinct()
            ->orderByDesc('tahun')
            ->pluck('tahun')
            ->toArray();

        if (!in_array(date('Y'), $tahunTersedia)) {
            $tahunTersedia[] = date('Y');
        }

        rsort($tahunTersedia);

        // Komponen & Distribusi untuk panel konfigurasi
        $komponenAll = ShuKomponen::orderBy('tipe')->orderBy('urutan')->get();
        $distribusiAll = ShuDistribusi::orderBy('urutan')->get();
        $sumberTersedia = $this->shuService->sumberDataTersedia();

        return view('keuangan.shu.index', compact(
            'tahun', 'tahunTersedia', 'shu',
            'komponenAll', 'distribusiAll', 'sumberTersedia'
        ));
    }

    // =============================================
    //  CRUD Komponen SHU
    // =============================================

    public function storeKomponen(Request $request)
    {
        $request->validate([
            'nama'        => 'required|string|max:100',
            'tipe'        => 'required|in:pendapatan,beban',
            'sumber_data' => 'required|string|max:50',
            'deskripsi'   => 'nullable|string',
        ]);

        $komponen = ShuKomponen::create([
            'nama'        => $request->nama,
            'tipe'        => $request->tipe,
            'sumber_data' => $request->sumber_data,
            'deskripsi'   => $request->deskripsi,
            'urutan'      => ShuKomponen::where('tipe', $request->tipe)->max('urutan') + 1,
        ]);

        $this->logger->log('shu_komponen_created', "Komponen SHU '{$komponen->nama}' ({$komponen->tipe}) berhasil ditambahkan.");

        return back()->with('success', "Komponen '{$komponen->nama}' berhasil ditambahkan.");
    }

    public function updateKomponen(Request $request, ShuKomponen $komponen)
    {
        $request->validate([
            'nama'        => 'required|string|max:100',
            'tipe'        => 'required|in:pendapatan,beban',
            'sumber_data' => 'required|string|max:50',
            'deskripsi'   => 'nullable|string',
            'is_aktif'    => 'sometimes|boolean',
        ]);

        $komponen->update($request->only(['nama', 'tipe', 'sumber_data', 'deskripsi', 'is_aktif']));

        $this->logger->log('shu_komponen_updated', "Komponen SHU '{$komponen->nama}' berhasil diperbarui.");

        return back()->with('success', "Komponen '{$komponen->nama}' berhasil diperbarui.");
    }

    public function destroyKomponen(ShuKomponen $komponen)
    {
        $nama = $komponen->nama;
        $komponen->delete();

        $this->logger->log('shu_komponen_deleted', "Komponen SHU '{$nama}' berhasil dihapus.");

        return back()->with('success', "Komponen '{$nama}' berhasil dihapus.");
    }

    // =============================================
    //  CRUD Distribusi SHU
    // =============================================

    public function storeDistribusi(Request $request)
    {
        $request->validate([
            'nama'        => 'required|string|max:100',
            'persen'      => 'required|numeric|min:0|max:100',
            'deskripsi'   => 'nullable|string',
        ]);

        $distribusi = ShuDistribusi::create([
            'nama'      => $request->nama,
            'persen'    => $request->persen,
            'deskripsi' => $request->deskripsi,
            'urutan'    => ShuDistribusi::max('urutan') + 1,
        ]);

        $this->logger->log('shu_distribusi_created', "Alokasi SHU '{$distribusi->nama}' ({$distribusi->persen}%) berhasil ditambahkan.");

        return back()->with('success', "Alokasi '{$distribusi->nama}' berhasil ditambahkan.");
    }

    public function updateDistribusi(Request $request, ShuDistribusi $distribusi)
    {
        $request->validate([
            'nama'        => 'required|string|max:100',
            'persen'      => 'required|numeric|min:0|max:100',
            'deskripsi'   => 'nullable|string',
            'is_aktif'    => 'sometimes|boolean',
        ]);

        $distribusi->update($request->only(['nama', 'persen', 'deskripsi', 'is_aktif']));

        $this->logger->log('shu_distribusi_updated', "Alokasi SHU '{$distribusi->nama}' berhasil diperbarui.");

        return back()->with('success', "Alokasi '{$distribusi->nama}' berhasil diperbarui.");
    }

    public function destroyDistribusi(ShuDistribusi $distribusi)
    {
        $nama = $distribusi->nama;
        $distribusi->delete();

        $this->logger->log('shu_distribusi_deleted', "Alokasi SHU '{$nama}' berhasil dihapus.");

        return back()->with('success', "Alokasi '{$nama}' berhasil dihapus.");
    }
}
