<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shu_distribusi', function (Blueprint $table) {
            $table->string('tipe_routing', 30)->default('kewajiban')->after('persen')
                ->comment('jasa_modal, jasa_anggota, dana_pengurus, dana_cadangan, kewajiban');
            $table->boolean('is_protected')->default(false)->after('is_aktif')
                ->comment('true = built-in, tidak bisa dihapus');
        });

        // Update existing rows
        DB::table('shu_distribusi')->where('nama', 'like', '%Jasa Modal%')->update(['tipe_routing' => 'jasa_modal', 'is_protected' => true]);
        DB::table('shu_distribusi')->where('nama', 'like', '%Jasa Anggota%')->update(['tipe_routing' => 'jasa_anggota', 'is_protected' => true]);
        DB::table('shu_distribusi')->where('nama', 'like', '%Dana Pengurus%')->update(['tipe_routing' => 'dana_pengurus', 'is_protected' => true]);
        DB::table('shu_distribusi')->where('nama', 'like', '%Cadangan%')->update(['tipe_routing' => 'dana_cadangan', 'is_protected' => true]);
        // Sisanya tetap 'kewajiban' (default)
    }

    public function down(): void
    {
        Schema::table('shu_distribusi', function (Blueprint $table) {
            $table->dropColumn(['tipe_routing', 'is_protected']);
        });
    }
};
