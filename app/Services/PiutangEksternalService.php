<?php

namespace App\Services;

use App\Enums\KategoriLedger;
use App\Enums\TipeLedger;
use App\Models\PembayaranPiutangEksternal;
use App\Models\PiutangEksternal;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PiutangEksternalService
{
    public function __construct(
        private LedgerService $ledgerService,
        private ActivityLogService $logger,
    ) {}

    public function store(array $data, int $userId): PiutangEksternal
    {
        return DB::transaction(function () use ($data, $userId) {
            $piutang = PiutangEksternal::create([
                ...$data,
                'sisa_piutang' => $data['nominal_awal'],
                'nominal_terbayar' => 0,
                'status' => 'aktif',
                'tanggal_catat' => $data['tanggal_catat'] ?? now()->toDateString(),
                'dicatat_oleh' => $userId,
            ]);

            // Jika ada sumber dana (bukan piutang masa lalu), catat ke Ledger sebagai Pengeluaran (Debit)
            if (!empty($data['sumber_dana'])) {
                $this->ledgerService->catat(
                    TipeLedger::Debit,
                    KategoriLedger::PiutangEksternal,
                    $piutang->nominal_awal,
                    "Pencairan Piutang Lain-Lain: {$piutang->nama_peminjam}",
                    [
                        'transaksi_ref_id' => $piutang->id,
                        'transaksi_ref_type' => PiutangEksternal::class,
                        'tanggal_efektif' => $piutang->tanggal_catat,
                        'dicatat_oleh' => $userId,
                    ],
                );
            }

            $this->logger->log(
                'piutang_eksternal_created',
                "Piutang Lain-Lain dicatat: {$piutang->nama_peminjam} — " . format_rupiah($piutang->nominal_awal) . " (Tahun {$piutang->tahun_pinjam})",
                dataBaru: $piutang->toArray(),
            );

            return $piutang;
        });
    }

    /**
     * Update data piutang (hanya data deskriptif, bukan nominal).
     */
    public function update(PiutangEksternal $piutang, array $data): PiutangEksternal
    {
        $dataLama = $piutang->toArray();

        $piutang->update($data);

        $this->logger->log(
            'piutang_eksternal_updated',
            "Data Piutang Lain-Lain diperbarui: {$piutang->nama_peminjam}",
            dataLama: $dataLama,
            dataBaru: $piutang->fresh()->toArray(),
        );

        return $piutang->fresh();
    }

    public function catatPembayaran(
        PiutangEksternal $piutang,
        float $nominal,
        string $tanggalBayar,
        ?string $buktiPath,
        ?string $keterangan,
        ?string $sumberDana,
        int $userId,
    ): PembayaranPiutangEksternal {
        if ($nominal <= 0) {
            throw new \InvalidArgumentException('Nominal pembayaran harus lebih dari 0.');
        }

        if ($nominal > $piutang->sisa_piutang) {
            throw new \InvalidArgumentException('Nominal pembayaran melebihi sisa piutang (' . format_rupiah($piutang->sisa_piutang) . ').');
        }

        return DB::transaction(function () use ($piutang, $nominal, $tanggalBayar, $buktiPath, $keterangan, $sumberDana, $userId) {
            // 1. Simpan record pembayaran
            $pembayaran = PembayaranPiutangEksternal::create([
                'piutang_eksternal_id' => $piutang->id,
                'sumber_dana' => $sumberDana,
                'nominal' => $nominal,
                'tanggal_bayar' => $tanggalBayar,
                'bukti_bayar' => $buktiPath,
                'keterangan' => $keterangan,
                'dicatat_oleh' => $userId,
            ]);

            // 2. Update saldo piutang
            $piutang->increment('nominal_terbayar', $nominal);
            $piutang->decrement('sisa_piutang', $nominal);

            // 3. Cek apakah lunas
            $piutang->refresh();
            if ($piutang->sisa_piutang <= 0) {
                $piutang->update(['status' => 'lunas']);
            }

            // 4. Catat ke Ledger — KREDIT (uang masuk ke kas koperasi)
            $this->ledgerService->catat(
                TipeLedger::Kredit,
                KategoriLedger::PiutangEksternal,
                $nominal,
                "Pembayaran Piutang Lain-Lain: {$piutang->nama_peminjam} (Tahun {$piutang->tahun_pinjam})",
                [
                    'transaksi_ref_id' => $pembayaran->id,
                    'transaksi_ref_type' => PembayaranPiutangEksternal::class,
                    'tanggal_efektif' => $tanggalBayar,
                    'dicatat_oleh' => $userId,
                ],
            );

            // 5. Log
            $this->logger->log(
                'piutang_eksternal_payment',
                "Pembayaran Piutang Lain-Lain: {$piutang->nama_peminjam} sebesar " . format_rupiah($nominal) . ". Sisa: " . format_rupiah($piutang->sisa_piutang),
                dataBaru: $pembayaran->toArray(),
            );

            return $pembayaran;
        });
    }

    /**
     * Hapus pembayaran (reverse).
     */
    public function hapusPembayaran(PembayaranPiutangEksternal $pembayaran, int $userId): void
    {
        DB::transaction(function () use ($pembayaran, $userId) {
            $piutang = $pembayaran->piutangEksternal;

            // 1. Kembalikan saldo
            $piutang->decrement('nominal_terbayar', $pembayaran->nominal);
            $piutang->increment('sisa_piutang', $pembayaran->nominal);

            // 2. Jika statusnya lunas, kembalikan ke aktif
            $piutang->refresh();
            if ($piutang->status === 'lunas' && $piutang->sisa_piutang > 0) {
                $piutang->update(['status' => 'aktif']);
            }

            // 3. Void entry ledger terkait
            $ledgerEntry = $this->ledgerService->findByTransaksi(
                PembayaranPiutangEksternal::class,
                $pembayaran->id
            );
            if ($ledgerEntry) {
                $this->ledgerService->buatReversal($ledgerEntry, 'Pembatalan pembayaran piutang lain-lain', $userId);
            }

            // 4. Hapus bukti file jika ada
            if ($pembayaran->bukti_bayar && Storage::exists($pembayaran->bukti_bayar)) {
                Storage::delete($pembayaran->bukti_bayar);
            }

            // 5. Log & delete
            $this->logger->log(
                'piutang_eksternal_payment_deleted',
                "Pembayaran Piutang Lain-Lain dibatalkan: {$piutang->nama_peminjam} sebesar " . format_rupiah($pembayaran->nominal),
                dataLama: $pembayaran->toArray(),
            );

            $pembayaran->delete();
        });
    }

    /**
     * Total sisa piutang eksternal (untuk neraca).
     */
    public function totalSisaPiutang(): float
    {
        return (float) PiutangEksternal::where('status', 'aktif')->sum('sisa_piutang');
    }

    /**
     * Total sisa piutang grouped by tahun.
     */
    public function sisaPerTahun(): \Illuminate\Support\Collection
    {
        return PiutangEksternal::where('status', 'aktif')
            ->selectRaw('tahun_pinjam, SUM(sisa_piutang) as total_sisa, COUNT(*) as jumlah')
            ->groupBy('tahun_pinjam')
            ->orderBy('tahun_pinjam')
            ->get();
    }
}
