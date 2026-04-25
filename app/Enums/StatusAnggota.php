<?php

namespace App\Enums;

enum StatusAnggota: string
{
    case Aktif = 'aktif';
    case Nonaktif = 'nonaktif';
    case Pensiun = 'pensiun';

    public function label(): string
    {
        return match ($this) {
            self::Aktif => 'Aktif',
            self::Nonaktif => 'Nonaktif / Keluar',
            self::Pensiun => 'Pensiun',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Aktif => 'emerald',
            self::Nonaktif => 'red',
            self::Pensiun => 'amber',
        };
    }
}
