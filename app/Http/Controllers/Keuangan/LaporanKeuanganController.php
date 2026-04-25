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

        $totalPokokPinjamanAktif = (float) DB::table('pinjaman')->where('status', 'berjalan')->sum('nominal_pinjaman');
        $angsuranPokokTerbayar = (float) DB::table('angsuran')
            ->join('pinjaman', 'angsuran.pinjaman_id', '=', 'pinjaman.id')
            ->where('pinjaman.status', 'berjalan')
            ->where('angsuran.status', 'lunas')
            ->sum('angsuran.nominal_pokok');
        $piutangBerjalan = $totalPokokPinjamanAktif - $angsuranPokokTerbayar;

        $totalAset = $saldoKoperasi + $piutangBerjalan;

        // Breakdown Kas
        $masukSimpanan = $totalSimpanan;
        $masukAngsuran = (float) DB::table('angsuran')->where('status', 'lunas')->sum(DB::raw('nominal_pokok + nominal_bunga'));
        $masukFee = (float) DB::table('pinjaman')->whereIn('status', ['berjalan', 'lunas'])->sum(DB::raw('potongan_dana_resiko + potongan_biaya_admin'));
        $keluarPinjaman = (float) DB::table('pinjaman')->whereIn('status', ['berjalan', 'lunas'])->sum('nominal_pinjaman');
        $keluarTarik = $totalPenarikan;
        $keluarPengeluaranKas = (float) DB::table('pengeluaran_kas')->sum('nominal');

        // Breakdown Simpanan per Jenis (Neto)
        $setorPerJenis = DB::table('simpanan')
            ->join('jenis_simpanan', 'simpanan.jenis_simpanan_id', '=', 'jenis_simpanan.id')
            ->select('jenis_simpanan.nama', 'jenis_simpanan.kode', DB::raw('SUM(simpanan.nominal) as total'))
            ->groupBy('jenis_simpanan.nama', 'jenis_simpanan.kode')
            ->get()->keyBy('kode');

        $tarikPerJenis = DB::table('penarikan_simpanan')
            ->join('jenis_simpanan', 'penarikan_simpanan.jenis_simpanan_id', '=', 'jenis_simpanan.id')
            ->select('jenis_simpanan.kode', DB::raw('SUM(penarikan_simpanan.nominal) as total'))
            ->groupBy('jenis_simpanan.kode')
            ->get()->keyBy('kode');

        $simpananPerJenis = $setorPerJenis->map(function ($item) use ($tarikPerJenis) {
            $tarik = isset($tarikPerJenis[$item->kode]) ? (float) $tarikPerJenis[$item->kode]->total : 0;
            $item->total = (float) $item->total - $tarik;
            return $item;
        })->filter(fn($item) => $item->total > 0)->values();

        $ringkasan = compact(
            'saldoKoperasi', 'totalSimpanan', 'simpananBersih', 'piutangBerjalan', 'totalAset',
            'masukSimpanan', 'masukAngsuran', 'masukFee', 'keluarPinjaman', 'keluarTarik', 'keluarPengeluaranKas',
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
                'anggota.status as status_anggota',
                'jenis_simpanan.id as jenis_id',
                'jenis_simpanan.kode as jenis_kode',
                'jenis_simpanan.nama as jenis_nama',
                DB::raw('SUM(simpanan.nominal) as total_setor'),
                DB::raw('COUNT(simpanan.id) as jumlah_transaksi')
            )
            ->groupBy('anggota.id', 'anggota.nip', 'anggota.nama', 'anggota.status',
                       'jenis_simpanan.id', 'jenis_simpanan.kode', 'jenis_simpanan.nama')
            ->orderBy('anggota.nama')
            ->get();

        // Ambil penarikan per anggota per jenis
        $penarikanMap = DB::table('penarikan_simpanan')
            ->select('anggota_id', 'jenis_simpanan_id', DB::raw('SUM(nominal) as total_tarik'))
            ->groupBy('anggota_id', 'jenis_simpanan_id')
            ->get()
            ->groupBy('anggota_id')
            ->map(fn($items) => $items->keyBy('jenis_simpanan_id'));

        // Group: anggota_id => [jenis => neto]
        $simpananGrouped = $simpananAnggota->groupBy('anggota_id')->map(function ($items) use ($penarikanMap) {
            $first = $items->first();
            $detail = [];
            $grandTotal = 0;

            foreach ($items as $item) {
                $penarikan = 0;
                if (isset($penarikanMap[$item->anggota_id][$item->jenis_id])) {
                    $penarikan = (float) $penarikanMap[$item->anggota_id][$item->jenis_id]->total_tarik;
                }
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

        // Get all jenis simpanan codes for table headers
        $jenisKodes = DB::table('jenis_simpanan')->orderBy('id')->pluck('nama', 'kode');

        return view('keuangan.laporan', compact(
            'tab', 'ringkasan', 'piutangGrouped', 'piutangAnggota',
            'simpananGrouped', 'jenisKodes'
        ));
    }
}
