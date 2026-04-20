<?php

namespace Database\Seeders;

use App\Models\Bidang;
use Illuminate\Database\Seeder;

class BidangSeeder extends Seeder
{
    public function run(): void
    {
        $bidangList = [
            ['nama_bidang' => 'Sekretariat', 'keterangan' => 'Bagian Sekretariat Dinas PUPR'],
            ['nama_bidang' => 'Bina Marga', 'keterangan' => 'Bidang Bina Marga'],
            ['nama_bidang' => 'Cipta Karya', 'keterangan' => 'Bidang Cipta Karya'],
            ['nama_bidang' => 'Sumber Daya Air', 'keterangan' => 'Bidang Sumber Daya Air'],
            ['nama_bidang' => 'Jasa Konstruksi', 'keterangan' => 'Bidang Jasa Konstruksi'],
            ['nama_bidang' => 'Tata Ruang', 'keterangan' => 'Bidang Tata Ruang'],
        ];

        foreach ($bidangList as $bidang) {
            Bidang::updateOrCreate(
                ['nama_bidang' => $bidang['nama_bidang']],
                $bidang
            );
        }
    }
}
