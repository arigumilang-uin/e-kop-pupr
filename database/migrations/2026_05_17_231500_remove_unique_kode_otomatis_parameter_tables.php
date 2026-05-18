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
            $table->dropUnique(['kode_otomatis']);
        });

        Schema::table('parameter_phu', function (Blueprint $table) {
            $table->dropUnique(['kode_otomatis']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parameter_neraca', function (Blueprint $table) {
            $table->unique('kode_otomatis');
        });

        Schema::table('parameter_phu', function (Blueprint $table) {
            $table->unique('kode_otomatis');
        });
    }
};
