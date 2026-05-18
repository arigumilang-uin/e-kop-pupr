<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Tambahkan 'piutang_eksternal' ke enum kolom 'kategori' di tabel ledger.
 * Enum ini tertinggal saat ledger table pertama kali dibuat.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE `ledger` MODIFY COLUMN `kategori` ENUM(
            'simpanan', 'angsuran', 'pencairan', 'penarikan',
            'pengeluaran', 'piutang_eksternal', 'void', 'koreksi', 'lainnya'
        ) NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `ledger` MODIFY COLUMN `kategori` ENUM(
            'simpanan', 'angsuran', 'pencairan', 'penarikan',
            'pengeluaran', 'void', 'koreksi', 'lainnya'
        ) NOT NULL");
    }
};
