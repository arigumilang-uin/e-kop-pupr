<?php

namespace App\Services;

use App\Models\ShuKomponen;
use App\Models\ShuDistribusi;
use App\Models\PengeluaranKas;
use Illuminate\Support\Facades\DB;

/**
 * Service untuk menghitung SHU secara dinamis berdasarkan komponen
 * yang dikonfigurasi oleh pengurus melalui panel admin.
 *
 * Sumber data yang didukung (sumber_data field):
 *   - bunga_pinjaman     → total_bunga dari pinjaman approved tahun tsb
 *   - dana_resiko        → potongan_dana_resiko dari pinjaman approved tahun tsb
 *   - biaya_admin        → potongan_biaya_admin dari pinjaman approved tahun tsb
 *   - pengeluaran_kas    → total pengeluaran manual tahun tsb
 *   - simpanan_swp       → total simpanan SWP yang tercatat tahun tsb
 *
 * Pengurus bisa menambah sumber_data baru di masa depan.
 */
class ShuService
{
    public function hitung(int $tahun): array
    {
        // 1. Ambil Laba/Rugi dari Laporan PHU Utama
        $phu = app(PhuService::class)->hitung($tahun);

        $pendapatanItems = $phu['pendapatan']['items'];
        $bebanItems      = $phu['beban']['items'];
        $totalPendapatan = $phu['pendapatan']['total'];
        $totalBeban      = $phu['beban']['total'];
        $shuBersih       = $phu['shu_bersih'];

        $shuDibagi = $shuBersih > 0 ? $shuBersih : 0;

        // 2. Hitung distribusi dari tabel ShuDistribusi
        $distribusiAktif = ShuDistribusi::aktif()->orderBy('urutan')->get();
        
        $distribusiItems = [];
        $totalPersenDistribusi = 0;
        foreach ($distribusiAktif as $d) {
            $distribusiItems[] = [
                'id'           => $d->id,
                'nama'         => $d->nama,
                'persen'       => $d->persen,
                'tipe_routing' => $d->tipe_routing,
                'nominal'      => $shuDibagi * ($d->persen / 100),
                'deskripsi'    => $d->deskripsi,
            ];
            $totalPersenDistribusi += $d->persen;
        }

        return [
            'tahun'                   => $tahun,
            'pendapatan_items'        => $pendapatanItems,
            'beban_items'             => $bebanItems,
            'total_pendapatan'        => $totalPendapatan,
            'total_beban'             => $totalBeban,
            'shu_bersih'              => $shuBersih,
            'distribusi_items'        => $distribusiItems,
            'total_persen_distribusi' => $totalPersenDistribusi,
        ];
    }
}
