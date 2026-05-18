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
        // Add sumber_dana to pengeluaran_kas
        Schema::table('pengeluaran_kas', function (Blueprint $table) {
            $table->enum('sumber_dana', ['brk', 'kas'])->default('brk')->after('nominal');
        });

        // Create mutasi_rekening table
        Schema::create('mutasi_rekening', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->enum('jenis_mutasi', ['brk_ke_kas', 'kas_ke_brk']);
            $table->decimal('nominal', 15, 2);
            $table->string('keterangan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mutasi_rekening');
        
        Schema::table('pengeluaran_kas', function (Blueprint $table) {
            $table->dropColumn('sumber_dana');
        });
    }
};
