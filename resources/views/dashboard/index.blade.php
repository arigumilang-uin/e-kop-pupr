@extends('layouts.app')

@section('title', 'Dashboard')
@section('subtitle', 'Selamat datang, ' . auth()->user()->nama)

@section('content')
{{-- Stats Grid --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
    {{-- Saldo Koperasi --}}
    <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-2xl p-5 text-white shadow-lg shadow-blue-600/20">
        <div class="flex items-center justify-between mb-3">
            <span class="text-blue-200 text-sm font-medium">Saldo Koperasi</span>
            <div class="w-10 h-10 rounded-xl bg-white/15 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold">{{ format_rupiah($stats['saldo_koperasi']) }}</p>
        <p class="text-blue-200 text-xs mt-1">Dihitung real-time</p>
    </div>

    {{-- Total Anggota --}}
    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm transition-all hover:shadow-md hover:border-slate-300">
        <div class="flex items-center justify-between mb-3">
            <span class="text-slate-500 text-sm font-medium">Anggota Aktif</span>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-slate-800">{{ number_format($stats['total_anggota'], 0, ',', '.') }}</p>
        <p class="text-slate-400 text-xs mt-1">Orang</p>
    </div>

    {{-- Pinjaman Aktif --}}
    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm transition-all hover:shadow-md hover:border-slate-300">
        <div class="flex items-center justify-between mb-3">
            <span class="text-slate-500 text-sm font-medium">Pinjaman Aktif</span>
            <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-slate-800">{{ number_format($stats['total_pinjaman_aktif'], 0, ',', '.') }}</p>
        <p class="text-slate-400 text-xs mt-1">Pinjaman berjalan</p>
    </div>

    {{-- Total Simpanan --}}
    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm transition-all hover:shadow-md hover:border-slate-300">
        <div class="flex items-center justify-between mb-3">
            <span class="text-slate-500 text-sm font-medium">Total Simpanan</span>
            <div class="w-10 h-10 rounded-xl bg-violet-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-slate-800">{{ format_rupiah($stats['total_simpanan']) }}</p>
        <p class="text-slate-400 text-xs mt-1">Seluruh anggota</p>
    </div>
</div>

{{-- Pinjaman Menunggu Approval --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
    <div class="p-5 border-b border-slate-100 flex items-center justify-between">
        <div>
            <h3 class="font-semibold text-slate-800">Pengajuan Pinjaman Terbaru</h3>
            <p class="text-sm text-slate-500 mt-0.5">Menunggu review pengurus</p>
        </div>
        @if($pinjamanMenunggu->count() > 0)
        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">
            {{ $pinjamanMenunggu->count() }} menunggu
        </span>
        @endif
    </div>

    @if($pinjamanMenunggu->isEmpty())
    <div class="p-10 text-center">
        <div class="w-14 h-14 rounded-2xl bg-slate-50 flex items-center justify-center mx-auto mb-3 border border-slate-100">
            <svg class="w-7 h-7 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
        </div>
        <p class="text-slate-500 text-sm">Tidak ada pengajuan pinjaman baru yang menunggu review.</p>
    </div>
    @else
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-50/80 border-y border-slate-100">
                    <th class="px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">No. Referensi</th>
                    <th class="px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Anggota</th>
                    <th class="px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Nominal</th>
                    <th class="px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Tenor</th>
                    <th class="px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Tanggal</th>
                    <th class="px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
                @foreach($pinjamanMenunggu as $pinjaman)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-5 py-3.5 whitespace-nowrap">
                        <span class="text-sm font-semibold text-blue-600">{{ $pinjaman->no_referensi }}</span>
                    </td>
                    <td class="px-5 py-3.5 whitespace-nowrap">
                        <p class="text-sm font-medium text-slate-800">{{ $pinjaman->anggota->nama }}</p>
                        <p class="text-xs text-slate-400 mt-0.5">{{ $pinjaman->anggota->nip }}</p>
                    </td>
                    <td class="px-5 py-3.5 whitespace-nowrap">
                        <span class="text-sm font-medium text-slate-700">{{ format_rupiah($pinjaman->nominal_pinjaman) }}</span>
                    </td>
                    <td class="px-5 py-3.5 whitespace-nowrap">
                        <span class="text-sm text-slate-600">{{ $pinjaman->tenor_bulan }} bulan</span>
                    </td>
                    <td class="px-5 py-3.5 whitespace-nowrap">
                        <span class="text-sm text-slate-500">{{ $pinjaman->tanggal_pengajuan->format('d M Y') }}</span>
                    </td>
                    <td class="px-5 py-3.5 whitespace-nowrap">
                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-600/20">
                            Menunggu
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection
