<?php

namespace App\Enums;

enum StatusPinjaman: string
{
    case Menunggu = 'menunggu';
    case Disetujui = 'disetujui';
    case Ditolak = 'ditolak';
    case Dibatalkan = 'dibatalkan';
    case Berjalan = 'berjalan';
    case Lunas = 'lunas';

    public function label(): string
    {
        return match ($this) {
            self::Menunggu => 'Menunggu',
            self::Disetujui => 'Disetujui',
            self::Ditolak => 'Ditolak',
            self::Dibatalkan => 'Dibatalkan',
            self::Berjalan => 'Berjalan',
            self::Lunas => 'Lunas',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Menunggu => 'amber',
            self::Disetujui => 'blue',
            self::Ditolak => 'red',
            self::Dibatalkan => 'slate',
            self::Berjalan => 'emerald',
            self::Lunas => 'slate',
        };
    }

    /**
     * Status yang dianggap "aktif" (belum selesai/ditolak).
     */
    public static function aktif(): array
    {
        return [self::Menunggu, self::Disetujui, self::Berjalan];
    }
}
