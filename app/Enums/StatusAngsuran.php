<?php

namespace App\Enums;

enum StatusAngsuran: string
{
    case Belum = 'belum';
    case Lunas = 'lunas';

    public function label(): string
    {
        return match ($this) {
            self::Belum => 'Belum Dibayar',
            self::Lunas => 'Lunas',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Belum => 'amber',
            self::Lunas => 'emerald',
        };
    }
}
