<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shu\StoreShuKomponenRequest;
use App\Http\Requests\Shu\UpdateShuKomponenRequest;
use App\Http\Requests\Shu\StoreShuDistribusiRequest;
use App\Http\Requests\Shu\UpdateShuDistribusiRequest;
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

    public function storeKomponen(StoreShuKomponenRequest $request)
    {
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

    public function updateKomponen(UpdateShuKomponenRequest $request, ShuKomponen $komponen)
    {
        $komponen->update($request->validated());

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

    public function storeDistribusi(StoreShuDistribusiRequest $request)
    {
        $distribusi = ShuDistribusi::create([
            'nama'      => $request->nama,
            'persen'    => $request->persen,
            'deskripsi' => $request->deskripsi,
            'urutan'    => ShuDistribusi::max('urutan') + 1,
        ]);

        $this->logger->log('shu_distribusi_created', "Alokasi SHU '{$distribusi->nama}' ({$distribusi->persen}%) berhasil ditambahkan.");

        return back()->with('success', "Alokasi '{$distribusi->nama}' berhasil ditambahkan.");
    }

    public function updateDistribusi(UpdateShuDistribusiRequest $request, ShuDistribusi $distribusi)
    {
        $distribusi->update($request->validated());

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
