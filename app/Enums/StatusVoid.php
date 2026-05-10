<?php

namespace App\Enums;

enum StatusVoid: string
{
    case Menunggu = 'menunggu';
    case Disetujui = 'disetujui';
    case Ditolak = 'ditolak';

    public function label(): string
    {
        return match ($this) {
            self::Menunggu => 'Menunggu Persetujuan',
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
