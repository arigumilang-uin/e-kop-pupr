<?php

namespace App\Enums;

enum StatusPeriode: string
{
    case Buka = 'buka';
    case Tutup = 'tutup';
    case Selesai = 'selesai';

    public function label(): string
    {
        return match ($this) {
            self::Buka => 'Buka',
            self::Tutup => 'Tutup',
            self::Selesai => 'Selesai',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Buka => 'emerald',
            self::Tutup => 'red',
            self::Selesai => 'slate',
        };
    }
}
