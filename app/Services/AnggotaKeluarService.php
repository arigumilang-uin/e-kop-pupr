<?php

namespace App\Services;

use App\Enums\StatusAnggota;
use App\Models\Anggota;
use App\Models\ArsipKeluarAnggota;
use App\Models\PenarikanSimpanan;
use Illuminate\Support\Facades\DB;

/**
 * Service untuk mengelola proses keluarnya anggota dari koperasi.
 *
 * Flow:
 * 1. Cek pinjaman aktif → jika ada, tolak proses
 * 2. Hitung total simpanan anggota (per jenis)
 * 3. Cek kecukupan saldo kas koperasi
 * 4. Jika cukup → proses pengembalian seluruh simpanan
 * 5. Arsipkan data keluar (termasuk nominal wajib setor ulang)
 * 6. Update status anggota → Nonaktif
 */
class AnggotaKeluarService
{
    public function __construct(
        private SaldoService $saldoService,
        private LedgerService $ledgerService,
    ) {}

    /**
     * Pra-validasi: analisis kelayakan anggota keluar.
     * Mengembalikan array status dan detail.
     */
    public function analisis(Anggota $anggota): array
    {
        // 1. Cek pinjaman aktif
        $pinjamanAktif = $anggota->pinjaman()
            ->whereIn('status', ['berjalan', 'menunggu'])
            ->get();

        $hasPinjamanAktif = $pinjamanAktif->isNotEmpty();

        // 2. Hitung total simpanan per jenis
        $simpananPerJenis = DB::table('simpanan')
            ->join('jenis_simpanan', 'simpanan.jenis_simpanan_id', '=', 'jenis_simpanan.id')
            ->where('simpanan.anggota_id', $anggota->id)
            ->whereNull('simpanan.deleted_at')
            ->where(function ($q) { $q->where('simpanan.status', 'aktif')->orWhereNull('simpanan.status'); })
            ->select(
                'jenis_simpanan.id as jenis_id',
                'jenis_simpanan.kode',
                'jenis_simpanan.nama',
                DB::raw('SUM(simpanan.nominal) as total')
            )
            ->groupBy('jenis_simpanan.id', 'jenis_simpanan.kode', 'jenis_simpanan.nama')
            ->get();

        // Kurangi penarikan yang sudah dilakukan
        $penarikanPerJenis = DB::table('penarikan_simpanan')
            ->where('anggota_id', $anggota->id)
            ->select('jenis_simpanan_id', DB::raw('SUM(nominal) as total'))
            ->groupBy('jenis_simpanan_id')
            ->pluck('total', 'jenis_simpanan_id');

        $rincian = [];
        $totalDikembalikan = 0;

        foreach ($simpananPerJenis as $s) {
            $penarikan = (float) ($penarikanPerJenis[$s->jenis_id] ?? 0);
            $neto = max(0, (float) $s->total - $penarikan);

            $rincian[] = [
                'jenis_id' => $s->jenis_id,
                'kode' => $s->kode,
                'nama' => $s->nama,
                'bruto' => (float) $s->total,
                'sudah_ditarik' => $penarikan,
                'neto' => $neto,
            ];

            $totalDikembalikan += $neto;
        }

        // 3. Cek kecukupan saldo
        $saldoKas = $this->saldoService->saldoKoperasi();
        $saldoCukup = $saldoKas >= $totalDikembalikan;

        // 4. Cek arsip keluar sebelumnya (untuk pendaftar ulang)
        $arsipSebelumnya = ArsipKeluarAnggota::where('anggota_id', $anggota->id)
            ->latest()
            ->first();

        return [
            'anggota' => $anggota,
            'has_pinjaman_aktif' => $hasPinjamanAktif,
            'pinjaman_aktif' => $pinjamanAktif,
            'rincian_simpanan' => $rincian,
            'total_dikembalikan' => $totalDikembalikan,
            'saldo_kas' => $saldoKas,
            'saldo_cukup' => $saldoCukup,
            'defisit' => $saldoCukup ? 0 : ($totalDikembalikan - $saldoKas),
            'arsip_sebelumnya' => $arsipSebelumnya,
            'bisa_proses' => !$hasPinjamanAktif && $saldoCukup && $totalDikembalikan > 0,
        ];
    }

    /**
     * Eksekusi proses pengeluaran anggota dari koperasi.
     * Hanya boleh dipanggil jika analisis().bisa_proses == true.
     */
    public function proses(Anggota $anggota, int $userId, ?string $catatan = null): ArsipKeluarAnggota
    {
        $analisis = $this->analisis($anggota);

        if (!$analisis['bisa_proses']) {
            throw new \RuntimeException('Anggota tidak dapat diproses keluar. Cek pinjaman aktif atau kecukupan saldo.');
        }

        return DB::transaction(function () use ($anggota, $userId, $catatan, $analisis) {
            $tanggalKeluar = now()->toDateString();
            $rincianJson = [];

            // 1. Buat record penarikan_simpanan untuk setiap jenis (agar tercatat di arus kas)
            foreach ($analisis['rincian_simpanan'] as $item) {
                if ($item['neto'] <= 0) continue;

                $penarikan = PenarikanSimpanan::create([
                    'anggota_id' => $anggota->id,
                    'jenis_simpanan_id' => $item['jenis_id'],
                    'nominal' => $item['neto'],
                    'tanggal' => $tanggalKeluar,
                    'keterangan' => "Pengembalian {$item['nama']} — Anggota keluar dari koperasi",
                    'diproses_oleh' => $userId,
                ]);

                // Tulis penarikan ke Ledger
                $this->ledgerService->catatPenarikan(
                    $item['neto'],
                    $penarikan->id,
                    $anggota->id,
                    $item['nama'],
                    $tanggalKeluar,
                    $userId,
                );

                $rincianJson[] = [
                    'kode' => $item['kode'],
                    'nama' => $item['nama'],
                    'nominal' => $item['neto'],
                ];
            }

            // 2. Simpan arsip keluar
            $arsip = ArsipKeluarAnggota::create([
                'anggota_id' => $anggota->id,
                'tanggal_keluar' => $tanggalKeluar,
                'total_simpanan_dikembalikan' => $analisis['total_dikembalikan'],
                'rincian_simpanan' => $rincianJson,
                'nominal_wajib_setor_ulang' => $analisis['total_dikembalikan'],
                'catatan' => $catatan,
                'diproses_oleh' => $userId,
            ]);

            // 3. Update status anggota
            $anggota->update([
                'status' => StatusAnggota::Nonaktif,
                'tanggal_keluar' => $tanggalKeluar,
            ]);

            return $arsip;
        });
    }
}
