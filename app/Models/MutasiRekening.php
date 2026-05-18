<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MutasiRekening extends Model
{
    use HasFactory;

    protected $table = 'mutasi_rekening';

    protected $fillable = [
        'tanggal',
        'jenis_mutasi',
        'nominal',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];
}
