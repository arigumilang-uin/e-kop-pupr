@extends('layouts.guest')
@section('title', 'Jadwal Periode Pinjaman - ASSET')

@section('content')
<x-guest-nav />

<main class="max-w-4xl mx-auto px-4 py-8 md:py-16">
    
    <!-- Premium Header Area -->
    <div class="mb-10 text-left max-w-2xl">
        <div class="flex flex-row items-center justify-start gap-4 mb-6">
            <h1 style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 46px; font-weight: 800; margin: 0; letter-spacing: -2.5px; line-height: 1; color: #0f172a; white-space: nowrap;">ASSET</h1>
            <div style="width: 2px; height: 38px; background: #cbd5e1;"></div>
            <p style="color: #475569; font-size: 12px; margin: 0; line-height: 1.4; font-weight: 600;">
                Jadwal Periode<br>Mendatang
            </p>
        </div>
        <p class="text-slate-500 text-[15px] leading-relaxed">
            Saat ini tidak ada periode pengajuan pinjaman yang sedang dibuka. Berikut adalah jadwal periode yang telah dikonfigurasi oleh Pengurus.
        </p>
    </div>

    <!-- Daftar Periode Mendatang -->
    <div class="space-y-6">
        @foreach($periodeMendatang as $index => $periode)
        <x-card class="bg-white p-0 border border-slate-200 shadow-sm overflow-hidden transition-shadow hover:shadow-md relative">
            
            <div class="px-6 md:px-8 py-5 border-b border-slate-100 flex flex-col md:flex-row items-start md:items-center justify-between gap-4 bg-slate-50">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-white border border-slate-200 shadow-sm flex items-center justify-center text-slate-400 shrink-0">
                        <span class="font-mono font-bold text-[20px] text-[#0f172a]">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold tracking-widest text-slate-400 uppercase mb-0.5">Nama Periode</p>
                        <h3 class="font-bold text-[18px] md:text-[20px] text-[#0f172a] tracking-tight truncate">{{ $periode->nama_periode }}</h3>
                    </div>
                </div>

                <div class="flex flex-col md:items-end gap-1.5 w-full md:w-auto mt-2 md:mt-0 pt-3 md:pt-0 border-t md:border-0 border-slate-200/50">
                    <span class="inline-flex mt-0.5 md:mt-0 items-center px-2.5 py-1 rounded-[6px] border text-[11px] font-bold tracking-widest uppercase bg-amber-100 text-amber-700 border-amber-200">
                        Terjadwal
                    </span>
                </div>
            </div>

            <div class="px-6 py-6 md:px-8 md:py-8 grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-8">
                <div>
                    <div class="flex items-center gap-2 mb-4 pb-2 border-b border-slate-100">
                        <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <h3 class="text-[12px] font-bold tracking-widest text-slate-800 uppercase">Rentang Waktu</h3>
                    </div>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-[13px] font-medium text-slate-500">Tanggal Dibuka</span>
                            <span class="font-mono font-semibold text-slate-900">{{ $periode->tanggal_buka->translatedFormat('d M Y') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-[13px] font-medium text-slate-500">Tanggal Ditutup</span>
                            <span class="font-mono font-semibold text-slate-700">{{ $periode->tanggal_tutup->translatedFormat('d M Y') }}</span>
                        </div>
                    </div>
                </div>

                @php
                    $hariLagi = now()->startOfDay()->diffInDays($periode->tanggal_buka, false);
                @endphp
                <div class="flex flex-col justify-center bg-slate-50 rounded-xl border border-slate-100 p-5 items-center text-center">
                    <p class="text-[12px] font-bold tracking-widest text-slate-400 uppercase mb-2">Formulir Dibuka Dalam</p>
                    <div class="flex items-end gap-2">
                        <span class="font-mono font-bold text-[32px] md:text-[40px] leading-none text-[#0f172a]">{{ (int) $hariLagi }}</span>
                        <span class="text-[15px] font-bold text-slate-500 mb-1">Hari Lagi</span>
                    </div>
                </div>
            </div>
            
        </x-card>
        @endforeach
    </div>

    <!-- Quick Links -->
    <div class="mt-12 flex flex-col sm:flex-row items-center justify-start gap-4">
        <a href="{{ route('simulasi') }}" class="w-full sm:w-auto h-12 px-8 bg-[#0f172a] text-white hover:bg-slate-800 rounded-xl font-bold text-[14px] shadow-lg shadow-[#0f172a]/10 transition-all flex items-center justify-center gap-2 outline-none">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
            Simulasi Pinjaman
        </a>
        <a href="{{ route('pinjaman.guest.status') }}" class="w-full sm:w-auto h-12 px-8 bg-white text-slate-700 hover:bg-slate-50 border border-slate-200 rounded-xl font-bold text-[14px] shadow-sm transition-all flex items-center justify-center gap-2 outline-none">
            Cek Status Pengajuan
        </a>
    </div>

    <footer class="mt-16 text-left space-y-3">
        <p class="text-[12px] font-semibold text-slate-400 tracking-widest uppercase">
            © {{ date('Y') }} KOPERASI SIMPAN PINJAM PKPP PUPR RIAU
        </p>
    </footer>
</main>
@endsection
