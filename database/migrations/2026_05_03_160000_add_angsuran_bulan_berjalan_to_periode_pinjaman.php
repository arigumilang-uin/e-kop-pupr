<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('periode_pinjaman', function (Blueprint $table) {
            $table->boolean('angsuran_bulan_berjalan')
                  ->default(false)
                  ->after('batas_bulan_pelunasan')
                  ->comment('Jika true, angsuran pertama dimulai di bulan pengajuan (tenor +1)');
        });
    }

    public function down(): void
    {
        Schema::table('periode_pinjaman', function (Blueprint $table) {
            $table->dropColumn('angsuran_bulan_berjalan');
        });
    }
};
