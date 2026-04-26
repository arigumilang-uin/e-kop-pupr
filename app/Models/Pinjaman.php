<?php

namespace App\Models;

use App\Enums\StatusPinjaman;
use App\Traits\HasNoReferensi;
use App\Traits\HasRupiahFormat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Pinjaman extends Model
{
    use HasFactory, HasNoReferensi, HasRupiahFormat, SoftDeletes;

    protected static string $refPrefix = 'PJM';

    protected $table = 'pinjaman';

    protected $fillable = [
        'no_referensi',
        'anggota_id',
        'periode_pinjaman_id',
        'bulan_pengajuan',
        'nama_bank',
        'nama_rekening',
        'no_rekening',
        'nominal_pinjaman',
        'bunga_persen',
        'total_bunga',
        'tenor_bulan',
        'angsuran_pokok',
        'angsuran_bunga',
        'total_angsuran',
        'total_bayar',
        'potongan_swp',
        'potongan_dana_resiko',
        'potongan_biaya_admin',
        'total_potongan',
        'dana_diterima',
        'tanggal_pengajuan',
        'tanggal_approval',
        'status',
        'approved_by',
        'is_override',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'nominal_pinjaman' => 'decimal:2',
            'bunga_persen' => 'decimal:2',
            'total_bunga' => 'decimal:2',
            'angsuran_pokok' => 'decimal:2',
            'angsuran_bunga' => 'decimal:2',
            'total_angsuran' => 'decimal:2',
            'total_bayar' => 'decimal:2',
            'potongan_swp' => 'decimal:2',
            'potongan_dana_resiko' => 'decimal:2',
            'potongan_biaya_admin' => 'decimal:2',
            'total_potongan' => 'decimal:2',
            'dana_diterima' => 'decimal:2',
            'tanggal_pengajuan' => 'datetime',
            'tanggal_approval' => 'datetime',
            'status' => StatusPinjaman::class,
            'is_override' => 'boolean',
        ];
    }

    // === Relationships ===

    public function anggota(): BelongsTo
    {
        return $this->belongsTo(Anggota::class);
    }

    public function periodePinjaman(): BelongsTo
    {
        return $this->belongsTo(PeriodePinjaman::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function angsuran(): HasMany
    {
        return $this->hasMany(Angsuran::class);
    }

    public function simpananSwp(): HasMany
    {
        return $this->hasMany(Simpanan::class);
    }

    // === Business Logic ===

    public function sisaPokok(): float
    {
        $dibayar = $this->angsuran()->where('status', 'lunas')->sum('nominal_pokok');
        return (float) ($this->nominal_pinjaman - $dibayar);
    }

    public function angsuranTerbayar(): int
    {
        return $this->angsuran()->where('status', 'lunas')->count();
    }

    public function angsuranBelum(): int
    {
        return $this->angsuran()->where('status', 'belum')->count();
    }

    // === Scopes ===

    public function scopeBerjalan($query)
    {
        return $query->where('status', StatusPinjaman::Berjalan);
    }

    public function scopeAktif($query)
    {
        return $query->whereIn('status', StatusPinjaman::aktif());
    }

    public function scopeMenunggu($query)
    {
        return $query->where('status', StatusPinjaman::Menunggu);
    }
}
