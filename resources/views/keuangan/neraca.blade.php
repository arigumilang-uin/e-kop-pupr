@extends('layouts.app')

@section('title', 'Neraca Keuangan')
@section('subtitle', 'Laporan Posisi Keuangan Koperasi Simpan Pinjam Konsumen Tirta Bina Karya')

@section('actions')
<div class="flex items-center gap-3" x-data="{ open: false }">
    <div class="relative">
        <button @click="open = !open" @click.away="open = false" 
                class="inline-flex items-center gap-2 py-2.5 px-4 rounded-xl bg-white border border-stone-200 text-stone-700 hover:bg-stone-50 hover:text-stone-900 text-sm font-bold transition-all shadow-sm">
            <svg class="w-4 h-4 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Export Neraca
            <svg class="w-4 h-4 text-stone-400 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
        </button>

        <div x-show="open" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-2"
             class="absolute right-0 mt-2 w-48 bg-white border border-stone-200 rounded-xl shadow-lg z-50 overflow-hidden" 
             style="display: none;">
             
            <a href="{{ route('keuangan.neraca.export.excel') }}" class="flex items-center gap-3 px-4 py-3 hover:bg-stone-50 text-stone-700 transition-colors border-b border-stone-100">
                <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                </div>
                <div>
                    <p class="text-sm font-bold">Excel</p>
                    <p class="text-[10px] text-stone-500">Format .xlsx</p>
                </div>
            </a>

            <a href="{{ route('keuangan.neraca.export.pdf') }}" class="flex items-center gap-3 px-4 py-3 hover:bg-stone-50 text-stone-700 transition-colors" target="_blank">
                <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <p class="text-sm font-bold">PDF</p>
                    <p class="text-[10px] text-stone-500">Format Dokumen</p>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="space-y-8">

    {{-- Header Dokumen Formal --}}
    <div class="bg-white border border-stone-200 rounded-2xl shadow-sm overflow-hidden relative">
        <div class="absolute top-0 inset-x-0 h-1 bg-[#043d2e]"></div>
        
        <div class="bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] bg-[#043d2e] px-8 py-10 text-center relative overflow-hidden">
            <!-- Decorative Elements -->
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
                
                <h2 class="text-xl md:text-2xl font-serif font-bold text-white uppercase tracking-[0.2em] mb-2">NERACA</h2>
                <p class="text-stone-300 text-sm font-medium">Per {{ \Carbon\Carbon::parse($neraca['tanggal'])->translatedFormat('d F Y') }}</p>
            </div>
        </div>

        <div class="p-6 md:p-10">
            {{-- ============================================================ --}}
            {{-- KOLOM DUA: AKTIVA (Kiri) | PASIVA (Kanan)                    --}}
            {{-- ============================================================ --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-12 gap-y-10">

                {{-- ======================== --}}
                {{--  AKTIVA (ASET)           --}}
                {{-- ======================== --}}
                <div class="flex flex-col h-full">
                    <div class="flex items-center gap-3 border-b-2 border-[#043d2e] pb-3 mb-6">
                        <div class="w-8 h-8 rounded-lg bg-[#043d2e]/10 flex items-center justify-center">
                            <svg class="w-5 h-5 text-[#043d2e]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <h3 class="text-lg font-black text-stone-800 uppercase tracking-widest">A K T I V A</h3>
                    </div>

                    <div class="flex-grow space-y-6">
                        <div>
                            <div class="bg-stone-50 px-3 py-2 rounded-lg mb-3 border border-stone-100">
                                <p class="text-[11px] font-black text-stone-500 uppercase tracking-widest">Aktiva Lancar</p>
                            </div>

                            <div class="space-y-1 px-1">
                                {{-- Kas --}}
                                <div class="group flex justify-between items-center py-2.5 px-3 hover:bg-stone-50 rounded-xl transition-colors border-b border-dashed border-stone-200">
                                    <div class="flex items-center gap-3">
                                        <div class="w-1.5 h-1.5 rounded-full bg-stone-300 group-hover:bg-[#043d2e] transition-colors"></div>
                                        <span class="text-sm font-medium text-stone-700">Kas & Setara Kas</span>
                                    </div>
                                    <span class="text-[14px] font-mono font-black text-stone-800">{{ format_rupiah($neraca['aktiva']['kas']) }}</span>
                                </div>

                                {{-- Piutang --}}
                                <div class="group py-2.5 px-3 hover:bg-stone-50 rounded-xl transition-colors border-b border-dashed border-stone-200">
                                    <div class="flex justify-between items-start mb-1">
                                        <div class="flex items-center gap-3">
                                            <div class="w-1.5 h-1.5 rounded-full bg-stone-300 group-hover:bg-[#043d2e] transition-colors mt-1.5"></div>
                                            <span class="text-sm font-medium text-stone-700">Piutang Pinjaman Anggota</span>
                                        </div>
                                        <span class="text-[14px] font-mono font-black text-stone-800">{{ format_rupiah($neraca['aktiva']['piutang_pinjaman']) }}</span>
                                    </div>
                                    <p class="text-[11px] text-stone-500 pl-4 ml-0.5">
                                        Total: {{ format_rupiah($neraca['aktiva']['piutang_detail']['total_pokok']) }} − Terbayar: {{ format_rupiah($neraca['aktiva']['piutang_detail']['pokok_terbayar']) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Total Aktiva --}}
                    <div class="mt-8 bg-[#043d2e] text-white p-5 rounded-2xl shadow-md border border-[#022a20] relative overflow-hidden">
                        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>
                        <div class="relative z-10 flex justify-between items-center">
                            <span class="text-[13px] font-black uppercase tracking-widest text-emerald-100">TOTAL AKTIVA</span>
                            <span class="text-xl font-mono font-black">{{ format_rupiah($neraca['aktiva']['total']) }}</span>
                        </div>
                    </div>
                </div>

                {{-- ======================== --}}
                {{--  PASIVA                  --}}
                {{-- ======================== --}}
                <div class="flex flex-col h-full">
                    <div class="flex items-center gap-3 border-b-2 border-[#043d2e] pb-3 mb-6">
                        <div class="w-8 h-8 rounded-lg bg-[#043d2e]/10 flex items-center justify-center">
                            <svg class="w-5 h-5 text-[#043d2e]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                        </div>
                        <h3 class="text-lg font-black text-stone-800 uppercase tracking-widest">P A S I V A</h3>
                    </div>

                    <div class="flex-grow space-y-6">
                        {{-- I. Kewajiban --}}
                        <div>
                            <div class="bg-stone-50 px-3 py-2 rounded-lg mb-3 border border-stone-100">
                                <p class="text-[11px] font-black text-stone-500 uppercase tracking-widest">I. Kewajiban (Hutang)</p>
                            </div>

                            <div class="space-y-1 px-1">
                                @foreach($neraca['kewajiban']['simpanan_items'] as $item)
                                <div class="group flex justify-between items-center py-2.5 px-3 hover:bg-stone-50 rounded-xl transition-colors border-b border-dashed border-stone-200">
                                    <div class="flex items-center gap-3">
                                        <div class="w-1.5 h-1.5 rounded-full bg-stone-300 group-hover:bg-[#043d2e] transition-colors"></div>
                                        <span class="text-sm font-medium text-stone-700">{{ $item->nama }}</span>
                                    </div>
                                    <span class="text-[14px] font-mono font-bold text-stone-800">{{ format_rupiah($item->total) }}</span>
                                </div>
                                @endforeach

                                <div class="group flex justify-between items-center py-2.5 px-3 hover:bg-stone-50 rounded-xl transition-colors border-b border-dashed border-stone-200">
                                    <div class="flex items-center gap-3">
                                        <div class="w-1.5 h-1.5 rounded-full bg-stone-300 group-hover:bg-[#043d2e] transition-colors"></div>
                                        <span class="text-sm font-medium text-stone-700">Cadangan Dana Resiko</span>
                                    </div>
                                    <span class="text-[14px] font-mono font-bold text-stone-800">{{ format_rupiah($neraca['kewajiban']['dana_resiko']) }}</span>
                                </div>
                            </div>
                            
                            <div class="flex justify-between items-center py-3 px-3 bg-stone-100/50 rounded-xl mt-3 border border-stone-200 border-dashed">
                                <span class="text-[12px] font-black text-stone-600 uppercase tracking-wider pl-4">Total Kewajiban</span>
                                <span class="text-[14px] font-mono font-black text-stone-800">{{ format_rupiah($neraca['kewajiban']['total']) }}</span>
                            </div>
                        </div>

                        {{-- II. Modal / Ekuitas --}}
                        <div>
                            <div class="bg-stone-50 px-3 py-2 rounded-lg mb-3 border border-stone-100">
                                <p class="text-[11px] font-black text-stone-500 uppercase tracking-widest">II. Modal / Ekuitas</p>
                            </div>

                            <div class="space-y-1 px-1">
                                @foreach($neraca['modal']['simpanan_items'] as $item)
                                <div class="group flex justify-between items-center py-2.5 px-3 hover:bg-stone-50 rounded-xl transition-colors border-b border-dashed border-stone-200">
                                    <div class="flex items-center gap-3">
                                        <div class="w-1.5 h-1.5 rounded-full bg-stone-300 group-hover:bg-[#043d2e] transition-colors"></div>
                                        <span class="text-sm font-medium text-stone-700">{{ $item->nama }}</span>
                                    </div>
                                    <span class="text-[14px] font-mono font-bold text-stone-800">{{ format_rupiah($item->total) }}</span>
                                </div>
                                @endforeach

                                <div class="group py-2.5 px-3 hover:bg-stone-50 rounded-xl transition-colors border-b border-dashed border-stone-200">
                                    <div class="flex justify-between items-start mb-1">
                                        <div class="flex items-center gap-3">
                                            <div class="w-1.5 h-1.5 rounded-full bg-stone-300 group-hover:bg-[#043d2e] transition-colors mt-1.5"></div>
                                            <span class="text-sm font-medium text-stone-700">Laba Ditahan <span class="text-stone-400 font-normal">(SHU Berjalan)</span></span>
                                        </div>
                                        <span class="text-[14px] font-mono font-black {{ $neraca['modal']['laba_ditahan'] >= 0 ? 'text-emerald-700' : 'text-red-600' }}">
                                            {{ format_rupiah($neraca['modal']['laba_ditahan']) }}
                                        </span>
                                    </div>
                                    <p class="text-[11px] text-stone-500 pl-4 ml-0.5">
                                        Pendapatan ({{ format_rupiah($neraca['modal']['detail_laba']['pendapatan_bunga'] + $neraca['modal']['detail_laba']['pendapatan_biaya_admin']) }})
                                        − Beban ({{ format_rupiah($neraca['modal']['detail_laba']['total_beban']) }})
                                    </p>
                                </div>
                            </div>

                            <div class="flex justify-between items-center py-3 px-3 bg-stone-100/50 rounded-xl mt-3 border border-stone-200 border-dashed">
                                <span class="text-[12px] font-black text-stone-600 uppercase tracking-wider pl-4">Total Modal / Ekuitas</span>
                                <span class="text-[14px] font-mono font-black text-stone-800">{{ format_rupiah($neraca['modal']['total']) }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Total Pasiva --}}
                    <div class="mt-8 bg-[#043d2e] text-white p-5 rounded-2xl shadow-md border border-[#022a20] relative overflow-hidden">
                        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>
                        <div class="relative z-10 flex justify-between items-center">
                            <span class="text-[13px] font-black uppercase tracking-widest text-emerald-100">TOTAL PASIVA</span>
                            <span class="text-xl font-mono font-black">{{ format_rupiah($neraca['pasiva']['total']) }}</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- Balance Check Footer --}}
        <div class="border-t-4 {{ $neraca['is_balance'] ? 'border-emerald-500 bg-emerald-50' : 'border-red-500 bg-red-50' }} px-8 py-6 relative overflow-hidden">
            <!-- Decorative Icon Background -->
            <div class="absolute -right-6 -bottom-6 opacity-[0.03] {{ $neraca['is_balance'] ? 'text-emerald-900' : 'text-red-900' }} pointer-events-none">
                <svg class="w-48 h-48" fill="currentColor" viewBox="0 0 24 24">
                    @if($neraca['is_balance'])
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                    @else
                    <path d="M12 2C6.47 2 2 6.47 2 12s4.47 10 10 10 10-4.47 10-10S17.53 2 12 2zm5 13.59L15.59 17 12 13.41 8.41 17 7 15.59 10.59 12 7 8.41 8.41 7 12 10.59 15.59 7 17 8.41 13.41 12 17 15.59z"/>
                    @endif
                </svg>
            </div>

            <div class="flex flex-col md:flex-row items-center justify-between gap-6 relative z-10">
                <div class="flex items-center gap-4">
                    @if($neraca['is_balance'])
                    <div class="w-14 h-14 rounded-2xl bg-emerald-100 flex items-center justify-center border border-emerald-200 shadow-sm shrink-0">
                        <svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                    </div>
                    <div>
                        <p class="text-base font-black text-emerald-800 tracking-wide">NERACA SEIMBANG (BALANCED)</p>
                        <p class="text-sm text-emerald-600/90 font-medium mt-0.5">Total Aktiva dan Pasiva telah sesuai tanpa ada selisih.</p>
                    </div>
                    @else
                    <div class="w-14 h-14 rounded-2xl bg-red-100 flex items-center justify-center border border-red-200 shadow-sm shrink-0 animate-pulse">
                        <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div>
                        <p class="text-base font-black text-red-800 tracking-wide">NERACA TIDAK SEIMBANG</p>
                        <p class="text-sm text-red-600/90 font-medium mt-0.5">Terdapat selisih sebesar <strong class="font-bold text-red-700 bg-red-100 px-1 rounded">{{ format_rupiah($neraca['selisih']) }}</strong> — Perlu investigasi lebih lanjut.</p>
                    </div>
                    @endif
                </div>

                <div class="flex items-center gap-4 text-sm font-mono bg-white/60 p-3 rounded-xl border {{ $neraca['is_balance'] ? 'border-emerald-200' : 'border-red-200' }}">
                    <div class="text-right">
                        <p class="text-[10px] text-stone-500 font-sans font-bold uppercase tracking-widest mb-1">AKTIVA</p>
                        <p class="font-black text-stone-800 text-base">{{ format_rupiah($neraca['aktiva']['total']) }}</p>
                    </div>
                    <div class="w-8 h-8 rounded-full {{ $neraca['is_balance'] ? 'bg-emerald-100 text-emerald-600' : 'bg-red-100 text-red-600' }} flex items-center justify-center font-black text-lg">
                        =
                    </div>
                    <div class="text-left">
                        <p class="text-[10px] text-stone-500 font-sans font-bold uppercase tracking-widest mb-1">PASIVA</p>
                        <p class="font-black text-stone-800 text-base">{{ format_rupiah($neraca['pasiva']['total']) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Catatan Kaki --}}
    <div class="bg-white border border-stone-200 rounded-2xl shadow-sm p-8">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center border border-amber-100">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h4 class="text-sm font-black text-stone-800 uppercase tracking-widest">Catatan atas Laporan Posisi Keuangan</h4>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 text-[13px] text-stone-600 leading-relaxed">
            <div class="space-y-4">
                <div class="flex gap-3">
                    <span class="flex-shrink-0 w-5 h-5 rounded-full bg-stone-100 flex items-center justify-center text-[11px] font-bold text-stone-600 mt-0.5">1</span>
                    <p><strong class="text-stone-800 font-bold">Kas & Setara Kas</strong><br>Saldo liquid yang tersedia di kas koperasi (seluruh dana masuk dikurangi seluruh dana keluar).</p>
                </div>
                <div class="flex gap-3">
                    <span class="flex-shrink-0 w-5 h-5 rounded-full bg-stone-100 flex items-center justify-center text-[11px] font-bold text-stone-600 mt-0.5">2</span>
                    <p><strong class="text-stone-800 font-bold">Piutang Pokok</strong><br>Sisa pokok pinjaman yang berstatus "berjalan" dan belum diangsur oleh anggota (pendapatan bunga belum diakui sebagai aset/piutang).</p>
                </div>
                <div class="flex gap-3">
                    <span class="flex-shrink-0 w-5 h-5 rounded-full bg-stone-100 flex items-center justify-center text-[11px] font-bold text-stone-600 mt-0.5">3</span>
                    <p><strong class="text-stone-800 font-bold">Cadangan Dana Resiko</strong><br>Potongan proporsional (1.5%) dari setiap pencairan pinjaman, dicadangkan untuk menutup risiko kredit macet di masa depan.</p>
                </div>
            </div>
            <div class="space-y-4">
                <div class="flex gap-3">
                    <span class="flex-shrink-0 w-5 h-5 rounded-full bg-stone-100 flex items-center justify-center text-[11px] font-bold text-stone-600 mt-0.5">4</span>
                    <p><strong class="text-stone-800 font-bold">Simpanan Pokok & Wajib</strong><br>Dicatat sebagai modal/ekuitas karena bersifat tetap dan tidak dapat ditarik selama anggota masih aktif di koperasi.</p>
                </div>
                <div class="flex gap-3">
                    <span class="flex-shrink-0 w-5 h-5 rounded-full bg-stone-100 flex items-center justify-center text-[11px] font-bold text-stone-600 mt-0.5">5</span>
                    <p><strong class="text-stone-800 font-bold">Laba Ditahan (SHU Berjalan)</strong><br>Selisih antara total pendapatan terealisasi (bunga pinjaman terbayar + biaya admin) dengan total beban operasional.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Lembar Pengesahan / Tanda Tangan --}}
    <div class="bg-white border border-stone-200 rounded-2xl shadow-sm p-10 relative overflow-hidden">
        {{-- Background Pattern --}}
        <div class="absolute inset-0 opacity-[0.02] pointer-events-none bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
        
        <div class="relative z-10 grid grid-cols-1 md:grid-cols-3 gap-10 text-center text-[14px] text-stone-800">
            <div class="flex flex-col items-center justify-end">
                <p class="font-medium text-stone-500 mb-20">Dibuat oleh,</p>
                <div class="w-48 border-b-2 border-stone-800 pb-1">
                    <p class="font-bold text-stone-800">Bendahara</p>
                </div>
                <p class="text-xs text-stone-500 mt-1 uppercase tracking-wider">Koperasi Tirta Bina Karya</p>
            </div>
            
            <div class="flex flex-col items-center justify-end">
                <p class="font-medium text-stone-500 mb-20">Diperiksa oleh,</p>
                <div class="w-48 border-b-2 border-stone-800 pb-1">
                    <p class="font-bold text-stone-800">Pengawas</p>
                </div>
                <p class="text-xs text-stone-500 mt-1 uppercase tracking-wider">Koperasi Tirta Bina Karya</p>
            </div>
            
            <div class="flex flex-col items-center justify-end">
                <p class="text-xs text-stone-500 mb-1 text-right w-full pr-6">Pekanbaru, {{ now()->translatedFormat('d F Y') }}</p>
                <p class="font-medium text-stone-500 mb-16">Disetujui oleh,</p>
                <div class="w-48 border-b-2 border-stone-800 pb-1">
                    <p class="font-bold text-stone-800">Ketua Koperasi</p>
                </div>
                <p class="text-xs text-stone-500 mt-1 uppercase tracking-wider">Koperasi Tirta Bina Karya</p>
            </div>
        </div>
    </div>

</div>
@endsection
