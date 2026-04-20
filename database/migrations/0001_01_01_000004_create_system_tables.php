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
        // 11. Log Aktivitas — Audit trail seluruh aktivitas pengguna
        Schema::create('log_aktivitas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnUpdate()->nullOnDelete()
                  ->comment('NULL jika aksi oleh guest/sistem');
            $table->string('aktivitas', 50)->index();
            $table->text('deskripsi');
            $table->string('ip_address', 45)->nullable();
            $table->json('data_lama')->nullable();
            $table->json('data_baru')->nullable();
            $table->timestamp('created_at')->useCurrent()->index();
        });

        // 12. Pengaturan — Konfigurasi sistem dengan kategori + Maker-Checker
        Schema::create('pengaturan', function (Blueprint $table) {
            $table->id();
            $table->string('key', 50)->unique();
            $table->string('value', 255);
            $table->string('deskripsi', 255);
            $table->enum('kategori', ['keuangan', 'teknis'])->default('keuangan');
            $table->boolean('memerlukan_persetujuan')->default(true);
            $table->timestamps();
        });

        // 13. Perubahan Pengaturan — Riwayat pengajuan perubahan (Maker-Checker)
        Schema::create('perubahan_pengaturan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengaturan_id')->constrained('pengaturan')->cascadeOnUpdate();
            $table->string('nilai_lama', 255);
            $table->string('nilai_baru', 255);
            $table->text('alasan')->comment('Wajib diisi oleh Maker');
            $table->foreignId('diajukan_oleh')->constrained('users')->cascadeOnUpdate()
                  ->comment('Admin — Maker');
            $table->dateTime('tanggal_pengajuan');
            $table->enum('status', ['menunggu', 'disetujui', 'ditolak'])->default('menunggu')->index();
            $table->foreignId('diputuskan_oleh')->nullable()->constrained('users')->cascadeOnUpdate()->nullOnDelete()
                  ->comment('Pimpinan — Checker');
            $table->dateTime('tanggal_keputusan')->nullable();
            $table->text('catatan_keputusan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perubahan_pengaturan');
        Schema::dropIfExists('pengaturan');
        Schema::dropIfExists('log_aktivitas');
    }
};
