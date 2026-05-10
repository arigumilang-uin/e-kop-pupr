<?php

namespace App\Exports;

use App\Models\Anggota;
use App\Models\Pinjaman;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class PinjamanAktifExport
{
    private Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function download()
    {
        $data = $this->getData();
        
        $dataHash = md5('Pinjaman Aktif' . 'EXCEL' . json_encode($data));
        $existings = \App\Models\ArsipLaporan::where('data_hash', $dataHash)->get();
        foreach ($existings as $existing) {
            if (\Illuminate\Support\Facades\Storage::exists($existing->file_path)) {
                return \Illuminate\Support\Facades\Storage::download($existing->file_path, $existing->nama_file);
            } else {
                $existing->delete();
            }
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Pinjaman Aktif');

        $titleStyle = [
            'font' => ['bold' => true, 'size' => 12],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ];

        $headerStyle = [
            'font' => ['bold' => true, 'size' => 10],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E2EFDA'],
            ]
        ];

        $cellStyle = [
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
            'font' => ['size' => 10],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_TOP,
            ]
        ];

        $subtotalStyle = [
            'font' => ['bold' => true, 'size' => 10],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFF2CC'],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ]
        ];
        
        $grandTotalStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1F4E78'],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ]
        ];

        // 1. Titles
        $sheet->mergeCells('A1:S1');
        $sheet->setCellValue('A1', 'DAFTAR PINJAMAN AKTIF ANGGOTA KOPERASI KONSUMEN TIRTA BINA KARYA');
        $sheet->getStyle('A1')->applyFromArray($titleStyle);

        $sheet->mergeCells('A2:S2');
        $sheet->setCellValue('A2', 'DINAS PUPRPKPP PROVINSI RIAU');
        $sheet->getStyle('A2')->applyFromArray($titleStyle);

        $sheet->mergeCells('A3:S3');
        $sheet->setCellValue('A3', 'Periode: ' . $this->getFilterInfo() . ' | Dicetak: ' . now()->translatedFormat('d F Y, H:i') . ' WIB');
        $sheet->getStyle('A3')->applyFromArray([
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'font' => ['italic' => true, 'size' => 10]
        ]);

        // 2. Headers
        $row = 5;
        
        // Row 1 Headers
        $sheet->mergeCells("A{$row}:A".($row+1));
        $sheet->setCellValue("A{$row}", 'No');

        $sheet->mergeCells("B{$row}:C".($row+1));
        $sheet->setCellValue("B{$row}", 'NIP/Nama/Unit Kerja');

        $sheet->mergeCells("D{$row}:F{$row}");
        $sheet->setCellValue("D{$row}", 'Pinjaman');

        $sheet->mergeCells("G{$row}:J{$row}");
        $sheet->setCellValue("G{$row}", 'Potongan Awal');

        $sheet->mergeCells("K{$row}:K".($row+1));
        $sheet->setCellValue("K{$row}", 'Dana Dicairkan');

        $sheet->mergeCells("L{$row}:N{$row}");
        $sheet->setCellValue("L{$row}", 'Angsuran perbulan');

        $sheet->mergeCells("O{$row}:O".($row+1));
        $sheet->setCellValue("O{$row}", 'Tenor');

        $sheet->mergeCells("P{$row}:Q{$row}");
        $sheet->setCellValue("P{$row}", 'Progres');

        $sheet->mergeCells("R{$row}:R".($row+1));
        $sheet->setCellValue("R{$row}", 'Total Dibayar');

        $sheet->mergeCells("S{$row}:S".($row+1));
        $sheet->setCellValue("S{$row}", 'Sisa Hutang');

        // Row 2 Headers
        $r2 = $row + 1;
        $sheet->setCellValue("D{$r2}", 'Pokok');
        $sheet->setCellValue("E{$r2}", 'Total Bunga');
        $sheet->setCellValue("F{$r2}", 'Total');
        
        $sheet->setCellValue("G{$r2}", 'SWP');
        $sheet->setCellValue("H{$r2}", 'Dana Resiko');
        $sheet->setCellValue("I{$r2}", 'Biaya Admin');
        $sheet->setCellValue("J{$r2}", 'Total Potongan');

        $sheet->setCellValue("L{$r2}", 'Pokok');
        $sheet->setCellValue("M{$r2}", 'Bunga');
        $sheet->setCellValue("N{$r2}", 'Total');

        $sheet->setCellValue("P{$r2}", 'ke-');
        $sheet->setCellValue("Q{$r2}", 'dari');

        $sheet->getStyle("A{$row}:S{$r2}")->applyFromArray($headerStyle);
        $row += 2;

        // 3. Grand Total at Top
        $sheet->mergeCells("A{$row}:C{$row}");
        $sheet->setCellValue("A{$row}", 'TOTAL SELURUHNYA');
        
        $gt = $data['grandTotals'];
        $sheet->setCellValue("D{$row}", $gt['pokok']);
        $sheet->setCellValue("E{$row}", $gt['total_bunga']);
        $sheet->setCellValue("F{$row}", $gt['total']);
        $sheet->setCellValue("G{$row}", $gt['swp']);
        $sheet->setCellValue("H{$row}", $gt['dana_resiko']);
        $sheet->setCellValue("I{$row}", $gt['biaya_admin']);
        $sheet->setCellValue("J{$row}", $gt['total_potongan']);
        $sheet->setCellValue("K{$row}", $gt['dana_dicairkan']);
        $sheet->setCellValue("L{$row}", $gt['angsuran_pokok']);
        $sheet->setCellValue("M{$row}", $gt['angsuran_bunga']);
        $sheet->setCellValue("N{$row}", $gt['angsuran_total']);
        $sheet->setCellValue("O{$row}", '');
        $sheet->setCellValue("P{$row}", '');
        $sheet->setCellValue("Q{$row}", '');
        $sheet->setCellValue("R{$row}", $gt['total_dibayar']);
        $sheet->setCellValue("S{$row}", $gt['sisa_hutang']);

        $sheet->getStyle("A{$row}:S{$row}")->applyFromArray($grandTotalStyle);
        $sheet->getStyle("D{$row}:N{$row}")->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle("R{$row}:S{$row}")->getNumberFormat()->setFormatCode('#,##0');
        $row++;

        // 4. Data by Bidang
        $groupedRows = collect($data['rows'])->groupBy('bidang');

        foreach ($groupedRows as $bidangName => $anggotaList) {
            // Unit Kerja Sub-header / Total
            $sheet->mergeCells("A{$row}:C{$row}");
            $sheet->setCellValue("A{$row}", mb_strtoupper($bidangName));
            
            // Calculate subtotal
            $subPokok = $anggotaList->sum('pokok');
            $subBunga = $anggotaList->sum('total_bunga');
            $subTotal = $anggotaList->sum('total');
            $subSwp = $anggotaList->sum('swp');
            $subResiko = $anggotaList->sum('dana_resiko');
            $subAdmin = $anggotaList->sum('biaya_admin');
            $subTotPotongan = $anggotaList->sum('total_potongan');
            $subCair = $anggotaList->sum('dana_dicairkan');
            $subAngPokok = $anggotaList->sum('angsuran_pokok');
            $subAngBunga = $anggotaList->sum('angsuran_bunga');
            $subAngTotal = $anggotaList->sum('angsuran_total');
            $subDibayar = $anggotaList->sum('total_dibayar');
            $subSisa = $anggotaList->sum('sisa_hutang');

            $sheet->setCellValue("D{$row}", $subPokok);
            $sheet->setCellValue("E{$row}", $subBunga);
            $sheet->setCellValue("F{$row}", $subTotal);
            $sheet->setCellValue("G{$row}", $subSwp);
            $sheet->setCellValue("H{$row}", $subResiko);
            $sheet->setCellValue("I{$row}", $subAdmin);
            $sheet->setCellValue("J{$row}", $subTotPotongan);
            $sheet->setCellValue("K{$row}", $subCair);
            $sheet->setCellValue("L{$row}", $subAngPokok);
            $sheet->setCellValue("M{$row}", $subAngBunga);
            $sheet->setCellValue("N{$row}", $subAngTotal);
            $sheet->setCellValue("O{$row}", '');
            $sheet->setCellValue("P{$row}", '');
            $sheet->setCellValue("Q{$row}", '');
            $sheet->setCellValue("R{$row}", $subDibayar);
            $sheet->setCellValue("S{$row}", $subSisa);
            
            $sheet->getStyle("A{$row}:S{$row}")->applyFromArray($subtotalStyle);
            $sheet->getStyle("D{$row}:N{$row}")->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle("R{$row}:S{$row}")->getNumberFormat()->setFormatCode('#,##0');
            $row++;

            // Anggota Rows
            $no = 1;
            foreach ($anggotaList as $item) {
                $sheet->setCellValue("A{$row}", $no++);
                $sheet->setCellValueExplicit("B{$row}", $item['nip'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $sheet->setCellValue("C{$row}", $item['nama']);
                $sheet->setCellValue("D{$row}", $item['pokok']);
                $sheet->setCellValue("E{$row}", $item['total_bunga']);
                $sheet->setCellValue("F{$row}", $item['total']);
                $sheet->setCellValue("G{$row}", $item['swp']);
                $sheet->setCellValue("H{$row}", $item['dana_resiko']);
                $sheet->setCellValue("I{$row}", $item['biaya_admin']);
                $sheet->setCellValue("J{$row}", $item['total_potongan']);
                $sheet->setCellValue("K{$row}", $item['dana_dicairkan']);
                $sheet->setCellValue("L{$row}", $item['angsuran_pokok']);
                $sheet->setCellValue("M{$row}", $item['angsuran_bunga']);
                $sheet->setCellValue("N{$row}", $item['angsuran_total']);
                $sheet->setCellValue("O{$row}", $item['tenor_bulan']);
                $sheet->setCellValue("P{$row}", $item['progres_ke']);
                $sheet->setCellValue("Q{$row}", $item['progres_dari']);
                $sheet->setCellValue("R{$row}", $item['total_dibayar']);
                $sheet->setCellValue("S{$row}", $item['sisa_hutang']);

                $sheet->getStyle("A{$row}:S{$row}")->applyFromArray($cellStyle);
                $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("O{$row}:Q{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("D{$row}:N{$row}")->getNumberFormat()->setFormatCode('#,##0');
                $sheet->getStyle("R{$row}:S{$row}")->getNumberFormat()->setFormatCode('#,##0');
                $row++;
            }
        }

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(13);
        $sheet->getColumnDimension('E')->setWidth(13);
        $sheet->getColumnDimension('F')->setWidth(14);
        $sheet->getColumnDimension('G')->setWidth(12);
        $sheet->getColumnDimension('H')->setWidth(12);
        $sheet->getColumnDimension('I')->setWidth(12);
        $sheet->getColumnDimension('J')->setWidth(14);
        $sheet->getColumnDimension('K')->setWidth(15);
        $sheet->getColumnDimension('L')->setWidth(13);
        $sheet->getColumnDimension('M')->setWidth(13);
        $sheet->getColumnDimension('N')->setWidth(14);
        $sheet->getColumnDimension('O')->setWidth(8);
        $sheet->getColumnDimension('P')->setWidth(6);
        $sheet->getColumnDimension('Q')->setWidth(6);
        $sheet->getColumnDimension('R')->setWidth(15);
        $sheet->getColumnDimension('S')->setWidth(15);

        $filename = 'Laporan_Pinjaman_Aktif_' . now()->format('Ymd_His') . '.xlsx';
        $path = 'arsip_laporan/' . $filename;
        
        $writer = new Xlsx($spreadsheet);
        
        \Illuminate\Support\Facades\Storage::makeDirectory('arsip_laporan');
        $writer->save(\Illuminate\Support\Facades\Storage::path($path));
        
        \App\Models\ArsipLaporan::create([
            'tipe_laporan' => 'Pinjaman Aktif',
            'format' => 'EXCEL',
            'nama_file' => $filename,
            'file_path' => $path,
            'data_hash' => $dataHash,
            'filter_info' => $data['filterInfo'],
            'dibuat_oleh' => auth()->id(),
        ]);
        
        return \Illuminate\Support\Facades\Storage::download($path, $filename);
    }

    public function getData(): array
    {
        $request = $this->request;

        $query = \App\Models\Anggota::with([
            'bidang',
            'pinjaman' => function($q) use ($request) {
                $q->where('status', \App\Enums\StatusPinjaman::Berjalan)
                  ->with('angsuran');
                  
                if ($request->filled('bulan_awal')) {
                    $parts = explode('-', $request->bulan_awal);
                    if (count($parts) == 2) {
                        $q->whereYear('tanggal_approval', $parts[0])
                          ->whereMonth('tanggal_approval', $parts[1]);
                    }
                }
                if ($request->filled('tenor')) {
                    $q->where('tenor_bulan', $request->tenor);
                }
                if ($request->filled('nominal')) {
                    $q->where('nominal_pinjaman', $request->nominal);
                }
                if ($request->filled('periode_id')) {
                    $q->where('periode_pinjaman_id', $request->periode_id);
                }
            }
        ])->whereHas('pinjaman', function($q) use ($request) {
            $q->where('status', \App\Enums\StatusPinjaman::Berjalan);
            
            if ($request->filled('bulan_awal')) {
                $parts = explode('-', $request->bulan_awal);
                if (count($parts) == 2) {
                    $q->whereYear('tanggal_approval', $parts[0])
                      ->whereMonth('tanggal_approval', $parts[1]);
                }
            }
            if ($request->filled('tenor')) {
                $q->where('tenor_bulan', $request->tenor);
            }
            if ($request->filled('nominal')) {
                $q->where('nominal_pinjaman', $request->nominal);
            }
            if ($request->filled('periode_id')) {
                $q->where('periode_pinjaman_id', $request->periode_id);
            }
        });

        if ($request->filled('bidang_id')) {
            $query->where('bidang_id', $request->bidang_id);
        }

        if ($request->filled('q')) {
            $query->where(function($q) use ($request) {
                $q->where('nama', 'like', "%{$request->q}%")
                  ->orWhere('nip', 'like', "%{$request->q}%");
            });
        }

        $anggotas = $query->orderBy('bidang_id')->orderBy('nama')->get();

        $grandTotals = [
            'pokok' => 0, 'total_bunga' => 0, 'total' => 0,
            'swp' => 0, 'dana_resiko' => 0, 'biaya_admin' => 0, 'total_potongan' => 0,
            'dana_dicairkan' => 0,
            'angsuran_pokok' => 0, 'angsuran_bunga' => 0, 'angsuran_total' => 0,
            'total_dibayar' => 0, 'sisa_hutang' => 0
        ];

        $rows = [];

        foreach ($anggotas as $anggota) {
            foreach ($anggota->pinjaman as $pinjaman) {
                $pokok = $pinjaman->nominal_pinjaman;
                $totalBunga = $pinjaman->total_bunga;
                $total = $pinjaman->total_angsuran * $pinjaman->tenor_bulan; // or $pokok + $totalBunga
                
                $swp = $pinjaman->potongan_swp;
                $danaResiko = $pinjaman->potongan_dana_resiko;
                $biayaAdmin = $pinjaman->potongan_biaya_admin;
                $totalPotongan = $swp + $danaResiko + $biayaAdmin;
                $danaDicairkan = $pokok - $totalPotongan;

                $angsuranPokok = $pinjaman->angsuran_pokok;
                $angsuranBunga = $pinjaman->angsuran_bunga;
                $angsuranTotal = $pinjaman->total_angsuran;

                $tenor = $pinjaman->tenor_bulan;
                
                $lunasCount = $pinjaman->angsuran->filter(fn($a) => $a->status->value === 'lunas')->count();
                $totalLunas = $pinjaman->angsuran->filter(fn($a) => $a->status->value === 'lunas')->sum('nominal_total');
                
                $sisaHutang = $total - $totalLunas;

                // Adds to grand totals
                $grandTotals['pokok'] += $pokok;
                $grandTotals['total_bunga'] += $totalBunga;
                $grandTotals['total'] += $total;
                $grandTotals['swp'] += $swp;
                $grandTotals['dana_resiko'] += $danaResiko;
                $grandTotals['biaya_admin'] += $biayaAdmin;
                $grandTotals['total_potongan'] += $totalPotongan;
                $grandTotals['dana_dicairkan'] += $danaDicairkan;
                $grandTotals['angsuran_pokok'] += $angsuranPokok;
                $grandTotals['angsuran_bunga'] += $angsuranBunga;
                $grandTotals['angsuran_total'] += $angsuranTotal;
                $grandTotals['total_dibayar'] += $totalLunas;
                $grandTotals['sisa_hutang'] += $sisaHutang;

                $rows[] = [
                    'bidang' => $anggota->bidang->nama_bidang ?? 'LAINNYA',
                    'nip' => $anggota->nip,
                    'nama' => $anggota->nama,
                    'pokok' => $pokok,
                    'total_bunga' => $totalBunga,
                    'total' => $total,
                    'swp' => $swp,
                    'dana_resiko' => $danaResiko,
                    'biaya_admin' => $biayaAdmin,
                    'total_potongan' => $totalPotongan,
                    'dana_dicairkan' => $danaDicairkan,
                    'angsuran_pokok' => $angsuranPokok,
                    'angsuran_bunga' => $angsuranBunga,
                    'angsuran_total' => $angsuranTotal,
                    'tenor_bulan' => $tenor,
                    'progres_ke' => $lunasCount,
                    'progres_dari' => $tenor,
                    'total_dibayar' => $totalLunas,
                    'sisa_hutang' => $sisaHutang,
                ];
            }
        }

        return [
            'rows' => $rows,
            'grandTotals' => $grandTotals,
            'filterInfo' => $this->getFilterInfo(),
        ];
    }

    private function getFilterInfo(): string
    {
        $parts = [];
        if ($this->request->filled('bidang_id')) {
            $bidang = \App\Models\Bidang::find($this->request->bidang_id);
            if ($bidang) $parts[] = 'Bidang: ' . $bidang->nama_bidang;
        }
        if ($this->request->filled('bulan_awal')) {
            $parts[] = 'Bulan: ' . \Carbon\Carbon::parse($this->request->bulan_awal.'-01')->translatedFormat('F Y');
        }
        if ($this->request->filled('tenor')) {
            $parts[] = 'Tenor: ' . $this->request->tenor . ' Bln';
        }
        if ($this->request->filled('nominal')) {
            $parts[] = 'Nominal: Rp ' . number_format($this->request->nominal, 0, ',', '.');
        }
        if ($this->request->filled('q')) {
            $parts[] = 'Pencarian: "' . $this->request->q . '"';
        }
        return $parts ? implode(' | ', $parts) : 'Semua Data (Hingga ' . now()->translatedFormat('d F Y') . ')';
    }
}
