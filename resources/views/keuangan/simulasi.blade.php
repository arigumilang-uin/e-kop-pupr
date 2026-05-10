@extends('layouts.app')

@section('title', 'Proyeksi Aliran Dana')
@section('subtitle', 'Simulasi kondisi kas koperasi di masa depan untuk perencanaan strategis')

@section('actions')
<a href="{{ route('keuangan.laporan') }}" class="inline-flex items-center gap-2 py-2.5 px-4 rounded-xl bg-white border border-stone-200 text-stone-600 hover:bg-stone-50 hover:text-stone-900 text-sm font-bold transition-all shadow-sm">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
    Kembali ke Laporan
</a>
@endsection

@section('content')
<div class="space-y-6">

    {{-- Form Pilih Target Bulan --}}
    <div class="bg-white rounded-2xl border border-stone-200 shadow-sm p-8">
        <form method="GET" action="{{ route('keuangan.simulasi') }}" class="flex flex-col md:flex-row items-end gap-5">
            <div class="flex-1">
                <label class="block text-sm font-black text-stone-700 uppercase tracking-widest mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Target Waktu Proyeksi
                </label>
                <div class="flex gap-3">
                    <select name="bulan_target" class="flex-1 px-4 py-3 rounded-xl border border-stone-200 focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] text-sm font-medium bg-stone-50 hover:bg-stone-100 transition-colors cursor-pointer">
                        @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $targetBulan == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create(null, $m, 1)->translatedFormat('F') }}
                        </option>
                        @endfor
                    </select>
                    <select name="tahun_target" class="w-32 px-4 py-3 rounded-xl border border-stone-200 focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] text-sm font-medium bg-stone-50 hover:bg-stone-100 transition-colors cursor-pointer">
                        @for($y = now()->year; $y <= now()->year + 2; $y++)
                        <option value="{{ $y }}" {{ $targetTahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
            </div>
            <button type="submit" class="px-8 py-3 rounded-xl bg-[#043d2e] hover:bg-[#022a20] text-white text-sm font-bold transition-all shadow-md hover:shadow-lg whitespace-nowrap flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                Jalankan Simulasi
            </button>
        </form>
    </div>

    {{-- Ringkasan Proyeksi --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-stone-800 rounded-2xl p-6 text-white shadow-lg relative overflow-hidden">
            <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>
            <div class="relative z-10">
                <p class="text-stone-400 text-[10px] font-bold uppercase tracking-widest mb-1">Kas Saat Ini</p>
                <p class="text-2xl font-black font-mono mt-1 mb-1">{{ format_rupiah($summary['kasSekarang']) }}</p>
                <p class="text-stone-400 text-[11px]">Bulan {{ now()->translatedFormat('F Y') }}</p>
            </div>
        </div>
        <div class="bg-[#043d2e] rounded-2xl p-6 text-white shadow-lg relative overflow-hidden">
            <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>
            <div class="absolute top-0 right-0 p-4 opacity-10">
                <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
            </div>
            <div class="relative z-10">
                <p class="text-emerald-200/80 text-[10px] font-bold uppercase tracking-widest mb-1">Target Proyeksi Kas</p>
                <p class="text-2xl font-black font-mono mt-1 mb-1">{{ format_rupiah($summary['kasProyeksiAkhir']) }}</p>
                <p class="text-emerald-200/60 text-[11px]">Bulan {{ \Carbon\Carbon::create($targetTahun, $targetBulan, 1)->translatedFormat('F Y') }}</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-6 border border-stone-200 shadow-sm relative overflow-hidden">
            <div class="absolute inset-x-0 bottom-0 h-1 bg-emerald-500"></div>
            <div class="relative z-10">
                <p class="text-stone-500 text-[10px] font-bold uppercase tracking-widest mb-1">Total Estimasi Masuk</p>
                <p class="text-2xl font-black font-mono text-emerald-600 mt-1 mb-1">+{{ format_rupiah($summary['totalPemasukan']) }}</p>
                <p class="text-stone-400 text-[11px] truncate">Simpanan: {{ format_rupiah($summary['totalPendapatanWajib']) }}</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-6 border border-stone-200 shadow-sm relative overflow-hidden">
            <div class="absolute top-0 right-0 p-4 opacity-5">
                <svg class="w-16 h-16 text-stone-800" fill="currentColor" viewBox="0 0 24 24"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
            </div>
            <div class="relative z-10">
                <p class="text-stone-500 text-[10px] font-bold uppercase tracking-widest mb-1">Kapasitas Peminjaman</p>
                <p class="text-2xl font-black font-mono text-stone-800 mt-1 mb-1">~{{ $summary['kapasitasPinjaman'] }} <span class="text-sm font-normal text-stone-400">Pengajuan</span></p>
                <p class="text-stone-400 text-[11px]">Rata-rata {{ format_rupiah($summary['avgPinjaman']) }}/org</p>
            </div>
        </div>
    </div>

    {{-- Info Penting --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-stone-50 border border-stone-200 rounded-2xl p-6 flex items-start gap-4 transition-all hover:border-stone-300">
            <div class="w-10 h-10 rounded-xl bg-white border border-stone-200 flex items-center justify-center shadow-sm shrink-0">
                <svg class="w-5 h-5 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div>
                <p class="text-sm font-black text-stone-800 mb-1">{{ $summary['jumlahAnggotaAktif'] }} Anggota Aktif</p>
                <p class="text-[11px] text-stone-500 leading-relaxed">Masing-masing menyetor rata-rata global Rp {{ number_format($summary['nominalWajibGlobal'], 0, ',', '.') }} simpanan wajib/bulan sesuai jadwal.</p>
            </div>
        </div>
        <div class="bg-stone-50 border border-stone-200 rounded-2xl p-6 flex items-start gap-4 transition-all hover:border-stone-300">
            <div class="w-10 h-10 rounded-xl bg-white border border-stone-200 flex items-center justify-center shadow-sm shrink-0">
                <svg class="w-5 h-5 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div>
                <p class="text-sm font-black text-stone-800 mb-1">{{ $pinjamanBerjalan->count() }} Pinjaman Beredar</p>
                <p class="text-[11px] text-stone-500 leading-relaxed">{{ $summary['pinjamanAkanLunas'] }} diantaranya diproyeksikan akan lunas sebelum bulan {{ \Carbon\Carbon::create($targetTahun, $targetBulan, 1)->translatedFormat('F Y') }}.</p>
            </div>
        </div>
        <div class="bg-stone-50 border border-stone-200 rounded-2xl p-6 flex items-start gap-4 transition-all hover:border-stone-300">
            <div class="w-10 h-10 rounded-xl bg-white border border-stone-200 flex items-center justify-center shadow-sm shrink-0">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            </div>
            <div>
                <p class="text-sm font-black text-stone-800 mb-1">Pertumbuhan Kas</p>
                @php $growth = $summary['kasSekarang'] > 0 ? round(($summary['totalPemasukan'] / $summary['kasSekarang']) * 100, 1) : 0; @endphp
                <p class="text-[11px] text-stone-500 leading-relaxed">Kas diproyeksikan tumbuh sebesar <span class="font-bold text-emerald-600">+{{ $growth }}%</span> dalam {{ count($proyeksi) }} bulan berjalan.</p>
            </div>
        </div>
    </div>

    {{-- Tabel Proyeksi Bulan per Bulan --}}
    <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden">
        <div class="px-8 py-5 bg-stone-50 border-b border-stone-200">
            <h3 class="text-sm font-black text-stone-800 uppercase tracking-widest flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-white border border-stone-200 flex items-center justify-center">
                    <svg class="w-4 h-4 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                Rincian Arus Kas Bulanan
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-white border-b border-stone-100">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-stone-500 uppercase tracking-widest">Bulan Proyeksi</th>
                        <th class="px-6 py-4 text-xs font-bold text-stone-500 uppercase tracking-widest text-right">Simpanan Wajib</th>
                        <th class="px-6 py-4 text-xs font-bold text-stone-500 uppercase tracking-widest text-right">Angsuran Masuk</th>
                        <th class="px-6 py-4 text-xs font-bold text-stone-500 uppercase tracking-widest text-right">Total Pemasukan</th>
                        <th class="px-6 py-4 text-xs font-black text-stone-800 uppercase tracking-widest text-right">Proyeksi Kas Liquid</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    {{-- Baris Titik Awal --}}
                    <tr class="bg-stone-50/50">
                        <td class="px-6 py-4 font-bold text-stone-600">
                            <div class="flex items-center gap-3">
                                <span class="w-2.5 h-2.5 rounded-full bg-stone-300"></span>
                                {{ now()->translatedFormat('F Y') }} <span class="text-[10px] font-normal text-stone-400 uppercase tracking-wider ml-1">(Bulan Berjalan)</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right text-stone-400 font-mono">-</td>
                        <td class="px-6 py-4 text-right text-stone-400 font-mono">-</td>
                        <td class="px-6 py-4 text-right text-stone-400 font-mono">-</td>
                        <td class="px-6 py-4 text-right font-mono font-black text-stone-800 text-base">{{ format_rupiah($summary['kasSekarang']) }}</td>
                    </tr>

                    @foreach($proyeksi as $i => $p)
                    <tr class="hover:bg-stone-50 transition-colors {{ $i === count($proyeksi) - 1 ? 'bg-[#043d2e]/5' : '' }}">
                        <td class="px-6 py-4 font-bold text-stone-700">
                            <div class="flex items-center gap-3">
                                @if($i === count($proyeksi) - 1)
                                <span class="w-2.5 h-2.5 rounded-full bg-[#043d2e] animate-pulse"></span>
                                @else
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
                                @endif
                                {{ $p['bulan'] }}
                                @if($i === count($proyeksi) - 1)
                                <span class="text-[10px] text-[#043d2e] ml-2 font-black uppercase tracking-widest">(Target)</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right font-mono text-emerald-600">+{{ format_rupiah($p['pendapatan_wajib']) }}</td>
                        <td class="px-6 py-4 text-right">
                            @if($p['pendapatan_angsuran'] > 0)
                            <span class="font-mono text-emerald-600">+{{ format_rupiah($p['pendapatan_angsuran']) }}</span>
                            <button type="button" onclick="this.nextElementSibling.classList.toggle('hidden')" class="ml-2 text-stone-400 hover:text-stone-800 transition-colors focus:outline-none">
                                <svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </button>
                            <div class="hidden mt-3 p-4 bg-white border border-stone-200 rounded-xl shadow-xl text-left text-xs w-[280px] absolute z-10 -ml-32">
                                <p class="font-black text-stone-800 uppercase tracking-widest mb-3 border-b border-stone-100 pb-2">Rincian Angsuran Masuk</p>
                                <div class="max-h-48 overflow-y-auto pr-2 space-y-1.5 custom-scrollbar">
                                    @foreach($p['detail_angsuran'] as $da)
                                    <div class="flex justify-between items-center text-stone-600">
                                        <span class="truncate pr-3">{{ $da['anggota'] }} <span class="text-[10px] text-stone-400">#{{ $da['angsuran_ke'] }}</span></span>
                                        <span class="font-mono font-bold shrink-0">{{ format_rupiah($da['nominal']) }}</span>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @else
                            <span class="font-mono text-stone-300">Rp 0</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right font-mono text-emerald-700 font-bold">+{{ format_rupiah($p['total_masuk']) }}</td>
                        <td class="px-6 py-4 text-right font-mono font-black {{ $i === count($proyeksi) - 1 ? 'text-[#043d2e] text-lg' : 'text-stone-800 text-base' }}">
                            {{ format_rupiah($p['kumulatif_kas']) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-stone-100 border-t-2 border-stone-200">
                    <tr>
                        <td class="px-6 py-4 text-xs font-black text-stone-800 uppercase tracking-widest">Total Akumulasi</td>
                        <td class="px-6 py-4 text-right font-mono font-black text-emerald-700">+{{ format_rupiah($summary['totalPendapatanWajib']) }}</td>
                        <td class="px-6 py-4 text-right font-mono font-black text-emerald-700">+{{ format_rupiah($summary['totalPendapatanAngsuran']) }}</td>
                        <td class="px-6 py-4 text-right font-mono font-black text-emerald-700">+{{ format_rupiah($summary['totalPemasukan']) }}</td>
                        <td class="px-6 py-4 text-right font-mono text-xl font-black text-[#043d2e]">{{ format_rupiah($summary['kasProyeksiAkhir']) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f5f5f4; border-radius: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #d6d3d1; border-radius: 4px; }
    </style>

    {{-- Visual Progress Bar --}}
    <div class="bg-white rounded-2xl border border-stone-200 shadow-sm p-8">
        <h3 class="text-sm font-black text-stone-800 uppercase tracking-widest mb-6 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-emerald-50 border border-emerald-100 flex items-center justify-center">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            </div>
            Visualisasi Pertumbuhan
        </h3>
        @php
            $maxKas = collect($proyeksi)->max('kumulatif_kas');
            $maxKas = max($maxKas, $summary['kasSekarang'], 1);
        @endphp
        <div class="space-y-4">
            {{-- Current --}}
            <div class="flex items-center gap-4">
                <span class="text-xs font-bold text-stone-500 w-28 shrink-0 text-right uppercase tracking-wider">Sekarang</span>
                <div class="flex-1 bg-stone-100 rounded-full h-8 relative overflow-hidden border border-stone-200/50">
                    @php $pct = round(($summary['kasSekarang'] / $maxKas) * 100, 1); @endphp
                    <div class="bg-stone-500 h-8 rounded-full transition-all flex items-center justify-end pr-4 shadow-sm" style="width: {{ max($pct, 8) }}%">
                        <span class="text-[11px] font-mono font-bold text-white whitespace-nowrap">{{ format_rupiah($summary['kasSekarang']) }}</span>
                    </div>
                </div>
            </div>

            @foreach($proyeksi as $i => $p)
            <div class="flex items-center gap-4">
                <span class="text-xs font-bold text-stone-600 w-28 shrink-0 text-right uppercase tracking-wider">{{ $p['bulan_raw']->translatedFormat('M Y') }}</span>
                <div class="flex-1 bg-stone-100 rounded-full h-8 relative overflow-hidden border border-stone-200/50">
                    @php $pct = round(($p['kumulatif_kas'] / $maxKas) * 100, 1); @endphp
                    <div class="{{ $i === count($proyeksi) - 1 ? 'bg-[#043d2e]' : 'bg-emerald-500' }} h-8 rounded-full transition-all flex items-center justify-end pr-4 shadow-sm" style="width: {{ max($pct, 8) }}%">
                        <span class="text-[11px] font-mono font-bold text-white whitespace-nowrap">{{ format_rupiah($p['kumulatif_kas']) }}</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Rekomendasi --}}
    <div class="bg-[#043d2e] rounded-2xl p-8 text-white shadow-xl relative overflow-hidden">
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>
        <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
            <div>
                <h3 class="text-xl font-black mb-3 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/20 border border-emerald-500/30 flex items-center justify-center">
                        <svg class="w-5 h-5 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                    </div>
                    Rekomendasi Sistem
                </h3>
                <p class="text-emerald-100/80 text-sm max-w-2xl leading-relaxed">
                    Hingga <strong>{{ \Carbon\Carbon::create($targetTahun, $targetBulan, 1)->translatedFormat('F Y') }}</strong>, 
                    kas Koperasi ditargetkan mencapai <strong class="text-white">{{ format_rupiah($summary['kasProyeksiAkhir']) }}</strong>. 
                    Dengan nilai rata-rata pinjaman berjalan sebesar {{ format_rupiah($summary['avgPinjaman']) }}, 
                    Koperasi dapat menyalurkan sekitar <strong class="text-white">{{ $summary['kapasitasPinjaman'] }} pengajuan pinjaman baru</strong> 
                    kepada anggota.
                </p>
            </div>
            <div class="text-center shrink-0 w-full md:w-auto bg-black/20 p-5 rounded-2xl border border-white/10 backdrop-blur-sm">
                <p class="text-emerald-200/80 text-[10px] font-bold uppercase tracking-widest mb-1">Limit Pinjaman Aman (70%)</p>
                <p class="text-3xl font-black font-mono text-emerald-400 drop-shadow-md">{{ format_rupiah($summary['kasProyeksiAkhir'] * 0.7) }}</p>
            </div>
        </div>
    </div>

    {{-- Disclaimer --}}
    <div class="p-5 bg-amber-50 border border-amber-200 rounded-2xl text-xs text-amber-800 flex items-start gap-3">
        <div class="w-8 h-8 rounded-full bg-amber-100 flex items-center justify-center shrink-0">
            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <div class="pt-1.5 leading-relaxed">
            <strong class="uppercase tracking-wide font-black">Disclaimer:</strong> Simulasi ini murni kalkulasi linier berdasarkan data pada ({{ now()->translatedFormat('d F Y') }}). Tidak mengikutsertakan fluktuasi realita seperti: penambahan/pencabutan anggota, denda telat bayar, pencairan baru dalam rentang waktu, dan pengeluaran operasional. Gunakan sebagai alat perencanaan prediktif, bukan laporan absolut.
        </div>
    </div>
</div>
@endsection
