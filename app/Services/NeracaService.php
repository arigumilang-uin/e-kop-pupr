<?php

namespace App\Services;

use App\Models\ParameterNeraca;
use App\Models\PengeluaranKas;
use App\Models\PiutangEksternal;
use Illuminate\Support\Facades\DB;

/**
 * NeracaService — Laporan Posisi Keuangan (Neraca) Dinamis.
 *
 * Menggunakan arsitektur Dynamic Parameter (ParameterNeraca).
 * Tabel master menentukan item apa saja yang tampil, dan apakah 
 * angkanya manual atau dihitung otomatis oleh resolver di bawah ini.
 */
class NeracaService
{
    // Caching context supaya tidak query berulang-ulang
    private ?array $context = null;

    public function __construct(
        private PhuService $phuService,
    ) {}

    public function hitung(?int $tahun = null): array
    {
        $tahun = $tahun ?? now()->year;
        
        // Membangun struktur dinamis
        $neraca = [
            'tanggal'  => now()->toDateString(),
            'tahun'    => $tahun,
            'sections' => [],
            'aktiva_total' => 0,
            'pasiva_total' => 0,
            'selisih' => 0,
            'is_balance' => false,
        ];

        // 1. Proses Aktiva
        foreach (ParameterNeraca::posisiAktiva() as $posisi) {
            $section = $this->prosesSection($posisi, $tahun);
            $neraca['sections'][$posisi] = $section;
            $neraca['aktiva_total'] += $section['total'];
        }

        // 2. Proses Pasiva
        foreach (ParameterNeraca::posisiPasiva() as $posisi) {
            $section = $this->prosesSection($posisi, $tahun);
            $neraca['sections'][$posisi] = $section;
            $neraca['pasiva_total'] += $section['total'];
        }

        // 3. Balance Check
        $neraca['selisih'] = round($neraca['aktiva_total'] - $neraca['pasiva_total']);
        $neraca['is_balance'] = abs($neraca['selisih']) <= 1;

        return $neraca;
    }

    private function prosesSection(string $posisi, int $tahun): array
    {
        $params = ParameterNeraca::byPosisi($posisi, $tahun);
        $items = [];
        $total = 0;

        foreach ($params as $param) {
            $nominal = $param->isManual() 
                ? (float) $param->nominal_manual 
                : $this->resolve($param, $tahun);

            $items[] = [
                'nama' => $param->nama,
                'nominal' => $nominal,
                'is_pengurang' => $param->is_pengurang,
            ];

            if ($param->is_pengurang) {
                $total -= $nominal;
            } else {
                $total += $nominal;
            }
        }

        return [
            'label' => ParameterNeraca::labelPosisi($posisi),
            'items' => $items,
            'total' => $total,
        ];
    }

    /**
     * Map kode_otomatis ke perhitungan riil database.
     */
    private function resolve(ParameterNeraca $param, int $tahun): float
    {
        $kode = $param->kode_otomatis;
        if (!$kode) return 0;
        
        $this->initContext($tahun);

        return match ($kode) {
            'SALDO_BANK_BRK' => $this->resolveBankBrk($tahun),
            'SALDO_KAS_TUNAI' => $this->resolveKasTunai($tahun),
            
            'PIUTANG_PINJAMAN' => $this->context['piutang_pinjaman'],
            
            // Konsep Baru: Generic JSON Config Resolver
            'PIUTANG_EKSTERNAL' => $this->resolvePiutangEksternalDinamis($param->konfigurasi),
            
            'DANA_RESIKO_LIVE' => (float) DB::table('pinjaman')
                ->whereIn('status', ['berjalan', 'lunas'])
                ->whereYear('tanggal_approval', '>=', $tahun)
                ->sum('potongan_dana_resiko'),
                
            'SIMPANAN_ANGGOTA_KUSTOM' => $this->resolveSimpananLive($param->konfigurasi['jenis_simpanan_kode'] ?? '', $tahun),
            
            'SHU_TAHUN_BERJALAN' => $this->context['shu_bersih'],
            
            default => 0,
        };
    }

    private function resolvePiutangEksternalDinamis(?array $config): float
    {
        if (!$config) return 0;

        $query = PiutangEksternal::where('status', 'aktif');

        if (!empty($config['kategori_peminjam'])) {
            $query->where('kategori_peminjam', $config['kategori_peminjam']);
        }
        
        if (!empty($config['periode_pengurus'])) {
            $query->where('periode_pengurus', $config['periode_pengurus']);
        }
        
        if (!empty($config['tahun_pinjam'])) {
            $query->where('tahun_pinjam', $config['tahun_pinjam']);
        }

        return (float) $query->sum('sisa_piutang');
    }

    private function resolveKasTunai(int $tahun): float
    {
        $pengeluaranKas = (float) PengeluaranKas::where(function ($q) {
                $q->where('status', 'aktif')->orWhereNull('status');
            })
            ->where('sumber_dana', 'kas')
            ->whereYear('tanggal', '>=', $tahun)
            ->sum('nominal');

        $mutasiMasuk = (float) DB::table('mutasi_rekening')
            ->where('jenis_mutasi', 'brk_ke_kas')
            ->whereYear('tanggal', '>=', $tahun)
            ->sum('nominal');

        $mutasiKeluar = (float) DB::table('mutasi_rekening')
            ->where('jenis_mutasi', 'kas_ke_brk')
            ->whereYear('tanggal', '>=', $tahun)
            ->sum('nominal');

        $param = ParameterNeraca::where('kode_otomatis', 'SALDO_KAS_TUNAI')->first();
        $saldoAwal = $param ? (float) $param->nominal_manual : 0;

        return $saldoAwal + $mutasiMasuk - $mutasiKeluar - $pengeluaranKas;
    }

    private function resolveBankBrk(int $tahun): float
    {
        // Pemasukan
        $angsuranMasuk = (float) DB::table('angsuran')
            ->where('status', 'lunas')
            ->whereYear('tanggal_bayar', '>=', $tahun)
            ->sum(DB::raw('nominal_pokok + nominal_bunga'));

        $piutangMasuk = (float) DB::table('pembayaran_piutang_eksternal')
            ->whereYear('tanggal_bayar', '>=', $tahun)
            ->sum('nominal');

        $simpananMasuk = (float) DB::table('simpanan')
            ->whereNull('deleted_at')
            ->where(function ($q) { $q->where('status', 'aktif')->orWhereNull('status'); })
            ->whereYear('tanggal', '>=', $tahun)
            ->sum('nominal');

        $potonganMasuk = (float) DB::table('pinjaman')
            ->whereIn('status', ['berjalan', 'lunas'])
            ->whereYear('tanggal_approval', '>=', $tahun)
            ->sum(DB::raw('potongan_dana_resiko + potongan_biaya_admin'));

        $pemasukanLive = $angsuranMasuk + $piutangMasuk + $simpananMasuk + $potonganMasuk;

        // Mutasi Masuk dari Kas
        $mutasiMasuk = (float) DB::table('mutasi_rekening')
            ->where('jenis_mutasi', 'kas_ke_brk')
            ->whereYear('tanggal', '>=', $tahun)
            ->sum('nominal');

        $pemasukanTotal = $pemasukanLive + $mutasiMasuk;

        // Pengeluaran
        $pencairanKeluar = (float) DB::table('pinjaman')
            ->whereIn('status', ['berjalan', 'lunas'])
            ->whereYear('tanggal_approval', '>=', $tahun)
            ->sum('nominal_pinjaman');

        $pengeluaranKeluar = (float) PengeluaranKas::where(function ($q) {
                $q->where('status', 'aktif')->orWhereNull('status');
            })
            ->where(function ($q) {
                $q->where('sumber_dana', 'brk')->orWhereNull('sumber_dana');
            })
            ->whereYear('tanggal', '>=', $tahun)
            ->sum('nominal');

        $penarikanKeluar = (float) DB::table('penarikan_simpanan')
            ->whereYear('tanggal', '>=', $tahun)
            ->sum('nominal');

        $pengeluaranLive = $pencairanKeluar + $pengeluaranKeluar + $penarikanKeluar;

        // Mutasi Keluar ke Kas
        $mutasiKeluar = (float) DB::table('mutasi_rekening')
            ->where('jenis_mutasi', 'brk_ke_kas')
            ->whereYear('tanggal', '>=', $tahun)
            ->sum('nominal');

        $pengeluaranTotal = $pengeluaranLive + $mutasiKeluar;

        $param = ParameterNeraca::where('kode_otomatis', 'SALDO_BANK_BRK')->first();
        $saldoAwal = $param ? (float) $param->nominal_manual : 0;

        return $saldoAwal + $pemasukanTotal - $pengeluaranTotal;
    }



    private function resolveSimpananLive(string $kodeSimpanan, int $tahun): float
    {
        $masuk = (float) DB::table('simpanan')
            ->join('jenis_simpanan', 'simpanan.jenis_simpanan_id', '=', 'jenis_simpanan.id')
            ->whereNull('simpanan.deleted_at')
            ->where(function ($q) { $q->where('simpanan.status', 'aktif')->orWhereNull('simpanan.status'); })
            ->where('jenis_simpanan.kode', $kodeSimpanan)
            ->whereYear('simpanan.tanggal', '>=', $tahun)
            ->sum('simpanan.nominal');

        $keluar = (float) DB::table('penarikan_simpanan')
            ->join('jenis_simpanan', 'penarikan_simpanan.jenis_simpanan_id', '=', 'jenis_simpanan.id')
            ->where('jenis_simpanan.kode', $kodeSimpanan)
            ->whereYear('penarikan_simpanan.tanggal', '>=', $tahun)
            ->sum('penarikan_simpanan.nominal');

        // Sama seperti bank, jika mau hybrid: tambahkan saldo awal dari nominal_manual
        $param = ParameterNeraca::where('kode_otomatis', "SIMPANAN_LIVE_{$kodeSimpanan}")->first();
        $saldoAwal = $param ? (float) $param->nominal_manual : 0;

        return $saldoAwal + ($masuk - $keluar);
    }

    private function initContext(int $tahun): void
    {
        if ($this->context !== null) return;

        // Piutang Pinjaman Anggota
        $totalPokokBerjalan = (float) DB::table('pinjaman')
            ->where('status', 'berjalan')
            ->sum('nominal_pinjaman');

        $totalPokokTerbayar = (float) DB::table('angsuran')
            ->join('pinjaman', 'angsuran.pinjaman_id', '=', 'pinjaman.id')
            ->where('pinjaman.status', 'berjalan')
            ->where('angsuran.status', 'lunas')
            ->sum('angsuran.nominal_pokok');

        $this->context['piutang_pinjaman'] = $totalPokokBerjalan - $totalPokokTerbayar;

        // SHU Bersih
        $phuLive = $this->phuService->hitung($tahun);
        $this->context['shu_bersih'] = $phuLive['shu_bersih'];
    }
}
