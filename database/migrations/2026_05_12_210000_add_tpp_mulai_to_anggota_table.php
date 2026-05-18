<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('anggota', function (Blueprint $table) {
            $table->tinyInteger('tpp_mulai_pokok')->nullable()->after('tanggal_masuk')
                  ->comment('Bulan (1-12) pertama potongan TPP simpanan pokok');
            $table->smallInteger('tpp_tahun_pokok')->nullable()->after('tpp_mulai_pokok')
                  ->comment('Tahun pertama potongan TPP simpanan pokok');
            $table->tinyInteger('tpp_mulai_wajib')->nullable()->after('tpp_tahun_pokok')
                  ->comment('Bulan (1-12) pertama potongan TPP simpanan wajib');
            $table->smallInteger('tpp_tahun_wajib')->nullable()->after('tpp_mulai_wajib')
                  ->comment('Tahun pertama potongan TPP simpanan wajib');
        });
    }

    public function down(): void
    {
        Schema::table('anggota', function (Blueprint $table) {
            $table->dropColumn(['tpp_mulai_pokok', 'tpp_tahun_pokok', 'tpp_mulai_wajib', 'tpp_tahun_wajib']);
        });
    }
};
