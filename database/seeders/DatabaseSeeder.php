<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed data master
        $this->call([
            BidangSeeder::class,
            AnggotaMassSeeder::class,
            JenisSimpananSeeder::class,
            PengaturanSeeder::class,
            ShuConfigSeeder::class,
        ]);

        // Ambil 2 anggota untuk dihubungkan dengan akun default
        $anggotaAdmin = \App\Models\Anggota::first();
        $anggotaPimpinan = \App\Models\Anggota::skip(1)->first();

        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'nama' => 'Administrator',
                'username' => 'admin',
                'password' => bcrypt('admin123'),
                'role' => 'admin',
                'nip' => $anggotaAdmin?->nip,
            ]
        );

        User::updateOrCreate(
            ['username' => 'pimpinan'],
            [
                'nama' => 'Kepala Koperasi',
                'username' => 'pimpinan',
                'password' => bcrypt('pimpinan123'),
                'role' => 'pimpinan',
                'nip' => $anggotaPimpinan?->nip,
            ]
        );
    }
}
