<?php

namespace App\Console\Commands;

use App\Enums\StatusPeriode;
use App\Models\PeriodePinjaman;
use Illuminate\Console\Command;

class SinkronStatusPeriode extends Command
{
    protected $signature = 'periode:sinkron-status';

    protected $description = 'Sinkronkan status periode pinjaman berdasarkan tanggal hari ini (Terjadwal → Buka → Tutup)';

    public function handle(): int
    {
        $today = now()->startOfDay();
        $updated = 0;

        // 1. Terjadwal → Buka (tanggal_buka sudah tiba)
        $toBuka = PeriodePinjaman::where('status', StatusPeriode::Terjadwal)
            ->whereDate('tanggal_buka', '<=', $today)
            ->whereDate('tanggal_tutup', '>=', $today)
            ->get();

        foreach ($toBuka as $periode) {
            $periode->update(['status' => StatusPeriode::Buka]);
            $this->info("→ BUKA: {$periode->nama_periode}");
            $updated++;
        }

        // 2. Buka/Terjadwal → Tutup (tanggal_tutup sudah lewat)
        $toTutup = PeriodePinjaman::whereIn('status', [StatusPeriode::Buka, StatusPeriode::Terjadwal])
            ->whereDate('tanggal_tutup', '<', $today)
            ->get();

        foreach ($toTutup as $periode) {
            $periode->update(['status' => StatusPeriode::Tutup]);
            $this->info("→ TUTUP: {$periode->nama_periode}");
            $updated++;
        }

        if ($updated === 0) {
            $this->info('Semua status periode sudah sinkron.');
        } else {
            $this->info("Total {$updated} periode diperbarui.");
        }

        return self::SUCCESS;
    }
}
