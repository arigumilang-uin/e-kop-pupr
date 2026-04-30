<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\Bidang;
use App\Models\JenisSimpanan;
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

        $query = Anggota::with('bidang')->where($anggotaFilters)->latest();

        $sampaiTanggal = $request->input('sampai_tanggal');
        $dariTanggal = $request->input('dari_tanggal');

        // Sum helper closures
        $sumSetor = function($jenisId) use ($sampaiTanggal, $dariTanggal) {
            return function($q) use ($jenisId, $sampaiTanggal, $dariTanggal) {
                $q->where('jenis_simpanan_id', $jenisId);
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
        $jenisSukarelaId = JenisSimpanan::where('kode', 'SUKARELA')->value('id');
        $jenisSwpId = JenisSimpanan::where('kode', 'SWP')->value('id');

        $query->withSum(['simpanan as sum_pokok' => $sumSetor($jenisPokokId)], 'nominal')
              ->withSum(['penarikanSimpanan as tarik_pokok' => $sumTarik($jenisPokokId)], 'nominal')

              ->withSum(['simpanan as sum_wajib' => $sumSetor($jenisWajibId)], 'nominal')
              ->withSum(['penarikanSimpanan as tarik_wajib' => $sumTarik($jenisWajibId)], 'nominal')

              ->withSum(['simpanan as sum_sukarela' => $sumSetor($jenisSukarelaId)], 'nominal')
              ->withSum(['penarikanSimpanan as tarik_sukarela' => $sumTarik($jenisSukarelaId)], 'nominal')

              ->withSum(['simpanan as sum_swp' => $sumSetor($jenisSwpId)], 'nominal')
              ->withSum(['penarikanSimpanan as tarik_swp' => $sumTarik($jenisSwpId)], 'nominal');

        $anggotas = $query->paginate(15)->withQueryString();

        foreach ($anggotas as $anggota) {
            $pokok = ($anggota->sum_pokok ?? 0) - ($anggota->tarik_pokok ?? 0);
            $wajib = ($anggota->sum_wajib ?? 0) - ($anggota->tarik_wajib ?? 0);
            $sukarela = ($anggota->sum_sukarela ?? 0) - ($anggota->tarik_sukarela ?? 0);
            $swp = ($anggota->sum_swp ?? 0) - ($anggota->tarik_swp ?? 0);

            $anggota->neto_pokok = max(0, $pokok);
            $anggota->neto_wajib = max(0, $wajib);
            $anggota->neto_sukarela = max(0, $sukarela);
            $anggota->neto_swp = max(0, $swp);
            $anggota->neto_total = $anggota->neto_pokok + $anggota->neto_wajib + $anggota->neto_sukarela + $anggota->neto_swp;
        }

        // Calculate Grand Total for the filtered result
        $grandGross = \App\Models\Simpanan::whereHas('anggota', $anggotaFilters)
            ->when($dariTanggal, fn($q) => $q->where('tanggal', '>=', $dariTanggal))
            ->when($sampaiTanggal, fn($q) => $q->where('tanggal', '<=', $sampaiTanggal))
            ->sum('nominal');

        $grandTarik = \App\Models\PenarikanSimpanan::whereHas('anggota', $anggotaFilters)
            ->when($dariTanggal, fn($q) => $q->where('tanggal', '>=', $dariTanggal))
            ->when($sampaiTanggal, fn($q) => $q->where('tanggal', '<=', $sampaiTanggal))
            ->sum('nominal');

        $grandTotal = $grandGross - $grandTarik;

        $bidangs = Bidang::orderBy('nama_bidang')->get();
        $jenisSimpananList = JenisSimpanan::orderBy('nama')->get();

        if ($request->ajax()) {
            return response()->json([
                'html' => view('simpanan.partials.table', compact('anggotas'))->render(),
                'grandTotal' => format_rupiah($grandTotal),
            ]);
        }

        return view('simpanan.index', compact('anggotas', 'bidangs', 'grandTotal', 'jenisSimpananList'));
    }

    public function riwayat(Request $request)
    {
        $query = \App\Models\Simpanan::with(['anggota.bidang', 'jenisSimpanan', 'pencatat'])
                    ->latest('tanggal')
                    ->latest('id');

        // Filter Q (Nama / NIP Anggota)
        if ($request->filled('q')) {
            $query->whereHas('anggota', function ($q) use ($request) {
                $q->where('nama', 'like', "%{$request->q}%")
                  ->orWhere('nip', 'like', "%{$request->q}%");
            });
        }

        // Filter Rentang Waktu
        if ($request->filled('dari_tanggal')) {
            $query->where('tanggal', '>=', $request->dari_tanggal);
        }
        if ($request->filled('sampai_tanggal')) {
            $query->where('tanggal', '<=', $request->sampai_tanggal);
        }

        // Filter Jenis Simpanan
        if ($request->filled('jenis_simpanan_id')) {
            $query->where('jenis_simpanan_id', $request->jenis_simpanan_id);
        }

        // Filter Bidang Anggota
        if ($request->filled('bidang')) {
            $query->whereHas('anggota', function ($q) use ($request) {
                $q->where('bidang_id', $request->bidang);
            });
        }

        // Filter Golongan ASN
        if ($request->filled('golongan')) {
            $query->whereHas('anggota', function ($q) use ($request) {
                $q->where('golongan_asn', $request->golongan);
            });
        }

        // Filter Pencatat (User)
        if ($request->filled('pencatat_id')) {
            $query->where('dicatat_oleh', $request->pencatat_id);
        }

        // Hitung total transaksi (nominal setoran masuk sesuai filter)
        $totalTransaksi = (clone $query)->sum('nominal');

        $simpanans = $query->paginate(15)->withQueryString();
        
        $bidangs = Bidang::orderBy('nama_bidang')->get();
        $jenisSimpananList = JenisSimpanan::orderBy('nama')->get();
        $pencatatList = \App\Models\User::whereIn('id', \App\Models\Simpanan::select('dicatat_oleh')->distinct())->orderBy('nama')->get();

        if ($request->ajax()) {
            return response()->json([
                'html' => view('simpanan.partials.table_riwayat', compact('simpanans'))->render(),
                'totalTransaksi' => format_rupiah($totalTransaksi)
            ]);
        }

        return view('simpanan.riwayat', compact('simpanans', 'totalTransaksi', 'bidangs', 'jenisSimpananList', 'pencatatList'));
    }
}
