<?php

namespace App\Traits;

/**
 * Trait untuk model yang memiliki nomor referensi otomatis.
 * Format: {PREFIX}-{YYYY}-{NNNN}
 *
 * Cara pakai di Model:
 * use HasNoReferensi;
 * protected static string $refPrefix = 'PJM'; // SIM, TRK, PJM
 */
trait HasNoReferensi
{
    public static function bootHasNoReferensi(): void
    {
        static::creating(function ($model) {
            if (empty($model->no_referensi)) {
                $model->no_referensi = static::generateNoReferensi($model);
            }
        });
    }

    /**
     * Generate nomor referensi unik: PREFIX-YYYY-NNNN
     */
    public static function generateNoReferensi($model = null): string
    {
        $prefix = static::$refPrefix;
        
        $tahun = now()->format('Y');
        
        if ($model) {
            if (!empty($model->tanggal)) {
                $tahun = \Carbon\Carbon::parse($model->tanggal)->format('Y');
            } elseif (!empty($model->tanggal_pengajuan)) {
                $tahun = \Carbon\Carbon::parse($model->tanggal_pengajuan)->format('Y');
            }
        }

        $pattern = "{$prefix}-{$tahun}-";

        $lastNumber = static::where('no_referensi', 'like', "{$pattern}%")
            ->orderByDesc('no_referensi')
            ->value('no_referensi');

        if ($lastNumber) {
            $lastSeq = (int) substr($lastNumber, -4);
            $nextSeq = $lastSeq + 1;
        } else {
            $nextSeq = 1;
        }

        return $pattern . str_pad($nextSeq, 4, '0', STR_PAD_LEFT);
    }
}
