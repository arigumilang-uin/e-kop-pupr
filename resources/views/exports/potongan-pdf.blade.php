<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Potongan TPP</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 9px; color: #44403c; line-height: 1.4; }
        
        .header { text-align: center; margin-bottom: 16px; border-bottom: 1px solid #d6d3d1; padding-bottom: 12px; }
        .header h1 { font-size: 14px; font-weight: bold; color: #292524; letter-spacing: 1px; }
        .header h2 { font-size: 11px; font-weight: bold; margin-top: 4px; color: #44403c; }
        .header .meta { font-size: 8px; color: #78716c; margin-top: 6px; }

        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        
        thead th {
            background-color: #f5f5f4;
            color: #44403c;
            font-size: 8.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            padding: 8px 4px;
            text-align: center;
            border: 1px solid #d6d3d1;
        }
        thead th.left { text-align: left; }

        tbody td {
            padding: 6px 4px;
            border: 1px solid #e7e5e4;
            font-size: 8.5px;
            vertical-align: middle;
            color: #44403c;
        }
        
        .row-even { background-color: #fafaf9; }
        
        .num { text-align: right; font-family: 'DejaVu Sans Mono', monospace; font-size: 8px; }
        .center { text-align: center; }
        .bold { font-weight: bold; }
        
        .grand-total-row td {
            background-color: #ecfdf5;
            font-weight: bold;
            color: #065f46;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 9px;
            border-top: 2px solid #a7f3d0;
            border-bottom: 2px solid #a7f3d0;
        }
        
        .subtotal-row td {
            background-color: #f3f4f6;
            color: #374151;
            font-weight: bold;
            font-size: 9px;
            border-top: 2px solid #d1d5db;
            border-bottom: 1px solid #e5e7eb;
            padding-top: 8px;
            padding-bottom: 8px;
        }

        .footer {
            margin-top: 20px;
            font-size: 8px;
            color: #78716c;
            display: flex;
            justify-content: space-between;
        }
        .footer-left { float: left; }
        .footer-right { float: right; text-align: right; }

        .sign-section { margin-top: 40px; page-break-inside: avoid; }
        .sign-grid { width: 100%; border-collapse: collapse; }
        .sign-grid td { text-align: center; vertical-align: top; padding: 0 16px; border: none; font-size: 9px; }
        .sign-space { height: 60px; }

        @page { margin: 12mm 10mm 12mm 10mm; size: A4 landscape; }
    </style>
</head>
<body>

    <div class="header">
        <h1>DAFTAR POTONGAN TPP ANGGOTA KOPERASI KONSUMEN TIRTA BINA KARYA</h1>
        <h2>DINAS PUPRPKPP PROVINSI RIAU</h2>
        <h2>TAHUN {{ $filterYear }}</h2>
        <div class="meta">
            Periode: {{ $filterInfo }} &nbsp;|&nbsp; Dicetak: {{ now()->translatedFormat('d F Y, H:i') }} WIB
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2" style="width: 25px;">No</th>
                <th colspan="2" rowspan="2" style="width: 200px;">NIP / Nama / Unit Kerja</th>
                <th colspan="3">Rincian Potongan TPP</th>
                <th rowspan="2" style="width: 80px;">Total Potongan</th>
            </tr>
            <tr>
                <th style="width: 80px;">S. Pokok</th>
                <th style="width: 80px;">S. Wajib</th>
                <th style="width: 90px;">Angsuran Pinjaman</th>
            </tr>
        </thead>
        <tbody>
            <!-- Grand Total Row -->
            <tr class="grand-total-row">
                <td colspan="3" class="center">TOTAL SELURUHNYA</td>
                <td class="num">{{ number_format($grandTotals['pokok'], 0, ',', '.') }}</td>
                <td class="num">{{ number_format($grandTotals['wajib'], 0, ',', '.') }}</td>
                <td class="num">{{ number_format($grandTotals['pinjaman'], 0, ',', '.') }}</td>
                <td class="num" style="font-size: 9px;">{{ number_format($grandTotals['total'], 0, ',', '.') }}</td>
            </tr>

            <!-- Group By Bidang -->
            @php $groupedRows = collect($rows)->groupBy('bidang'); @endphp
            
            @foreach($groupedRows as $bidangName => $anggotaList)
                <!-- Subtotal Unit Kerja -->
                <tr class="subtotal-row">
                    <td colspan="3" style="text-indent: 5px;">{{ mb_strtoupper($bidangName) }}</td>
                    <td class="num">{{ number_format($anggotaList->sum('pokok'), 0, ',', '.') }}</td>
                    <td class="num">{{ number_format($anggotaList->sum('wajib'), 0, ',', '.') }}</td>
                    <td class="num">{{ number_format($anggotaList->sum('pinjaman'), 0, ',', '.') }}</td>
                    <td class="num" style="font-size: 9px;">{{ number_format($anggotaList->sum('total'), 0, ',', '.') }}</td>
                </tr>

                <!-- Anggota List -->
                @foreach($anggotaList as $idx => $row)
                <tr class="{{ $idx % 2 == 1 ? 'row-even' : '' }}">
                    <td class="center">{{ $idx + 1 }}</td>
                    <td class="center" style="width: 75px; font-family: 'DejaVu Sans Mono', monospace; font-size: 7.5px;">{{ $row['nip'] }}</td>
                    <td class="bold" style="font-size: 9px;">{{ $row['nama'] }}</td>
                    
                    <td class="num">{{ $row['pokok'] > 0 ? number_format($row['pokok'], 0, ',', '.') : '-' }}</td>
                    <td class="num">{{ $row['wajib'] > 0 ? number_format($row['wajib'], 0, ',', '.') : '-' }}</td>
                    <td class="num">{{ $row['pinjaman'] > 0 ? number_format($row['pinjaman'], 0, ',', '.') : '-' }}</td>
                    
                    <td class="num bold">{{ number_format($row['total'], 0, ',', '.') }}</td>
                </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>

    <div class="sign-section">
        <table class="sign-grid">
            <tr>
                <td>Mengetahui,</td>
                <td></td>
                <td>Pekanbaru, {{ now()->translatedFormat('d F Y') }}</td>
            </tr>
            <tr>
                <td><strong>Ketua Koperasi</strong></td>
                <td><strong>Pengawas</strong></td>
                <td><strong>Bendahara</strong></td>
            </tr>
            <tr><td class="sign-space"></td><td class="sign-space"></td><td class="sign-space"></td></tr>
            <tr>
                <td style="border-top: 1px solid #78716c;">( ............................ )</td>
                <td style="border-top: 1px solid #78716c;">( ............................ )</td>
                <td style="border-top: 1px solid #78716c;">( ............................ )</td>
            </tr>
        </table>
    </div>

    <div class="footer" style="margin-top: 16px;">
        <span class="footer-left">Dokumen ini dicetak secara otomatis dari Sistem e-Koperasi PUPR Riau.</span>
    </div>

</body>
</html>
