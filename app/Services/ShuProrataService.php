<?php

namespace App\Services;

use App\Models\Anggota;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Service untuk menghitung distribusi prorata SHU ke masing-masing anggota.
 *
 * Dua jenis jasa yang dibagikan:
 *   1. Jasa Modal   → Proporsi berdasarkan simpanan anggota vs total simpanan
 *   2. Jasa Usaha   → Proporsi berdasarkan bunga pinjaman anggota vs total bunga
 *
 * Rumus:
 *   Jasa Modal Anggota i  = (Simpanan_i / Σ Simpanan) × Dana Jasa Modal
 *   Jasa Usaha Anggota i  = (Bunga_i   / Σ Bunga)    × Dana Jasa Usaha
 *
 * Catatan penting:
 *   - Simpanan yang dihitung = Simpanan Neto (setor - tarik) dari jenis POKOK, WAJIB, SWP
 *   - Bunga yang dihitung = total nominal_bunga yang STATUS-nya 'lunas' di tabel angsuran
 *     pada tahun berjalan SHU
 *   - Anggota non-aktif tetap dihitung jika masih memiliki saldo simpanan/bunga di tahun tsb
 */
class ShuProrataService
{
    /**
     * Hitung distribusi SHU per anggota.
     *
     * @param  float  $danaJasaModal   Total dana "Jasa Modal" dari ShuService
     * @param  float  $danaJasaUsaha   Total dana "Jasa Usaha/Anggota" dari ShuService
     * @param  int    $tahun           Tahun SHU yang dihitung
     * @return array  ['ringkasan' => [...], 'detail' => Collection]
     */
    public function hitung(float $danaJasaModal, float $danaJasaUsaha, int $tahun): array
    {
        // 1. Hitung simpanan neto per anggota (Pokok + Wajib + SWP minus penarikan)
        $simpananPerAnggota = $this->hitungSimpananPerAnggota();
        $totalSimpanan = $simpananPerAnggota->sum('total_simpanan');

        // 2. Hitung bunga terbayar per anggota di tahun tersebut
        $bungaPerAnggota = $this->hitungBungaPerAnggota($tahun);
        $totalBunga = $bungaPerAnggota->sum('total_bunga');

        // 3. Gabungkan data anggota
        $semuaAnggotaIds = $simpananPerAnggota->pluck('anggota_id')
            ->merge($bungaPerAnggota->pluck('anggota_id'))
            ->unique();

        // 4. Ambil data nama anggota
        $anggotaMap = Anggota::whereIn('id', $semuaAnggotaIds)
            ->get()
            ->keyBy('id');

        // 5. Hitung prorata per anggota
        $detail = $semuaAnggotaIds->map(function ($anggotaId) use (
            $simpananPerAnggota, $bungaPerAnggota,
            $totalSimpanan, $totalBunga,
            $danaJasaModal, $danaJasaUsaha,
            $anggotaMap
        ) {
            $anggota = $anggotaMap->get($anggotaId);
            if (!$anggota) {
                return null;
            }

            $simpananAnggota = $simpananPerAnggota->firstWhere('anggota_id', $anggotaId);
            $bungaAnggota = $bungaPerAnggota->firstWhere('anggota_id', $anggotaId);

            $nilaiSimpanan = $simpananAnggota ? (float) $simpananAnggota->total_simpanan : 0;
            $nilaiBunga = $bungaAnggota ? (float) $bungaAnggota->total_bunga : 0;

            // Proporsi
            $proporsiModal = $totalSimpanan > 0 ? ($nilaiSimpanan / $totalSimpanan) : 0;
            $proporsiUsaha = $totalBunga > 0 ? ($nilaiBunga / $totalBunga) : 0;

            // Hasil
            $jasaModal = round($danaJasaModal * $proporsiModal, 2);
            $jasaUsaha = round($danaJasaUsaha * $proporsiUsaha, 2);

            return (object) [
                'anggota_id'       => $anggotaId,
                'nip'              => $anggota->nip,
                'nama'             => $anggota->nama,
                'simpanan'         => $nilaiSimpanan,
                'proporsi_modal'   => round($proporsiModal * 100, 4),
                'jasa_modal'       => $jasaModal,
                'bunga_dibayar'    => $nilaiBunga,
                'proporsi_usaha'   => round($proporsiUsaha * 100, 4),
                'jasa_usaha'       => $jasaUsaha,
                'total_shu'        => $jasaModal + $jasaUsaha,
            ];
        })->filter()->sortByDesc('total_shu')->values();

        return [
            'ringkasan' => [
                'tahun'               => $tahun,
                'dana_jasa_modal'     => $danaJasaModal,
                'dana_jasa_usaha'     => $danaJasaUsaha,
                'total_simpanan'      => $totalSimpanan,
                'total_bunga'         => $totalBunga,
                'jumlah_penerima'     => $detail->count(),
                'total_terdistribusi' => $detail->sum('total_shu'),
            ],
            'detail' => $detail,
        ];
    }

    /**
     * Hitung simpanan neto per anggota (Pokok + Wajib + SWP - Penarikan).
     * Kode simpanan yang masuk hitungan modal: POKOK, WAJIB, SWP.
     */
    private function hitungSimpananPerAnggota(): Collection
    {
        $kodeModal = ['POKOK', 'WAJIB', 'SWP'];

        $setoran = DB::table('simpanan')
            ->join('jenis_simpanan', 'simpanan.jenis_simpanan_id', '=', 'jenis_simpanan.id')
            ->whereIn('jenis_simpanan.kode', $kodeModal)
            ->whereNull('simpanan.deleted_at')
            ->select('simpanan.anggota_id', DB::raw('SUM(simpanan.nominal) as total'))
            ->groupBy('simpanan.anggota_id')
            ->get()
            ->keyBy('anggota_id');

        $penarikan = DB::table('penarikan_simpanan')
            ->join('jenis_simpanan', 'penarikan_simpanan.jenis_simpanan_id', '=', 'jenis_simpanan.id')
            ->whereIn('jenis_simpanan.kode', $kodeModal)
            ->select('penarikan_simpanan.anggota_id', DB::raw('SUM(penarikan_simpanan.nominal) as total'))
            ->groupBy('penarikan_simpanan.anggota_id')
            ->get()
            ->keyBy('anggota_id');

        return $setoran->map(function ($s) use ($penarikan) {
            $tarik = $penarikan->get($s->anggota_id);
            $s->total_simpanan = (float) $s->total - ($tarik ? (float) $tarik->total : 0);
            return $s;
        })->filter(fn($s) => $s->total_simpanan > 0)->values();
    }

    /**
     * Hitung total bunga angsuran yang sudah dibayar (lunas) per anggota di tahun tertentu.
     */
    private function hitungBungaPerAnggota(int $tahun): Collection
    {
        return DB::table('angsuran')
            ->join('pinjaman', 'angsuran.pinjaman_id', '=', 'pinjaman.id')
            ->where('angsuran.status', 'lunas')
            ->whereYear('angsuran.tanggal_bayar', $tahun)
            ->select('pinjaman.anggota_id', DB::raw('SUM(angsuran.nominal_bunga) as total_bunga'))
            ->groupBy('pinjaman.anggota_id')
            ->get();
    }
}
