<?php

namespace App\Exports;

use App\Models\Anggota;
use App\Models\JenisSimpanan;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class SimpananExport
{
    private Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function download()
    {
        $data = $this->getData();
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Simpanan Anggota');

        // Style Definition
        $titleStyle = [
            'font' => ['bold' => true, 'size' => 12],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ];

        $headerStyle = [
            'font' => ['bold' => true],
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
                'startColor' => ['rgb' => 'E2EFDA'], // Light green
            ]
        ];

        $cellStyle = [
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
        ];

        $subtotalStyle = [
            'font' => ['bold' => true],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFF2CC'],
            ]
        ];
        
        $grandTotalStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1F4E78'],
            ]
        ];

        // 1. Titles
        $sheet->mergeCells('A1:J1');
        $sheet->setCellValue('A1', 'DAFTAR SIMPANAN ANGGOTA KOPERASI KONSUMEN TIRTA BINA KARYA');
        $sheet->getStyle('A1')->applyFromArray($titleStyle);

        $sheet->mergeCells('A2:J2');
        $sheet->setCellValue('A2', 'DINAS PUPRPKPP PROVINSI RIAU');
        $sheet->getStyle('A2')->applyFromArray($titleStyle);

        $sheet->mergeCells('A3:J3');
        $year = now()->year;
        if ($this->request->filled('dari_tanggal')) {
            $year = \Carbon\Carbon::parse($this->request->dari_tanggal)->year;
        }
        $sheet->setCellValue('A3', 'TAHUN ' . $year);
        $sheet->getStyle('A3')->applyFromArray($titleStyle);

        $sheet->mergeCells('A4:J4');
        $sheet->setCellValue('A4', 'Periode: ' . $this->getFilterInfo());
        $sheet->getStyle('A4')->applyFromArray([
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        // 2. Headers
        $row = 6;
        $sheet->mergeCells("A{$row}:A".($row+1));
        $sheet->setCellValue("A{$row}", 'No');

        $sheet->mergeCells("B{$row}:C".($row+1));
        $sheet->setCellValue("B{$row}", 'NIP / Nama / Unit Kerja');

        $sheet->mergeCells("D{$row}:H{$row}");
        $sheet->setCellValue("D{$row}", 'Simpanan Anggota');

        $sheet->setCellValue("D".($row+1), 'Simpanan 2025');
        $sheet->setCellValue("E".($row+1), 'Pokok');
        $sheet->setCellValue("F".($row+1), 'Wajib');
        $sheet->setCellValue("G".($row+1), 'SWP');
        $sheet->setCellValue("H".($row+1), 'Bonus SHU');

        $sheet->mergeCells("I{$row}:J{$row}");
        $sheet->setCellValue("I{$row}", 'Total Simpanan');
        $sheet->mergeCells("I".($row+1).":J".($row+1));
        // Add one more line for aesthetic but let's just merge them properly
        $sheet->unmergeCells("I{$row}:J{$row}");
        $sheet->unmergeCells("I".($row+1).":J".($row+1));
        $sheet->mergeCells("I{$row}:J".($row+1));
        $sheet->setCellValue("I{$row}", 'Total Simpanan');

        $sheet->getStyle("A{$row}:J".($row+1))->applyFromArray($headerStyle);
        $row += 2;

        // 3. Grand Total at Top
        $sheet->mergeCells("A{$row}:C{$row}");
        $sheet->setCellValue("A{$row}", 'TOTAL SELURUHNYA');
        
        $gt = $data['grandTotals'];
        $sheet->setCellValue("D{$row}", $gt['sim2025']);
        $sheet->setCellValue("E{$row}", $gt['pokok']);
        $sheet->setCellValue("F{$row}", $gt['wajib']);
        $sheet->setCellValue("G{$row}", $gt['swp']);
        $sheet->setCellValue("H{$row}", $gt['bonus_shu']);
        $sheet->mergeCells("I{$row}:J{$row}");
        $sheet->setCellValue("I{$row}", $gt['total']);
        $sheet->getStyle("A{$row}:J{$row}")->applyFromArray($grandTotalStyle);
        $sheet->getStyle("D{$row}:J{$row}")->getNumberFormat()->setFormatCode('#,##0');
        $row++;

        // 4. Data by Bidang
        $groupedRows = collect($data['rows'])->groupBy('bidang');

        foreach ($groupedRows as $bidangName => $anggotaList) {
            // Unit Kerja Sub-header / Total
            $sheet->mergeCells("A{$row}:C{$row}");
            $sheet->setCellValue("A{$row}", mb_strtoupper($bidangName));
            
            // Calculate subtotal
            $subSim2025 = $anggotaList->sum('sim2025');
            $subPokok = $anggotaList->sum('pokok');
            $subWajib = $anggotaList->sum('wajib');
            $subSwp = $anggotaList->sum('swp');
            $subBonus = $anggotaList->sum('bonus_shu');
            $subTotal = $anggotaList->sum('total');

            $sheet->setCellValue("D{$row}", $subSim2025);
            $sheet->setCellValue("E{$row}", $subPokok);
            $sheet->setCellValue("F{$row}", $subWajib);
            $sheet->setCellValue("G{$row}", $subSwp);
            $sheet->setCellValue("H{$row}", $subBonus);
            
            $sheet->mergeCells("I{$row}:J{$row}");
            $sheet->setCellValue("I{$row}", $subTotal);
            $sheet->getStyle("A{$row}:J{$row}")->applyFromArray($subtotalStyle);
            $sheet->getStyle("D{$row}:J{$row}")->getNumberFormat()->setFormatCode('#,##0');
            $row++;

            // Anggota Rows
            $no = 1;
            foreach ($anggotaList as $anggota) {
                $sheet->setCellValue("A{$row}", $no++);
                $sheet->setCellValueExplicit("B{$row}", $anggota['nip'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $sheet->setCellValue("C{$row}", $anggota['nama']);
                $sheet->setCellValue("D{$row}", $anggota['sim2025']);
                $sheet->setCellValue("E{$row}", $anggota['pokok']);
                $sheet->setCellValue("F{$row}", $anggota['wajib']);
                $sheet->setCellValue("G{$row}", $anggota['swp']);
                $sheet->setCellValue("H{$row}", $anggota['bonus_shu']);
                $sheet->mergeCells("I{$row}:J{$row}");
                $sheet->setCellValue("I{$row}", $anggota['total']);

                $sheet->getStyle("A{$row}:J{$row}")->applyFromArray($cellStyle);
                $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("D{$row}:J{$row}")->getNumberFormat()->setFormatCode('#,##0');
                $row++;
            }
        }

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(20); // NIP
        $sheet->getColumnDimension('C')->setWidth(35); // Nama
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(15);
        $sheet->getColumnDimension('H')->setWidth(15);
        $sheet->getColumnDimension('I')->setWidth(10);
        $sheet->getColumnDimension('J')->setWidth(10);

        $filename = 'Laporan_Simpanan_' . now()->format('Ymd_His') . '.xlsx';
        $path = storage_path('app/public/' . $filename);
        
        $writer = new Xlsx($spreadsheet);
        
        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function getData(): array
    {
        $request = $this->request;

        $anggotaFilters = function ($q) use ($request) {
            $q->where('status', \App\Enums\StatusAnggota::Aktif);

            if ($request->filled('q')) {
                $q->where(function ($sub) use ($request) {
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

        $sampaiTanggal = $request->input('sampai_tanggal');
        $dariTanggal = $request->input('dari_tanggal');

        $sumSetor = function ($jenisId) use ($sampaiTanggal, $dariTanggal) {
            return function ($q) use ($jenisId, $sampaiTanggal, $dariTanggal) {
                $q->where('jenis_simpanan_id', $jenisId);
                if ($dariTanggal) $q->where('tanggal', '>=', $dariTanggal);
                if ($sampaiTanggal) $q->where('tanggal', '<=', $sampaiTanggal);
            };
        };

        $sumTarik = function ($jenisId) use ($sampaiTanggal, $dariTanggal) {
            return function ($q) use ($jenisId, $sampaiTanggal, $dariTanggal) {
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

        $anggotas = Anggota::with('bidang')
            ->where($anggotaFilters)
            ->withSum(['simpanan as sum_pokok' => $sumSetor($jenisPokokId)], 'nominal')
            ->withSum(['penarikanSimpanan as tarik_pokok' => $sumTarik($jenisPokokId)], 'nominal')
            ->withSum(['simpanan as sum_wajib' => $sumSetor($jenisWajibId)], 'nominal')
            ->withSum(['penarikanSimpanan as tarik_wajib' => $sumTarik($jenisWajibId)], 'nominal')
            ->withSum(['simpanan as sum_sim2025' => $sumSetor($jenisSim2025Id)], 'nominal')
            ->withSum(['penarikanSimpanan as tarik_sim2025' => $sumTarik($jenisSim2025Id)], 'nominal')
            ->withSum(['simpanan as sum_swp' => $sumSetor($jenisSwpId)], 'nominal')
            ->withSum(['penarikanSimpanan as tarik_swp' => $sumTarik($jenisSwpId)], 'nominal')
            ->withSum(['simpanan as sum_bonus_shu' => $sumSetor($jenisBonusShuId)], 'nominal')
            ->withSum(['penarikanSimpanan as tarik_bonus_shu' => $sumTarik($jenisBonusShuId)], 'nominal')
            ->orderBy('bidang_id')
            ->orderBy('nama')
            ->get();

        $grandTotals = [
            'sim2025' => 0, 'pokok' => 0, 'wajib' => 0, 'swp' => 0, 'bonus_shu' => 0, 'total' => 0,
        ];

        $rows = [];
        foreach ($anggotas as $anggota) {
            $sim2025 = max(0, ($anggota->sum_sim2025 ?? 0) - ($anggota->tarik_sim2025 ?? 0));
            $pokok = max(0, ($anggota->sum_pokok ?? 0) - ($anggota->tarik_pokok ?? 0));
            $wajib = max(0, ($anggota->sum_wajib ?? 0) - ($anggota->tarik_wajib ?? 0));
            $swp = max(0, ($anggota->sum_swp ?? 0) - ($anggota->tarik_swp ?? 0));
            $bonusShu = max(0, ($anggota->sum_bonus_shu ?? 0) - ($anggota->tarik_bonus_shu ?? 0));
            $total = $sim2025 + $pokok + $wajib + $swp + $bonusShu;

            $grandTotals['sim2025'] += $sim2025;
            $grandTotals['pokok'] += $pokok;
            $grandTotals['wajib'] += $wajib;
            $grandTotals['swp'] += $swp;
            $grandTotals['bonus_shu'] += $bonusShu;
            $grandTotals['total'] += $total;

            $rows[] = [
                'nama' => $anggota->nama,
                'nip' => $anggota->nip,
                'bidang' => $anggota->bidang->nama_bidang ?? 'LAINNYA',
                'golongan' => $anggota->golongan_asn?->label() ?? '-',
                'sim2025' => $sim2025,
                'pokok' => $pokok,
                'wajib' => $wajib,
                'swp' => $swp,
                'bonus_shu' => $bonusShu,
                'total' => $total,
            ];
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
        if ($this->request->filled('dari_tanggal') || $this->request->filled('sampai_tanggal')) {
            $start = $this->request->filled('dari_tanggal') 
                ? \Carbon\Carbon::parse($this->request->dari_tanggal)->translatedFormat('d F Y') 
                : 'Awal';
            $end = $this->request->filled('sampai_tanggal') 
                ? \Carbon\Carbon::parse($this->request->sampai_tanggal)->translatedFormat('d F Y') 
                : 'Sekarang';
            $parts[] = "$start - $end";
        }
        
        if ($this->request->filled('bidang')) {
            $bidang = \App\Models\Bidang::find($this->request->bidang);
            if ($bidang) $parts[] = 'Bidang: ' . $bidang->nama_bidang;
        }
        if ($this->request->filled('golongan')) {
            $parts[] = 'Golongan: ' . $this->request->golongan;
        }
        if ($this->request->filled('q')) {
            $parts[] = 'Pencarian: "' . $this->request->q . '"';
        }
        return $parts ? implode(' | ', $parts) : 'Semua Data (Hingga ' . now()->translatedFormat('d F Y') . ')';
    }
}

