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
        Schema::create('kategori_pengeluaran', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100)->unique();
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });

        Schema::create('pengeluaran_kas', function (Blueprint $table) {
            $table->id();
            $table->string('no_referensi', 20)->unique()->comment('KLR-YYYY-NNNN');
            $table->foreignId('kategori_pengeluaran_id')->constrained('kategori_pengeluaran')->cascadeOnUpdate();
            $table->decimal('nominal', 15, 2);
            $table->date('tanggal');
            $table->text('keterangan');
            $table->foreignId('dicatat_oleh')->nullable()->constrained('users')->cascadeOnUpdate()->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengeluaran_kas');
        Schema::dropIfExists('kategori_pengeluaran');
    }
};
