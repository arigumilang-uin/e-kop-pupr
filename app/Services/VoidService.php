<?php

namespace App\Services;

use App\Enums\KategoriLedger;
use App\Enums\StatusAngsuran;
use App\Enums\StatusSimpanan;
use App\Enums\StatusVoid;
use App\Models\Angsuran;
use App\Models\Ledger;
use App\Models\PengeluaranKas;
use App\Models\Simpanan;
use App\Models\VoidRequest;
use Illuminate\Support\Facades\DB;

/**
 * VoidService — Service untuk mengelola proses void transaksi.
 *
 * Scope void (rilis pertama):
 * - ✅ Simpanan (semua jenis)
 * - ✅ Angsuran (pembayaran angsuran lunas)
 * - ✅ Pengeluaran Kas
 * - ❌ Pencairan Pinjaman (ditangani manual oleh auditor)
 * - ❌ Penarikan Simpanan/Anggota Keluar (terlalu kompleks untuk v1)
 *
 * Alur:
 * 1. Pengurus memanggil requestVoid() → membuat VoidRequest status menunggu.
 * 2. Pimpinan memanggil approveVoid() → membuat reversal entry + mark transaksi voided.
 * 3. Atau Pimpinan memanggil rejectVoid() → menolak permintaan.
 */
class VoidService
{
    public function __construct(
        private LedgerService $ledger,
        private ActivityLogService $logger,
    ) {}

    /**
     * Validasi apakah sebuah ledger entry bisa di-void.
     *
     * @return array ['can_void' => bool, 'reason' => string|null]
     */
    public function canVoid(Ledger $entry): array
    {
        // 1. Cek apakah sudah pernah di-void
        if ($entry->isVoided()) {
            return ['can_void' => false, 'reason' => 'Transaksi ini sudah pernah di-void.'];
        }

        // 2. Cek apakah sudah ada pending void request
        if ($entry->hasPendingVoidRequest()) {
            return ['can_void' => false, 'reason' => 'Sudah ada permintaan void yang sedang menunggu persetujuan.'];
        }

        // 3. Cek scope: hanya simpanan, angsuran, dan pengeluaran yang bisa di-void
        $allowedCategories = [
            KategoriLedger::Simpanan,
            KategoriLedger::Angsuran,
            KategoriLedger::Pengeluaran,
        ];

        if (!in_array($entry->kategori, $allowedCategories)) {
            return ['can_void' => false, 'reason' => "Kategori '{$entry->kategori->label()}' tidak termasuk dalam cakupan void."];
        }

        // 4. Cek apakah entry ini sendiri adalah void/reversal (tidak bisa void void)
        if ($entry->kategori === KategoriLedger::Void) {
            return ['can_void' => false, 'reason' => 'Entry void/reversal tidak bisa di-void kembali.'];
        }

        return ['can_void' => true, 'reason' => null];
    }

    /**
     * Step 1: Pengurus membuat permintaan void.
     *
     * @param Ledger $entry  Entry ledger yang akan di-void
     * @param string $alasan Alasan void (wajib diisi)
     * @param int    $userId User yang mengajukan (Maker)
     * @return VoidRequest
     * @throws \RuntimeException jika entry tidak bisa di-void
     */
    public function requestVoid(Ledger $entry, string $alasan, int $userId): VoidRequest
    {
        $check = $this->canVoid($entry);
        if (!$check['can_void']) {
            throw new \RuntimeException($check['reason']);
        }

        $voidRequest = VoidRequest::create([
            'ledger_id' => $entry->id,
            'alasan' => $alasan,
            'status' => StatusVoid::Menunggu,
            'diminta_oleh' => $userId,
            'tanggal_permintaan' => now(),
        ]);

        $this->logger->log(
            'void_requested',
            "Permintaan void untuk transaksi {$entry->no_referensi} ({$entry->deskripsi}). Alasan: {$alasan}",
            ['ledger_id' => $entry->id, 'nominal' => $entry->nominal],
        );

        return $voidRequest;
    }

    /**
     * Step 2a: Pimpinan menyetujui permintaan void.
     *
     * Proses:
     * 1. Buat reversal entry di ledger (entry kebalikan).
     * 2. Mark transaksi domain sebagai voided (simpanan/angsuran/pengeluaran).
     * 3. Update void request status → disetujui.
     *
     * @param VoidRequest $voidRequest Permintaan void yang akan disetujui
     * @param int         $userId      User yang menyetujui (Checker/Pimpinan)
     * @param string|null $catatan     Catatan keputusan opsional
     * @return Ledger                  Entry reversal yang dibuat
     * @throws \RuntimeException
     */
    public function approveVoid(VoidRequest $voidRequest, int $userId, ?string $catatan = null): Ledger
    {
        if ($voidRequest->status !== StatusVoid::Menunggu) {
            throw new \RuntimeException('Permintaan void ini sudah diputuskan sebelumnya.');
        }

        return DB::transaction(function () use ($voidRequest, $userId, $catatan) {
            $originalEntry = $voidRequest->ledger;

            // 1. Buat reversal entry di ledger
            $reversalEntry = $this->ledger->buatReversal(
                $originalEntry,
                $voidRequest->alasan,
                $userId,
            );

            // 2. Mark transaksi domain sebagai voided
            $this->markDomainTransaksiVoided($originalEntry);

            // 3. Update void request
            $voidRequest->update([
                'status' => StatusVoid::Disetujui,
                'diputuskan_oleh' => $userId,
                'tanggal_keputusan' => now(),
                'catatan_keputusan' => $catatan,
                'reversal_ledger_id' => $reversalEntry->id,
            ]);

            $this->logger->log(
                'void_approved',
                "Void disetujui untuk transaksi {$originalEntry->no_referensi}. Reversal: {$reversalEntry->no_referensi}. Nominal: Rp " . number_format($originalEntry->nominal, 0, ',', '.'),
                ['void_request_id' => $voidRequest->id],
                ['reversal_ledger_id' => $reversalEntry->id],
                $userId,
            );

            return $reversalEntry;
        });
    }

    /**
     * Step 2b: Pimpinan menolak permintaan void.
     */
    public function rejectVoid(VoidRequest $voidRequest, int $userId, string $catatan): VoidRequest
    {
        if ($voidRequest->status !== StatusVoid::Menunggu) {
            throw new \RuntimeException('Permintaan void ini sudah diputuskan sebelumnya.');
        }

        $voidRequest->update([
            'status' => StatusVoid::Ditolak,
            'diputuskan_oleh' => $userId,
            'tanggal_keputusan' => now(),
            'catatan_keputusan' => $catatan,
        ]);

        $originalEntry = $voidRequest->ledger;

        $this->logger->log(
            'void_rejected',
            "Void ditolak untuk transaksi {$originalEntry->no_referensi}. Alasan penolakan: {$catatan}",
            ['void_request_id' => $voidRequest->id],
            userId: $userId,
        );

        return $voidRequest;
    }

    /**
     * Mark transaksi di tabel domain (simpanan/angsuran/pengeluaran) sebagai voided.
     *
     * Ini mengubah status di tabel asal agar query-query existing
     * (yang menggunakan Eloquent/status filter) bisa mengecualikan
     * transaksi yang sudah di-void.
     */
    private function markDomainTransaksiVoided(Ledger $entry): void
    {
        if (!$entry->transaksi_ref_type || !$entry->transaksi_ref_id) {
            return;
        }

        $modelClass = $entry->transaksi_ref_type;
        $modelId = $entry->transaksi_ref_id;

        switch ($modelClass) {
            case Simpanan::class:
                Simpanan::withTrashed()->where('id', $modelId)->update([
                    'status' => StatusSimpanan::Voided,
                ]);
                break;

            case Angsuran::class:
                $angsuran = Angsuran::withTrashed()->find($modelId);
                if ($angsuran) {
                    // Kembalikan status angsuran ke 'belum' agar bisa dibayar ulang
                    $angsuran->update([
                        'status' => StatusAngsuran::Belum,
                        'tanggal_bayar' => null,
                    ]);

                    // Jika pinjaman sudah lunas (karena ini angsuran terakhir),
                    // kembalikan status pinjaman ke berjalan
                    $pinjaman = $angsuran->pinjaman;
                    if ($pinjaman && $pinjaman->status->value === 'lunas') {
                        $pinjaman->update([
                            'status' => \App\Enums\StatusPinjaman::Berjalan,
                        ]);
                    }
                }
                break;

            case PengeluaranKas::class:
                PengeluaranKas::withTrashed()->where('id', $modelId)->update([
                    'status' => 'voided',
                ]);
                break;
        }
    }

    /**
     * Hitung jumlah void request yang menunggu persetujuan.
     * Digunakan untuk notification badge di UI.
     */
    public function pendingCount(): int
    {
        return VoidRequest::menunggu()->count();
    }
}
