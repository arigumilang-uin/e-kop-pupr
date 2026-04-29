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
        Schema::table('periode_pinjaman', function (Blueprint $table) {
            $table->decimal('nominal_min', 15, 2)->default(100000)->after('limit_per_anggota')
                  ->comment('Nominal pinjaman minimum');
            $table->decimal('kelipatan_nominal', 15, 2)->default(100000)->after('nominal_min')
                  ->comment('Kelipatan nominal pinjaman (step)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('periode_pinjaman', function (Blueprint $table) {
            $table->dropColumn(['nominal_min', 'kelipatan_nominal']);
        });
    }
};
