<?php

namespace App\Models;

use App\Enums\StatusSimpanan;
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
        'status',
        'dicatat_oleh',
    ];

    protected function casts(): array
    {
        return [
            'nominal' => 'decimal:2',
            'tanggal' => 'date',
            'status' => StatusSimpanan::class,
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

    // === Scopes ===

    /**
     * Hanya simpanan yang aktif (belum di-void).
     */
    public function scopeAktif($query)
    {
        return $query->where('status', StatusSimpanan::Aktif);
    }

    /**
     * Cek apakah simpanan ini sudah di-void.
     */
    public function isVoided(): bool
    {
        return $this->status === StatusSimpanan::Voided;
    }
}
