<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Services\NeracaService;
use Illuminate\Http\Request;

class NeracaController extends Controller
{
    public function __construct(
        private NeracaService $neracaService,
    ) {}

    public function index(Request $request)
    {
        $tahun = $request->input('tahun', now()->year);
        $neraca = $this->neracaService->hitung($tahun);

        // Get available years for the dropdown
        $availableYears = array_unique(array_merge(
            \App\Models\ParameterNeraca::select('tahun_buku')->pluck('tahun_buku')->toArray(),
            \App\Models\ParameterPhu::select('tahun_buku')->pluck('tahun_buku')->toArray(),
            [now()->year, now()->year - 1, now()->year - 2, now()->year - 3, now()->year - 4, now()->year - 5]
        ));
        rsort($availableYears);

        return view('keuangan.neraca', compact('neraca', 'tahun', 'availableYears'));
    }

    public function exportExcel(Request $request)
    {
        $tahun = $request->input('tahun', now()->year);
        $neraca = $this->neracaService->hitung($tahun);
        $export = new \App\Exports\NeracaExport($neraca);
        return $export->download();
    }

    public function exportPdf(Request $request)
    {
        $tahun = $request->input('tahun', now()->year);
        $neraca = $this->neracaService->hitung($tahun);

        $dataHash = md5('Neraca Keuangan v2' . 'PDF' . json_encode($neraca));
        $existings = \App\Models\ArsipLaporan::where('data_hash', $dataHash)->get();
        foreach ($existings as $existing) {
            /** @var \App\Models\ArsipLaporan $existing */
            if (\Illuminate\Support\Facades\Storage::exists($existing->file_path)) {
                return \Illuminate\Support\Facades\Storage::download($existing->file_path, $existing->nama_file);
            } else {
                $existing->delete();
            }
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.neraca-pdf', compact('neraca'))
            ->setPaper('a4', 'portrait');

        $filename = 'Laporan_Neraca_Keuangan_' . now()->format('Ymd_His') . '.pdf';
        $path = 'arsip_laporan/' . $filename;
        
        \Illuminate\Support\Facades\Storage::makeDirectory('arsip_laporan');
        \Illuminate\Support\Facades\Storage::put($path, $pdf->output());
        
        \App\Models\ArsipLaporan::create([
            'tipe_laporan' => 'Neraca',
            'format' => 'PDF',
            'nama_file' => $filename,
            'file_path' => $path,
            'data_hash' => $dataHash,
            'filter_info' => 'Per ' . \Carbon\Carbon::parse($neraca['tanggal'])->translatedFormat('d F Y'),
            'dibuat_oleh' => auth()->id(),
        ]);

        return \Illuminate\Support\Facades\Storage::download($path, $filename);
    }
}
