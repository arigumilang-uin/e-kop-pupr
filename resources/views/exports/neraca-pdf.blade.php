<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Neraca Keuangan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #333;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        .mb-2 { margin-bottom: 10px; }
        .mb-4 { margin-bottom: 20px; }
        .mt-4 { margin-top: 20px; }
        .uppercase { text-transform: uppercase; }
        
        .header { border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .title { font-size: 16px; font-weight: bold; }
        .subtitle { font-size: 14px; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { padding: 6px; border: 1px solid #ddd; }
        th { background-color: #f2f2f2; font-weight: bold; }
        
        .section-header { background-color: #e0e0e0; font-weight: bold; text-align: center; }
        .sub-header { background-color: #f5f5f5; font-weight: bold; }
        .total-row { background-color: #f0f0f0; font-weight: bold; }
        .grand-total { background-color: #000000; color: #ffffff; font-weight: bold; }
        
        .signature-table { border: none; margin-top: 40px; }
        .signature-table td { border: none; text-align: center; width: 33%; }
        .signature-space { height: 80px; }
    </style>
</head>
<body>

    <div class="header text-center">
        <div class="title uppercase mb-2">KOPERASI SIMPAN PINJAM KONSUMEN TIRTA BINA KARYA</div>
        <div class="title uppercase mb-2">DINAS PUPRPKPP PROVINSI RIAU</div>
        <div class="title uppercase mt-4">NERACA (LAPORAN POSISI KEUANGAN)</div>
        <div class="mb-2">Per {{ \Carbon\Carbon::parse($neraca['tanggal'])->translatedFormat('d F Y') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th colspan="2" class="section-header">AKTIVA</th>
                <th colspan="2" class="section-header">PASIVA</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <!-- AKTIVA COLUMN -->
                <td style="width: 25%; vertical-align: top; border-right: none;">
                    <div class="mb-2"><strong>Aktiva Lancar</strong></div>
                    <div>Kas & Setara Kas</div>
                    <div>Piutang Pinjaman Anggota</div>
                </td>
                <td style="width: 25%; vertical-align: top; text-align: right;">
                    <div class="mb-2">&nbsp;</div>
                    <div>{{ number_format($neraca['aktiva']['kas'], 0, ',', '.') }}</div>
                    <div>{{ number_format($neraca['aktiva']['piutang_pinjaman'], 0, ',', '.') }}</div>
                </td>

                <!-- PASIVA COLUMN -->
                <td style="width: 25%; vertical-align: top; border-right: none;">
                    <div class="mb-2"><strong>I. Kewajiban (Hutang)</strong></div>
                    @foreach($neraca['kewajiban']['simpanan_items'] as $item)
                        <div>{{ $item->nama }}</div>
                    @endforeach
                    <div>Cadangan Dana Resiko</div>
                    <div class="font-bold mt-2">Total Kewajiban</div>
                    
                    <div class="mt-4 mb-2"><strong>II. Modal / Ekuitas</strong></div>
                    @foreach($neraca['modal']['simpanan_items'] as $item)
                        <div>{{ $item->nama }}</div>
                    @endforeach
                    <div>Laba Ditahan (SHU Berjalan)</div>
                    <div class="font-bold mt-2">Total Modal / Ekuitas</div>
                </td>
                <td style="width: 25%; vertical-align: top; text-align: right;">
                    <div class="mb-2">&nbsp;</div>
                    @foreach($neraca['kewajiban']['simpanan_items'] as $item)
                        <div>{{ number_format($item->total, 0, ',', '.') }}</div>
                    @endforeach
                    <div>{{ number_format($neraca['kewajiban']['dana_resiko'], 0, ',', '.') }}</div>
                    <div class="font-bold mt-2">{{ number_format($neraca['kewajiban']['total'], 0, ',', '.') }}</div>

                    <div class="mt-4 mb-2">&nbsp;</div>
                    @foreach($neraca['modal']['simpanan_items'] as $item)
                        <div>{{ number_format($item->total, 0, ',', '.') }}</div>
                    @endforeach
                    <div>{{ number_format($neraca['modal']['laba_ditahan'], 0, ',', '.') }}</div>
                    <div class="font-bold mt-2">{{ number_format($neraca['modal']['total'], 0, ',', '.') }}</div>
                </td>
            </tr>
            <tr class="grand-total">
                <td>TOTAL AKTIVA</td>
                <td class="text-right">{{ number_format($neraca['aktiva']['total'], 0, ',', '.') }}</td>
                <td>TOTAL PASIVA</td>
                <td class="text-right">{{ number_format($neraca['pasiva']['total'], 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="mt-4" style="font-size: 10px;">
        <div><strong>Status Neraca:</strong> 
            @if($neraca['is_balance']) 
                SEIMBANG (Tidak ada selisih) 
            @else 
                TIDAK SEIMBANG (Selisih: {{ number_format($neraca['selisih'], 0, ',', '.') }}) 
            @endif
        </div>
    </div>

    <table class="signature-table">
        <tr>
            <td>
                Disusun oleh,
                <div class="signature-space"></div>
                <div class="font-bold">Bendahara</div>
            </td>
            <td>
                Diperiksa oleh,
                <div class="signature-space"></div>
                <div class="font-bold">Pengawas</div>
            </td>
            <td>
                Pekanbaru, {{ \Carbon\Carbon::parse($neraca['tanggal'])->translatedFormat('d F Y') }}<br>
                Disetujui oleh,
                <div class="signature-space"></div>
                <div class="font-bold">Ketua Koperasi</div>
            </td>
        </tr>
    </table>

</body>
</html>
