<?php

namespace App\Http\Controllers\Pinjaman;

use App\Enums\StatusPinjaman;
use App\Http\Controllers\Controller;
use App\Models\Pinjaman;
use App\Services\ActivityLogService;
use App\Services\SaldoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PinjamanAdminController extends Controller
{
    public function __construct(
        private ActivityLogService $logger,
        private SaldoService $saldoService
    ) {}

    public function index(Request $request)
    {
        $query = Pinjaman::with('anggota')->latest('tanggal_pengajuan');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('q')) {
            $query->whereHas('anggota', function ($q) use ($request) {
                $q->where('nama', 'like', "%{$request->q}%")
                  ->orWhere('nip', 'like', "%{$request->q}%");
            })->orWhere('no_referensi', 'like', "%{$request->q}%");
        }

        $pinjamans = $query->paginate(15)->withQueryString();
        
        $totalSaldoKoperasi = $this->saldoService->saldoKoperasi();

        return view('pinjaman.admin.index', compact('pinjamans', 'totalSaldoKoperasi'));
    }

    public function show(Pinjaman $pinjaman)
    {
        $pinjaman->load(['anggota.bidang', 'periodePinjaman']);
        $saldoAktif = $this->saldoService->saldoKoperasi();
        
        return view('pinjaman.admin.show', compact('pinjaman', 'saldoAktif'));
    }

    public function approve(Request $request, Pinjaman $pinjaman)
    {
        if ($pinjaman->status !== StatusPinjaman::Menunggu) {
            return back()->with('error', 'Status pengajuan bukan "Menunggu" sehingga tidak bisa di-Approve.');
        }

        $saldoSekarang = $this->saldoService->saldoKoperasi();

        if ($saldoSekarang < $pinjaman->nominal_pinjaman) {
            return back()->with('error', 'Persetujuan ditolak oleh sistem! Saldo/Kas Koperasi saat ini (Rp ' . number_format($saldoSekarang, 0, ',', '.') . ') tidak mencukupi untuk mendanai pinjaman ini (Rp ' . number_format($pinjaman->nominal_pinjaman, 0, ',', '.') . '). Tambah kas via Simpanan terlebih dahulu.');
        }

        try {
            DB::beginTransaction();

            $pinjaman->update([
                'status' => StatusPinjaman::Berjalan,
                'tanggal_approval' => now(), 
                'approved_by' => auth()->id(),
            ]);

            // Di dunia nyata, di sini akan mendaftarkan jadwal Angsuran & memotong SWP
            // Untuk flow dasar saat ini kita hanya acc status pinjaman.

            $this->logger->log(
                'pinjaman_approved',
                "Pinjaman disetujui untuk {$pinjaman->anggota->nama} senilai Rp " . number_format($pinjaman->nominal_pinjaman, 0, ',', '.'),
                ['no_referensi' => $pinjaman->no_referensi]
            );

            DB::commit();

            return redirect()->route('pinjaman.index')->with('success', 'Pinjaman berhasil disetujui (Berjalan) dan kas secara logis terpotong.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan sistem saat memproses persetujuan.');
        }
    }

    public function reject(Request $request, Pinjaman $pinjaman)
    {
        $request->validate([
            'alasan_penolakan' => 'required|string|max:255'
        ]);

        if ($pinjaman->status !== StatusPinjaman::Menunggu && $pinjaman->status !== StatusPinjaman::Ditinjau) {
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
}
