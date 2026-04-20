<?php

namespace App\Enums;

enum KategoriPengaturan: string
{
    case Keuangan = 'keuangan';
    case Teknis = 'teknis';

    public function label(): string
    {
        return match ($this) {
            self::Keuangan => 'Keuangan',
            self::Teknis => 'Teknis',
        };
    }

    /**
     * Apakah kategori ini memerlukan persetujuan pimpinan.
     */
    public function memerlukanPersetujuan(): bool
    {
        return match ($this) {
            self::Keuangan => true,
            self::Teknis => false,
        };
    }
}
