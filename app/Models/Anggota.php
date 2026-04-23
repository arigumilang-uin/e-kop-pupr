<?php

namespace App\Models;

use App\Enums\StatusAnggota;
use App\Enums\StatusPinjaman;
use App\Traits\HasRupiahFormat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Anggota extends Model
{
    use HasFactory, HasRupiahFormat;

    protected $table = 'anggota';

    protected $fillable = [
        'nip',
        'nama',
        'bidang_id',
        'no_hp',
        'tanggal_masuk',
        'tanggal_keluar',
        'is_pendaftar_ulang',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_masuk' => 'date',
            'tanggal_keluar' => 'date',
            'is_pendaftar_ulang' => 'boolean',
            'status' => StatusAnggota::class,
        ];
    }

    // === Relationships ===

    public function bidang(): BelongsTo
    {
        return $this->belongsTo(Bidang::class);
    }

    public function simpanan(): HasMany
    {
        return $this->hasMany(Simpanan::class);
    }

    public function penarikanSimpanan(): HasMany
    {
        return $this->hasMany(PenarikanSimpanan::class);
    }

    public function pinjaman(): HasMany
    {
        return $this->hasMany(Pinjaman::class);
    }

    public function potonganBulanan(): HasMany
    {
        return $this->hasMany(PotonganBulanan::class);
    }

    // === Business Logic ===

    /**
     * Cek apakah anggota memiliki pinjaman aktif di tahun tertentu.
     */
    public function hasPinjamanAktifDiTahun(int $tahun): bool
    {
        return $this->pinjaman()
            ->whereIn('status', StatusPinjaman::aktif())
            ->whereYear('tanggal_pengajuan', $tahun)
            ->exists();
    }

    /**
     * Hitung total saldo simpanan (per jenis atau semua).
     */
    public function totalSimpanan(?int $jenisSimpananId = null): float
    {
        $querySetor = $this->simpanan()
            ->when($jenisSimpananId, fn ($q) => $q->where('jenis_simpanan_id', $jenisSimpananId));

        $queryTarik = $this->penarikanSimpanan()
            ->when($jenisSimpananId, fn ($q) => $q->where('jenis_simpanan_id', $jenisSimpananId));

        return (float) ($querySetor->sum('nominal') - $queryTarik->sum('nominal'));
    }

    // === Scopes ===

    public function scopeAktif($query)
    {
        return $query->where('status', StatusAnggota::Aktif);
    }

    public function scopeNonaktif($query)
    {
        return $query->where('status', StatusAnggota::Nonaktif);
    }

    public function scopeByBidang($query, int $bidangId)
    {
        return $query->where('bidang_id', $bidangId);
    }
}
