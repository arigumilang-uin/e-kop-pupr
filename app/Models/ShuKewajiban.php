<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShuKewajiban extends Model
{
    protected $table = 'shu_kewajiban';

    protected $fillable = [
        'tahun',
        'nama_alokasi',
        'nominal_awal',
        'nominal_terpakai',
        'saldo_tersisa',
    ];

    protected function casts(): array
    {
        return [
            'nominal_awal' => 'decimal:2',
            'nominal_terpakai' => 'decimal:2',
            'saldo_tersisa' => 'decimal:2',
        ];
    }

    public function realisasi(): HasMany
    {
        return $this->hasMany(ShuRealisasiKewajiban::class, 'shu_kewajiban_id');
    }
}
