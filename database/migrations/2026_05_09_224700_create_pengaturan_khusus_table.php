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
        Schema::create('pengaturan_khusus', function (Blueprint $table) {
            $table->id();
            $table->string('key', 50)->index();
            $table->integer('bulan');
            $table->integer('tahun');
            $table->string('value', 255);
            $table->timestamps();

            $table->unique(['key', 'bulan', 'tahun'], 'pengaturan_khusus_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengaturan_khusus');
    }
};
