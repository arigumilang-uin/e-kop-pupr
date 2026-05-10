<?php

namespace App\Console\Commands;

use App\Enums\KategoriLedger;
use App\Enums\TipeLedger;
use App\Models\Ledger;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Artisan command untuk melakukan backfill (retrofit) seluruh data transaksi
 * legacy dari tabel domain (simpanan, angsuran, pinjaman, penarikan, pengeluaran)
 * ke dalam tabel ledger yang baru.
 *
 * PERHATIAN:
 * - Command ini AMAN dijalankan berulang kali (idempotent).
 *   Entry yang sudah ada di ledger (berdasarkan ref_type + ref_id) TIDAK akan
 *   diduplikasi.
 * - Data yang sudah soft-deleted TIDAK akan di-backfill.
 * - Jalankan command ini di luar jam operasional untuk menghindari lock contention.
 *
 * Usage:
 *   php artisan ledger:rebuild
 *   php artisan ledger:rebuild --dry-run    (preview tanpa insert)
 */
class RebuildLedger extends Command
{
    protected $signature = 'ledger:rebuild {--dry-run : Preview tanpa menulis ke database}';

    protected $description = 'Backfill (retrofit) seluruh data transaksi legacy ke tabel ledger';

    private int $created = 0;
    private int $skipped = 0;

    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');

        $this->info('');
        $this->info('╔══════════════════════════════════════════════╗');
        $this->info('║   LEDGER REBUILD — Backfill Data Legacy      ║');
        $this->info('╚══════════════════════════════════════════════╝');
        $this->info('');

        if ($isDryRun) {
            $this->warn('⚡ MODE DRY RUN — Tidak ada data yang akan ditulis.');
            $this->info('');
        }

        // 1. Simpanan
        $this->backfillSimpanan($isDryRun);

        // 2. Angsuran (yang sudah lunas)
        $this->backfillAngsuran($isDryRun);

        // 3. Pencairan Pinjaman (yang sudah disetujui/berjalan/lunas)
        $this->backfillPencairan($isDryRun);

        // 4. Potongan Pinjaman (dana resiko + biaya admin)
        $this->backfillPotonganPinjaman($isDryRun);

        // 5. Penarikan Simpanan
        $this->backfillPenarikan($isDryRun);

        // 6. Pengeluaran Kas
        $this->backfillPengeluaran($isDryRun);

        $this->info('');
        $this->info('═══════════════════════════════════════════════');
        $this->info("✅ Selesai! Created: {$this->created} | Skipped: {$this->skipped}");
        $this->info('═══════════════════════════════════════════════');

        return Command::SUCCESS;
    }

    private function backfillSimpanan(bool $isDryRun): void
    {
        $this->info('📥 [1/6] Backfill Simpanan...');

        $simpanans = DB::table('simpanan')
            ->join('jenis_simpanan', 'simpanan.jenis_simpanan_id', '=', 'jenis_simpanan.id')
            ->whereNull('simpanan.deleted_at')
            ->where(function ($q) {
                $q->where('simpanan.status', 'aktif')->orWhereNull('simpanan.status');
            })
            ->select('simpanan.*', 'jenis_simpanan.nama as jenis_nama')
            ->orderBy('simpanan.id')
            ->get();

        $bar = $this->output->createProgressBar($simpanans->count());

        foreach ($simpanans as $s) {
            if ($this->alreadyExists(\App\Models\Simpanan::class, $s->id)) {
                $this->skipped++;
                $bar->advance();
                continue;
            }

            if (!$isDryRun) {
                Ledger::create([
                    'tipe' => TipeLedger::Kredit,
                    'kategori' => KategoriLedger::Simpanan,
                    'nominal' => $s->nominal,
                    'deskripsi' => "Setoran Simpanan {$s->jenis_nama}",
                    'transaksi_ref_id' => $s->id,
                    'transaksi_ref_type' => \App\Models\Simpanan::class,
                    'anggota_id' => $s->anggota_id,
                    'tanggal_efektif' => $s->tanggal,
                    'dicatat_oleh' => $s->dicatat_oleh,
                ]);
            }
            $this->created++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
    }

    private function backfillAngsuran(bool $isDryRun): void
    {
        $this->info('📥 [2/6] Backfill Angsuran (Lunas)...');

        $angsurans = DB::table('angsuran')
            ->join('pinjaman', 'angsuran.pinjaman_id', '=', 'pinjaman.id')
            ->where('angsuran.status', 'lunas')
            ->whereNull('angsuran.deleted_at')
            ->select('angsuran.*', 'pinjaman.anggota_id', 'pinjaman.no_referensi as pinjaman_ref')
            ->orderBy('angsuran.id')
            ->get();

        $bar = $this->output->createProgressBar($angsurans->count());

        foreach ($angsurans as $a) {
            if ($this->alreadyExists(\App\Models\Angsuran::class, $a->id)) {
                $this->skipped++;
                $bar->advance();
                continue;
            }

            if (!$isDryRun) {
                Ledger::create([
                    'tipe' => TipeLedger::Kredit,
                    'kategori' => KategoriLedger::Angsuran,
                    'nominal' => $a->nominal_total,
                    'deskripsi' => "Pembayaran Angsuran Ke-{$a->angsuran_ke} Pinjaman {$a->pinjaman_ref}",
                    'transaksi_ref_id' => $a->id,
                    'transaksi_ref_type' => \App\Models\Angsuran::class,
                    'anggota_id' => $a->anggota_id,
                    'tanggal_efektif' => $a->tanggal_bayar ?? $a->tanggal_jatuh_tempo,
                    'dicatat_oleh' => null,
                ]);
            }
            $this->created++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
    }

    private function backfillPencairan(bool $isDryRun): void
    {
        $this->info('📥 [3/6] Backfill Pencairan Pinjaman...');

        $pinjamans = DB::table('pinjaman')
            ->whereIn('status', ['berjalan', 'lunas'])
            ->whereNull('deleted_at')
            ->orderBy('id')
            ->get();

        $bar = $this->output->createProgressBar($pinjamans->count());

        foreach ($pinjamans as $p) {
            // Cek pencairan sudah ada
            $key = \App\Models\Pinjaman::class . ':pencairan:' . $p->id;
            if ($this->alreadyExists(\App\Models\Pinjaman::class, $p->id, KategoriLedger::Pencairan)) {
                $this->skipped++;
                $bar->advance();
                continue;
            }

            if (!$isDryRun) {
                Ledger::create([
                    'tipe' => TipeLedger::Debit,
                    'kategori' => KategoriLedger::Pencairan,
                    'nominal' => $p->nominal_pinjaman,
                    'deskripsi' => "Pencairan Pinjaman {$p->no_referensi}",
                    'transaksi_ref_id' => $p->id,
                    'transaksi_ref_type' => \App\Models\Pinjaman::class,
                    'anggota_id' => $p->anggota_id,
                    'tanggal_efektif' => $p->tanggal_approval ?? $p->tanggal_pengajuan,
                    'dicatat_oleh' => $p->approved_by,
                ]);
            }
            $this->created++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
    }

    private function backfillPotonganPinjaman(bool $isDryRun): void
    {
        $this->info('📥 [4/6] Backfill Potongan Pinjaman (Dana Resiko + Biaya Admin)...');

        $pinjamans = DB::table('pinjaman')
            ->whereIn('status', ['berjalan', 'lunas'])
            ->whereNull('deleted_at')
            ->where(function ($q) {
                $q->where('potongan_dana_resiko', '>', 0)
                  ->orWhere('potongan_biaya_admin', '>', 0);
            })
            ->orderBy('id')
            ->get();

        $bar = $this->output->createProgressBar($pinjamans->count());

        foreach ($pinjamans as $p) {
            // Dana Resiko
            if ($p->potongan_dana_resiko > 0) {
                if (!$this->alreadyExistsWithDesc(\App\Models\Pinjaman::class, $p->id, 'Dana Resiko')) {
                    if (!$isDryRun) {
                        Ledger::create([
                            'tipe' => TipeLedger::Kredit,
                            'kategori' => KategoriLedger::Lainnya,
                            'nominal' => $p->potongan_dana_resiko,
                            'deskripsi' => "Potongan Dana Resiko 1.5% dari Pinjaman {$p->no_referensi}",
                            'transaksi_ref_id' => $p->id,
                            'transaksi_ref_type' => \App\Models\Pinjaman::class,
                            'anggota_id' => $p->anggota_id,
                            'tanggal_efektif' => $p->tanggal_approval ?? $p->tanggal_pengajuan,
                            'dicatat_oleh' => $p->approved_by,
                        ]);
                    }
                    $this->created++;
                } else {
                    $this->skipped++;
                }
            }

            // Biaya Admin
            if ($p->potongan_biaya_admin > 0) {
                if (!$this->alreadyExistsWithDesc(\App\Models\Pinjaman::class, $p->id, 'Biaya Admin')) {
                    if (!$isDryRun) {
                        Ledger::create([
                            'tipe' => TipeLedger::Kredit,
                            'kategori' => KategoriLedger::Lainnya,
                            'nominal' => $p->potongan_biaya_admin,
                            'deskripsi' => "Potongan Biaya Admin 0.5% dari Pinjaman {$p->no_referensi}",
                            'transaksi_ref_id' => $p->id,
                            'transaksi_ref_type' => \App\Models\Pinjaman::class,
                            'anggota_id' => $p->anggota_id,
                            'tanggal_efektif' => $p->tanggal_approval ?? $p->tanggal_pengajuan,
                            'dicatat_oleh' => $p->approved_by,
                        ]);
                    }
                    $this->created++;
                } else {
                    $this->skipped++;
                }
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
    }

    private function backfillPenarikan(bool $isDryRun): void
    {
        $this->info('📥 [5/6] Backfill Penarikan Simpanan...');

        $penarikans = DB::table('penarikan_simpanan')
            ->join('jenis_simpanan', 'penarikan_simpanan.jenis_simpanan_id', '=', 'jenis_simpanan.id')
            ->whereNull('penarikan_simpanan.deleted_at')
            ->select('penarikan_simpanan.*', 'jenis_simpanan.nama as jenis_nama')
            ->orderBy('penarikan_simpanan.id')
            ->get();

        $bar = $this->output->createProgressBar($penarikans->count());

        foreach ($penarikans as $p) {
            if ($this->alreadyExists(\App\Models\PenarikanSimpanan::class, $p->id)) {
                $this->skipped++;
                $bar->advance();
                continue;
            }

            if (!$isDryRun) {
                Ledger::create([
                    'tipe' => TipeLedger::Debit,
                    'kategori' => KategoriLedger::Penarikan,
                    'nominal' => $p->nominal,
                    'deskripsi' => "Penarikan Simpanan {$p->jenis_nama} (Anggota Keluar)",
                    'transaksi_ref_id' => $p->id,
                    'transaksi_ref_type' => \App\Models\PenarikanSimpanan::class,
                    'anggota_id' => $p->anggota_id,
                    'tanggal_efektif' => $p->tanggal,
                    'dicatat_oleh' => $p->diproses_oleh,
                ]);
            }
            $this->created++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
    }

    private function backfillPengeluaran(bool $isDryRun): void
    {
        $this->info('📥 [6/6] Backfill Pengeluaran Kas...');

        $pengeluarans = DB::table('pengeluaran_kas')
            ->join('kategori_pengeluaran', 'pengeluaran_kas.kategori_pengeluaran_id', '=', 'kategori_pengeluaran.id')
            ->whereNull('pengeluaran_kas.deleted_at')
            ->where(function ($q) {
                $q->where('pengeluaran_kas.status', 'aktif')
                  ->orWhereNull('pengeluaran_kas.status');
            })
            ->select('pengeluaran_kas.*', 'kategori_pengeluaran.nama as kategori_nama')
            ->orderBy('pengeluaran_kas.id')
            ->get();

        $bar = $this->output->createProgressBar($pengeluarans->count());

        foreach ($pengeluarans as $p) {
            if ($this->alreadyExists(\App\Models\PengeluaranKas::class, $p->id)) {
                $this->skipped++;
                $bar->advance();
                continue;
            }

            if (!$isDryRun) {
                Ledger::create([
                    'tipe' => TipeLedger::Debit,
                    'kategori' => KategoriLedger::Pengeluaran,
                    'nominal' => $p->nominal,
                    'deskripsi' => "Pengeluaran Kas: {$p->kategori_nama}",
                    'transaksi_ref_id' => $p->id,
                    'transaksi_ref_type' => \App\Models\PengeluaranKas::class,
                    'tanggal_efektif' => $p->tanggal,
                    'dicatat_oleh' => $p->dicatat_oleh,
                ]);
            }
            $this->created++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
    }

    // === Helpers ===

    private function alreadyExists(string $modelClass, int $modelId, ?KategoriLedger $kategori = null): bool
    {
        $query = Ledger::where('transaksi_ref_type', $modelClass)
            ->where('transaksi_ref_id', $modelId);

        if ($kategori) {
            $query->where('kategori', $kategori);
        }

        return $query->exists();
    }

    private function alreadyExistsWithDesc(string $modelClass, int $modelId, string $descContains): bool
    {
        return Ledger::where('transaksi_ref_type', $modelClass)
            ->where('transaksi_ref_id', $modelId)
            ->where('deskripsi', 'like', "%{$descContains}%")
            ->exists();
    }
}
