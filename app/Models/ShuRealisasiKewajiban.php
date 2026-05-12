<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShuRealisasiKewajiban extends Model
{
    protected $table = 'shu_realisasi_kewajiban';

    protected $fillable = [
        'shu_kewajiban_id',
        'tanggal_realisasi',
        'nominal',
        'keterangan',
        'dicatat_oleh',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_realisasi' => 'date',
            'nominal' => 'decimal:2',
        ];
    }

    public function kewajiban(): BelongsTo
    {
        return $this->belongsTo(ShuKewajiban::class, 'shu_kewajiban_id');
    }

    public function pencatat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dicatat_oleh');
    }
}
