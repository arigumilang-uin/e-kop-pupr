<?php

namespace Database\Seeders;

use App\Models\Bidang;
use Illuminate\Database\Seeder;

class BidangSeeder extends Seeder
{
    public function run(): void
    {
        $bidangList = [
            ['nama_bidang' => 'Sekretariat', 'keterangan' => 'Bagian Sekretariat Dinas PUPRPKPP'],
            ['nama_bidang' => 'Sumber Daya Air', 'keterangan' => 'Bidang Sumber Daya Air'],
            ['nama_bidang' => 'Cipta Karya', 'keterangan' => 'Bidang Cipta Karya'],
            ['nama_bidang' => 'Bina Marga', 'keterangan' => 'Bidang Bina Marga'],
            ['nama_bidang' => 'Perumahan dan Kawasan Permukiman', 'keterangan' => 'Bidang Perumahan dan Kawasan Permukiman'],
            ['nama_bidang' => 'Pertanahan dan Penataan Ruang', 'keterangan' => 'Bidang Pertanahan dan Penataan Ruang'],
            ['nama_bidang' => 'UPT Jalan dan Jembatan Wilayah I', 'keterangan' => 'UPT Jalan dan Jembatan Wilayah I'],
            ['nama_bidang' => 'UPT Jalan dan Jembatan Wilayah II', 'keterangan' => 'UPT Jalan dan Jembatan Wilayah II'],
            ['nama_bidang' => 'UPT Jalan dan Jembatan Wilayah III', 'keterangan' => 'UPT Jalan dan Jembatan Wilayah III'],
            ['nama_bidang' => 'UPT Jalan dan Jembatan Wilayah IV', 'keterangan' => 'UPT Jalan dan Jembatan Wilayah IV'],
            ['nama_bidang' => 'UPT Jalan dan Jembatan Wilayah V', 'keterangan' => 'UPT Jalan dan Jembatan Wilayah V'],
            ['nama_bidang' => 'UPT Jalan dan Jembatan Wilayah VI', 'keterangan' => 'UPT Jalan dan Jembatan Wilayah VI'],
            ['nama_bidang' => 'UPT Air Minum', 'keterangan' => 'UPT Air Minum'],
            ['nama_bidang' => 'UPT Peralatan Konstruksi', 'keterangan' => 'UPT Peralatan Konstruksi'],
            ['nama_bidang' => 'Bina Jasa Konstruksi', 'keterangan' => 'Bidang Bina Jasa Konstruksi'],
            ['nama_bidang' => 'UPT Laboratorium Bahan Konstruksi', 'keterangan' => 'UPT Laboratorium Bahan Konstruksi'],
        ];

        foreach ($bidangList as $bidang) {
            Bidang::updateOrCreate(
                ['nama_bidang' => $bidang['nama_bidang']],
                $bidang
            );
        }
    }
}
