<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('parameter_neraca', function (Blueprint $table) {
            $table->json('konfigurasi')->nullable()->after('kode_otomatis')->comment('Digunakan sebagai filter dinamis resolver, contoh: {"kategori": "...", "tahun": "..."}');
        });

        Schema::table('parameter_phu', function (Blueprint $table) {
            $table->json('konfigurasi')->nullable()->after('kode_otomatis')->comment('Digunakan sebagai filter dinamis resolver, contoh: {"pos": "...", "referensi": "..."}');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parameter_neraca', function (Blueprint $table) {
            $table->dropColumn('konfigurasi');
        });

        Schema::table('parameter_phu', function (Blueprint $table) {
            $table->dropColumn('konfigurasi');
        });
    }
};
