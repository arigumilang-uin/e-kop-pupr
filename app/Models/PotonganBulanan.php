<?php

namespace App\Models;

use App\Enums\StatusPotongan;
use App\Traits\HasRupiahFormat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class PotonganBulanan extends Model
{
    use HasFactory, HasRupiahFormat, SoftDeletes;

    protected $table = 'potongan_bulanan';

    protected $fillable = [
        'anggota_id',
        'bulan',
        'tahun',
        'potongan_simpanan_wajib',
        'potongan_angsuran',
        'potongan_lainnya',
        'total_potongan',
        'catatan',
        'status',
        'dicatat_oleh',
    ];

    protected function casts(): array
    {
        return [
            'potongan_simpanan_wajib' => 'decimal:2',
            'potongan_angsuran' => 'decimal:2',
            'potongan_lainnya' => 'decimal:2',
            'total_potongan' => 'decimal:2',
            'status' => StatusPotongan::class,
        ];
    }

    // === Relationships ===

    public function anggota(): BelongsTo
    {
        return $this->belongsTo(Anggota::class);
    }

    public function pencatat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dicatat_oleh');
    }

    // === Scopes ===

    public function scopeDraft($query)
    {
        return $query->where('status', StatusPotongan::Draft);
    }

    public function scopeDikonfirmasi($query)
    {
        return $query->where('status', StatusPotongan::Dikonfirmasi);
    }

    public function scopeBulanTahun($query, int $bulan, int $tahun)
    {
        return $query->where('bulan', $bulan)->where('tahun', $tahun);
    }
}
