<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('piutang_eksternal', function (Blueprint $table) {
            if (Schema::hasColumn('piutang_eksternal', 'sumber_dana_id')) {
                $table->dropConstrainedForeignId('sumber_dana_id');
            }
            if (!Schema::hasColumn('piutang_eksternal', 'sumber_dana')) {
                $table->string('sumber_dana', 20)->nullable()->after('kategori_peminjam');
            }
        });

        Schema::table('pembayaran_piutang_eksternal', function (Blueprint $table) {
            if (Schema::hasColumn('pembayaran_piutang_eksternal', 'sumber_dana_id')) {
                $table->dropConstrainedForeignId('sumber_dana_id');
            }
            if (!Schema::hasColumn('pembayaran_piutang_eksternal', 'sumber_dana')) {
                $table->string('sumber_dana', 20)->nullable()->after('nominal');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pembayaran_piutang_eksternal', function (Blueprint $table) {
            if (Schema::hasColumn('pembayaran_piutang_eksternal', 'sumber_dana')) {
                $table->dropColumn('sumber_dana');
            }
        });

        Schema::table('piutang_eksternal', function (Blueprint $table) {
            if (Schema::hasColumn('piutang_eksternal', 'sumber_dana')) {
                $table->dropColumn('sumber_dana');
            }
        });
    }
};
