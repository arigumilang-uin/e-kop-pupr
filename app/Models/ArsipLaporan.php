<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArsipLaporan extends Model
{
    use HasFactory;

    protected $table = 'arsip_laporan';

    protected $fillable = [
        'tipe_laporan',
        'format',
        'nama_file',
        'file_path',
        'data_hash',
        'filter_info',
        'dibuat_oleh',
    ];

    protected $casts = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }
}
