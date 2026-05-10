<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pinjaman Aktif</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 8px; /* VERY SMALL due to 18 columns */
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
        }
        .header h2, .header h3 {
            margin: 2px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #000;
            padding: 3px;
        }
        th {
            background-color: #e0e0e0;
            text-align: center;
            vertical-align: middle;
            font-weight: bold;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .bg-gray { background-color: #f2f2f2; }
        .bg-dark { background-color: #000000; color: #ffffff; }
        .bg-yellow { background-color: #f0f0f0; }
    </style>
</head>
<body>
    <div class="header">
        <h2>DAFTAR PINJAMAN AKTIF ANGGOTA KOPERASI KONSUMEN TIRTA BINA KARYA</h2>
        <h3>DINAS PUPRPKPP PROVINSI RIAU</h3>
        <p>Periode: {{ $filterInfo }} | Dicetak: {{ now()->translatedFormat('d F Y, H:i') }} WIB</p>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2">No</th>
                <th colspan="2" rowspan="2">NIP/Nama/Unit Kerja</th>
                <th colspan="3">Pinjaman</th>
                <th colspan="4">Potongan Awal</th>
                <th rowspan="2">Dana Dicairkan</th>
                <th colspan="3">Angsuran perbulan</th>
                <th rowspan="2">Tenor</th>
                <th colspan="2">Progres</th>
                <th rowspan="2">Total Dibayar</th>
                <th rowspan="2">Sisa Hutang</th>
            </tr>
            <tr>
                <th>Pokok</th>
                <th>Total Bunga</th>
                <th>Total</th>
                
                <th>SWP</th>
                <th>Dana Resiko</th>
                <th>Biaya Admin</th>
                <th>Total Potongan</th>
                
                <th>Pokok</th>
                <th>Bunga</th>
                <th>Total</th>
                
                <th>ke-</th>
                <th>dari</th>
            </tr>
        </thead>
        <tbody>
            <tr class="bg-dark font-bold text-right">
                <td colspan="3" class="text-center">TOTAL SELURUHNYA</td>
                <td>{{ number_format($grandTotals['pokok'], 0, ',', '.') }}</td>
                <td>{{ number_format($grandTotals['total_bunga'], 0, ',', '.') }}</td>
                <td>{{ number_format($grandTotals['total'], 0, ',', '.') }}</td>
                <td>{{ number_format($grandTotals['swp'], 0, ',', '.') }}</td>
                <td>{{ number_format($grandTotals['dana_resiko'], 0, ',', '.') }}</td>
                <td>{{ number_format($grandTotals['biaya_admin'], 0, ',', '.') }}</td>
                <td>{{ number_format($grandTotals['total_potongan'], 0, ',', '.') }}</td>
                <td>{{ number_format($grandTotals['dana_dicairkan'], 0, ',', '.') }}</td>
                <td>{{ number_format($grandTotals['angsuran_pokok'], 0, ',', '.') }}</td>
                <td>{{ number_format($grandTotals['angsuran_bunga'], 0, ',', '.') }}</td>
                <td>{{ number_format($grandTotals['angsuran_total'], 0, ',', '.') }}</td>
                <td colspan="3"></td>
                <td>{{ number_format($grandTotals['total_dibayar'], 0, ',', '.') }}</td>
                <td>{{ number_format($grandTotals['sisa_hutang'], 0, ',', '.') }}</td>
            </tr>

            @php
                $groupedRows = collect($rows)->groupBy('bidang');
            @endphp

            @foreach ($groupedRows as $bidangName => $anggotaList)
                <tr class="bg-yellow font-bold text-right">
                    <td colspan="3" class="text-center">{{ mb_strtoupper($bidangName) }}</td>
                    <td>{{ number_format($anggotaList->sum('pokok'), 0, ',', '.') }}</td>
                    <td>{{ number_format($anggotaList->sum('total_bunga'), 0, ',', '.') }}</td>
                    <td>{{ number_format($anggotaList->sum('total'), 0, ',', '.') }}</td>
                    <td>{{ number_format($anggotaList->sum('swp'), 0, ',', '.') }}</td>
                    <td>{{ number_format($anggotaList->sum('dana_resiko'), 0, ',', '.') }}</td>
                    <td>{{ number_format($anggotaList->sum('biaya_admin'), 0, ',', '.') }}</td>
                    <td>{{ number_format($anggotaList->sum('total_potongan'), 0, ',', '.') }}</td>
                    <td>{{ number_format($anggotaList->sum('dana_dicairkan'), 0, ',', '.') }}</td>
                    <td>{{ number_format($anggotaList->sum('angsuran_pokok'), 0, ',', '.') }}</td>
                    <td>{{ number_format($anggotaList->sum('angsuran_bunga'), 0, ',', '.') }}</td>
                    <td>{{ number_format($anggotaList->sum('angsuran_total'), 0, ',', '.') }}</td>
                    <td colspan="3"></td>
                    <td>{{ number_format($anggotaList->sum('total_dibayar'), 0, ',', '.') }}</td>
                    <td>{{ number_format($anggotaList->sum('sisa_hutang'), 0, ',', '.') }}</td>
                </tr>

                @php $no = 1; @endphp
                @foreach ($anggotaList as $item)
                    <tr class="text-right">
                        <td class="text-center">{{ $no++ }}</td>
                        <td class="text-center" style="white-space: nowrap;">{{ $item['nip'] }}</td>
                        <td class="text-center" style="white-space: nowrap; text-align: left;">{{ $item['nama'] }}</td>
                        <td>{{ number_format($item['pokok'], 0, ',', '.') }}</td>
                        <td>{{ number_format($item['total_bunga'], 0, ',', '.') }}</td>
                        <td>{{ number_format($item['total'], 0, ',', '.') }}</td>
                        <td>{{ number_format($item['swp'], 0, ',', '.') }}</td>
                        <td>{{ number_format($item['dana_resiko'], 0, ',', '.') }}</td>
                        <td>{{ number_format($item['biaya_admin'], 0, ',', '.') }}</td>
                        <td>{{ number_format($item['total_potongan'], 0, ',', '.') }}</td>
                        <td>{{ number_format($item['dana_dicairkan'], 0, ',', '.') }}</td>
                        <td>{{ number_format($item['angsuran_pokok'], 0, ',', '.') }}</td>
                        <td>{{ number_format($item['angsuran_bunga'], 0, ',', '.') }}</td>
                        <td>{{ number_format($item['angsuran_total'], 0, ',', '.') }}</td>
                        <td class="text-center">{{ $item['tenor_bulan'] }}</td>
                        <td class="text-center">{{ $item['progres_ke'] }}</td>
                        <td class="text-center">{{ $item['progres_dari'] }}</td>
                        <td>{{ number_format($item['total_dibayar'], 0, ',', '.') }}</td>
                        <td>{{ number_format($item['sisa_hutang'], 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</body>
</html>
