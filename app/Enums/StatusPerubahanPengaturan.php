<?php

namespace App\Enums;

enum StatusPerubahanPengaturan: string
{
    case Menunggu = 'menunggu';
    case Disetujui = 'disetujui';
    case Ditolak = 'ditolak';

    public function label(): string
    {
        return match ($this) {
            self::Menunggu => 'Menunggu',
            self::Disetujui => 'Disetujui',
            self::Ditolak => 'Ditolak',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Menunggu => 'amber',
            self::Disetujui => 'emerald',
            self::Ditolak => 'red',
        };
    }
}
