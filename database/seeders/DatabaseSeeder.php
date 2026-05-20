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
            JenisSimpananSeeder::class,
            PengaturanSeeder::class,
            ShuConfigSeeder::class,
            RbacSeeder::class,
        ]);

        // Ambil 2 anggota untuk dihubungkan dengan akun default
        $anggotaAdmin = \App\Models\Anggota::first();
        $anggotaPimpinan = \App\Models\Anggota::skip(1)->first();

        $admin = User::updateOrCreate(
            ['username' => 'admin'],
            [
                'nama' => 'Administrator',
                'username' => 'admin',
                'password' => bcrypt('admin123'),
                'nip' => $anggotaAdmin?->nip,
            ]
        );
        $admin->assignRole('super_admin'); // Sesuai aturan ID 1 harus super admin

        $pimpinan = User::updateOrCreate(
            ['username' => 'pimpinan'],
            [
                'nama' => 'Kepala Koperasi',
                'username' => 'pimpinan',
                'password' => bcrypt('pimpinan123'),
                'nip' => $anggotaPimpinan?->nip,
            ]
        );
        $pimpinan->assignRole('pimpinan');
    }
}
