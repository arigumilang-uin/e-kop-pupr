<?php

namespace App\Services;

use App\Models\Pengaturan;
use Illuminate\Support\Facades\Cache;

/**
 * Service untuk mengakses pengaturan sistem dengan caching.
 * Sumber data: tabel `pengaturan`.
 *
 * Optimasi: Semua pengaturan di-load dalam 1 query dan di-cache
 * sebagai collection, menghindari N+1 query per key.
 */
class PengaturanService
{
    private const CACHE_KEY = 'pengaturan:all';
    private const CACHE_TTL = 3600; // 1 jam

    /**
     * In-memory store agar dalam satu request tidak perlu
     * hit cache driver berulang kali.
     */
    private ?array $store = null;

    /**
     * Load semua pengaturan sekaligus (1 query, 1 cache entry).
     */
    private function loadAll(): array
    {
        if ($this->store !== null) {
            return $this->store;
        }

        $this->store = Cache::remember(
            self::CACHE_KEY,
            self::CACHE_TTL,
            fn () => Pengaturan::pluck('value', 'key')->toArray()
        );

        return $this->store;
    }

    /**
     * Ambil nilai pengaturan by key (dengan cache batch).
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $all = $this->loadAll();

        return $all[$key] ?? $default;
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
     * Karena sekarang batch, kita flush seluruh cache pengaturan.
     */
    public function clearCache(string $key): void
    {
        Cache::forget(self::CACHE_KEY);
        $this->store = null;
    }

    /**
     * Hapus semua cache pengaturan.
     */
    public function clearAllCache(): void
    {
        Cache::forget(self::CACHE_KEY);
        $this->store = null;
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

    public function simpananWajib(?int $bulan = null, ?int $tahun = null): float
    {
        if ($bulan !== null && $tahun !== null) {
            $khusus = \App\Models\PengaturanKhusus::where('key', 'simpanan_wajib')
                ->where('bulan', $bulan)
                ->where('tahun', $tahun)
                ->value('value');

            if ($khusus !== null) {
                return (float) $khusus;
            }
        }

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
