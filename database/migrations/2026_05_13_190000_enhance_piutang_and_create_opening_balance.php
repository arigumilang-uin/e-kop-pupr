<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration GAP-2 & GAP-3:
 * 1. Enhance tabel piutang_eksternal → tambah kategori_piutang & bidang
 * 2. Buat tabel saldo_opening_balance → saldo statis historis untuk Neraca
 */
return new class extends Migration
{
    public function up(): void
    {
        // =====================================================================
        // 1. Enhance piutang_eksternal — tambah kolom untuk multi-kategori
        // =====================================================================
        Schema::table('piutang_eksternal', function (Blueprint $table) {
            $table->string('kategori_piutang', 30)
                  ->default('pengurus_berjalan')
                  ->after('kategori_peminjam')
                  ->comment('Enum KategoriPiutangLegacy: pengurus_berjalan, pengurus_lama, meninggal, sisa_pengurus, pihak_ketiga');

            $table->string('bidang', 100)
                  ->nullable()
                  ->after('kategori_piutang')
                  ->comment('Bidang/instansi asal: Bina Marga, PKP, Sekretariat, dll');

            $table->string('periode_pengurus', 20)
                  ->nullable()
                  ->after('bidang')
                  ->comment('Periode pengurus: 2019-2025, 2016-2019, dll');
        });

        // =====================================================================
        // 2. Tabel saldo_opening_balance — saldo statis historis
        //    Digunakan agar NeracaService bisa memasukkan angka-angka legacy
        //    (Dana Pendidikan, Cadangan, dll) tanpa merusak Ledger 2026.
        // =====================================================================
        Schema::create('saldo_opening_balance', function (Blueprint $table) {
            $table->id();
            $table->string('kode_akun', 20)->unique()
                  ->comment('Kode identifikasi akun, misal: KAS_TUNAI, BANK_BRK, DANA_PENDIDIKAN');
            $table->string('nama_akun', 100)
                  ->comment('Nama akun human-readable');
            $table->string('posisi_neraca', 20)
                  ->comment('Posisi di Neraca: aktiva_lancar, penyertaan, harta_tetap, kewajiban_pendek, kewajiban_panjang, modal');
            $table->string('sub_kategori', 50)->nullable()
                  ->comment('Sub-kategori opsional untuk grouping');
            $table->decimal('nominal', 15, 2)->default(0);
            $table->enum('sisi', ['debit', 'kredit'])->default('debit')
                  ->comment('Sisi normal akun: debit=aset, kredit=kewajiban/modal');
            $table->unsignedSmallInteger('tahun_buku')
                  ->comment('Tahun buku referensi');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->index(['posisi_neraca', 'tahun_buku']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saldo_opening_balance');

        Schema::table('piutang_eksternal', function (Blueprint $table) {
            $table->dropColumn(['kategori_piutang', 'bidang', 'periode_pengurus']);
        });
    }
};
