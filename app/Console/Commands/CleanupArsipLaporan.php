<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('arsip:cleanup {--days=30}')]
#[Description('Membersihkan arsip laporan fisik dan database yang sudah kadaluarsa (tidak dipin)')]
class CleanupArsipLaporan extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        $this->info("Memulai pembersihan arsip laporan sementara yang lebih tua dari {$days} hari...");

        $cutoffDate = now()->subDays($days);

        $arsipKadaluarsa = \App\Models\ArsipLaporan::where('is_permanent', false)
                            ->where('created_at', '<', $cutoffDate)
                            ->get();

        if ($arsipKadaluarsa->isEmpty()) {
            $this->info("Tidak ada arsip kadaluarsa yang ditemukan.");
            return;
        }

        $count = 0;
        foreach ($arsipKadaluarsa as $arsip) {
            if (\Illuminate\Support\Facades\Storage::exists($arsip->file_path)) {
                \Illuminate\Support\Facades\Storage::delete($arsip->file_path);
            }
            $arsip->delete();
            $count++;
        }

        $this->info("Berhasil membersihkan {$count} file arsip laporan beserta data di database.");

        if ($count > 0) {
            $logService = app(\App\Services\ActivityLogService::class);
            // using a system user context, or no user (userId = null)
            $logService->log(
                'arsip_auto_prune',
                "Sistem membersihkan {$count} file arsip laporan sementara yang berumur lebih dari {$days} hari secara otomatis.",
                dataBaru: ['jumlah_dihapus' => $count]
            );
        }
    }
}
