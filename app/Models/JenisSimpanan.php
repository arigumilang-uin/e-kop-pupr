<?php

namespace App\Models;

use App\Enums\FrekuensiSimpanan;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class JenisSimpanan extends Model
{
    use HasFactory;

    protected $table = 'jenis_simpanan';

    protected $fillable = [
        'kode',
        'nama',
        'nominal_default',
        'is_wajib',
        'frekuensi',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'nominal_default' => 'decimal:2',
            'is_wajib' => 'boolean',
            'frekuensi' => FrekuensiSimpanan::class,
        ];
    }

    // === Relationships ===

    public function simpanan(): HasMany
    {
        return $this->hasMany(Simpanan::class);
    }

    // === Static Accessors ===

    public static function pokok(): ?self
    {
        return static::where('kode', 'POKOK')->first();
    }

    public static function wajib(): ?self
    {
        return static::where('kode', 'WAJIB')->first();
    }

    public static function sim2025(): ?self
    {
        return static::where('kode', 'SIM2025')->first();
    }

    public static function swp(): ?self
    {
        return static::where('kode', 'SWP')->first();
    }

    public static function bonusShu(): ?self
    {
        return static::where('kode', 'BONUS_SHU')->first();
    }
}
