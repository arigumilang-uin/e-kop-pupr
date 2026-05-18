<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\ShuKewajiban;
use App\Models\ShuRealisasiKewajiban;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ShuKewajibanController extends Controller
{
    public function __construct(
        private ActivityLogService $logger,
    ) {}

    /**
     * Halaman utama Kewajiban & Realisasi Dana SHU.
     */
    public function index(Request $request)
    {
        $tahunFilter = $request->input('tahun');

        $query = ShuKewajiban::withCount('realisasi')
            ->orderByDesc('tahun')
            ->orderBy('nama_alokasi');

        if ($tahunFilter) {
            $query->where('tahun', $tahunFilter);
        }

        $kewajibanAll = $query->get();

        // Tahun yang tersedia untuk filter
        $tahunTersedia = ShuKewajiban::selectRaw('DISTINCT tahun')
            ->orderByDesc('tahun')
            ->pluck('tahun')
            ->toArray();

        // Ringkasan global
        $ringkasan = [
            'total_nominal_awal' => $kewajibanAll->sum('nominal_awal'),
            'total_terpakai' => $kewajibanAll->sum('nominal_terpakai'),
            'total_sisa' => $kewajibanAll->sum('saldo_tersisa'),
            'jumlah_dompet' => $kewajibanAll->count(),
            'jumlah_lunas' => $kewajibanAll->where('saldo_tersisa', '<=', 0)->count(),
        ];

        return view('keuangan.shu-kewajiban.index', compact(
            'kewajibanAll', 'tahunTersedia', 'tahunFilter', 'ringkasan'
        ));
    }

    /**
     * Detail satu dompet kewajiban beserta histori realisasinya.
     */
    public function show(ShuKewajiban $kewajiban)
    {
        $kewajiban->load(['realisasi' => fn($q) => $q->orderByDesc('tanggal_realisasi'), 'realisasi.pencatat']);

        $persentaseTerpakai = $kewajiban->nominal_awal > 0
            ? round(($kewajiban->nominal_terpakai / $kewajiban->nominal_awal) * 100, 1)
            : 0;

        return view('keuangan.shu-kewajiban.show', compact('kewajiban', 'persentaseTerpakai'));
    }

    /**
     * Catat realisasi pengeluaran dari dompet kewajiban.
     */
    public function storeRealisasi(Request $request, ShuKewajiban $kewajiban)
    {
        $validated = $request->validate([
            'tanggal_realisasi' => 'required|date|before_or_equal:today',
            'nominal' => 'required|numeric|min:1',
            'keterangan' => 'required|string|max:500',
        ], [
            'nominal.min' => 'Nominal pengeluaran minimal Rp 1.',
            'tanggal_realisasi.before_or_equal' => 'Tanggal realisasi tidak boleh di masa depan.',
            'keterangan.required' => 'Keterangan pengeluaran wajib diisi.',
        ]);

        // Guard: pastikan saldo mencukupi
        if ($validated['nominal'] > $kewajiban->saldo_tersisa) {
            return back()->with('error', 'Nominal realisasi (Rp ' . number_format((float) $validated['nominal'], 0, ',', '.') . ') melebihi saldo tersisa (Rp ' . number_format((float) $kewajiban->saldo_tersisa, 0, ',', '.') . ').');
        }

        DB::transaction(function () use ($validated, $kewajiban) {
            // 1. Insert realisasi
            ShuRealisasiKewajiban::create([
                'shu_kewajiban_id' => $kewajiban->id,
                'tanggal_realisasi' => $validated['tanggal_realisasi'],
                'nominal' => $validated['nominal'],
                'keterangan' => $validated['keterangan'],
                'dicatat_oleh' => Auth::id(),
            ]);

            // 2. Update saldo dompet
            $kewajiban->increment('nominal_terpakai', $validated['nominal']);
            $kewajiban->update([
                'saldo_tersisa' => $kewajiban->nominal_awal - $kewajiban->nominal_terpakai,
            ]);
        });

        $this->logger->log(
            'shu_realisasi_created',
            "Realisasi dana \"{$kewajiban->nama_alokasi}\" (SHU {$kewajiban->tahun}) sebesar Rp " . number_format((float) $validated['nominal'], 0, ',', '.') . ". Keterangan: {$validated['keterangan']}",
        );

        return back()->with('success', 'Realisasi pengeluaran berhasil dicatat. Saldo tersisa: Rp ' . number_format((float) $kewajiban->saldo_tersisa, 0, ',', '.'));
    }

    /**
     * Hapus realisasi pengeluaran (rollback saldo).
     */
    public function destroyRealisasi(ShuKewajiban $kewajiban, ShuRealisasiKewajiban $realisasi)
    {
        // Guard: pastikan realisasi ini milik kewajiban yang benar
        if ($realisasi->shu_kewajiban_id !== $kewajiban->id) {
            abort(403);
        }

        $nominal = $realisasi->nominal;
        $keterangan = $realisasi->keterangan;

        DB::transaction(function () use ($realisasi, $kewajiban, $nominal) {
            $realisasi->delete();

            // Rollback saldo
            $kewajiban->decrement('nominal_terpakai', (float) $nominal);
            $kewajiban->update([
                'saldo_tersisa' => $kewajiban->nominal_awal - $kewajiban->nominal_terpakai,
            ]);
        });

        $this->logger->log(
            'shu_realisasi_deleted',
            "Realisasi \"{$keterangan}\" pada dompet \"{$kewajiban->nama_alokasi}\" (SHU {$kewajiban->tahun}) sebesar Rp " . number_format((float) $nominal, 0, ',', '.') . " telah dihapus/rollback.",
        );

        return back()->with('success', 'Realisasi berhasil dihapus dan saldo dikembalikan.');
    }
}
