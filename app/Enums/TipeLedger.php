<?php

namespace App\Enums;

enum TipeLedger: string
{
    case Kredit = 'kredit';
    case Debit = 'debit';

    public function label(): string
    {
        return match ($this) {
            self::Kredit => 'Kredit (Masuk)',
            self::Debit => 'Debit (Keluar)',
        };
    }

    public function sign(): int
    {
        return match ($this) {
            self::Kredit => 1,
            self::Debit => -1,
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Kredit => 'emerald',
            self::Debit => 'red',
        };
    }
}
