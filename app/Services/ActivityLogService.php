<?php

namespace App\Services;

use App\Models\LogAktivitas;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

/**
 * Service untuk mencatat log aktivitas ke tabel log_aktivitas.
 * Digunakan di controller dan observer.
 */
class ActivityLogService
{
    /**
     * Catat aktivitas.
     */
    public function log(
        string $aktivitas,
        string $deskripsi,
        ?array $dataLama = null,
        ?array $dataBaru = null,
        ?int $userId = null,
    ): LogAktivitas {
        return LogAktivitas::create([
            'user_id' => $userId ?? Auth::id(),
            'aktivitas' => $aktivitas,
            'deskripsi' => $deskripsi,
            'ip_address' => Request::ip(),
            'data_lama' => $dataLama,
            'data_baru' => $dataBaru,
        ]);
    }

    /**
     * Log login berhasil.
     */
    public function logLogin(int $userId, string $nama, string $role): void
    {
        $this->log('login', "{$nama} ({$role}) berhasil login.", userId: $userId);
    }

    /**
     * Log logout.
     */
    public function logLogout(int $userId, string $nama): void
    {
        $this->log('logout', "{$nama} logout.", userId: $userId);
    }

    /**
     * Log akun terkunci.
     */
    public function logAkunTerkunci(int $userId, string $username, int $percobaan): void
    {
        $this->log(
            'akun_terkunci',
            "Akun {$username} terkunci karena {$percobaan}x salah password.",
            userId: $userId,
        );
    }

    // =========================================
    // Financial Event Logging
    // =========================================

    /**
     * Log simpanan dicatat.
     */
    public function logSimpanan(string $namaAnggota, string $jenis, float $nominal, string $noRef): void
    {
        $this->log(
            'simpanan_created',
            "Simpanan {$jenis} sebesar Rp " . number_format($nominal, 0, ',', '.') . " dicatat untuk {$namaAnggota}.",
            dataBaru: ['no_referensi' => $noRef, 'nominal' => $nominal],
        );
    }

    /**
     * Log pengeluaran kas dicatat.
     */
    public function logPengeluaran(string $kategori, float $nominal, string $noRef): void
    {
        $this->log(
            'pengeluaran_created',
            "Pengeluaran kas ({$kategori}) sebesar Rp " . number_format($nominal, 0, ',', '.') . " dicatat.",
            dataBaru: ['no_referensi' => $noRef, 'nominal' => $nominal],
        );
    }

    /**
     * Log pengeluaran kas dihapus (soft delete).
     */
    public function logPengeluaranDeleted(string $noRef, float $nominal): void
    {
        $this->log(
            'pengeluaran_deleted',
            "Pengeluaran kas Ref: {$noRef} sebesar Rp " . number_format($nominal, 0, ',', '.') . " dihapus.",
            dataLama: ['no_referensi' => $noRef, 'nominal' => $nominal],
        );
    }

    /**
     * Log anggota keluar dari koperasi.
     */
    public function logAnggotaKeluar(string $nama, float $totalDikembalikan): void
    {
        $this->log(
            'anggota_keluar',
            "Anggota {$nama} keluar dari koperasi. Simpanan dikembalikan: Rp " . number_format($totalDikembalikan, 0, ',', '.'),
            dataBaru: ['total_dikembalikan' => $totalDikembalikan],
        );
    }

    /**
     * Log anggota reaktivasi (masuk kembali).
     */
    public function logAnggotaReaktivasi(string $nama, float $totalSetorUlang): void
    {
        $this->log(
            'anggota_reaktivasi',
            "Anggota {$nama} reaktivasi (masuk kembali). Setor ulang: Rp " . number_format($totalSetorUlang, 0, ',', '.'),
            dataBaru: ['total_setor_ulang' => $totalSetorUlang],
        );
    }
}
