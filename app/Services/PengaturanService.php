<?php

namespace App\Services;

use App\Models\Pengaturan;
use Illuminate\Support\Facades\Cache;

/**
 * Service untuk mengakses pengaturan sistem dengan caching.
 * Sumber data: tabel `pengaturan`.
 */
class PengaturanService
{
    private const CACHE_PREFIX = 'pengaturan:';
    private const CACHE_TTL = 3600; // 1 jam

    /**
     * Ambil nilai pengaturan by key (dengan cache).
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember(
            self::CACHE_PREFIX . $key,
            self::CACHE_TTL,
            fn () => Pengaturan::where('key', $key)->value('value') ?? $default
        );
    }

    /**
     * Ambil sebagai float.
     */
    public function getFloat(string $key, float $default = 0): float
    {
        return (float) $this->get($key, $default);
    }

    /**
     * Ambil sebagai integer.
     */
    public function getInt(string $key, int $default = 0): int
    {
        return (int) $this->get($key, $default);
    }

    /**
     * Hapus cache untuk key tertentu (dipanggil setelah update pengaturan).
     */
    public function clearCache(string $key): void
    {
        Cache::forget(self::CACHE_PREFIX . $key);
    }

    /**
     * Hapus semua cache pengaturan.
     */
    public function clearAllCache(): void
    {
        $keys = Pengaturan::pluck('key');
        foreach ($keys as $key) {
            Cache::forget(self::CACHE_PREFIX . $key);
        }
    }

    // === Shortcut Methods (yang sering dipakai) ===

    public function bungaPersen(): float
    {
        return $this->getFloat('bunga_pinjaman_persen', 15);
    }

    public function potonganSwpPersen(): float
    {
        return $this->getFloat('potongan_swp_persen', 3);
    }

    public function potonganDanaResikoPersen(): float
    {
        return $this->getFloat('potongan_dana_resiko_persen', 1.5);
    }

    public function potonganBiayaAdminPersen(): float
    {
        return $this->getFloat('potongan_biaya_admin_persen', 0.5);
    }

    public function simpananPokok(): float
    {
        return $this->getFloat('simpanan_pokok', 50000);
    }

    public function simpananWajib(): float
    {
        return $this->getFloat('simpanan_wajib', 50000);
    }

    public function batasBulanPelunasan(): int
    {
        return $this->getInt('batas_bulan_pelunasan_default', 11);
    }

    public function tenorMinimal(): int
    {
        return $this->getInt('tenor_minimal', 1);
    }

    public function maksPercobaanLogin(): int
    {
        return $this->getInt('maks_percobaan_login', 5);
    }

    public function durasiKunciAkunMenit(): int
    {
        return $this->getInt('durasi_kunci_akun_menit', 30);
    }
}
