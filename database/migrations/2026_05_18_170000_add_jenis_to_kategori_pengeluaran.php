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
        Schema::table('kategori_pengeluaran', function (Blueprint $table) {
            $table->enum('jenis', ['beban', 'aset'])->default('beban')->after('nama');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kategori_pengeluaran', function (Blueprint $table) {
            $table->dropColumn('jenis');
        });
    }
};
