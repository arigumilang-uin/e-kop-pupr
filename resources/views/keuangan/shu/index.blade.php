@extends('layouts.app')

@section('title', 'Simulasi S.H.U')
@section('subtitle', 'Konfigurasi komponen, alokasi distribusi, dan simulasi pembagian Sisa Hasil Usaha.')

@section('content')
<div class="space-y-6">

    {{-- Filter & Header Section --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-white border border-stone-200 text-[#043d2e] shadow-sm flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <div>
                <h2 class="text-xl font-bold text-stone-800">Tahun Buku {{ $tahun }}</h2>
                <p class="text-[13px] text-stone-500">Pilih tahun untuk melihat dan mengatur simulasi SHU.</p>
            </div>
        </div>

        <form action="{{ route('keuangan.shu') }}" method="GET" class="shrink-0">
            <select name="tahun" class="px-5 py-3 rounded-2xl border border-stone-200 bg-white shadow-sm focus:ring-4 focus:ring-[#043d2e]/10 focus:border-[#043d2e] text-sm font-bold text-stone-700 outline-none transition-all cursor-pointer hover:border-stone-300" onchange="this.form.submit()">
                @foreach($tahunTersedia as $t)
                    <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}>Tahun {{ $t }}</option>
                @endforeach
            </select>
        </form>
    </div>

    {{-- Stats Overview --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- KIRI: Laporan Laba/Rugi Mini (Penyusun SHU) --}}
        <div class="bg-white rounded-3xl border border-stone-200 shadow-sm flex flex-col overflow-hidden">
            <div class="px-6 py-5 border-b border-stone-100 flex items-center gap-3 bg-stone-50/50">
                <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                </div>
                <h3 class="font-bold text-stone-800">Penyusun Nilai S.H.U</h3>
            </div>
            
            <div class="p-6 flex-1 flex flex-col justify-between space-y-8">
                <div class="space-y-6">
                    {{-- Pendapatan --}}
                    <div>
                        <h4 class="text-[11px] font-black text-emerald-600 uppercase tracking-widest mb-3 flex items-center gap-2">
                            <span>Pendapatan</span>
                            <div class="h-px bg-emerald-100 flex-1"></div>
                        </h4>
                        <div class="space-y-2.5">
                            @forelse($shu['pendapatan_items'] as $item)
                            <div class="flex justify-between items-center group">
                                <span class="text-[13px] text-stone-600 font-bold group-hover:text-stone-900 transition-colors">{{ $item['nama'] }}</span>
                                <span class="font-mono text-[13px] font-bold text-emerald-700">{{ format_rupiah($item['nominal']) }}</span>
                            </div>
                            @empty
                            <p class="text-xs text-stone-400 italic">Tidak ada pendapatan tercatat.</p>
                            @endforelse
                        </div>
                    </div>

                    {{-- Beban --}}
                    <div>
                        <h4 class="text-[11px] font-black text-red-500 uppercase tracking-widest mb-3 flex items-center gap-2">
                            <span>Beban & Pengeluaran</span>
                            <div class="h-px bg-red-100 flex-1"></div>
                        </h4>
                        <div class="space-y-2.5">
                            @forelse($shu['beban_items'] as $item)
                            <div class="flex justify-between items-center group">
                                <span class="text-[13px] text-stone-600 font-bold group-hover:text-stone-900 transition-colors">{{ $item['nama'] }}</span>
                                <span class="font-mono text-[13px] font-bold text-red-600">{{ format_rupiah($item['nominal']) }}</span>
                            </div>
                            @empty
                            <p class="text-xs text-stone-400 italic">Tidak ada beban tercatat.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Total Bersih --}}
                <div class="pt-5 border-t-2 border-dashed border-stone-200 flex justify-between items-end">
                    <div>
                        <p class="text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1">Sisa Hasil Usaha (Bersih)</p>
                        <p class="text-[11px] text-stone-400 font-medium">Total Pendapatan − Total Beban</p>
                    </div>
                    <p class="text-3xl font-black font-mono tracking-tight {{ $shu['shu_bersih'] >= 0 ? 'text-[#043d2e]' : 'text-red-500' }}">
                        {{ format_rupiah($shu['shu_bersih']) }}
                    </p>
                </div>
            </div>
        </div>

        {{-- KANAN: Distribusi SHU --}}
        <div class="bg-gradient-to-br from-[#043d2e] to-[#022118] rounded-3xl p-8 shadow-xl relative overflow-hidden flex flex-col text-white">
            <div class="absolute -top-24 -right-24 w-64 h-64 rounded-full bg-emerald-400/10 blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-24 -left-24 w-64 h-64 rounded-full bg-blue-400/10 blur-3xl pointer-events-none"></div>

            <div class="relative z-10 flex flex-col h-full">
                <div class="flex items-center gap-3 mb-8">
                    <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center backdrop-blur-md shrink-0">
                        <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg text-white">Simulasi Pembagian</h3>
                        <p class="text-[11px] text-emerald-100/70 font-mono mt-0.5">Berdasarkan persentase alokasi aktif</p>
                    </div>
                </div>

                @if($shu['shu_bersih'] <= 0)
                <div class="flex-1 flex flex-col items-center justify-center text-center p-6 bg-white/5 border border-white/10 rounded-2xl backdrop-blur-sm">
                    <svg class="w-12 h-12 text-white/20 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 12H4M12 20V4"/></svg>
                    <p class="text-emerald-400 font-bold mb-1">SHU Negatif atau Nol</p>
                    <p class="text-[13px] text-stone-300">Tidak ada dana yang dapat dibagikan untuk periode ini.</p>
                </div>
                @else
                <div class="flex-1 space-y-3">
                    @foreach($shu['distribusi_items'] as $d)
                    <div class="group flex justify-between items-center bg-white/5 hover:bg-white/10 border border-white/5 hover:border-white/10 rounded-xl p-3.5 transition-all cursor-default">
                        <div>
                            <p class="text-[13px] font-bold text-stone-100">{{ $d['nama'] }}</p>
                            @if($d['deskripsi'])
                            <p class="text-[10px] text-stone-400 mt-0.5 group-hover:text-stone-300 transition-colors">{{ $d['deskripsi'] }}</p>
                            @endif
                        </div>
                        <div class="text-right">
                            <p class="text-base font-bold text-white font-mono">{{ format_rupiah($d['nominal']) }}</p>
                            <p class="text-[11px] font-mono font-bold text-amber-400 mt-0.5">{{ number_format($d['persen'], 1) }}%</p>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="mt-6 pt-5 border-t border-white/10 flex justify-between items-center">
                    <div>
                        <span class="block text-[11px] font-bold text-emerald-100/50 uppercase tracking-wider">Total Alokasi</span>
                        @if(abs($shu['total_persen_distribusi'] - 100) > 0.01)
                        <span class="block text-[10px] text-amber-400/80 mt-0.5 font-medium">⚠️ Belum mencapai 100%</span>
                        @endif
                    </div>
                    <span class="text-xl font-black font-mono {{ abs($shu['total_persen_distribusi'] - 100) < 0.01 ? 'text-emerald-400' : 'text-amber-400' }}">
                        {{ number_format($shu['total_persen_distribusi'], 1) }}%
                    </span>
                </div>
                @endif
            </div>
        </div>

    </div>

    {{-- Panel Konfigurasi --}}
    @can('shu.manage')
    <div x-data class="bg-white rounded-3xl border border-stone-200 shadow-sm overflow-hidden" id="konfigurasi-panel">
        
        <div class="px-6 py-4 border-b border-stone-100 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-stone-50/30">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-violet-100 text-violet-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="font-bold text-stone-800">Konfigurasi Distribusi SHU</h3>
                        <button type="button" @click="$dispatch('open-modal', 'modal-info-shu')" class="w-5 h-5 rounded-full bg-[#043d2e]/10 text-[#043d2e] flex items-center justify-center hover:bg-[#043d2e]/20 transition-colors" title="Informasi Logika SHU">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </button>
                    </div>
                    <p class="text-[12px] text-stone-500">Sesuaikan persentase alokasi untuk simulasi.</p>
                </div>
            </div>
        </div>

        <div class="p-6">
            {{-- Daftar Distribusi --}}
            <div>
                <div class="flex justify-between items-center mb-6">
                    <h4 class="text-sm font-bold text-stone-800">Daftar Alokasi Distribusi</h4>
                    <button type="button" @click="$dispatch('open-modal', 'modal-tambah-distribusi')" class="text-[13px] font-bold text-[#043d2e] hover:text-[#022118] bg-[#043d2e]/5 hover:bg-[#043d2e]/10 px-4 py-2 rounded-xl transition-colors flex items-center gap-2">
                        + Tambah Alokasi
                    </button>
                </div>

                @php
                    $totalPersen = $distribusiAll->where('is_aktif', true)->sum('persen');

                    $routingBadge = [
                        'prorata_simpanan'    => ['badge' => 'bg-emerald-100 text-emerald-700', 'icon' => 'bg-emerald-50 text-emerald-600', 'short' => 'Prorata Simpanan'],
                        'prorata_pinjaman'    => ['badge' => 'bg-blue-100 text-blue-700', 'icon' => 'bg-blue-50 text-blue-600', 'short' => 'Prorata Pinjaman'],
                        'bagi_rata_pengurus'  => ['badge' => 'bg-violet-100 text-violet-700', 'icon' => 'bg-violet-50 text-violet-600', 'short' => 'Bagi Rata Pengurus'],
                        'ekuitas'             => ['badge' => 'bg-amber-100 text-amber-700', 'icon' => 'bg-amber-50 text-amber-600', 'short' => 'Ekuitas / Modal'],
                        'kewajiban'           => ['badge' => 'bg-stone-100 text-stone-600', 'icon' => 'bg-stone-50 text-stone-500', 'short' => 'Kewajiban Titipan'],
                    ];
                @endphp

                <div class="flex flex-col space-y-3">
                    @foreach($distribusiAll as $d)
                    @php $rb = $routingBadge[$d->tipe_routing] ?? $routingBadge['kewajiban']; @endphp
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 p-4 rounded-xl border transition-all {{ $d->is_aktif ? 'bg-white border-stone-200 hover:border-stone-300 shadow-sm' : 'bg-stone-50/50 border-stone-100 opacity-60 grayscale' }}">
                        
                        <div class="flex items-center gap-5 flex-1">
                            {{-- Toggle Aktif/Nonaktif --}}
                            <form action="{{ route('shu.distribusi.update', $d->id) }}" method="POST" class="shrink-0 m-0">
                                @csrf @method('PATCH')
                                <input type="hidden" name="nama" value="{{ $d->nama }}">
                                <input type="hidden" name="persen" value="{{ $d->persen }}">
                                <input type="hidden" name="deskripsi" value="{{ $d->deskripsi }}">
                                <input type="hidden" name="is_aktif" value="{{ $d->is_aktif ? '0' : '1' }}">
                                <button type="submit" class="relative inline-flex items-center cursor-pointer h-6 w-11 rounded-full transition-colors {{ $d->is_aktif ? 'bg-[#043d2e]' : 'bg-stone-300 hover:bg-stone-400' }}" title="{{ $d->is_aktif ? 'Nonaktifkan' : 'Aktifkan' }}">
                                    <span class="inline-block w-4 h-4 transform bg-white rounded-full transition-transform {{ $d->is_aktif ? 'translate-x-6' : 'translate-x-1' }}"></span>
                                </button>
                            </form>

                            <div class="flex items-center gap-4 flex-1">
                                <div class="w-10 h-10 rounded-full {{ $rb['icon'] }} flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/></svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h5 class="text-[14px] font-bold text-stone-800">{{ $d->nama }}</h5>
                                    <span class="inline-block text-[10px] font-bold {{ $rb['badge'] }} px-2.5 py-0.5 rounded-full mt-1">→ {{ $rb['short'] }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-4 pl-16 md:pl-0 shrink-0">
                            {{-- Edit Persentase --}}
                            <form action="{{ route('shu.distribusi.update', $d->id) }}" method="POST" class="flex items-center gap-3 m-0">
                                @csrf @method('PATCH')
                                <input type="hidden" name="nama" value="{{ $d->nama }}">
                                <input type="hidden" name="deskripsi" value="{{ $d->deskripsi }}">
                                <input type="hidden" name="is_aktif" value="{{ $d->is_aktif ? '1' : '0' }}">
                                
                                <div class="flex items-center gap-2 bg-stone-50 px-3 py-1.5 rounded-xl border border-stone-200 transition-colors focus-within:border-[#043d2e]/30">
                                    <input type="number" name="persen" value="{{ $d->persen }}" step="0.01" min="0" max="100" class="w-20 bg-transparent border-none p-0 focus:ring-0 text-[13px] font-mono font-black text-right text-[#043d2e] outline-none">
                                    <span class="text-[12px] font-bold text-stone-400">%</span>
                                </div>
                                <button type="submit" class="w-9 h-9 rounded-xl flex items-center justify-center bg-emerald-50 hover:bg-emerald-100 text-emerald-600 hover:text-emerald-700 transition-colors" title="Simpan Persentase">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                </button>
                            </form>

                            {{-- Hapus --}}
                            <form action="{{ route('shu.distribusi.destroy', $d->id) }}" method="POST" onsubmit="return confirm('Hapus permanen alokasi ini?')" class="m-0">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-9 h-9 rounded-xl flex items-center justify-center bg-red-50 hover:bg-red-100 text-red-500 hover:text-red-600 transition-colors" title="Hapus Alokasi">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="mt-6 p-5 rounded-2xl flex justify-between items-center {{ abs($totalPersen - 100) < 0.01 ? 'bg-emerald-50 border border-emerald-100 text-emerald-800' : 'bg-amber-50 border border-amber-200 text-amber-800' }}">
                    <span class="text-[13px] font-bold uppercase tracking-wider">Total Alokasi Saat Ini</span>
                    <div class="text-right">
                        <span class="text-2xl font-black font-mono">{{ number_format($totalPersen, 1) }}%</span>
                        @if(abs($totalPersen - 100) > 0.01)
                        <span class="block text-[11px] font-bold opacity-80 mt-1 uppercase tracking-widest text-amber-600">⚠️ Harus genap 100%</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endcan

    {{-- ============================================== --}}
    {{-- STATUS DISTRIBUSI & TABEL PRORATA              --}}
    {{-- ============================================== --}}
    @if($payoutTahunIni)
    <div class="bg-emerald-50 border border-emerald-200 rounded-3xl p-6 md:p-8 flex flex-col md:flex-row items-center gap-6 shadow-sm">
        <div class="w-16 h-16 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div class="flex-1 text-center md:text-left">
            <h3 class="text-lg font-black text-emerald-800 mb-1 tracking-tight">SHU {{ $tahun }} Telah Didistribusikan</h3>
            <p class="text-[13px] text-emerald-700">Berhasil dieksekusi pada <span class="font-bold">{{ $payoutTahunIni->created_at->translatedFormat('d F Y, H:i') }} WIB</span> oleh {{ $payoutTahunIni->eksekutor->nama ?? 'Sistem' }}.</p>
        </div>
        <div class="flex gap-6 shrink-0 bg-white/60 px-6 py-4 rounded-2xl border border-emerald-100">
            <div>
                <p class="text-[11px] font-bold text-emerald-600 uppercase tracking-wider mb-1">Total Dana</p>
                <p class="text-xl font-black font-mono text-emerald-800">{{ format_rupiah($payoutTahunIni->total_terdistribusi) }}</p>
            </div>
            <div class="w-px bg-emerald-200"></div>
            <div>
                <p class="text-[11px] font-bold text-emerald-600 uppercase tracking-wider mb-1">Penerima</p>
                <p class="text-xl font-black font-mono text-emerald-800">{{ $payoutTahunIni->jumlah_penerima }} <span class="text-sm font-bold font-sans">org</span></p>
            </div>
        </div>
    </div>
    @endif

    @if($prorata && $prorata['detail']->isNotEmpty())
    <div class="bg-white rounded-3xl border border-stone-200 shadow-sm overflow-hidden">
        <div class="p-6 md:px-8 md:py-6 border-b border-stone-100 flex flex-col md:flex-row md:items-center justify-between gap-6 bg-stone-50/30">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-2xl bg-blue-50 border border-blue-100 text-blue-600 flex items-center justify-center shrink-0 mt-1">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div>
                    <h3 class="text-base font-black text-stone-800 tracking-tight">Rincian Prorata Anggota</h3>
                    <p class="text-[12px] text-stone-500 mt-1 flex items-center gap-3">
                        <span>Jasa Modal: <strong class="font-mono text-stone-700">{{ format_rupiah($danaJasaModal) }}</strong></span>
                        <span class="w-1 h-1 rounded-full bg-stone-300"></span>
                        <span>Jasa Usaha: <strong class="font-mono text-stone-700">{{ format_rupiah($danaJasaUsaha) }}</strong></span>
                    </p>
                </div>
            </div>

            @can('shu.manage')
            @if(!$payoutTahunIni)
            <form action="{{ route('shu.payout') }}" method="POST" class="shrink-0"
                  onsubmit="return confirm('⚠️ PERHATIAN:\n\nAnda akan mendistribusikan SHU tahun {{ $tahun }} ke Simpanan Sukarela seluruh anggota.\nTotal: {{ format_rupiah($prorata['ringkasan']['total_terdistribusi']) }} untuk {{ $prorata['ringkasan']['jumlah_penerima'] }} anggota.\n\nAksi ini TIDAK BISA DIBATALKAN. Lanjutkan?')">
                @csrf
                <input type="hidden" name="tahun" value="{{ $tahun }}">
                <button type="submit" class="w-full md:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 bg-[#043d2e] hover:bg-[#022118] text-white rounded-xl text-[13px] font-bold shadow-lg shadow-[#043d2e]/20 transition-all active:scale-95 border border-[#043d2e]/50">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    Eksekusi Distribusi Sekarang
                </button>
            </form>
            @endif
            @endcan
        </div>

        <div class="bg-white rounded-2xl border border-stone-200 shadow-sm flex flex-col">
            <div class="overflow-x-auto">
                <x-table class="!border-t-0">
                    <x-table.thead :sticky="true">
                        <x-table.th class="text-center w-12">#</x-table.th>
                        <x-table.th>Informasi Anggota</x-table.th>
                        <x-table.th class="text-right">Simpanan Neto</x-table.th>
                        <x-table.th class="text-right">Bunga Dibayar</x-table.th>
                        <x-table.th class="text-right bg-emerald-50/80 !text-emerald-700">Jasa Modal</x-table.th>
                        <x-table.th class="text-right bg-blue-50/80 !text-blue-700">Jasa Usaha</x-table.th>
                        <x-table.th class="text-right text-[#043d2e] bg-emerald-50/90 backdrop-blur-sm border-l border-stone-200/60">Total S.H.U</x-table.th>
                    </x-table.thead>
                    
                    <x-table.tbody class="divide-y divide-stone-100">
                        @php 
                            $detailCollection = collect($prorata['detail']);
                            $totalAnggota = $detailCollection->count();
                            $perPage = 15;
                            $currentPage = request()->input('page', 1);
                            $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
                                $detailCollection->forPage($currentPage, $perPage),
                                $totalAnggota,
                                $perPage,
                                $currentPage,
                                ['path' => request()->url(), 'query' => request()->query()]
                            );
                        @endphp
                        
                        @foreach($paginator as $item)
                        <x-table.tr class="hover:bg-stone-50/80 transition-colors group">
                            <x-table.td class="text-center text-stone-400 font-mono">{{ ($currentPage - 1) * $perPage + $loop->iteration }}</x-table.td>
                            <x-table.td>
                                <div class="flex flex-col">
                                    <span class="font-bold text-stone-800">{{ $item->nama }}</span>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-[11px] text-stone-500 font-mono font-medium tracking-wide">NIP. <x-nip-display :value="$item->nip" /></span>
                                    </div>
                                </div>
                            </x-table.td>
                            <x-table.td class="text-right font-mono text-stone-600">{{ format_rupiah($item->simpanan) }}</x-table.td>
                            <x-table.td class="text-right font-mono text-stone-600">{{ format_rupiah($item->bunga_dibayar) }}</x-table.td>
                            
                            <x-table.td class="text-right bg-emerald-50/30 group-hover:bg-emerald-50/80 transition-colors">
                                <div class="font-mono text-[13px] font-bold text-emerald-700">{{ format_rupiah($item->jasa_modal) }}</div>
                                <div class="text-[10px] font-sans font-medium text-emerald-600/70 mt-0.5">{{ $item->proporsi_modal }}% dari total</div>
                            </x-table.td>
                            
                            <x-table.td class="text-right bg-blue-50/30 group-hover:bg-blue-50/80 transition-colors">
                                <div class="font-mono text-[13px] font-bold text-blue-700">{{ format_rupiah($item->jasa_usaha) }}</div>
                                <div class="text-[10px] font-sans font-medium text-blue-600/70 mt-0.5">{{ $item->proporsi_usaha }}% dari total</div>
                            </x-table.td>
                            
                            <x-table.td class="text-right bg-emerald-50/30 border-l border-stone-200/60 group-hover:bg-[#043d2e]/[0.05] transition-colors">
                                <div class="font-mono font-black text-[#043d2e] text-[15px] block ml-auto">{{ format_rupiah($item->total_shu) }}</div>
                            </x-table.td>
                        </x-table.tr>
                        @endforeach
                    </x-table.tbody>
                    
                    <tfoot class="bg-stone-50 border-t-2 border-stone-200">
                        <tr>
                            <td colspan="4" class="px-5 py-4 text-[12px] font-black text-stone-700 uppercase tracking-widest text-right">Total Keseluruhan</td>
                            <td class="px-5 py-4 font-mono font-black text-emerald-700 text-right text-[14px] bg-emerald-100/50">{{ format_rupiah($prorata['detail']->sum('jasa_modal')) }}</td>
                            <td class="px-5 py-4 font-mono font-black text-blue-700 text-right text-[14px] bg-blue-100/50">{{ format_rupiah($prorata['detail']->sum('jasa_usaha')) }}</td>
                            <td class="px-5 py-4 font-mono font-black text-[#043d2e] text-right text-[16px] bg-[#043d2e]/10 border-l border-stone-200/60">{{ format_rupiah($prorata['ringkasan']['total_terdistribusi']) }}</td>
                        </tr>
                    </tfoot>
                </x-table>
            </div>
            
            @if($paginator->hasPages())
            <div class="px-6 py-4 border-t border-stone-200 bg-stone-50/50">
                {{ $paginator->links() }}
            </div>
            @endif
        </div>
    </div>
    
    @elseif($shu['shu_bersih'] > 0 && !$payoutTahunIni)
    <div class="bg-amber-50 border border-amber-200 rounded-3xl p-10 text-center flex flex-col items-center shadow-sm">
        <div class="w-16 h-16 rounded-2xl bg-amber-100 text-amber-500 flex items-center justify-center mb-5 rotate-12">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <h3 class="text-lg font-black text-amber-900 mb-2">Simulasi Prorata Tidak Tersedia</h3>
        <p class="text-[13px] text-amber-800 max-w-lg mx-auto leading-relaxed">
            SHU Bersih <span class="font-mono font-bold">{{ format_rupiah($shu['shu_bersih']) }}</span> tersedia, namun tidak ditemukan pos distribusi "Jasa Modal" atau "Jasa Anggota" yang aktif pada alokasi persentase, atau belum ada anggota yang memenuhi syarat.
        </p>
    </div>
    @endif

    {{-- MODALS --}}
    @can('shu.manage')
    {{-- Modal Tambah Distribusi --}}
    <x-modal name="modal-tambah-distribusi" title="Tambah Alokasi Distribusi" maxWidth="2xl">
        <form action="{{ route('shu.distribusi.store') }}" method="POST" id="form-tambah-distribusi">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="col-span-1 md:col-span-2">
                    <label class="block text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-2">Nama Alokasi <span class="text-red-500">*</span></label>
                    <input type="text" name="nama" placeholder="Misal: Dana Sosial" required class="w-full px-4 py-2.5 rounded-xl border border-stone-200 focus:border-[#043d2e] focus:ring-2 focus:ring-[#043d2e]/20 text-[13px] outline-none transition-all bg-white">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-2">Tujuan Penyaluran Dana <span class="text-red-500">*</span></label>
                    <select name="tipe_routing" required class="w-full px-4 py-2.5 rounded-xl border border-stone-200 focus:border-[#043d2e] focus:ring-2 focus:ring-[#043d2e]/20 text-[13px] outline-none transition-all bg-white font-medium text-stone-700">
                        <option value="" disabled selected>— Pilih tujuan penyaluran —</option>
                        @foreach(\App\Models\ShuDistribusi::ROUTING_OPTIONS as $key => $opt)
                        <option value="{{ $key }}">{{ $opt['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-2">Persentase (%) <span class="text-red-500">*</span></label>
                    <input type="number" name="persen" step="0.01" min="0" max="100" placeholder="0.00" required class="w-full px-4 py-2.5 rounded-xl border border-stone-200 focus:border-[#043d2e] focus:ring-2 focus:ring-[#043d2e]/20 text-[13px] font-mono outline-none transition-all bg-white font-bold text-[#043d2e]">
                </div>
                <div class="col-span-1 md:col-span-2">
                    <label class="block text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-2">Keterangan Tambahan</label>
                    <input type="text" name="deskripsi" placeholder="Opsional" class="w-full px-4 py-2.5 rounded-xl border border-stone-200 focus:border-[#043d2e] focus:ring-2 focus:ring-[#043d2e]/20 text-[13px] outline-none transition-all bg-white">
                </div>
            </div>
        </form>

        <x-slot name="footer">
            <button type="button" @click="$dispatch('close-modal', 'modal-tambah-distribusi')" class="px-5 py-2.5 text-stone-500 hover:bg-stone-200 rounded-xl text-[13px] font-bold transition-colors">Batal</button>
            <button type="submit" form="form-tambah-distribusi" class="px-5 py-2.5 bg-[#043d2e] text-white rounded-xl text-[13px] font-bold hover:bg-[#022118] transition-colors shadow-sm">Simpan Alokasi</button>
        </x-slot>
    </x-modal>

    {{-- Modal Info Logika SHU --}}
    <x-modal name="modal-info-shu" title="Informasi & Logika Distribusi SHU" subtitle="Panduan lengkap alur penyaluran dana" maxWidth="3xl">
        <div class="space-y-6 text-[13px] text-stone-600 leading-relaxed">
            
            {{-- Intro --}}
            <div class="p-4 bg-stone-50 rounded-2xl border border-stone-200 text-stone-700 font-medium">
                Sistem Informasi Koperasi Tirta Bina Karya mendistribusikan SHU Bersih secara otomatis berdasarkan persentase alokasi yang Anda atur. Setiap penyaluran diarahkan ke pos-pos keuangan yang berbeda secara sistematis.
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Jasa Modal --}}
                <div class="space-y-2.5">
                    <div class="flex items-center gap-3 mb-1">
                        <div class="w-8 h-8 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 border border-emerald-100">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h4 class="font-bold text-stone-800 text-[14px]">Prorata Simpanan (Jasa Modal)</h4>
                    </div>
                    <p class="text-stone-500">Dikalkulasi berdasarkan total <strong>Simpanan</strong> setiap anggota (Simpanan Wajib + Simpanan Pokok + SWP).</p>
                    <div class="py-2.5 px-3 bg-emerald-50/50 rounded-xl border border-emerald-100/50 font-mono text-[11px] text-emerald-700 font-medium">
                        (Simp. Anggota / Total Simp.) × Rp Alokasi
                    </div>
                    <div class="flex items-center gap-1.5 text-[12px] font-bold text-emerald-600 mt-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Masuk ke: Simpanan Anggota (Bonus SHU)
                    </div>
                </div>

                {{-- Jasa Usaha --}}
                <div class="space-y-2.5">
                    <div class="flex items-center gap-3 mb-1">
                        <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 border border-blue-100">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <h4 class="font-bold text-stone-800 text-[14px]">Prorata Pinjaman (Jasa Usaha)</h4>
                    </div>
                    <p class="text-stone-500">Dikalkulasi berdasarkan partisipasi pembayaran <strong>Bunga Pinjaman</strong> dari setiap anggota ke koperasi pada tahun buku tersebut.</p>
                    <div class="py-2.5 px-3 bg-blue-50/50 rounded-xl border border-blue-100/50 font-mono text-[11px] text-blue-700 font-medium">
                        (Bunga Anggota / Total Bunga) × Rp Alokasi
                    </div>
                    <div class="flex items-center gap-1.5 text-[12px] font-bold text-blue-600 mt-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Masuk ke: Simpanan Anggota (Bonus SHU)
                    </div>
                </div>

                {{-- Pengurus --}}
                <div class="space-y-2.5">
                    <div class="flex items-center gap-3 mb-1">
                        <div class="w-8 h-8 rounded-full bg-violet-50 text-violet-600 flex items-center justify-center shrink-0 border border-violet-100">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <h4 class="font-bold text-stone-800 text-[14px]">Bagi Rata Pengurus</h4>
                    </div>
                    <p class="text-stone-500">Total alokasi akan dibagi rata ke seluruh akun pengguna yang memiliki level akses Pengurus dan masih aktif berstatus anggota.</p>
                    <div class="flex items-center gap-1.5 text-[12px] font-bold text-violet-600 mt-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Masuk ke: Simpanan Anggota (Bonus SHU)
                    </div>
                </div>

                {{-- Ekuitas --}}
                <div class="space-y-2.5">
                    <div class="flex items-center gap-3 mb-1">
                        <div class="w-8 h-8 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 border border-amber-100">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                        </div>
                        <h4 class="font-bold text-stone-800 text-[14px]">Ekuitas / Modal (Cadangan)</h4>
                    </div>
                    <p class="text-stone-500">Dana ini ditahan di koperasi sebagai cadangan modal untuk memperkuat fundamental keuangan koperasi di tahun berjalan maupun mendatang.</p>
                    <div class="flex items-center gap-1.5 text-[12px] font-bold text-amber-600 mt-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Status: Disimpan sebagai Kas Modal
                    </div>
                </div>

                {{-- Kewajiban --}}
                <div class="space-y-2.5 md:col-span-2 p-5 bg-stone-50/50 rounded-2xl border border-stone-200 mt-2">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-8 h-8 rounded-full bg-stone-200 text-stone-700 flex items-center justify-center shrink-0 border border-stone-300">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/></svg>
                        </div>
                        <h4 class="font-bold text-stone-800 text-[14px]">Kewajiban Titipan (Dana Sosial, Pendidikan, dll)</h4>
                    </div>
                    <p class="text-stone-500">Dana ini "dipisahkan" dari arus kas bebas dan ditampung ke dalam pos Kewajiban/Hutang Koperasi kepada pihak ketiga atau pos kegiatan spesifik.</p>
                    <div class="flex items-center gap-1.5 text-[12px] font-bold text-stone-700 mt-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Masuk ke: Modul Kewajiban Dana SHU
                    </div>
                </div>
            </div>
        </div>

        <x-slot name="footer">
            <button type="button" @click="$dispatch('close-modal', 'modal-info-shu')" class="px-5 py-2.5 bg-stone-800 text-white rounded-xl text-[13px] font-bold hover:bg-stone-900 transition-colors shadow-sm">Tutup Panduan</button>
        </x-slot>
    </x-modal>
    @endcan

</div>

{{-- SCRIPT UNTUK PRESERVE SCROLL SAAT SUBMIT TOGGLE/DELETE --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let scrollPos = sessionStorage.getItem('shu_scroll_pos');
        if (scrollPos) {
            window.scrollTo(0, parseInt(scrollPos));
            sessionStorage.removeItem('shu_scroll_pos');
        }
    });

    document.addEventListener('submit', function(e) {
        sessionStorage.setItem('shu_scroll_pos', window.scrollY);
    });
</script>
@endsection
