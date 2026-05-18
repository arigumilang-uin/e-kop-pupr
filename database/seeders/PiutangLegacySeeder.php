<?php

namespace Database\Seeders;

use App\Models\PiutangEksternal;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

/**
 * PiutangLegacySeeder — Membaca data piutang legacy dari CSV dan menyuntikkan ke tabel piutang_eksternal.
 *
 * Sumber data:
 *   - csv/8 PRINT PIUTANG BERJALAN 2025.csv → kategori: pengurus_berjalan
 *   - csv/7 PIUTANG PENGURUS PERIODE 2016.csv → kategori: pengurus_lama
 *
 * Jalankan: php artisan db:seed --class=PiutangLegacySeeder
 */
class PiutangLegacySeeder extends Seeder
{
    public function run(): void
    {
        $basePath = base_path('csv');

        $this->command->info('=== Seeder Piutang Legacy ===');

        // ---------------------------------------------------------------------------
        // 1. Piutang Berjalan 2025 (Sheet 8)
        // ---------------------------------------------------------------------------
        $fileBerjalan = $basePath . '/8 PRINT PIUTANG BERJALAN 2025.csv';
        $countBerjalan = $this->seedFromCsv(
            filePath: $fileBerjalan,
            kategoriPiutang: 'pengurus_berjalan',
            kategoriPeminjam: 'pengurus',
            periodeLabel: '2019-2025',
            tahunPinjam: 2025,
            // Kolom CSV: NO(0), KETERANGAN(1), INSTANSI/BIDANG(2), TOTAL_PIUTANG(3), KET(4)
            namaIndex: 1,
            nominalIndex: 3,
            bidangIndex: 2,
            headerRows: 4, // 4 baris header sebelum data
        );
        $this->command->info("  ✓ Piutang Berjalan 2025: {$countBerjalan} records");

        // ---------------------------------------------------------------------------
        // 2. Piutang Pengurus Periode 2016-2019 (Sheet 7)
        // ---------------------------------------------------------------------------
        $fileLama = $basePath . '/7 PIUTANG PENGURUS PERIODE 2016.csv';
        $countLama = $this->seedFromCsv(
            filePath: $fileLama,
            kategoriPiutang: 'pengurus_lama',
            kategoriPeminjam: 'pengurus',
            periodeLabel: '2016-2019',
            tahunPinjam: 2018,
            // Kolom CSV: NO(0), NAMA(1), BIDANG(2), JUMLAH_PINJAMAN(3), KET(4), PEMBERI(5)
            namaIndex: 1,
            nominalIndex: 3,
            bidangIndex: 2,
            headerRows: 4,
        );
        $this->command->info("  ✓ Piutang Pengurus Lama (2016-2019): {$countLama} records");

        // ---------------------------------------------------------------------------
        // 3. Sisa Piutang Pengurus Periode 2025 (Entry statis dari Neraca)
        //    Ini adalah angka gelondongan Rp 473.000.000 dari Neraca CSV
        //    yang tidak memiliki rincian per-orang.
        // ---------------------------------------------------------------------------
        $countSisa = 0;
        $existing = PiutangEksternal::where('kategori_piutang', 'sisa_pengurus')
            ->where('tahun_pinjam', 2025)
            ->first();

        if (!$existing) {
            PiutangEksternal::create([
                'nama_peminjam'    => 'Akumulasi Sisa Piutang Pengurus 2025',
                'jabatan_peminjam' => null,
                'kategori_peminjam'=> 'pengurus',
                'kategori_piutang' => 'sisa_pengurus',
                'bidang'           => null,
                'periode_pengurus' => '2019-2025',
                'tahun_pinjam'     => 2025,
                'nominal_awal'     => 473000000,
                'nominal_terbayar' => 0,
                'sisa_piutang'     => 473000000,
                'keterangan'       => 'Sisa Piutang Pengurus Periode 2025 — angka gelondongan dari Neraca CSV',
                'status'           => 'aktif',
                'tanggal_catat'    => '2025-12-31',
                'dicatat_oleh'     => 1,
            ]);
            $countSisa = 1;
        } else {
            $this->command->line("    → Skip duplikat: Sisa Piutang Pengurus 2025");
        }
        $this->command->info("  ✓ Sisa Piutang Pengurus 2025: {$countSisa} record (Rp 473.000.000)");

        $total = $countBerjalan + $countLama + $countSisa;
        $this->command->info("  Total: {$total} piutang legacy berhasil di-seed.");
    }

    /**
     * Baca file CSV dan masukkan data ke piutang_eksternal.
     */
    private function seedFromCsv(
        string $filePath,
        string $kategoriPiutang,
        string $kategoriPeminjam,
        string $periodeLabel,
        int $tahunPinjam,
        int $namaIndex,
        int $nominalIndex,
        int $bidangIndex,
        int $headerRows,
    ): int {
        if (!file_exists($filePath)) {
            $this->command->warn("  ⚠ File tidak ditemukan: {$filePath}");
            return 0;
        }

        $handle = fopen($filePath, 'r');
        if ($handle === false) {
            $this->command->error("  ✗ Gagal membuka file: {$filePath}");
            return 0;
        }

        $count = 0;
        $currentRow = 0;

        while (($row = fgetcsv($handle)) !== false) {
            $currentRow++;

            // Skip header rows
            if ($currentRow <= $headerRows) {
                continue;
            }

            // Skip baris kosong
            if (empty(array_filter($row))) {
                continue;
            }

            // Skip baris TOTAL / footer
            $firstCol = trim($row[0] ?? '');
            if (stripos($firstCol, 'TOTAL') !== false ||
                stripos($firstCol, 'PENGURUS') !== false ||
                stripos($firstCol, 'KOPERASI') !== false ||
                stripos($firstCol, 'KETUA') !== false ||
                stripos($firstCol, 'BENDAHARA') !== false ||
                stripos($firstCol, 'KHAIRIL') !== false ||
                $firstCol === '') {
                // Jika kolom pertama kosong TAPI ada nama di kolom nama, lanjutkan
                $nama = trim($row[$namaIndex] ?? '');
                if (empty($nama) || stripos($nama, 'TOTAL') !== false ||
                    stripos($nama, 'PENGURUS') !== false ||
                    stripos($nama, 'KOPERASI') !== false) {
                    continue;
                }
            }

            $nama = trim($row[$namaIndex] ?? '');
            $nominalRaw = trim($row[$nominalIndex] ?? '0');
            $bidang = trim($row[$bidangIndex] ?? '');

            // Skip jika nama kosong
            if (empty($nama)) {
                continue;
            }

            // Parse nominal: hapus karakter non-angka kecuali titik desimal
            $nominal = $this->parseNominal($nominalRaw);

            // Skip jika nominal 0 atau negatif
            if ($nominal <= 0) {
                continue;
            }

            // Cek duplikat (berdasarkan nama + kategori + tahun)
            $existing = PiutangEksternal::where('nama_peminjam', $nama)
                ->where('kategori_piutang', $kategoriPiutang)
                ->where('tahun_pinjam', $tahunPinjam)
                ->first();

            if ($existing) {
                $this->command->line("    → Skip duplikat: {$nama}");
                continue;
            }

            PiutangEksternal::create([
                'nama_peminjam' => $nama,
                'jabatan_peminjam' => null,
                'kategori_peminjam' => $kategoriPeminjam,
                'kategori_piutang' => $kategoriPiutang,
                'bidang' => $bidang ?: null,
                'periode_pengurus' => $periodeLabel,
                'tahun_pinjam' => $tahunPinjam,
                'nominal_awal' => $nominal,
                'nominal_terbayar' => 0,
                'sisa_piutang' => $nominal,
                'keterangan' => "Import dari CSV — Kategori: {$periodeLabel}",
                'status' => 'aktif',
                'tanggal_catat' => '2025-12-31',
                'dicatat_oleh' => 1, // Admin user
            ]);

            $count++;
        }

        fclose($handle);
        return $count;
    }

    /**
     * Parse string nominal dari CSV ke float.
     * Menangani format: "6300000", "6,300,000", "6300000.00", dsb.
     */
    private function parseNominal(string $raw): float
    {
        // Hapus spasi, Rp, titik ribuan
        $clean = preg_replace('/[^\d.]/', '', $raw);

        // Jika ada multiple dots (format ribuan), hapus semua kecuali yang terakhir
        if (substr_count($clean, '.') > 1) {
            $parts = explode('.', $clean);
            $lastPart = array_pop($parts);
            $clean = implode('', $parts) . '.' . $lastPart;
        }

        return (float) $clean;
    }
}
