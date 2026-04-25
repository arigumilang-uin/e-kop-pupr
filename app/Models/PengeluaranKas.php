<?php

namespace App\Models;

use App\Traits\HasNoReferensi;
use App\Traits\HasRupiahFormat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengeluaranKas extends Model
{
    use HasFactory, HasNoReferensi, HasRupiahFormat;

    protected static string $refPrefix = 'KLR';

    protected $table = 'pengeluaran_kas';

    protected $fillable = [
        'no_referensi',
        'kategori_pengeluaran_id',
        'nominal',
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
