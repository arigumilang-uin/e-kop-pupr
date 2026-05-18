<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\Ledger;
use App\Models\VoidRequest;
use App\Services\VoidService;
use Illuminate\Http\Request;

class VoidController extends Controller
{
    public function __construct(
        private VoidService $voidService,
    ) {}

    /**
     * Halaman utama: Riwayat Jurnal Ledger + Daftar Void Request.
     */
    public function index(Request $request)
    {
        // Tab aktif
        $tab = $request->input('tab', 'ledger');

        // === Tab 1: Jurnal Ledger ===
        if ($tab === 'ledger') {
            $ledgerQuery = Ledger::with(['anggota', 'pencatat', 'voidOf'])
                ->withExists([
                    'reversals as is_voided',
                    'voidRequests as has_pending_void' => function ($q) {
                        $q->where('status', 'menunggu');
                    },
                ])
                ->latest('created_at')
                ->latest('id');

            if ($request->filled('q')) {
                $ledgerQuery->where(function ($q) use ($request) {
                    $q->where('no_referensi', 'like', "%{$request->q}%")
                      ->orWhere('deskripsi', 'like', "%{$request->q}%")
                      ->orWhereHas('anggota', function ($aq) use ($request) {
                          $aq->where('nama', 'like', "%{$request->q}%")
                            ->orWhere('nip', 'like', "%{$request->q}%");
                      });
                });
            }

            if ($request->filled('tipe')) {
                $ledgerQuery->where('tipe', $request->tipe);
            }

            if ($request->filled('kategori')) {
                $ledgerQuery->where('kategori', $request->kategori);
            }

            if ($request->filled('dari_tanggal')) {
                $ledgerQuery->where('tanggal_efektif', '>=', $request->dari_tanggal);
            }

            if ($request->filled('sampai_tanggal')) {
                $ledgerQuery->where('tanggal_efektif', '<=', $request->sampai_tanggal);
            }

            $ledgerEntries = $ledgerQuery->paginate(20, ['*'], 'ledger_page')->withQueryString();
        } else {
            $ledgerEntries = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20);
        }

        // === Tab 2: Void Requests ===
        if ($tab === 'void') {
            $voidQuery = VoidRequest::with(['ledger.anggota', 'pemohon', 'pemutus', 'reversalLedger'])
                ->latest('tanggal_permintaan');

            if ($request->filled('void_status')) {
                $voidQuery->where('status', $request->void_status);
            }

            if ($request->filled('q')) {
                $voidQuery->where(function ($q) use ($request) {
                    $q->where('alasan', 'like', "%{$request->q}%")
                      ->orWhereHas('ledger', function ($lq) use ($request) {
                          $lq->where('no_referensi', 'like', "%{$request->q}%")
                            ->orWhere('deskripsi', 'like', "%{$request->q}%")
                            ->orWhereHas('anggota', function ($aq) use ($request) {
                                $aq->where('nama', 'like', "%{$request->q}%")
                                  ->orWhere('nip', 'like', "%{$request->q}%");
                            });
                      });
                });
            }

            $voidRequests = $voidQuery->paginate(15, ['*'], 'void_page')->withQueryString();
        } else {
            $voidRequests = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15);
        }

        $pendingCount = $this->voidService->pendingCount();

        return view('void.index', compact('tab', 'ledgerEntries', 'voidRequests', 'pendingCount'));
    }



    /**
     * Proses submit permintaan void (Maker).
     */
    public function store(Request $request)
    {
        $request->validate([
            'ledger_id' => 'required|exists:ledger,id',
            'alasan' => 'required|string|min:10|max:1000',
        ], [
            'alasan.required' => 'Alasan void wajib diisi.',
            'alasan.min' => 'Alasan minimal 10 karakter agar jelas.',
        ]);

        $ledger = Ledger::findOrFail($request->ledger_id);

        try {
            $this->voidService->requestVoid($ledger, $request->alasan, auth()->id());
            return redirect()->route('void.index', ['tab' => 'void'])->with('success', 'Permintaan void berhasil diajukan. Menunggu persetujuan Pimpinan.');
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }



    /**
     * Pimpinan approve void request.
     */
    public function approve(Request $request, VoidRequest $voidRequest)
    {
        $request->validate([
            'catatan' => 'nullable|string|max:1000',
        ]);

        try {
            $this->voidService->approveVoid($voidRequest, auth()->id(), $request->catatan);
            return redirect()->route('void.index', ['tab' => 'void'])->with('success', 'Void berhasil disetujui. Entry reversal telah dibuat di jurnal.');
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Pimpinan reject void request.
     */
    public function reject(Request $request, VoidRequest $voidRequest)
    {
        $request->validate([
            'catatan_penolakan' => 'required|string|min:5|max:1000',
        ], [
            'catatan_penolakan.required' => 'Alasan penolakan wajib diisi.',
        ]);

        try {
            $this->voidService->rejectVoid($voidRequest, auth()->id(), $request->catatan_penolakan);
            return redirect()->route('void.index', ['tab' => 'void'])->with('success', 'Permintaan void berhasil ditolak.');
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
