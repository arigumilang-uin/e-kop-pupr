<?php

namespace App\Services;

use App\Models\Pinjaman;
use Illuminate\Support\Facades\DB;

class LaporanService
{
    public function __construct(
        private SaldoService $saldo,
        private PiutangEksternalService $piutangEksternalService,
        private NeracaService $neracaService,
    ) {}

    /**
     * Hitung ringkasan keuangan global.
     */
    public function ringkasan(): array
    {
        $saldoKoperasi = $this->saldo->saldoKoperasi();
        $totalSimpanan = (float) DB::table('simpanan')
            ->whereNull('deleted_at')
            ->where(function ($q) { $q->where('status', 'aktif')->orWhereNull('status'); })
            ->sum('nominal');
        $totalPenarikan = (float) DB::table('penarikan_simpanan')->sum('nominal');
        $simpananBersih = $totalSimpanan - $totalPenarikan;

        // Piutang = Sisa Pokok saja (cash basis: bunga diakui saat dibayar)
        $totalPokokPinjamanAktif = (float) DB::table('pinjaman')->where('status', 'berjalan')->sum('nominal_pinjaman');
        $angsuranPokokTerbayar = (float) DB::table('angsuran')
            ->join('pinjaman', 'angsuran.pinjaman_id', '=', 'pinjaman.id')
            ->where('pinjaman.status', 'berjalan')
            ->where('angsuran.status', 'lunas')
            ->sum('angsuran.nominal_pokok');
        $piutangBerjalan = $totalPokokPinjamanAktif - $angsuranPokokTerbayar;

        $piutangLain = $this->piutangEksternalService->totalSisaPiutang();

        $neraca = $this->neracaService->hitung(now()->year);
        $totalAset = $neraca['aktiva_total'];

        // Breakdown Kas
        $masukSimpanan = $totalSimpanan;
        $masukAngsuran = (float) DB::table('angsuran')->where('status', 'lunas')->sum(DB::raw('nominal_pokok + nominal_bunga'));
        $masukPiutangLain = (float) DB::table('pembayaran_piutang_eksternal')->sum('nominal');
        $masukFee = (float) DB::table('pinjaman')->whereIn('status', ['berjalan', 'lunas'])->sum(DB::raw('potongan_dana_resiko + potongan_biaya_admin'));
        $keluarPinjaman = (float) DB::table('pinjaman')->whereIn('status', ['berjalan', 'lunas'])->sum('nominal_pinjaman');
        $keluarTarik = $totalPenarikan;
        $keluarPengeluaranKas = (float) DB::table('pengeluaran_kas')
            ->whereNull('deleted_at')
            ->where(function ($q) { $q->where('status', 'aktif')->orWhereNull('status'); })
            ->sum('nominal');
        $keluarRealisasiShu = (float) DB::table('shu_realisasi_kewajiban')->sum('nominal');

        // Breakdown Simpanan per Jenis (Neto)
        $simpananPerJenis = $this->simpananPerJenisNeto();

        return compact(
            'saldoKoperasi', 'totalSimpanan', 'simpananBersih', 'piutangBerjalan', 'piutangLain', 'totalAset',
            'masukSimpanan', 'masukAngsuran', 'masukPiutangLain', 'masukFee', 'keluarPinjaman', 'keluarTarik', 'keluarPengeluaranKas', 'keluarRealisasiShu',
            'simpananPerJenis', 'totalPenarikan'
        );
    }

    /**
     * Hitung simpanan neto per jenis (setor - tarik).
     */
    public function simpananPerJenisNeto(): \Illuminate\Support\Collection
    {
        $setorPerJenis = DB::table('simpanan')
            ->join('jenis_simpanan', 'simpanan.jenis_simpanan_id', '=', 'jenis_simpanan.id')
            ->whereNull('simpanan.deleted_at')
            ->where(function ($q) { $q->where('simpanan.status', 'aktif')->orWhereNull('simpanan.status'); })
            ->select('jenis_simpanan.nama', 'jenis_simpanan.kode', DB::raw('SUM(simpanan.nominal) as total'))
            ->groupBy('jenis_simpanan.nama', 'jenis_simpanan.kode')
            ->get()->keyBy('kode');

        $tarikPerJenis = DB::table('penarikan_simpanan')
            ->join('jenis_simpanan', 'penarikan_simpanan.jenis_simpanan_id', '=', 'jenis_simpanan.id')
            ->select('jenis_simpanan.kode', DB::raw('SUM(penarikan_simpanan.nominal) as total'))
            ->groupBy('jenis_simpanan.kode')
            ->get()->keyBy('kode');

        return $setorPerJenis->map(function ($item) use ($tarikPerJenis) {
            $tarik = isset($tarikPerJenis[$item->kode]) ? (float) $tarikPerJenis[$item->kode]->total : 0;
            $item->total = (float) $item->total - $tarik;
            return $item;
        })->filter(fn($item) => $item->total > 0)->values();
    }

    /**
     * Ambil detail piutang per anggota (pinjaman berjalan).
     */
    public function piutangAnggota(): array
    {
        $piutangAnggota = Pinjaman::with(['anggota.bidang', 'angsuran'])
            ->where('status', 'berjalan')
            ->orderBy('tanggal_approval', 'desc')
            ->get()
            ->map(function ($p) {
                $lunas = $p->angsuran->where('status.value', 'lunas');
                $belum = $p->angsuran->where('status.value', 'belum');

                return (object) [
                    'pinjaman' => $p,
                    'anggota' => $p->anggota,
                    'total_tagihan' => (float) $p->total_bayar,
                    'total_dibayar' => (float) $lunas->sum('nominal_total'),
                    'total_belum' => (float) $belum->sum('nominal_total'),
                    'total_belum_pokok' => (float) $belum->sum('nominal_pokok'),
                    'total_belum_bunga' => (float) $belum->sum('nominal_bunga'),
                    'angsuran_lunas' => $lunas->count(),
                    'angsuran_belum' => $belum->count(),
                    'tenor' => $p->tenor_bulan,
                    'mulai' => $p->tanggal_approval,
                    'estimasi_lunas' => $p->angsuran->sortByDesc('tanggal_jatuh_tempo')->first()?->tanggal_jatuh_tempo,
                    'progress' => $p->tenor_bulan > 0 ? round(($lunas->count() / $p->tenor_bulan) * 100) : 0,
                ];
            });

        $piutangGrouped = $piutangAnggota->groupBy(fn($item) => $item->anggota->id);

        return compact('piutangAnggota', 'piutangGrouped');
    }

    /**
     * Ambil detail simpanan per anggota (neto).
     */
    public function simpananAnggota(): array
    {
        $simpananAnggota = DB::table('simpanan')
            ->join('anggota', 'simpanan.anggota_id', '=', 'anggota.id')
            ->join('jenis_simpanan', 'simpanan.jenis_simpanan_id', '=', 'jenis_simpanan.id')
            ->whereNull('simpanan.deleted_at')
            ->where(function ($q) { $q->where('simpanan.status', 'aktif')->orWhereNull('simpanan.status'); })
            ->select(
                'anggota.id as anggota_id', 'anggota.nip', 'anggota.nama',
                'anggota.status as status_anggota',
                'jenis_simpanan.id as jenis_id', 'jenis_simpanan.kode as jenis_kode',
                'jenis_simpanan.nama as jenis_nama',
                DB::raw('SUM(simpanan.nominal) as total_setor'),
                DB::raw('COUNT(simpanan.id) as jumlah_transaksi')
            )
            ->groupBy('anggota.id', 'anggota.nip', 'anggota.nama', 'anggota.status',
                       'jenis_simpanan.id', 'jenis_simpanan.kode', 'jenis_simpanan.nama')
            ->orderBy('anggota.nama')
            ->get();

        $penarikanMap = DB::table('penarikan_simpanan')
            ->select('anggota_id', 'jenis_simpanan_id', DB::raw('SUM(nominal) as total_tarik'))
            ->groupBy('anggota_id', 'jenis_simpanan_id')
            ->get()
            ->groupBy('anggota_id')
            ->map(fn($items) => $items->keyBy('jenis_simpanan_id'));

        $simpananGrouped = $simpananAnggota->groupBy('anggota_id')->map(function ($items) use ($penarikanMap) {
            $first = $items->first();
            $detail = [];
            $grandTotal = 0;

            foreach ($items as $item) {
                $penarikan = isset($penarikanMap[$item->anggota_id][$item->jenis_id])
                    ? (float) $penarikanMap[$item->anggota_id][$item->jenis_id]->total_tarik : 0;
                $neto = (float) $item->total_setor - $penarikan;

                $detail[$item->jenis_kode] = [
                    'nama' => $item->jenis_nama,
                    'total' => $neto,
                    'transaksi' => $item->jumlah_transaksi,
                ];
                $grandTotal += $neto;
            }

            return (object) [
                'anggota_id' => $first->anggota_id,
                'nip' => $first->nip,
                'nama' => $first->nama,
                'status_anggota' => $first->status_anggota,
                'detail' => $detail,
                'grand_total' => $grandTotal,
            ];
        })->filter(fn($d) => $d->grand_total > 0)->sortByDesc('grand_total');

        $jenisKodes = DB::table('jenis_simpanan')->orderBy('id')->pluck('nama', 'kode');

        return compact('simpananGrouped', 'jenisKodes');
    }
}
