@extends('layouts.app')

@section('title', 'Profil Anggota')
@section('subtitle', 'Detail Informasi & Finansial Anggota Koperasi')

@section('actions')
<a href="{{ route('anggota.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-lg text-sm font-medium hover:bg-slate-50 transition-colors">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
    </svg>
    Kembali
</a>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- KIRI: Biodata Profil --}}
    <div class="lg:col-span-1 space-y-6">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-blue-800 px-6 py-8 text-center relative overflow-hidden">
                <div class="absolute inset-0 bg-white/10 opacity-20"></div>
                <div class="w-20 h-20 mx-auto bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center text-3xl font-bold text-white border-2 border-white/50 mb-4 shadow-lg">
                    {{ strtoupper(substr($anggota->nama, 0, 1)) }}
                </div>
                <h2 class="text-xl font-bold text-white relative z-10">{{ $anggota->nama }}</h2>
                <p class="text-blue-100 text-sm mt-1 relative z-10">NIP: {{ $anggota->nip }}</p>
                
                <div class="mt-4 relative z-10">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $anggota->status->value === 'aktif' ? 'bg-emerald-400 text-emerald-900' : 'bg-red-400 text-red-900' }} border border-white/20 capitalize shadow-sm">
                        {{ $anggota->status->value }}
                    </span>
                </div>
            </div>
            
            <div class="p-6">
                <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-4 border-b border-slate-100 pb-2">Informasi Jabatan</h3>
                <dl class="space-y-4 text-sm">
                    <div>
                        <dt class="text-slate-500 text-xs mb-1">Golongan</dt>
                        <dd class="font-medium text-slate-800">{{ $anggota->golongan ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500 text-xs mb-1">Jabatan / Pekerjaan</dt>
                        <dd class="font-medium text-slate-800">{{ $anggota->jabatan ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500 text-xs mb-1">Bidang / Unit Kerja</dt>
                        <dd class="font-medium text-slate-800">
                            @if($anggota->bidang)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-blue-50 text-blue-700 text-xs font-medium border border-blue-100">
                                    {{ $anggota->bidang->nama_bidang }}
                                </span>
                            @else
                                <span class="text-slate-400 italic">Belum diet</span>
                            @endif
                        </dd>
                    </div>
                </dl>

                <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mt-6 mb-4 border-b border-slate-100 pb-2">Kontak & Alamat</h3>
                <dl class="space-y-4 text-sm">
                    <div>
                        <dt class="text-slate-500 text-xs mb-1">No. Handphone</dt>
                        <dd class="font-medium text-slate-800">{{ $anggota->no_hp ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500 text-xs mb-1">Alamat Domisili</dt>
                        <dd class="font-medium text-slate-800 leading-relaxed">{{ $anggota->alamat ?? '-' }}</dd>
                    </div>
                </dl>
                
                <div class="mt-8">
                    <a href="{{ route('anggota.edit', $anggota->id) }}" class="w-full text-center inline-block bg-white border border-slate-300 rounded-xl px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors focus:ring-2 focus:ring-slate-200">
                        Edit Data Anggota
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- KANAN: Rekapitulasi Keuangan --}}
    <div class="lg:col-span-2 space-y-6">
        
        {{-- Card: Total Simpanan --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 overflow-hidden relative group hover:shadow-md transition-shadow">
            <div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 rounded-full bg-violet-50 opacity-50 group-hover:bg-violet-100 transition-colors z-0"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-violet-100 flex items-center justify-center text-violet-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <h3 class="font-bold text-slate-800">Total Simpanan Keseluruhan</h3>
                    </div>
                    <span class="text-2xl font-bold font-mono text-violet-700">{{ format_rupiah($totalSimpanan) }}</span>
                </div>
                
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    @forelse($simpananPerJenis as $nama_jenis => $nominal)
                    <div class="bg-slate-50 p-3 rounded-lg border border-slate-100 flex flex-col justify-between h-full">
                        <span class="text-xs text-slate-500 font-medium mb-1 line-clamp-1">{{ $nama_jenis }}</span>
                        <span class="font-mono font-bold text-slate-800 text-sm shadow-sm">{{ format_rupiah($nominal) }}</span>
                    </div>
                    @empty
                    <div class="col-span-full py-4 text-center text-sm text-slate-500 bg-slate-50 rounded-lg border border-slate-100 border-dashed">
                        Belum ada riwayat simpanan.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Card: Sisa Hutang & Pinjaman --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 overflow-hidden relative group hover:shadow-md transition-shadow">
            <div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 rounded-full bg-amber-50 opacity-50 group-hover:bg-amber-100 transition-colors z-0"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center text-amber-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <h3 class="font-bold text-slate-800">Sisa Utang Aktif (Piutang)</h3>
                    </div>
                    <span class="text-2xl font-bold font-mono text-amber-600">{{ format_rupiah($sisaUtang) }}</span>
                </div>
                
                @if($pinjamanAktif->count() > 0)
                <div class="space-y-4">
                    @foreach($pinjamanAktif as $pinj)
                    @php
                        $sudahDibayar = $pinj->angsuran->filter(fn($a) => $a->status->value === 'lunas')->sum('nominal_total');
                        $progress = $pinj->total_bayar > 0 ? ($sudahDibayar / $pinj->total_bayar) * 100 : 0;
                        $sisa = $pinj->total_bayar - $sudahDibayar;
                    @endphp
                    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h4 class="text-sm font-bold text-slate-700 flex items-center gap-2">
                                    Ref: {{ $pinj->no_referensi }}
                                    <a href="{{ route('pinjaman.show', $pinj->id) }}" class="text-xs font-semibold text-blue-600 hover:text-blue-800 transition-colors" title="Lihat Detail Pinjaman">
                                        [Detail]
                                    </a>
                                </h4>
                                <p class="text-xs text-slate-500 mt-1">Status: <span class="uppercase tracking-wider font-semibold text-amber-600">{{ $pinj->status->value }}</span></p>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] text-slate-400 uppercase tracking-wider font-semibold">Tersisa</p>
                                <p class="font-mono text-sm font-bold text-slate-800">{{ format_rupiah($sisa) }}</p>
                            </div>
                        </div>
                        
                        {{-- Progress Bar --}}
                        <div class="mt-3">
                            <div class="flex justify-between text-[11px] mb-1 font-medium text-slate-500">
                                <span>Dibayar: {{ format_rupiah($sudahDibayar) }}</span>
                                <span>Total Tagihan: {{ format_rupiah($pinj->total_bayar) }}</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden ring-1 ring-inset ring-slate-200/50">
                                <div class="bg-gradient-to-r from-amber-400 to-amber-500 h-2.5 rounded-full transition-all duration-500" style="width: {{ $progress }}%"></div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="w-full py-6 text-center text-sm text-slate-500 bg-slate-50 rounded-xl border border-slate-100 border-dashed">
                    Tidak ada tunggakan atau pinjaman aktif.
                </div>
                @endif
            </div>
        </div>
        
    </div>
</div>
@endsection
