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
        // 4. Jenis Simpanan — Pokok, Wajib, SIM2025, SWP, BONUS_SHU
        Schema::create('jenis_simpanan', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 10)->unique()->comment('POKOK, WAJIB, SIM2025, SWP, BONUS_SHU');
            $table->string('nama', 50);
            $table->decimal('nominal_default', 15, 2)->default(0);
            $table->boolean('is_wajib')->default(false);
            $table->enum('frekuensi', ['sekali', 'bulanan', 'bebas', 'per_pinjaman'])->default('bebas');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // 7. Periode Pinjaman — Pembukaan pinjaman + link guest token
        Schema::create('periode_pinjaman', function (Blueprint $table) {
            $table->id();
            $table->string('nama_periode', 100);
            $table->smallInteger('tahun');
            $table->date('tanggal_buka');
            $table->date('tanggal_tutup')->nullable()->comment('NULL = belum ditentukan');
            $table->tinyInteger('batas_bulan_pelunasan')->default(11)->comment('Default November');
            $table->decimal('limit_per_anggota', 15, 2)->comment('Maks pinjaman per anggota');
            $table->string('token', 64)->unique()->comment('Token unik untuk link pengajuan guest');
            $table->text('catatan')->nullable();
            $table->enum('status', ['buka', 'tutup', 'terjadwal'])->default('buka');
            $table->foreignId('dibuka_oleh')->constrained('users')->cascadeOnUpdate();
            $table->timestamps();
        });

        // 8. Pinjaman — Data pinjaman + bank (per pengajuan) + potongan 5%
        Schema::create('pinjaman', function (Blueprint $table) {
            $table->id();
            $table->string('no_referensi', 20)->unique()->comment('PJM-YYYY-NNNN');
            $table->foreignId('anggota_id')->constrained('anggota')->cascadeOnUpdate();
            $table->foreignId('periode_pinjaman_id')->constrained('periode_pinjaman')->cascadeOnUpdate();
            $table->tinyInteger('bulan_pengajuan')->comment('1-12');

            // Data Bank — dari form guest, per pengajuan
            $table->string('nama_bank', 100);
            $table->string('nama_rekening', 100);
            $table->string('no_rekening', 30);

            // Nominal & Bunga
            $table->decimal('nominal_pinjaman', 15, 2);
            $table->decimal('bunga_persen', 5, 2)->comment('Bunga TOTAL, default 15%');
            $table->decimal('total_bunga', 15, 2)->comment('= nominal × bunga%');
            $table->integer('tenor_bulan');

            // Angsuran (pre-calculated)
            $table->decimal('angsuran_pokok', 15, 2)->comment('= nominal ÷ tenor');
            $table->decimal('angsuran_bunga', 15, 2)->comment('= total_bunga ÷ tenor');
            $table->decimal('total_angsuran', 15, 2)->comment('Per bulan = pokok + bunga');
            $table->decimal('total_bayar', 15, 2)->comment('= nominal + total_bunga');

            // Potongan 5% di muka
            $table->decimal('potongan_swp', 15, 2)->comment('3% → simpanan SWP anggota');
            $table->decimal('potongan_dana_resiko', 15, 2)->comment('1.5%');
            $table->decimal('potongan_biaya_admin', 15, 2)->comment('0.5%');
            $table->decimal('total_potongan', 15, 2)->comment('5%');
            $table->decimal('dana_diterima', 15, 2)->comment('95% — yang diterima peminjam');

            // Status & Approval
            $table->dateTime('tanggal_pengajuan');
            $table->dateTime('tanggal_approval')->nullable();
            $table->enum('status', ['menunggu', 'disetujui', 'ditolak', 'dibatalkan', 'berjalan', 'lunas'])->default('menunggu');
            $table->foreignId('approved_by')->nullable()->constrained('users')->cascadeOnUpdate()->nullOnDelete();
            $table->boolean('is_override')->default(false)->comment('Override aturan 1 pinjaman/tahun');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        // 5. Simpanan — Catatan setoran simpanan
        Schema::create('simpanan', function (Blueprint $table) {
            $table->id();
            $table->string('no_referensi', 20)->unique()->comment('SIM-YYYY-NNNN');
            $table->foreignId('anggota_id')->constrained('anggota')->cascadeOnUpdate();
            $table->foreignId('jenis_simpanan_id')->constrained('jenis_simpanan')->cascadeOnUpdate();
            $table->decimal('nominal', 15, 2);
            $table->date('tanggal');
            $table->tinyInteger('bulan_untuk')->nullable()->comment('Bulan (khusus simpanan wajib)');
            $table->smallInteger('tahun_untuk')->nullable()->comment('Tahun (khusus simpanan wajib)');
            $table->foreignId('pinjaman_id')->nullable()->constrained('pinjaman')->cascadeOnUpdate()->nullOnDelete()
                  ->comment('Relasi ke pinjaman (khusus SWP)');
            $table->text('keterangan')->nullable();
            $table->foreignId('dicatat_oleh')->nullable()->constrained('users')->cascadeOnUpdate()->nullOnDelete();
            $table->timestamps();

            $table->unique(['anggota_id', 'jenis_simpanan_id', 'bulan_untuk', 'tahun_untuk'], 'unique_wajib_bulanan');
        });

        // 6. Penarikan Simpanan — Hanya saat keluar koperasi
        Schema::create('penarikan_simpanan', function (Blueprint $table) {
            $table->id();
            $table->string('no_referensi', 20)->unique()->comment('TRK-YYYY-NNNN');
            $table->foreignId('anggota_id')->constrained('anggota')->cascadeOnUpdate();
            $table->foreignId('jenis_simpanan_id')->constrained('jenis_simpanan')->cascadeOnUpdate();
            $table->decimal('nominal', 15, 2)->comment('FULL tanpa potongan');
            $table->date('tanggal');
            $table->text('keterangan')->nullable();
            $table->foreignId('diproses_oleh')->constrained('users')->cascadeOnUpdate();
            $table->timestamps();
        });

        // 9. Angsuran — Jadwal cicilan bulanan
        Schema::create('angsuran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pinjaman_id')->constrained('pinjaman')->cascadeOnUpdate()->cascadeOnDelete();
            $table->integer('angsuran_ke');
            $table->decimal('nominal_pokok', 15, 2);
            $table->decimal('nominal_bunga', 15, 2);
            $table->decimal('nominal_total', 15, 2);
            $table->date('tanggal_jatuh_tempo');
            $table->date('tanggal_bayar')->nullable();
            $table->enum('status', ['belum', 'lunas'])->default('belum');
            $table->timestamps();
        });

        // 10. Potongan Bulanan — Rekap potongan TPP per anggota per bulan
        Schema::create('potongan_bulanan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anggota_id')->constrained('anggota')->cascadeOnUpdate();
            $table->tinyInteger('bulan')->comment('1-12');
            $table->smallInteger('tahun');
            $table->decimal('potongan_simpanan_wajib', 15, 2)->default(0);
            $table->decimal('potongan_angsuran', 15, 2)->default(0);
            $table->decimal('potongan_lainnya', 15, 2)->default(0);
            $table->decimal('total_potongan', 15, 2)->default(0);
            $table->text('catatan')->nullable();
            $table->enum('status', ['draft', 'dikonfirmasi'])->default('draft');
            $table->foreignId('dicatat_oleh')->nullable()->constrained('users')->cascadeOnUpdate()->nullOnDelete();
            $table->timestamps();

            $table->unique(['anggota_id', 'bulan', 'tahun'], 'unique_anggota_bulan_tahun');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('potongan_bulanan');
        Schema::dropIfExists('angsuran');
        Schema::dropIfExists('penarikan_simpanan');
        Schema::dropIfExists('simpanan');
        Schema::dropIfExists('pinjaman');
        Schema::dropIfExists('periode_pinjaman');
        Schema::dropIfExists('jenis_simpanan');
    }
};
