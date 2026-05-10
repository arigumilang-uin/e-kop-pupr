<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel Ledger — Jurnal Umum Koperasi (Immutable Append-Only).
     *
     * Setiap transaksi keuangan (simpanan, angsuran, pencairan, pengeluaran, void)
     * dicatat sebagai baris baru di sini. Record TIDAK BOLEH di-UPDATE atau di-DELETE.
     *
     * Saldo Koperasi = SUM(nominal) WHERE tipe='kredit' - SUM(nominal) WHERE tipe='debit'
     */
    public function up(): void
    {
        Schema::create('ledger', function (Blueprint $table) {
            $table->id();
            $table->string('no_referensi', 24)->unique()->comment('LDG-YYYY-NNNNNN');

            // Tipe: kredit (dana masuk) atau debit (dana keluar)
            $table->enum('tipe', ['kredit', 'debit'])->index();

            // Kategori transaksi
            $table->enum('kategori', [
                'simpanan', 'angsuran', 'pencairan', 'penarikan',
                'pengeluaran', 'void', 'koreksi', 'lainnya',
            ])->index();

            // Nominal SELALU POSITIF. Arah ditentukan oleh `tipe`.
            $table->decimal('nominal', 15, 2)->comment('Selalu positif');

            $table->string('deskripsi', 500);

            // Polymorphic reference ke tabel asal (simpanan, angsuran, pinjaman, dll)
            $table->unsignedBigInteger('transaksi_ref_id')->nullable()->comment('ID di tabel asal');
            $table->string('transaksi_ref_type', 100)->nullable()->comment('Model class tabel asal');

            // Self-referencing: jika entry ini adalah void/reversal dari entry lain
            $table->foreignId('void_of_id')->nullable()
                  ->constrained('ledger')->cascadeOnUpdate()->nullOnDelete()
                  ->comment('Merujuk ke ledger.id yang di-void');

            // Anggota terkait (nullable, karena pengeluaran kas tidak selalu terkait anggota)
            $table->foreignId('anggota_id')->nullable()
                  ->constrained('anggota')->cascadeOnUpdate()->nullOnDelete();

            $table->date('tanggal_efektif')->index()->comment('Tanggal transaksi berlaku');

            $table->foreignId('dicatat_oleh')->nullable()
                  ->constrained('users')->cascadeOnUpdate()->nullOnDelete();

            $table->timestamp('created_at')->useCurrent();

            // Indexes untuk performa query saldo
            $table->index(['tipe', 'kategori']);
            $table->index(['anggota_id', 'tipe']);
            $table->index(['transaksi_ref_type', 'transaksi_ref_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ledger');
    }
};
