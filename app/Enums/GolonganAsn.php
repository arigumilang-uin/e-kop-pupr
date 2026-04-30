<?php

namespace App\Enums;

enum GolonganAsn: string
{
    case PNS = 'pns';
    case PPPK = 'pppk';

    public function label(): string
    {
        return match ($this) {
            self::PNS => 'PNS',
            self::PPPK => 'PPPK',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PNS => 'blue',
            self::PPPK => 'violet',
        };
    }
}
