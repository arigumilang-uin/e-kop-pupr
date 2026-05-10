<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel Void Requests — Workflow persetujuan void transaksi.
     *
     * Alur: Pengurus request void → Pimpinan approve/reject.
     * Semua void wajib melalui persetujuan (tidak ada auto-approve).
     */
    public function up(): void
    {
        Schema::create('void_requests', function (Blueprint $table) {
            $table->id();

            // Referensi ke ledger entry yang ingin di-void
            $table->foreignId('ledger_id')
                  ->constrained('ledger')->cascadeOnUpdate()
                  ->comment('Entry ledger yang diminta void');

            // Alasan wajib diisi oleh Pengurus (Maker)
            $table->text('alasan')->comment('Wajib diisi oleh pemohon');

            // Status approval
            $table->enum('status', ['menunggu', 'disetujui', 'ditolak'])
                  ->default('menunggu')->index();

            // Maker: siapa yang mengajukan void
            $table->foreignId('diminta_oleh')
                  ->constrained('users')->cascadeOnUpdate()
                  ->comment('Pengurus yang meminta void');
            $table->dateTime('tanggal_permintaan');

            // Checker: siapa yang memutuskan (Pimpinan/Super Admin)
            $table->foreignId('diputuskan_oleh')->nullable()
                  ->constrained('users')->cascadeOnUpdate()->nullOnDelete()
                  ->comment('Pimpinan yang approve/reject');
            $table->dateTime('tanggal_keputusan')->nullable();
            $table->text('catatan_keputusan')->nullable();

            // Referensi ke reversal entry yang dibuat setelah approve
            $table->foreignId('reversal_ledger_id')->nullable()
                  ->constrained('ledger')->cascadeOnUpdate()->nullOnDelete()
                  ->comment('Entry ledger reversal yang dibuat setelah void disetujui');

            $table->timestamps();

            // Prevent double void request untuk ledger yang sama
            $table->unique(['ledger_id', 'status'], 'unique_pending_void');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('void_requests');
    }
};
