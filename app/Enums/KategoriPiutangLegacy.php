<?php

namespace App\Enums;

/**
 * Kategori piutang legacy berdasarkan laporan riil Koperasi Tirta Bina Karya.
 *
 * Mapping ke sheet CSV:
 * - PengurusBerjalan → Sheet 8: PIUTANG BERJALAN 2025
 * - PengurusLama     → Sheet 7: PIUTANG PENGURUS PERIODE 2016
 * - Meninggal        → Sheet 6: PIUTANG ANGG MENINGGAL
 * - SisaPengurus     → Neraca: "Sisa Piutang Pengurus Periode 2025"
 */
enum KategoriPiutangLegacy: string
{
    case PengurusBerjalan = 'pengurus_berjalan';
    case PengurusLama = 'pengurus_lama';
    case Meninggal = 'meninggal';
    case SisaPengurus = 'sisa_pengurus';
    case PihakKetiga = 'pihak_ketiga';

    public function label(): string
    {
        return match ($this) {
            self::PengurusBerjalan => 'Piutang Pengurus Berjalan',
            self::PengurusLama => 'Piutang Pengurus Periode Lama',
            self::Meninggal => 'Piutang Anggota Meninggal',
            self::SisaPengurus => 'Sisa Piutang Pengurus',
            self::PihakKetiga => 'Piutang Pihak Ketiga',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PengurusBerjalan => 'amber',
            self::PengurusLama => 'violet',
            self::Meninggal => 'red',
            self::SisaPengurus => 'blue',
            self::PihakKetiga => 'stone',
        };
    }

    /**
     * Label pendek untuk tampilan Neraca.
     */
    public function labelNeraca(): string
    {
        return match ($this) {
            self::PengurusBerjalan => 'Piutang Anggota Periode 2025',
            self::PengurusLama => 'Piutang Anggota Periode 2018',
            self::Meninggal => 'Piutang Tak Tertagih',
            self::SisaPengurus => 'Sisa Piutang Pengurus Periode 2025',
            self::PihakKetiga => 'Piutang Pihak Ketiga',
        };
    }

    /**
     * Apakah piutang ini dihitung sebagai aset aktif di Neraca?
     * Piutang meninggal biasanya sudah diputihkan.
     */
    public function isAktifDiNeraca(): bool
    {
        return $this !== self::Meninggal;
    }
}
