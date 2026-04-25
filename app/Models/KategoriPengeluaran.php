<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KategoriPengeluaran extends Model
{
    use HasFactory;

    protected $table = 'kategori_pengeluaran';

    protected $fillable = [
        'nama',
        'deskripsi',
    ];

    public function pengeluaranKas(): HasMany
    {
        return $this->hasMany(PengeluaranKas::class);
    }
}
