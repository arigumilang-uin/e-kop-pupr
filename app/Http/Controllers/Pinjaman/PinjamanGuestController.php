<?php

namespace App\Http\Controllers\Pinjaman;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pinjaman\GuestPinjamanRequest;
use App\Models\Anggota;
use App\Models\PeriodePinjaman;
use App\Models\Pinjaman;
use App\Services\PinjamanService;
use App\Services\PengaturanService;
use Illuminate\Http\Request;

class PinjamanGuestController extends Controller
{
    public function __construct(
        private PinjamanService $pinjamanService,
        private PengaturanService $pengaturanService,
    ) {}

    /**
     * Tampilkan form pengajuan pinjaman guest (berdasarkan token).
     */
    public function form(string $token)
    {
        $periode = PeriodePinjaman::where('token', $token)->firstOrFail();

        // Hanya periode yang masih "buka" yang boleh diakses
        if (!$periode->isBuka()) {
            return view('pinjaman.guest.closed', compact('periode'));
        }

        $pengaturan = [
            'bunga_persen' => $this->pengaturanService->bungaPersen(),
            'batas_bulan' => $this->pengaturanService->batasBulanPelunasan(),
            'tenor_maks' => max(1, $this->pengaturanService->batasBulanPelunasan() - (int) now()->format('n')),
            'tenor_min' => $this->pengaturanService->tenorMinimal(),
            'limit' => $periode->limit_per_anggota,
            'swp_persen' => $this->pengaturanService->potonganSwpPersen(),
            'resiko_persen' => $this->pengaturanService->potonganDanaResikoPersen(),
            'admin_persen' => $this->pengaturanService->potonganBiayaAdminPersen(),
        ];

        return view('pinjaman.guest.form', compact('periode', 'pengaturan'));
    }

    /**
     * Proses submit form pinjaman oleh guest.
     */
    public function submit(GuestPinjamanRequest $request, string $token)
    {
        $periode = PeriodePinjaman::where('token', $token)->firstOrFail();

        if (!$periode->isBuka()) {
            return back()->with('error', 'Periode pinjaman ini sudah ditutup.');
        }

        // 1. Cari & Validasi Anggota
        $anggota = Anggota::where('nip', $request->nip)
            ->where('nama', $request->nama)
            ->aktif()
            ->first();

        if (!$anggota) {
            return back()->withInput()->with('error', 'Data NIP atau Nama tidak cocok dengan data keanggotaan aktif yang terdaftar.');
        }

        // 2. Cek Kelayakan (Pinjaman aktif tahun ini, limit, tenor)
        $kelayakan = $this->pinjamanService->cekKelayakan(
            $anggota->id,
            $request->nominal_pinjaman,
            $request->tenor_bulan,
            $periode->limit_per_anggota,
            $this->pengaturanService->batasBulanPelunasan()
        );

        if (!$kelayakan['layak']) {
            return back()->withInput()->withErrors(['nominal_pinjaman' => implode(' ', $kelayakan['pesan'])]);
        }

        // Cek Confirmation Override jika anggota sudah punya pinjaman
        if ($kelayakan['perlu_override'] && !$request->boolean('confirm_override')) {
            return back()->withInput()->with('needs_override_confirmation', 'Anda sudah memiliki pinjaman aktif pada tahun ini. Apakah Anda yakin ingin mengajukan pinjaman baru? Pengajuan ini akan membutuhkan persetujuan/override khusus dari pengurus.');
        }

        // 3. Hitung Keseluruhan Rincian Pinjaman
        $rincian = $this->pinjamanService->hitungPinjaman($request->nominal_pinjaman, $request->tenor_bulan);

        // 4. Simpan Pengajuan
        $pinjaman = Pinjaman::create([
            'anggota_id' => $anggota->id,
            'periode_pinjaman_id' => $periode->id,
            'bulan_pengajuan' => (int) now()->format('n'),
            'nama_bank' => $request->nama_bank,
            'nama_rekening' => $request->nama_rekening,
            'no_rekening' => $request->no_rekening,
            ...$rincian, // unpack rincian calculations
            'tanggal_pengajuan' => now(),
            'status' => \App\Enums\StatusPinjaman::Menunggu,
            'is_override' => $kelayakan['perlu_override'],
        ]);

        return redirect()->route('pinjaman.guest.status')->with('success_ref', $pinjaman->no_referensi);
    }

    /**
     * Halaman cek status pengajuan (hanya butuh NIP).
     */
    public function statusForm()
    {
        return view('pinjaman.guest.status-form');
    }

    /**
     * Proses cek status pengajuan.
     */
    public function statusCheck(Request $request)
    {
        $request->validate([
            'nip' => 'required|string',
            'no_referensi' => 'nullable|string',
        ]);

        $anggota = Anggota::where('nip', $request->nip)->first();

        if (!$anggota) {
            return back()->withInput()->with('error', 'NIP tidak ditemukan.');
        }

        $query = Pinjaman::where('anggota_id', $anggota->id);

        if ($request->filled('no_referensi')) {
            $query->where('no_referensi', $request->no_referensi);
        } else {
            // Ambil pengajuan terakhir di tahun ini
            $tahunSekarang = now()->format('Y');
            $query->whereYear('tanggal_pengajuan', $tahunSekarang);
        }

        $pinjaman = $query->latest('tanggal_pengajuan')->first();

        if (!$pinjaman) {
            return back()->withInput()->with('error', 'Tidak ada data pengajuan pinjaman untuk tahun ini.');
        }

        return view('pinjaman.guest.status-result', compact('anggota', 'pinjaman'));
    }

    /**
     * Membatalkan pengajuan pinjaman (Guest).
     */
    public function cancel(Request $request)
    {
        $request->validate([
            'no_referensi' => 'required|string',
        ]);

        $pinjaman = Pinjaman::where('no_referensi', $request->no_referensi)->firstOrFail();

        if ($pinjaman->status !== \App\Enums\StatusPinjaman::Menunggu) {
            return back()->with('error', 'Hanya pengajuan dengan status Menunggu yang dapat dibatalkan.');
        }

        $pinjaman->update([
            'status' => \App\Enums\StatusPinjaman::Ditolak,
            'catatan' => 'Dibatalkan oleh peminjam.',
        ]);

        return back()->with('success', 'Pengajuan pinjaman berhasil dibatalkan.');
    }
}
