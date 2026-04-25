<?php

namespace Database\Seeders;

use App\Models\Pengaturan;
use Illuminate\Database\Seeder;

class PengaturanSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            // Pengaturan Keuangan (Maker-Checker)
            [
                'key' => 'bunga_pinjaman_persen',
                'value' => '15',
                'deskripsi' => 'Bunga TOTAL per pinjaman (%)',
                'kategori' => 'keuangan',
                'memerlukan_persetujuan' => true,
            ],
            [
                'key' => 'potongan_swp_persen',
                'value' => '3',
                'deskripsi' => 'Potongan SWP dari pinjaman (%)',
                'kategori' => 'keuangan',
                'memerlukan_persetujuan' => true,
            ],
            [
                'key' => 'potongan_dana_resiko_persen',
                'value' => '1.5',
                'deskripsi' => 'Potongan dana resiko dari pinjaman (%)',
                'kategori' => 'keuangan',
                'memerlukan_persetujuan' => true,
            ],
            [
                'key' => 'potongan_biaya_admin_persen',
                'value' => '0.5',
                'deskripsi' => 'Potongan biaya admin dari pinjaman (%)',
                'kategori' => 'keuangan',
                'memerlukan_persetujuan' => true,
            ],
            [
                'key' => 'simpanan_pokok',
                'value' => '50000',
                'deskripsi' => 'Nominal simpanan pokok saat daftar (Rp)',
                'kategori' => 'keuangan',
                'memerlukan_persetujuan' => true,
            ],
            [
                'key' => 'simpanan_wajib',
                'value' => '50000',
                'deskripsi' => 'Nominal simpanan wajib bulanan (Rp)',
                'kategori' => 'keuangan',
                'memerlukan_persetujuan' => true,
            ],
            [
                'key' => 'batas_bulan_pelunasan_default',
                'value' => '11',
                'deskripsi' => 'Default bulan terakhir pelunasan pinjaman',
                'kategori' => 'keuangan',
                'memerlukan_persetujuan' => true,
            ],
            [
                'key' => 'tenor_minimal',
                'value' => '1',
                'deskripsi' => 'Tenor minimal pinjaman (bulan)',
                'kategori' => 'keuangan',
                'memerlukan_persetujuan' => true,
            ],
            // (SHU proportions are now managed via shu_distribusi table)

            // Pengaturan Teknis (Admin bisa ubah langsung)
            [
                'key' => 'maks_percobaan_login',
                'value' => '5',
                'deskripsi' => 'Maksimal percobaan login gagal sebelum akun terkunci',
                'kategori' => 'teknis',
                'memerlukan_persetujuan' => false,
            ],
            [
                'key' => 'durasi_kunci_akun_menit',
                'value' => '30',
                'deskripsi' => 'Durasi kunci akun setelah melebihi batas login gagal (menit)',
                'kategori' => 'teknis',
                'memerlukan_persetujuan' => false,
            ],
            [
                'key' => 'session_timeout_menit',
                'value' => '30',
                'deskripsi' => 'Timeout sesi login tidak aktif (menit)',
                'kategori' => 'teknis',
                'memerlukan_persetujuan' => false,
            ],
        ];

        foreach ($data as $item) {
            Pengaturan::updateOrCreate(
                ['key' => $item['key']],
                $item
            );
        }
    }
}
