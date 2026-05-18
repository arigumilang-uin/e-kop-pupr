<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('periode_pinjaman', function (Blueprint $table) {
            $table->tinyInteger('bulan_potongan_awal')->after('batas_bulan_pelunasan')
                  ->comment('Bulan pertama potongan TPP pinjaman (1-12)');
            $table->tinyInteger('bulan_potongan_akhir')->after('bulan_potongan_awal')
                  ->comment('Bulan terakhir potongan TPP pinjaman (1-12)');
        });

        // Drop fields yang tidak lagi digunakan
        Schema::table('periode_pinjaman', function (Blueprint $table) {
            $table->dropColumn(['batas_bulan_pelunasan', 'angsuran_bulan_berjalan']);
        });
    }

    public function down(): void
    {
        Schema::table('periode_pinjaman', function (Blueprint $table) {
            $table->tinyInteger('batas_bulan_pelunasan')->default(11)->after('tanggal_tutup');
            $table->boolean('angsuran_bulan_berjalan')->default(false)->after('batas_bulan_pelunasan');
        });

        Schema::table('periode_pinjaman', function (Blueprint $table) {
            $table->dropColumn(['bulan_potongan_awal', 'bulan_potongan_akhir']);
        });
    }
};
