<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE pinjaman MODIFY COLUMN status ENUM('menunggu', 'disetujui', 'ditolak', 'dibatalkan', 'berjalan', 'lunas') DEFAULT 'menunggu'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE pinjaman MODIFY COLUMN status ENUM('menunggu', 'disetujui', 'ditolak', 'berjalan', 'lunas') DEFAULT 'menunggu'");
    }
};
