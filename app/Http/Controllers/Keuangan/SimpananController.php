<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\Bidang;
use App\Models\JenisSimpanan;
use App\Services\LedgerService;
use Illuminate\Http\Request;

class SimpananController extends Controller
{
    public function index(Request $request)
    {
        $anggotaFilters = function($q) use ($request) {
            $q->where('status', \App\Enums\StatusAnggota::Aktif);
            
            if ($request->filled('q')) {
                $q->where(function($sub) use ($request) {
                    $sub->where('nama', 'like', "%{$request->q}%")
                        ->orWhere('nip', 'like', "%{$request->q}%");
                });
            }

            if ($request->filled('bidang')) {
                $q->where('bidang_id', $request->bidang);
            }

            if ($request->filled('golongan')) {
                $q->where('golongan_asn', $request->golongan);
            }
        };

        $query = Anggota::with('bidang')->where($anggotaFilters)->orderBy('nama');

        $sampaiTanggal = $request->input('sampai_tanggal');
        $dariTanggal = $request->input('dari_tanggal');

        // Sum helper closures
        $sumSetor = function($jenisId) use ($sampaiTanggal, $dariTanggal) {
            return function($q) use ($jenisId, $sampaiTanggal, $dariTanggal) {
                $q->where('jenis_simpanan_id', $jenisId);
                $q->where(function ($sq) { $sq->where('status', 'aktif')->orWhereNull('status'); });
                if ($dariTanggal) $q->where('tanggal', '>=', $dariTanggal);
                if ($sampaiTanggal) $q->where('tanggal', '<=', $sampaiTanggal);
            };
        };

        $sumTarik = function($jenisId) use ($sampaiTanggal, $dariTanggal) {
            return function($q) use ($jenisId, $sampaiTanggal, $dariTanggal) {
                $q->where('jenis_simpanan_id', $jenisId);
                if ($dariTanggal) $q->where('tanggal', '>=', $dariTanggal);
                if ($sampaiTanggal) $q->where('tanggal', '<=', $sampaiTanggal);
            };
        };

        $jenisPokokId = JenisSimpanan::where('kode', 'POKOK')->value('id');
        $jenisWajibId = JenisSimpanan::where('kode', 'WAJIB')->value('id');
        $jenisSim2025Id = JenisSimpanan::where('kode', 'SIM2025')->value('id');
        $jenisSwpId = JenisSimpanan::where('kode', 'SWP')->value('id');
        $jenisBonusShuId = JenisSimpanan::where('kode', 'BONUS_SHU')->value('id');

        $query->withSum(['simpanan as sum_pokok' => $sumSetor($jenisPokokId)], 'nominal')
              ->withSum(['penarikanSimpanan as tarik_pokok' => $sumTarik($jenisPokokId)], 'nominal')

              ->withSum(['simpanan as sum_wajib' => $sumSetor($jenisWajibId)], 'nominal')
              ->withSum(['penarikanSimpanan as tarik_wajib' => $sumTarik($jenisWajibId)], 'nominal')

              ->withSum(['simpanan as sum_sim2025' => $sumSetor($jenisSim2025Id)], 'nominal')
              ->withSum(['penarikanSimpanan as tarik_sim2025' => $sumTarik($jenisSim2025Id)], 'nominal')

              ->withSum(['simpanan as sum_swp' => $sumSetor($jenisSwpId)], 'nominal')
              ->withSum(['penarikanSimpanan as tarik_swp' => $sumTarik($jenisSwpId)], 'nominal')

              ->withSum(['simpanan as sum_bonus_shu' => $sumSetor($jenisBonusShuId)], 'nominal')
              ->withSum(['penarikanSimpanan as tarik_bonus_shu' => $sumTarik($jenisBonusShuId)], 'nominal');

        $anggotas = $query->paginate(15)->withQueryString();

        foreach ($anggotas as $anggota) {
            $pokok = ($anggota->sum_pokok ?? 0) - ($anggota->tarik_pokok ?? 0);
            $wajib = ($anggota->sum_wajib ?? 0) - ($anggota->tarik_wajib ?? 0);
            $sim2025 = ($anggota->sum_sim2025 ?? 0) - ($anggota->tarik_sim2025 ?? 0);
            $swp = ($anggota->sum_swp ?? 0) - ($anggota->tarik_swp ?? 0);
            $bonusShu = ($anggota->sum_bonus_shu ?? 0) - ($anggota->tarik_bonus_shu ?? 0);

            $anggota->neto_pokok = max(0, $pokok);
            $anggota->neto_wajib = max(0, $wajib);
            $anggota->neto_sim2025 = max(0, $sim2025);
            $anggota->neto_swp = max(0, $swp);
            $anggota->neto_bonus_shu = max(0, $bonusShu);
            $anggota->neto_total = $anggota->neto_pokok + $anggota->neto_wajib + $anggota->neto_sim2025 + $anggota->neto_swp + $anggota->neto_bonus_shu;
        }

        // Calculate Grand Total for the filtered result per tab
        $baseSimpananQuery = \App\Models\Simpanan::aktif()->whereHas('anggota', $anggotaFilters)
            ->when($dariTanggal, fn($q) => $q->where('tanggal', '>=', $dariTanggal))
            ->when($sampaiTanggal, fn($q) => $q->where('tanggal', '<=', $sampaiTanggal));

        $basePenarikanQuery = \App\Models\PenarikanSimpanan::whereHas('anggota', $anggotaFilters)
            ->when($dariTanggal, fn($q) => $q->where('tanggal', '>=', $dariTanggal))
            ->when($sampaiTanggal, fn($q) => $q->where('tanggal', '<=', $sampaiTanggal));

        $simpananGrouped = (clone $baseSimpananQuery)
            ->selectRaw('jenis_simpanan_id, SUM(nominal) as total')
            ->groupBy('jenis_simpanan_id')
            ->pluck('total', 'jenis_simpanan_id');

        $penarikanGrouped = (clone $basePenarikanQuery)
            ->selectRaw('jenis_simpanan_id, SUM(nominal) as total')
            ->groupBy('jenis_simpanan_id')
            ->pluck('total', 'jenis_simpanan_id');

        $grandTotalPokok = max(0, ($simpananGrouped[$jenisPokokId] ?? 0) - ($penarikanGrouped[$jenisPokokId] ?? 0));
        $grandTotalWajib = max(0, ($simpananGrouped[$jenisWajibId] ?? 0) - ($penarikanGrouped[$jenisWajibId] ?? 0));
        $grandTotalSim2025 = max(0, ($simpananGrouped[$jenisSim2025Id] ?? 0) - ($penarikanGrouped[$jenisSim2025Id] ?? 0));
        $grandTotalSwp = max(0, ($simpananGrouped[$jenisSwpId] ?? 0) - ($penarikanGrouped[$jenisSwpId] ?? 0));
        $grandTotalBonusShu = max(0, ($simpananGrouped[$jenisBonusShuId] ?? 0) - ($penarikanGrouped[$jenisBonusShuId] ?? 0));

        $grandTotal = $grandTotalPokok + $grandTotalWajib + $grandTotalSim2025 + $grandTotalSwp + $grandTotalBonusShu;
        
        $grandTotals = [
            'pokok' => $grandTotalPokok,
            'wajib' => $grandTotalWajib,
            'sim2025' => $grandTotalSim2025,
            'swp' => $grandTotalSwp,
            'bonus_shu' => $grandTotalBonusShu,
        ];

        $bidangs = Bidang::orderBy('nama_bidang')->get();
        $jenisSimpananList = JenisSimpanan::orderBy('nama')->get();

        $pengaturan = resolve(\App\Services\PengaturanService::class);
        $nominalPokok = $pengaturan->simpananPokok();
        $nominalWajib = $pengaturan->simpananWajib();

        if ($request->ajax()) {
            return response()->json([
                'html' => view('simpanan.partials.table', compact('anggotas', 'grandTotals', 'grandTotal'))->render(),
                'grandTotal' => format_rupiah($grandTotal),
            ]);
        }

        return view('simpanan.index', compact('anggotas', 'bidangs', 'grandTotal', 'grandTotals', 'jenisSimpananList', 'nominalPokok', 'nominalWajib'));
    }



    public function store(Request $request)
    {
        $request->validate([
            'anggota_id' => 'required|exists:anggota,id',
            'jenis_simpanan_id' => 'required|exists:jenis_simpanan,id',
            'nominal' => 'required|numeric|min:1',
            'tanggal' => 'required|date',
            'bulan_untuk' => 'nullable|integer|min:1|max:12',
            'tahun_untuk' => 'nullable|integer|min:2000',
            'keterangan' => 'nullable|string'
        ]);

        $jenisSimpanan = \App\Models\JenisSimpanan::find($request->jenis_simpanan_id);
        
        $nominal = $request->nominal;
        if ($jenisSimpanan->kode === 'POKOK') {
            $nominal = resolve(\App\Services\PengaturanService::class)->simpananPokok();
        } elseif ($jenisSimpanan->kode === 'WAJIB') {
            $nominal = resolve(\App\Services\PengaturanService::class)->simpananWajib();
        }

        // Validasi duplikat (menggantikan unique constraint yang sudah dihapus)
        // Hanya cek untuk simpanan wajib bulanan yang masih aktif
        if ($request->bulan_untuk && $request->tahun_untuk) {
            $exists = \App\Models\Simpanan::where('anggota_id', $request->anggota_id)
                ->where('jenis_simpanan_id', $request->jenis_simpanan_id)
                ->where('bulan_untuk', $request->bulan_untuk)
                ->where('tahun_untuk', $request->tahun_untuk)
                ->where('status', \App\Enums\StatusSimpanan::Aktif)
                ->exists();

            if ($exists) {
                return back()->with('error', 'Setoran gagal. Anggota ini sudah tercatat membayar kategori simpanan bulanan pada bulan dan tahun tersebut.');
            }
        }

        try {
            $simpanan = \App\Models\Simpanan::create([
                'anggota_id' => $request->anggota_id,
                'jenis_simpanan_id' => $request->jenis_simpanan_id,
                'nominal' => $nominal,
                'tanggal' => $request->tanggal,
                'bulan_untuk' => $request->bulan_untuk,
                'tahun_untuk' => $request->tahun_untuk,
                'keterangan' => $request->keterangan,
                'dicatat_oleh' => auth()->id()
            ]);

            // Tulis ke Ledger
            resolve(LedgerService::class)->catatSimpanan(
                $nominal,
                $simpanan->id,
                $simpanan->anggota_id,
                $jenisSimpanan->nama,
                $request->tanggal,
            );
        } catch (\Exception $e) {
            return back()->with('error', 'Setoran gagal: ' . $e->getMessage());
        }

        return back()->with('success', 'Setoran simpanan berhasil dicatat!');
    }

    public function exportExcel(Request $request)
    {
        $export = new \App\Exports\SimpananExport($request);
        return $export->download();
    }

    public function exportPdf(Request $request)
    {
        $export = new \App\Exports\SimpananExport($request);
        $data = $export->getData();

        // Cek arsip laporan
        $dataHash = md5('Simpanan Anggota' . 'PDF' . json_encode($data));
        $existings = \App\Models\ArsipLaporan::where('data_hash', $dataHash)->get();
        foreach ($existings as $existing) {
            if (\Illuminate\Support\Facades\Storage::exists($existing->file_path)) {
                return \Illuminate\Support\Facades\Storage::download($existing->file_path, $existing->nama_file);
            } else {
                $existing->delete();
            }
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.simpanan-pdf', [
            'rows' => $data['rows'],
            'grandTotals' => $data['grandTotals'],
            'filterInfo' => $data['filterInfo'],
        ])->setPaper('a4', 'landscape');

        $filename = 'Laporan_Simpanan_Anggota_' . now()->format('Ymd_His') . '.pdf';
        $path = 'arsip_laporan/' . $filename;
        
        \Illuminate\Support\Facades\Storage::makeDirectory('arsip_laporan');
        \Illuminate\Support\Facades\Storage::put($path, $pdf->output());
        
        \App\Models\ArsipLaporan::create([
            'tipe_laporan' => 'Simpanan',
            'format' => 'PDF',
            'nama_file' => $filename,
            'file_path' => $path,
            'data_hash' => $dataHash,
            'filter_info' => $data['filterInfo'],
            'dibuat_oleh' => auth()->id(),
        ]);

        return \Illuminate\Support\Facades\Storage::download($path, $filename);
    }
}
