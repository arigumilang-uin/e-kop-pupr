<?php

namespace App\Exports;

use App\Models\Anggota;
use App\Models\JenisSimpanan;
use App\Services\PengaturanService;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PotonganExport
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
        $sheet->setTitle('Potongan TPP');

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
        $sheet->mergeCells('A1:G1');
        $sheet->setCellValue('A1', 'DAFTAR POTONGAN TPP ANGGOTA KOPERASI KONSUMEN TIRTA BINA KARYA');
        $sheet->getStyle('A1')->applyFromArray($titleStyle);

        $sheet->mergeCells('A2:G2');
        $sheet->setCellValue('A2', 'DINAS PUPRPKPP PROVINSI RIAU');
        $sheet->getStyle('A2')->applyFromArray($titleStyle);

        $sheet->mergeCells('A3:G3');
        $sheet->setCellValue('A3', 'TAHUN ' . $data['filterYear']);
        $sheet->getStyle('A3')->applyFromArray($titleStyle);

        $sheet->mergeCells('A4:G4');
        $sheet->setCellValue('A4', 'Periode: ' . $data['filterInfo']);
        $sheet->getStyle('A4')->applyFromArray([
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        // 2. Headers
        $row = 6;
        $sheet->mergeCells("A{$row}:A".($row+1));
        $sheet->setCellValue("A{$row}", 'No');

        $sheet->mergeCells("B{$row}:C".($row+1));
        $sheet->setCellValue("B{$row}", 'NIP / Nama / Unit Kerja');

        $sheet->mergeCells("D{$row}:F{$row}");
        $sheet->setCellValue("D{$row}", 'Rincian Potongan TPP');

        $sheet->setCellValue("D".($row+1), 'S. Pokok');
        $sheet->setCellValue("E".($row+1), 'S. Wajib');
        $sheet->setCellValue("F".($row+1), 'Pinjaman');

        $sheet->mergeCells("G{$row}:G".($row+1));
        $sheet->setCellValue("G{$row}", 'Total Potongan');

        $sheet->getStyle("A{$row}:G".($row+1))->applyFromArray($headerStyle);
        $row += 2;

        // 3. Grand Total at Top
        $sheet->mergeCells("A{$row}:C{$row}");
        $sheet->setCellValue("A{$row}", 'TOTAL SELURUHNYA');
        
        $gt = $data['grandTotals'];
        $sheet->setCellValue("D{$row}", $gt['pokok']);
        $sheet->setCellValue("E{$row}", $gt['wajib']);
        $sheet->setCellValue("F{$row}", $gt['pinjaman']);
        $sheet->setCellValue("G{$row}", $gt['total']);
        $sheet->getStyle("A{$row}:G{$row}")->applyFromArray($grandTotalStyle);
        $sheet->getStyle("D{$row}:G{$row}")->getNumberFormat()->setFormatCode('#,##0');
        $row++;

        // 4. Data by Bidang
        $groupedRows = collect($data['rows'])->groupBy('bidang');

        foreach ($groupedRows as $bidangName => $anggotaList) {
            // Unit Kerja Sub-header / Total
            $sheet->mergeCells("A{$row}:C{$row}");
            $sheet->setCellValue("A{$row}", mb_strtoupper($bidangName));
            
            // Calculate subtotal
            $subPokok = $anggotaList->sum('pokok');
            $subWajib = $anggotaList->sum('wajib');
            $subPinjaman = $anggotaList->sum('pinjaman');
            $subTotal = $anggotaList->sum('total');

            $sheet->setCellValue("D{$row}", $subPokok);
            $sheet->setCellValue("E{$row}", $subWajib);
            $sheet->setCellValue("F{$row}", $subPinjaman);
            $sheet->setCellValue("G{$row}", $subTotal);
            $sheet->getStyle("A{$row}:G{$row}")->applyFromArray($subtotalStyle);
            $sheet->getStyle("D{$row}:G{$row}")->getNumberFormat()->setFormatCode('#,##0');
            $row++;

            // Anggota Rows
            $no = 1;
            foreach ($anggotaList as $anggota) {
                $sheet->setCellValue("A{$row}", $no++);
                $sheet->setCellValueExplicit("B{$row}", $anggota['nip'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $sheet->setCellValue("C{$row}", $anggota['nama']);
                $sheet->setCellValue("D{$row}", $anggota['pokok']);
                $sheet->setCellValue("E{$row}", $anggota['wajib']);
                $sheet->setCellValue("F{$row}", $anggota['pinjaman']);
                $sheet->setCellValue("G{$row}", $anggota['total']);

                $sheet->getStyle("A{$row}:G{$row}")->applyFromArray($cellStyle);
                $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("D{$row}:G{$row}")->getNumberFormat()->setFormatCode('#,##0');
                $row++;
            }
        }

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(20); // NIP
        $sheet->getColumnDimension('C')->setWidth(35); // Nama
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(18);

        $filename = 'Laporan_Potongan_TPP_' . now()->format('Ymd_His') . '.xlsx';
        
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
        
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);
        $search = $request->input('q');
        $bidangId = $request->input('bidang');
        $jenisFilter = $request->input('jenis');

        $jenisPokok = JenisSimpanan::pokok();
        $jenisWajib = JenisSimpanan::wajib();

        $pengaturan = resolve(PengaturanService::class);
        $nominalPokok = $pengaturan->simpananPokok();
        $nominalWajib = $pengaturan->simpananWajib();

        $query = Anggota::aktif()->with(['bidang'])->orderBy('bidang_id')->orderBy('nama');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%");
            });
        }

        if ($bidangId) {
            $query->where('bidang_id', $bidangId);
        }

        $anggotas = $query->get();

        $paidPokokIds = [];
        if (!$jenisFilter || $jenisFilter === 'pokok') {
            $paidPokokIds = DB::table('simpanan')
                ->where('jenis_simpanan_id', $jenisPokok->id ?? 0)
                ->pluck('anggota_id')
                ->toArray();
        }

        $paidWajibIds = [];
        if (!$jenisFilter || $jenisFilter === 'wajib') {
            $paidWajibIds = DB::table('simpanan')
                ->where('jenis_simpanan_id', $jenisWajib->id ?? 0)
                ->where('bulan_untuk', $month)
                ->where('tahun_untuk', $year)
                ->pluck('anggota_id')
                ->toArray();
        }

        $allPinjamanData = collect();
        if (!$jenisFilter || $jenisFilter === 'pinjaman') {
            $allPinjamanData = DB::table('angsuran')
                ->join('pinjaman', 'angsuran.pinjaman_id', '=', 'pinjaman.id')
                ->where('angsuran.status', 'belum')
                ->whereMonth('angsuran.tanggal_jatuh_tempo', $month)
                ->whereYear('angsuran.tanggal_jatuh_tempo', $year)
                ->select(
                    'pinjaman.anggota_id',
                    'angsuran.nominal_total'
                )
                ->get()
                ->groupBy('anggota_id');
        }

        $grandTotals = [
            'pokok' => 0, 'wajib' => 0, 'pinjaman' => 0, 'total' => 0,
        ];

        $rows = [];
        foreach ($anggotas as $anggota) {
            $tanggalMasuk = $anggota->tanggal_masuk;
            $bulanMulaiPotongan = $tanggalMasuk ? $tanggalMasuk->copy()->addMonth()->startOfMonth() : null;
            $periodeFilter = Carbon::createFromDate($year, $month, 1);
            $belumWaktunyaDipotong = $bulanMulaiPotongan && $periodeFilter->lt($bulanMulaiPotongan);

            $potonganPokok = 0;
            if (!$jenisFilter || $jenisFilter === 'pokok') {
                $sudahBayarPokok = in_array($anggota->id, $paidPokokIds);
                $potonganPokok = ($sudahBayarPokok || $belumWaktunyaDipotong) ? 0 : $nominalPokok;
            }

            $potonganWajib = 0;
            if (!$jenisFilter || $jenisFilter === 'wajib') {
                $sudahBayarWajib = in_array($anggota->id, $paidWajibIds);
                $potonganWajib = ($sudahBayarWajib || $belumWaktunyaDipotong) ? 0 : $nominalWajib;
            }

            $potonganPinjaman = 0;
            if (!$jenisFilter || $jenisFilter === 'pinjaman') {
                if ($allPinjamanData->has($anggota->id)) {
                    $pinjamanData = $allPinjamanData->get($anggota->id);
                    $potonganPinjaman = $pinjamanData->sum('nominal_total');
                }
            }

            $totalPotongan = $potonganPokok + $potonganWajib + $potonganPinjaman;
            
            if ($totalPotongan > 0 || !$jenisFilter) {
                if ($jenisFilter && $totalPotongan <= 0) continue;

                $grandTotals['pokok'] += $potonganPokok;
                $grandTotals['wajib'] += $potonganWajib;
                $grandTotals['pinjaman'] += $potonganPinjaman;
                $grandTotals['total'] += $totalPotongan;

                $rows[] = [
                    'nama' => $anggota->nama,
                    'nip' => $anggota->nip,
                    'bidang' => $anggota->bidang->nama_bidang ?? 'LAINNYA',
                    'pokok' => $potonganPokok,
                    'wajib' => $potonganWajib,
                    'pinjaman' => $potonganPinjaman,
                    'total' => $totalPotongan,
                ];
            }
        }

        return [
            'rows' => $rows,
            'grandTotals' => $grandTotals,
            'filterYear' => $year,
            'filterInfo' => $this->getFilterInfo($month, $year),
        ];
    }

    private function getFilterInfo($month, $year): string
    {
        $parts = [];
        $parts[] = 'Bulan: ' . date('F', mktime(0, 0, 0, $month, 1)) . " $year";
        
        if ($this->request->filled('bidang')) {
            $bidang = \App\Models\Bidang::find($this->request->bidang);
            if ($bidang) $parts[] = 'Bidang: ' . $bidang->nama_bidang;
        }
        if ($this->request->filled('jenis')) {
            $parts[] = 'Jenis: ' . strtoupper($this->request->jenis);
        }
        if ($this->request->filled('q')) {
            $parts[] = 'Pencarian: "' . $this->request->q . '"';
        }
        return implode(' | ', $parts);
    }
}
