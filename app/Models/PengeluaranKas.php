<?php

namespace App\Models;

use App\Traits\HasNoReferensi;
use App\Traits\HasRupiahFormat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PengeluaranKas extends Model
{
    use HasFactory, HasNoReferensi, HasRupiahFormat, SoftDeletes;

    protected static string $refPrefix = 'KLR';

    protected $table = 'pengeluaran_kas';

    protected $fillable = [
        'no_referensi',
        'kategori_pengeluaran_id',
        'nominal',
        'sumber_dana',
        'tanggal',
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

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriPengeluaran::class, 'kategori_pengeluaran_id');
    }

    public function pencatat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dicatat_oleh');
    }
}
