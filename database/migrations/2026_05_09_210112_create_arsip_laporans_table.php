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
        Schema::create('arsip_laporan', function (Blueprint $table) {
            $table->id();
            $table->string('tipe_laporan', 50); // e.g. Potongan TPP, Simpanan, Pinjaman
            $table->string('format', 10); // PDF, EXCEL
            $table->string('nama_file');
            $table->string('file_path');
            $table->string('data_hash', 64)->index(); // MD5/SHA256 of data & format to avoid duplicates
            $table->text('filter_info')->nullable(); // Human-readable applied filters (for UI)
            $table->foreignId('dibuat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('arsip_laporan');
    }
};
