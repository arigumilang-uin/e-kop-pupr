@extends('layouts.app')

@section('title', 'Simulasi Proyeksi Keuangan')
@section('subtitle', 'Proyeksikan kondisi kas koperasi ke depan untuk perencanaan strategis')

@section('actions')
<a href="{{ route('keuangan.laporan') }}" class="py-2.5 px-4 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-slate-900 text-sm font-medium transition-colors">
    ← Laporan Keuangan
</a>
@endsection

@section('content')
<div class="space-y-6">

    {{-- Form Pilih Target Bulan --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <form method="GET" action="{{ route('keuangan.simulasi') }}" class="flex flex-col md:flex-row items-end gap-4">
            <div class="flex-1">
                <label class="block text-sm font-semibold text-slate-700 mb-2">Proyeksikan Kas Koperasi Hingga Bulan:</label>
                <div class="flex gap-3">
                    <select name="bulan_target" class="flex-1 px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-sm">
                        @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $targetBulan == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create(null, $m, 1)->translatedFormat('F') }}
                        </option>
                        @endfor
                    </select>
                    <select name="tahun_target" class="w-28 px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-sm">
                        @for($y = now()->year; $y <= now()->year + 2; $y++)
                        <option value="{{ $y }}" {{ $targetTahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
            </div>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold transition-colors shadow-sm whitespace-nowrap">
                <svg class="w-4 h-4 inline-block mr-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                Jalankan Simulasi
            </button>
        </form>
    </div>

    {{-- Ringkasan Proyeksi --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="bg-gradient-to-br from-slate-700 to-slate-800 rounded-2xl p-5 text-white shadow-lg">
            <p class="text-slate-300 text-xs font-medium mb-1">Kas Saat Ini ({{ now()->translatedFormat('F Y') }})</p>
            <p class="text-xl font-bold font-mono">{{ format_rupiah($summary['kasSekarang']) }}</p>
            <p class="text-slate-400 text-[10px] mt-1">Saldo liquid di rekening koperasi</p>
        </div>
        <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-2xl p-5 text-white shadow-lg shadow-blue-600/20">
            <p class="text-blue-200 text-xs font-medium mb-1">Proyeksi Kas di {{ \Carbon\Carbon::create($targetTahun, $targetBulan, 1)->translatedFormat('F Y') }}</p>
            <p class="text-xl font-bold font-mono">{{ format_rupiah($summary['kasProyeksiAkhir']) }}</p>
            <p class="text-blue-200 text-[10px] mt-1">Estimasi setelah pemasukan {{ count($proyeksi) }} bulan</p>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
            <p class="text-slate-500 text-xs font-medium mb-1">Total Proyeksi Pemasukan</p>
            <p class="text-xl font-bold font-mono text-emerald-600">+{{ format_rupiah($summary['totalPemasukan']) }}</p>
            <p class="text-slate-400 text-[10px] mt-1">Simpanan: {{ format_rupiah($summary['totalPendapatanWajib']) }} + Angsuran: {{ format_rupiah($summary['totalPendapatanAngsuran']) }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
            <p class="text-slate-500 text-xs font-medium mb-1">Kapasitas Pinjaman Baru</p>
            <p class="text-xl font-bold text-slate-800">~{{ $summary['kapasitasPinjaman'] }} <span class="text-sm font-normal text-slate-400">pinjaman</span></p>
            <p class="text-slate-400 text-[10px] mt-1">Berdasarkan rata-rata {{ format_rupiah($summary['avgPinjaman']) }}/pinjaman</p>
        </div>
    </div>

    {{-- Info Penting --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <div class="bg-violet-50 border border-violet-200 rounded-2xl p-5 flex items-start gap-3">
            <div class="w-9 h-9 rounded-xl bg-violet-100 flex items-center justify-center shrink-0 mt-0.5">
                <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div>
                <p class="text-sm font-bold text-violet-800">{{ $summary['jumlahAnggotaAktif'] }} Anggota Aktif</p>
                <p class="text-xs text-violet-600 mt-0.5">Masing-masing menyetor Rp {{ number_format($summary['nominalWajib'], 0, ',', '.') }} simpanan wajib/bulan</p>
            </div>
        </div>
        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5 flex items-start gap-3">
            <div class="w-9 h-9 rounded-xl bg-amber-100 flex items-center justify-center shrink-0 mt-0.5">
                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </div>
            <div>
                <p class="text-sm font-bold text-amber-800">{{ $pinjamanBerjalan->count() }} Pinjaman Berjalan</p>
                <p class="text-xs text-amber-600 mt-0.5">{{ $summary['pinjamanAkanLunas'] }} diantaranya diproyeksikan lunas sebelum {{ \Carbon\Carbon::create($targetTahun, $targetBulan, 1)->translatedFormat('F Y') }}</p>
            </div>
        </div>
        <div class="bg-blue-50 border border-blue-200 rounded-2xl p-5 flex items-start gap-3">
            <div class="w-9 h-9 rounded-xl bg-blue-100 flex items-center justify-center shrink-0 mt-0.5">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            </div>
            <div>
                <p class="text-sm font-bold text-blue-800">Pertumbuhan Kas</p>
                @php $growth = $summary['kasSekarang'] > 0 ? round(($summary['totalPemasukan'] / $summary['kasSekarang']) * 100, 1) : 0; @endphp
                <p class="text-xs text-blue-600 mt-0.5">+{{ $growth }}% dalam {{ count($proyeksi) }} bulan ke depan</p>
            </div>
        </div>
    </div>

    {{-- Tabel Proyeksi Bulan per Bulan --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-200">
            <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider flex items-center gap-2">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                Proyeksi Arus Kas Bulanan
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50/80 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Bulan</th>
                        <th class="px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider text-right">Simpanan Wajib</th>
                        <th class="px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider text-right">Angsuran Masuk</th>
                        <th class="px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider text-right">Total Pemasukan</th>
                        <th class="px-5 py-3 text-xs font-bold text-blue-700 uppercase tracking-wider text-right">Proyeksi Kas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    {{-- Baris Titik Awal --}}
                    <tr class="bg-slate-50/50">
                        <td class="px-5 py-3 font-medium text-slate-600">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                                {{ now()->translatedFormat('F Y') }} <span class="text-[10px] text-slate-400 ml-1">(sekarang)</span>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-right text-slate-400 font-mono">-</td>
                        <td class="px-5 py-3 text-right text-slate-400 font-mono">-</td>
                        <td class="px-5 py-3 text-right text-slate-400 font-mono">-</td>
                        <td class="px-5 py-3 text-right font-mono font-bold text-slate-700">{{ format_rupiah($summary['kasSekarang']) }}</td>
                    </tr>

                    @foreach($proyeksi as $i => $p)
                    <tr class="hover:bg-blue-50/30 transition-colors {{ $i === count($proyeksi) - 1 ? 'bg-blue-50/50' : '' }}">
                        <td class="px-5 py-3 font-medium text-slate-700">
                            <div class="flex items-center gap-2">
                                @if($i === count($proyeksi) - 1)
                                <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                                @else
                                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                                @endif
                                {{ $p['bulan'] }}
                                @if($i === count($proyeksi) - 1)
                                <span class="text-[10px] text-blue-600 ml-1 font-semibold">(TARGET)</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-5 py-3 text-right font-mono text-emerald-600">+{{ format_rupiah($p['pendapatan_wajib']) }}</td>
                        <td class="px-5 py-3 text-right">
                            @if($p['pendapatan_angsuran'] > 0)
                            <span class="font-mono text-emerald-600">+{{ format_rupiah($p['pendapatan_angsuran']) }}</span>
                            <button type="button" onclick="this.nextElementSibling.classList.toggle('hidden')" class="ml-1 text-blue-400 hover:text-blue-600 transition-colors">
                                <svg class="w-3.5 h-3.5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </button>
                            <div class="hidden mt-2 p-2 bg-white border border-slate-200 rounded-lg shadow-lg text-left text-xs max-w-[240px] absolute z-10">
                                <p class="font-semibold text-slate-700 mb-1">Rincian Angsuran:</p>
                                @foreach($p['detail_angsuran'] as $da)
                                <div class="flex justify-between py-0.5 text-slate-600">
                                    <span>{{ $da['anggota'] }} #{{ $da['angsuran_ke'] }}</span>
                                    <span class="font-mono">{{ format_rupiah($da['nominal']) }}</span>
                                </div>
                                @endforeach
                            </div>
                            @else
                            <span class="font-mono text-slate-300">Rp 0</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right font-mono text-emerald-700 font-semibold">+{{ format_rupiah($p['total_masuk']) }}</td>
                        <td class="px-5 py-3 text-right font-mono font-bold {{ $i === count($proyeksi) - 1 ? 'text-blue-700 text-base' : 'text-slate-700' }}">
                            {{ format_rupiah($p['kumulatif_kas']) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-slate-50 border-t-2 border-slate-200">
                    <tr>
                        <td class="px-5 py-3 text-xs font-bold text-slate-700 uppercase">Total Proyeksi {{ count($proyeksi) }} Bulan</td>
                        <td class="px-5 py-3 text-right font-mono font-bold text-emerald-700">+{{ format_rupiah($summary['totalPendapatanWajib']) }}</td>
                        <td class="px-5 py-3 text-right font-mono font-bold text-emerald-700">+{{ format_rupiah($summary['totalPendapatanAngsuran']) }}</td>
                        <td class="px-5 py-3 text-right font-mono font-bold text-emerald-700">+{{ format_rupiah($summary['totalPemasukan']) }}</td>
                        <td class="px-5 py-3 text-right font-mono text-lg font-bold text-blue-700">{{ format_rupiah($summary['kasProyeksiAkhir']) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Visual Progress Bar --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <h3 class="text-sm font-bold text-slate-700 mb-4">Visualisasi Pertumbuhan Kas</h3>
        @php
            $maxKas = collect($proyeksi)->max('kumulatif_kas');
            $maxKas = max($maxKas, $summary['kasSekarang'], 1);
        @endphp
        <div class="space-y-3">
            {{-- Current --}}
            <div class="flex items-center gap-3">
                <span class="text-xs text-slate-500 w-28 shrink-0 text-right truncate">Sekarang</span>
                <div class="flex-1 bg-slate-100 rounded-full h-6 relative overflow-hidden">
                    @php $pct = round(($summary['kasSekarang'] / $maxKas) * 100, 1); @endphp
                    <div class="bg-slate-500 h-6 rounded-full transition-all flex items-center justify-end pr-2" style="width: {{ max($pct, 5) }}%">
                        <span class="text-[10px] font-mono font-bold text-white whitespace-nowrap">{{ format_rupiah($summary['kasSekarang']) }}</span>
                    </div>
                </div>
            </div>

            @foreach($proyeksi as $i => $p)
            <div class="flex items-center gap-3">
                <span class="text-xs text-slate-500 w-28 shrink-0 text-right truncate">{{ $p['bulan_raw']->translatedFormat('M Y') }}</span>
                <div class="flex-1 bg-slate-100 rounded-full h-6 relative overflow-hidden">
                    @php $pct = round(($p['kumulatif_kas'] / $maxKas) * 100, 1); @endphp
                    <div class="{{ $i === count($proyeksi) - 1 ? 'bg-blue-600' : 'bg-emerald-500' }} h-6 rounded-full transition-all flex items-center justify-end pr-2" style="width: {{ max($pct, 8) }}%">
                        <span class="text-[10px] font-mono font-bold text-white whitespace-nowrap">{{ format_rupiah($p['kumulatif_kas']) }}</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Rekomendasi --}}
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl p-6 text-white shadow-xl shadow-blue-600/20">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div>
                <h3 class="text-lg font-bold mb-1 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                    Rekomendasi Sistem
                </h3>
                <p class="text-blue-100 text-sm max-w-xl">
                    Berdasarkan proyeksi kas hingga <strong>{{ \Carbon\Carbon::create($targetTahun, $targetBulan, 1)->translatedFormat('F Y') }}</strong>,
                    koperasi diperkirakan memiliki kas sebesar <strong>{{ format_rupiah($summary['kasProyeksiAkhir']) }}</strong>.
                    Dengan rata-rata pinjaman {{ format_rupiah($summary['avgPinjaman']) }},
                    koperasi dapat memfasilitasi sekitar <strong>{{ $summary['kapasitasPinjaman'] }} pengajuan pinjaman baru</strong>.
                    @if($summary['pinjamanAkanLunas'] > 0)
                    Sebanyak <strong>{{ $summary['pinjamanAkanLunas'] }} pinjaman</strong> diproyeksikan lunas dalam periode ini.
                    @endif
                </p>
            </div>
            <div class="text-center shrink-0">
                <p class="text-blue-200 text-xs mb-1">Limit Pinjaman Aman</p>
                <p class="text-3xl font-bold font-mono">{{ format_rupiah($summary['kasProyeksiAkhir'] * 0.7) }}</p>
                <p class="text-blue-300 text-[10px]">(70% dari proyeksi kas)</p>
            </div>
        </div>
    </div>

    {{-- Disclaimer --}}
    <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-700 flex items-start gap-2">
        <svg class="w-4 h-4 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        <div>
            <strong>Disclaimer:</strong> Simulasi ini bersifat <em>estimasi</em> berdasarkan data saat ini ({{ now()->translatedFormat('d F Y') }}).
            Tidak memperhitungkan: pencairan pinjaman baru, penarikan simpanan, penambahan anggota baru, atau anggota yang keluar.
            Gunakan sebagai alat bantu perencanaan, bukan keputusan final.
        </div>
    </div>
</div>
@endsection
