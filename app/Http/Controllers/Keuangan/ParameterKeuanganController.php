<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\ParameterNeraca;
use App\Models\ParameterPhu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ParameterKeuanganController extends Controller
{
    public function index(Request $request, \App\Services\NeracaService $neracaService, \App\Services\PhuService $phuService)
    {
        $tahun = $request->input('tahun', now()->year);

        $parameterNeraca = ParameterNeraca::where('tahun_buku', $tahun)
            ->orderBy('posisi')
            ->orderBy('urutan')
            ->get()
            ->groupBy('posisi');

        $parameterPhu = ParameterPhu::where('tahun_buku', $tahun)
            ->orderBy('tipe')
            ->orderBy('urutan')
            ->get()
            ->groupBy('tipe');

        // Fetch Live Values for Preview
        $neracaReport = $neracaService->hitung($tahun);
        $phuReport = $phuService->hitung($tahun);

        // Populate last 5 years + any existing data years
        $pastYears = [];
        for ($i = 0; $i <= 5; $i++) {
            $pastYears[] = now()->year - $i;
        }

        $tahunBukuList = array_unique(array_merge(
            ParameterNeraca::select('tahun_buku')->pluck('tahun_buku')->toArray(),
            ParameterPhu::select('tahun_buku')->pluck('tahun_buku')->toArray(),
            $pastYears
        ));
        rsort($tahunBukuList);

        $jenisSimpananList = \App\Models\JenisSimpanan::orderBy('nama')->get();
        $tab = $request->input('tab', 'neraca');

        return view('keuangan.parameter.index', compact('parameterNeraca', 'parameterPhu', 'tahun', 'tahunBukuList', 'jenisSimpananList', 'tab', 'neracaReport', 'phuReport'));
    }

    public function storeNeraca(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:150',
            'posisi' => 'required|string',
            'sumber_data' => 'required|in:manual,otomatis',
            'kode_otomatis' => 'nullable|string',
            'nominal_manual' => 'nullable|numeric|min:0',
            'is_pengurang' => 'boolean',
            'kategori_peminjam' => 'nullable|string',
            'tahun_pinjam' => 'nullable|string',
        ]);

        $konfigurasi = [];
        if ($request->sumber_data === 'otomatis') {
            if ($request->kode_otomatis === 'PIUTANG_EKSTERNAL') {
                if ($request->filled('kategori_peminjam')) $konfigurasi['kategori_peminjam'] = $request->kategori_peminjam;
                if ($request->filled('tahun_pinjam')) $konfigurasi['tahun_pinjam'] = $request->tahun_pinjam;
            } elseif ($request->kode_otomatis === 'SIMPANAN_ANGGOTA_KUSTOM') {
                if ($request->filled('jenis_simpanan_kode')) $konfigurasi['jenis_simpanan_kode'] = $request->jenis_simpanan_kode;
            }
        }

        $urutan = ParameterNeraca::where('tahun_buku', $request->input('tahun', now()->year))
            ->where('posisi', $request->posisi)
            ->max('urutan') + 1;

        ParameterNeraca::create([
            'nama' => $request->nama,
            'posisi' => $request->posisi,
            'sumber_data' => $request->sumber_data,
            'kode_otomatis' => $request->sumber_data === 'otomatis' ? $request->kode_otomatis : null,
            'nominal_manual' => $request->nominal_manual ?? 0,
            'is_pengurang' => $request->has('is_pengurang'),
            'urutan' => $urutan,
            'tahun_buku' => $request->input('tahun', now()->year),
            'konfigurasi' => empty($konfigurasi) ? null : $konfigurasi,
        ]);

        return back()->with('success', 'Parameter Neraca berhasil ditambahkan.');
    }

    public function updateNeraca(Request $request, ParameterNeraca $parameter)
    {
        $request->validate([
            'nama' => 'required|string|max:150',
            'nominal_manual' => 'nullable|numeric|min:0',
            'kategori_peminjam' => 'nullable|string',
            'tahun_pinjam' => 'nullable|string',
        ]);

        $konfigurasi = $parameter->konfigurasi ?? [];
        if ($parameter->isOtomatis() && $parameter->kode_otomatis === 'PIUTANG_EKSTERNAL') {
            if ($request->filled('kategori_peminjam')) {
                $konfigurasi['kategori_peminjam'] = $request->kategori_peminjam;
            } else {
                unset($konfigurasi['kategori_peminjam']);
            }
            if ($request->filled('tahun_pinjam')) {
                $konfigurasi['tahun_pinjam'] = $request->tahun_pinjam;
            } else {
                unset($konfigurasi['tahun_pinjam']);
            }
        }

        $isHybrid = in_array($parameter->kode_otomatis, ['SALDO_BANK_BRK', 'SALDO_KAS_TUNAI', 'SIMPANAN_LIVE_POKOK', 'SIMPANAN_LIVE_WAJIB', 'SIMPANAN_LIVE_SWP']);

        $parameter->update([
            'nama' => $request->nama,
            'nominal_manual' => ($parameter->isManual() || $isHybrid) ? ($request->nominal_manual ?? 0) : 0,
            'konfigurasi' => empty($konfigurasi) ? null : $konfigurasi,
        ]);

        return back()->with('success', 'Parameter Neraca berhasil diperbarui.');
    }

    public function storePhu(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:150',
            'tipe' => 'required|string|in:pendapatan,beban',
            'sumber_data' => 'required|in:manual,otomatis',
            'kode_otomatis' => 'nullable|string',
            'nominal_manual' => 'nullable|numeric|min:0',
        ]);

        $urutan = ParameterPhu::where('tahun_buku', $request->input('tahun', now()->year))
            ->where('tipe', $request->tipe)
            ->max('urutan') + 1;

        ParameterPhu::create([
            'nama' => $request->nama,
            'tipe' => $request->tipe,
            'sumber_data' => $request->sumber_data,
            'kode_otomatis' => $request->sumber_data === 'otomatis' ? $request->kode_otomatis : null,
            'nominal_manual' => $request->nominal_manual ?? 0,
            'urutan' => $urutan,
            'tahun_buku' => $request->input('tahun', now()->year),
        ]);

        return back()->with('success', 'Parameter PHU berhasil ditambahkan.');
    }

    public function updatePhu(Request $request, ParameterPhu $parameter)
    {
        $request->validate([
            'nama' => 'required|string|max:150',
            'nominal_manual' => 'nullable|numeric|min:0',
        ]);

        $parameter->update([
            'nama' => $request->nama,
            'nominal_manual' => $parameter->isManual() ? ($request->nominal_manual ?? 0) : 0,
        ]);

        return back()->with('success', 'Parameter PHU berhasil diperbarui.');
    }

    public function copyFromPreviousYear(Request $request)
    {
        $request->validate([
            'tahun_target' => 'required|integer',
        ]);

        $target = $request->tahun_target;
        $sumber = $target - 1;

        $hasSumberNeraca = ParameterNeraca::where('tahun_buku', $sumber)->exists();
        $hasSumberPhu = ParameterPhu::where('tahun_buku', $sumber)->exists();

        if (!$hasSumberNeraca && !$hasSumberPhu) {
            return back()->with('error', "Parameter tahun buku {$sumber} tidak ditemukan untuk diduplikasi. Silakan buat secara manual.");
        }

        DB::transaction(function () use ($sumber, $target) {
            // Copy Neraca
            $neracaParams = ParameterNeraca::where('tahun_buku', $sumber)->get();
            foreach ($neracaParams as $param) {
                ParameterNeraca::updateOrCreate(
                    [
                        'kode_otomatis' => $param->kode_otomatis,
                        'nama' => $param->nama,
                        'posisi' => $param->posisi,
                        'tahun_buku' => $target,
                    ],
                    [
                        'sumber_data' => $param->sumber_data,
                        'nominal_manual' => $param->nominal_manual,
                        'urutan' => $param->urutan,
                        'is_pengurang' => $param->is_pengurang,
                        'is_active' => $param->is_active,
                        'keterangan' => $param->keterangan,
                        'konfigurasi' => $param->konfigurasi,
                    ]
                );
            }

            // Copy PHU
            $phuParams = ParameterPhu::where('tahun_buku', $sumber)->get();
            foreach ($phuParams as $param) {
                ParameterPhu::updateOrCreate(
                    [
                        'kode_otomatis' => $param->kode_otomatis,
                        'nama' => $param->nama,
                        'tipe' => $param->tipe,
                        'tahun_buku' => $target,
                    ],
                    [
                        'sumber_data' => $param->sumber_data,
                        'nominal_manual' => $param->nominal_manual,
                        'urutan' => $param->urutan,
                        'is_active' => $param->is_active,
                        'keterangan' => $param->keterangan,
                        'konfigurasi' => $param->konfigurasi,
                    ]
                );
            }
        });

        return back()->with('success', "Berhasil menyalin struktur parameter dari tahun {$sumber} ke {$target}.");
    }

    public function destroyNeraca(ParameterNeraca $parameter)
    {
        $parameter->delete();
        return back()->with('success', 'Parameter Neraca berhasil dihapus.');
    }

    public function destroyPhu(ParameterPhu $parameter)
    {
        $parameter->delete();
        return back()->with('success', 'Parameter PHU berhasil dihapus.');
    }
}
