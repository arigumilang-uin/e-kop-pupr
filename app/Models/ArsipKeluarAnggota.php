<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArsipKeluarAnggota extends Model
{
    protected $table = 'arsip_keluar_anggota';

    protected $fillable = [
        'anggota_id',
        'tanggal_keluar',
        'total_simpanan_dikembalikan',
        'rincian_simpanan',
        'nominal_wajib_setor_ulang',
        'catatan',
        'diproses_oleh',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_keluar' => 'date',
            'total_simpanan_dikembalikan' => 'decimal:2',
            'nominal_wajib_setor_ulang' => 'decimal:2',
            'rincian_simpanan' => 'array',
        ];
    }

    public function anggota(): BelongsTo
    {
        return $this->belongsTo(Anggota::class);
    }

    public function pemroses(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diproses_oleh');
    }
}
