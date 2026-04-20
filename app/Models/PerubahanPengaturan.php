<?php

namespace App\Models;

use App\Enums\StatusPerubahanPengaturan;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class PerubahanPengaturan extends Model
{
    protected $table = 'perubahan_pengaturan';

    protected $fillable = [
        'pengaturan_id',
        'nilai_lama',
        'nilai_baru',
        'alasan',
        'diajukan_oleh',
        'tanggal_pengajuan',
        'status',
        'diputuskan_oleh',
        'tanggal_keputusan',
        'catatan_keputusan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_pengajuan' => 'datetime',
            'tanggal_keputusan' => 'datetime',
            'status' => StatusPerubahanPengaturan::class,
        ];
    }

    // === Relationships ===

    public function pengaturan(): BelongsTo
    {
        return $this->belongsTo(Pengaturan::class);
    }

    /**
     * Admin yang mengajukan (Maker).
     */
    public function pengaju(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diajukan_oleh');
    }

    /**
     * Pimpinan yang memutuskan (Checker).
     */
    public function pemutus(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diputuskan_oleh');
    }

    // === Scopes ===

    public function scopeMenunggu($query)
    {
        return $query->where('status', StatusPerubahanPengaturan::Menunggu);
    }
}
