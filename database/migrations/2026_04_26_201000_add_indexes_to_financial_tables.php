<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambahkan index pada kolom yang sering digunakan
     * untuk WHERE, ORDER BY, dan filter di laporan keuangan.
     */
    public function up(): void
    {
        // pinjaman — sering difilter berdasar status & tanggal
        Schema::table('pinjaman', function (Blueprint $table) {
            $table->index('status');
            $table->index('tanggal_pengajuan');
            $table->index('tanggal_approval');
        });

        // angsuran — sering difilter berdasar status & tanggal bayar
        Schema::table('angsuran', function (Blueprint $table) {
            $table->index('status');
            $table->index('tanggal_bayar');
            $table->index('tanggal_jatuh_tempo');
        });

        // simpanan — sering difilter berdasar tanggal & bulan
        Schema::table('simpanan', function (Blueprint $table) {
            $table->index('tanggal');
            $table->index(['bulan_untuk', 'tahun_untuk']);
        });

        // penarikan_simpanan — filter arsip berdasar tanggal
        Schema::table('penarikan_simpanan', function (Blueprint $table) {
            $table->index('tanggal');
        });

        // pengeluaran_kas — filter arsip berdasar tanggal
        Schema::table('pengeluaran_kas', function (Blueprint $table) {
            $table->index('tanggal');
        });

        // anggota — sering difilter berdasar status
        Schema::table('anggota', function (Blueprint $table) {
            $table->index('status');
        });

        // potongan_bulanan — filter berdasar bulan/tahun
        Schema::table('potongan_bulanan', function (Blueprint $table) {
            $table->index(['bulan', 'tahun']);
        });
    }

    public function down(): void
    {
        Schema::table('pinjaman', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['tanggal_pengajuan']);
            $table->dropIndex(['tanggal_approval']);
        });

        Schema::table('angsuran', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['tanggal_bayar']);
            $table->dropIndex(['tanggal_jatuh_tempo']);
        });

        Schema::table('simpanan', function (Blueprint $table) {
            $table->dropIndex(['tanggal']);
            $table->dropIndex(['bulan_untuk', 'tahun_untuk']);
        });

        Schema::table('penarikan_simpanan', function (Blueprint $table) {
            $table->dropIndex(['tanggal']);
        });

        Schema::table('pengeluaran_kas', function (Blueprint $table) {
            $table->dropIndex(['tanggal']);
        });

        Schema::table('anggota', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });

        Schema::table('potongan_bulanan', function (Blueprint $table) {
            $table->dropIndex(['bulan', 'tahun']);
        });
    }
};
