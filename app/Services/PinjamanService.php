<?php

namespace App\Services;

use App\Enums\StatusPinjaman;

/**
 * Service untuk semua kalkulasi pinjaman:
 * bunga 15%, potongan 5%, angsuran, tenor, dan validasi.
 */
class PinjamanService
{
    public function __construct(
        private PengaturanService $pengaturan,
    ) {}

    /**
     * Hitung seluruh detail pinjaman dari nominal dan tenor.
     * Digunakan di simulasi dan pengajuan.
     */
    public function hitungPinjaman(float $nominal, int $tenor): array
    {
        $bungaPersen = $this->pengaturan->bungaPersen();
        $swpPersen = $this->pengaturan->potonganSwpPersen();
        $resikoPersen = $this->pengaturan->potonganDanaResikoPersen();
        $adminPersen = $this->pengaturan->potonganBiayaAdminPersen();

        // Bunga
        $totalBunga = $nominal * ($bungaPersen / 100);
        $angsuranPokok = $nominal / $tenor;
        $angsuranBunga = $totalBunga / $tenor;
        $totalAngsuran = $angsuranPokok + $angsuranBunga;
        $totalBayar = $nominal + $totalBunga;

        // Potongan 5% di muka
        $potonganSwp = $nominal * ($swpPersen / 100);
        $potonganResiko = $nominal * ($resikoPersen / 100);
        $potonganAdmin = $nominal * ($adminPersen / 100);
        $totalPotongan = $potonganSwp + $potonganResiko + $potonganAdmin;
        $danaDiterima = $nominal - $totalPotongan;

        return [
            'nominal_pinjaman' => $nominal,
            'bunga_persen' => $bungaPersen,
            'total_bunga' => round($totalBunga, 2),
            'tenor_bulan' => $tenor,
            'angsuran_pokok' => round($angsuranPokok, 2),
            'angsuran_bunga' => round($angsuranBunga, 2),
            'total_angsuran' => round($totalAngsuran, 2),
            'total_bayar' => round($totalBayar, 2),
            'potongan_swp' => round($potonganSwp, 2),
            'potongan_dana_resiko' => round($potonganResiko, 2),
            'potongan_biaya_admin' => round($potonganAdmin, 2),
            'total_potongan' => round($totalPotongan, 2),
            'dana_diterima' => round($danaDiterima, 2),
            // Detail persen (untuk tampilan)
            'swp_persen' => $swpPersen,
            'resiko_persen' => $resikoPersen,
            'admin_persen' => $adminPersen,
        ];
    }

    /**
     * Hitung tenor maksimal dari bulan saat ini.
     * Rumus: batas_bulan_pelunasan - bulan_saat_ini
     */
    public function tenorMaksimal(?int $batasBulan = null): int
    {
        $batas = $batasBulan ?? $this->pengaturan->batasBulanPelunasan();
        $bulanSekarang = (int) now()->format('n');

        return max(0, $batas - $bulanSekarang);
    }

    /**
     * Cek kelayakan pengajuan pinjaman anggota.
     * Mengembalikan array [layak, pesan[], perlu_override].
     */
    public function cekKelayakan(
        int $anggotaId,
        float $nominal,
        int $tenor,
        float $limitPerAnggota,
        int $batasBulanPelunasan,
    ): array {
        $pesan = [];
        $perluOverride = false;

        // 1. Cek bulan
        $bulanSekarang = (int) now()->format('n');
        if ($bulanSekarang >= $batasBulanPelunasan) {
            $pesan[] = 'Bulan pengajuan sudah melewati batas pelunasan.';
            return ['layak' => false, 'pesan' => $pesan, 'perlu_override' => false];
        }

        // 2. Cek pinjaman aktif di tahun ini
        $tahun = (int) now()->format('Y');
        $pinjamanAktif = \App\Models\Pinjaman::where('anggota_id', $anggotaId)
            ->whereIn('status', StatusPinjaman::aktif())
            ->whereYear('tanggal_pengajuan', $tahun)
            ->exists();

        if ($pinjamanAktif) {
            $pesan[] = 'Anggota sudah memiliki pinjaman aktif di tahun ini. Memerlukan override pengurus.';
            $perluOverride = true;
        }

        // 3. Cek nominal vs limit
        if ($nominal > $limitPerAnggota) {
            $pesan[] = 'Nominal melebihi batas per anggota.';
            return ['layak' => false, 'pesan' => $pesan, 'perlu_override' => $perluOverride];
        }

        // 4. Cek tenor
        $tenorMaks = $batasBulanPelunasan - $bulanSekarang;
        $tenorMin = $this->pengaturan->tenorMinimal();
        if ($tenor < $tenorMin || $tenor > $tenorMaks) {
            $pesan[] = "Tenor harus antara {$tenorMin} sampai {$tenorMaks} bulan.";
            return ['layak' => false, 'pesan' => $pesan, 'perlu_override' => $perluOverride];
        }

        return [
            'layak' => true,
            'pesan' => $pesan,
            'perlu_override' => $perluOverride,
        ];
    }
}
