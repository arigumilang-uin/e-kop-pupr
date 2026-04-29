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
    /**
     * Registry: mapping sumber_data ke query-nya.
     * Setiap entry = Closure(int $tahun): float
     */
    private function getDataResolvers(): array
    {
        return [
            // Bunga TEREALISASI: hanya dari angsuran yang sudah lunas di tahun tersebut
            'bunga_pinjaman' => fn(int $tahun) =>
                (float) DB::table('angsuran')
                    ->join('pinjaman', 'angsuran.pinjaman_id', '=', 'pinjaman.id')
                    ->where('angsuran.status', 'lunas')
                    ->whereYear('angsuran.tanggal_bayar', $tahun)
                    ->sum('angsuran.nominal_bunga'),

            'dana_resiko' => fn(int $tahun) =>
                (float) DB::table('pinjaman')
                    ->whereIn('status', ['berjalan', 'lunas'])
                    ->whereYear('tanggal_approval', $tahun)
                    ->sum('potongan_dana_resiko'),

            'biaya_admin' => fn(int $tahun) =>
                (float) DB::table('pinjaman')
                    ->whereIn('status', ['berjalan', 'lunas'])
                    ->whereYear('tanggal_approval', $tahun)
                    ->sum('potongan_biaya_admin'),

            'pengeluaran_kas' => fn(int $tahun) =>
                (float) PengeluaranKas::whereYear('tanggal', $tahun)->sum('nominal'),

            'simpanan_swp' => fn(int $tahun) =>
                (float) DB::table('simpanan')
                    ->join('jenis_simpanan', 'simpanan.jenis_simpanan_id', '=', 'jenis_simpanan.id')
                    ->where('jenis_simpanan.kode', 'SWP')
                    ->whereYear('simpanan.tanggal', $tahun)
                    ->sum('simpanan.nominal'),
        ];
    }

    /**
     * Ambil nominal dari suatu sumber_data untuk tahun tertentu.
     */
    public function resolveNominal(string $sumberData, int $tahun): float
    {
        $resolvers = $this->getDataResolvers();

        if (isset($resolvers[$sumberData])) {
            return $resolvers[$sumberData]($tahun);
        }

        return 0;
    }

    /**
     * Hitung seluruh SHU berdasarkan komponen aktif.
     */
    public function hitung(int $tahun): array
    {
        $komponenAktif = ShuKomponen::aktif()->orderBy('tipe')->orderBy('urutan')->get();
        $distribusiAktif = ShuDistribusi::aktif()->orderBy('urutan')->get();

        // Hitung setiap komponen
        $pendapatanItems = [];
        $bebanItems = [];
        $totalPendapatan = 0;
        $totalBeban = 0;

        foreach ($komponenAktif as $k) {
            $nominal = $this->resolveNominal($k->sumber_data, $tahun);

            $item = [
                'id' => $k->id,
                'nama' => $k->nama,
                'sumber_data' => $k->sumber_data,
                'nominal' => $nominal,
            ];

            if ($k->tipe === 'pendapatan') {
                $pendapatanItems[] = $item;
                $totalPendapatan += $nominal;
            } else {
                $bebanItems[] = $item;
                $totalBeban += $nominal;
            }
        }

        $shuBersih = $totalPendapatan - $totalBeban;
        $shuDibagi = $shuBersih > 0 ? $shuBersih : 0;

        // Hitung distribusi
        $distribusiItems = [];
        $totalPersenDistribusi = 0;
        foreach ($distribusiAktif as $d) {
            $distribusiItems[] = [
                'id' => $d->id,
                'nama' => $d->nama,
                'persen' => $d->persen,
                'nominal' => $shuDibagi * ($d->persen / 100),
                'deskripsi' => $d->deskripsi,
            ];
            $totalPersenDistribusi += $d->persen;
        }

        return [
            'tahun' => $tahun,
            'pendapatan_items' => $pendapatanItems,
            'beban_items' => $bebanItems,
            'total_pendapatan' => $totalPendapatan,
            'total_beban' => $totalBeban,
            'shu_bersih' => $shuBersih,
            'distribusi_items' => $distribusiItems,
            'total_persen_distribusi' => $totalPersenDistribusi,
        ];
    }

    /**
     * Daftar semua sumber data yang tersedia untuk dipilih pengurus.
     */
    public function sumberDataTersedia(): array
    {
        return [
            'bunga_pinjaman'  => 'Pendapatan Bunga Pinjaman',
            'dana_resiko'     => 'Pendapatan Dana Resiko (1.5%)',
            'biaya_admin'     => 'Pendapatan Biaya Admin (0.5%)',
            'pengeluaran_kas' => 'Beban Pengeluaran Kas Manual',
            'simpanan_swp'    => 'Simpanan Wajib Pinjam (SWP)',
        ];
    }
}
