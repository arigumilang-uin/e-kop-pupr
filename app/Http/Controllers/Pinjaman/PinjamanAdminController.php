<?php

namespace App\Http\Controllers\Pinjaman;

use App\Enums\StatusPinjaman;
use App\Http\Controllers\Controller;
use App\Models\Pinjaman;
use App\Services\ActivityLogService;
use App\Services\LedgerService;
use App\Services\SaldoService;
use App\Http\Requests\Pinjaman\RejectPinjamanRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PinjamanAdminController extends Controller
{
    public function __construct(
        private ActivityLogService $logger,
        private SaldoService $saldoService,
        private LedgerService $ledgerService,
    ) {}

    public function index(Request $request)
    {
        $query = Pinjaman::with(['anggota' => function ($q) {
            $q->with('bidang')
              ->withCount([
                  'pinjaman as pinjaman_aktif_tahun_ini' => function ($q2) {
                      $q2->where('status', \App\Enums\StatusPinjaman::Berjalan)
                         ->whereYear('tanggal_pengajuan', now()->year);
                  },
                  'pinjaman as pinjaman_menunggu' => function ($q3) {
                      $q3->where('status', \App\Enums\StatusPinjaman::Menunggu);
                  }
              ]);
        }, 'periodePinjaman'])->latest('tanggal_pengajuan');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('bidang_id')) {
            $query->whereHas('anggota', function ($q) use ($request) {
                $q->where('bidang_id', $request->bidang_id);
            });
        }

        if ($request->filled('periode_id')) {
            $query->where('periode_pinjaman_id', $request->periode_id);
        }

        if ($request->filled('indikator')) {
            if ($request->indikator === 'ganda') {
                $query->where('status', \App\Enums\StatusPinjaman::Menunggu)
                      ->whereHas('anggota', function ($q) {
                          $q->whereHas('pinjaman', function ($q2) {
                              $q2->where('status', \App\Enums\StatusPinjaman::Menunggu);
                          }, '>', 1);
                      });
            } elseif ($request->indikator === 'aktif') {
                $query->where('status', \App\Enums\StatusPinjaman::Menunggu)
                      ->whereHas('anggota', function ($q) {
                          $q->whereHas('pinjaman', function ($q2) {
                              $q2->where('status', \App\Enums\StatusPinjaman::Berjalan)
                                 ->whereYear('tanggal_pengajuan', now()->year);
                          });
                      });
            }
        }

        if ($request->filled('q')) {
            $query->where(function($qq) use ($request) {
                $qq->whereHas('anggota', function ($q) use ($request) {
                    $q->where('nama', 'like', "%{$request->q}%")
                      ->orWhere('nip', 'like', "%{$request->q}%");
                })->orWhere('no_referensi', 'like', "%{$request->q}%");
            });
        }

        $pinjamans = $query->paginate(15)->withQueryString();
        $totalSaldoKoperasi = $this->saldoService->saldoKoperasi();
        
        $bidangs = \App\Models\Bidang::orderBy('nama_bidang')->get();
        $periodes = \App\Models\PeriodePinjaman::latest('tanggal_buka')->get();

        return view('pinjaman.admin.index', compact('pinjamans', 'totalSaldoKoperasi', 'bidangs', 'periodes'));
    }

    public function aktif(Request $request)
    {
        $query = \App\Models\Anggota::with([
            'bidang',
            'pinjaman' => function($q) use ($request) {
                $q->where('status', \App\Enums\StatusPinjaman::Berjalan)
                  ->latest('tanggal_approval')
                  ->with('angsuran');
                  
                if ($request->filled('bulan_awal')) {
                    $parts = explode('-', $request->bulan_awal);
                    if (count($parts) == 2) {
                        $q->whereYear('tanggal_approval', $parts[0])
                          ->whereMonth('tanggal_approval', $parts[1]);
                    }
                }
                if ($request->filled('tenor')) {
                    $q->where('tenor_bulan', $request->tenor);
                }
                if ($request->filled('nominal')) {
                    $q->where('nominal_pinjaman', $request->nominal);
                }
                if ($request->filled('periode_id')) {
                    $q->where('periode_pinjaman_id', $request->periode_id);
                }
            }
        ])->whereHas('pinjaman', function($q) use ($request) {
            $q->where('status', \App\Enums\StatusPinjaman::Berjalan);
            
            if ($request->filled('bulan_awal')) {
                $parts = explode('-', $request->bulan_awal);
                if (count($parts) == 2) {
                    $q->whereYear('tanggal_approval', $parts[0])
                      ->whereMonth('tanggal_approval', $parts[1]);
                }
            }
            if ($request->filled('tenor')) {
                $q->where('tenor_bulan', $request->tenor);
            }
            if ($request->filled('nominal')) {
                $q->where('nominal_pinjaman', $request->nominal);
            }
            if ($request->filled('periode_id')) {
                $q->where('periode_pinjaman_id', $request->periode_id);
            }
        });

        if ($request->filled('bidang_id')) {
            $query->where('bidang_id', $request->bidang_id);
        }

        if ($request->filled('q')) {
            $query->where(function($q) use ($request) {
                $q->where('nama', 'like', "%{$request->q}%")
                  ->orWhere('nip', 'like', "%{$request->q}%");
            });
        }

        $anggotas = $query->paginate(15)->withQueryString();

        $bidangs = \App\Models\Bidang::orderBy('nama_bidang')->get();
        $periodes = \App\Models\PeriodePinjaman::latest('tanggal_buka')->get();
        
        $tenors = \App\Models\Pinjaman::where('status', \App\Enums\StatusPinjaman::Berjalan)
                                      ->distinct()->orderBy('tenor_bulan')->pluck('tenor_bulan');
        $nominals = \App\Models\Pinjaman::where('status', \App\Enums\StatusPinjaman::Berjalan)
                                        ->distinct()->orderBy('nominal_pinjaman')->pluck('nominal_pinjaman');
        $bulans = \App\Models\Pinjaman::where('status', \App\Enums\StatusPinjaman::Berjalan)
                                      ->selectRaw("DATE_FORMAT(tanggal_approval, '%Y-%m') as bulan")
                                      ->distinct()->orderBy('bulan', 'desc')->pluck('bulan');

        return view('pinjaman.admin.aktif', compact('anggotas', 'bidangs', 'periodes', 'tenors', 'nominals', 'bulans'));
    }

    public function exportExcel(Request $request)
    {
        $export = new \App\Exports\PinjamanAktifExport($request);
        return $export->download();
    }

    public function exportPdf(Request $request)
    {
        $export = new \App\Exports\PinjamanAktifExport($request);
        $data = $export->getData();

        $dataHash = md5('Pinjaman Aktif' . 'PDF' . json_encode($data));
        $existings = \App\Models\ArsipLaporan::where('data_hash', $dataHash)->get();
        foreach ($existings as $existing) {
            if (\Illuminate\Support\Facades\Storage::exists($existing->file_path)) {
                return \Illuminate\Support\Facades\Storage::download($existing->file_path, $existing->nama_file);
            } else {
                $existing->delete();
            }
        }

        // Setup PDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.pinjaman-aktif-pdf', $data)
            ->setPaper('legal', 'landscape');

        $filename = 'Laporan_Pinjaman_Aktif_' . now()->format('Ymd_His') . '.pdf';
        $path = 'arsip_laporan/' . $filename;
        
        \Illuminate\Support\Facades\Storage::makeDirectory('arsip_laporan');
        \Illuminate\Support\Facades\Storage::put($path, $pdf->output());
        
        \App\Models\ArsipLaporan::create([
            'tipe_laporan' => 'Pinjaman Aktif',
            'format' => 'PDF',
            'nama_file' => $filename,
            'file_path' => $path,
            'data_hash' => $dataHash,
            'filter_info' => $data['filterInfo'],
            'dibuat_oleh' => auth()->id(),
        ]);
        
        return \Illuminate\Support\Facades\Storage::download($path, $filename);
    }

    public function show(Pinjaman $pinjaman)
    {
        $pinjaman->load(['anggota.bidang', 'periodePinjaman', 'angsuran' => function($q) { $q->orderBy('angsuran_ke'); }]);
        $saldoAktif = $this->saldoService->saldoKoperasi();
        
        return view('pinjaman.admin.show', compact('pinjaman', 'saldoAktif'));
    }

    public function approve(Request $request, Pinjaman $pinjaman)
    {
        if ($pinjaman->status !== StatusPinjaman::Menunggu) {
            return back()->with('error', 'Status pengajuan bukan "Menunggu" sehingga tidak bisa di-Approve.');
        }

        try {
            DB::beginTransaction();
            $this->_processApproval($pinjaman);
            DB::commit();
            return redirect()->route('pinjaman.index')->with('success', 'Pinjaman berhasil disetujui (Berjalan) dan kas secara logis terpotong.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage() !== '' ? $e->getMessage() : 'Terjadi kesalahan sistem saat memproses persetujuan.');
        }
    }

    public function reject(RejectPinjamanRequest $request, Pinjaman $pinjaman)
    {

        if ($pinjaman->status !== StatusPinjaman::Menunggu) {
            return back()->with('error', 'Pengajuan ini sudah tidak bisa ditolak.');
        }

        $pinjaman->update([
            'status' => StatusPinjaman::Ditolak,
            'catatan' => $request->alasan_penolakan,
            'tanggal_approval' => now(),
            'approved_by' => auth()->id(),
        ]);

        $this->logger->log(
            'pinjaman_rejected',
            "Pinjaman ditolak untuk {$pinjaman->anggota->nama}. Alasan: {$request->alasan_penolakan}",
            ['no_referensi' => $pinjaman->no_referensi]
        );

        return redirect()->route('pinjaman.index')->with('success', 'Pengajuan pinjaman berhasil ditolak.');
    }

    private function _processApproval(Pinjaman $pinjaman)
    {
        $saldoSekarang = $this->saldoService->saldoKoperasi();

        if ($saldoSekarang < $pinjaman->nominal_pinjaman) {
            throw new \Exception('Saldo/Kas Koperasi tidak mencukupi.');
        }

        $pinjaman->update([
            'status' => StatusPinjaman::Berjalan,
            'tanggal_approval' => now(), 
            'approved_by' => auth()->id(),
        ]);

        if ($pinjaman->potongan_swp > 0) {
            $jenisSwp = \App\Models\JenisSimpanan::swp();
            if ($jenisSwp) {
                $simpananSwp = \App\Models\Simpanan::create([
                    'anggota_id' => $pinjaman->anggota_id,
                    'jenis_simpanan_id' => $jenisSwp->id,
                    'nominal' => $pinjaman->potongan_swp,
                    'tanggal' => now()->format('Y-m-d'),
                    'keterangan' => 'Potongan SWP Otomatis dari Pencairan Pinjaman Ref: ' . $pinjaman->no_referensi,
                    'dicatat_oleh' => auth()->id(),
                    'pinjaman_id' => $pinjaman->id,
                ]);

                // Tulis simpanan SWP ke Ledger
                $this->ledgerService->catatSimpanan(
                    $pinjaman->potongan_swp,
                    $simpananSwp->id,
                    $pinjaman->anggota_id,
                    $jenisSwp->nama,
                    now()->toDateString(),
                );
            }
        }

        // Tulis potongan dana resiko + biaya admin ke Ledger
        $this->ledgerService->catatPotonganPinjaman(
            (float) $pinjaman->potongan_dana_resiko,
            (float) $pinjaman->potongan_biaya_admin,
            $pinjaman->id,
            $pinjaman->anggota_id,
            $pinjaman->no_referensi,
            now()->toDateString(),
        );

        // Tulis pencairan pinjaman (dana keluar) ke Ledger
        $this->ledgerService->catatPencairan(
            (float) $pinjaman->nominal_pinjaman,
            $pinjaman->id,
            $pinjaman->anggota_id,
            $pinjaman->no_referensi,
            now()->toDateString(),
        );

        $tanggalMulai = now();
        $angsuranBulanBerjalan = (bool) ($pinjaman->periodePinjaman->angsuran_bulan_berjalan ?? false);

        for ($i = 1; $i <= $pinjaman->tenor_bulan; $i++) {
            $offsetBulan = $angsuranBulanBerjalan ? ($i - 1) : $i;

            \App\Models\Angsuran::create([
                'pinjaman_id' => $pinjaman->id,
                'angsuran_ke' => $i,
                'tanggal_jatuh_tempo' => $tanggalMulai->copy()->addMonths($offsetBulan)->format('Y-m-d'),
                'nominal_pokok' => $pinjaman->angsuran_pokok,
                'nominal_bunga' => $pinjaman->angsuran_bunga,
                'nominal_total' => $pinjaman->total_angsuran,
                'status' => \App\Enums\StatusAngsuran::Belum,
            ]);
        }

        $this->logger->log(
            'pinjaman_approved',
            "Pinjaman disetujui untuk {$pinjaman->anggota->nama} senilai Rp " . number_format($pinjaman->nominal_pinjaman, 0, ',', '.'),
            ['no_referensi' => $pinjaman->no_referensi]
        );
    }

    public function massApprove(Request $request)
    {
        $request->validate(['pinjaman_ids' => 'required|array']);
        
        $sukses = 0;
        $gagal = 0;

        foreach ($request->pinjaman_ids as $id) {
            $pinjaman = Pinjaman::find($id);
            if (!$pinjaman || $pinjaman->status !== StatusPinjaman::Menunggu) continue;

            try {
                DB::beginTransaction();
                $this->_processApproval($pinjaman);
                DB::commit();
                $sukses++;
            } catch (\Exception $e) {
                DB::rollBack();
                $gagal++;
            }
        }

        $pesan = "Berhasil menyetujui $sukses pengajuan.";
        if ($gagal > 0) $pesan .= " Gagal menyetujui $gagal pengajuan (kemungkinan saldo tidak cukup).";

        return back()->with($gagal > 0 ? 'warning' : 'success', $pesan);
    }

    public function massReject(Request $request)
    {
        $request->validate([
            'pinjaman_ids' => 'required|array',
            'alasan_penolakan' => 'required|string|max:500'
        ]);

        $sukses = 0;
        foreach ($request->pinjaman_ids as $id) {
            $pinjaman = Pinjaman::find($id);
            if (!$pinjaman || $pinjaman->status !== StatusPinjaman::Menunggu) continue;

            $pinjaman->update([
                'status' => StatusPinjaman::Ditolak,
                'catatan' => $request->alasan_penolakan,
                'tanggal_approval' => now(),
                'approved_by' => auth()->id(),
            ]);

            $this->logger->log(
                'pinjaman_rejected',
                "Pinjaman ditolak untuk {$pinjaman->anggota->nama}. Alasan: {$request->alasan_penolakan}",
                ['no_referensi' => $pinjaman->no_referensi]
            );
            $sukses++;
        }

        return back()->with('success', "Berhasil menolak $sukses pengajuan.");
    }

    public function bayarAngsuran(Request $request, Pinjaman $pinjaman, \App\Models\Angsuran $angsuran)
    {
        if ($angsuran->pinjaman_id !== $pinjaman->id) {
            abort(404);
        }

        if ($angsuran->status !== \App\Enums\StatusAngsuran::Belum) {
            return back()->with('error', 'Angsuran ini sudah lunas atau dibatalkan.');
        }

        try {
            DB::beginTransaction();

            $angsuran->update([
                'status' => \App\Enums\StatusAngsuran::Lunas,
                'tanggal_bayar' => now(),
            ]);

            // Tulis angsuran ke Ledger
            $this->ledgerService->catatAngsuran(
                (float) $angsuran->nominal_total,
                $angsuran->id,
                $pinjaman->anggota_id,
                $angsuran->angsuran_ke,
                $pinjaman->no_referensi,
                now()->toDateString(),
            );

            // Check if all angsurans are paid
            $belumLunasCount = $pinjaman->angsuran()->where('status', \App\Enums\StatusAngsuran::Belum)->count();
            
            if ($belumLunasCount === 0) {
                $pinjaman->update([
                    'status' => \App\Enums\StatusPinjaman::Lunas,
                ]);
            }

            $this->logger->log(
                'angsuran_paid',
                "Pembayaran Angsuran Ke-{$angsuran->angsuran_ke} sebesar Rp " . number_format($angsuran->nominal_total, 0, ',', '.') . " diterima untuk Pinjaman Ref: {$pinjaman->no_referensi}.",
                ['angsuran_id' => $angsuran->id, 'pinjaman_id' => $pinjaman->id]
            );

            DB::commit();

            return back()->with('success', 'Pembayaran angsuran berhasil dicatat.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mencatat pembayaran angsuran.');
        }
    }
}
