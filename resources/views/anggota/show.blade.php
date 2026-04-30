@extends('layouts.app')

@section('title', 'Profil Anggota')
@section('subtitle', 'Detail Informasi & Finansial Anggota Koperasi')

@section('actions')
<a href="{{ route('anggota.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-stone-200 text-stone-600 rounded-xl text-sm font-medium hover:bg-stone-50 transition-colors shadow-sm">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
    </svg>
    Kembali
</a>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 max-w-7xl">

    {{-- KIRI: Biodata Profil --}}
    <div class="lg:col-span-1 space-y-6">
        <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden flex flex-col">
            <div class="bg-gradient-to-br from-[#043d2e] to-emerald-900 px-6 py-8 text-center relative overflow-hidden">
                <div class="absolute inset-0 bg-black/10 opacity-20"></div>
                <div class="w-20 h-20 mx-auto bg-white/20 backdrop-blur-md rounded-full flex items-center justify-center text-3xl font-bold text-white border border-white/40 mb-4 shadow-xl">
                    {{ strtoupper(substr($anggota->nama, 0, 1)) }}
                </div>
                <h2 class="text-xl font-bold text-white relative z-0">{{ $anggota->nama }}</h2>
                <div class="text-emerald-100 text-sm mt-1.5 relative z-0 font-mono tracking-wide">
                    NIP. <x-nip-display :value="$anggota->nip" />
                </div>
                
                <div class="mt-4 relative z-0">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold {{ $anggota->status->value === 'aktif' ? 'bg-emerald-400 text-emerald-950' : 'bg-stone-400 text-stone-900' }} border border-white/20 uppercase tracking-widest shadow-sm">
                        {{ $anggota->status->value }}
                    </span>
                </div>
            </div>
            
            <div class="p-6 flex-1 flex flex-col">
                <h3 class="text-[11px] font-bold text-stone-400 uppercase tracking-wider mb-4 border-b border-stone-100 pb-2">Informasi Jabatan</h3>
                <dl class="space-y-4 text-sm flex-1">
                    <div>
                        <dt class="text-stone-500 text-xs mb-1">Golongan ASN</dt>
                        <dd class="font-medium text-stone-800">
                            @if($anggota->golongan_asn)
                                @php $golColor = $anggota->golongan_asn->color(); @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md bg-{{ $golColor }}-50 text-{{ $golColor }}-700 text-xs font-bold border border-{{ $golColor }}-200 uppercase tracking-wider shadow-sm">
                                    {{ $anggota->golongan_asn->label() }}
                                </span>
                            @else
                                <span class="text-stone-400 italic">Belum diset</span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-stone-500 text-xs mb-1">Bidang / Unit Kerja</dt>
                        <dd class="font-medium text-stone-800">
                            @if($anggota->bidang)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-stone-100 text-stone-700 text-xs font-bold uppercase tracking-wider border border-stone-200">
                                    {{ $anggota->bidang->nama_bidang }}
                                </span>
                            @else
                                <span class="text-stone-400 italic">Belum diset</span>
                            @endif
                        </dd>
                    </div>
                </dl>

                <h3 class="text-[11px] font-bold text-stone-400 uppercase tracking-wider mt-6 mb-4 border-b border-stone-100 pb-2">Kontak Internal</h3>
                <dl class="space-y-4 text-sm">
                    <div>
                        <dt class="text-stone-500 text-xs mb-1">No. Handphone (WA)</dt>
                        <dd class="font-medium text-stone-800">{{ $anggota->no_hp ?? '-' }}</dd>
                    </div>
                </dl>
                
                <div class="mt-8 pt-2">
                    <a href="{{ route('anggota.edit', $anggota->id) }}" class="w-full text-center flex items-center justify-center gap-2 bg-stone-50 border border-stone-200 rounded-xl px-4 py-2.5 text-sm font-bold text-stone-600 hover:bg-stone-100 hover:text-stone-900 transition-colors shadow-sm active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        Edit Data Anggota
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- KANAN: Rekapitulasi Keuangan --}}
    <div class="lg:col-span-2 space-y-6">
        
        {{-- Card: Total Simpanan --}}
        <div class="bg-white rounded-2xl border border-stone-200 shadow-sm p-6 overflow-hidden relative group hover:border-[#043d2e]/30 transition-colors">
            <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-50 rounded-bl-full opacity-50 -z-0"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-100/50 border border-emerald-200 flex items-center justify-center text-[#043d2e]">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <h3 class="font-bold text-stone-800 tracking-tight">Total Simpanan Keseluruhan</h3>
                    </div>
                    <span class="text-2xl font-black font-mono text-[#043d2e]">{{ format_rupiah($totalSimpanan) }}</span>
                </div>
                
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4">
                    @forelse($simpananPerJenis as $nama_jenis => $nominal)
                    <div class="bg-stone-50 p-4 rounded-xl border border-stone-100 flex flex-col justify-between h-full">
                        <span class="text-xs text-stone-500 font-bold uppercase tracking-wider mb-2 line-clamp-1">{{ $nama_jenis }}</span>
                        <span class="font-mono font-bold text-stone-800 text-sm shadow-sm">{{ format_rupiah($nominal) }}</span>
                    </div>
                    @empty
                    <div class="col-span-full py-8 text-center text-sm font-medium text-stone-500 bg-stone-50 rounded-xl border border-stone-200 border-dashed">
                        Belum ada riwayat simpanan tercatat.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Card: Sisa Hutang & Pinjaman --}}
        <div class="bg-white rounded-2xl border border-stone-200 shadow-sm p-6 overflow-hidden relative group hover:border-[#043d2e]/30 transition-colors">
            <div class="absolute top-0 right-0 w-32 h-32 bg-stone-100 rounded-bl-full opacity-50 -z-0"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-stone-100 border border-stone-200 flex items-center justify-center text-stone-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <h3 class="font-bold text-stone-800 tracking-tight">Sisa Utang Aktif (Piutang)</h3>
                    </div>
                    <span class="text-2xl font-black font-mono text-stone-700">{{ format_rupiah($sisaUtang) }}</span>
                </div>
                
                @if($pinjamanAktif->count() > 0)
                <div class="space-y-4">
                    @foreach($pinjamanAktif as $pinj)
                    @php
                        $sudahDibayar = $pinj->angsuran->filter(fn($a) => $a->status->value === 'lunas')->sum('nominal_total');
                        $progress = $pinj->total_bayar > 0 ? ($sudahDibayar / $pinj->total_bayar) * 100 : 0;
                        $sisa = $pinj->total_bayar - $sudahDibayar;
                    @endphp
                    <div class="bg-white p-5 rounded-2xl border border-stone-200 shadow-sm">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h4 class="text-sm font-bold text-stone-800 flex items-center gap-2">
                                    Ref: <span class="font-mono text-stone-600 font-medium">{{ $pinj->no_referensi }}</span>
                                    <a href="{{ route('pinjaman.show', $pinj->id) }}" class="text-xs font-bold text-[#043d2e] hover:text-[#043d2e]/80 transition-colors" title="Lihat Detail Pinjaman">
                                        Lihat Detail
                                    </a>
                                </h4>
                                <div class="mt-1.5 flex items-center gap-2">
                                    <span class="text-xs font-bold uppercase tracking-wider px-2 py-0.5 rounded {{ $pinj->status->value === 'berjalan' ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-stone-100 text-stone-600 border border-stone-200' }}">{{ $pinj->status->value }}</span>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] text-stone-400 font-bold uppercase tracking-wider mb-0.5">Sisa Tagihan</p>
                                <p class="font-mono text-base font-black text-stone-800">{{ format_rupiah($sisa) }}</p>
                            </div>
                        </div>
                        
                        {{-- Progress Bar --}}
                        <div class="mt-4 pt-4 border-t border-stone-100">
                            <div class="flex justify-between text-xs font-medium text-stone-500 mb-2">
                                <span>Dibayar: <span class="font-mono text-stone-700">{{ format_rupiah($sudahDibayar) }}</span></span>
                                <span>Total Tagihan: <span class="font-mono text-stone-700">{{ format_rupiah($pinj->total_bayar) }}</span></span>
                            </div>
                            <div class="w-full bg-stone-100 rounded-full h-2 overflow-hidden">
                                <div class="bg-[#043d2e] h-2 rounded-full transition-all duration-500" style="width: {{ $progress }}%"></div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="w-full py-8 text-center text-sm font-medium text-stone-500 bg-stone-50 rounded-xl border border-stone-200 border-dashed">
                    Tidak ada tunggakan atau pinjaman aktif.
                </div>
                @endif
            </div>
        </div>

        {{-- Button Proses Keluar --}}
        @if($anggota->status->value === 'aktif')
        <div class="bg-white rounded-2xl border border-red-200 shadow-sm p-6 overflow-hidden relative">
            <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h3 class="font-bold text-red-700 text-sm tracking-tight">Proses Keluarkan Anggota</h3>
                    <p class="text-[13px] text-stone-500 mt-1">Analisis kelayakan & pengembalian simpanan sebelum proses keluar</p>
                </div>
                <a href="{{ route('anggota.keluar', $anggota) }}" class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-bold rounded-xl transition-all shadow-sm active:scale-95 text-center flex-shrink-0 whitespace-nowrap">
                    Proses Keluar →
                </a>
            </div>
        </div>
        @endif

        {{-- Arsip Keluar Sebelumnya --}}
        @if(isset($arsipKeluar) && $arsipKeluar->isNotEmpty())
        <div class="bg-white rounded-2xl border border-amber-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-amber-50 border-b border-amber-200 flex items-center gap-2">
                <h3 class="text-sm font-bold text-amber-800 tracking-tight">Riwayat Keluar dari Koperasi</h3>
            </div>
            <div class="p-6 space-y-4">
                @foreach($arsipKeluar as $arsip)
                <div class="bg-white border border-stone-200 rounded-xl p-5 shadow-sm">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div class="bg-stone-50 p-3 rounded-lg border border-stone-100">
                            <span class="text-stone-500 text-[10px] font-bold uppercase tracking-wider block mb-1">Tanggal Keluar</span>
                            <p class="font-semibold text-stone-800">{{ $arsip->tanggal_keluar->translatedFormat('d F Y') }}</p>
                        </div>
                        <div class="bg-stone-50 p-3 rounded-lg border border-stone-100">
                            <span class="text-stone-500 text-[10px] font-bold uppercase tracking-wider block mb-1">Total Dikembalikan</span>
                            <p class="font-mono font-bold text-stone-800">{{ format_rupiah($arsip->total_simpanan_dikembalikan) }}</p>
                        </div>
                        
                        <div class="col-span-2 border-t border-stone-100 pt-3">
                            <span class="text-amber-800 text-[11px] font-bold uppercase tracking-wider block mb-1">Wajib Setor Jika Daftar Ulang</span>
                            <p class="font-mono font-black text-amber-600 text-lg">{{ format_rupiah($arsip->nominal_wajib_setor_ulang) }}</p>
                        </div>
                        
                        @if($arsip->rincian_simpanan)
                        <div class="col-span-2 pt-2">
                            <span class="text-stone-500 text-[11px] font-bold uppercase tracking-wider block mb-2">Rincian Pengembalian</span>
                            <div class="flex flex-wrap gap-2">
                                @foreach($arsip->rincian_simpanan as $r)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-stone-50 border border-stone-200 rounded-lg text-xs">
                                    <span class="text-stone-600 font-medium">{{ $r['nama'] }}:</span>
                                    <span class="font-mono font-bold text-stone-800">{{ format_rupiah($r['nominal']) }}</span>
                                </span>
                                @endforeach
                            </div>
                        </div>
                        @endif
                        
                        @if($arsip->catatan)
                        <div class="col-span-2 pt-2">
                            <span class="text-stone-500 text-[11px] font-bold uppercase tracking-wider block mb-1">Catatan</span>
                            <p class="text-stone-700 text-sm leading-relaxed p-3 bg-stone-50 border border-stone-100 rounded-lg">{{ $arsip->catatan }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>
</div>
@endsection
