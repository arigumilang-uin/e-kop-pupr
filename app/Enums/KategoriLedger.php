<?php

namespace App\Enums;

enum KategoriLedger: string
{
    case Simpanan = 'simpanan';
    case Angsuran = 'angsuran';
    case Pencairan = 'pencairan';
    case Penarikan = 'penarikan';
    case Pengeluaran = 'pengeluaran';
    case PiutangEksternal = 'piutang_eksternal';
    case Void = 'void';
    case Koreksi = 'koreksi';
    case Lainnya = 'lainnya';

    public function label(): string
    {
        return match ($this) {
            self::Simpanan => 'Simpanan',
            self::Angsuran => 'Angsuran Pinjaman',
            self::Pencairan => 'Pencairan Pinjaman',
            self::Penarikan => 'Penarikan Simpanan',
            self::Pengeluaran => 'Pengeluaran Kas',
            self::PiutangEksternal => 'Pembayaran Piutang Lain-Lain',
            self::Void => 'Void / Pembatalan',
            self::Koreksi => 'Koreksi',
            self::Lainnya => 'Lainnya',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Simpanan => 'emerald',
            self::Angsuran => 'blue',
            self::Pencairan => 'amber',
            self::Penarikan => 'orange',
            self::Pengeluaran => 'red',
            self::PiutangEksternal => 'teal',
            self::Void => 'slate',
            self::Koreksi => 'violet',
            self::Lainnya => 'stone',
        };
    }
}
