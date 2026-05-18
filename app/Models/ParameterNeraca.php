<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParameterNeraca extends Model
{
    protected $table = 'parameter_neraca';

    protected $fillable = [
        'nama', 'posisi', 'sumber_data', 'kode_otomatis', 'konfigurasi',
        'nominal_manual', 'urutan', 'is_pengurang', 'is_active',
        'tahun_buku', 'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'nominal_manual' => 'decimal:2',
            'urutan'         => 'integer',
            'is_pengurang'   => 'boolean',
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

    public function scopePosisi($query, string $posisi)
    {
        return $query->where('posisi', $posisi);
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
     * Ambil semua parameter aktif, grouped by posisi, untuk tahun tertentu.
     */
    public static function byPosisi(string $posisi, int $tahun): \Illuminate\Database\Eloquent\Collection
    {
        return static::aktif()->tahun($tahun)->posisi($posisi)->urut()->get();
    }

    /**
     * Daftar posisi Aktiva (kiri Neraca).
     */
    public static function posisiAktiva(): array
    {
        return ['aktiva_lancar', 'penyertaan', 'harta_tetap', 'harta_lain'];
    }

    /**
     * Daftar posisi Pasiva (kanan Neraca).
     */
    public static function posisiPasiva(): array
    {
        return ['kewajiban_pendek', 'kewajiban_panjang', 'modal'];
    }

    /**
     * Label human-readable untuk posisi.
     */
    public static function labelPosisi(string $posisi): string
    {
        return match ($posisi) {
            'aktiva_lancar'     => 'I. Harta Lancar',
            'penyertaan'        => 'II. Penyertaan',
            'harta_tetap'       => 'III. Harta Tetap',
            'harta_lain'        => 'IV. Harta Lain-lain',
            'kewajiban_pendek'  => 'IV. Kewajiban Jangka Pendek',
            'kewajiban_panjang' => 'V. Kewajiban Jangka Panjang',
            'modal'             => 'VI. Modal Sendiri',
            default             => ucwords(str_replace('_', ' ', $posisi)),
        };
    }
}
