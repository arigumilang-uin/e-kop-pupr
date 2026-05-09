<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengaturanKhusus extends Model
{
    protected $table = 'pengaturan_khusus';

    protected $fillable = [
        'key',
        'bulan',
        'tahun',
        'value',
    ];

    protected $casts = [
        'bulan' => 'integer',
        'tahun' => 'integer',
    ];
}
