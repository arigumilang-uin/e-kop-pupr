<?php

namespace App\Enums;

enum StatusSimpanan: string
{
    case Aktif = 'aktif';
    case Voided = 'voided';

    public function label(): string
    {
        return match ($this) {
            self::Aktif => 'Aktif',
            self::Voided => 'Dibatalkan (Void)',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Aktif => 'emerald',
            self::Voided => 'red',
        };
    }
}
