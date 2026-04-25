<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel komponen pendapatan & beban SHU — CRUD oleh pengurus
        Schema::create('shu_komponen', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100)->comment('Nama komponen, misal: Pendapatan Bunga Pinjaman');
            $table->enum('tipe', ['pendapatan', 'beban'])->comment('Masuk sebagai pendapatan atau beban');
            $table->string('sumber_data', 50)->comment('Kunci sumber data otomatis dari sistem');
            $table->text('deskripsi')->nullable();
            $table->boolean('is_aktif')->default(true);
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });

        // Tabel distribusi/alokasi SHU — CRUD oleh pengurus
        Schema::create('shu_distribusi', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100)->comment('Nama alokasi, misal: Jasa Modal');
            $table->decimal('persen', 5, 2)->comment('Persentase alokasi');
            $table->text('deskripsi')->nullable();
            $table->boolean('is_aktif')->default(true);
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shu_distribusi');
        Schema::dropIfExists('shu_komponen');
    }
};
