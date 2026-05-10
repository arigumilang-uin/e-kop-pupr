<?php

namespace App\Models;

use App\Enums\StatusVoid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

/**
 * VoidRequest — Permintaan void transaksi (Maker-Checker Workflow).
 *
 * Alur:
 * 1. Pengurus (Maker) membuat permintaan void dengan alasan wajib.
 * 2. Pimpinan/Super Admin (Checker) menyetujui atau menolak.
 * 3. Jika disetujui: VoidService membuat reversal entry di ledger.
 * 4. Semua void wajib melalui approval — tidak ada auto-approve.
 */
class VoidRequest extends Model
{
    use HasFactory;

    protected $table = 'void_requests';

    protected $fillable = [
        'ledger_id',
        'alasan',
        'status',
        'diminta_oleh',
        'tanggal_permintaan',
        'diputuskan_oleh',
        'tanggal_keputusan',
        'catatan_keputusan',
        'reversal_ledger_id',
    ];

    protected function casts(): array
    {
        return [
            'status' => StatusVoid::class,
            'tanggal_permintaan' => 'datetime',
            'tanggal_keputusan' => 'datetime',
        ];
    }

    // === Relationships ===

    /**
     * Entry ledger yang diminta untuk di-void.
     */
    public function ledger(): BelongsTo
    {
        return $this->belongsTo(Ledger::class);
    }

    /**
     * Entry ledger reversal yang dibuat setelah void disetujui.
     */
    public function reversalLedger(): BelongsTo
    {
        return $this->belongsTo(Ledger::class, 'reversal_ledger_id');
    }

    /**
     * Pengurus yang mengajukan void (Maker).
     */
    public function pemohon(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diminta_oleh');
    }

    /**
     * Pimpinan yang memutuskan void (Checker).
     */
    public function pemutus(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diputuskan_oleh');
    }

    // === Scopes ===

    public function scopeMenunggu($query)
    {
        return $query->where('status', StatusVoid::Menunggu);
    }

    public function scopeDisetujui($query)
    {
        return $query->where('status', StatusVoid::Disetujui);
    }

    public function scopeDitolak($query)
    {
        return $query->where('status', StatusVoid::Ditolak);
    }
}
