<?php

namespace App\Http\Controllers\Periode;

use App\Enums\StatusPeriode;
use App\Http\Controllers\Controller;
use App\Http\Requests\Periode\StorePeriodeRequest;
use App\Http\Requests\Periode\UpdatePeriodeRequest;
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
            ->when(request('q'), function ($query, $q) {
                $query->where('nama_periode', 'like', "%{$q}%");
            })
            ->withCount('pinjaman')
            ->latest('tahun')
            ->latest('created_at')
            ->paginate(10);

        $totalPengajuan = \App\Models\Pinjaman::count();

        return view('periode.index', compact('periodes', 'totalPengajuan'));
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
        $tanggalBuka = \Carbon\Carbon::parse($request->tanggal_buka)->startOfDay();
        $tanggalTutup = \Carbon\Carbon::parse($request->tanggal_tutup)->startOfDay();
        $today = now()->startOfDay();

        // Validasi 1: tanggal_buka tidak boleh sebelum hari ini
        if ($tanggalBuka->lessThan($today)) {
            return back()->withInput()->with('error',
                'Tanggal buka tidak boleh sebelum hari ini (' . $today->translatedFormat('d F Y') . '). Periode pinjaman hanya bisa dibuka mulai hari ini atau seterusnya.'
            );
        }

        // Validasi 2: cek apakah range tanggal bentrok dengan periode lain yang sudah ada
        $periodeBentrok = PeriodePinjaman::where(function ($query) use ($tanggalBuka, $tanggalTutup) {
            // Overlap terjadi jika: existing_buka <= new_tutup AND existing_tutup >= new_buka
            $query->whereDate('tanggal_buka', '<=', $tanggalTutup)
                  ->whereDate('tanggal_tutup', '>=', $tanggalBuka);
        })->first();

        if ($periodeBentrok) {
            return back()->withInput()->with('error',
                "Tanggal yang dipilih bentrok dengan periode \"{$periodeBentrok->nama_periode}\" "
                . "({$periodeBentrok->tanggal_buka->translatedFormat('d M Y')} — {$periodeBentrok->tanggal_tutup->translatedFormat('d M Y')}). "
                . "Silakan pilih rentang tanggal yang tidak tumpang tindih."
            );
        }

        // Auto-determine status: hari ini = Buka, masa depan = Terjadwal
        $initialStatus = $tanggalBuka->equalTo($today) ? StatusPeriode::Buka : StatusPeriode::Terjadwal;

        $periode = PeriodePinjaman::create([
            ...$request->validated(),
            'token' => Str::random(32),
            'status' => $initialStatus,
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
     * Form edit periode pinjaman.
     */
    public function edit(PeriodePinjaman $periode)
    {
        $periode->loadCount('pinjaman');
        $adaPengajuan = $periode->pinjaman_count > 0;

        $defaults = [
            'batas_bulan' => $this->pengaturanService->batasBulanPelunasan(),
        ];

        return view('periode.edit', compact('periode', 'adaPengajuan', 'defaults'));
    }

    /**
     * Update periode pinjaman.
     * - Jika belum ada pengajuan: full edit semua field
     * - Jika sudah ada pengajuan: hanya nama_periode, tanggal_tutup, catatan
     */
    public function update(UpdatePeriodeRequest $request, PeriodePinjaman $periode)
    {
        $adaPengajuan = $periode->pinjaman()->exists();
        $dataLama = $periode->toArray();

        if ($adaPengajuan) {
            // Partial update — hanya field aman
            $periode->update($request->only(['nama_periode', 'tanggal_tutup', 'catatan']));
        } else {
            // Full update — validasi overlap tanggal
            $tanggalBuka = \Carbon\Carbon::parse($request->tanggal_buka)->startOfDay();
            $tanggalTutup = \Carbon\Carbon::parse($request->tanggal_tutup)->startOfDay();

            $periodeBentrok = PeriodePinjaman::where('id', '!=', $periode->id)
                ->where(function ($query) use ($tanggalBuka, $tanggalTutup) {
                    $query->whereDate('tanggal_buka', '<=', $tanggalTutup)
                          ->whereDate('tanggal_tutup', '>=', $tanggalBuka);
                })->first();

            if ($periodeBentrok) {
                return back()->withInput()->with('error',
                    "Tanggal bentrok dengan periode \"{$periodeBentrok->nama_periode}\" "
                    . "({$periodeBentrok->tanggal_buka->translatedFormat('d M Y')} — {$periodeBentrok->tanggal_tutup->translatedFormat('d M Y')})."
                );
            }

            $periode->update($request->validated());
        }

        $this->logger->log(
            'periode_diupdate',
            "Periode '{$periode->nama_periode}' diperbarui.",
            dataLama: $dataLama,
            dataBaru: $periode->fresh()->toArray(),
        );

        return redirect()->route('periode.show', $periode)
            ->with('success', 'Periode pinjaman berhasil diperbarui.');
    }

    /**
     * Hapus periode pinjaman (hanya jika belum ada pengajuan).
     */
    public function destroy(PeriodePinjaman $periode)
    {
        if ($periode->pinjaman()->exists()) {
            return back()->with('error', 'Periode tidak dapat dihapus karena sudah memiliki pengajuan pinjaman.');
        }

        $nama = $periode->nama_periode;
        $periode->delete();

        $this->logger->log(
            'periode_dihapus',
            "Periode '{$nama}' dihapus.",
        );

        return redirect()->route('periode.index')
            ->with('success', "Periode \"{$nama}\" berhasil dihapus.");
    }

    /**
     * Tutup periode (tidak bisa menerima pengajuan baru).
     */
    public function tutup(PeriodePinjaman $periode)
    {
        $periode->update([
            'status' => StatusPeriode::Tutup,
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
        // Validasi: cek overlap dengan periode lain yang tidak Tutup
        $periodeBentrok = PeriodePinjaman::whereIn('status', [StatusPeriode::Buka, StatusPeriode::Terjadwal])
            ->where('id', '!=', $periode->id)
            ->where(function ($query) use ($periode) {
                $query->whereDate('tanggal_buka', '<=', $periode->tanggal_tutup)
                      ->whereDate('tanggal_tutup', '>=', $periode->tanggal_buka);
            })
            ->first();

        if ($periodeBentrok) {
            return back()->with('error',
                "Tidak dapat membuka kembali. Rentang tanggal bentrok dengan periode \"{$periodeBentrok->nama_periode}\" "
                . "({$periodeBentrok->tanggal_buka->translatedFormat('d M Y')} — {$periodeBentrok->tanggal_tutup->translatedFormat('d M Y')})."
            );
        }

        // Auto-determine status saat dibuka kembali
        $today = now()->startOfDay();
        $newStatus = $today->lessThan($periode->tanggal_buka) ? StatusPeriode::Terjadwal : StatusPeriode::Buka;

        $periode->update([
            'status' => $newStatus,
        ]);

        $this->logger->log(
            'periode_dibuka_kembali',
            "Periode '{$periode->nama_periode}' dibuka kembali dengan status {$newStatus->label()}.",
        );

        return back()->with('success', "Periode pinjaman berhasil dibuka kembali (Status: {$newStatus->label()}).");
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
