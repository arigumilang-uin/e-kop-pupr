<?php

namespace App\Models;

use App\Traits\HasNoReferensi;
use App\Traits\HasRupiahFormat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Simpanan extends Model
{
    use HasFactory, HasNoReferensi, HasRupiahFormat, SoftDeletes;

    protected static string $refPrefix = 'SIM';

    protected $table = 'simpanan';

    protected $fillable = [
        'no_referensi',
        'anggota_id',
        'jenis_simpanan_id',
        'nominal',
        'tanggal',
        'bulan_untuk',
        'tahun_untuk',
        'pinjaman_id',
        'keterangan',
        'dicatat_oleh',
    ];

    protected function casts(): array
    {
        return [
            'nominal' => 'decimal:2',
            'tanggal' => 'date',
        ];
    }

    // === Relationships ===

    public function anggota(): BelongsTo
    {
        return $this->belongsTo(Anggota::class);
    }

    public function jenisSimpanan(): BelongsTo
    {
        return $this->belongsTo(JenisSimpanan::class);
    }

    public function pinjaman(): BelongsTo
    {
        return $this->belongsTo(Pinjaman::class);
    }

    public function pencatat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dicatat_oleh');
    }
}
