<?php

namespace Database\Seeders;

use App\Models\JenisSimpanan;
use Illuminate\Database\Seeder;

class JenisSimpananSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'kode' => 'POKOK',
                'nama' => 'Simpanan Pokok',
                'nominal_default' => 50000,
                'is_wajib' => false,
                'frekuensi' => 'sekali',
                'keterangan' => 'Dibayar 1x saat mendaftar menjadi anggota. Tidak bisa ditarik selama masih aktif.',
            ],
            [
                'kode' => 'WAJIB',
                'nama' => 'Simpanan Wajib',
                'nominal_default' => 50000,
                'is_wajib' => true,
                'frekuensi' => 'bulanan',
                'keterangan' => 'Wajib dibayar setiap bulan melalui potongan TPP. Tidak bisa ditarik selama masih aktif.',
            ],
            [
                'kode' => 'SUKARELA',
                'nama' => 'Simpanan Sukarela',
                'nominal_default' => 0,
                'is_wajib' => false,
                'frekuensi' => 'bebas',
                'keterangan' => 'Nominal bebas, kapan saja. Tidak bisa ditarik selama masih aktif.',
            ],
            [
                'kode' => 'SWP',
                'nama' => 'Simpanan Wajib Pinjam',
                'nominal_default' => 0,
                'is_wajib' => false,
                'frekuensi' => 'per_pinjaman',
                'keterangan' => 'Otomatis 3% dari nominal pinjaman saat disetujui. Tidak bisa ditarik selama masih aktif. Dikembalikan saat keluar koperasi.',
            ],
        ];

        foreach ($data as $item) {
            JenisSimpanan::updateOrCreate(
                ['kode' => $item['kode']],
                $item
            );
        }
    }
}
