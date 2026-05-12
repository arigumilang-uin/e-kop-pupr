<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Remap tipe_routing values ke nama baru yang lebih eksplisit
        DB::table('shu_distribusi')->where('tipe_routing', 'jasa_modal')->update(['tipe_routing' => 'prorata_simpanan']);
        DB::table('shu_distribusi')->where('tipe_routing', 'jasa_anggota')->update(['tipe_routing' => 'prorata_pinjaman']);
        DB::table('shu_distribusi')->where('tipe_routing', 'dana_pengurus')->update(['tipe_routing' => 'bagi_rata_pengurus']);
        DB::table('shu_distribusi')->where('tipe_routing', 'dana_cadangan')->update(['tipe_routing' => 'ekuitas']);
        // 'kewajiban' tetap 'kewajiban'

        // 2. Drop kolom is_protected (tidak diperlukan lagi)
        Schema::table('shu_distribusi', function (Blueprint $table) {
            $table->dropColumn('is_protected');
        });
    }

    public function down(): void
    {
        Schema::table('shu_distribusi', function (Blueprint $table) {
            $table->boolean('is_protected')->default(false)->after('is_aktif');
        });

        DB::table('shu_distribusi')->where('tipe_routing', 'prorata_simpanan')->update(['tipe_routing' => 'jasa_modal', 'is_protected' => true]);
        DB::table('shu_distribusi')->where('tipe_routing', 'prorata_pinjaman')->update(['tipe_routing' => 'jasa_anggota', 'is_protected' => true]);
        DB::table('shu_distribusi')->where('tipe_routing', 'bagi_rata_pengurus')->update(['tipe_routing' => 'dana_pengurus', 'is_protected' => true]);
        DB::table('shu_distribusi')->where('tipe_routing', 'ekuitas')->update(['tipe_routing' => 'dana_cadangan', 'is_protected' => true]);
    }
};
