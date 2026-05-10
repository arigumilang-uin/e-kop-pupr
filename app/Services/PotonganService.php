<?php

namespace App\Services;

use App\Enums\StatusAngsuran;
use App\Enums\StatusPinjaman;
use App\Models\Anggota;
use App\Models\JenisSimpanan;
use App\Models\Simpanan;
use App\Models\Angsuran;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PotonganService
{
    public function __construct(
        private PengaturanService $pengaturan,
        private ActivityLogService $logger,
        private LedgerService $ledgerService,
    ) {}

    /**
     * Proses potongan TPP bulanan massal dengan chunking.
     *
     * @return int jumlah anggota yang diproses
     */
    public function proses(int $month, int $year, int $userId): int
    {
        $jenisPokok = JenisSimpanan::pokok();
        $jenisWajib = JenisSimpanan::wajib();
        $nominalPokok = $this->pengaturan->simpananPokok();
        $nominalWajib = $this->pengaturan->simpananWajib($month, $year);
        $periodeFilter = Carbon::createFromDate($year, $month, 1);

        $countProcessed = 0;

        // Satu transaksi atomik membungkus seluruh chunk.
        // Jika error di batch manapun → seluruh operasi rollback.
        DB::transaction(function () use (
            $jenisPokok, $jenisWajib, $nominalPokok, $nominalWajib,
            $month, $year, $userId, $periodeFilter, &$countProcessed
        ) {
            Anggota::aktif()->chunk(50, function ($anggotas) use (
                $jenisPokok, $jenisWajib, $nominalPokok, $nominalWajib,
                $month, $year, $userId, $periodeFilter, &$countProcessed
            ) {
                foreach ($anggotas as $anggota) {
                    $this->prosesPerAnggota(
                        $anggota, $jenisPokok, $jenisWajib,
                        $nominalPokok, $nominalWajib,
                        $month, $year, $userId, $periodeFilter
                    );
                    $countProcessed++;
                }
            });
        });

        $this->logger->log(
            'tpp_processed',
            "Pemrosesan TPP Massal berhasil untuk periode $month/$year. Total $countProcessed anggota diproses.",
            ['month' => $month, 'year' => $year]
        );

        return $countProcessed;
    }

    /**
     * Proses potongan untuk 1 anggota.
     */
    private function prosesPerAnggota(
        Anggota $anggota,
        ?JenisSimpanan $jenisPokok,
        ?JenisSimpanan $jenisWajib,
        float $nominalPokok,
        float $nominalWajib,
        int $month,
        int $year,
        int $userId,
        Carbon $periodeFilter,
    ): void {
        // Cek kelayakan periode berdasar tanggal masuk
        $tanggalMasuk = $anggota->tanggal_masuk;
        $bulanMulaiPotongan = $tanggalMasuk ? $tanggalMasuk->copy()->addMonth()->startOfMonth() : null;
        $belumWaktunyaDipotong = $bulanMulaiPotongan && $periodeFilter->lt($bulanMulaiPotongan);

        // 1. Simpanan Pokok
        if ($jenisPokok) {
            $sudahBayarPokok = DB::table('simpanan')
                ->where('anggota_id', $anggota->id)
                ->where('jenis_simpanan_id', $jenisPokok->id)
                ->whereNull('deleted_at')
                ->where(function ($q) {
                    $q->where('status', 'aktif')->orWhereNull('status');
                })
                ->exists();

            if (!$sudahBayarPokok && !$belumWaktunyaDipotong) {
                $simpanan = Simpanan::create([
                    'anggota_id' => $anggota->id,
                    'jenis_simpanan_id' => $jenisPokok->id,
                    'nominal' => $nominalPokok,
                    'tanggal' => now(),
                    'bulan_untuk' => $month,
                    'tahun_untuk' => $year,
                    'keterangan' => "Potongan TPP Masal (Pokok) - Periode $month/$year",
                    'dicatat_oleh' => $userId,
                ]);

                $this->ledgerService->catatSimpanan(
                    $nominalPokok,
                    $simpanan->id,
                    $anggota->id,
                    $jenisPokok->nama,
                    now()->toDateString(),
                    $userId,
                );
            }
        }

        // 2. Simpanan Wajib
        if ($jenisWajib) {
            // Validasi duplikat: hanya cek simpanan aktif (menggantikan unique constraint)
            $sudahBayarWajib = DB::table('simpanan')
                ->where('anggota_id', $anggota->id)
                ->where('jenis_simpanan_id', $jenisWajib->id)
                ->where('bulan_untuk', $month)
                ->where('tahun_untuk', $year)
                ->whereNull('deleted_at')
                ->where(function ($q) {
                    $q->where('status', 'aktif')->orWhereNull('status');
                })
                ->exists();

            if (!$sudahBayarWajib && !$belumWaktunyaDipotong) {
                $simpanan = Simpanan::create([
                    'anggota_id' => $anggota->id,
                    'jenis_simpanan_id' => $jenisWajib->id,
                    'nominal' => $nominalWajib,
                    'tanggal' => now(),
                    'bulan_untuk' => $month,
                    'tahun_untuk' => $year,
                    'keterangan' => "Potongan TPP Masal (Wajib) - Periode $month/$year",
                    'dicatat_oleh' => $userId,
                ]);

                $this->ledgerService->catatSimpanan(
                    $nominalWajib,
                    $simpanan->id,
                    $anggota->id,
                    $jenisWajib->nama,
                    now()->toDateString(),
                    $userId,
                );
            }
        }

        // 3. Angsuran Pinjaman
        $angsurans = Angsuran::join('pinjaman', 'angsuran.pinjaman_id', '=', 'pinjaman.id')
            ->where('pinjaman.anggota_id', $anggota->id)
            ->where('angsuran.status', StatusAngsuran::Belum)
            ->whereMonth('angsuran.tanggal_jatuh_tempo', $month)
            ->whereYear('angsuran.tanggal_jatuh_tempo', $year)
            ->select('angsuran.*')
            ->get();

        foreach ($angsurans as $angs) {
            $angs->update([
                'status' => StatusAngsuran::Lunas,
                'tanggal_bayar' => now(),
            ]);

            // Tulis angsuran ke Ledger
            $pinjaman = $angs->pinjaman;
            $this->ledgerService->catatAngsuran(
                (float) $angs->nominal_total,
                $angs->id,
                $anggota->id,
                $angs->angsuran_ke,
                $pinjaman->no_referensi ?? '-',
                now()->toDateString(),
                $userId,
            );

            if ($pinjaman->angsuran()->where('status', StatusAngsuran::Belum)->count() === 0) {
                $pinjaman->update(['status' => StatusPinjaman::Lunas]);
            }
        }
    }
}
