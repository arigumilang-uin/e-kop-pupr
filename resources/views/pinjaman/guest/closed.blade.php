@extends('layouts.guest')

@section('title', 'Periode Ditutup')

@section('content')
<div class="w-full max-w-md text-center">
    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-red-500/10 mb-6 border border-red-500/20">
        <svg class="w-10 h-10 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
    </div>

    <h1 class="text-2xl font-bold text-white mb-2">Periode Pinjaman Ditutup</h1>
    
    <div class="bg-white/5 backdrop-blur-xl rounded-2xl border border-white/10 p-6 shadow-2xl mt-6 text-left">
        <p class="text-slate-300 text-sm leading-relaxed mb-4">
            {!! $pesan_tutup ?? 'Mohon maaf, pengajuan pinjaman untuk periode <strong>'.$periode->nama_periode.'</strong> saat ini sedang tidak dibuka atau sudah ditutup oleh Pengurus.' !!}
        </p>

        <p class="text-slate-300 text-sm leading-relaxed">
            Silahkan hubungi pihak internal koperasi Dinas PUPR Provinsi Riau untuk informasi jadwal pembukaan pinjaman selanjutnya.
        </p>
    </div>

    <div class="mt-8">
        <a href="{{ route('simulasi') }}" class="text-sm font-medium text-blue-400 hover:text-blue-300 transition-colors">
            Kembali ke Simulasi Pinjaman &rarr;
        </a>
    </div>
</div>
@endsection
