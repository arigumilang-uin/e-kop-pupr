<?php

namespace Database\Seeders;

use App\Models\ParameterNeraca;
use App\Models\ParameterPhu;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

/**
 * Seed parameter_neraca (22 item) + parameter_phu (5 pendapatan + 15 beban placeholder)
 * + register permissions.
 *
 * Jalankan: php artisan db:seed --class=ParameterKeuanganSeeder
 */
class ParameterKeuanganSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedParameterNeraca();
        $this->seedParameterPhu();
        $this->seedPermissions();
    }

    private function seedParameterNeraca(): void
    {
        $this->command->info('=== Seed Parameter Neraca ===');
        $tahun = 2025;

        $items = [
            // ===================== AKTIVA — Harta Lancar =====================
            ['nama' => 'Kas',                               'posisi' => 'aktiva_lancar', 'sumber' => 'manual',   'kode' => null,                     'nominal' => 12460347,   'urutan' => 1,  'pengurang' => false],
            ['nama' => 'Bank BRK Syariah',                  'posisi' => 'aktiva_lancar', 'sumber' => 'otomatis', 'kode' => 'SALDO_BANK_BRK',         'nominal' => 733339185,  'urutan' => 2,  'pengurang' => false],
            ['nama' => 'Piutang Pinjaman Anggota',          'posisi' => 'aktiva_lancar', 'sumber' => 'otomatis', 'kode' => 'PIUTANG_PINJAMAN',       'nominal' => 0,          'urutan' => 3,  'pengurang' => false],
            ['nama' => 'Piutang Anggota Periode 2025',      'posisi' => 'aktiva_lancar', 'sumber' => 'otomatis', 'kode' => 'PIUTANG_LEGACY_BERJALAN','nominal' => 0,          'urutan' => 4,  'pengurang' => false],
            ['nama' => 'Piutang Anggota Periode 2018',      'posisi' => 'aktiva_lancar', 'sumber' => 'otomatis', 'kode' => 'PIUTANG_LEGACY_LAMA',    'nominal' => 0,          'urutan' => 5,  'pengurang' => false],
            ['nama' => 'Sisa Piutang Pengurus Periode 2025','posisi' => 'aktiva_lancar', 'sumber' => 'otomatis', 'kode' => 'PIUTANG_LEGACY_SISA',    'nominal' => 0,          'urutan' => 6,  'pengurang' => false],

            // ===================== AKTIVA — Penyertaan =====================
            ['nama' => 'Simpanan Pada PK-PRI',              'posisi' => 'penyertaan',    'sumber' => 'manual',   'kode' => null,                     'nominal' => 6981810,    'urutan' => 1,  'pengurang' => false],

            // ===================== AKTIVA — Harta Tetap =====================
            ['nama' => 'Inventaris',                        'posisi' => 'harta_tetap',   'sumber' => 'manual',   'kode' => null,                     'nominal' => 25303500,   'urutan' => 1,  'pengurang' => false],
            ['nama' => 'Ak. Penyusutan Inventaris',         'posisi' => 'harta_tetap',   'sumber' => 'manual',   'kode' => null,                     'nominal' => 5103500,    'urutan' => 2,  'pengurang' => true],

            // ===================== KEWAJIBAN — Jk. Pendek =====================
            ['nama' => 'Simpanan Sukarela',                 'posisi' => 'kewajiban_pendek', 'sumber' => 'manual',   'kode' => null,                  'nominal' => 67736378,   'urutan' => 1,  'pengurang' => false],
            ['nama' => 'Dana Pendidikan',                   'posisi' => 'kewajiban_pendek', 'sumber' => 'manual',   'kode' => null,                  'nominal' => 65597422,   'urutan' => 2,  'pengurang' => false],
            ['nama' => 'Dana Sosial',                       'posisi' => 'kewajiban_pendek', 'sumber' => 'manual',   'kode' => null,                  'nominal' => 76567376,   'urutan' => 3,  'pengurang' => false],
            ['nama' => 'Dana Pemdaker',                     'posisi' => 'kewajiban_pendek', 'sumber' => 'manual',   'kode' => null,                  'nominal' => 61837879,   'urutan' => 4,  'pengurang' => false],
            ['nama' => 'Dana Cadangan Resiko',              'posisi' => 'kewajiban_pendek', 'sumber' => 'otomatis', 'kode' => 'DANA_RESIKO_LIVE',    'nominal' => 171000000,  'urutan' => 5,  'pengurang' => false],
            ['nama' => 'Hutang Pajak',                      'posisi' => 'kewajiban_pendek', 'sumber' => 'manual',   'kode' => null,                  'nominal' => 11355553,   'urutan' => 6,  'pengurang' => false],

            // ===================== MODAL =====================
            ['nama' => 'Simpanan Pokok',                    'posisi' => 'modal',         'sumber' => 'otomatis', 'kode' => 'SIMPANAN_LIVE_POKOK',    'nominal' => 5210000,    'urutan' => 1,  'pengurang' => false],
            ['nama' => 'Simpanan Wajib',                    'posisi' => 'modal',         'sumber' => 'otomatis', 'kode' => 'SIMPANAN_LIVE_WAJIB',    'nominal' => 214156000,  'urutan' => 2,  'pengurang' => false],
            ['nama' => 'Simpanan Wajib Peminjam',           'posisi' => 'modal',         'sumber' => 'otomatis', 'kode' => 'SIMPANAN_LIVE_SWP',      'nominal' => 451533740,  'urutan' => 3,  'pengurang' => false],
            ['nama' => 'Donasi/Hibah',                      'posisi' => 'modal',         'sumber' => 'manual',   'kode' => null,                     'nominal' => 11958150,   'urutan' => 4,  'pengurang' => false],
            ['nama' => 'SHU Tahun Berjalan',                'posisi' => 'modal',         'sumber' => 'otomatis', 'kode' => 'SHU_TAHUN_BERJALAN',     'nominal' => 0,          'urutan' => 5,  'pengurang' => false],
            ['nama' => 'Cadangan',                          'posisi' => 'modal',         'sumber' => 'manual',   'kode' => null,                     'nominal' => 246613289,  'urutan' => 6,  'pengurang' => false],
        ];

        $count = 0;
        foreach ($items as $item) {
            ParameterNeraca::updateOrCreate(
                ['kode_otomatis' => $item['kode'], 'tahun_buku' => $tahun, 'nama' => $item['nama']],
                [
                    'posisi'         => $item['posisi'],
                    'sumber_data'    => $item['sumber'],
                    'kode_otomatis'  => $item['kode'],
                    'nominal_manual' => $item['nominal'],
                    'urutan'         => $item['urutan'],
                    'is_pengurang'   => $item['pengurang'],
                    'is_active'      => true,
                    'tahun_buku'     => $tahun,
                ]
            );
            $count++;
        }

        $this->command->info("  ✓ {$count} parameter neraca berhasil di-seed.");
    }

    private function seedParameterPhu(): void
    {
        $this->command->info('=== Seed Parameter PHU ===');
        $tahun = now()->year;

        $pendapatan = [
            ['nama' => 'Pendapatan Jasa Simpan Pinjam', 'sumber' => 'otomatis', 'kode' => 'PHU_JASA_PINJAM',  'nominal' => 0, 'urutan' => 1],
            ['nama' => 'Pendapatan ADM',                 'sumber' => 'otomatis', 'kode' => 'PHU_ADM',          'nominal' => 0, 'urutan' => 2],
            ['nama' => 'SHU PK-PRI',                     'sumber' => 'manual',   'kode' => null,               'nominal' => 0, 'urutan' => 3],
            ['nama' => 'Pendapatan Bunga Bank',           'sumber' => 'manual',   'kode' => null,               'nominal' => 0, 'urutan' => 4],
            ['nama' => 'Pendapatan Lainnya',              'sumber' => 'manual',   'kode' => null,               'nominal' => 0, 'urutan' => 5],
        ];

        $beban = [
            ['nama' => 'Beban Operasional',              'sumber' => 'otomatis', 'kode' => 'PHU_BEBAN_OPERASIONAL', 'nominal' => 0, 'urutan' => 1],
        ];

        $count = 0;
        foreach ($pendapatan as $item) {
            ParameterPhu::updateOrCreate(
                ['kode_otomatis' => $item['kode'], 'tahun_buku' => $tahun, 'nama' => $item['nama']],
                [
                    'tipe'           => 'pendapatan',
                    'sumber_data'    => $item['sumber'],
                    'kode_otomatis'  => $item['kode'],
                    'nominal_manual' => $item['nominal'],
                    'urutan'         => $item['urutan'],
                    'is_active'      => true,
                    'tahun_buku'     => $tahun,
                ]
            );
            $count++;
        }

        foreach ($beban as $item) {
            ParameterPhu::updateOrCreate(
                ['kode_otomatis' => $item['kode'], 'tahun_buku' => $tahun, 'nama' => $item['nama']],
                [
                    'tipe'           => 'beban',
                    'sumber_data'    => $item['sumber'],
                    'kode_otomatis'  => $item['kode'],
                    'nominal_manual' => $item['nominal'],
                    'urutan'         => $item['urutan'],
                    'is_active'      => true,
                    'tahun_buku'     => $tahun,
                ]
            );
            $count++;
        }

        $this->command->info("  ✓ {$count} parameter PHU berhasil di-seed.");
    }

    private function seedPermissions(): void
    {
        $this->command->info('=== Seed Permissions ===');

        $newPerms = [
            'parameter_neraca.manage' => 'Kelola parameter Neraca',
            'parameter_phu.manage'    => 'Kelola parameter PHU',
        ];

        foreach ($newPerms as $name => $label) {
            Permission::firstOrCreate(
                ['name' => $name, 'guard_name' => 'web']
            );
        }

        $this->command->info("  ✓ " . count($newPerms) . " permissions baru didaftarkan.");
    }
}
