@extends('layouts.app')

@section('title', 'Profil Anggota')
@section('subtitle', 'Detail Informasi & Rekapitulasi Finansial')

@section('actions')
<a href="{{ route('anggota.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-stone-200 text-stone-600 rounded-xl text-sm font-bold hover:bg-stone-50 transition-all hover:-translate-x-1 shadow-sm">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
    </svg>
    Kembali ke Daftar
</a>
@endsection

@section('content')
<div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-6">

    {{-- KIRI: Informasi Biodata Ringkas (Span 4) --}}
    <div class="lg:col-span-4 space-y-6">
        <div class="bg-white rounded-3xl border border-stone-200 shadow-sm overflow-hidden flex flex-col h-full sticky top-32">
            <div class="p-6 md:p-8 flex flex-col items-start gap-4 border-b border-stone-100 bg-stone-50/50">
                <div class="w-14 h-14 bg-[#043d2e]/10 text-[#043d2e] rounded-2xl flex items-center justify-center shrink-0 border border-[#043d2e]/20">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <div>
                    <h1 class="text-xl font-black text-stone-900 tracking-tight leading-tight">{{ $anggota->nama }}</h1>
                    <div class="flex flex-wrap items-center gap-2 mt-2.5">
                        <span class="inline-flex items-center gap-1.5 text-stone-600 font-mono text-[11px] font-bold bg-white px-2.5 py-1 rounded-lg border border-stone-200 shadow-sm">
                            NIP. <x-nip-display :value="$anggota->nip" />
                        </span>
                        @if($anggota->status->value === 'aktif')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-bold uppercase tracking-wider shadow-sm">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-stone-100 text-stone-600 border border-stone-200 text-[10px] font-bold uppercase tracking-wider shadow-sm">
                                Non-Aktif
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="p-6 md:p-8 flex-1 flex flex-col">
                <div class="space-y-6 flex-1">
                    <div class="flex flex-col gap-2">
                        <span class="text-[10px] font-bold text-stone-400 uppercase tracking-wider flex items-center gap-2">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            Bidang / Unit Kerja
                        </span>
                        @if($anggota->bidang)
                            <span class="text-sm font-bold text-stone-800">{{ $anggota->bidang->nama_bidang }}</span>
                        @else
                            <span class="text-stone-400 italic text-sm font-medium">Belum diset</span>
                        @endif
                    </div>

                    <div class="flex flex-col gap-2">
                        <span class="text-[10px] font-bold text-stone-400 uppercase tracking-wider flex items-center gap-2">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Golongan ASN
                        </span>
                        @if($anggota->golongan_asn)
                            @php $golColor = $anggota->golongan_asn->color(); @endphp
                            <div>
                                <span class="inline-flex text-[11px] items-center gap-1.5 px-2.5 py-1 rounded-lg bg-{{ $golColor }}-50 text-{{ $golColor }}-700 font-bold border border-{{ $golColor }}-200 uppercase tracking-wider">
                                    {{ $anggota->golongan_asn->label() }}
                                </span>
                            </div>
                        @else
                            <span class="text-stone-400 italic text-sm font-medium">Belum diset</span>
                        @endif
                    </div>

                    <div class="flex flex-col gap-2">
                        <span class="text-[10px] font-bold text-stone-400 uppercase tracking-wider flex items-center gap-2">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            Kontak Handphone
                        </span>
                        @if($anggota->no_hp)
                        <span class="text-sm font-bold font-mono text-[#043d2e] bg-emerald-50 px-3 py-1.5 rounded-xl border border-emerald-100 self-start">
                            {{ $anggota->no_hp }}
                        </span>
                        @else
                        <span class="text-stone-400 italic text-sm font-medium">Belum ada No HP</span>
                        @endif
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-stone-100">
                    <a href="{{ route('anggota.edit', $anggota->id) }}" class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 bg-stone-50 border border-stone-200 hover:border-stone-300 text-stone-700 hover:bg-white hover:text-stone-900 font-bold rounded-xl transition-all shadow-sm active:scale-95 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        Edit Biodata
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- KANAN: Rekapitulasi Keuangan (Span 8) --}}
    <div class="lg:col-span-8 space-y-6">
        
        {{-- Kartu: Total Simpanan (Premium Glassmorphism Style) --}}
        <div class="relative bg-gradient-to-br from-[#043d2e] to-[#01251b] rounded-3xl shadow-xl overflow-hidden group">
            <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI4NiIgaGVpZ2h0PSI4NiI+CjxyZWN0IHdpZHRoPSI4NiIgaGVpZ2h0PSI4NiIgeD0iMCIgeT0iMCIgZmlsbD0idHJhbnNwYXJlbnQiPjwvcmVjdD4KPGNpcmNsZSBjeD0iNDMiIGN5PSI0MyIgcj0iNDMiIGZpbGw9InJnYmEoMjU1LDI1NSwyNTUsMC4wMSkiPjwvY2lyY2xlPgo8L3N2Zz4=')] opacity-30 mix-blend-overlay pointer-events-none"></div>
            <div class="absolute -right-20 -top-20 w-80 h-80 bg-emerald-500 rounded-full blur-3xl opacity-10 group-hover:opacity-20 transition-opacity duration-700 pointer-events-none"></div>
            
            <div class="p-8 relative z-10 flex flex-col h-full justify-between">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-12 h-12 rounded-2xl bg-white/10 border border-white/20 flex items-center justify-center text-emerald-300 backdrop-blur-md">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <h3 class="font-bold text-white text-lg tracking-tight">Total Simpanan Dimiliki</h3>
                    </div>
                </div>
                
                <div class="mt-6 mb-8">
                    <span class="text-4xl lg:text-5xl font-black font-mono text-transparent bg-clip-text bg-gradient-to-br from-white to-emerald-200 tracking-tight drop-shadow-sm">
                        {{ format_rupiah($totalSimpanan) }}
                    </span>
                </div>

                {{-- Breakdowns --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    @forelse($simpananPerJenis as $nama_jenis => $nominal)
                    <div class="bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl p-4 hover:bg-white/10 transition-colors">
                        <span class="text-[10px] text-emerald-200/80 font-bold uppercase tracking-wider block mb-1.5 line-clamp-1">{{ $nama_jenis }}</span>
                        <span class="font-mono font-bold text-white text-base shadow-sm block">{{ format_rupiah($nominal) }}</span>
                    </div>
                    @empty
                    <div class="col-span-full py-6 text-center text-sm font-medium text-emerald-200/60 bg-white/5 rounded-2xl border border-white/10 border-dashed backdrop-blur-sm">
                        Belum ada riwayat simpanan tercatat.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Kartu: Total Utang (Light Modern Style) --}}
        <div class="bg-white rounded-3xl border border-stone-200 shadow-sm overflow-hidden relative group hover:border-[#043d2e]/30 transition-all duration-300">
            <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-rose-50 rounded-full blur-3xl opacity-50 group-hover:opacity-100 transition-opacity duration-700 pointer-events-none"></div>
            
            <div class="p-8 relative z-10 flex flex-col h-full">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-12 h-12 rounded-2xl bg-stone-100 border border-stone-200 flex items-center justify-center text-stone-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <h3 class="font-bold text-stone-800 text-lg tracking-tight">Sisa Utang Aktif (Piutang)</h3>
                </div>
                
                <div class="mt-6 mb-8">
                    <span class="text-4xl lg:text-5xl font-black font-mono text-stone-800 tracking-tight drop-shadow-sm">
                        {{ format_rupiah($sisaUtang) }}
                    </span>
                </div>

                {{-- Pinjaman Items Stack --}}
                <div class="mt-auto space-y-3">
                    @if($pinjamanAktif->count() > 0)
                        @foreach($pinjamanAktif as $pinj)
                        @php
                            $sudahDibayar = $pinj->angsuran->filter(fn($a) => $a->status->value === 'lunas')->sum('nominal_total');
                            $progress = $pinj->total_bayar > 0 ? ($sudahDibayar / $pinj->total_bayar) * 100 : 0;
                            $sisa = $pinj->total_bayar - $sudahDibayar;
                        @endphp
                        <div class="bg-stone-50 border border-stone-200/80 p-4 rounded-2xl hover:bg-stone-100 transition-colors">
                            <div class="flex flex-wrap justify-between items-center gap-3 mb-3">
                                <div>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="text-sm font-bold text-stone-800 font-mono">{{ $pinj->no_referensi }}</span>
                                        <span class="text-[9px] font-bold uppercase tracking-wider px-1.5 py-0.5 rounded {{ $pinj->status->value === 'berjalan' ? 'bg-blue-100 text-blue-700' : 'bg-stone-200 text-stone-700' }}">{{ $pinj->status->value }}</span>
                                    </div>
                                </div>
                                <a href="{{ route('pinjaman.show', $pinj->id) }}" class="text-[11px] font-bold text-[#043d2e] hover:text-[#043d2e]/70 bg-emerald-50 px-3 py-1.5 rounded-lg border border-emerald-100 shadow-sm transition-colors shrink-0">
                                    Lihat Detail &rarr;
                                </a>
                            </div>
                            
                            {{-- Unified Progress --}}
                            <div>
                                <div class="flex justify-between text-[10px] sm:text-xs font-bold text-stone-500 uppercase tracking-wider mb-2">
                                    <span>Telah Dibayar: {{ format_rupiah($sudahDibayar) }}</span>
                                    <span class="text-stone-800">Sisa: {{ format_rupiah($sisa) }}</span>
                                </div>
                                <div class="w-full bg-stone-200 rounded-full h-2 overflow-hidden">
                                    <div class="bg-[#043d2e] h-2 rounded-full transition-all duration-1000" style="width: {{ $progress }}%"></div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="w-full py-6 text-center text-sm font-bold text-stone-400 bg-stone-50 rounded-2xl border border-stone-200 border-dashed">
                            Tidak ada hutang pinjaman aktif
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        {{-- BOTTOM DANGER / HISTORY SECTION --}}
        @if($anggota->status->value === 'aktif' || (isset($arsipKeluar) && $arsipKeluar->isNotEmpty()))
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            {{-- Proses Keluar (Only if Active) --}}
            @if($anggota->status->value === 'aktif')
            <div class="bg-white rounded-3xl border border-rose-200 shadow-sm p-6 sm:p-8 flex flex-col justify-center items-center text-center relative overflow-hidden group hover:border-rose-300 transition-colors {{ !isset($arsipKeluar) || $arsipKeluar->isEmpty() ? 'md:col-span-2' : '' }}">
                <div class="absolute inset-0 bg-gradient-to-b from-white to-rose-50/30"></div>
                <div class="relative z-10 w-full flex flex-col items-center">
                    <div class="w-14 h-14 bg-white text-rose-500 rounded-2xl flex items-center justify-center mb-5 border border-rose-100 shadow-sm group-hover:-translate-y-1 transition-transform">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </div>
                    <h3 class="text-xl font-black text-stone-900 mb-2">Keluarkan Anggota</h3>
                    <p class="text-[13px] text-stone-500 mb-6 max-w-xs">Jalankan proses multi-step untuk menganalisis kelayakan keluar & kalkulasi pengembalian.</p>
                    <a href="{{ route('anggota.keluar', $anggota) }}" class="inline-flex items-center gap-2 px-6 py-3 bg-white text-rose-600 border-2 border-rose-200 hover:border-rose-600 hover:bg-rose-50 font-bold rounded-xl transition-all shadow-sm active:scale-95 w-full justify-center hover:shadow-md text-sm">
                        Mulai Proses Keluar
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>
            </div>
            @endif

            {{-- Arsip Keluar Sebelumnya --}}
            @if(isset($arsipKeluar) && $arsipKeluar->isNotEmpty())
            <div class="bg-white rounded-3xl border border-amber-200 shadow-sm overflow-hidden flex flex-col {{ $anggota->status->value !== 'aktif' ? 'md:col-span-2' : '' }}">
                <div class="px-6 py-5 bg-gradient-to-r from-amber-50 to-white border-b border-amber-100 flex items-center gap-3 shrink-0">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center border border-amber-200 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-amber-900 tracking-tight">Riwayat Berhenti / Keluar</h3>
                        <p class="text-[10px] font-bold text-amber-600 uppercase tracking-widest mt-0.5">Arsip Keluar Sebelumnya</p>
                    </div>
                </div>
                <div class="p-6 space-y-4 max-h-[300px] overflow-y-auto custom-scrollbar bg-stone-50/50 flex-1">
                    @foreach($arsipKeluar as $arsip)
                    <div class="border border-stone-200 rounded-2xl p-5 hover:border-amber-300 transition-colors bg-white shadow-sm">
                        <div class="flex flex-wrap justify-between items-start gap-4 mb-4 pb-4 border-b border-stone-100">
                            <div>
                                <span class="text-[10px] font-bold text-stone-400 uppercase tracking-wider block mb-1">Tanggal Keluar</span>
                                <div class="font-bold text-stone-800 text-xs bg-stone-100 px-2.5 py-1 rounded-lg inline-block">{{ $arsip->tanggal_keluar->translatedFormat('d F Y') }}</div>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] font-bold text-stone-400 uppercase tracking-wider block mb-1">Total Dikembalikan</span>
                                <div class="font-black font-mono text-[#043d2e] text-base">{{ format_rupiah($arsip->total_simpanan_dikembalikan) }}</div>
                            </div>
                        </div>
                        
                        <div class="bg-amber-50 rounded-xl p-4 border border-amber-100 mb-4 flex flex-col xl:flex-row xl:items-center justify-between gap-2">
                            <div class="flex items-center gap-2">
                                <div class="p-1 px-1.5 bg-white rounded text-amber-600 shadow-sm shrink-0">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <span class="text-[10px] font-bold text-amber-800 uppercase tracking-wider">Wajib Setor Daftar Ulang</span>
                            </div>
                            <span class="font-black font-mono text-amber-700 text-base xl:text-lg">{{ format_rupiah($arsip->nominal_wajib_setor_ulang) }}</span>
                        </div>

                        @if($arsip->catatan)
                        <div class="relative">
                            <svg class="absolute top-0 left-0 w-5 h-5 text-stone-200 -translate-x-2 -translate-y-2 transform" fill="currentColor" viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/></svg>
                            <p class="text-[13px] text-stone-600 bg-stone-50 border border-stone-100 p-3 pl-6 rounded-xl italic relative z-10">{{ $arsip->catatan }}</p>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
        @endif
        
    </div>
</div>
@endsection
