@extends('layouts.app')

@section('title', 'Perhitungan Hasil Usaha')
@section('subtitle', 'Laporan Laba/Rugi Koperasi Tirta Bina Karya — Tahun Buku ' . $phu['tahun'])

@section('content')
<div class="space-y-8">

    {{-- Header Dokumen --}}
    <div class="bg-white border border-stone-200 rounded-2xl shadow-sm overflow-hidden relative">
        <div class="absolute top-0 inset-x-0 h-1 bg-[#043d2e]"></div>
        
        <div class="bg-[#043d2e] px-8 py-10 text-center relative overflow-hidden">
            <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 rounded-full bg-white/5 blur-3xl"></div>
            <div class="absolute bottom-0 left-0 -ml-16 -mb-16 w-64 h-64 rounded-full bg-[#022a20]/50 blur-3xl"></div>
            
            <div class="relative z-10">
                <p class="text-amber-400 text-[10px] font-black uppercase tracking-[0.4em] mb-2 opacity-90">Koperasi Simpan Pinjam Konsumen</p>
                <h1 class="text-2xl md:text-3xl font-black text-white tracking-wide mb-4 drop-shadow-md">TIRTA BINA KARYA <br> DINAS PUPRPKPP PROVINSI RIAU</h1>
                <div class="flex items-center justify-center gap-4 mb-4">
                    <div class="w-12 h-[1px] bg-stone-500/50"></div>
                    <div class="w-2 h-2 rotate-45 bg-amber-400"></div>
                    <div class="w-12 h-[1px] bg-stone-500/50"></div>
                </div>
                <h2 class="text-xl md:text-2xl font-serif font-bold text-white uppercase tracking-[0.2em] mb-2">PERHITUNGAN HASIL USAHA</h2>
                <p class="text-stone-300 text-sm font-medium">Per 31 Desember {{ $phu['tahun'] }}</p>
            </div>
        </div>

        <div class="p-6 md:p-10">
            <div class="max-w-3xl mx-auto space-y-8">

                {{-- ======================== --}}
                {{-- I. PENDAPATAN            --}}
                {{-- ======================== --}}
                <div>
                    <div class="flex items-center gap-3 border-b-2 border-emerald-600 pb-3 mb-5">
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center border border-emerald-100">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        </div>
                        <h3 class="text-base font-black text-stone-800 uppercase tracking-widest">I. Pendapatan</h3>
                    </div>

                    <div class="space-y-1">
                        @foreach($phu['pendapatan']['items'] as $item)
                        <div class="group flex justify-between items-center py-3 px-4 hover:bg-emerald-50/50 rounded-xl transition-colors border-b border-dashed border-stone-200">
                            <div class="flex items-center gap-3">
                                <div class="w-1.5 h-1.5 rounded-full bg-emerald-300 group-hover:bg-emerald-500 transition-colors"></div>
                                <span class="text-sm font-medium text-stone-700">{{ $item['nama'] }}</span>
                            </div>
                            <span class="text-[14px] font-mono font-bold {{ $item['nominal'] > 0 ? 'text-emerald-700' : 'text-stone-400' }}">
                                {{ format_rupiah($item['nominal']) }}
                            </span>
                        </div>
                        @endforeach
                    </div>

                    {{-- Total Pendapatan --}}
                    <div class="mt-4 p-4 rounded-xl border border-emerald-200 bg-emerald-50/70 backdrop-blur-sm">
                        <div class="flex justify-between items-center">
                            <span class="text-[12px] font-black text-emerald-700 uppercase tracking-widest">Jumlah Pendapatan</span>
                            <span class="text-lg font-mono font-black text-emerald-800">{{ format_rupiah($phu['pendapatan']['total']) }}</span>
                        </div>
                    </div>
                </div>

                {{-- ======================== --}}
                {{-- II. BEBAN OPERASIONAL    --}}
                {{-- ======================== --}}
                <div>
                    <div class="flex items-center gap-3 border-b-2 border-red-400 pb-3 mb-5">
                        <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center border border-red-100">
                            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/></svg>
                        </div>
                        <h3 class="text-base font-black text-stone-800 uppercase tracking-widest">II. Beban</h3>
                    </div>

                    <div class="space-y-1">
                        @forelse($phu['beban']['items'] as $item)
                        <div class="group flex justify-between items-center py-3 px-4 hover:bg-red-50/50 rounded-xl transition-colors border-b border-dashed border-stone-200">
                            <div class="flex items-center gap-3">
                                <div class="w-1.5 h-1.5 rounded-full bg-red-300 group-hover:bg-red-500 transition-colors"></div>
                                <span class="text-sm font-medium text-stone-700">{{ $item['nama'] }}</span>
                            </div>
                            <span class="text-[14px] font-mono font-bold text-red-600">{{ format_rupiah($item['nominal']) }}</span>
                        </div>
                        @empty
                        <div class="py-8 text-center">
                            <p class="text-sm text-stone-400 italic">Belum ada beban operasional tercatat di tahun {{ $phu['tahun'] }}.</p>
                        </div>
                        @endforelse
                    </div>

                    {{-- Total Beban --}}
                    <div class="mt-4 p-4 rounded-xl border border-red-200 bg-red-50/70 backdrop-blur-sm">
                        <div class="flex justify-between items-center">
                            <span class="text-[12px] font-black text-red-600 uppercase tracking-widest">Jumlah Beban Operasional</span>
                            <span class="text-lg font-mono font-black text-red-700">{{ format_rupiah($phu['beban']['total']) }}</span>
                        </div>
                    </div>
                </div>

                {{-- ======================== --}}
                {{-- RINGKASAN SHU            --}}
                {{-- ======================== --}}
                <div class="space-y-3">
                    {{-- SHU Sebelum Pajak --}}
                    <div class="flex justify-between items-center py-3 px-5 bg-stone-100 rounded-xl border border-stone-200">
                        <span class="text-sm font-bold text-stone-700">SHU Sebelum Pajak</span>
                        <span class="text-base font-mono font-black {{ $phu['shu_sebelum_pajak'] >= 0 ? 'text-emerald-700' : 'text-red-600' }}">
                            {{ format_rupiah($phu['shu_sebelum_pajak']) }}
                        </span>
                    </div>

                    {{-- Pajak SHU --}}
                    @if($phu['pajak_shu'] > 0)
                    <div class="flex justify-between items-center py-3 px-5 bg-stone-50 rounded-xl border border-stone-200">
                        <span class="text-sm font-medium text-stone-600">Pajak SHU</span>
                        <span class="text-[14px] font-mono font-bold text-stone-700">{{ format_rupiah($phu['pajak_shu']) }}</span>
                    </div>
                    @endif

                    {{-- SHU Bersih — Glassmorphism Card --}}
                    <div class="p-6 rounded-2xl shadow-lg border border-white/20 relative overflow-hidden"
                         style="background: linear-gradient(135deg, {{ $phu['shu_bersih'] >= 0 ? '#043d2e 0%, #065a45 50%, #087a5e 100%' : '#7f1d1d 0%, #991b1b 50%, #b91c1c 100%' }});">
                        <div class="absolute inset-0 backdrop-blur-sm bg-white/5"></div>
                        <div class="absolute -top-10 -right-10 w-40 h-40 rounded-full {{ $phu['shu_bersih'] >= 0 ? 'bg-emerald-400/10' : 'bg-red-400/10' }} blur-2xl"></div>
                        <div class="absolute -bottom-10 -left-10 w-32 h-32 rounded-full {{ $phu['shu_bersih'] >= 0 ? 'bg-amber-400/10' : 'bg-red-300/10' }} blur-2xl"></div>
                        <div class="relative z-10 flex justify-between items-center">
                            <div>
                                <p class="text-[11px] font-black uppercase tracking-widest {{ $phu['shu_bersih'] >= 0 ? 'text-emerald-200' : 'text-red-200' }} mb-1">SHU Bersih</p>
                                <p class="text-[10px] {{ $phu['shu_bersih'] >= 0 ? 'text-emerald-300/70' : 'text-red-300/70' }}">Tahun Buku {{ $phu['tahun'] }}</p>
                            </div>
                            <span class="text-2xl font-mono font-black text-white drop-shadow-sm">{{ format_rupiah($phu['shu_bersih']) }}</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Lembar Pengesahan --}}
    <div class="bg-white border border-stone-200 rounded-2xl shadow-sm p-10 relative overflow-hidden">
        <div class="absolute inset-0 opacity-[0.02] pointer-events-none bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
        <div class="relative z-10 grid grid-cols-1 md:grid-cols-3 gap-10 text-center text-[14px] text-stone-800">
            <div class="flex flex-col items-center justify-end">
                <p class="font-medium text-stone-500 mb-20">Dibuat oleh,</p>
                <div class="w-48 border-b-2 border-stone-800 pb-1"><p class="font-bold text-stone-800">Bendahara</p></div>
                <p class="text-xs text-stone-500 mt-1 uppercase tracking-wider">Koperasi Tirta Bina Karya</p>
            </div>
            <div class="flex flex-col items-center justify-end">
                <p class="font-medium text-stone-500 mb-20">Diperiksa oleh,</p>
                <div class="w-48 border-b-2 border-stone-800 pb-1"><p class="font-bold text-stone-800">Pengawas</p></div>
                <p class="text-xs text-stone-500 mt-1 uppercase tracking-wider">Koperasi Tirta Bina Karya</p>
            </div>
            <div class="flex flex-col items-center justify-end">
                <p class="text-xs text-stone-500 mb-1 text-right w-full pr-6">Pekanbaru, {{ now()->translatedFormat('d F Y') }}</p>
                <p class="font-medium text-stone-500 mb-16">Disetujui oleh,</p>
                <div class="w-48 border-b-2 border-stone-800 pb-1"><p class="font-bold text-stone-800">Ketua Koperasi</p></div>
                <p class="text-xs text-stone-500 mt-1 uppercase tracking-wider">Koperasi Tirta Bina Karya</p>
            </div>
        </div>
    </div>

</div>
@endsection
