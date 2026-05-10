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
        $neraca = $this->neracaService->hitung();

        return view('keuangan.neraca', compact('neraca'));
    }

    public function exportExcel(Request $request)
    {
        $neraca = $this->neracaService->hitung();
        $export = new \App\Exports\NeracaExport($neraca);
        return $export->download();
    }

    public function exportPdf(Request $request)
    {
        $neraca = $this->neracaService->hitung();

        $dataHash = md5('Neraca Keuangan v2' . 'PDF' . json_encode($neraca));
        $existings = \App\Models\ArsipLaporan::where('data_hash', $dataHash)->get();
        foreach ($existings as $existing) {
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
