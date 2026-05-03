<?php

namespace App\Http\Controllers\Master;

use App\Enums\StatusAnggota;
use App\Http\Controllers\Controller;
use App\Http\Requests\Anggota\StoreAnggotaRequest;
use App\Http\Requests\Anggota\UpdateAnggotaRequest;
use App\Models\Anggota;
use App\Models\ArsipKeluarAnggota;
use App\Models\Bidang;
use App\Services\ActivityLogService;
use App\Services\AnggotaKeluarService;
use Illuminate\Http\Request;

class AnggotaController extends Controller
{
    public function __construct(
        private ActivityLogService $logger,
        private AnggotaKeluarService $keluarService,
    ) {}

    public function index(Request $request)
    {
        $query = Anggota::with('bidang')->orderBy('nama');

        if ($request->filled('q')) {
            $query->where(function($q) use ($request) {
                $q->where('nama', 'like', "%{$request->q}%")
                  ->orWhere('nip', 'like', "%{$request->q}%");
            });
        }

        if ($request->filled('bidang')) {
            $query->where('bidang_id', $request->bidang);
        }

        if ($request->filled('golongan')) {
            $query->where('golongan_asn', $request->golongan);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $anggotas = $query->paginate(10)->withQueryString();
        $bidangs = Bidang::orderBy('nama_bidang')->get();

        return view('anggota.index', compact('anggotas', 'bidangs'));
    }

    public function create()
    {
        $bidangs = Bidang::orderBy('nama_bidang')->get();

        // Cek apakah ada arsip keluar (untuk pendaftar ulang)
        $arsipKeluar = null;
        if (request()->filled('nip_ulang')) {
            $anggotaLama = Anggota::where('nip', request('nip_ulang'))
                ->where('status', StatusAnggota::Nonaktif)
                ->first();

            if ($anggotaLama) {
                $arsipKeluar = ArsipKeluarAnggota::where('anggota_id', $anggotaLama->id)
                    ->latest()
                    ->first();
            }
        }

        return view('anggota.create', compact('bidangs', 'arsipKeluar'));
    }

    public function store(StoreAnggotaRequest $request)
    {
        // Cek apakah ini pendaftar ulang
        $isPendaftarUlang = false;
        $arsipId = null;

        if ($request->filled('arsip_keluar_id')) {
            $arsip = ArsipKeluarAnggota::findOrFail($request->arsip_keluar_id);
            $isPendaftarUlang = true;
            $arsipId = $arsip->id;
        }

        $anggota = Anggota::create([
            ...$request->validated(),
            'tanggal_masuk' => $request->input('tanggal_masuk', now()->format('Y-m-d')),
            'status' => StatusAnggota::Aktif,
            'is_pendaftar_ulang' => $isPendaftarUlang,
        ]);

        $pesan = "Anggota baru terdaftar: {$anggota->nama} ({$anggota->nip})";
        if ($isPendaftarUlang) {
            $pesan .= " [PENDAFTAR ULANG — Wajib setor: " . format_rupiah($arsip->nominal_wajib_setor_ulang) . "]";
        }

        $this->logger->log('anggota_created', $pesan, dataBaru: $anggota->toArray());

        return redirect()->route('anggota.index')->with('success', 'Data anggota berhasil ditambahkan.' . ($isPendaftarUlang ? ' (Pendaftar Ulang)' : ''));
    }

    public function show(Anggota $anggotum)
    {
        $anggota = collect([$anggotum])->first();
        if(!$anggota->id) $anggota = request()->route('anggota');

        $anggota->load(['bidang', 'simpanan.jenisSimpanan', 'pinjaman' => function($q) {
            $q->whereIn('status', ['berjalan', 'lunas', 'menunggu', 'ditinjau'])->with('angsuran');
        }]);

        // Hitung neto simpanan per jenis
        $simpananGroup = $anggota->simpanan->groupBy('jenisSimpanan.nama')->map(fn($i) => $i->sum('nominal'));
        $anggota->load('penarikanSimpanan.jenisSimpanan');
        $tarikGroup = $anggota->penarikanSimpanan->groupBy('jenisSimpanan.nama')->map(fn($i) => $i->sum('nominal'));

        $simpananPerJenis = collect();
        $totalSimpanan = 0;

        foreach ($simpananGroup as $nama => $bruto) {
            $tarik = $tarikGroup->get($nama, 0);
            $neto = $bruto - $tarik;
            if ($neto > 0) {
                $simpananPerJenis->put($nama, $neto);
                $totalSimpanan += $neto;
            }
        }

        // Hitung piutang aktif tersisa
        $pinjamanAktif = $anggota->pinjaman->filter(fn($p) => $p->status->value === 'berjalan');
        $sisaUtang = 0;
        foreach($pinjamanAktif as $p) {
            $lunas = $p->angsuran->filter(fn($a) => $a->status->value === 'lunas')->sum('nominal_total');
            $sisaUtang += ($p->total_bayar - $lunas);
        }

        // Arsip keluar (jika pernah keluar sebelumnya)
        $arsipKeluar = ArsipKeluarAnggota::where('anggota_id', $anggota->id)->latest()->get();

        return view('anggota.show', compact('anggota', 'simpananPerJenis', 'totalSimpanan', 'pinjamanAktif', 'sisaUtang', 'arsipKeluar'));
    }

    public function edit(Anggota $anggotum)
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

        $this->logger->log('anggota_updated', "Data anggota diubah: {$anggota->nama} ({$anggota->nip})");

        return redirect()->route('anggota.index')->with('success', 'Data anggota berhasil diperbarui.');
    }

    public function destroy(Anggota $anggotum)
    {
        $anggota = collect([$anggotum])->first();
        if(!$anggota->id) $anggota = request()->route('anggota');

        // Jika anggota AKTIF → redirect ke halaman analisis keluar (flow keuangan)
        if ($anggota->status === StatusAnggota::Aktif) {
            return redirect()->route('anggota.keluar', $anggota);
        }

        // Jika anggota NON-AKTIF → redirect ke halaman konfirmasi reaktivasi
        return redirect()->route('anggota.reaktivasi', $anggota);
    }

    /**
     * Halaman konfirmasi reaktivasi anggota (GET).
     * Tampilkan arsip keluar + rincian wajib setor ulang.
     */
    public function reaktivasiForm(Anggota $anggota)
    {
        if ($anggota->status === StatusAnggota::Aktif) {
            return redirect()->route('anggota.show', $anggota)
                ->with('warning', 'Anggota ini sudah aktif.');
        }

        $arsipKeluar = ArsipKeluarAnggota::where('anggota_id', $anggota->id)
            ->latest()
            ->first();

        $anggota->load('bidang');

        return view('anggota.reaktivasi', compact('anggota', 'arsipKeluar'));
    }

    /**
     * Proses reaktivasi anggota (POST).
     * Otomatis membuat record simpanan sesuai arsip keluar.
     */
    public function reaktivasiProses(Request $request, Anggota $anggota)
    {
        $request->validate([
            'konfirmasi' => 'required|accepted',
        ]);

        if ($anggota->status === StatusAnggota::Aktif) {
            return back()->with('error', 'Anggota sudah aktif.');
        }

        $arsipKeluar = ArsipKeluarAnggota::where('anggota_id', $anggota->id)
            ->latest()
            ->first();

        \DB::transaction(function () use ($anggota, $arsipKeluar) {
            // Jika ada arsip keluar → otomatis buat simpanan baru sesuai rincian
            if ($arsipKeluar && $arsipKeluar->rincian_simpanan) {
                $jenisMap = \App\Models\JenisSimpanan::pluck('id', 'kode');

                foreach ($arsipKeluar->rincian_simpanan as $item) {
                    $jenisId = $jenisMap[$item['kode']] ?? null;
                    if (!$jenisId || $item['nominal'] <= 0) continue;

                    \App\Models\Simpanan::create([
                        'anggota_id' => $anggota->id,
                        'jenis_simpanan_id' => $jenisId,
                        'nominal' => $item['nominal'],
                        'tanggal' => now()->toDateString(),
                        'keterangan' => "Setoran wajib pendaftar ulang — berdasar arsip keluar #{$arsipKeluar->id}",
                    ]);
                }
            }

            $anggota->update([
                'status' => StatusAnggota::Aktif,
                'tanggal_keluar' => null,
                'tanggal_masuk' => now()->toDateString(),
                'is_pendaftar_ulang' => $arsipKeluar ? true : false,
            ]);
        });

        $pesan = "Anggota {$anggota->nama} berhasil diaktifkan kembali.";
        if ($arsipKeluar) {
            $pesan .= " Simpanan sebesar " . format_rupiah($arsipKeluar->nominal_wajib_setor_ulang) . " telah otomatis dicatat.";
        }

        $this->logger->log(
            'anggota_reaktivasi',
            "Anggota {$anggota->nama} ({$anggota->nip}) diaktifkan kembali." .
            ($arsipKeluar ? " Simpanan otomatis disetor: " . format_rupiah($arsipKeluar->nominal_wajib_setor_ulang) : ''),
        );

        return redirect()->route('anggota.show', $anggota)->with('success', $pesan);
    }

    // =============================================
    //  PROSES KELUAR ANGGOTA
    // =============================================

    /**
     * Halaman analisis pra-keluar anggota.
     */
    public function keluarAnalisis(Anggota $anggota)
    {
        if ($anggota->status !== StatusAnggota::Aktif) {
            return back()->with('error', 'Hanya anggota aktif yang dapat diproses keluar.');
        }

        $analisis = $this->keluarService->analisis($anggota);
        $anggota->load('bidang');

        return view('anggota.keluar', compact('anggota', 'analisis'));
    }

    /**
     * Eksekusi proses keluar anggota.
     */
    public function keluarProses(Request $request, Anggota $anggota)
    {
        $request->validate([
            'catatan' => 'nullable|string|max:500',
            'konfirmasi' => 'required|accepted',
        ]);

        if ($anggota->status !== StatusAnggota::Aktif) {
            return back()->with('error', 'Anggota ini sudah tidak aktif.');
        }

        $analisis = $this->keluarService->analisis($anggota);

        if (!$analisis['bisa_proses']) {
            return back()->with('error', 'Proses keluar tidak dapat dilanjutkan. Periksa pinjaman aktif atau kecukupan saldo.');
        }

        $arsip = $this->keluarService->proses(
            $anggota,
            auth()->id(),
            $request->catatan
        );

        $this->logger->log(
            'anggota_keluar',
            "Anggota {$anggota->nama} ({$anggota->nip}) keluar dari koperasi. Total simpanan dikembalikan: " . format_rupiah($arsip->total_simpanan_dikembalikan),
            dataBaru: $arsip->toArray()
        );

        return redirect()->route('anggota.show', $anggota)
            ->with('success', "Anggota {$anggota->nama} berhasil diproses keluar. Total simpanan " . format_rupiah($arsip->total_simpanan_dikembalikan) . " telah dikembalikan.");
    }
}
