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
        // 1. Bidang — Master data bagian/bidang di Dinas PUPR
        Schema::create('bidang', function (Blueprint $table) {
            $table->id();
            $table->string('nama_bidang', 100);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // 2. Anggota — Data anggota koperasi (untuk pencocokan NIP, bukan login)
        Schema::create('anggota', function (Blueprint $table) {
            $table->id();
            $table->string('nip', 20)->unique()->comment('NIP — identitas unik, pencocokan form guest');
            $table->string('nama', 100);
            $table->string('golongan', 10)->comment('Golongan PNS');
            $table->string('jabatan', 100)->nullable();
            $table->foreignId('bidang_id')->constrained('bidang')->cascadeOnUpdate();
            $table->text('alamat')->nullable();
            $table->string('no_hp', 15)->nullable();
            $table->date('tanggal_masuk');
            $table->date('tanggal_keluar')->nullable()->comment('NULL = masih aktif');
            $table->boolean('is_pendaftar_ulang')->default(false);
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();
        });

        // 3. Users — Hanya untuk Admin/Pengurus dan Pimpinan/Kepala
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->string('username', 50)->unique();
            $table->string('password')->comment('Bcrypt hash');
            $table->tinyInteger('failed_login_attempts')->unsigned()->default(0);
            $table->dateTime('locked_until')->nullable();
            $table->dateTime('last_login_at')->nullable();
            $table->string('last_login_ip', 45)->nullable();
            $table->enum('role', ['admin', 'pimpinan'])->default('admin');
            $table->rememberToken();
            $table->timestamps();
        });

        // Sessions — untuk session driver database (Laravel default)
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        // Password reset tokens (Laravel default, untuk admin)
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('username')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('users');
        Schema::dropIfExists('anggota');
        Schema::dropIfExists('bidang');
    }
};
