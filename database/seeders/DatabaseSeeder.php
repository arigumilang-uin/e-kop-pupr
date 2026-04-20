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
        ]);

        // 2. Buat akun default admin & pimpinan
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'nama' => 'Administrator',
                'username' => 'admin',
                'password' => bcrypt('admin123'),
                'role' => 'admin',
            ]
        );

        User::updateOrCreate(
            ['username' => 'pimpinan'],
            [
                'nama' => 'Kepala Koperasi',
                'username' => 'pimpinan',
                'password' => bcrypt('pimpinan123'),
                'role' => 'pimpinan',
            ]
        );
    }
}
