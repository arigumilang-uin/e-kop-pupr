<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShuDistribusi extends Model
{
    protected $table = 'shu_distribusi';

    protected $fillable = [
        'nama',
        'persen',
        'deskripsi',
        'is_aktif',
        'urutan',
    ];

    protected function casts(): array
    {
        return [
            'persen' => 'float',
            'is_aktif' => 'boolean',
        ];
    }

    public function scopeAktif($query)
    {
        return $query->where('is_aktif', true);
    }
}
