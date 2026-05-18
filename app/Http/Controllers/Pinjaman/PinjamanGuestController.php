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
     * Redirect ke periode pinjaman yang relevan berdasarkan tanggal hari ini.
     *
     * Prioritas:
     * 1. Periode aktif hari ini → langsung ke form pengajuan
     * 2. Ada periode terjadwal di masa depan → tampilkan jadwal
     * 3. Tidak ada keduanya → tampilkan halaman closed dari periode terakhir
     */
    public function formRedirect()
    {
        // 1. Ada periode AKTIF hari ini?
        $periodeAktif = PeriodePinjaman::aktifHariIni()->first();

        if ($periodeAktif) {
            return redirect()->route('pinjaman.guest.form', $periodeAktif->token);
        }

        // 2. Ada periode TERJADWAL di masa depan?
        $periodeMendatang = PeriodePinjaman::mendatang()->get();

        if ($periodeMendatang->isNotEmpty()) {
            return view('pinjaman.guest.upcoming', compact('periodeMendatang'));
        }

        // 3. Fallback: periode TUTUP terakhir
        $periodeTerakhir = PeriodePinjaman::sudahTutup()->first();

        if ($periodeTerakhir) {
            return redirect()->route('pinjaman.guest.form', $periodeTerakhir->token);
        }

        // 4. Belum ada periode sama sekali
        return redirect()->route('pinjaman.guest.status')
            ->with('error', 'Belum ada periode pengajuan pinjaman yang tersedia. Silakan hubungi pengurus koperasi.');
    }

    /**
     * Tampilkan form pengajuan pinjaman guest (berdasarkan token).
     */
    public function form(string $token)
    {
        $periode = PeriodePinjaman::where('token', $token)->firstOrFail();

        // Gunakan statusEfektif() sebagai single source of truth
        $statusNow = $periode->statusEfektif();

        if ($statusNow !== \App\Enums\StatusPeriode::Buka) {
            $today = now()->startOfDay();

            if ($statusNow === \App\Enums\StatusPeriode::Terjadwal) {
                $pesan_tutup = 'Formulir pengajuan pinjaman belum dibuka. Periode ini baru akan aktif mulai tanggal <strong>' . $periode->tanggal_buka->translatedFormat('d F Y') . '</strong>.';
            } elseif ($periode->status === \App\Enums\StatusPeriode::Tutup && $today->betweenIncluded($periode->tanggal_buka, $periode->tanggal_tutup)) {
                $pesan_tutup = 'Periode pinjaman ini telah ditutup secara manual oleh Pengurus.';
            } else {
                $pesan_tutup = 'Batas waktu pengajuan pinjaman pada periode ini telah berakhir sejak tanggal <strong>' . $periode->tanggal_tutup->translatedFormat('d F Y') . '</strong>.';
            }

            return view('pinjaman.guest.closed', compact('periode', 'pesan_tutup'));
        }

        $tenorMaks = $periode->tenorTersedia();

        $pengaturan = [
            'bunga_persen' => $this->pengaturanService->bungaPersen(),
            'bulan_potongan_awal' => $periode->bulan_potongan_awal,
            'bulan_potongan_akhir' => $periode->bulan_potongan_akhir,
            'tenor_maks' => max(1, $tenorMaks),
            'tenor_min' => $this->pengaturanService->tenorMinimal(),
            'limit' => $periode->limit_per_anggota,
            'nominal_min' => $periode->nominal_min ?? 100000,
            'kelipatan' => $periode->kelipatan_nominal ?? 100000,
            'swp_persen' => $this->pengaturanService->potonganSwpPersen(),
            'resiko_persen' => $this->pengaturanService->potonganDanaResikoPersen(),
            'admin_persen' => $this->pengaturanService->potonganBiayaAdminPersen(),
        ];

        return view('pinjaman.guest.form', compact('periode', 'pengaturan'));
    }

    /**
     * Tampilkan halaman validasi pengajuan (review).
     */
    public function review(GuestPinjamanRequest $request, string $token)
    {
        $periode = PeriodePinjaman::where('token', $token)->firstOrFail();

        if (!$periode->isBuka()) {
            return back()->with('error', 'Periode pinjaman ini tidak sedang aktif. Pengajuan tidak dapat diproses.');
        }

        // 1. Cari & Validasi Anggota (hanya berdasarkan NIP)
        $anggota = Anggota::where('nip', $request->nip)->aktif()->first();

        if (!$anggota) {
            return back()->withInput()->with('error', 'NIP tidak terdaftar sebagai anggota koperasi aktif. Pastikan NIP yang Anda masukkan benar.');
        }

        // 2. Cek Kelayakan (Pinjaman aktif tahun ini, limit, tenor)
        $kelayakan = $this->pinjamanService->cekKelayakan(
            $anggota->id,
            $request->nominal_pinjaman,
            $request->tenor_bulan,
            $periode->limit_per_anggota,
            $periode->bulan_potongan_awal,
            $periode->bulan_potongan_akhir,
        );

        if (!$kelayakan['layak']) {
            return back()->withInput()->withErrors(['nominal_pinjaman' => implode(' ', $kelayakan['pesan'])]);
        }

        // 3. Hitung Keseluruhan Rincian Pinjaman
        $rincian = $this->pinjamanService->hitungPinjaman($request->nominal_pinjaman, $request->tenor_bulan);

        $pengaturan = [
            'swp_persen' => $this->pengaturanService->potonganSwpPersen(),
            'resiko_persen' => $this->pengaturanService->potonganDanaResikoPersen(),
            'admin_persen' => $this->pengaturanService->potonganBiayaAdminPersen(),
            'bunga_persen' => $this->pengaturanService->bungaPersen(),
        ];

        return view('pinjaman.guest.review', compact('periode', 'anggota', 'rincian', 'request', 'kelayakan', 'pengaturan'));
    }

    /**
     * Proses final (store) form pinjaman oleh guest.
     */
    public function store(GuestPinjamanRequest $request, string $token)
    {
        $periode = PeriodePinjaman::where('token', $token)->firstOrFail();

        if (!$periode->isBuka()) {
            return back()->with('error', 'Periode pinjaman ini tidak sedang aktif. Pengajuan tidak dapat diproses.');
        }

        // 1. Cari & Validasi Anggota
        $anggota = Anggota::where('nip', $request->nip)->aktif()->first();

        if (!$anggota) {
            return back()->withInput()->with('error', 'NIP tidak terdaftar sebagai anggota koperasi aktif.');
        }

        // 2. Cek Kelayakan
        $kelayakan = $this->pinjamanService->cekKelayakan(
            $anggota->id,
            $request->nominal_pinjaman,
            $request->tenor_bulan,
            $periode->limit_per_anggota,
            $periode->bulan_potongan_awal,
            $periode->bulan_potongan_akhir,
        );

        if (!$kelayakan['layak']) {
            return back()->withInput()->withErrors(['nominal_pinjaman' => implode(' ', $kelayakan['pesan'])]);
        }

        // Pastikan agreement dicheck, ini adalah validasi ekstra di controller sbg pengaman
        if (!$request->has('agreed')) {
            return back()->withInput()->with('error', 'Anda harus menyetujui rincian dan mematuhi aturan standar KSP PUPR PKPP Riau sebelum melanjutkan.');
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
            // Ambil SEMUA pengajuan di tahun ini
            $tahunSekarang = now()->format('Y');
            $query->whereYear('tanggal_pengajuan', $tahunSekarang);
        }

        $pinjamans = $query->latest('tanggal_pengajuan')->get();

        if ($pinjamans->isEmpty()) {
            return back()->withInput()->with('error', 'Tidak ada data pengajuan pinjaman untuk kriteria tersebut.');
        }

        return view('pinjaman.guest.status-result', compact('anggota', 'pinjamans'));
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
            'status' => \App\Enums\StatusPinjaman::Dibatalkan,
            'catatan' => 'Dibatalkan oleh peminjam.',
        ]);

        return back()->with('success', 'Pengajuan pinjaman berhasil dibatalkan.');
    }
}
