<?php

namespace App\Models;

use App\Enums\KategoriPengaturan;
use App\Enums\StatusPerubahanPengaturan;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Pengaturan extends Model
{
    protected $table = 'pengaturan';

    protected $fillable = [
        'key',
        'value',
        'deskripsi',
        'kategori',
        'memerlukan_persetujuan',
    ];

    protected function casts(): array
    {
        return [
            'memerlukan_persetujuan' => 'boolean',
            'kategori' => KategoriPengaturan::class,
        ];
    }

    // === Relationships ===

    public function perubahanPengaturan(): HasMany
    {
        return $this->hasMany(PerubahanPengaturan::class);
    }

    // === Business Logic ===

    /**
     * Cek apakah ada pengajuan perubahan yang menunggu.
     */
    public function hasPendingChange(): bool
    {
        return $this->perubahanPengaturan()
            ->where('status', StatusPerubahanPengaturan::Menunggu)
            ->exists();
    }
}
