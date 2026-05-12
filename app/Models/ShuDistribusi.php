<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShuDistribusi extends Model
{
    protected $table = 'shu_distribusi';

    // === Routing Constants ===
    public const ROUTING_PRORATA_SIMPANAN    = 'prorata_simpanan';
    public const ROUTING_PRORATA_PINJAMAN    = 'prorata_pinjaman';
    public const ROUTING_BAGI_RATA_PENGURUS  = 'bagi_rata_pengurus';
    public const ROUTING_EKUITAS             = 'ekuitas';
    public const ROUTING_KEWAJIBAN           = 'kewajiban';

    /**
     * Opsi dropdown untuk UI.
     */
    public const ROUTING_OPTIONS = [
        self::ROUTING_PRORATA_SIMPANAN   => [
            'label' => 'Simpanan Anggota — Prorata Simpanan (Jasa Modal)',
            'desc'  => 'Dibagikan ke seluruh anggota berdasarkan proporsi simpanan neto (Pokok + Wajib + SWP).',
            'color' => 'emerald',
        ],
        self::ROUTING_PRORATA_PINJAMAN   => [
            'label' => 'Simpanan Anggota — Prorata Pinjaman (Jasa Usaha)',
            'desc'  => 'Dibagikan ke anggota peminjam berdasarkan proporsi bunga yang sudah dibayar.',
            'color' => 'blue',
        ],
        self::ROUTING_BAGI_RATA_PENGURUS => [
            'label' => 'Dana Pengurus — Bagi Rata',
            'desc'  => 'Dibagikan rata ke pengurus yang NIP-nya terdaftar di sistem.',
            'color' => 'violet',
        ],
        self::ROUTING_EKUITAS            => [
            'label' => 'Ekuitas / Modal Koperasi',
            'desc'  => 'Dicatat sebagai penambahan modal koperasi (retained earnings).',
            'color' => 'amber',
        ],
        self::ROUTING_KEWAJIBAN          => [
            'label' => 'Kewajiban / Dana Titipan',
            'desc'  => 'Disimpan sebagai dompet dana titipan yang bisa direalisasikan secara berkala.',
            'color' => 'stone',
        ],
    ];

    /**
     * Daftar valid tipe_routing untuk validasi request.
     */
    public const VALID_ROUTINGS = [
        self::ROUTING_PRORATA_SIMPANAN,
        self::ROUTING_PRORATA_PINJAMAN,
        self::ROUTING_BAGI_RATA_PENGURUS,
        self::ROUTING_EKUITAS,
        self::ROUTING_KEWAJIBAN,
    ];

    protected $fillable = [
        'nama',
        'persen',
        'tipe_routing',
        'deskripsi',
        'is_aktif',
        'urutan',
    ];

    protected function casts(): array
    {
        return [
            'persen' => 'float',
            'is_aktif' => 'boolean',
        ];
    }

    public function scopeAktif($query)
    {
        return $query->where('is_aktif', true);
    }

    /**
     * Label routing yang mudah dibaca.
     */
    public function getRoutingLabelAttribute(): string
    {
        return self::ROUTING_OPTIONS[$this->tipe_routing]['label'] ?? $this->tipe_routing;
    }

    /**
     * Warna CSS routing untuk badge.
     */
    public function getRoutingColorAttribute(): string
    {
        return self::ROUTING_OPTIONS[$this->tipe_routing]['color'] ?? 'stone';
    }

    /**
     * Cek apakah routing ke simpanan anggota.
     */
    public function isRoutingKeAnggota(): bool
    {
        return in_array($this->tipe_routing, [
            self::ROUTING_PRORATA_SIMPANAN,
            self::ROUTING_PRORATA_PINJAMAN,
            self::ROUTING_BAGI_RATA_PENGURUS,
        ]);
    }
}
