<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Bidang extends Model
{
    use HasFactory;

    protected $table = 'bidang';

    protected $fillable = [
        'nama_bidang',
        'keterangan',
    ];

    /**
     * Anggota yang terdaftar di bidang ini.
     */
    public function anggota(): HasMany
    {
        return $this->hasMany(Anggota::class);
    }
}
