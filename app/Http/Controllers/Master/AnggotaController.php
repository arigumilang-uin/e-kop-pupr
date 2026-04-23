<?php

namespace App\Http\Controllers\Master;

use App\Enums\StatusAnggota;
use App\Http\Controllers\Controller;
use App\Http\Requests\Anggota\StoreAnggotaRequest;
use App\Http\Requests\Anggota\UpdateAnggotaRequest;
use App\Models\Anggota;
use App\Models\Bidang;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;

class AnggotaController extends Controller
{
    public function __construct(
        private ActivityLogService $logger,
    ) {}

    public function index(Request $request)
    {
        $query = Anggota::with('bidang')->latest();

        if ($request->filled('q')) {
            $query->where('nama', 'like', "%{$request->q}%")
                  ->orWhere('nip', 'like', "%{$request->q}%");
        }

        $anggotas = $query->paginate(10)->withQueryString();

        return view('anggota.index', compact('anggotas'));
    }

    public function create()
    {
        $bidangs = Bidang::orderBy('nama_bidang')->get();
        return view('anggota.create', compact('bidangs'));
    }

    public function store(StoreAnggotaRequest $request)
    {
        $anggota = Anggota::create([
            ...$request->validated(),
            'tanggal_masuk' => $request->input('tanggal_masuk', now()->format('Y-m-d')),
            'status' => StatusAnggota::Aktif,
        ]);

        $this->logger->log(
            'anggota_created',
            "Anggota baru terdaftar: {$anggota->nama} ({$anggota->nip})",
            dataBaru: $anggota->toArray()
        );

        return redirect()->route('anggota.index')->with('success', 'Data anggota berhasil ditambahkan.');
    }

    public function show(Anggota $anggotum)
    {
        $anggota = collect([$anggotum])->first();
        if(!$anggota->id) $anggota = request()->route('anggota');

        $anggota->load(['bidang', 'simpanan.jenisSimpanan', 'pinjaman' => function($q) {
            $q->whereIn('status', ['berjalan', 'lunas', 'menunggu', 'ditinjau'])->with('angsuran');
        }]);

        // Hitung total simpanan per jenis
        $simpananPerJenis = $anggota->simpanan->groupBy('jenisSimpanan.nama')->map(function($items) {
            return $items->sum('nominal');
        });
        $totalSimpanan = $anggota->simpanan->sum('nominal');

        // Hitung piutang aktif tersisa (berdasarkan angsuran yang belum lunas)
        $pinjamanAktif = $anggota->pinjaman->filter(fn($p) => $p->status->value === 'berjalan');
        $sisaUtang = 0;
        foreach($pinjamanAktif as $p) {
            $lunas = $p->angsuran->filter(fn($a) => $a->status->value === 'lunas')->sum('nominal_total');
            $sisaUtang += ($p->total_bayar - $lunas);
        }

        return view('anggota.show', compact('anggota', 'simpananPerJenis', 'totalSimpanan', 'pinjamanAktif', 'sisaUtang'));
    }

    public function edit(Anggota $anggotum) // Laravel resource binding weirdness workaround
    {
        $anggota = collect([$anggotum])->first();
        if(!$anggota->id) $anggota = request()->route('anggota');

        $bidangs = Bidang::orderBy('nama_bidang')->get();
        return view('anggota.edit', compact('anggota', 'bidangs'));
    }

    public function update(UpdateAnggotaRequest $request, Anggota $anggotum)
    {
        $anggota = collect([$anggotum])->first();
        if(!$anggota->id) $anggota = request()->route('anggota');

        $anggota->update($request->validated());

        $this->logger->log(
            'anggota_updated',
            "Data anggota diubah: {$anggota->nama} ({$anggota->nip})"
        );

        return redirect()->route('anggota.index')->with('success', 'Data anggota berhasil diperbarui.');
    }

    public function destroy(Anggota $anggotum)
    {
        // Fitur Nonaktifkan (Bukan hapus beneran agar relasi historical kas tidak error)
        $anggota = collect([$anggotum])->first();
        if(!$anggota->id) $anggota = request()->route('anggota');

        $statusBaru = $anggota->status === StatusAnggota::Aktif ? StatusAnggota::Pensiun : StatusAnggota::Aktif;
        
        $anggota->update(['status' => $statusBaru]);

        $this->logger->log(
            'anggota_status_changed',
            "Status anggota {$anggota->nama} diubah menjadi {$statusBaru->value}"
        );

        return back()->with('success', "Status anggota berhasil diubah menjadi {$statusBaru->label()}");
    }
}
