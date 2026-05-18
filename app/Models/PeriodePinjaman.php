<?php

namespace App\Models;

use App\Enums\StatusPeriode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class PeriodePinjaman extends Model
{
    use HasFactory;

    protected $table = 'periode_pinjaman';

    protected $fillable = [
        'nama_periode',
        'tahun',
        'tanggal_buka',
        'tanggal_tutup',
        'bulan_potongan_awal',
        'bulan_potongan_akhir',
        'limit_per_anggota',
        'nominal_min',
        'kelipatan_nominal',
        'token',
        'catatan',
        'status',
        'dibuka_oleh',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_buka' => 'date',
            'tanggal_tutup' => 'date',
            'limit_per_anggota' => 'decimal:2',
            'nominal_min' => 'decimal:2',
            'kelipatan_nominal' => 'decimal:2',
            'status' => StatusPeriode::class,
        ];
    }

    /**
     * Hitung tenor maksimal yang tersedia berdasarkan bulan potongan awal dan akhir.
     */
    public function tenorTersedia(): int
    {
        return $this->bulan_potongan_akhir - $this->bulan_potongan_awal + 1;
    }

    // === Relationships ===

    public function pembuka(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibuka_oleh');
    }

    public function pinjaman(): HasMany
    {
        return $this->hasMany(Pinjaman::class);
    }

    // === Status Otomatis (Real-Time) ===

    /**
     * Hitung status efektif berdasarkan tanggal hari ini.
     * Ini adalah sumber kebenaran tunggal (single source of truth).
     *
     * - tanggal_buka > hari ini           → Terjadwal
     * - tanggal_buka <= hari ini <= tanggal_tutup → Buka
     * - tanggal_tutup < hari ini          → Tutup
     * - Status di DB = Tutup (manual override oleh pengurus) → tetap Tutup
     */
    public function statusEfektif(): StatusPeriode
    {
        // Pengurus bisa manual menutup kapan saja — ini override tertinggi
        if ($this->status === StatusPeriode::Tutup) {
            return StatusPeriode::Tutup;
        }

        $today = now()->startOfDay();

        if ($today->lessThan($this->tanggal_buka)) {
            return StatusPeriode::Terjadwal;
        }

        if ($today->betweenIncluded($this->tanggal_buka, $this->tanggal_tutup)) {
            return StatusPeriode::Buka;
        }

        return StatusPeriode::Tutup;
    }

    /**
     * Cek apakah periode ini sedang aktif (bisa menerima pengajuan).
     */
    public function isBuka(): bool
    {
        return $this->statusEfektif() === StatusPeriode::Buka;
    }

    /**
     * Accessor: URL lengkap link pengajuan guest.
     */
    public function getLinkPengajuanAttribute(): string
    {
        return url("/pinjaman/ajukan/{$this->token}");
    }

    // === Scopes ===

    /**
     * Scope: periode yang statusnya Buka DAN tanggal hari ini di dalam range.
     */
    public function scopeAktifHariIni($query)
    {
        $today = now()->startOfDay();

        return $query->where('status', '!=', StatusPeriode::Tutup)
            ->whereDate('tanggal_buka', '<=', $today)
            ->whereDate('tanggal_tutup', '>=', $today);
    }

    /**
     * Scope: periode mendatang (belum mulai).
     */
    public function scopeMendatang($query)
    {
        $today = now()->startOfDay();

        return $query->where('status', '!=', StatusPeriode::Tutup)
            ->whereDate('tanggal_buka', '>', $today)
            ->orderBy('tanggal_buka', 'asc');
    }

    /**
     * Scope: periode sudah selesai/tutup.
     */
    public function scopeSudahTutup($query)
    {
        $today = now()->startOfDay();

        return $query->where(function ($q) use ($today) {
            $q->where('status', StatusPeriode::Tutup)
              ->orWhereDate('tanggal_tutup', '<', $today);
        })->orderByDesc('tanggal_tutup');
    }

    public function scopeBuka($query)
    {
        return $this->scopeAktifHariIni($query);
    }

    public function scopeTahun($query, int $tahun)
    {
        return $query->where('tahun', $tahun);
    }
}
