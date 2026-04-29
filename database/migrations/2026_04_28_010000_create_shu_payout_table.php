<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shu_payout', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('tahun')->unique()->comment('Tahun SHU yang dibagikan, unique agar tidak bisa double-execute');
            $table->decimal('total_shu_bersih', 15, 2)->comment('Nominal SHU Bersih yang dibagikan');
            $table->decimal('total_jasa_modal', 15, 2)->comment('Total dana Jasa Modal');
            $table->decimal('total_jasa_usaha', 15, 2)->comment('Total dana Jasa Usaha/Anggota');
            $table->integer('jumlah_penerima')->comment('Jumlah anggota penerima');
            $table->decimal('total_terdistribusi', 15, 2)->comment('Total rupiah yang masuk ke simpanan');
            $table->foreignId('dieksekusi_oleh')->constrained('users')->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shu_payout');
    }
};
