<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('piutang_eksternal', function (Blueprint $table) {
            $table->id();
            $table->string('no_referensi')->unique();
            $table->string('nama_peminjam');
            $table->string('jabatan_peminjam')->nullable();
            $table->string('kategori_peminjam')->default('pengurus');
            $table->unsignedSmallInteger('tahun_pinjam');
            $table->decimal('nominal_awal', 15, 2);
            $table->decimal('nominal_terbayar', 15, 2)->default(0);
            $table->decimal('sisa_piutang', 15, 2);
            $table->text('keterangan')->nullable();
            $table->string('status')->default('aktif'); // aktif, lunas
            $table->date('tanggal_catat');
            $table->foreignId('dicatat_oleh')->constrained('users');
            $table->timestamps();
        });

        Schema::create('pembayaran_piutang_eksternal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('piutang_eksternal_id')->constrained('piutang_eksternal')->cascadeOnDelete();
            $table->decimal('nominal', 15, 2);
            $table->date('tanggal_bayar');
            $table->string('bukti_bayar')->nullable();
            $table->text('keterangan')->nullable();
            $table->foreignId('dicatat_oleh')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran_piutang_eksternal');
        Schema::dropIfExists('piutang_eksternal');
    }
};
