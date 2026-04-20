@extends('layouts.guest')

@section('title', 'Cek Status Pengajuan')

@section('content')
<div class="w-full max-w-md">
    <div class="text-center mb-8">
        <h1 class="text-2xl font-bold text-white">Cek Status Pinjaman</h1>
        <p class="text-slate-400 text-sm mt-1">Lacak status pengajuan pinjaman Anda</p>
    </div>

    @if(session('success_ref'))
    <div class="mb-6 p-5 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-center">
        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-emerald-500/20 mb-3">
            <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <h3 class="text-emerald-400 font-bold mb-1">Pengajuan Berhasil Disimpan</h3>
        <p class="text-slate-300 text-sm mb-3">Nomor referensi pengajuan Anda:</p>
        <p class="text-2xl font-mono font-bold text-white tracking-widest">{{ session('success_ref') }}</p>
    </div>
    @endif

    <div class="bg-white/5 backdrop-blur-xl rounded-2xl border border-white/10 p-8 shadow-2xl">
        <form method="POST" action="{{ route('pinjaman.guest.check') }}" class="space-y-5">
            @csrf

            @if(session('error'))
            <div class="p-3.5 rounded-xl bg-red-500/10 border border-red-500/20 text-red-300 text-sm">
                {{ session('error') }}
            </div>
            @endif

            {{-- NIP --}}
            <div>
                <label for="nip" class="block text-sm font-medium text-slate-300 mb-1.5">NIP Anggota <span class="text-red-400">*</span></label>
                <input type="text" id="nip" name="nip" value="{{ old('nip') }}" required
                       class="w-full px-4 py-3 rounded-xl bg-white/5 border border-white/10 text-white placeholder-slate-500
                              focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50 text-sm">
            </div>

            {{-- No Referensi --}}
            <div>
                <label for="no_referensi" class="block text-sm font-medium text-slate-300 mb-1.5">No Referensi <span class="text-slate-500 font-normal">(Opsional)</span></label>
                <input type="text" id="no_referensi" name="no_referensi" value="{{ old('no_referensi') }}"
                       placeholder="Misal: PJM-2026-0001"
                       class="w-full px-4 py-3 rounded-xl bg-white/5 border border-white/10 text-white placeholder-slate-500
                              focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50 text-sm uppercase">
                <p class="text-xs text-slate-500 mt-2">Kosongkan jika ingin melihat pengajuan terbaru Anda tahun ini.</p>
            </div>

            <button type="submit"
                    class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-blue-600 to-blue-500 text-white font-medium text-sm
                           hover:from-blue-500 hover:to-blue-400 transition-all duration-200">
                Lacak Status
            </button>
        </form>
    </div>
</div>
@endsection
