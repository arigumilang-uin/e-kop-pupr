<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shu\StoreShuDistribusiRequest;
use App\Http\Requests\Shu\UpdateShuDistribusiRequest;
use App\Models\Anggota;
use App\Models\JenisSimpanan;
use App\Models\Simpanan;
use App\Models\ShuDistribusi;
use App\Models\ShuKewajiban;
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
    ) {
    }

    /**
     * Halaman utama Simulasi SHU — perhitungan dinamis.
     */
    public function index(Request $request)
    {
        $tahun = $request->input('tahun', date('Y'));

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

        $distribusiAll = ShuDistribusi::orderBy('urutan')->get();

        // Cek apakah payout tahun ini sudah dieksekusi
        $payoutTahunIni = ShuPayout::where('tahun', $tahun)->first();

        // Hitung prorata jika SHU > 0 dan ada distribusi ke anggota
        $prorata = null;
        $danaJasaModal = 0;
        $danaJasaUsaha = 0;

        if ($shu['shu_bersih'] > 0) {
            // Akumulasi semua item per routing type
            foreach ($shu['distribusi_items'] as $d) {
                if ($d['tipe_routing'] === ShuDistribusi::ROUTING_PRORATA_SIMPANAN) {
                    $danaJasaModal += $d['nominal'];
                } elseif ($d['tipe_routing'] === ShuDistribusi::ROUTING_PRORATA_PINJAMAN) {
                    $danaJasaUsaha += $d['nominal'];
                }
            }

            if ($danaJasaModal > 0 || $danaJasaUsaha > 0) {
                $prorata = $this->prorataService->hitung($danaJasaModal, $danaJasaUsaha, (int) $tahun);
            }
        }

        return view('keuangan.shu.index', compact(
            'tahun',
            'tahunTersedia',
            'shu',
            'distribusiAll',
            'payoutTahunIni',
            'prorata',
            'danaJasaModal',
            'danaJasaUsaha'
        ));
    }

    // =============================================
    //  CRUD Distribusi SHU
    // =============================================

    public function storeDistribusi(StoreShuDistribusiRequest $request)
    {
        $distribusi = ShuDistribusi::create([
            'nama' => $request->nama,
            'persen' => $request->persen,
            'tipe_routing' => $request->tipe_routing,
            'deskripsi' => $request->deskripsi,
            'urutan' => ShuDistribusi::max('urutan') + 1,
        ]);

        $routingLabel = ShuDistribusi::ROUTING_OPTIONS[$distribusi->tipe_routing]['label'] ?? $distribusi->tipe_routing;
        $this->logger->log('shu_distribusi_created', "Alokasi SHU '{$distribusi->nama}' ({$distribusi->persen}%) berhasil ditambahkan. Routing: {$routingLabel}.");

        return back()->with('success', "Alokasi '{$distribusi->nama}' berhasil ditambahkan.");
    }

    public function updateDistribusi(UpdateShuDistribusiRequest $request, ShuDistribusi $distribusi)
    {
        // Handle toggle aktif/nonaktif
        if ($request->filled('is_aktif_toggle')) {
            $distribusi->update(['is_aktif' => !$distribusi->is_aktif]);
            $status = $distribusi->is_aktif ? 'diaktifkan' : 'dinonaktifkan';
            $this->logger->log('shu_distribusi_toggled', "Alokasi SHU '{$distribusi->nama}' berhasil {$status}.");
            return back()->with('success', "Alokasi '{$distribusi->nama}' berhasil {$status}.");
        }

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

        // Guard 3: Klasifikasi dana berdasarkan tipe_routing (akumulasi jika ada multiple items per routing)
        $danaJasaModal = 0;
        $danaJasaUsaha = 0;
        $danaPengurus = 0;
        $danaCadangan = 0;
        $danaKewajibanList = [];
        $totalDanaKewajiban = 0;

        foreach ($shu['distribusi_items'] as $d) {
            match ($d['tipe_routing']) {
                ShuDistribusi::ROUTING_PRORATA_SIMPANAN => $danaJasaModal += $d['nominal'],
                ShuDistribusi::ROUTING_PRORATA_PINJAMAN => $danaJasaUsaha += $d['nominal'],
                ShuDistribusi::ROUTING_BAGI_RATA_PENGURUS => $danaPengurus += $d['nominal'],
                ShuDistribusi::ROUTING_EKUITAS => $danaCadangan += $d['nominal'],
                ShuDistribusi::ROUTING_KEWAJIBAN => (function () use ($d, &$danaKewajibanList, &$totalDanaKewajiban) {
                        $danaKewajibanList[] = ['nama_alokasi' => $d['nama'], 'nominal' => $d['nominal']];
                        $totalDanaKewajiban += $d['nominal'];
                    })(),
                default => null,
            };
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
        DB::transaction(function () use ($prorata, $shu, $tahun, $jenisBonusShu, $danaJasaModal, $danaJasaUsaha, $danaPengurus, $pengurusEligible, &$totalDanaPengurusTerdistribusi, $danaCadangan, $danaKewajibanList, $totalDanaKewajiban) {

            $today = now()->toDateString();
            $userId = Auth::id();

            // Pre-calculate reference sequence
            $pattern = "SIM-" . now()->format('Y') . "-";
            $lastRecord = Simpanan::withTrashed()
                ->where('no_referensi', 'like', "{$pattern}%")
                ->orderByDesc('no_referensi')
                ->value('no_referensi');
            $simpananSeq = $lastRecord ? (int) substr($lastRecord, -4) : 0;

            // 1. Batch insert simpanan Jasa Modal + Jasa Usaha — chunks of 100
            $prorata['detail']->chunk(100)->each(function ($chunk) use ($jenisBonusShu, $today, $userId, $tahun, $pattern, &$simpananSeq) {
                $rows = [];
                foreach ($chunk as $item) {
                    if ($item->total_shu <= 0) {
                        continue;
                    }

                    $rows[] = [
                        'no_referensi' => $pattern . str_pad(++$simpananSeq, 4, '0', STR_PAD_LEFT),
                        'anggota_id' => $item->anggota_id,
                        'jenis_simpanan_id' => $jenisBonusShu->id,
                        'nominal' => $item->total_shu,
                        'tanggal' => $today,
                        'bulan_untuk' => null,
                        'tahun_untuk' => null,
                        'pinjaman_id' => null,
                        'keterangan' => "Distribusi SHU Tahun {$tahun} (Jasa Modal + Jasa Usaha)",
                        'dicatat_oleh' => $userId,
                        'created_at' => now(),
                        'updated_at' => now(),
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
                        'no_referensi' => $pattern . str_pad(++$simpananSeq, 4, '0', STR_PAD_LEFT),
                        'anggota_id' => $pg->anggota_id,
                        'jenis_simpanan_id' => $jenisBonusShu->id,
                        'nominal' => $nominalPerPengurus,
                        'tanggal' => $today,
                        'bulan_untuk' => null,
                        'tahun_untuk' => null,
                        'pinjaman_id' => null,
                        'keterangan' => "Distribusi Dana Pengurus SHU Tahun {$tahun}",
                        'dicatat_oleh' => $userId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                if (!empty($rowsPengurus)) {
                    Simpanan::insert($rowsPengurus);
                }
            }

            // 3. Catat Kewajiban / Dompet Dana Titipan
            foreach ($danaKewajibanList as $kewajiban) {
                if ($kewajiban['nominal'] > 0) {
                    ShuKewajiban::create([
                        'tahun' => $tahun,
                        'nama_alokasi' => $kewajiban['nama_alokasi'],
                        'nominal_awal' => $kewajiban['nominal'],
                        'nominal_terpakai' => 0,
                        'saldo_tersisa' => $kewajiban['nominal'],
                    ]);
                }
            }

            // 4. Catat ke shu_payout
            ShuPayout::create([
                'tahun' => $tahun,
                'total_shu_bersih' => $shu['shu_bersih'],
                'total_jasa_modal' => $danaJasaModal,
                'total_jasa_usaha' => $danaJasaUsaha,
                'total_cadangan' => $danaCadangan,
                'total_dana_kewajiban' => $totalDanaKewajiban,
                'jumlah_penerima' => $prorata['ringkasan']['jumlah_penerima'],
                'total_terdistribusi' => $prorata['ringkasan']['total_terdistribusi'] + $totalDanaPengurusTerdistribusi,
                'dieksekusi_oleh' => Auth::id(),
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

        if ($danaCadangan > 0) {
            $logMessage .= " Dana Cadangan Rp " . number_format($danaCadangan, 0, ',', '.') . " dialokasikan ke Ekuitas Koperasi.";
        }
        if ($totalDanaKewajiban > 0) {
            $logMessage .= " Terdapat " . count($danaKewajibanList) . " pos Kewajiban Titipan senilai total Rp " . number_format($totalDanaKewajiban, 0, ',', '.') . ".";
        }

        $this->logger->log(
            'shu_payout_executed',
            $logMessage,
            dataBaru: array_merge($prorata['ringkasan'], [
                'dana_pengurus_total' => $totalDanaPengurusTerdistribusi,
                'jumlah_pengurus_penerima' => $pengurusEligible->count(),
                'total_cadangan' => $danaCadangan,
                'total_dana_kewajiban' => $totalDanaKewajiban,
            ]),
        );

        $successMessage = "Distribusi SHU tahun {$tahun} berhasil! "
            . "{$prorata['ringkasan']['jumlah_penerima']} anggota menerima total Rp "
            . number_format($prorata['ringkasan']['total_terdistribusi'], 0, ',', '.')
            . " ke Bonus SHU mereka.";

        if ($danaPengurus > 0 && $pengurusEligible->isNotEmpty()) {
            $successMessage .= " Dana Pengurus didistribusikan ke {$pengurusEligible->count()} pengurus.";
        }

        if ($totalDanaKewajiban > 0) {
            $successMessage .= " " . count($danaKewajibanList) . " pos Dana Kewajiban/Titipan telah dibuat dan siap untuk direalisasikan secara berkala.";
        }

        return back()->with('success', $successMessage);
    }
}
