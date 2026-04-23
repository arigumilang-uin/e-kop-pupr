@extends('layouts.app')

@section('title', 'Potongan Bulanan TPP')
@section('subtitle', 'Rekapitulasi beban potongan per anggota sesuai parameter bulan.')

@section('actions')
<div class="flex items-center gap-3">
    @if($totalKeseluruhan > 0)
    <form action="{{ route('potongan.proses') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membukukan seluruh potongan TPP periode {{ $months[$month] }} {{ $year }} ini ke dalam buku Kas Koperasi? Tindakan ini tidak dapat dibatalkan.')">
        @csrf
        <input type="hidden" name="month" value="{{ $month }}">
        <input type="hidden" name="year" value="{{ $year }}">
        <button type="submit" class="py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold transition-all flex items-center gap-2 shadow-sm shadow-emerald-600/20">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Simpan & Bukukan Potongan TPP
        </button>
    </form>
    @else
    <div class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 text-slate-500 rounded-xl text-xs font-bold border border-slate-200">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        TEREKSEKUSI / TIDAK ADA TAGIHAN
    </div>
    @endif

    <button onclick="window.print()" class="py-2.5 px-4 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-sm font-medium transition-colors flex items-center gap-2 shadow-sm">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
        </svg>
        Cetak
    </button>
</div>
@endsection

@section('content')
<div class="space-y-6">

    {{-- Filter Bar --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden p-5">
        <form action="{{ route('potongan.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4 items-end">
            <div class="w-full sm:w-48">
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Bulan</label>
                <select name="month" class="w-full px-4 py-2 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
                    @foreach($months as $num => $name)
                        <option value="{{ $num }}" {{ $month == $num ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-full sm:w-32">
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Tahun</label>
                <select name="year" class="w-full px-4 py-2 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
                    @foreach($years as $yr)
                        <option value="{{ $yr }}" {{ $year == $yr ? 'selected' : '' }}>{{ $yr }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-full sm:w-auto">
                <button type="submit" class="w-full py-2 px-5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-semibold transition-colors">
                    Tampilkan Data
                </button>
            </div>
            
            {{-- Summary Card in Filter --}}
            <div class="ml-auto flex items-center gap-3 bg-blue-50/50 border border-blue-100 rounded-xl px-5 py-2.5">
                <div class="text-xs text-blue-600/70 font-semibold uppercase tracking-wider">Total Est. Potongan<br>Bulan Ini</div>
                <div class="font-mono text-xl font-bold text-blue-700">{{ format_rupiah($totalKeseluruhan) }}</div>
            </div>
        </form>
    </div>

    {{-- Data Table --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden text-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-4 font-semibold text-slate-600">NIP & Nama Pegawai</th>
                        <th class="px-5 py-4 font-semibold text-slate-600 text-right">Pot. Pokok (Unpaid)</th>
                        <th class="px-5 py-4 font-semibold text-slate-600 text-right">Pot. Wajib (Bln)</th>
                        <th class="px-5 py-4 font-semibold text-slate-600 text-right">Angsuran Pinjaman</th>
                        <th class="px-5 py-4 font-bold text-slate-800 text-right bg-slate-100/50">Total Potongan TPP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($dataPotongan as $item)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-5 py-3">
                            <div class="font-bold text-slate-800">{{ $item->anggota->nama }}</div>
                            <div class="text-[11px] font-mono text-slate-500 mt-0.5">NIP: {{ $item->anggota->nip }}</div>
                            @if($item->anggota->bidang)
                            <div class="text-[10px] text-slate-400 mt-1 uppercase">{{ $item->anggota->bidang->nama_bidang }}</div>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right">
                            @if($item->potongan_pokok > 0)
                                <span class="font-mono text-amber-600 font-semibold">{{ format_rupiah($item->potongan_pokok) }}</span>
                            @else
                                <span class="font-mono text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right">
                            <span class="font-mono text-slate-700">{{ format_rupiah($item->potongan_wajib) }}</span>
                        </td>
                        <td class="px-5 py-3 text-right">
                            @if($item->potongan_pinjaman > 0)
                                <span class="font-mono text-red-500 font-semibold">{{ format_rupiah($item->potongan_pinjaman) }}</span>
                            @else
                                <span class="font-mono text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right bg-slate-50/30">
                            <span class="font-mono font-bold text-lg {{ $item->total_potongan > 0 ? 'text-slate-800' : 'text-slate-400' }}">
                                {{ format_rupiah($item->total_potongan) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-8 text-center text-slate-500">Belum ada data anggota aktif untuk diproses.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
@media print {
    body * { visibility: hidden; }
    .bg-white, .bg-white * { visibility: visible; }
    .bg-white { position: absolute; left: 0; top: 0; width: 100%; border: none !important; box-shadow: none !important; }
    header, aside, .form, button { display: none !important; }
}
</style>
@endsection
