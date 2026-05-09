@extends('layouts.guest')
@section('title', 'Status Pengajuan Pinjaman')

@section('content')
<x-guest-nav />

<main class="max-w-4xl mx-auto px-4 py-8 md:py-16">
    <div class="mb-10 text-center">
        <h1 style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 32px; font-weight: 800; letter-spacing: -1px; color: #0f172a; margin-bottom: 8px;">
            Riwayat Pengajuan
        </h1>
        <p class="text-slate-500 text-[15px] leading-relaxed">
            Menampilkan data permohonan pinjaman atas nama <span class="font-bold text-slate-800">{{ $anggota->nama }}</span> (NIP: {{ $anggota->nip }}).
        </p>
    </div>

    <div class="space-y-8">
        @foreach($pinjamans as $pinjaman)
        <x-card class="bg-white p-0 overflow-hidden border border-slate-200 shadow-sm transition-shadow hover:shadow-md relative">
            
            <!-- Header (No Referensi & Status) -->
            <div class="px-6 md:px-8 py-5 border-b border-slate-100 flex flex-col md:flex-row items-start md:items-center justify-between gap-4 bg-slate-50">
                <div class="flex items-center gap-4">
                    <!-- Elegant Number Display Box -->
                    <div class="w-12 h-12 rounded-xl bg-white border border-slate-200 shadow-sm flex items-center justify-center text-slate-400 shrink-0">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold tracking-widest text-slate-400 uppercase mb-0.5">Nomor Referensi</p>
                        <p class="text-[18px] md:text-[20px] font-mono font-bold text-[#0f172a] tracking-wider">{{ $pinjaman->no_referensi }}</p>
                    </div>
                </div>

                <div class="flex flex-col md:items-end gap-1.5 w-full md:w-auto mt-2 md:mt-0 pt-3 md:pt-0 border-t md:border-0 border-slate-200/50">
                    <p class="text-[12px] text-slate-500 font-medium">Diajukan: <span class="font-semibold text-slate-700">{{ $pinjaman->tanggal_pengajuan->translatedFormat('d M Y, H:i') }}</span></p>
                    @php
                        $statusStyle = match($pinjaman->status->value) {
                            'menunggu' => 'bg-amber-100 text-amber-700 border-amber-200',
                            'disetujui', 'berjalan' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                            'ditolak', 'dibatalkan' => 'bg-red-100 text-red-700 border-red-200',
                            default => 'bg-slate-100 text-slate-700 border-slate-200',
                        };
                    @endphp
                    <span class="inline-flex mt-0.5 md:mt-0 items-center px-2.5 py-1 rounded-[6px] border text-[11px] font-bold tracking-widest uppercase {{ $statusStyle }}">
                        {{ $pinjaman->status->label() }}
                    </span>
                </div>
            </div>

            <!-- Body Details -->
            <div class="px-6 py-6 md:px-8 md:py-8 grid grid-cols-1 lg:grid-cols-2 gap-x-12 gap-y-10">
                
                <!-- Kiri: Rekening Pencairan -->
                <div>
                    <div class="flex items-center gap-2 mb-4 pb-2 border-b border-slate-100">
                        <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        <h3 class="text-[12px] font-bold tracking-widest text-slate-800 uppercase">Rekening Pencairan</h3>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <p class="text-[11px] font-bold tracking-widest text-slate-400 uppercase mb-1">Nama Bank</p>
                            <p class="font-bold text-[14px] text-slate-800">{{ $pinjaman->nama_bank }}</p>
                        </div>
                        <div>
                            <p class="text-[11px] font-bold tracking-widest text-slate-400 uppercase mb-1">Nomor Rekening</p>
                            <p class="font-mono font-bold text-[16px] text-slate-900 tracking-wider">{{ $pinjaman->no_rekening }}</p>
                        </div>
                        <div>
                            <p class="text-[11px] font-bold tracking-widest text-slate-400 uppercase mb-1">Pemilik Rekening</p>
                            <p class="font-semibold text-[14px] text-slate-700">{{ $pinjaman->nama_rekening }}</p>
                        </div>
                    </div>
                </div>

                <!-- Kanan: Rincian Pinjaman -->
                <div>
                    <div class="flex items-center gap-2 mb-4 pb-2 border-b border-slate-100">
                        <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <h3 class="text-[12px] font-bold tracking-widest text-slate-800 uppercase">Rincian Finansial</h3>
                    </div>

                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-[13px] font-medium text-slate-500">Nominal Pengajuan</span>
                            <span class="font-mono font-bold text-slate-900 border-b border-dashed border-slate-300">Rp {{ number_format($pinjaman->nominal_pinjaman, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-[13px] font-medium text-slate-500">Total Bunga ({{ floatval($pinjaman->bunga_persen) }}%)</span>
                            <span class="font-mono font-semibold text-slate-700">Rp {{ number_format($pinjaman->total_bunga, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-[13px] font-medium text-slate-500">Tenor</span>
                            <span class="font-mono font-semibold text-slate-700">{{ $pinjaman->tenor_bulan }} Bulan</span>
                        </div>
                        <div class="flex justify-between items-center pt-2 mt-2 border-t border-slate-100">
                            <span class="text-[13px] font-bold text-[#0f172a]">Angsuran / Bulan</span>
                            <span class="font-mono font-bold text-[15px] text-[#0f172a]">Rp {{ number_format($pinjaman->total_angsuran, 0, ',', '.') }}</span>
                        </div>
                        
                        <!-- Box Highlight Cair Premium Minimalist -->
                        <div class="mt-4 p-4 rounded-xl bg-slate-50 border border-slate-200 flex justify-between items-center">
                            <span class="text-[12px] font-bold text-slate-600 tracking-widest uppercase">Pencairan Bersih</span>
                            <span class="font-mono font-black text-lg text-emerald-600">Rp {{ number_format($pinjaman->dana_diterima, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Footer Notes & Actions -->
            @if($pinjaman->catatan || $pinjaman->status->value === 'menunggu')
            <div class="px-6 md:px-8 py-4 bg-slate-50/80 border-t border-slate-200 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                @if($pinjaman->catatan)
                <div class="flex-1 w-full relative">
                    <p class="text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-1.5 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Tanggapan Pengurus
                    </p>
                    <div class="bg-white border border-slate-200 rounded-lg p-3 text-[13px] text-slate-700 italic shadow-sm">
                        {{ $pinjaman->catatan }}
                    </div>
                </div>
                @else
                <div class="flex-1"></div>
                @endif

                @if($pinjaman->status->value === 'menunggu')
                <div class="shrink-0 w-full md:w-auto mt-2 md:mt-0 flex justify-end">
                    <form action="{{ route('pinjaman.guest.cancel') }}" method="POST" onsubmit="return confirm('Tindakan ini tidak bisa dibatalkan!\nApakah Anda benar-benar yakin ingin membatalkan pengajuan ini?');" class="inline-block w-full md:w-auto">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="no_referensi" value="{{ $pinjaman->no_referensi }}">
                        <button type="submit" class="w-full bg-white hover:bg-slate-50 text-red-600 border border-slate-300 rounded-[10px] px-5 py-2.5 text-[13px] font-bold transition-all flex items-center justify-center gap-2 group outline-none shadow-sm">
                            <svg class="w-4 h-4 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            Batal Pengajuan
                        </button>
                    </form>
                </div>
                @endif
            </div>
            @endif

        </x-card>
        @endforeach

        <!-- Global Action -->
        <div class="pt-6 flex justify-center">
            <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('pinjaman.guest.status') }}" class="h-12 px-8 bg-[#0f172a] text-white hover:bg-slate-800 rounded-xl font-bold text-[14px] shadow-lg shadow-slate-900/10 transition-all flex items-center justify-center gap-2 outline-none group">
                Kembali ke Pencarian
                <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </a>
        </div>
    </div>
</main>
@endsection
