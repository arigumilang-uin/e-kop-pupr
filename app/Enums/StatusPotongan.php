<?php

namespace App\Enums;

enum StatusPotongan: string
{
    case Draft = 'draft';
    case Dikonfirmasi = 'dikonfirmasi';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Dikonfirmasi => 'Dikonfirmasi',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Draft => 'amber',
            self::Dikonfirmasi => 'emerald',
        };
    }
}
