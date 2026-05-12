<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Drop unique constraint yang menghalangi re-entry setelah void.
     *
     * Constraint lama: unique(anggota_id, jenis_simpanan_id, bulan_untuk, tahun_untuk)
     * Masalah: Record voided (status='voided') masih menghuni slot unique,
     *          sehingga insert simpanan baru untuk bulan/tahun yang sama gagal.
     * Solusi:  Drop constraint, ganti dengan non-unique index, validasi duplikat
     *          ditangani di PHP (PotonganService).
     */
    public function up(): void
    {
        $indexExists = DB::select(
            "SHOW INDEX FROM `simpanan` WHERE Key_name = 'unique_wajib_bulanan'"
        );

        if (!empty($indexExists)) {
            // Drop FK first because it relies on this index
            DB::statement('ALTER TABLE `simpanan` DROP FOREIGN KEY `simpanan_anggota_id_foreign`');
            DB::statement('ALTER TABLE `simpanan` DROP INDEX `unique_wajib_bulanan`');
            
            // Re-create the index as non-unique for performance
            DB::statement('CREATE INDEX `idx_wajib_bulanan` ON `simpanan` (`anggota_id`, `jenis_simpanan_id`, `bulan_untuk`, `tahun_untuk`)');
            
            // Restore FK
            DB::statement('ALTER TABLE `simpanan` ADD CONSTRAINT `simpanan_anggota_id_foreign` FOREIGN KEY (`anggota_id`) REFERENCES `anggota`(`id`) ON UPDATE CASCADE');
        }
    }

    public function down(): void
    {
        $newIndexExists = DB::select(
            "SHOW INDEX FROM `simpanan` WHERE Key_name = 'idx_wajib_bulanan'"
        );

        if (!empty($newIndexExists)) {
            DB::statement('ALTER TABLE `simpanan` DROP FOREIGN KEY `simpanan_anggota_id_foreign`');
            DB::statement('DROP INDEX `idx_wajib_bulanan` ON `simpanan`');
        }

        $oldIndexExists = DB::select(
            "SHOW INDEX FROM `simpanan` WHERE Key_name = 'unique_wajib_bulanan'"
        );

        if (empty($oldIndexExists)) {
            if (empty($newIndexExists)) {
                DB::statement('ALTER TABLE `simpanan` DROP FOREIGN KEY `simpanan_anggota_id_foreign`');
            }
            DB::statement('CREATE UNIQUE INDEX `unique_wajib_bulanan` ON `simpanan` (`anggota_id`, `jenis_simpanan_id`, `bulan_untuk`, `tahun_untuk`)');
            DB::statement('ALTER TABLE `simpanan` ADD CONSTRAINT `simpanan_anggota_id_foreign` FOREIGN KEY (`anggota_id`) REFERENCES `anggota`(`id`) ON UPDATE CASCADE');
        }
    }
};
