<?php

namespace App\Enums;

enum StatusAnggota: string
{
    case Aktif = 'aktif';
    case Nonaktif = 'nonaktif';

    public function label(): string
    {
        return match ($this) {
            self::Aktif => 'Aktif',
            self::Nonaktif => 'Nonaktif',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Aktif => 'emerald',
            self::Nonaktif => 'red',
        };
    }
}
