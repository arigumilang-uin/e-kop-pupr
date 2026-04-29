@extends('layouts.guest')
@section('title', 'Periode Ditutup - Tirta Bina Karya')

@section('content')
<x-guest-nav />

<main class="max-w-4xl mx-auto px-4 py-8 md:py-16">
    
    <!-- Premium Header Area -->
    <div class="mb-10 text-left max-w-2xl">
        <h1 class="font-['Plus_Jakarta_Sans'] text-3xl md:text-4xl font-extrabold tracking-tight text-[#0f172a] mb-4">
            Periode Ditutup
        </h1>
        <p class="text-slate-500 text-[15px] leading-relaxed">
            Periode pembiayaan yang Anda tuju saat ini tidak menerima pengajuan baru karena rentang waktunya telah lewat atau ditutup oleh administrator.
        </p>
    </div>

    <x-card class="bg-white p-0 overflow-hidden border border-slate-200 shadow-sm relative">
        <div class="px-6 md:px-8 py-5 border-b border-slate-100 flex flex-col md:flex-row items-start md:items-center justify-between gap-4 bg-slate-50">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-white border border-slate-200 shadow-sm flex items-center justify-center text-red-500 shrink-0">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                <div>
                    <p class="text-[11px] font-bold tracking-widest text-slate-400 uppercase mb-0.5">Nama Periode</p>
                    <h3 class="font-bold text-[18px] md:text-[20px] text-[#0f172a] tracking-tight truncate">{{ $periode->nama_periode }}</h3>
                </div>
            </div>

            <div class="flex flex-col md:items-end w-full md:w-auto mt-2 md:mt-0 pt-3 md:pt-0 border-t md:border-0 border-slate-200/50">
                <span class="inline-flex mt-0.5 md:mt-0 items-center px-2.5 py-1 rounded-[6px] border text-[11px] font-bold tracking-widest uppercase bg-red-100 text-red-700 border-red-200">
                    Tidak Tersedia
                </span>
            </div>
        </div>

        <div class="px-6 py-6 md:px-8 md:py-8 space-y-4">
            <div class="p-5 rounded-xl border border-rose-100 bg-rose-50/50">
                <div class="text-[14px] text-slate-700 leading-relaxed font-medium">
                    {!! $pesan_tutup !!}
                </div>
            </div>
            <p class="text-[13px] text-slate-500 leading-relaxed">
                <span class="font-semibold text-slate-700">Tindakan Selanjutnya:</span> Silahkan hubungi pihak internal koperasi Dinas PUPR Provinsi Riau untuk informasi lebih mendetail terkait jadwal relist/pembukaan pinjaman selanjutnya.
            </p>
        </div>
    </x-card>

    <div class="mt-12 flex flex-col sm:flex-row items-center justify-start gap-4">
        <a href="{{ route('simulasi') }}" class="w-full sm:w-auto h-12 px-8 bg-[#0f172a] text-white hover:bg-slate-800 rounded-xl font-bold text-[14px] shadow-lg shadow-[#0f172a]/10 transition-all flex items-center justify-center gap-2 outline-none">
            Simulasi Pinjaman
        </a>
        <a href="{{ route('pinjaman.guest.status') }}" class="w-full sm:w-auto h-12 px-8 bg-white text-slate-700 hover:bg-slate-50 border border-slate-200 rounded-xl font-bold text-[14px] shadow-sm transition-all flex items-center justify-center gap-2 outline-none">
            Cek Status Pengajuan
        </a>
    </div>

    <footer class="mt-16 text-left space-y-3">
        <x-guest-footer />
    </footer>
</main>
@endsection
