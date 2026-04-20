@extends('layouts.app')

@section('title', 'Transaksi Simpanan')
@section('subtitle', 'Histori penyetoran simpanan anggota dan kas koperasi')

@section('actions')
<a href="{{ route('simpanan.create') }}" class="py-2.5 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium transition-colors flex items-center gap-2 shadow-sm">
    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
    </svg>
    Catat Penyetoran
</a>
@endsection

@section('content')

{{-- Info Banner Saldo Koperasi --}}
<div class="mb-6 p-6 rounded-2xl bg-gradient-to-br from-slate-900 to-blue-900 border border-slate-800 flex flex-col md:flex-row items-center justify-between gap-6 shadow-xl relative overflow-hidden">
    <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>
    <div class="relative z-10">
        <p class="text-blue-300 font-medium text-sm mb-1 uppercase tracking-wider">Total Kas / Saldo Koperasi Aktif</p>
        <h2 class="text-3xl md:text-4xl font-bold text-white font-mono tracking-tight">Rp {{ number_format($totalSaldoKoperasi, 0, ',', '.') }}</h2>
        <p class="text-xs text-slate-400 mt-2">Perhitungan otomatis agregasi uang masuk dikurangi uang dipinjamkan.</p>
    </div>
    <div class="relative z-10 shrink-0 w-16 h-16 rounded-full bg-blue-500/20 flex items-center justify-center border border-blue-500/30">
        <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
        </svg>
    </div>
</div>

<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    {{-- Filter Bar --}}
    <div class="p-4 sm:p-5 border-b border-slate-200 bg-slate-50/50 flex flex-col sm:flex-row gap-4 justify-between items-center">
        <form action="{{ route('simpanan.index') }}" method="GET" class="w-full sm:max-w-xs relative">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari Nama Anggota/NIP..."
                   class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
            <svg class="w-5 h-5 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </form>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-50 text-slate-500 font-medium border-b border-slate-200 text-[13px] uppercase tracking-wider">
                <tr>
                    <th scope="col" class="px-6 py-4">Tanggal & Ref</th>
                    <th scope="col" class="px-6 py-4">Anggota</th>
                    <th scope="col" class="px-6 py-4">Kategori Simpanan</th>
                    <th scope="col" class="px-6 py-4 text-right">Nominal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse($simpanans as $simpanan)
                <tr class="hover:bg-slate-50/80 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span class="font-semibold text-slate-800">{{ $simpanan->tanggal->format('d M Y') }}</span>
                            <span class="text-xs text-slate-500 font-mono mt-0.5">{{ $simpanan->no_referensi }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span class="text-slate-800 font-medium">{{ $simpanan->anggota->nama }}</span>
                            <span class="text-xs text-slate-500 mt-0.5">NIP: {{ $simpanan->anggota->nip }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-blue-50 text-blue-700 border border-blue-200 text-xs font-semibold">
                            {{ $simpanan->jenisSimpanan->nama }}
                        </span>
                        @if($simpanan->bulan_untuk)
                        <div class="text-[11px] text-slate-500 mt-1">Untuk: {{ App\Helpers\FormatHelper::namaBulan($simpanan->bulan_untuk) }} {{ $simpanan->tahun_untuk }}</div>
                        @elseif($simpanan->keterangan)
                        <div class="text-[11px] text-slate-500 mt-1">{{ Str::limit($simpanan->keterangan, 30) }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right">
                        <span class="text-emerald-600 font-bold font-mono">
                            + Rp {{ number_format($simpanan->nominal, 0, ',', '.') }}
                        </span>
                        <div class="text-[10px] text-slate-400 mt-0.5 uppercase tracking-wide">
                            Ditambah oleh: {{ $simpanan->pencatat->nama }}
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-slate-500">
                        Belum ada riwayat transaksi simpanan/uang masuk.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($simpanans->hasPages())
    <div class="px-6 py-4 border-t border-slate-200">
        {{ $simpanans->links() }}
    </div>
    @endif
</div>
@endsection
