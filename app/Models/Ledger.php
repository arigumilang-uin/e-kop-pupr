<?php

namespace App\Models;

use App\Enums\KategoriLedger;
use App\Enums\TipeLedger;
use App\Traits\HasNoReferensi;
use App\Traits\HasRupiahFormat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Model;

/**
 * Ledger — Jurnal Umum Koperasi (Immutable Append-Only).
 *
 * ATURAN:
 * 1. Record TIDAK BOLEH di-UPDATE (kecuali timestamp oleh framework).
 * 2. Record TIDAK BOLEH di-DELETE.
 * 3. Koreksi dilakukan dengan INSERT entry reversal baru.
 * 4. Nominal SELALU POSITIF. Arah ditentukan oleh `tipe` (kredit/debit).
 */
class Ledger extends Model
{
    use HasFactory, HasNoReferensi, HasRupiahFormat;

    public $timestamps = false;

    protected static string $refPrefix = 'LDG';

    protected $table = 'ledger';

    protected $fillable = [
        'no_referensi',
        'tipe',
        'kategori',
        'nominal',
        'deskripsi',
        'transaksi_ref_id',
        'transaksi_ref_type',
        'void_of_id',
        'anggota_id',
        'tanggal_efektif',
        'dicatat_oleh',
    ];

    protected function casts(): array
    {
        return [
            'tipe' => TipeLedger::class,
            'kategori' => KategoriLedger::class,
            'nominal' => 'decimal:2',
            'tanggal_efektif' => 'date',
            'created_at' => 'datetime',
        ];
    }

    // === Relationships ===

    /**
     * Transaksi asal (polymorphic: Simpanan, Angsuran, Pinjaman, PengeluaranKas, dll).
     */
    public function transaksiRef(): MorphTo
    {
        return $this->morphTo('transaksiRef', 'transaksi_ref_type', 'transaksi_ref_id');
    }

    /**
     * Jika entry ini adalah reversal, merujuk ke entry asli yang di-void.
     */
    public function voidOf(): BelongsTo
    {
        return $this->belongsTo(Ledger::class, 'void_of_id');
    }

    /**
     * Entry reversal yang membatalkan entry ini.
     */
    public function reversals(): HasMany
    {
        return $this->hasMany(Ledger::class, 'void_of_id');
    }

    /**
     * Void request terkait entry ini.
     */
    public function voidRequests(): HasMany
    {
        return $this->hasMany(VoidRequest::class, 'ledger_id');
    }

    public function anggota(): BelongsTo
    {
        return $this->belongsTo(Anggota::class);
    }

    public function pencatat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dicatat_oleh');
    }

    // === Scopes ===

    public function scopeKredit($query)
    {
        return $query->where('tipe', TipeLedger::Kredit);
    }

    public function scopeDebit($query)
    {
        return $query->where('tipe', TipeLedger::Debit);
    }

    public function scopeKategori($query, KategoriLedger $kategori)
    {
        return $query->where('kategori', $kategori);
    }

    public function scopeBukanVoid($query)
    {
        return $query->where('kategori', '!=', KategoriLedger::Void);
    }

    /**
     * Hanya entry yang belum pernah di-void (tidak memiliki reversal).
     */
    public function scopeAktif($query)
    {
        return $query->whereDoesntHave('reversals');
    }

    /**
     * Cek apakah entry ini sudah di-void (memiliki reversal entry).
     */
    public function isVoided(): bool
    {
        return $this->reversals()->exists();
    }

    /**
     * Cek apakah entry ini memiliki pending void request.
     */
    public function hasPendingVoidRequest(): bool
    {
        return $this->voidRequests()->where('status', 'menunggu')->exists();
    }

    /**
     * Override generateNoReferensi untuk format 6-digit (LDG-YYYY-NNNNNN).
     */
    public static function generateNoReferensi($model = null): string
    {
        $prefix = static::$refPrefix;
        $tahun = now()->format('Y');

        if ($model && !empty($model->tanggal_efektif)) {
            $tahun = \Carbon\Carbon::parse($model->tanggal_efektif)->format('Y');
        }

        $pattern = "{$prefix}-{$tahun}-";

        $lastNumber = static::where('no_referensi', 'like', "{$pattern}%")
            ->orderByDesc('no_referensi')
            ->value('no_referensi');

        if ($lastNumber) {
            $lastSeq = (int) substr($lastNumber, -6);
            $nextSeq = $lastSeq + 1;
        } else {
            $nextSeq = 1;
        }

        return $pattern . str_pad($nextSeq, 6, '0', STR_PAD_LEFT);
    }
}
