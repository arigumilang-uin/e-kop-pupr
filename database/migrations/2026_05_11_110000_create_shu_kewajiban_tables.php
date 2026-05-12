<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Update tabel shu_payout
        Schema::table('shu_payout', function (Blueprint $table) {
            $table->decimal('total_cadangan', 15, 2)->default(0)->after('total_jasa_usaha')->comment('Masuk ke Ekuitas/Modal Koperasi');
            $table->decimal('total_dana_kewajiban', 15, 2)->default(0)->after('total_cadangan')->comment('Masuk ke Kewajiban Koperasi');
        });

        // 2. Buat tabel dompet kewajiban/titipan SHU
        Schema::create('shu_kewajiban', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('tahun')->comment('Tahun buku SHU');
            $table->string('nama_alokasi', 100)->comment('Misal: Dana Sosial, Dana Pendidikan');
            $table->decimal('nominal_awal', 15, 2)->comment('Nilai awal saat payout dieksekusi');
            $table->decimal('nominal_terpakai', 15, 2)->default(0)->comment('Akumulasi nilai yang sudah direalisasikan');
            $table->decimal('saldo_tersisa', 15, 2)->comment('nominal_awal - nominal_terpakai');
            $table->timestamps();

            // Bisa ada banyak alokasi di tahun yang sama
            $table->unique(['tahun', 'nama_alokasi']);
        });

        // 3. Buat tabel histori pengeluaran / realisasi kewajiban
        Schema::create('shu_realisasi_kewajiban', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shu_kewajiban_id')->constrained('shu_kewajiban')->cascadeOnDelete();
            $table->date('tanggal_realisasi');
            $table->decimal('nominal', 15, 2);
            $table->text('keterangan');
            $table->foreignId('dicatat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shu_realisasi_kewajiban');
        Schema::dropIfExists('shu_kewajiban');
        
        Schema::table('shu_payout', function (Blueprint $table) {
            $table->dropColumn(['total_cadangan', 'total_dana_kewajiban']);
        });
    }
};
