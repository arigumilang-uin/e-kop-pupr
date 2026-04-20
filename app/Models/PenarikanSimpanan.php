<?php

namespace App\Models;

use App\Traits\HasNoReferensi;
use App\Traits\HasRupiahFormat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class PenarikanSimpanan extends Model
{
    use HasFactory, HasNoReferensi, HasRupiahFormat;

    protected static string $refPrefix = 'TRK';

    protected $table = 'penarikan_simpanan';

    protected $fillable = [
        'no_referensi',
        'anggota_id',
        'jenis_simpanan_id',
        'nominal',
        'tanggal',
        'keterangan',
        'diproses_oleh',
    ];

    protected function casts(): array
    {
        return [
            'nominal' => 'decimal:2',
            'tanggal' => 'date',
        ];
    }

    public function anggota(): BelongsTo
    {
        return $this->belongsTo(Anggota::class);
    }

    public function jenisSimpanan(): BelongsTo
    {
        return $this->belongsTo(JenisSimpanan::class);
    }

    public function pemroses(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diproses_oleh');
    }
}
