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
        'batas_bulan_pelunasan',
        'limit_per_anggota',
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
            'status' => StatusPeriode::class,
        ];
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

    // === Business Logic ===

    public function isBuka(): bool
    {
        if ($this->status !== \App\Enums\StatusPeriode::Buka) {
            return false;
        }
        
        $now = now()->startOfDay();
        return $now->betweenIncluded($this->tanggal_buka, $this->tanggal_tutup);
    }

    /**
     * Accessor: URL lengkap link pengajuan guest.
     */
    public function getLinkPengajuanAttribute(): string
    {
        return url("/pinjaman/ajukan/{$this->token}");
    }

    // === Scopes ===

    public function scopeBuka($query)
    {
        return $query->where('status', StatusPeriode::Buka);
    }

    public function scopeTahun($query, int $tahun)
    {
        return $query->where('tahun', $tahun);
    }
}
