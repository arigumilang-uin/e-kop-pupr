<?php

namespace App\Http\Controllers\Keuangan;

use App\Enums\KategoriPeminjam;
use App\Http\Controllers\Controller;
use App\Http\Requests\PiutangEksternal\StorePembayaranRequest;
use App\Http\Requests\PiutangEksternal\StorePiutangEksternalRequest;
use App\Models\PembayaranPiutangEksternal;
use App\Models\PiutangEksternal;
use App\Services\PiutangEksternalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PiutangEksternalController extends Controller
{
    public function __construct(
        private PiutangEksternalService $service,
    ) {}

    /**
     * Daftar semua piutang lain-lain.
     */
    public function index(Request $request)
    {
        $query = PiutangEksternal::with('pencatat')
            ->withCount('pembayaran');

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter kategori
        if ($request->filled('kategori')) {
            $query->where('kategori_peminjam', $request->kategori);
        }

        // Filter tahun
        if ($request->filled('tahun')) {
            $query->where('tahun_pinjam', $request->tahun);
        }

        // Search
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($qb) use ($q) {
                $qb->where('nama_peminjam', 'like', "%{$q}%")
                   ->orWhere('jabatan_peminjam', 'like', "%{$q}%")
                   ->orWhere('no_referensi', 'like', "%{$q}%");
            });
        }

        $piutangs = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

        // Aggregates
        $totalAktif = PiutangEksternal::aktif()->sum('sisa_piutang');
        $totalLunas = PiutangEksternal::lunas()->sum('nominal_awal');
        $countAktif = PiutangEksternal::aktif()->count();
        $sisaPerTahun = $this->service->sisaPerTahun();

        $kategoriOptions = KategoriPeminjam::cases();
        $tahunOptions = PiutangEksternal::selectRaw('DISTINCT tahun_pinjam')
            ->orderByDesc('tahun_pinjam')
            ->pluck('tahun_pinjam');

        return view('piutang-eksternal.index', compact(
            'piutangs', 'totalAktif', 'totalLunas', 'countAktif',
            'sisaPerTahun', 'kategoriOptions', 'tahunOptions'
        ));
    }

    /**
     * Simpan piutang baru.
     */
    public function store(StorePiutangEksternalRequest $request)
    {
        $this->service->store($request->validated(), auth()->id());

        return back()->with('success', 'Piutang Lain-Lain berhasil dicatat.');
    }

    /**
     * Detail piutang + riwayat pembayaran.
     */
    public function show(PiutangEksternal $piutangEksternal)
    {
        $piutangEksternal->load(['pembayaran.pencatat', 'pencatat']);

        return view('piutang-eksternal.show', [
            'piutang' => $piutangEksternal,
        ]);
    }

    /**
     * Update data piutang (deskriptif saja).
     */
    public function update(Request $request, PiutangEksternal $piutangEksternal)
    {
        $validated = $request->validate([
            'nama_peminjam' => ['required', 'string', 'max:150'],
            'jabatan_peminjam' => ['nullable', 'string', 'max:100'],
            'kategori_peminjam' => [
                'required',
                new \Illuminate\Validation\Rules\Enum(\App\Enums\KategoriPeminjam::class),
                function ($attribute, $value, $fail) use ($piutangEksternal) {
                    if ($value === \App\Enums\KategoriPeminjam::Anggota->value && (int) $piutangEksternal->tahun_pinjam >= now()->year) {
                        $fail('Untuk Piutang Anggota, data hanya valid untuk pinjaman dari periode tahun sebelum ' . now()->year . ' (data ini tercatat di tahun ' . $piutangEksternal->tahun_pinjam . ').');
                    }
                },
            ],
            'keterangan' => ['nullable', 'string', 'max:1000'],
        ]);

        $this->service->update($piutangEksternal, $validated);

        return back()->with('success', 'Data piutang berhasil diperbarui.');
    }

    /**
     * Catat pembayaran piutang.
     */
    public function storePembayaran(StorePembayaranRequest $request, PiutangEksternal $piutangEksternal)
    {
        $buktiPath = null;
        if ($request->hasFile('bukti_bayar')) {
            $buktiPath = $request->file('bukti_bayar')->store('bukti_piutang', 'public');
        }

        try {
            $this->service->catatPembayaran(
                $piutangEksternal,
                (float) $request->nominal,
                $request->tanggal_bayar,
                $buktiPath,
                $request->keterangan,
                auth()->id(),
            );

            return back()->with('success', 'Pembayaran berhasil dicatat.');
        } catch (\InvalidArgumentException $e) {
            // Hapus file yang sudah diupload jika gagal
            if ($buktiPath && Storage::disk('public')->exists($buktiPath)) {
                Storage::disk('public')->delete($buktiPath);
            }
            return back()->withErrors(['nominal' => $e->getMessage()]);
        }
    }

    /**
     * Hapus pembayaran (reverse).
     */
    public function destroyPembayaran(PiutangEksternal $piutangEksternal, PembayaranPiutangEksternal $pembayaran)
    {
        $this->service->hapusPembayaran($pembayaran, auth()->id());

        return back()->with('success', 'Pembayaran berhasil dibatalkan.');
    }
}
