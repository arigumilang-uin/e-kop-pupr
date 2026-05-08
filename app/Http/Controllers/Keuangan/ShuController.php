<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shu\StoreShuKomponenRequest;
use App\Http\Requests\Shu\UpdateShuKomponenRequest;
use App\Http\Requests\Shu\StoreShuDistribusiRequest;
use App\Http\Requests\Shu\UpdateShuDistribusiRequest;
use App\Models\Anggota;
use App\Models\JenisSimpanan;
use App\Models\Simpanan;
use App\Models\ShuKomponen;
use App\Models\ShuDistribusi;
use App\Models\ShuPayout;
use App\Models\Pinjaman;
use App\Models\User;
use App\Services\ShuService;
use App\Services\ShuProrataService;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ShuController extends Controller
{
    public function __construct(
        private ShuService $shuService,
        private ShuProrataService $prorataService,
        private ActivityLogService $logger,
    ) {}

    /**
     * Halaman utama Simulasi SHU — perhitungan dinamis.
     */
    public function index(Request $request)
    {
        $tahun = $request->get('tahun', date('Y'));

        $shu = $this->shuService->hitung((int) $tahun);

        // Dropdown tahun
        $tahunTersedia = Pinjaman::selectRaw('YEAR(tanggal_approval) as tahun')
            ->whereNotNull('tanggal_approval')
            ->distinct()
            ->orderByDesc('tahun')
            ->pluck('tahun')
            ->toArray();

        if (!in_array(date('Y'), $tahunTersedia)) {
            $tahunTersedia[] = date('Y');
        }

        rsort($tahunTersedia);

        // Komponen & Distribusi untuk panel konfigurasi
        $komponenAll = ShuKomponen::orderBy('tipe')->orderBy('urutan')->get();
        $distribusiAll = ShuDistribusi::orderBy('urutan')->get();
        $sumberTersedia = $this->shuService->sumberDataTersedia();

        // Cek apakah payout tahun ini sudah dieksekusi
        $payoutTahunIni = ShuPayout::where('tahun', $tahun)->first();

        // Hitung prorata jika SHU > 0 dan ada distribusi Jasa Modal/Anggota
        $prorata = null;
        $danaJasaModal = 0;
        $danaJasaUsaha = 0;

        if ($shu['shu_bersih'] > 0) {
            foreach ($shu['distribusi_items'] as $d) {
                $namaLower = strtolower($d['nama']);
                if (str_contains($namaLower, 'jasa modal')) {
                    $danaJasaModal = $d['nominal'];
                } elseif (str_contains($namaLower, 'jasa anggota') || str_contains($namaLower, 'jasa usaha')) {
                    $danaJasaUsaha = $d['nominal'];
                }
            }

            if ($danaJasaModal > 0 || $danaJasaUsaha > 0) {
                $prorata = $this->prorataService->hitung($danaJasaModal, $danaJasaUsaha, (int) $tahun);
            }
        }

        return view('keuangan.shu.index', compact(
            'tahun', 'tahunTersedia', 'shu',
            'komponenAll', 'distribusiAll', 'sumberTersedia',
            'payoutTahunIni', 'prorata', 'danaJasaModal', 'danaJasaUsaha'
        ));
    }

    // =============================================
    //  CRUD Komponen SHU
    // =============================================

    public function storeKomponen(StoreShuKomponenRequest $request)
    {
        $komponen = ShuKomponen::create([
            'nama'        => $request->nama,
            'tipe'        => $request->tipe,
            'sumber_data' => $request->sumber_data,
            'deskripsi'   => $request->deskripsi,
            'urutan'      => ShuKomponen::where('tipe', $request->tipe)->max('urutan') + 1,
        ]);

        $this->logger->log('shu_komponen_created', "Komponen SHU '{$komponen->nama}' ({$komponen->tipe}) berhasil ditambahkan.");

        return back()->with('success', "Komponen '{$komponen->nama}' berhasil ditambahkan.");
    }

    public function updateKomponen(UpdateShuKomponenRequest $request, ShuKomponen $komponen)
    {
        $komponen->update($request->validated());

        $this->logger->log('shu_komponen_updated', "Komponen SHU '{$komponen->nama}' berhasil diperbarui.");

        return back()->with('success', "Komponen '{$komponen->nama}' berhasil diperbarui.");
    }

    public function destroyKomponen(ShuKomponen $komponen)
    {
        $nama = $komponen->nama;
        $komponen->delete();

        $this->logger->log('shu_komponen_deleted', "Komponen SHU '{$nama}' berhasil dihapus.");

        return back()->with('success', "Komponen '{$nama}' berhasil dihapus.");
    }

    // =============================================
    //  CRUD Distribusi SHU
    // =============================================

    public function storeDistribusi(StoreShuDistribusiRequest $request)
    {
        $distribusi = ShuDistribusi::create([
            'nama'      => $request->nama,
            'persen'    => $request->persen,
            'deskripsi' => $request->deskripsi,
            'urutan'    => ShuDistribusi::max('urutan') + 1,
        ]);

        $this->logger->log('shu_distribusi_created', "Alokasi SHU '{$distribusi->nama}' ({$distribusi->persen}%) berhasil ditambahkan.");

        return back()->with('success', "Alokasi '{$distribusi->nama}' berhasil ditambahkan.");
    }

    public function updateDistribusi(UpdateShuDistribusiRequest $request, ShuDistribusi $distribusi)
    {
        $distribusi->update($request->validated());

        $this->logger->log('shu_distribusi_updated', "Alokasi SHU '{$distribusi->nama}' berhasil diperbarui.");

        return back()->with('success', "Alokasi '{$distribusi->nama}' berhasil diperbarui.");
    }

    public function destroyDistribusi(ShuDistribusi $distribusi)
    {
        $nama = $distribusi->nama;
        $distribusi->delete();

        $this->logger->log('shu_distribusi_deleted', "Alokasi SHU '{$nama}' berhasil dihapus.");

        return back()->with('success', "Alokasi '{$nama}' berhasil dihapus.");
    }

    // =============================================
    //  PAYOUT — Eksekusi Distribusi SHU ke Simpanan
    // =============================================

    /**
     * One-Click Payout: Kapitalisasi SHU ke Bonus SHU seluruh anggota.
     *
     * Skema:
     *   1. Hitung SHU & prorata per anggota (Jasa Modal + Jasa Usaha)
     *   2. Batch insert ke tabel simpanan (jenis: BONUS_SHU)
     *   3. Distribusi Dana Pengurus ke pengurus yang terkait anggota
     *   4. Catat ke tabel shu_payout agar tidak bisa dieksekusi 2x
     *   5. Log aktivitas
     */
    public function eksekusiPayout(Request $request)
    {
        $tahun = (int) $request->input('tahun', date('Y'));

        // Guard 1: Cek apakah payout sudah pernah dieksekusi untuk tahun ini
        if (ShuPayout::where('tahun', $tahun)->exists()) {
            return back()->with('error', "SHU tahun {$tahun} sudah pernah didistribusikan. Tidak dapat dieksekusi dua kali.");
        }

        // Guard 2: Hitung SHU & pastikan ada surplus
        $shu = $this->shuService->hitung($tahun);

        if ($shu['shu_bersih'] <= 0) {
            return back()->with('error', "SHU Bersih tahun {$tahun} bernilai nol atau negatif. Tidak ada yang bisa didistribusikan.");
        }

        // Guard 3: Cari pos Jasa Modal, Jasa Usaha, dan Dana Pengurus dari distribusi
        $danaJasaModal = 0;
        $danaJasaUsaha = 0;
        $danaPengurus = 0;

        foreach ($shu['distribusi_items'] as $d) {
            $namaLower = strtolower($d['nama']);
            if (str_contains($namaLower, 'jasa modal')) {
                $danaJasaModal = $d['nominal'];
            } elseif (str_contains($namaLower, 'jasa anggota') || str_contains($namaLower, 'jasa usaha')) {
                $danaJasaUsaha = $d['nominal'];
            } elseif (str_contains($namaLower, 'dana pengurus')) {
                $danaPengurus = $d['nominal'];
            }
        }

        if ($danaJasaModal <= 0 && $danaJasaUsaha <= 0) {
            return back()->with('error', 'Tidak ditemukan pos distribusi "Jasa Modal" atau "Jasa Anggota" yang bernilai > 0.');
        }

        // Guard 4: Pastikan jenis simpanan BONUS_SHU ada
        $jenisBonusShu = JenisSimpanan::where('kode', 'BONUS_SHU')->first();
        if (!$jenisBonusShu) {
            return back()->with('error', 'Jenis simpanan BONUS_SHU tidak ditemukan di database.');
        }

        // Hitung prorata
        $prorata = $this->prorataService->hitung($danaJasaModal, $danaJasaUsaha, $tahun);

        if ($prorata['detail']->isEmpty()) {
            return back()->with('error', 'Tidak ada anggota yang memenuhi syarat untuk menerima SHU.');
        }

        // Cari pengurus yang memenuhi syarat untuk Dana Pengurus
        $pengurusEligible = collect();
        $totalDanaPengurusTerdistribusi = 0;

        if ($danaPengurus > 0) {
            // Ambil semua user yang punya NIP dan NIP-nya cocok dengan anggota aktif
            $pengurusEligible = User::pengurusAktif()->get()->map(function ($user) {
                $anggota = Anggota::where('nip', $user->nip)->where('status', 'aktif')->first();
                return $anggota ? (object) [
                    'user_id' => $user->id,
                    'user_nama' => $user->nama,
                    'anggota_id' => $anggota->id,
                    'nip' => $user->nip,
                ] : null;
            })->filter()->values();
        }

        // === EKSEKUSI DALAM TRANSAKSI ===
        DB::transaction(function () use ($prorata, $shu, $tahun, $jenisBonusShu, $danaJasaModal, $danaJasaUsaha, $danaPengurus, $pengurusEligible, &$totalDanaPengurusTerdistribusi) {

            $today = now()->toDateString();
            $userId = Auth::id();

            // 1. Batch insert simpanan Jasa Modal + Jasa Usaha — chunks of 100
            $prorata['detail']->chunk(100)->each(function ($chunk) use ($jenisBonusShu, $today, $userId, $tahun) {
                $rows = [];
                foreach ($chunk as $item) {
                    if ($item->total_shu <= 0) {
                        continue;
                    }

                    $rows[] = [
                        'no_referensi'      => Simpanan::generateNoReferensi(),
                        'anggota_id'        => $item->anggota_id,
                        'jenis_simpanan_id' => $jenisBonusShu->id,
                        'nominal'           => $item->total_shu,
                        'tanggal'           => $today,
                        'bulan_untuk'       => null,
                        'tahun_untuk'       => null,
                        'pinjaman_id'       => null,
                        'keterangan'        => "Distribusi SHU Tahun {$tahun} (Jasa Modal + Jasa Usaha)",
                        'dicatat_oleh'      => $userId,
                        'created_at'        => now(),
                        'updated_at'        => now(),
                    ];
                }

                if (!empty($rows)) {
                    Simpanan::insert($rows);
                }
            });

            // 2. Distribusi Dana Pengurus — bagi rata ke pengurus yang eligible
            if ($danaPengurus > 0 && $pengurusEligible->isNotEmpty()) {
                $nominalPerPengurus = floor($danaPengurus / $pengurusEligible->count());
                $totalDanaPengurusTerdistribusi = $nominalPerPengurus * $pengurusEligible->count();

                $rowsPengurus = [];
                foreach ($pengurusEligible as $pg) {
                    $rowsPengurus[] = [
                        'no_referensi'      => Simpanan::generateNoReferensi(),
                        'anggota_id'        => $pg->anggota_id,
                        'jenis_simpanan_id' => $jenisBonusShu->id,
                        'nominal'           => $nominalPerPengurus,
                        'tanggal'           => $today,
                        'bulan_untuk'       => null,
                        'tahun_untuk'       => null,
                        'pinjaman_id'       => null,
                        'keterangan'        => "Distribusi Dana Pengurus SHU Tahun {$tahun}",
                        'dicatat_oleh'      => $userId,
                        'created_at'        => now(),
                        'updated_at'        => now(),
                    ];
                }

                if (!empty($rowsPengurus)) {
                    Simpanan::insert($rowsPengurus);
                }
            }

            // 3. Catat ke shu_payout
            ShuPayout::create([
                'tahun'                => $tahun,
                'total_shu_bersih'     => $shu['shu_bersih'],
                'total_jasa_modal'     => $danaJasaModal,
                'total_jasa_usaha'     => $danaJasaUsaha,
                'jumlah_penerima'      => $prorata['ringkasan']['jumlah_penerima'],
                'total_terdistribusi'  => $prorata['ringkasan']['total_terdistribusi'] + $totalDanaPengurusTerdistribusi,
                'dieksekusi_oleh'      => Auth::id(),
            ]);
        });

        // Log aktivitas
        $logMessage = "Distribusi SHU tahun {$tahun} berhasil dieksekusi. "
            . "{$prorata['ringkasan']['jumlah_penerima']} anggota menerima total Rp "
            . number_format($prorata['ringkasan']['total_terdistribusi'], 0, ',', '.')
            . " ke Bonus SHU.";

        if ($danaPengurus > 0 && $pengurusEligible->isNotEmpty()) {
            $logMessage .= " Dana Pengurus Rp " . number_format($totalDanaPengurusTerdistribusi, 0, ',', '.')
                . " didistribusikan ke {$pengurusEligible->count()} pengurus.";
        }

        $this->logger->log(
            'shu_payout_executed',
            $logMessage,
            dataBaru: array_merge($prorata['ringkasan'], [
                'dana_pengurus_total' => $totalDanaPengurusTerdistribusi,
                'jumlah_pengurus_penerima' => $pengurusEligible->count(),
            ]),
        );

        $successMessage = "Distribusi SHU tahun {$tahun} berhasil! "
            . "{$prorata['ringkasan']['jumlah_penerima']} anggota menerima total Rp "
            . number_format($prorata['ringkasan']['total_terdistribusi'], 0, ',', '.')
            . " ke Bonus SHU mereka.";

        if ($danaPengurus > 0 && $pengurusEligible->isNotEmpty()) {
            $successMessage .= " Dana Pengurus Rp " . number_format($totalDanaPengurusTerdistribusi, 0, ',', '.')
                . " telah didistribusikan ke {$pengurusEligible->count()} pengurus.";
        }

        return back()->with('success', $successMessage);
    }
}
