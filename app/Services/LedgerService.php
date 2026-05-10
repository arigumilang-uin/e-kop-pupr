<?php

namespace App\Services;

use App\Enums\KategoriLedger;
use App\Enums\TipeLedger;
use App\Models\Ledger;
use Illuminate\Support\Facades\DB;

/**
 * LedgerService — Service untuk menulis entry ke Jurnal Umum Koperasi.
 *
 * ATURAN EMAS:
 * 1. Semua transaksi keuangan WAJIB memanggil service ini.
 * 2. Entry yang sudah ditulis TIDAK BOLEH di-UPDATE atau di-DELETE.
 * 3. Koreksi/Void dilakukan dengan menulis entry reversal baru via VoidService.
 * 4. Nominal yang dikirim ke method ini SELALU POSITIF.
 */
class LedgerService
{
    /**
     * Tulis satu entry ke ledger.
     *
     * @param TipeLedger     $tipe        Kredit (masuk) atau Debit (keluar)
     * @param KategoriLedger $kategori    Kategori transaksi
     * @param float          $nominal     Nilai POSITIF
     * @param string         $deskripsi   Deskripsi human-readable
     * @param array          $options     Opsi tambahan:
     *                                    - transaksi_ref_id: ID di tabel asal
     *                                    - transaksi_ref_type: Model class tabel asal
     *                                    - void_of_id: ID ledger yang di-void (untuk reversal)
     *                                    - anggota_id: ID anggota terkait
     *                                    - tanggal_efektif: Tanggal transaksi (default: today)
     *                                    - dicatat_oleh: User ID pencatat (default: auth user)
     * @return Ledger
     */
    public function catat(
        TipeLedger $tipe,
        KategoriLedger $kategori,
        float $nominal,
        string $deskripsi,
        array $options = [],
    ): Ledger {
        if ($nominal < 0) {
            throw new \InvalidArgumentException('Nominal ledger harus positif. Gunakan tipe debit/kredit untuk menentukan arah.');
        }

        return Ledger::create([
            'tipe' => $tipe,
            'kategori' => $kategori,
            'nominal' => $nominal,
            'deskripsi' => $deskripsi,
            'transaksi_ref_id' => $options['transaksi_ref_id'] ?? null,
            'transaksi_ref_type' => $options['transaksi_ref_type'] ?? null,
            'void_of_id' => $options['void_of_id'] ?? null,
            'anggota_id' => $options['anggota_id'] ?? null,
            'tanggal_efektif' => $options['tanggal_efektif'] ?? now()->toDateString(),
            'dicatat_oleh' => $options['dicatat_oleh'] ?? auth()->id(),
        ]);
    }

    // =========================================================================
    // Shorthand Methods — Arus Masuk (Kredit)
    // =========================================================================

    /**
     * Catat simpanan masuk ke ledger.
     */
    public function catatSimpanan(
        float $nominal,
        int $simpananId,
        int $anggotaId,
        string $jenisSimpanan,
        string $tanggal,
        ?int $userId = null,
    ): Ledger {
        return $this->catat(
            TipeLedger::Kredit,
            KategoriLedger::Simpanan,
            $nominal,
            "Setoran Simpanan {$jenisSimpanan}",
            [
                'transaksi_ref_id' => $simpananId,
                'transaksi_ref_type' => \App\Models\Simpanan::class,
                'anggota_id' => $anggotaId,
                'tanggal_efektif' => $tanggal,
                'dicatat_oleh' => $userId ?? auth()->id(),
            ],
        );
    }

    /**
     * Catat angsuran masuk ke ledger.
     */
    public function catatAngsuran(
        float $nominal,
        int $angsuranId,
        int $anggotaId,
        int $angsuranKe,
        string $noRefPinjaman,
        string $tanggal,
        ?int $userId = null,
    ): Ledger {
        return $this->catat(
            TipeLedger::Kredit,
            KategoriLedger::Angsuran,
            $nominal,
            "Pembayaran Angsuran Ke-{$angsuranKe} Pinjaman {$noRefPinjaman}",
            [
                'transaksi_ref_id' => $angsuranId,
                'transaksi_ref_type' => \App\Models\Angsuran::class,
                'anggota_id' => $anggotaId,
                'tanggal_efektif' => $tanggal,
                'dicatat_oleh' => $userId ?? auth()->id(),
            ],
        );
    }

    /**
     * Catat potongan pinjaman (dana resiko + biaya admin) masuk ke ledger.
     * Dipanggil saat pinjaman disetujui/dicairkan.
     */
    public function catatPotonganPinjaman(
        float $danaResiko,
        float $biayaAdmin,
        int $pinjamanId,
        int $anggotaId,
        string $noRefPinjaman,
        string $tanggal,
        ?int $userId = null,
    ): array {
        $entries = [];

        if ($danaResiko > 0) {
            $entries[] = $this->catat(
                TipeLedger::Kredit,
                KategoriLedger::Lainnya,
                $danaResiko,
                "Potongan Dana Resiko 1.5% dari Pinjaman {$noRefPinjaman}",
                [
                    'transaksi_ref_id' => $pinjamanId,
                    'transaksi_ref_type' => \App\Models\Pinjaman::class,
                    'anggota_id' => $anggotaId,
                    'tanggal_efektif' => $tanggal,
                    'dicatat_oleh' => $userId ?? auth()->id(),
                ],
            );
        }

        if ($biayaAdmin > 0) {
            $entries[] = $this->catat(
                TipeLedger::Kredit,
                KategoriLedger::Lainnya,
                $biayaAdmin,
                "Potongan Biaya Admin 0.5% dari Pinjaman {$noRefPinjaman}",
                [
                    'transaksi_ref_id' => $pinjamanId,
                    'transaksi_ref_type' => \App\Models\Pinjaman::class,
                    'anggota_id' => $anggotaId,
                    'tanggal_efektif' => $tanggal,
                    'dicatat_oleh' => $userId ?? auth()->id(),
                ],
            );
        }

        return $entries;
    }

    // =========================================================================
    // Shorthand Methods — Arus Keluar (Debit)
    // =========================================================================

    /**
     * Catat pencairan pinjaman (dana keluar) ke ledger.
     */
    public function catatPencairan(
        float $nominalBruto,
        int $pinjamanId,
        int $anggotaId,
        string $noRefPinjaman,
        string $tanggal,
        ?int $userId = null,
    ): Ledger {
        return $this->catat(
            TipeLedger::Debit,
            KategoriLedger::Pencairan,
            $nominalBruto,
            "Pencairan Pinjaman {$noRefPinjaman}",
            [
                'transaksi_ref_id' => $pinjamanId,
                'transaksi_ref_type' => \App\Models\Pinjaman::class,
                'anggota_id' => $anggotaId,
                'tanggal_efektif' => $tanggal,
                'dicatat_oleh' => $userId ?? auth()->id(),
            ],
        );
    }

    /**
     * Catat penarikan simpanan (anggota keluar) ke ledger.
     */
    public function catatPenarikan(
        float $nominal,
        int $penarikanId,
        int $anggotaId,
        string $jenisSimpanan,
        string $tanggal,
        ?int $userId = null,
    ): Ledger {
        return $this->catat(
            TipeLedger::Debit,
            KategoriLedger::Penarikan,
            $nominal,
            "Penarikan Simpanan {$jenisSimpanan} (Anggota Keluar)",
            [
                'transaksi_ref_id' => $penarikanId,
                'transaksi_ref_type' => \App\Models\PenarikanSimpanan::class,
                'anggota_id' => $anggotaId,
                'tanggal_efektif' => $tanggal,
                'dicatat_oleh' => $userId ?? auth()->id(),
            ],
        );
    }

    /**
     * Catat pengeluaran kas operasional ke ledger.
     */
    public function catatPengeluaran(
        float $nominal,
        int $pengeluaranId,
        string $kategoriNama,
        string $tanggal,
        ?int $userId = null,
    ): Ledger {
        return $this->catat(
            TipeLedger::Debit,
            KategoriLedger::Pengeluaran,
            $nominal,
            "Pengeluaran Kas: {$kategoriNama}",
            [
                'transaksi_ref_id' => $pengeluaranId,
                'transaksi_ref_type' => \App\Models\PengeluaranKas::class,
                'tanggal_efektif' => $tanggal,
                'dicatat_oleh' => $userId ?? auth()->id(),
            ],
        );
    }

    // =========================================================================
    // Void / Reversal
    // =========================================================================

    /**
     * Buat entry reversal (kebalikan dari entry asli).
     * Dipanggil oleh VoidService setelah void request disetujui.
     *
     * @param Ledger $originalEntry Entry asli yang di-void
     * @param string $alasan        Alasan void
     * @param int    $userId        User yang meng-approve void
     * @return Ledger               Entry reversal yang baru dibuat
     */
    public function buatReversal(Ledger $originalEntry, string $alasan, int $userId): Ledger
    {
        // Tipe kebalikan dari entry asli
        $tipeReversal = $originalEntry->tipe === TipeLedger::Kredit
            ? TipeLedger::Debit
            : TipeLedger::Kredit;

        return $this->catat(
            $tipeReversal,
            KategoriLedger::Void,
            $originalEntry->nominal,
            "VOID: {$originalEntry->deskripsi} — Alasan: {$alasan}",
            [
                'transaksi_ref_id' => $originalEntry->transaksi_ref_id,
                'transaksi_ref_type' => $originalEntry->transaksi_ref_type,
                'void_of_id' => $originalEntry->id,
                'anggota_id' => $originalEntry->anggota_id,
                'tanggal_efektif' => now()->toDateString(),
                'dicatat_oleh' => $userId,
            ],
        );
    }

    // =========================================================================
    // Query Helpers
    // =========================================================================

    /**
     * Hitung saldo koperasi dari ledger.
     * Saldo = SUM(kredit) - SUM(debit)
     */
    public function saldoKoperasi(): float
    {
        $kredit = (float) Ledger::where('tipe', TipeLedger::Kredit)->sum('nominal');
        $debit = (float) Ledger::where('tipe', TipeLedger::Debit)->sum('nominal');

        return $kredit - $debit;
    }

    /**
     * Hitung saldo simpanan anggota dari ledger.
     */
    public function saldoSimpananAnggota(int $anggotaId): float
    {
        $kredit = (float) Ledger::where('anggota_id', $anggotaId)
            ->whereIn('kategori', [KategoriLedger::Simpanan])
            ->where('tipe', TipeLedger::Kredit)
            ->sum('nominal');

        $debit = (float) Ledger::where('anggota_id', $anggotaId)
            ->whereIn('kategori', [KategoriLedger::Penarikan, KategoriLedger::Void])
            ->where('tipe', TipeLedger::Debit)
            ->where(function ($q) {
                // Hanya void yang terkait simpanan
                $q->where('kategori', '!=', KategoriLedger::Void)
                  ->orWhereHas('voidOf', function ($q2) {
                      $q2->where('kategori', KategoriLedger::Simpanan);
                  });
            })
            ->sum('nominal');

        return $kredit - $debit;
    }

    /**
     * Ambil ledger entry berdasarkan transaksi asal.
     */
    public function findByTransaksi(string $modelClass, int $modelId): ?Ledger
    {
        return Ledger::where('transaksi_ref_type', $modelClass)
            ->where('transaksi_ref_id', $modelId)
            ->where('kategori', '!=', KategoriLedger::Void)
            ->first();
    }
}
