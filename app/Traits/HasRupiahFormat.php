<?php

namespace App\Traits;

/**
 * Trait untuk model yang memiliki format mata uang Rupiah.
 * Menyediakan method formatRupiah() untuk kolom decimal.
 */
trait HasRupiahFormat
{
    /**
     * Format angka ke Rupiah.
     */
    public function formatRupiah(string $column): string
    {
        $value = $this->{$column} ?? 0;
        return 'Rp ' . number_format((float) $value, 0, ',', '.');
    }
}
