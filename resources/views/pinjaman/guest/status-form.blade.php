@extends('layouts.guest')
@section('title', 'Cek Status Pengajuan - Tirta Bina Karya')

@section('content')
<x-guest-nav />

<main class="max-w-xl mx-auto px-4 py-8 md:py-16" x-data="statusChecker()">
    
    <!-- Premium Header Area -->
    <div class="mb-10 text-center">
        <h1 class="font-['Plus_Jakarta_Sans'] text-3xl md:text-4xl font-extrabold tracking-tight text-[#0f172a] mb-4">
            Lacak Pengajuan
        </h1>
        <p class="text-slate-500 text-[15px] leading-relaxed">
            Silakan masukkan NIP dan Nomor Referensi untuk mengetahui perkembangan permohonan pinjaman Anda.
        </p>
    </div>

    @if(session('success_ref'))
    <div class="mb-8 p-6 rounded-[24px] bg-emerald-50 border border-emerald-100 flex flex-col md:flex-row items-center md:items-start text-center md:text-left gap-5 shadow-sm">
        <div class="bg-white p-3.5 rounded-full shadow-sm shrink-0 border border-emerald-100">
            <svg class="w-7 h-7 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <h3 class="text-emerald-800 font-extrabold text-[16px] mb-1.5">Pengajuan Berhasil Dikirim!</h3>
            <p class="text-emerald-600/90 text-[13.5px] leading-relaxed mb-4">Harap simpan Nomor Referensi berikut. Anda dapat melacak perkembangan persetujuan dana kapan saja dengan nomor ini.</p>
            <div class="inline-block px-5 py-2.5 bg-white rounded-xl border border-emerald-200 shadow-sm">
                <span class="font-mono font-bold text-xl text-emerald-700 tracking-wider">{{ session('success_ref') }}</span>
            </div>
        </div>
    </div>
    @endif

    <x-card>
        <form method="POST" action="{{ route('pinjaman.guest.check') }}" class="space-y-6">
            @csrf
            
            @if(session('error'))
            <div class="p-4 rounded-xl bg-red-50 border border-red-100 flex items-center gap-3 mb-6">
                <svg class="w-5 h-5 text-red-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span class="text-[13.5px] font-medium text-red-800">{{ session('error') }}</span>
            </div>
            @endif

            <div>
                <x-label for="nip">Nomor Induk Pegawai (NIP)</x-label>
                <x-nip-input id="nip" name="nip" model="nip" required="true" placeholder="Contoh: 19800101...">
                    <x-slot name="icon">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </x-slot>
                </x-nip-input>
                <p class="mt-2 text-[12px] font-medium" :class="(nip.length > 0 && nip.length !== 18) ? 'text-red-500' : 'text-slate-500'">
                    <span x-show="nip.length === 0 || nip.length === 18">Masukkan NIP Anda untuk melacak.</span>
                    <span x-show="nip.length > 0 && nip.length !== 18">NIP harus tepat 18 digit angka (saat ini <span x-text="nip.length"></span> digit).</span>
                </p>
            </div>

            <div>
                <x-label for="no_referensi">Nomor Referensi <span class="text-slate-400 font-normal ml-1">(Opsional)</span></x-label>
                <x-input id="no_referensi" name="no_referensi" value="{{ old('no_referensi') }}" placeholder="PJM-2026-..." class="uppercase font-mono font-semibold tracking-wide text-lg text-slate-700">
                    <x-slot name="icon">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </x-slot>
                </x-input>
                <p class="mt-2 text-[12.5px] text-slate-500 font-medium leading-relaxed">Kosongkan kolom ini jika ingin melihat pengajuan terbaru Anda pada tahun ini.</p>
            </div>

            <div class="pt-4">
                <button type="submit" 
                        :disabled="!isValid"
                        :class="!isValid ? 'opacity-50 cursor-not-allowed bg-slate-300 text-slate-500' : 'bg-[#0f172a] text-white hover:bg-slate-800 shadow-xl shadow-slate-900/10'"
                        class="w-full h-14 rounded-xl font-bold text-[15px] transition-all flex items-center justify-center gap-2 group outline-none focus:ring-4 focus:ring-slate-900/20 active:scale-[0.98]">
                    Konfirmasi & Lacak Status
                    <svg class="w-5 h-5 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </button>
            </div>
        </form>
    </x-card>

    <footer class="mt-16 text-center space-y-3">
        <x-guest-footer />
    </footer>
</main>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('statusChecker', () => ({
            nip: '{{ old('nip', '') }}',
            get isValid() {
                return this.nip.length === 18;
            }
        }));
    });
</script>
@endsection
