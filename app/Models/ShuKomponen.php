<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShuKomponen extends Model
{
    protected $table = 'shu_komponen';

    protected $fillable = [
        'nama',
        'tipe',
        'sumber_data',
        'deskripsi',
        'is_aktif',
        'urutan',
    ];

    protected function casts(): array
    {
        return [
            'is_aktif' => 'boolean',
        ];
    }

    // === Scopes ===

    public function scopeAktif($query)
    {
        return $query->where('is_aktif', true);
    }

    public function scopePendapatan($query)
    {
        return $query->where('tipe', 'pendapatan');
    }

    public function scopeBeban($query)
    {
        return $query->where('tipe', 'beban');
    }
}
