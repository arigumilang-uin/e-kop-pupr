<?php

namespace App\Enums;

enum StatusPeriode: string
{
    case Buka = 'buka';
    case Tutup = 'tutup';
    case Terjadwal = 'terjadwal';

    public function label(): string
    {
        return match ($this) {
            self::Buka => 'Buka',
            self::Tutup => 'Tutup',
            self::Terjadwal => 'Terjadwal',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Buka => 'emerald',
            self::Tutup => 'red',
            self::Terjadwal => 'amber',
        };
    }
}
