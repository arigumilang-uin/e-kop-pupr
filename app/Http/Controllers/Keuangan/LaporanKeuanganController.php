<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\Pinjaman;
use App\Models\Simpanan;
use App\Services\SaldoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanKeuanganController extends Controller
{
    public function __construct(
        private SaldoService $saldo,
    ) {}

    public function index(Request $request)
    {
        $tab = $request->input('tab', 'ringkasan');

        // ============================
        // 1. RINGKASAN (Summary Cards)
        // ============================
        $saldoKoperasi = $this->saldo->saldoKoperasi();
        $totalSimpanan = (float) DB::table('simpanan')->sum('nominal');
        $totalPenarikan = (float) DB::table('penarikan_simpanan')->sum('nominal');
        $simpananBersih = $totalSimpanan - $totalPenarikan;

        $totalBayarPinjamanAktif = (float) DB::table('pinjaman')->where('status', 'berjalan')->sum('total_bayar');
        $angsuranTerbayar = (float) DB::table('angsuran')
            ->join('pinjaman', 'angsuran.pinjaman_id', '=', 'pinjaman.id')
            ->where('pinjaman.status', 'berjalan')
            ->where('angsuran.status', 'lunas')
            ->sum('angsuran.nominal_total');
        $piutangBerjalan = $totalBayarPinjamanAktif - $angsuranTerbayar;

        $totalAset = $saldoKoperasi + $piutangBerjalan;

        // Breakdown Kas
        $masukSimpanan = $totalSimpanan;
        $masukAngsuran = (float) DB::table('angsuran')->where('status', 'lunas')->sum(DB::raw('nominal_pokok + nominal_bunga'));
        $masukFee = (float) DB::table('pinjaman')->whereIn('status', ['berjalan', 'lunas'])->sum(DB::raw('potongan_dana_resiko + potongan_biaya_admin'));
        $keluarPinjaman = (float) DB::table('pinjaman')->whereIn('status', ['berjalan', 'lunas'])->sum('nominal_pinjaman');
        $keluarTarik = $totalPenarikan;

        // Breakdown Simpanan per Jenis
        $simpananPerJenis = DB::table('simpanan')
            ->join('jenis_simpanan', 'simpanan.jenis_simpanan_id', '=', 'jenis_simpanan.id')
            ->select('jenis_simpanan.nama', 'jenis_simpanan.kode', DB::raw('SUM(simpanan.nominal) as total'))
            ->groupBy('jenis_simpanan.nama', 'jenis_simpanan.kode')
            ->get();

        $ringkasan = compact(
            'saldoKoperasi', 'totalSimpanan', 'simpananBersih', 'piutangBerjalan', 'totalAset',
            'masukSimpanan', 'masukAngsuran', 'masukFee', 'keluarPinjaman', 'keluarTarik',
            'simpananPerJenis', 'totalPenarikan'
        );

        // ============================
        // 2. PIUTANG KOPERASI (Detail per Anggota)
        // ============================
        $piutangAnggota = Pinjaman::with(['anggota.bidang', 'angsuran'])
            ->where('status', 'berjalan')
            ->orderBy('tanggal_approval', 'desc')
            ->get()
            ->map(function ($p) {
                $lunas = $p->angsuran->where('status.value', 'lunas');
                $belum = $p->angsuran->where('status.value', 'belum');

                $totalDibayar = $lunas->sum('nominal_total');
                $totalBelum = $belum->sum('nominal_total');

                $terakhirJatuhTempo = $p->angsuran->sortByDesc('tanggal_jatuh_tempo')->first();

                return (object) [
                    'pinjaman' => $p,
                    'anggota' => $p->anggota,
                    'total_tagihan' => (float) $p->total_bayar,
                    'total_dibayar' => (float) $totalDibayar,
                    'total_belum' => (float) $totalBelum,
                    'angsuran_lunas' => $lunas->count(),
                    'angsuran_belum' => $belum->count(),
                    'tenor' => $p->tenor_bulan,
                    'mulai' => $p->tanggal_approval,
                    'estimasi_lunas' => $terakhirJatuhTempo?->tanggal_jatuh_tempo,
                    'progress' => $p->tenor_bulan > 0 ? round(($lunas->count() / $p->tenor_bulan) * 100) : 0,
                ];
            });

        // Group by anggota for multi-loan view
        $piutangGrouped = $piutangAnggota->groupBy(fn($item) => $item->anggota->id);

        // ============================
        // 3. SIMPANAN ANGGOTA (Detail per Anggota)
        // ============================
        $simpananAnggota = DB::table('simpanan')
            ->join('anggota', 'simpanan.anggota_id', '=', 'anggota.id')
            ->join('jenis_simpanan', 'simpanan.jenis_simpanan_id', '=', 'jenis_simpanan.id')
            ->select(
                'anggota.id as anggota_id',
                'anggota.nip',
                'anggota.nama',
                'jenis_simpanan.kode as jenis_kode',
                'jenis_simpanan.nama as jenis_nama',
                DB::raw('SUM(simpanan.nominal) as total'),
                DB::raw('COUNT(simpanan.id) as jumlah_transaksi')
            )
            ->groupBy('anggota.id', 'anggota.nip', 'anggota.nama', 'jenis_simpanan.kode', 'jenis_simpanan.nama')
            ->orderBy('anggota.nama')
            ->get();

        // Group: anggota_id => [jenis => total]
        $simpananGrouped = $simpananAnggota->groupBy('anggota_id')->map(function ($items) {
            $first = $items->first();
            $detail = [];
            $grandTotal = 0;

            foreach ($items as $item) {
                $detail[$item->jenis_kode] = [
                    'nama' => $item->jenis_nama,
                    'total' => (float) $item->total,
                    'transaksi' => $item->jumlah_transaksi,
                ];
                $grandTotal += (float) $item->total;
            }

            return (object) [
                'anggota_id' => $first->anggota_id,
                'nip' => $first->nip,
                'nama' => $first->nama,
                'detail' => $detail,
                'grand_total' => $grandTotal,
            ];
        })->sortByDesc('grand_total');

        // Get all jenis simpanan codes for table headers
        $jenisKodes = DB::table('jenis_simpanan')->orderBy('id')->pluck('nama', 'kode');

        return view('keuangan.laporan', compact(
            'tab', 'ringkasan', 'piutangGrouped', 'piutangAnggota',
            'simpananGrouped', 'jenisKodes'
        ));
    }
}
