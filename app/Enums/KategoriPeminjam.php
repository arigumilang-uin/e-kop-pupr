<?php

namespace App\Enums;

enum KategoriPeminjam: string
{
    case Anggota = 'anggota';
    case Pengurus = 'pengurus';
    case PihakKetiga = 'pihak_ketiga';
    case Instansi = 'instansi';
    case Lainnya = 'lainnya';

    public function label(): string
    {
        return match ($this) {
            self::Anggota => 'Anggota (Periode Lalu)',
            self::Pengurus => 'Pengurus',
            self::PihakKetiga => 'Pihak Ketiga',
            self::Instansi => 'Instansi',
            self::Lainnya => 'Lainnya',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Anggota => 'emerald',
            self::Pengurus => 'amber',
            self::PihakKetiga => 'blue',
            self::Instansi => 'violet',
            self::Lainnya => 'stone',
        };
    }
}
