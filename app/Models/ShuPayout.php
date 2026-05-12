<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShuPayout extends Model
{
    protected $table = 'shu_payout';

    protected $fillable = [
        'tahun',
        'total_shu_bersih',
        'total_jasa_modal',
        'total_jasa_usaha',
        'total_cadangan',
        'total_dana_kewajiban',
        'jumlah_penerima',
        'total_terdistribusi',
        'dieksekusi_oleh',
    ];

    protected function casts(): array
    {
        return [
            'total_shu_bersih' => 'decimal:2',
            'total_jasa_modal' => 'decimal:2',
            'total_jasa_usaha' => 'decimal:2',
            'total_cadangan' => 'decimal:2',
            'total_dana_kewajiban' => 'decimal:2',
            'total_terdistribusi' => 'decimal:2',
        ];
    }

    public function eksekutor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dieksekusi_oleh');
    }
}
