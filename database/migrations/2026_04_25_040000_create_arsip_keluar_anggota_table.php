<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel arsip keluarnya anggota — menyimpan riwayat lengkap keanggotaan
        Schema::create('arsip_keluar_anggota', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anggota_id')->constrained('anggota')->cascadeOnUpdate();
            $table->date('tanggal_keluar');
            $table->decimal('total_simpanan_dikembalikan', 15, 2)->comment('Grand total simpanan yg dikembalikan');
            $table->json('rincian_simpanan')->comment('Detail per jenis simpanan {kode, nama, nominal}');
            $table->decimal('nominal_wajib_setor_ulang', 15, 2)->comment('Nominal yg wajib disetor jika ingin daftar ulang');
            $table->text('catatan')->nullable();
            $table->foreignId('diproses_oleh')->constrained('users')->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('arsip_keluar_anggota');
    }
};
