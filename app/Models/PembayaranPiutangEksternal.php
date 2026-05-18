<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PembayaranPiutangEksternal extends Model
{
    protected $table = 'pembayaran_piutang_eksternal';

    protected $fillable = [
        'piutang_eksternal_id',
        'nominal',
        'tanggal_bayar',
        'bukti_bayar',
        'keterangan',
        'dicatat_oleh',
    ];

    protected function casts(): array
    {
        return [
            'nominal' => 'decimal:2',
            'tanggal_bayar' => 'date',
        ];
    }

    public function piutangEksternal(): BelongsTo
    {
        return $this->belongsTo(PiutangEksternal::class);
    }

    public function pencatat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dicatat_oleh');
    }
}
