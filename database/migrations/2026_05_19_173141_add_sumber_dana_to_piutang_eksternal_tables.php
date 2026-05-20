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
        Schema::table('piutang_eksternal', function (Blueprint $table) {
            $table->foreignId('sumber_dana_id')->nullable()->after('kategori_peminjam')->constrained('kategori_pengeluaran')->nullOnDelete();
        });

        Schema::table('pembayaran_piutang_eksternal', function (Blueprint $table) {
            $table->foreignId('sumber_dana_id')->nullable()->after('nominal')->constrained('kategori_pengeluaran')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pembayaran_piutang_eksternal', function (Blueprint $table) {
            $table->dropConstrainedForeignId('sumber_dana_id');
        });

        Schema::table('piutang_eksternal', function (Blueprint $table) {
            $table->dropConstrainedForeignId('sumber_dana_id');
        });
    }
};
