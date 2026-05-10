<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class NeracaExport
{
    private array $neraca;

    public function __construct(array $neraca)
    {
        $this->neraca = $neraca;
    }

    public function download()
    {
        $dataHash = md5('Neraca Keuangan v2' . 'EXCEL' . json_encode($this->neraca));
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
        $sheet->setTitle('Neraca Keuangan');

        // Styles
        $titleStyle = [
            'font' => ['bold' => true, 'size' => 12],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ];

        $headerStyle = [
            'font' => ['bold' => true],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E2EFDA'], // Light green
            ]
        ];

        $sectionHeaderStyle = [
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F2F2F2'],
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ]
        ];

        $cellStyle = [
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
        ];

        $totalStyle = [
            'font' => ['bold' => true],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFF2CC'],
            ]
        ];

        // 1. Titles
        $sheet->mergeCells('A1:F1');
        $sheet->setCellValue('A1', 'NERACA (LAPORAN POSISI KEUANGAN)');
        $sheet->getStyle('A1')->applyFromArray($titleStyle);

        $sheet->mergeCells('A2:F2');
        $sheet->setCellValue('A2', 'KOPERASI SIMPAN PINJAM KONSUMEN TIRTA BINA KARYA');
        $sheet->getStyle('A2')->applyFromArray($titleStyle);

        $sheet->mergeCells('A3:F3');
        $sheet->setCellValue('A3', 'DINAS PUPRPKPP PROVINSI RIAU');
        $sheet->getStyle('A3')->applyFromArray($titleStyle);

        $sheet->mergeCells('A4:F4');
        $sheet->setCellValue('A4', 'Per ' . \Carbon\Carbon::parse($this->neraca['tanggal'])->translatedFormat('d F Y'));
        $sheet->getStyle('A4')->applyFromArray([
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        // Headers
        $row = 6;
        $sheet->mergeCells("A{$row}:C{$row}");
        $sheet->setCellValue("A{$row}", 'AKTIVA');
        $sheet->mergeCells("D{$row}:F{$row}");
        $sheet->setCellValue("D{$row}", 'PASIVA');
        $sheet->getStyle("A{$row}:F{$row}")->applyFromArray($headerStyle);

        $row++;

        // Helper to format currency
        $formatCurrency = function ($value) {
            return $value; // We will apply number format to columns
        };

        $aktivaRow = $row;
        $pasivaRow = $row;

        // AKTIVA CONTENT
        // Aktiva Lancar
        $sheet->mergeCells("A{$aktivaRow}:B{$aktivaRow}");
        $sheet->setCellValue("A{$aktivaRow}", 'Aktiva Lancar');
        $sheet->getStyle("A{$aktivaRow}:C{$aktivaRow}")->applyFromArray($sectionHeaderStyle);
        $aktivaRow++;

        $sheet->setCellValue("A{$aktivaRow}", 'Kas & Setara Kas');
        $sheet->setCellValue("C{$aktivaRow}", $this->neraca['aktiva']['kas']);
        $sheet->getStyle("A{$aktivaRow}:C{$aktivaRow}")->applyFromArray($cellStyle);
        $aktivaRow++;

        $sheet->setCellValue("A{$aktivaRow}", 'Piutang Pinjaman Anggota');
        $sheet->setCellValue("C{$aktivaRow}", $this->neraca['aktiva']['piutang_pinjaman']);
        $sheet->getStyle("A{$aktivaRow}:C{$aktivaRow}")->applyFromArray($cellStyle);
        $aktivaRow++;

        // PASIVA CONTENT
        // I. Kewajiban
        $sheet->mergeCells("D{$pasivaRow}:E{$pasivaRow}");
        $sheet->setCellValue("D{$pasivaRow}", 'I. Kewajiban (Hutang)');
        $sheet->getStyle("D{$pasivaRow}:F{$pasivaRow}")->applyFromArray($sectionHeaderStyle);
        $pasivaRow++;

        foreach ($this->neraca['kewajiban']['simpanan_items'] as $item) {
            $sheet->setCellValue("D{$pasivaRow}", $item->nama);
            $sheet->setCellValue("F{$pasivaRow}", $item->total);
            $sheet->getStyle("D{$pasivaRow}:F{$pasivaRow}")->applyFromArray($cellStyle);
            $pasivaRow++;
        }

        $sheet->setCellValue("D{$pasivaRow}", 'Cadangan Dana Resiko');
        $sheet->setCellValue("F{$pasivaRow}", $this->neraca['kewajiban']['dana_resiko']);
        $sheet->getStyle("D{$pasivaRow}:F{$pasivaRow}")->applyFromArray($cellStyle);
        $pasivaRow++;

        $sheet->mergeCells("D{$pasivaRow}:E{$pasivaRow}");
        $sheet->setCellValue("D{$pasivaRow}", 'Total Kewajiban');
        $sheet->setCellValue("F{$pasivaRow}", $this->neraca['kewajiban']['total']);
        $sheet->getStyle("D{$pasivaRow}:F{$pasivaRow}")->applyFromArray($totalStyle);
        $pasivaRow++;

        // II. Modal
        $sheet->mergeCells("D{$pasivaRow}:E{$pasivaRow}");
        $sheet->setCellValue("D{$pasivaRow}", 'II. Modal / Ekuitas');
        $sheet->getStyle("D{$pasivaRow}:F{$pasivaRow}")->applyFromArray($sectionHeaderStyle);
        $pasivaRow++;

        foreach ($this->neraca['modal']['simpanan_items'] as $item) {
            $sheet->setCellValue("D{$pasivaRow}", $item->nama);
            $sheet->setCellValue("F{$pasivaRow}", $item->total);
            $sheet->getStyle("D{$pasivaRow}:F{$pasivaRow}")->applyFromArray($cellStyle);
            $pasivaRow++;
        }

        $sheet->setCellValue("D{$pasivaRow}", 'Laba Ditahan (SHU Berjalan)');
        $sheet->setCellValue("F{$pasivaRow}", $this->neraca['modal']['laba_ditahan']);
        $sheet->getStyle("D{$pasivaRow}:F{$pasivaRow}")->applyFromArray($cellStyle);
        $pasivaRow++;

        $sheet->mergeCells("D{$pasivaRow}:E{$pasivaRow}");
        $sheet->setCellValue("D{$pasivaRow}", 'Total Modal / Ekuitas');
        $sheet->setCellValue("F{$pasivaRow}", $this->neraca['modal']['total']);
        $sheet->getStyle("D{$pasivaRow}:F{$pasivaRow}")->applyFromArray($totalStyle);
        $pasivaRow++;

        // Balance blank lines to match height
        $maxRow = max($aktivaRow, $pasivaRow);
        
        while ($aktivaRow < $maxRow) {
            $sheet->getStyle("A{$aktivaRow}:C{$aktivaRow}")->applyFromArray($cellStyle);
            $aktivaRow++;
        }
        while ($pasivaRow < $maxRow) {
            $sheet->getStyle("D{$pasivaRow}:F{$pasivaRow}")->applyFromArray($cellStyle);
            $pasivaRow++;
        }

        // TOTAL ROWS
        $row = $maxRow;
        $sheet->mergeCells("A{$row}:B{$row}");
        $sheet->setCellValue("A{$row}", 'TOTAL AKTIVA');
        $sheet->setCellValue("C{$row}", $this->neraca['aktiva']['total']);
        
        $sheet->mergeCells("D{$row}:E{$row}");
        $sheet->setCellValue("D{$row}", 'TOTAL PASIVA');
        $sheet->setCellValue("F{$row}", $this->neraca['pasiva']['total']);
        
        $sheet->getStyle("A{$row}:F{$row}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1F4E78']]
        ]);

        // Format Numbers
        $sheet->getStyle("C5:C{$row}")->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle("F5:F{$row}")->getNumberFormat()->setFormatCode('#,##0');

        // Column Widths
        $sheet->getColumnDimension('A')->setWidth(30);
        $sheet->getColumnDimension('B')->setWidth(5);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(30);
        $sheet->getColumnDimension('E')->setWidth(5);
        $sheet->getColumnDimension('F')->setWidth(20);

        // Signatures
        $row += 4;
        $sheet->setCellValue("A{$row}", 'Disusun oleh,');
        $sheet->setCellValue("D{$row}", 'Diperiksa oleh,');
        $sheet->setCellValue("F{$row}", 'Disetujui oleh,');
        $sheet->getStyle("A{$row}:F{$row}")->applyFromArray(['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]]);

        $row += 4;
        $sheet->setCellValue("A{$row}", 'Bendahara');
        $sheet->setCellValue("D{$row}", 'Pengawas');
        $sheet->setCellValue("F{$row}", 'Ketua Koperasi');
        $sheet->getStyle("A{$row}:F{$row}")->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        $filename = 'Laporan_Neraca_Keuangan_' . now()->format('Ymd_His') . '.xlsx';
        $path = 'arsip_laporan/' . $filename;
        
        $writer = new Xlsx($spreadsheet);
        
        \Illuminate\Support\Facades\Storage::makeDirectory('arsip_laporan');
        $writer->save(\Illuminate\Support\Facades\Storage::path($path));
        
        \App\Models\ArsipLaporan::create([
            'tipe_laporan' => 'Neraca',
            'format' => 'EXCEL',
            'nama_file' => $filename,
            'file_path' => $path,
            'data_hash' => $dataHash,
            'filter_info' => 'Per ' . \Carbon\Carbon::parse($this->neraca['tanggal'])->translatedFormat('d F Y'),
            'dibuat_oleh' => auth()->id(),
        ]);
        
        return \Illuminate\Support\Facades\Storage::download($path, $filename);
    }
}
