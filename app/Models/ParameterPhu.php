<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParameterPhu extends Model
{
    protected $table = 'parameter_phu';

    protected $fillable = [
        'nama', 'tipe', 'sumber_data', 'kode_otomatis', 'konfigurasi',
        'nominal_manual', 'urutan', 'is_active',
        'tahun_buku', 'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'nominal_manual' => 'decimal:2',
            'urutan'         => 'integer',
            'is_active'      => 'boolean',
            'tahun_buku'     => 'integer',
            'konfigurasi'    => 'array',
        ];
    }

    // === Scopes ===

    public function scopeAktif($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeTipe($query, string $tipe)
    {
        return $query->where('tipe', $tipe);
    }

    public function scopeTahun($query, int $tahun)
    {
        return $query->where('tahun_buku', $tahun);
    }

    public function scopeUrut($query)
    {
        return $query->orderBy('urutan')->orderBy('id');
    }

    // === Helpers ===

    public function isManual(): bool
    {
        return $this->sumber_data === 'manual';
    }

    public function isOtomatis(): bool
    {
        return $this->sumber_data === 'otomatis';
    }

    /**
     * Ambil parameter aktif per tipe untuk tahun tertentu.
     */
    public static function byTipe(string $tipe, int $tahun): \Illuminate\Database\Eloquent\Collection
    {
        return static::aktif()->tahun($tahun)->tipe($tipe)->urut()->get();
    }
}
