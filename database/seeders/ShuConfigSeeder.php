<?php

namespace Database\Seeders;

use App\Models\ShuKomponen;
use App\Models\ShuDistribusi;
use Illuminate\Database\Seeder;

class ShuConfigSeeder extends Seeder
{
    public function run(): void
    {
        // ============================
        // Komponen Pendapatan & Beban
        // ============================
        $komponen = [
            [
                'nama'        => 'Pendapatan Bunga Pinjaman',
                'tipe'        => 'pendapatan',
                'sumber_data' => 'bunga_pinjaman',
                'deskripsi'   => 'Total bunga dari pinjaman yang sudah disetujui pada tahun berjalan.',
                'urutan'      => 1,
            ],
            [
                'nama'        => 'Pendapatan Dana Resiko',
                'tipe'        => 'pendapatan',
                'sumber_data' => 'dana_resiko',
                'deskripsi'   => 'Potongan 1.5% dari setiap pencairan pinjaman.',
                'urutan'      => 2,
            ],
            [
                'nama'        => 'Pendapatan Biaya Admin',
                'tipe'        => 'pendapatan',
                'sumber_data' => 'biaya_admin',
                'deskripsi'   => 'Potongan 0.5% dari setiap pencairan pinjaman.',
                'urutan'      => 3,
            ],
            [
                'nama'        => 'Beban Operasional (Pengeluaran Manual)',
                'tipe'        => 'beban',
                'sumber_data' => 'pengeluaran_kas',
                'deskripsi'   => 'Seluruh pengeluaran kas manual yang dicatat oleh pengurus.',
                'urutan'      => 1,
            ],
        ];

        foreach ($komponen as $item) {
            ShuKomponen::updateOrCreate(
                ['sumber_data' => $item['sumber_data']],
                $item
            );
        }

        // ============================
        // Distribusi / Alokasi SHU
        // ============================
        $distribusi = [
            ['nama' => 'Jasa Modal',                  'persen' => 25, 'deskripsi' => 'Dibagikan ke anggota berdasarkan proporsi simpanan.', 'urutan' => 1],
            ['nama' => 'Jasa Anggota (Peminjam)',      'persen' => 25, 'deskripsi' => 'Dibagikan ke anggota yang meminjam berdasarkan volume pinjaman.', 'urutan' => 2],
            ['nama' => 'Dana Cadangan Koperasi',       'persen' => 30, 'deskripsi' => 'Disimpan sebagai cadangan modal koperasi.', 'urutan' => 3],
            ['nama' => 'Dana Pengurus',                'persen' => 10, 'deskripsi' => 'Insentif untuk pengurus koperasi.', 'urutan' => 4],
            ['nama' => 'Dana Sosial & Pendidikan',     'persen' => 10, 'deskripsi' => 'Untuk kegiatan sosial dan pendidikan anggota.', 'urutan' => 5],
        ];

        foreach ($distribusi as $item) {
            ShuDistribusi::updateOrCreate(
                ['nama' => $item['nama']],
                $item
            );
        }
    }
}
