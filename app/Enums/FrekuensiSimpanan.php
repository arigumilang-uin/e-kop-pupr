<?php

namespace App\Enums;

enum FrekuensiSimpanan: string
{
    case Sekali = 'sekali';
    case Bulanan = 'bulanan';
    case Bebas = 'bebas';
    case PerPinjaman = 'per_pinjaman';

    public function label(): string
    {
        return match ($this) {
            self::Sekali => 'Sekali (saat daftar)',
            self::Bulanan => 'Bulanan',
            self::Bebas => 'Bebas',
            self::PerPinjaman => 'Per Pinjaman (otomatis)',
        };
    }
}
