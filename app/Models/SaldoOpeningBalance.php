<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Saldo Opening Balance — saldo statis historis untuk Neraca.
 *
 * Tabel ini menyimpan data akumulasi saldo dari tahun-tahun sebelumnya
 * yang tidak bisa dihitung otomatis oleh sistem karena transaksi masa lalunya
 * tidak tersimpan di database.
 *
 * Contoh: Dana Pendidikan, Dana Sosial, Cadangan, Donasi, Hutang Pajak.
 */
class SaldoOpeningBalance extends Model
{
    protected $table = 'saldo_opening_balance';

    protected $fillable = [
        'kode_akun',
        'nama_akun',
        'posisi_neraca',
        'sub_kategori',
        'nominal',
        'sisi',
        'tahun_buku',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'nominal' => 'decimal:2',
            'tahun_buku' => 'integer',
        ];
    }

    // === Scopes ===

    public function scopePosisi($query, string $posisi)
    {
        return $query->where('posisi_neraca', $posisi);
    }

    public function scopeTahun($query, int $tahun)
    {
        return $query->where('tahun_buku', $tahun);
    }

    public function scopeDebit($query)
    {
        return $query->where('sisi', 'debit');
    }

    public function scopeKredit($query)
    {
        return $query->where('sisi', 'kredit');
    }

    // === Query helpers para NeracaService ===

    /**
     * Ambil semua saldo opening balance untuk posisi neraca tertentu.
     */
    public static function saldoByPosisi(string $posisi, int $tahun = 2025): \Illuminate\Database\Eloquent\Collection
    {
        return static::posisi($posisi)->tahun($tahun)->get();
    }

    /**
     * Ambil saldo tunggal berdasarkan kode akun.
     */
    public static function saldoAkun(string $kodeAkun, int $tahun = 2025): float
    {
        return (float) static::where('kode_akun', $kodeAkun)
            ->where('tahun_buku', $tahun)
            ->value('nominal') ?? 0;
    }
}
