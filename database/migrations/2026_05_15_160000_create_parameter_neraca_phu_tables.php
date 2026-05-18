<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Dynamic Parameter System:
 * - parameter_neraca: setiap baris = satu line item di laporan Neraca
 * - parameter_phu: setiap baris = satu pos Pendapatan atau Beban di PHU
 * - Drop saldo_opening_balance (digantikan oleh parameter_neraca)
 */
return new class extends Migration
{
    public function up(): void
    {
        // =====================================================================
        // 1. Tabel parameter_neraca
        // =====================================================================
        Schema::create('parameter_neraca', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 150)->comment('Nama yang tampil di laporan');
            $table->enum('posisi', [
                'aktiva_lancar', 'penyertaan', 'harta_tetap', 'harta_lain',
                'kewajiban_pendek', 'kewajiban_panjang', 'modal',
            ])->index()->comment('Kelompok posisi di Neraca');
            $table->enum('sumber_data', ['manual', 'otomatis'])->default('manual');
            $table->string('kode_otomatis', 50)->nullable()->unique()
                  ->comment('Key resolver jika sumber_data=otomatis');
            $table->decimal('nominal_manual', 15, 2)->default(0)
                  ->comment('Nilai jika sumber_data=manual');
            $table->unsignedSmallInteger('urutan')->default(0)
                  ->comment('Urutan tampil dalam section');
            $table->boolean('is_pengurang')->default(false)
                  ->comment('True = pengurang (misal Ak. Penyusutan)');
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('tahun_buku')->index();
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->index(['posisi', 'tahun_buku', 'is_active']);
        });

        // =====================================================================
        // 2. Tabel parameter_phu
        // =====================================================================
        Schema::create('parameter_phu', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 150)->comment('Nama pos, misal: Pendapatan Jasa Simpan Pinjam');
            $table->enum('tipe', ['pendapatan', 'beban'])->index();
            $table->enum('sumber_data', ['manual', 'otomatis'])->default('manual');
            $table->string('kode_otomatis', 50)->nullable()->unique();
            $table->decimal('nominal_manual', 15, 2)->default(0);
            $table->unsignedSmallInteger('urutan')->default(0);
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('tahun_buku')->index();
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->index(['tipe', 'tahun_buku', 'is_active']);
        });

        // =====================================================================
        // 3. Drop saldo_opening_balance (digantikan oleh parameter_neraca)
        // =====================================================================
        Schema::dropIfExists('saldo_opening_balance');
    }

    public function down(): void
    {
        Schema::dropIfExists('parameter_phu');
        Schema::dropIfExists('parameter_neraca');

        // Recreate saldo_opening_balance (rollback)
        Schema::create('saldo_opening_balance', function (Blueprint $table) {
            $table->id();
            $table->string('kode_akun', 20)->unique();
            $table->string('nama_akun', 100);
            $table->string('posisi_neraca', 20);
            $table->string('sub_kategori', 50)->nullable();
            $table->decimal('nominal', 15, 2)->default(0);
            $table->enum('sisi', ['debit', 'kredit'])->default('debit');
            $table->unsignedSmallInteger('tahun_buku');
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->index(['posisi_neraca', 'tahun_buku']);
        });
    }
};
