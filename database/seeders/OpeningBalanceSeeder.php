<?php

namespace Database\Seeders;

use App\Models\SaldoOpeningBalance;
use Illuminate\Database\Seeder;

/**
 * OpeningBalanceSeeder — Memasukkan saldo historis statis dari Neraca 2025 CSV.
 *
 * Saldo ini adalah akumulasi dari tahun-tahun sebelumnya yang tidak ada
 * transaksinya di database sistem kita. Angka diambil dari:
 *   - 1 PRINT NERACA.csv (sheet Neraca)
 *   - BUKU BESAR NERACA.csv (sheet Buku Besar)
 *
 * Jalankan: php artisan db:seed --class=OpeningBalanceSeeder
 */
class OpeningBalanceSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('=== Seeder Opening Balance (Neraca 2025) ===');

        $data = [
            // ===================================================================
            // AKTIVA — Harta Lancar
            // ===================================================================
            [
                'kode_akun'      => 'KAS_TUNAI',
                'nama_akun'      => 'Kas',
                'posisi_neraca'  => 'aktiva_lancar',
                'sub_kategori'   => 'Kas & Setara Kas',
                'nominal'        => 12460347,
                'sisi'           => 'debit',
                'tahun_buku'     => 2025,
                'keterangan'     => 'Saldo kas tunai per 31 Des 2025',
            ],
            [
                'kode_akun'      => 'BANK_BRK',
                'nama_akun'      => 'Bank BRK Syariah',
                'posisi_neraca'  => 'aktiva_lancar',
                'sub_kategori'   => 'Kas & Setara Kas',
                'nominal'        => 733339185,
                'sisi'           => 'debit',
                'tahun_buku'     => 2025,
                'keterangan'     => 'Saldo rekening BRK Syariah per 31 Des 2025',
            ],
            // Piutang Anggota di-handle oleh tabel piutang_eksternal + pinjaman
            // Tidak perlu opening balance karena sudah memakai data real-time.

            // ===================================================================
            // AKTIVA — Penyertaan
            // ===================================================================
            [
                'kode_akun'      => 'PENYERTAAN_PKPRI',
                'nama_akun'      => 'Simpanan Pada PK-PRI',
                'posisi_neraca'  => 'penyertaan',
                'sub_kategori'   => null,
                'nominal'        => 6981810,
                'sisi'           => 'debit',
                'tahun_buku'     => 2025,
                'keterangan'     => 'Simpanan penyertaan pada Pusat Koperasi',
            ],

            // ===================================================================
            // AKTIVA — Harta Tetap
            // ===================================================================
            [
                'kode_akun'      => 'INVENTARIS',
                'nama_akun'      => 'Inventaris',
                'posisi_neraca'  => 'harta_tetap',
                'sub_kategori'   => null,
                'nominal'        => 25303500,
                'sisi'           => 'debit',
                'tahun_buku'     => 2025,
                'keterangan'     => 'Nilai perolehan inventaris kantor',
            ],
            [
                'kode_akun'      => 'AK_PENY_INVENTARIS',
                'nama_akun'      => 'Ak. Penyusutan Inventaris',
                'posisi_neraca'  => 'harta_tetap',
                'sub_kategori'   => null,
                'nominal'        => -5103500,  // Negatif = pengurang aset
                'sisi'           => 'debit',
                'tahun_buku'     => 2025,
                'keterangan'     => 'Akumulasi penyusutan inventaris',
            ],

            // ===================================================================
            // KEWAJIBAN — Jangka Pendek
            // ===================================================================
            [
                'kode_akun'      => 'SIMPANAN_SUKARELA',
                'nama_akun'      => 'Simpanan Sukarela',
                'posisi_neraca'  => 'kewajiban_pendek',
                'sub_kategori'   => null,
                'nominal'        => 67736378,
                'sisi'           => 'kredit',
                'tahun_buku'     => 2025,
                'keterangan'     => 'Simpanan sukarela anggota (dapat ditarik sewaktu-waktu)',
            ],
            [
                'kode_akun'      => 'DANA_PENDIDIKAN',
                'nama_akun'      => 'Dana Pendidikan',
                'posisi_neraca'  => 'kewajiban_pendek',
                'sub_kategori'   => 'Dana Alokasi SHU',
                'nominal'        => 65597422,
                'sisi'           => 'kredit',
                'tahun_buku'     => 2025,
                'keterangan'     => 'Akumulasi alokasi SHU untuk dana pendidikan',
            ],
            [
                'kode_akun'      => 'DANA_SOSIAL',
                'nama_akun'      => 'Dana Sosial',
                'posisi_neraca'  => 'kewajiban_pendek',
                'sub_kategori'   => 'Dana Alokasi SHU',
                'nominal'        => 76567376,
                'sisi'           => 'kredit',
                'tahun_buku'     => 2025,
                'keterangan'     => 'Akumulasi alokasi SHU untuk dana sosial',
            ],
            [
                'kode_akun'      => 'DANA_PEMDAKER',
                'nama_akun'      => 'Dana Pemdaker',
                'posisi_neraca'  => 'kewajiban_pendek',
                'sub_kategori'   => 'Dana Alokasi SHU',
                'nominal'        => 61837879,
                'sisi'           => 'kredit',
                'tahun_buku'     => 2025,
                'keterangan'     => 'Akumulasi alokasi SHU untuk dana pembangunan daerah kerja',
            ],
            [
                'kode_akun'      => 'DANA_CADANGAN_RESIKO',
                'nama_akun'      => 'Dana Cadangan Resiko',
                'posisi_neraca'  => 'kewajiban_pendek',
                'sub_kategori'   => null,
                'nominal'        => 171000000,
                'sisi'           => 'kredit',
                'tahun_buku'     => 2025,
                'keterangan'     => 'Dana cadangan resiko piutang tak tertagih',
            ],
            [
                'kode_akun'      => 'HUTANG_PAJAK',
                'nama_akun'      => 'Hutang Pajak',
                'posisi_neraca'  => 'kewajiban_pendek',
                'sub_kategori'   => null,
                'nominal'        => 11355553,
                'sisi'           => 'kredit',
                'tahun_buku'     => 2025,
                'keterangan'     => 'Hutang pajak yang belum dibayarkan',
            ],

            // ===================================================================
            // MODAL / EKUITAS
            // ===================================================================
            [
                'kode_akun'      => 'SIMPANAN_POKOK',
                'nama_akun'      => 'Simpanan Pokok',
                'posisi_neraca'  => 'modal',
                'sub_kategori'   => 'Simpanan Anggota',
                'nominal'        => 5210000,
                'sisi'           => 'kredit',
                'tahun_buku'     => 2025,
                'keterangan'     => 'Total simpanan pokok seluruh anggota',
            ],
            [
                'kode_akun'      => 'SIMPANAN_WAJIB',
                'nama_akun'      => 'Simpanan Wajib',
                'posisi_neraca'  => 'modal',
                'sub_kategori'   => 'Simpanan Anggota',
                'nominal'        => 214156000,
                'sisi'           => 'kredit',
                'tahun_buku'     => 2025,
                'keterangan'     => 'Total simpanan wajib seluruh anggota',
            ],
            [
                'kode_akun'      => 'SWP',
                'nama_akun'      => 'Simpanan Wajib Peminjam',
                'posisi_neraca'  => 'modal',
                'sub_kategori'   => 'Simpanan Anggota',
                'nominal'        => 451533740,
                'sisi'           => 'kredit',
                'tahun_buku'     => 2025,
                'keterangan'     => 'Total SWP (3% potongan pinjaman)',
            ],
            [
                'kode_akun'      => 'DONASI_HIBAH',
                'nama_akun'      => 'Donasi/Hibah',
                'posisi_neraca'  => 'modal',
                'sub_kategori'   => null,
                'nominal'        => 11958150,
                'sisi'           => 'kredit',
                'tahun_buku'     => 2025,
                'keterangan'     => 'Donasi dan hibah yang diterima koperasi',
            ],
            [
                'kode_akun'      => 'SHU_TAHUN_BERJALAN',
                'nama_akun'      => 'SHU Tahun Berjalan',
                'posisi_neraca'  => 'modal',
                'sub_kategori'   => null,
                'nominal'        => 30180899,
                'sisi'           => 'kredit',
                'tahun_buku'     => 2025,
                'keterangan'     => 'SHU bersih tahun buku 2025',
            ],
            [
                'kode_akun'      => 'CADANGAN',
                'nama_akun'      => 'Cadangan',
                'posisi_neraca'  => 'modal',
                'sub_kategori'   => null,
                'nominal'        => 246613289,
                'sisi'           => 'kredit',
                'tahun_buku'     => 2025,
                'keterangan'     => 'Cadangan modal koperasi (akumulasi alokasi SHU)',
            ],
        ];

        $count = 0;
        foreach ($data as $item) {
            SaldoOpeningBalance::updateOrCreate(
                [
                    'kode_akun'  => $item['kode_akun'],
                    'tahun_buku' => $item['tahun_buku'],
                ],
                $item
            );
            $count++;
        }

        $this->command->info("  ✓ {$count} akun opening balance berhasil di-seed.");

        // Verifikasi keseimbangan
        $this->verifikasiNeraca();
    }

    /**
     * Verifikasi bahwa total Aktiva = Total Pasiva dari opening balance.
     */
    private function verifikasiNeraca(): void
    {
        $tahun = 2025;

        $totalAktiva   = (float) SaldoOpeningBalance::tahun($tahun)
            ->where('sisi', 'debit')->sum('nominal');

        $totalPasiva   = (float) SaldoOpeningBalance::tahun($tahun)
            ->where('sisi', 'kredit')->sum('nominal');

        // Tambahkan piutang dari piutang_eksternal (yang aktif di neraca)
        $piutangLegacy = (float) \DB::table('piutang_eksternal')
            ->where('status', 'aktif')
            ->whereIn('kategori_piutang', [
                'pengurus_berjalan', 'pengurus_lama', 'sisa_pengurus', 'pihak_ketiga'
            ])
            ->sum('sisa_piutang');

        $totalAktivaFull = $totalAktiva + $piutangLegacy;
        $selisih = round($totalAktivaFull - $totalPasiva);

        $this->command->newLine();
        $this->command->info('  === Verifikasi Neraca Opening Balance ===');
        $this->command->info("  Aktiva (Opening Balance): Rp " . number_format($totalAktiva, 0, ',', '.'));
        $this->command->info("  Piutang Legacy (live):     Rp " . number_format($piutangLegacy, 0, ',', '.'));
        $this->command->info("  Total Aktiva:              Rp " . number_format($totalAktivaFull, 0, ',', '.'));
        $this->command->info("  Total Pasiva:              Rp " . number_format($totalPasiva, 0, ',', '.'));
        $this->command->info("  Selisih:                   Rp " . number_format($selisih, 0, ',', '.'));

        if (abs($selisih) <= 1) {
            $this->command->info('  ✓ NERACA SEIMBANG');
        } else {
            $this->command->warn("  ⚠ SELISIH Rp " . number_format(abs($selisih), 0, ',', '.') . ' — periksa data!');
        }
    }
}
