<?php

use Illuminate\Support\Number;

if (!function_exists('format_rupiah')) {
    /**
     * Format angka ke Rupiah: Rp 1.000.000
     */
    function format_rupiah(float|int|string|null $value): string
    {
        $value = (float) ($value ?? 0);
        return 'Rp ' . number_format($value, 0, ',', '.');
    }
}

if (!function_exists('parse_rupiah')) {
    /**
     * Parse string Rupiah ke float: "Rp 1.000.000" → 1000000
     */
    function parse_rupiah(string $value): float
    {
        $cleaned = preg_replace('/[^0-9,]/', '', $value);
        return (float) str_replace(',', '.', $cleaned);
    }
}

if (!function_exists('format_persen')) {
    /**
     * Format angka ke persen: 15%
     */
    function format_persen(float|int|string|null $value, int $decimals = 1): string
    {
        $value = (float) ($value ?? 0);
        $formatted = rtrim(rtrim(number_format($value, $decimals, ',', '.'), '0'), ',');
        return $formatted . '%';
    }
}

if (!function_exists('format_bulan_tahun')) {
    /**
     * Format bulan dan tahun: "April 2026"
     */
    function format_bulan_tahun(int $bulan, int $tahun): string
    {
        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        return ($namaBulan[$bulan] ?? $bulan) . ' ' . $tahun;
    }
}

if (!function_exists('nama_bulan')) {
    /**
     * Nama bulan Indonesia: 1 → "Januari"
     */
    function nama_bulan(int $bulan): string
    {
        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        return $namaBulan[$bulan] ?? (string) $bulan;
    }
}
