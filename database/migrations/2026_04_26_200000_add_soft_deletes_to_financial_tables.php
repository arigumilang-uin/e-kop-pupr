<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambahkan soft deletes pada tabel transaksi keuangan krusial.
     * Data keuangan TIDAK BOLEH dihapus permanen untuk keperluan audit.
     */
    public function up(): void
    {
        $tables = [
            'simpanan',
            'penarikan_simpanan',
            'pinjaman',
            'angsuran',
            'pengeluaran_kas',
            'potongan_bulanan',
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->softDeletes();
            });
        }
    }

    public function down(): void
    {
        $tables = [
            'simpanan',
            'penarikan_simpanan',
            'pinjaman',
            'angsuran',
            'pengeluaran_kas',
            'potongan_bulanan',
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->dropSoftDeletes();
            });
        }
    }
};
