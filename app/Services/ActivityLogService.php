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
}
