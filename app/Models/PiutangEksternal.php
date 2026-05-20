<?php

namespace App\Models;

use App\Enums\KategoriPeminjam;
use App\Enums\KategoriPiutangLegacy;
use App\Traits\HasNoReferensi;
use App\Traits\HasRupiahFormat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PiutangEksternal extends Model
{
    use HasNoReferensi, HasRupiahFormat;

    protected static string $refPrefix = 'PTE';

    protected $table = 'piutang_eksternal';

    protected $fillable = [
        'no_referensi',
        'nama_peminjam',
        'jabatan_peminjam',
        'kategori_peminjam',
        'sumber_dana',
        'kategori_piutang',
        'bidang',
        'periode_pengurus',
        'tahun_pinjam',
        'nominal_awal',
        'nominal_terbayar',
        'sisa_piutang',
        'keterangan',
        'status',
        'tanggal_catat',
        'dicatat_oleh',
    ];

    protected function casts(): array
    {
        return [
            'kategori_peminjam' => KategoriPeminjam::class,
            'kategori_piutang' => KategoriPiutangLegacy::class,
            'nominal_awal' => 'decimal:2',
            'nominal_terbayar' => 'decimal:2',
            'sisa_piutang' => 'decimal:2',
            'tanggal_catat' => 'date',
        ];
    }

    // === Relationships ===

    public function pembayaran(): HasMany
    {
        return $this->hasMany(PembayaranPiutangEksternal::class)->orderByDesc('tanggal_bayar');
    }

    public function pencatat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dicatat_oleh');
    }

    // === Scopes ===

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    public function scopeLunas($query)
    {
        return $query->where('status', 'lunas');
    }

    public function scopeKategoriPiutang($query, KategoriPiutangLegacy $kategori)
    {
        return $query->where('kategori_piutang', $kategori);
    }

    public function scopeAktifDiNeraca($query)
    {
        return $query->aktif()->whereIn('kategori_piutang', collect(KategoriPiutangLegacy::cases())
            ->filter(fn($k) => $k->isAktifDiNeraca())
            ->map(fn($k) => $k->value)
            ->values()
            ->toArray()
        );
    }

    // === Business Logic ===

    public function hitungSisaPiutang(): float
    {
        return (float) $this->nominal_awal - (float) $this->nominal_terbayar;
    }

    public function isLunas(): bool
    {
        return $this->sisa_piutang <= 0;
    }

    public function progressPersen(): float
    {
        if ($this->nominal_awal <= 0) return 0;
        return round(($this->nominal_terbayar / $this->nominal_awal) * 100, 1);
    }
}
