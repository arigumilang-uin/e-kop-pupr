<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Perbarui tabel-tabel keuangan untuk mendukung sistem Void/Reversal.
     *
     * 1. simpanan: tambah kolom `status` (aktif/voided) + hapus unique constraint
     * 2. angsuran: update enum `status` untuk menambah 'voided'
     * 3. pengeluaran_kas: tambah kolom `status` (aktif/voided)
     */
    public function up(): void
    {
        // === SIMPANAN ===
        if (!Schema::hasColumn('simpanan', 'status')) {
            Schema::table('simpanan', function (Blueprint $table) {
                $table->enum('status', ['aktif', 'voided'])->default('aktif')->after('keterangan')->index();
            });
        }

        // Hapus unique constraint yang menghalangi void + re-entry.
        // MySQL tidak bisa drop unique index yang juga dipakai FK,
        // jadi kita disable FK checks sementara.
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            Schema::table('simpanan', function (Blueprint $table) {
                $table->dropUnique('unique_wajib_bulanan');
            });
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        } catch (\Exception $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            // Index sudah tidak ada (dari run sebelumnya), lanjutkan
        }

        // === ANGSURAN ===
        DB::statement("ALTER TABLE `angsuran` MODIFY COLUMN `status` ENUM('belum', 'lunas', 'voided') NOT NULL DEFAULT 'belum'");

        // === PENGELUARAN KAS ===
        if (!Schema::hasColumn('pengeluaran_kas', 'status')) {
            Schema::table('pengeluaran_kas', function (Blueprint $table) {
                $table->enum('status', ['aktif', 'voided'])->default('aktif')->after('keterangan')->index();
            });
        }
    }

    public function down(): void
    {
        Schema::table('pengeluaran_kas', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        DB::statement("ALTER TABLE `angsuran` MODIFY COLUMN `status` ENUM('belum', 'lunas') NOT NULL DEFAULT 'belum'");

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Schema::table('simpanan', function (Blueprint $table) {
            $table->unique(['anggota_id', 'jenis_simpanan_id', 'bulan_untuk', 'tahun_untuk'], 'unique_wajib_bulanan');
        });
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        Schema::table('simpanan', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
