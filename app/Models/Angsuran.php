<?php

namespace App\Models;

use App\Enums\StatusAngsuran;
use App\Traits\HasRupiahFormat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Angsuran extends Model
{
    use HasFactory, HasRupiahFormat, SoftDeletes;

    protected $table = 'angsuran';

    protected $fillable = [
        'pinjaman_id',
        'angsuran_ke',
        'nominal_pokok',
        'nominal_bunga',
        'nominal_total',
        'tanggal_jatuh_tempo',
        'tanggal_bayar',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'nominal_pokok' => 'decimal:2',
            'nominal_bunga' => 'decimal:2',
            'nominal_total' => 'decimal:2',
            'tanggal_jatuh_tempo' => 'date',
            'tanggal_bayar' => 'date',
            'status' => StatusAngsuran::class,
        ];
    }

    // === Relationships ===

    public function pinjaman(): BelongsTo
    {
        return $this->belongsTo(Pinjaman::class);
    }

    // === Scopes ===

    public function scopeBelumLunas($query)
    {
        return $query->where('status', StatusAngsuran::Belum);
    }

    public function scopeLunas($query)
    {
        return $query->where('status', StatusAngsuran::Lunas);
    }
}
