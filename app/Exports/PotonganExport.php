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
        
        // Cek arsip laporan
        $dataHash = md5('Potongan TPP' . 'EXCEL' . json_encode($data));
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

        $jenisFilter = $this->request->input('jenis');

        // 2. Headers
        $row = 6;
        $sheet->mergeCells("A{$row}:A".($row+1));
        $sheet->setCellValue("A{$row}", 'No');

        $sheet->mergeCells("B{$row}:C".($row+1));
        $sheet->setCellValue("B{$row}", 'NIP / Nama / Unit Kerja');

        if ($jenisFilter === 'pinjaman') {
            $sheet->mergeCells("D{$row}:F{$row}");
            $sheet->setCellValue("D{$row}", 'Rincian Angsuran');
            $sheet->setCellValue("D".($row+1), 'Pokok Pinjaman');
            $sheet->setCellValue("E".($row+1), 'Bunga');
            $sheet->setCellValue("F".($row+1), 'Total Angsuran');
            
            $sheet->mergeCells("G{$row}:G".($row+1));
            $sheet->setCellValue("G{$row}", '');
        } elseif ($jenisFilter === 'pokok') {
            $sheet->mergeCells("D{$row}:D".($row+1));
            $sheet->setCellValue("D{$row}", 'Pot. Pokok');
            $sheet->mergeCells("E{$row}:F".($row+1));
            $sheet->setCellValue("E{$row}", '');
            $sheet->mergeCells("G{$row}:G".($row+1));
            $sheet->setCellValue("G{$row}", 'Total Potongan');
        } elseif ($jenisFilter === 'wajib') {
            $sheet->mergeCells("D{$row}:D".($row+1));
            $sheet->setCellValue("D{$row}", 'Pot. Wajib');
            $sheet->mergeCells("E{$row}:F".($row+1));
            $sheet->setCellValue("E{$row}", '');
            $sheet->mergeCells("G{$row}:G".($row+1));
            $sheet->setCellValue("G{$row}", 'Total Potongan');
        } else {
            $sheet->mergeCells("D{$row}:F{$row}");
            $sheet->setCellValue("D{$row}", 'Rincian Potongan TPP');
            $sheet->setCellValue("D".($row+1), 'S. Pokok');
            $sheet->setCellValue("E".($row+1), 'S. Wajib');
            $sheet->setCellValue("F".($row+1), 'Pinjaman');
            
            $sheet->mergeCells("G{$row}:G".($row+1));
            $sheet->setCellValue("G{$row}", 'Total Potongan');
        }

        $sheet->getStyle("A{$row}:G".($row+1))->applyFromArray($headerStyle);
        $row += 2;

        // 3. Grand Total at Top
        $sheet->mergeCells("A{$row}:C{$row}");
        $sheet->setCellValue("A{$row}", 'TOTAL SELURUHNYA');
        
        $gt = $data['grandTotals'];
        if ($jenisFilter === 'pinjaman') {
            $sheet->setCellValue("D{$row}", $gt['pinjaman_pokok']);
            $sheet->setCellValue("E{$row}", $gt['pinjaman_bunga']);
            $sheet->setCellValue("F{$row}", $gt['pinjaman']);
            $sheet->setCellValue("G{$row}", '');
        } elseif ($jenisFilter === 'pokok') {
            $sheet->setCellValue("D{$row}", $gt['pokok']);
            $sheet->setCellValue("E{$row}", '');
            $sheet->setCellValue("F{$row}", '');
            $sheet->setCellValue("G{$row}", $gt['total']);
        } elseif ($jenisFilter === 'wajib') {
            $sheet->setCellValue("D{$row}", $gt['wajib']);
            $sheet->setCellValue("E{$row}", '');
            $sheet->setCellValue("F{$row}", '');
            $sheet->setCellValue("G{$row}", $gt['total']);
        } else {
            $sheet->setCellValue("D{$row}", $gt['pokok']);
            $sheet->setCellValue("E{$row}", $gt['wajib']);
            $sheet->setCellValue("F{$row}", $gt['pinjaman']);
            $sheet->setCellValue("G{$row}", $gt['total']);
        }
        
        $sheet->getStyle("A{$row}:G{$row}")->applyFromArray($grandTotalStyle);
        $sheet->getStyle("D{$row}:G{$row}")->getNumberFormat()->setFormatCode('#,##0');
        $row++;

        // 4. Data by Bidang
        $groupedRows = collect($data['rows'])->groupBy('bidang');

        foreach ($groupedRows as $bidangName => $anggotaList) {
            // Unit Kerja Sub-header / Total
            $sheet->mergeCells("A{$row}:C{$row}");
            $sheet->setCellValue("A{$row}", mb_strtoupper($bidangName));
            
            if ($jenisFilter === 'pinjaman') {
                $sheet->setCellValue("D{$row}", $anggotaList->sum('pinjaman_pokok'));
                $sheet->setCellValue("E{$row}", $anggotaList->sum('pinjaman_bunga'));
                $sheet->setCellValue("F{$row}", $anggotaList->sum('pinjaman'));
                $sheet->setCellValue("G{$row}", '');
            } elseif ($jenisFilter === 'pokok') {
                $sheet->setCellValue("D{$row}", $anggotaList->sum('pokok'));
                $sheet->setCellValue("E{$row}", '');
                $sheet->setCellValue("F{$row}", '');
                $sheet->setCellValue("G{$row}", $anggotaList->sum('total'));
            } elseif ($jenisFilter === 'wajib') {
                $sheet->setCellValue("D{$row}", $anggotaList->sum('wajib'));
                $sheet->setCellValue("E{$row}", '');
                $sheet->setCellValue("F{$row}", '');
                $sheet->setCellValue("G{$row}", $anggotaList->sum('total'));
            } else {
                $sheet->setCellValue("D{$row}", $anggotaList->sum('pokok'));
                $sheet->setCellValue("E{$row}", $anggotaList->sum('wajib'));
                $sheet->setCellValue("F{$row}", $anggotaList->sum('pinjaman'));
                $sheet->setCellValue("G{$row}", $anggotaList->sum('total'));
            }

            $sheet->getStyle("A{$row}:G{$row}")->applyFromArray($subtotalStyle);
            $sheet->getStyle("D{$row}:G{$row}")->getNumberFormat()->setFormatCode('#,##0');
            $row++;

            // Anggota Rows
            $no = 1;
            foreach ($anggotaList as $anggota) {
                $sheet->setCellValue("A{$row}", $no++);
                $sheet->setCellValueExplicit("B{$row}", $anggota['nip'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $sheet->setCellValue("C{$row}", $anggota['nama']);
                
                if ($jenisFilter === 'pinjaman') {
                    $sheet->setCellValue("D{$row}", $anggota['pinjaman_pokok']);
                    $sheet->setCellValue("E{$row}", $anggota['pinjaman_bunga']);
                    $sheet->setCellValue("F{$row}", $anggota['pinjaman']);
                    $sheet->setCellValue("G{$row}", '');
                } elseif ($jenisFilter === 'pokok') {
                    $sheet->setCellValue("D{$row}", $anggota['pokok']);
                    $sheet->setCellValue("E{$row}", '');
                    $sheet->setCellValue("F{$row}", '');
                    $sheet->setCellValue("G{$row}", $anggota['total']);
                } elseif ($jenisFilter === 'wajib') {
                    $sheet->setCellValue("D{$row}", $anggota['wajib']);
                    $sheet->setCellValue("E{$row}", '');
                    $sheet->setCellValue("F{$row}", '');
                    $sheet->setCellValue("G{$row}", $anggota['total']);
                } else {
                    $sheet->setCellValue("D{$row}", $anggota['pokok']);
                    $sheet->setCellValue("E{$row}", $anggota['wajib']);
                    $sheet->setCellValue("F{$row}", $anggota['pinjaman']);
                    $sheet->setCellValue("G{$row}", $anggota['total']);
                }

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
        $sheet->getColumnDimension('D')->setWidth(18);
        $sheet->getColumnDimension('E')->setWidth(18);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(18);

        $filename = 'Laporan_Potongan_TPP_' . now()->format('Ymd_His') . '.xlsx';
        $path = 'arsip_laporan/' . $filename;
        
        $writer = new Xlsx($spreadsheet);
        
        \Illuminate\Support\Facades\Storage::makeDirectory('arsip_laporan');
        $writer->save(\Illuminate\Support\Facades\Storage::path($path));
        
        \App\Models\ArsipLaporan::create([
            'tipe_laporan' => 'Potongan TPP',
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
        
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);
        $search = $request->input('q');
        $bidangId = $request->input('bidang');
        $jenisFilter = $request->input('jenis');

        $jenisPokok = JenisSimpanan::pokok();
        $jenisWajib = JenisSimpanan::wajib();

        $pengaturan = resolve(PengaturanService::class);
        $nominalPokok = $pengaturan->simpananPokok();
        $nominalWajib = $pengaturan->simpananWajib((int) $month, (int) $year);

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
                ->whereNull('deleted_at')
                ->where(function ($q) { $q->where('status', 'aktif')->orWhereNull('status'); })
                ->pluck('anggota_id')
                ->toArray();
        }

        $jenisSim2025 = \App\Models\JenisSimpanan::sim2025();
        $hasSim2025Ids = [];
        if ($jenisSim2025 && (!$jenisFilter || $jenisFilter === 'pokok')) {
            $hasSim2025Ids = DB::table('simpanan')
                ->where('jenis_simpanan_id', $jenisSim2025->id)
                ->whereNull('deleted_at')
                ->where(function ($q) { $q->where('status', 'aktif')->orWhereNull('status'); })
                ->pluck('anggota_id')
                ->toArray();
        }

        $paidWajibIds = [];
        if (!$jenisFilter || $jenisFilter === 'wajib') {
            $paidWajibIds = DB::table('simpanan')
                ->where('jenis_simpanan_id', $jenisWajib->id ?? 0)
                ->where('bulan_untuk', $month)
                ->where('tahun_untuk', $year)
                ->whereNull('deleted_at')
                ->where(function ($q) { $q->where('status', 'aktif')->orWhereNull('status'); })
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
                    'angsuran.nominal_pokok',
                    'angsuran.nominal_bunga',
                    'angsuran.nominal_total'
                )
                ->get()
                ->groupBy('anggota_id');
        }

        $grandTotals = [
            'pokok' => 0, 'wajib' => 0, 'pinjaman' => 0, 'pinjaman_pokok' => 0, 'pinjaman_bunga' => 0, 'total' => 0,
        ];

        $rows = [];
        foreach ($anggotas as $anggota) {
            // Tentukan bulan mulai potongan TPP POKOK
            if ($anggota->tpp_mulai_pokok && $anggota->tpp_tahun_pokok) {
                $mulaiPotongPokok = Carbon::createFromDate($anggota->tpp_tahun_pokok, $anggota->tpp_mulai_pokok, 1);
            } else {
                $mulaiPotongPokok = $anggota->tanggal_masuk ? $anggota->tanggal_masuk->copy()->addMonth()->startOfMonth() : null;
            }

            // Tentukan bulan mulai potongan TPP WAJIB
            if ($anggota->tpp_mulai_wajib && $anggota->tpp_tahun_wajib) {
                $mulaiPotongWajib = Carbon::createFromDate($anggota->tpp_tahun_wajib, $anggota->tpp_mulai_wajib, 1);
            } else {
                $mulaiPotongWajib = $anggota->tanggal_masuk ? $anggota->tanggal_masuk->copy()->addMonth()->startOfMonth() : null;
            }

            $periodeFilter = Carbon::createFromDate($year, $month, 1);
            $belumWaktuPotongPokok = $mulaiPotongPokok && $periodeFilter->lt($mulaiPotongPokok);
            $belumWaktuPotongWajib = $mulaiPotongWajib && $periodeFilter->lt($mulaiPotongWajib);

            $potonganPokok = 0;
            if (!$jenisFilter || $jenisFilter === 'pokok') {
                $sudahBayarPokok = in_array($anggota->id, $paidPokokIds);
                $memilikiSim2025 = in_array($anggota->id, $hasSim2025Ids);
                $potonganPokok = ($sudahBayarPokok || $belumWaktuPotongPokok || $memilikiSim2025) ? 0 : $nominalPokok;
            }

            $potonganWajib = 0;
            if (!$jenisFilter || $jenisFilter === 'wajib') {
                $sudahBayarWajib = in_array($anggota->id, $paidWajibIds);
                $potonganWajib = ($sudahBayarWajib || $belumWaktuPotongWajib) ? 0 : $nominalWajib;
            }

            $potonganPinjaman = 0;
            $pinjamanPokok = 0;
            $pinjamanBunga = 0;
            if (!$jenisFilter || $jenisFilter === 'pinjaman') {
                if ($allPinjamanData->has($anggota->id)) {
                    $pinjamanData = $allPinjamanData->get($anggota->id);
                    $pinjamanPokok = $pinjamanData->sum('nominal_pokok');
                    $pinjamanBunga = $pinjamanData->sum('nominal_bunga');
                    $potonganPinjaman = $pinjamanData->sum('nominal_total');
                }
            }

            $totalPotongan = $potonganPokok + $potonganWajib + $potonganPinjaman;
            
            if ($totalPotongan > 0 || !$jenisFilter) {
                if ($jenisFilter && $totalPotongan <= 0) continue;

                $grandTotals['pokok'] += $potonganPokok;
                $grandTotals['wajib'] += $potonganWajib;
                $grandTotals['pinjaman'] += $potonganPinjaman;
                $grandTotals['pinjaman_pokok'] += $pinjamanPokok;
                $grandTotals['pinjaman_bunga'] += $pinjamanBunga;
                $grandTotals['total'] += $totalPotongan;

                $rows[] = [
                    'nama' => $anggota->nama,
                    'nip' => $anggota->nip,
                    'bidang' => $anggota->bidang->nama_bidang ?? 'LAINNYA',
                    'pokok' => $potonganPokok,
                    'wajib' => $potonganWajib,
                    'pinjaman' => $potonganPinjaman,
                    'pinjaman_pokok' => $pinjamanPokok,
                    'pinjaman_bunga' => $pinjamanBunga,
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
