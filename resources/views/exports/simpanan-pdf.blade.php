<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Simpanan Anggota</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 9px; color: #000000; line-height: 1.4; }
        
        .header { text-align: center; margin-bottom: 16px; border-bottom: 1px solid #000000; padding-bottom: 12px; }
        .header h1 { font-size: 14px; font-weight: bold; color: #000000; letter-spacing: 1px; }
        .header h2 { font-size: 11px; font-weight: bold; margin-top: 4px; color: #000000; }
        .header .meta { font-size: 8px; color: #333333; margin-top: 6px; }

        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        
        thead th {
            background-color: #e0e0e0;
            color: #000000;
            font-size: 8.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            padding: 8px 4px;
            text-align: center;
            border: 1px solid #000000;
        }
        thead th.left { text-align: left; }

        tbody td {
            padding: 6px 4px;
            border: 1px solid #999999;
            font-size: 8.5px;
            vertical-align: middle;
            color: #000000;
        }
        
        .row-even { background-color: #ffffff; }
        
        .num { text-align: right; font-family: 'DejaVu Sans Mono', monospace; font-size: 8px; }
        .center { text-align: center; }
        .bold { font-weight: bold; }
        
        .grand-total-row td {
            background-color: #000000;
            font-weight: bold;
            color: #ffffff;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 9px;
            border-top: 2px solid #000000;
            border-bottom: 2px solid #000000;
        }
        
        .subtotal-row td {
            background-color: #e0e0e0;
            color: #000000;
            font-weight: bold;
            font-size: 9px;
            border-top: 2px solid #666666;
            border-bottom: 1px solid #999999;
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
        <h1>DAFTAR SIMPANAN ANGGOTA KOPERASI KONSUMEN TIRTA BINA KARYA</h1>
        <h2>DINAS PUPRPKPP PROVINSI RIAU</h2>
        @php
            $year = now()->year;
            if (isset($dari_tanggal)) {
                $year = \Carbon\Carbon::parse($dari_tanggal)->year;
            }
        @endphp
        <h2>TAHUN {{ $year }}</h2>
        <div class="meta">
            Periode: {{ $filterInfo }} &nbsp;|&nbsp; Dicetak: {{ now()->translatedFormat('d F Y, H:i') }} WIB
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2" style="width: 25px;">No</th>
                <th colspan="2" rowspan="2" style="width: 200px;">NIP / Nama / Unit Kerja</th>
                <th colspan="5">Simpanan Anggota</th>
                <th rowspan="2" style="width: 80px;">Total Simpanan</th>
            </tr>
            <tr>
                <th style="width: 70px;">Simpanan 2025</th>
                <th style="width: 70px;">Pokok</th>
                <th style="width: 70px;">Wajib</th>
                <th style="width: 70px;">SWP</th>
                <th style="width: 70px;">Bonus SHU</th>
            </tr>
        </thead>
        <tbody>
            <!-- Grand Total Row -->
            <tr class="grand-total-row">
                <td colspan="3" class="center">TOTAL SELURUHNYA</td>
                <td class="num">{{ number_format($grandTotals['sim2025'], 0, ',', '.') }}</td>
                <td class="num">{{ number_format($grandTotals['pokok'], 0, ',', '.') }}</td>
                <td class="num">{{ number_format($grandTotals['wajib'], 0, ',', '.') }}</td>
                <td class="num">{{ number_format($grandTotals['swp'], 0, ',', '.') }}</td>
                <td class="num">{{ number_format($grandTotals['bonus_shu'], 0, ',', '.') }}</td>
                <td class="num" style="font-size: 9px;">{{ number_format($grandTotals['total'], 0, ',', '.') }}</td>
            </tr>

            <!-- Group By Bidang -->
            @php $groupedRows = collect($rows)->groupBy('bidang'); @endphp
            
            @foreach($groupedRows as $bidangName => $anggotaList)
                <!-- Subtotal Unit Kerja -->
                <tr class="subtotal-row">
                    <td colspan="3" style="text-indent: 5px;">{{ mb_strtoupper($bidangName) }}</td>
                    <td class="num">{{ number_format($anggotaList->sum('sim2025'), 0, ',', '.') }}</td>
                    <td class="num">{{ number_format($anggotaList->sum('pokok'), 0, ',', '.') }}</td>
                    <td class="num">{{ number_format($anggotaList->sum('wajib'), 0, ',', '.') }}</td>
                    <td class="num">{{ number_format($anggotaList->sum('swp'), 0, ',', '.') }}</td>
                    <td class="num">{{ number_format($anggotaList->sum('bonus_shu'), 0, ',', '.') }}</td>
                    <td class="num" style="font-size: 9px;">{{ number_format($anggotaList->sum('total'), 0, ',', '.') }}</td>
                </tr>

                <!-- Anggota List -->
                @foreach($anggotaList as $idx => $row)
                <tr class="{{ $idx % 2 == 1 ? 'row-even' : '' }}">
                    <td class="center">{{ $idx + 1 }}</td>
                    <td class="center" style="width: 75px; font-family: 'DejaVu Sans Mono', monospace; font-size: 7.5px;">{{ $row['nip'] }}</td>
                    <td class="bold" style="font-size: 9px;">{{ $row['nama'] }}</td>
                    
                    <td class="num">{{ $row['sim2025'] > 0 ? number_format($row['sim2025'], 0, ',', '.') : '-' }}</td>
                    <td class="num">{{ $row['pokok'] > 0 ? number_format($row['pokok'], 0, ',', '.') : '-' }}</td>
                    <td class="num">{{ $row['wajib'] > 0 ? number_format($row['wajib'], 0, ',', '.') : '-' }}</td>
                    <td class="num">{{ $row['swp'] > 0 ? number_format($row['swp'], 0, ',', '.') : '-' }}</td>
                    <td class="num">{{ $row['bonus_shu'] > 0 ? number_format($row['bonus_shu'], 0, ',', '.') : '-' }}</td>
                    
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
