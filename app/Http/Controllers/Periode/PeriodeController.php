<?php

namespace App\Http\Controllers\Periode;

use App\Enums\StatusPeriode;
use App\Http\Controllers\Controller;
use App\Http\Requests\Periode\StorePeriodeRequest;
use App\Models\PeriodePinjaman;
use App\Services\ActivityLogService;
use App\Services\PengaturanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PeriodeController extends Controller
{
    public function __construct(
        private PengaturanService $pengaturanService,
        private ActivityLogService $logger,
    ) {}

    /**
     * Daftar semua periode pinjaman.
     */
    public function index()
    {
        $periodes = PeriodePinjaman::with('pembuka')
            ->withCount('pinjaman')
            ->latest('tahun')
            ->latest('created_at')
            ->paginate(10);

        return view('periode.index', compact('periodes'));
    }

    /**
     * Form buat periode baru.
     */
    public function create()
    {
        $defaults = [
            'tahun' => (int) now()->format('Y'),
            'batas_bulan' => $this->pengaturanService->batasBulanPelunasan(),
        ];

        return view('periode.create', compact('defaults'));
    }

    /**
     * Simpan periode baru dan generate token unik.
     */
    public function store(StorePeriodeRequest $request)
    {
        $periode = PeriodePinjaman::create([
            ...$request->validated(),
            'token' => Str::random(32),
            'status' => StatusPeriode::Buka,
            'dibuka_oleh' => Auth::id(),
        ]);

        $this->logger->log(
            'periode_dibuka',
            "Periode '{$periode->nama_periode}' berhasil dibuat dan dibuka.",
            dataBaru: $periode->toArray(),
        );

        return redirect()->route('periode.show', $periode)
            ->with('success', 'Periode pinjaman berhasil dibuat. Salin link di bawah untuk dibagikan ke anggota.');
    }

    /**
     * Detail periode + link share.
     */
    public function show(PeriodePinjaman $periode)
    {
        $periode->load(['pembuka', 'pinjaman.anggota']);

        return view('periode.show', compact('periode'));
    }

    /**
     * Tutup periode (tidak bisa menerima pengajuan baru).
     */
    public function tutup(PeriodePinjaman $periode)
    {
        $periode->update([
            'status' => StatusPeriode::Tutup,
            'tanggal_tutup' => now(),
        ]);

        $this->logger->log(
            'periode_ditutup',
            "Periode '{$periode->nama_periode}' ditutup.",
        );

        return back()->with('success', 'Periode pinjaman berhasil ditutup.');
    }

    /**
     * Buka kembali periode yang telah ditutup.
     */
    public function buka(PeriodePinjaman $periode)
    {
        $periode->update([
            'status' => StatusPeriode::Buka,
        ]);

        $this->logger->log(
            'periode_dibuka_kembali',
            "Periode '{$periode->nama_periode}' dibuka kembali.",
        );

        return back()->with('success', 'Periode pinjaman berhasil dibuka kembali.');
    }

    /**
     * Generate token baru (reset link).
     */
    public function resetToken(PeriodePinjaman $periode)
    {
        $tokenLama = $periode->token;

        $periode->update([
            'token' => Str::random(32),
        ]);

        $this->logger->log(
            'periode_reset_token',
            "Token link periode '{$periode->nama_periode}' di-reset.",
            dataLama: ['token' => $tokenLama],
            dataBaru: ['token' => $periode->token],
        );

        return back()->with('success', 'Link pengajuan berhasil di-reset. Link lama tidak berlaku lagi.');
    }
}
