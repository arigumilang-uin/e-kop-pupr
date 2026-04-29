<?php

namespace Database\Seeders;

use App\Models\Anggota;
use App\Models\Bidang;
use Illuminate\Database\Seeder;

class AnggotaMassSeeder extends Seeder
{
    /**
     * Seed 250 anggota dengan NIP 18 digit acak, bidang dinas, dan nomor HP.
     */
    public function run(): void
    {
        $bidangIds = Bidang::pluck('id')->toArray();

        if (empty($bidangIds)) {
            $this->command->error('Tabel bidang masih kosong! Jalankan BidangSeeder terlebih dahulu.');
            return;
        }

        // Nama depan & belakang khas Indonesia
        $namaDepan = [
            'Ahmad', 'Budi', 'Cahya', 'Dian', 'Eka', 'Fajar', 'Galih', 'Hendra',
            'Indra', 'Joko', 'Kurnia', 'Lukman', 'Muhammad', 'Nanda', 'Okta',
            'Putra', 'Rizki', 'Sari', 'Taufik', 'Umar', 'Vina', 'Wahyu',
            'Yudi', 'Zainal', 'Ari', 'Bagus', 'Citra', 'Dewi', 'Endang',
            'Fitri', 'Gita', 'Hasan', 'Irfan', 'Joni', 'Kartika', 'Lestari',
            'Mira', 'Nur', 'Oki', 'Putri', 'Ratna', 'Sinta', 'Tina',
            'Umi', 'Vita', 'Wulan', 'Yanti', 'Zulfa', 'Rina', 'Deni',
            'Agus', 'Bambang', 'Cecep', 'Dedi', 'Eko', 'Feri', 'Gunawan',
            'Hadi', 'Ivan', 'Jaya', 'Kiki', 'Lia', 'Mega', 'Nina',
        ];

        $namaBelakang = [
            'Pratama', 'Saputra', 'Wijaya', 'Kusuma', 'Hidayat', 'Permana',
            'Nugroho', 'Santoso', 'Ramadhani', 'Setiawan', 'Surya', 'Putra',
            'Putri', 'Lestari', 'Wibowo', 'Suryadi', 'Prabowo', 'Firmansyah',
            'Ramadhan', 'Utama', 'Prasetyo', 'Handoko', 'Susanto', 'Hardianto',
            'Syahputra', 'Sitorus', 'Hutabarat', 'Nasution', 'Lubis', 'Harahap',
            'Siregar', 'Daulay', 'Ritonga', 'Batubara', 'Hasibuan', 'Manurung',
            'Siagian', 'Panjaitan', 'Tampubolon', 'Simanjuntak', 'Sinaga',
            'Situmorang', 'Pardede', 'Simbolon', 'Napitupulu', 'Aritonang',
        ];

        $usedNips = Anggota::pluck('nip')->toArray();
        $anggotaData = [];
        $now = now();

        for ($i = 0; $i < 250; $i++) {
            // Generate NIP 18 digit unik
            do {
                $nip = $this->generateNip18();
            } while (in_array($nip, $usedNips));
            $usedNips[] = $nip;

            // Nama acak
            $nama = $namaDepan[array_rand($namaDepan)] . ' ' . $namaBelakang[array_rand($namaBelakang)];

            // Nomor HP (08xx-xxxx-xxxx)
            $prefixes = ['0812', '0813', '0821', '0822', '0852', '0853', '0811', '0823', '0851', '0857'];
            $noHp = $prefixes[array_rand($prefixes)] . str_pad(mt_rand(0, 99999999), 8, '0', STR_PAD_LEFT);

            // Tanggal masuk acak antara 2020-2026
            $tanggalMasuk = now()
                ->subDays(mt_rand(90, 2190)) // ~3 bulan s.d ~6 tahun lalu
                ->format('Y-m-d');

            $anggotaData[] = [
                'nip'                => $nip,
                'nama'               => $nama,
                'bidang_id'          => $bidangIds[array_rand($bidangIds)],
                'no_hp'              => $noHp,
                'tanggal_masuk'      => $tanggalMasuk,
                'status'             => 'aktif',
                'is_pendaftar_ulang' => false,
                'created_at'         => $now,
                'updated_at'         => $now,
            ];
        }

        // Batch insert per 50 untuk efisiensi
        foreach (array_chunk($anggotaData, 50) as $chunk) {
            Anggota::insert($chunk);
        }

        $this->command->info("✅ Berhasil menambahkan 250 anggota baru.");
    }

    /**
     * Generate NIP acak 18 digit angka.
     */
    private function generateNip18(): string
    {
        // Format mirip NIP PNS: YYYYMMDDxxxxxxxxxx
        $tahunLahir = mt_rand(1970, 2000);
        $bulanLahir = str_pad(mt_rand(1, 12), 2, '0', STR_PAD_LEFT);
        $hariLahir = str_pad(mt_rand(1, 28), 2, '0', STR_PAD_LEFT);
        $sisaDigit = str_pad(mt_rand(0, 9999999999), 10, '0', STR_PAD_LEFT);

        return $tahunLahir . $bulanLahir . $hariLahir . $sisaDigit;
    }
}
