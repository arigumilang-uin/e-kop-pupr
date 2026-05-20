@extends('layouts.app')

@section('title', $tab === 'neraca' ? 'Parameter Laporan Neraca' : 'Parameter Laporan Laba/Rugi')
@section('subtitle', $tab === 'neraca' ? 'Konfigurasi akun Aktiva dan Pasiva sebagai fondasi pelaporan neraca keuangan.' : 'Konfigurasi akun Pendapatan dan Beban untuk perhitungan hasil usaha koperasi.')

@section('content')
<div class="space-y-6" x-data="{ tab: '{{ $tab }}' }">

@section('actions')
    <div class="flex flex-wrap items-center gap-3">
        <form method="GET" class="relative bg-white border border-stone-200 rounded-xl px-4 py-2.5 shadow-sm flex items-center gap-2">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <svg class="w-4 h-4 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <span class="text-[12px] font-bold text-stone-500 whitespace-nowrap">Tahun Pembukuan:</span>
            <select name="tahun" onchange="this.form.submit()" class="bg-transparent border-none text-stone-700 text-sm font-black p-0 py-0.5 outline-none cursor-pointer focus:ring-0">
                @foreach($tahunBukuList as $th)
                    <option value="{{ $th }}" {{ $th == $tahun ? 'selected' : '' }}>{{ $th }}</option>
                @endforeach
            </select>
        </form>

        @if($tab === 'neraca')
        <button x-data @click="$dispatch('open-modal', 'modal-tambah-neraca')" class="inline-flex justify-center items-center gap-2 px-5 py-2.5 bg-[#043d2e] hover:bg-[#065a45] text-white text-[13px] font-bold rounded-xl shadow-sm transition-all hover:scale-[1.02]">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Akun Neraca
        </button>
        @elseif($tab === 'phu')
        <button x-data @click="$dispatch('open-modal', 'modal-tambah-phu')" class="inline-flex justify-center items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-[13px] font-bold rounded-xl shadow-sm transition-all hover:scale-[1.02]">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Akun Laba/Rugi
        </button>
        @endif
    </div>
@endsection

    @if($tab === 'neraca')
    {{-- TAB NERACA --}}
    <div class="space-y-8">
        @if($parameterNeraca->isEmpty())
        <div class="bg-white py-14 px-6 rounded-3xl border border-stone-200 shadow-sm text-center">
            <div class="w-20 h-20 bg-stone-50 rounded-full flex items-center justify-center mx-auto mb-5 border border-stone-100 shadow-inner">
                <svg class="w-8 h-8 text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            </div>
            <h3 class="text-lg text-stone-800 font-black tracking-wide mb-2">Struktur Neraca Tahun {{ $tahun }} Belum Diinisialisasi</h3>
            <p class="text-[13px] text-stone-500 mb-8 max-w-md mx-auto leading-relaxed">Sistem belum menemukan konfigurasi Aktiva dan Pasiva untuk pembukuan tahun ini. Anda dapat menyalin struktur secara utuh dari parameter tahun sebelumnya.</p>
            
            <form action="{{ route('keuangan.parameter.copy') }}" method="POST">
                @csrf
                <input type="hidden" name="tahun_target" value="{{ $tahun }}">
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 bg-stone-800 hover:bg-black text-white text-[13px] font-bold rounded-xl shadow-lg shadow-black/10 transition-all hover:scale-105">
                    <svg class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/></svg>
                    Salin Struktur Parameter Tahun {{ $tahun - 1 }}
                </button>
            </form>
        </div>
        @else
            <div class="bg-white rounded-2xl shadow-sm border border-stone-200 flex flex-col mt-6 mb-8">
                <x-table>
                    <x-table.thead :sticky="true" topOffset="lg:top-[92px]">
                        <x-table.th class="w-1/2">Kategori Utama / Kelompok Akun / Nama Akun</x-table.th>
                        <x-table.th class="text-right">Nilai</x-table.th>
                        <x-table.th>Keterangan</x-table.th>
                        <x-table.th class="text-center w-24">Aksi</x-table.th>
                    </x-table.thead>
                    <x-table.tbody class="divide-y divide-stone-100">

                        {{-- AKTIVA --}}
                        <x-table.tr class="lg:sticky lg:top-[138px] z-10 shadow-sm bg-[#e8eeeb]">
                            <x-table.td class="px-6 py-3 font-black text-[#043d2e] uppercase text-[12px] tracking-widest">
                                AKTIVA
                            </x-table.td>
                            <x-table.td class="px-6 py-3 text-right font-mono font-black text-[#043d2e] text-[14px]">
                                {{ format_rupiah($neracaReport['aktiva_total'] ?? 0) }}
                            </x-table.td>
                            <x-table.td class="px-6 py-3"></x-table.td>
                            <x-table.td class="px-6 py-3 text-center"></x-table.td>
                        </x-table.tr>

                        @foreach(\App\Models\ParameterNeraca::posisiAktiva() as $posisi)
                            @if(isset($parameterNeraca[$posisi]))
                                <x-table.tr class="lg:sticky lg:top-[180px] z-[9] shadow-sm bg-stone-50">
                                    <x-table.td class="px-6 py-2.5 font-bold text-stone-700 text-[11px] uppercase tracking-wider pl-12">
                                        {{ \App\Models\ParameterNeraca::labelPosisi($posisi) }}
                                    </x-table.td>
                                    <x-table.td class="px-6 py-2.5 text-right font-mono font-bold text-stone-700 text-[13px]">
                                        {{ format_rupiah($neracaReport['sections'][$posisi]['total'] ?? 0) }}
                                    </x-table.td>
                                    <x-table.td class="px-6 py-2.5"></x-table.td>
                                    <x-table.td class="px-6 py-2.5 text-center"></x-table.td>
                                </x-table.tr>
                                
                                @foreach($parameterNeraca[$posisi] as $item)
                                    @php
                                        $real_nominal = 0;
                                        if (isset($neracaReport['sections'][$posisi]['items'])) {
                                            $f = collect($neracaReport['sections'][$posisi]['items'])->firstWhere('nama', $item->nama);
                                            if ($f) $real_nominal = $f['nominal'];
                                        }
                                    @endphp
                                    <x-table.tr class="hover:bg-stone-50 transition-colors group">
                                        <x-table.td class="px-6 py-3 pl-[4.5rem]">
                                            <div class="flex items-center gap-2">
                                                <span class="font-bold text-stone-800 text-[13px]">{{ $item->nama }}</span>
                                                @if($item->is_pengurang)
                                                    <span class="px-1.5 py-[1px] bg-red-50 text-red-600 border border-red-200/60 text-[9px] font-black uppercase rounded tracking-widest">Kontra</span>
                                                @endif
                                            </div>
                                        </x-table.td>
                                        <x-table.td class="px-6 py-3 text-right">
                                            @if($item->isOtomatis())
                                                <span class="font-mono font-bold text-stone-800 text-[13px] tabular-nums">{{ format_rupiah($real_nominal) }}</span>
                                            @else
                                                <span class="font-mono font-bold text-stone-800 text-[13px] tabular-nums">{{ format_rupiah($item->nominal_manual) }}</span>
                                            @endif
                                        </x-table.td>
                                        <x-table.td class="px-6 py-3">
                                            @if($item->isOtomatis())
                                                <div class="flex flex-col gap-1 items-start">
                                                    <span class="text-[9px] text-emerald-600 font-bold uppercase tracking-widest inline-flex items-center gap-1">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                                        @if($item->nominal_manual > 0)
                                                            Term. Saldo Awal: {{ format_rupiah($item->nominal_manual) }}
                                                        @else
                                                            Dihitung Sistem
                                                        @endif
                                                    </span>
                                                    <div class="flex gap-1 flex-wrap">
                                                        <span class="text-[9px] font-mono font-bold text-stone-500 bg-stone-100 px-1 py-[1px] rounded border border-stone-200/60">{{ $item->kode_otomatis }}</span>
                                                        @if($item->kode_otomatis === 'PIUTANG_EKSTERNAL')
                                                            <span class="text-[9px] font-bold uppercase text-stone-500 bg-stone-100 px-1 py-[1px] rounded border border-stone-200/60">Fltr: {{ $item->konfigurasi['kategori_peminjam'] ? ucfirst(str_replace('_', ' ', $item->konfigurasi['kategori_peminjam'])) : 'Gbl' }}</span>
                                                        @endif
                                                        @if($item->kode_otomatis === 'SIMPANAN_ANGGOTA_KUSTOM')
                                                            <span class="text-[9px] font-bold uppercase text-stone-500 bg-stone-100 px-1 py-[1px] rounded border border-stone-200/60">Trk: {{ $item->konfigurasi['jenis_simpanan_kode'] ?? 'N/A' }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-[9px] font-bold uppercase tracking-widest text-stone-500 bg-stone-100 px-1.5 py-[2px] rounded border border-stone-200 inline-block">Manual Statis</span>
                                            @endif
                                        </x-table.td>
                                        <x-table.td class="px-6 py-3 text-center align-middle">
                                            <div x-data="{ open: false }" class="relative inline-block text-left whitespace-normal">
                                                <button @click="open = !open" @click.away="open = false" type="button" class="flex items-center justify-center w-7 h-7 mx-auto rounded overflow-hidden hover:bg-stone-100 transition-colors text-stone-500 focus:outline-none focus:bg-stone-100">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/></svg>
                                                </button>
                                                <div x-show="open" x-transition class="absolute right-4 top-0 mt-8 w-44 bg-white rounded-xl shadow-xl border border-stone-200 z-50 overflow-hidden" style="display: none;">
                                                    <button @click="$dispatch('open-modal', 'modal-edit-neraca-{{ $item->id }}'); open = false" class="w-full text-left px-4 py-2.5 text-[12px] font-bold text-stone-700 hover:bg-stone-50 hover:text-[#043d2e] transition-colors">
                                                        Edit Akun
                                                    </button>
                                                    <form action="{{ route('keuangan.parameter.neraca.destroy', $item) }}" method="POST" onsubmit="return confirm('Yakin hapus akun ini dari Neraca?');">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="w-full text-left px-4 py-2.5 text-[12px] font-bold text-red-600 hover:bg-red-50 border-t border-stone-100 transition-colors">
                                                            Hapus Akun
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>

                                            <x-modal name="modal-edit-neraca-{{ $item->id }}" title="Edit Parameter Neraca" maxWidth="md">
                                                <form id="form-edit-neraca-{{ $item->id }}" action="{{ route('keuangan.parameter.neraca.update', $item) }}" method="POST" class="contents">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="space-y-4 text-left">
                                                            <div>
                                                                <label class="block text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Nama Tampilan</label>
                                                                <input type="text" name="nama" value="{{ $item->nama }}" class="w-full border-stone-200 rounded-xl shadow-sm focus:ring-[#043d2e]/20 focus:border-[#043d2e] py-2.5 bg-stone-50 text-sm" required>
                                                            </div>
                                                            
                                                            @if($item->isManual() || in_array($item->kode_otomatis, ['SALDO_BANK_BRK', 'SALDO_KAS_TUNAI', 'SIMPANAN_LIVE_POKOK', 'SIMPANAN_LIVE_WAJIB', 'SIMPANAN_LIVE_SWP']))
                                                            <div>
                                                                <label class="block text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">{{ $item->isManual() ? 'Nominal Manual' : 'Saldo Awal (Opening Balance)' }}</label>
                                                                <div class="relative">
                                                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                                                        <span class="text-stone-400 font-bold">Rp</span>
                                                                    </div>
                                                                    <input type="number" name="nominal_manual" value="{{ (int) $item->nominal_manual }}" class="w-full pl-11 border-stone-200 rounded-xl shadow-sm focus:ring-[#043d2e]/20 focus:border-[#043d2e] font-mono font-bold py-2.5 bg-stone-50" min="0" step="1">
                                                                </div>
                                                            </div>
                                                            @endif
                                                        </div>
                                                </form>

                                                <x-slot name="footer">
                                                    <button type="button" @click="$dispatch('close-modal', 'modal-edit-neraca-{{ $item->id }}')" class="px-5 py-2.5 text-sm font-bold text-stone-500 hover:text-stone-700 hover:bg-stone-100 rounded-xl transition-all">Batal</button>
                                                    <button type="submit" form="form-edit-neraca-{{ $item->id }}" class="px-6 py-2.5 text-sm font-bold text-white bg-[#043d2e] hover:bg-[#065a45] rounded-xl transition-all shadow-sm">Simpan Perubahan</button>
                                                </x-slot>
                                            </x-modal>
                                        </x-table.td>
                                    </x-table.tr>
                                @endforeach
                            @endif
                        @endforeach
                    
                        {{-- PASIVA --}}
                        <x-table.tr class="lg:sticky lg:top-[138px] z-10 shadow-sm bg-[#e8eeeb]">
                            <x-table.td class="px-6 py-3 font-black text-[#043d2e] uppercase text-[12px] tracking-widest">
                                PASIVA
                            </x-table.td>
                            <x-table.td class="px-6 py-3 text-right font-mono font-black text-[#043d2e] text-[14px]">
                                {{ format_rupiah($neracaReport['pasiva_total'] ?? 0) }}
                            </x-table.td>
                            <x-table.td class="px-6 py-3"></x-table.td>
                            <x-table.td class="px-6 py-3 text-center"></x-table.td>
                        </x-table.tr>

                        @foreach(\App\Models\ParameterNeraca::posisiPasiva() as $posisi)
                            @if(isset($parameterNeraca[$posisi]))
                                <x-table.tr class="lg:sticky lg:top-[180px] z-[9] shadow-sm bg-stone-50">
                                    <x-table.td class="px-6 py-2.5 font-bold text-stone-700 text-[11px] uppercase tracking-wider pl-12">
                                        {{ \App\Models\ParameterNeraca::labelPosisi($posisi) }}
                                    </x-table.td>
                                    <x-table.td class="px-6 py-2.5 text-right font-mono font-bold text-stone-700 text-[13px]">
                                        {{ format_rupiah($neracaReport['sections'][$posisi]['total'] ?? 0) }}
                                    </x-table.td>
                                    <x-table.td class="px-6 py-2.5"></x-table.td>
                                    <x-table.td class="px-6 py-2.5 text-center"></x-table.td>
                                </x-table.tr>
                                
                                @foreach($parameterNeraca[$posisi] as $item)
                                    @php
                                        $real_nominal = 0;
                                        if (isset($neracaReport['sections'][$posisi]['items'])) {
                                            $f = collect($neracaReport['sections'][$posisi]['items'])->firstWhere('nama', $item->nama);
                                            if ($f) $real_nominal = $f['nominal'];
                                        }
                                    @endphp
                                    <x-table.tr class="hover:bg-stone-50 transition-colors group">
                                        <x-table.td class="px-6 py-3 pl-[4.5rem]">
                                            <div class="flex items-center gap-2">
                                                <span class="font-bold text-stone-800 text-[13px]">{{ $item->nama }}</span>
                                                @if($item->is_pengurang)
                                                    <span class="px-1.5 py-[1px] bg-red-50 text-red-600 border border-red-200/60 text-[9px] font-black uppercase rounded tracking-widest">Kontra</span>
                                                @endif
                                            </div>
                                        </x-table.td>
                                        <x-table.td class="px-6 py-3 text-right">
                                            @if($item->isOtomatis())
                                                <span class="font-mono font-bold text-stone-800 text-[13px] tabular-nums">{{ format_rupiah($real_nominal) }}</span>
                                            @else
                                                <span class="font-mono font-bold text-stone-800 text-[13px] tabular-nums">{{ format_rupiah($item->nominal_manual) }}</span>
                                            @endif
                                        </x-table.td>
                                        <x-table.td class="px-6 py-3">
                                            @if($item->isOtomatis())
                                                <div class="flex flex-col gap-1 items-start">
                                                    <span class="text-[9px] text-emerald-600 font-bold uppercase tracking-widest inline-flex items-center gap-1">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                                        @if($item->nominal_manual > 0)
                                                            Term. Saldo Awal: {{ format_rupiah($item->nominal_manual) }}
                                                        @else
                                                            Dihitung Sistem
                                                        @endif
                                                    </span>
                                                    <div class="flex gap-1 flex-wrap">
                                                        <span class="text-[9px] font-mono font-bold text-stone-500 bg-stone-100 px-1 py-[1px] rounded border border-stone-200/60">{{ $item->kode_otomatis }}</span>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-[9px] font-bold uppercase tracking-widest text-stone-500 bg-stone-100 px-1.5 py-[2px] rounded border border-stone-200 inline-block">Manual Statis</span>
                                            @endif
                                        </x-table.td>
                                        <x-table.td class="px-6 py-3 text-center align-middle">
                                            <div x-data="{ open: false }" class="relative inline-block text-left whitespace-normal">
                                                <button @click="open = !open" @click.away="open = false" type="button" class="flex items-center justify-center w-7 h-7 mx-auto rounded overflow-hidden hover:bg-stone-100 transition-colors text-stone-500 focus:outline-none focus:bg-stone-100">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/></svg>
                                                </button>
                                                <div x-show="open" x-transition class="absolute right-4 top-0 mt-8 w-44 bg-white rounded-xl shadow-xl border border-stone-200 z-50 overflow-hidden" style="display: none;">
                                                    <button @click="$dispatch('open-modal', 'modal-edit-neraca-{{ $item->id }}'); open = false" class="w-full text-left px-4 py-2.5 text-[12px] font-bold text-stone-700 hover:bg-stone-50 hover:text-[#043d2e] transition-colors">
                                                        Edit Akun
                                                    </button>
                                                    <form action="{{ route('keuangan.parameter.neraca.destroy', $item) }}" method="POST" onsubmit="return confirm('Yakin hapus akun ini dari Neraca?');">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="w-full text-left px-4 py-2.5 text-[12px] font-bold text-red-600 hover:bg-red-50 border-t border-stone-100 transition-colors">
                                                            Hapus Akun
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>

                                            <x-modal name="modal-edit-neraca-{{ $item->id }}" title="Edit Parameter Neraca" maxWidth="md">
                                                <div class="p-6">
                                                    <form action="{{ route('keuangan.parameter.neraca.update', $item) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="space-y-4 text-left">
                                                            <div>
                                                                <label class="block text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Nama Tampilan</label>
                                                                <input type="text" name="nama" value="{{ $item->nama }}" class="w-full border-stone-200 rounded-xl shadow-sm focus:ring-[#043d2e]/20 focus:border-[#043d2e] py-2.5 bg-stone-50 text-sm" required>
                                                            </div>
                                                            
                                                            @if($item->isManual() || in_array($item->kode_otomatis, ['SALDO_BANK_BRK', 'SALDO_KAS_TUNAI', 'SIMPANAN_LIVE_POKOK', 'SIMPANAN_LIVE_WAJIB', 'SIMPANAN_LIVE_SWP']))
                                                            <div>
                                                                <label class="block text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">{{ $item->isManual() ? 'Nominal Manual' : 'Saldo Awal (Opening Balance)' }}</label>
                                                                <div class="relative">
                                                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                                                        <span class="text-stone-400 font-bold">Rp</span>
                                                                    </div>
                                                                    <input type="number" name="nominal_manual" value="{{ (int) $item->nominal_manual }}" class="w-full pl-11 border-stone-200 rounded-xl shadow-sm focus:ring-[#043d2e]/20 focus:border-[#043d2e] font-mono font-bold py-2.5 bg-stone-50" min="0" step="1">
                                                                </div>
                                                            </div>
                                                            @endif
                                                        </div>
                                                        <div class="flex gap-3 justify-end mt-6 pt-5">
                                                            <button type="button" @click="$dispatch('close-modal', 'modal-edit-neraca-{{ $item->id }}')" class="px-4 py-2 text-sm font-bold text-stone-500 hover:bg-stone-100 rounded-lg">Batal</button>
                                                            <button type="submit" class="px-5 py-2 text-sm font-bold text-white bg-[#043d2e] hover:bg-[#065a45] rounded-xl">Simpan</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </x-modal>
                                        </x-table.td>
                                    </x-table.tr>
                                @endforeach
                            @endif
                        @endforeach
                    </x-table.tbody>
                </x-table>
            </div>
        @endif
    </div>

    @elseif($tab === 'phu')
    {{-- TAB PHU --}}
    <div class="space-y-8">
        @if($parameterPhu->isEmpty())
        <div class="bg-white py-14 px-6 rounded-3xl border border-stone-200 shadow-sm text-center">
            <div class="w-20 h-20 bg-stone-50 rounded-full flex items-center justify-center mx-auto mb-5 border border-stone-100 shadow-inner">
                <svg class="w-8 h-8 text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h3 class="text-lg text-stone-800 font-black tracking-wide mb-2">Parameter Laba/Rugi Tahun {{ $tahun }} Belum Diinisialisasi</h3>
            <p class="text-[13px] text-stone-500 mb-8 max-w-md mx-auto leading-relaxed">Komponen pendapatan dan beban operasional belum ditentukan. Anda dapat menduplikasi seluruh parameter perhitungan Laba/Rugi dari tahun sebelumnya.</p>
            
            <form action="{{ route('keuangan.parameter.copy') }}" method="POST">
                @csrf
                <input type="hidden" name="tahun_target" value="{{ $tahun }}">
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white text-[13px] font-bold rounded-xl shadow-lg shadow-emerald-700/20 transition-all hover:scale-105">
                    <svg class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/></svg>
                    Salin Struktur Parameter Tahun {{ $tahun - 1 }}
                </button>
            </form>
        </div>
        @else
            <div class="bg-white rounded-2xl shadow-sm border border-stone-200 flex flex-col mt-6 mb-8">
                <x-table>
                    <x-table.thead :sticky="true" topOffset="lg:top-[92px]">
                        <x-table.th class="w-1/2">Kategori Utama / Kelompok Akun / Nama Akun</x-table.th>
                        <x-table.th class="text-right">Nilai</x-table.th>
                        <x-table.th>Keterangan</x-table.th>
                        <x-table.th class="text-center w-24">Aksi</x-table.th>
                    </x-table.thead>
                    @foreach(['pendapatan', 'beban'] as $tipe)
                        @if(isset($parameterPhu[$tipe]))
                        <x-table.tbody class="divide-y divide-stone-100">
                                <x-table.tr class="bg-[#e8eeeb]">
                                    <x-table.td class="px-6 py-3 font-black text-[#043d2e] uppercase text-[12px] tracking-widest">
                                        {{ strtoupper($tipe) }}
                                    </x-table.td>
                                    <x-table.td class="px-6 py-3 text-right font-mono font-black text-[#043d2e] text-[14px]">
                                        {{ format_rupiah($phuReport[$tipe]['total'] ?? 0) }}
                                    </x-table.td>
                                    <x-table.td class="px-6 py-3"></x-table.td>
                                    <x-table.td class="px-6 py-3 text-center"></x-table.td>
                                </x-table.tr>

                                @foreach($parameterPhu[$tipe] as $item)
                                    @php
                                        $real_nominal = 0;
                                        if (isset($phuReport[$tipe]['items'])) {
                                            $f = collect($phuReport[$tipe]['items'])->firstWhere('nama', $item->nama);
                                            if ($f) $real_nominal = $f['nominal'];
                                        }
                                    @endphp
                                    <x-table.tr class="hover:bg-stone-50 transition-colors group">
                                        <x-table.td class="px-6 py-3 pl-[4.5rem]">
                                            <div class="flex items-center gap-2">
                                                <span class="font-bold text-stone-800 text-[13px]">{{ $item->nama }}</span>
                                            </div>
                                        </x-table.td>
                                        <x-table.td class="px-6 py-3 text-right">
                                            @if($item->isOtomatis())
                                                <span class="font-mono font-bold text-stone-800 text-[13px] tabular-nums">{{ format_rupiah($real_nominal) }}</span>
                                            @else
                                                <span class="font-mono font-bold text-stone-800 text-[13px] tabular-nums">{{ format_rupiah($item->nominal_manual) }}</span>
                                            @endif
                                        </x-table.td>
                                        <x-table.td class="px-6 py-3">
                                            @if($item->isOtomatis())
                                                <div class="flex flex-col gap-1 items-start">
                                                    <span class="text-[9px] text-emerald-600 font-bold uppercase tracking-widest inline-flex items-center gap-1">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                                        Dihitung Sistem
                                                    </span>
                                                    <div class="flex gap-1 flex-wrap">
                                                        <span class="text-[9px] font-mono font-bold text-stone-500 bg-stone-100 px-1 py-[1px] rounded border border-stone-200/60">{{ $item->kode_otomatis }}</span>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-[9px] font-bold uppercase tracking-widest text-stone-500 bg-stone-100 px-1.5 py-[2px] rounded border border-stone-200 inline-block">Manual Statis</span>
                                            @endif
                                        </x-table.td>
                                        <x-table.td class="px-6 py-3 text-center align-middle">
                                            <div x-data="{ open: false }" class="relative inline-block text-left whitespace-normal">
                                                <button @click="open = !open" @click.away="open = false" type="button" class="flex items-center justify-center w-7 h-7 mx-auto rounded overflow-hidden hover:bg-stone-100 transition-colors text-stone-500 focus:outline-none focus:bg-stone-100">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/></svg>
                                                </button>
                                                <div x-show="open" x-transition class="absolute right-4 top-0 mt-8 w-44 bg-white rounded-xl shadow-xl border border-stone-200 z-50 overflow-hidden" style="display: none;">
                                                    <button @click="$dispatch('open-modal', 'modal-edit-phu-{{ $item->id }}'); open = false" class="w-full text-left px-4 py-2.5 text-[12px] font-bold text-stone-700 hover:bg-stone-50 hover:text-[#043d2e] transition-colors">
                                                        Edit Akun
                                                    </button>
                                                    <form action="{{ route('keuangan.parameter.phu.destroy', $item) }}" method="POST" onsubmit="return confirm('Peringatan: Menghapus komponen ini akan menyebabkan Hasil Akhir Laba/Rugi Koperasi berubah. Anda yakin?');">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="w-full text-left px-4 py-2.5 text-[12px] font-bold text-red-600 hover:bg-red-50 border-t border-stone-100 transition-colors">
                                                            Hapus Akun
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>

                                            <x-modal name="modal-edit-phu-{{ $item->id }}" title="Edit Parameter PHU" maxWidth="md">
                                                <form id="form-edit-phu-{{ $item->id }}" action="{{ route('keuangan.parameter.phu.update', $item) }}" method="POST" class="contents">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="space-y-4 text-left">
                                                            <div>
                                                                <label class="block text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Nama Pos {{ ucfirst($item->tipe) }}</label>
                                                                <input type="text" name="nama" value="{{ $item->nama }}" class="w-full border-stone-200 rounded-xl shadow-sm focus:ring-[#043d2e]/20 focus:border-[#043d2e] py-2.5 bg-stone-50 text-sm" required>
                                                            </div>
                                                            
                                                            @if($item->isManual())
                                                            <div>
                                                                <label class="block text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Nominal Manual</label>
                                                                <div class="relative">
                                                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                                                        <span class="text-stone-400 font-bold">Rp</span>
                                                                    </div>
                                                                    <input type="number" name="nominal_manual" value="{{ (int) $item->nominal_manual }}" class="w-full pl-11 border-stone-200 rounded-xl shadow-sm focus:ring-[#043d2e]/20 focus:border-[#043d2e] font-mono font-bold py-2.5 bg-stone-50" min="0" step="1">
                                                                </div>
                                                            </div>
                                                            @endif
                                                        </div>
                                                </form>

                                                <x-slot name="footer">
                                                    <button type="button" @click="$dispatch('close-modal', 'modal-edit-phu-{{ $item->id }}')" class="px-5 py-2.5 text-sm font-bold text-stone-500 hover:text-stone-700 hover:bg-stone-100 rounded-xl transition-all">Batal</button>
                                                    <button type="submit" form="form-edit-phu-{{ $item->id }}" class="px-6 py-2.5 text-sm font-bold text-white bg-[#043d2e] hover:bg-[#065a45] rounded-xl transition-all shadow-sm">Simpan Perubahan</button>
                                                </x-slot>
                                            </x-modal>
                                        </x-table.td>
                                    </x-table.tr>
                                @endforeach
                        </x-table.tbody>
                        @endif
                    @endforeach
                         
                    {{-- SHU SEBELUM PAJAK --}}
                    <x-table.tbody>
                        <x-table.tr class="bg-amber-50">
                            <x-table.td class="px-6 py-4">
                                <div class="font-black text-amber-900 uppercase text-[12px] tracking-widest flex items-center gap-2">
                                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    LABA/RUGI (SHU) TAHUN {{ $tahun }}
                                </div>
                            </x-table.td>
                            <x-table.td class="px-6 py-4 text-right font-mono font-black {{ ($phuReport['shu_bersih'] ?? 0) < 0 ? 'text-red-600' : 'text-amber-700' }} text-[16px]">
                                {{ format_rupiah($phuReport['shu_bersih'] ?? 0) }}
                            </x-table.td>
                            <x-table.td class="px-6 py-4"></x-table.td>
                            <x-table.td class="px-6 py-4 text-center"></x-table.td>
                        </x-table.tr>
                    </x-table.tbody>
                </x-table>
            </div>
        @endif
    </div>
    @endif



    {{-- MODAL TAMBAH NERACA --}}
    <x-modal name="modal-tambah-neraca" title="Buat Akun Neraca Baru (Kalkulasi {{ $tahun }})" maxWidth="lg">
        <form id="form-tambah-neraca" action="{{ route('keuangan.parameter.neraca.store') }}" method="POST" class="contents" x-data="{ sumberData: 'manual', kodeOtomatis: '' }">
            @csrf
            <input type="hidden" name="tahun" value="{{ $tahun }}">
            <div class="space-y-6">
                <div>
                    <label class="block text-[11px] font-black text-stone-500 uppercase tracking-wider mb-2">Nama Akun Pembukuan</label>
                    <input type="text" name="nama" class="w-full border-stone-200 rounded-xl shadow-[inset_0_2px_4px_rgba(0,0,0,0.02)] focus:ring-[#043d2e]/20 focus:border-[#043d2e] text-[13px] py-3 bg-stone-50 transition-all font-bold text-stone-700" placeholder="Contoh: Gedung Kantor / Simpanan Khusus..." required>
                </div>

                <div>
                    <label class="block text-[11px] font-black text-stone-500 uppercase tracking-wider mb-2">Posisi Kelompok Akun</label>
                    <select name="posisi" class="w-full border-stone-200 rounded-xl shadow-[inset_0_2px_4px_rgba(0,0,0,0.02)] focus:ring-[#043d2e]/20 focus:border-[#043d2e] text-[13px] py-3 bg-stone-50 transition-all font-bold text-stone-700">
                        <optgroup label="Aktiva (Harta & Piutang)">
                            <option value="aktiva_lancar">Harta Lancar (Kas & Bank)</option>
                            <option value="penyertaan">Penyertaan (Investasi)</option>
                            <option value="harta_tetap">Harta Tetap (Aset Tanah/Gedung)</option>
                            <option value="harta_lain">Harta Lain-lain</option>
                        </optgroup>
                        <optgroup label="Pasiva (Kewajiban & Modal)">
                            <option value="kewajiban_pendek">Kewajiban Jangka Pendek (Hutang Belum Jatuh Tempo)</option>
                            <option value="kewajiban_panjang">Kewajiban Jangka Panjang (Hutang Bank dll)</option>
                            <option value="modal">Modal Sendiri (Simpanan & Cadangan)</option>
                        </optgroup>
                    </select>
                </div>

                <div class="flex items-start gap-3 p-3 bg-red-50 border border-red-100/60 rounded-xl">
                    <div class="mt-0.5">
                        <input type="checkbox" name="is_pengurang" id="is_pengurang" value="1" class="rounded text-red-600 focus:ring-red-500/20 border-red-300 w-4 h-4 cursor-pointer">
                    </div>
                    <label for="is_pengurang" class="text-[13px] text-red-900 font-bold cursor-pointer select-none">Tandai sebagai Akun Pengurang (Kontra Akun)<br><span class="text-[11px] font-normal text-red-600/80">Ceklis ini jika nominal akun ini bertugas mengurangi total groupnya (Misal: Akumulasi Penyusutan, Prive).</span></label>
                </div>

                <div>
                    <label class="block text-[11px] font-black text-stone-500 uppercase tracking-wider mb-2">Sumber Nominal Keuangan</label>
                    <select name="sumber_data" x-model="sumberData" class="w-full border-stone-200 rounded-xl shadow-[inset_0_2px_4px_rgba(0,0,0,0.02)] focus:ring-[#043d2e]/20 focus:border-[#043d2e] text-[13px] py-3 bg-stone-50 transition-all font-bold text-[#043d2e]">
                        <option value="manual">Ketik Nominal Manual (Untuk Saldo Siluman / Saldo Bawaan)</option>
                        <option value="otomatis">Integrasi Live (Dihitung Otomatis dari Transaksi Sistem)</option>
                    </select>
                </div>

                <div x-show="sumberData === 'otomatis'" x-collapse class="space-y-4">
                    <div class="p-5 bg-gradient-to-br from-emerald-50 to-teal-50/30 border border-emerald-100 rounded-2xl shadow-sm space-y-4">
                        <div>
                            <label class="block text-[10px] font-black text-emerald-700 uppercase tracking-widest mb-2 flex items-center gap-1.5"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg> Pilih Mesin Kalkulator (Resolver)</label>
                            <select name="kode_otomatis" x-model="kodeOtomatis" class="w-full border-emerald-200/60 rounded-xl shadow-sm focus:ring-emerald-500/30 focus:border-emerald-500 text-[13px] py-3 bg-white font-black text-emerald-900">
                                <option value="" disabled>-- Silakan Pilih Integrasi --</option>
                                <option value="SALDO_BANK_BRK">Akumulasi Saldo Bank BRK Syariah</option>
                                <option value="SALDO_KAS_TUNAI">Akumulasi Saldo Kas Tunai</option>
                                <option value="PIUTANG_PINJAMAN">Total Piutang Berjalan (Anggota Aktif)</option>
                                <option value="PIUTANG_EKSTERNAL">Filter Piutang Lain-Lain & Pihak Luar</option>
                                <option value="DANA_RESIKO_LIVE">Akumulasi Dana Resiko Pinjaman</option>
                                <option value="SIMPANAN_ANGGOTA_KUSTOM">Total Simpanan Anggota (Berdasarkan Jenisnya)</option>
                                <option value="SHU_TAHUN_BERJALAN">Laba/Rugi Bersih Tahun Ini (SHU)</option>
                            </select>
                        </div>

                        <div x-show="kodeOtomatis === 'PIUTANG_EKSTERNAL'" x-collapse>
                            <div class="space-y-4 bg-white p-4 rounded-xl border border-emerald-100 shadow-sm mt-2">
                                <p class="text-[11px] font-black uppercase text-emerald-600 tracking-wider flex items-center gap-1.5"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg> Filter Piutang Eksternal</p>
                                <div>
                                    <label class="block text-[10px] sm:text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Kategori Peminjam</label>
                                    <select name="kategori_peminjam" class="w-full border-stone-200 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500 text-[13px] bg-stone-50 py-2">
                                        <option value="">Semua Kategori (Global)</option>
                                        <option value="anggota">Anggota (Periode Lalu)</option>
                                        <option value="pengurus">Pengurus</option>
                                        <option value="pihak_ketiga">Pihak Ketiga</option>
                                        <option value="instansi">Instansi</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] sm:text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Alokasi Tahun Pinjam</label>
                                    <select name="tahun_pinjam" class="w-full border-stone-200 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500 text-[13px] bg-stone-50 py-2">
                                        <option value="">Semua Tahun (Digabung)</option>
                                        @for($i = now()->year; $i >= 2015; $i--)
                                            <option value="{{ $i }}">Hanya Tahun {{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div x-show="kodeOtomatis === 'SIMPANAN_ANGGOTA_KUSTOM'" x-collapse>
                            <div class="space-y-4 bg-white p-4 rounded-xl border border-emerald-100 shadow-sm mt-2">
                                <label class="block text-[11px] font-black uppercase text-emerald-600 tracking-wider mb-1.5 flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Tarik Dari Simpanan Anggota
                                </label>
                                <div>
                                    <label class="block text-[10px] sm:text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Klasifikasi Jenis Simpanan</label>
                                    <select name="jenis_simpanan_kode" class="w-full border-stone-200 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500 text-[13px] bg-stone-50 py-2">
                                        <option value="">-- Wajib Pilih Jenis --</option>
                                        @foreach($jenisSimpananList as $kjs)
                                            <option value="{{ $kjs->kode }}">{{ $kjs->nama }} ({{ $kjs->kode }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div x-show="sumberData === 'manual' || ['SALDO_BANK_BRK','SALDO_KAS_TUNAI','SIMPANAN_LIVE_POKOK','SIMPANAN_LIVE_WAJIB','SIMPANAN_LIVE_SWP'].includes(kodeOtomatis)" x-collapse>
                    <label class="block text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1.5" x-text="sumberData === 'manual' ? 'Nominal Manual' : 'Saldo Awal (Opening Balance)'"></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <span class="text-stone-400 font-bold">Rp</span>
                        </div>
                        <input type="number" name="nominal_manual" value="0" class="w-full pl-11 border-stone-200 rounded-xl shadow-sm focus:ring-[#043d2e]/20 focus:border-[#043d2e] font-mono text-base font-bold bg-stone-50 py-2.5" min="0" step="1">
                    </div>
                    <p class="text-[10px] text-stone-400 mt-1 uppercase" x-show="sumberData !== 'manual'">Angka ini akan dijumlahkan dengan akumulasi riwayat sistem.</p>
                </div>
            </div>

        </form>

        <x-slot name="footer">
            <button type="button" @click="$dispatch('close-modal', 'modal-tambah-neraca')" class="px-5 py-2.5 text-sm font-bold text-stone-500 hover:text-stone-700 hover:bg-stone-100 rounded-xl transition-all">Batal</button>
            <button type="submit" form="form-tambah-neraca" class="px-6 py-2.5 text-sm font-bold text-white bg-[#043d2e] hover:bg-[#065a45] rounded-xl transition-all shadow-sm flex items-center gap-2">
                <svg class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Simpan Parameter
            </button>
        </x-slot>
    </x-modal>

    {{-- MODAL TAMBAH PHU --}}
    <x-modal name="modal-tambah-phu" title="Buat Akun Laba/Rugi Baru (Kalkulasi {{ $tahun }})" maxWidth="lg">
        <form id="form-tambah-phu" action="{{ route('keuangan.parameter.phu.store') }}" method="POST" class="contents" x-data="{ sumberData: 'manual', kodeOtomatis: '' }">
            @csrf
            <input type="hidden" name="tahun" value="{{ $tahun }}">
            <div class="space-y-6">
                <div>
                    <label class="block text-[11px] font-black text-stone-500 uppercase tracking-wider mb-2">Nama Akun Laba/Rugi</label>
                    <input type="text" name="nama" class="w-full border-stone-200 rounded-xl shadow-[inset_0_2px_4px_rgba(0,0,0,0.02)] focus:ring-emerald-500/20 focus:border-emerald-600 text-[13px] py-3 bg-stone-50 transition-all font-bold text-stone-700" placeholder="Contoh: Pendapatan Bunga, Biaya ATK..." required>
                </div>

                <div>
                    <label class="block text-[11px] font-black text-stone-500 uppercase tracking-wider mb-2">Pilih Kelompok Akun</label>
                    <select name="tipe" class="w-full border-stone-200 rounded-xl shadow-[inset_0_2px_4px_rgba(0,0,0,0.02)] focus:ring-emerald-500/20 focus:border-emerald-600 text-[13px] py-3 bg-stone-50 transition-all font-bold text-stone-700">
                        <option value="pendapatan">Pendapatan (Pemasukan Uang)</option>
                        <option value="beban">Beban (Pengeluaran Operasional)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-black text-stone-500 uppercase tracking-wider mb-2">Sumber Nominal Keuangan</label>
                    <select name="sumber_data" x-model="sumberData" class="w-full border-stone-200 rounded-xl shadow-[inset_0_2px_4px_rgba(0,0,0,0.02)] focus:ring-emerald-500/20 focus:border-emerald-600 text-[13px] py-3 bg-stone-50 transition-all font-bold text-emerald-700">
                        <option value="manual">Ketik Nominal Manual (Saldo Pasti / Tetap)</option>
                        <option value="otomatis">Integrasi Live (Dihitung Otomatis Sistem)</option>
                    </select>
                </div>

                <div x-show="sumberData === 'otomatis'" x-collapse class="space-y-4">
                    <div class="p-5 bg-gradient-to-br from-emerald-50 to-teal-50/30 border border-emerald-100 rounded-2xl shadow-sm space-y-4">
                        <div>
                            <label class="block text-[10px] font-black text-emerald-700 uppercase tracking-widest mb-2 flex items-center gap-1.5"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg> Pilih Mesin Kalkulator (Resolver)</label>
                            <select name="kode_otomatis" x-model="kodeOtomatis" class="w-full border-emerald-200/60 rounded-xl shadow-sm focus:ring-emerald-500/30 focus:border-emerald-500 text-[13px] py-3 bg-white font-black text-emerald-900">
                                <option value="" disabled>-- Silakan Pilih Integrasi --</option>
                                <optgroup label="Sistem Pemasukan">
                                    <option value="PENDAPATAN_JASA_PINJAMAN">Laba (Jasa/Bunga) dari Angsuran Pinjaman</option>
                                    <option value="PENDAPATAN_PROVISI">Biaya Admin (Provisi) dari Pencairan</option>
                                    <option value="PENDAPATAN_DANA_RESIKO">Pendapatan Dana Resiko</option>
                                </optgroup>
                                <optgroup label="Sistem Pengeluaran Kas">
                                    <option value="PHU_BEBAN_OPERASIONAL">Total Pengeluaran Kas Operasional Koperasi</option>
                                </optgroup>
                            </select>
                        </div>
                    </div>
                </div>

                <div x-show="sumberData === 'manual'" x-collapse>
                    <label class="block text-[11px] font-black text-stone-500 uppercase tracking-wider mb-2">Isi Nominal Manual</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-2 pl-3 flex items-center pointer-events-none">
                            <span class="text-stone-400 font-bold">Rp</span>
                        </div>
                        <input type="number" name="nominal_manual" value="0" class="w-full pl-12 border-stone-200 rounded-xl shadow-[inset_0_2px_4px_rgba(0,0,0,0.02)] focus:ring-emerald-500/20 focus:border-emerald-600 font-mono text-base font-bold bg-stone-50 py-3 transition-all" min="0" step="1">
                    </div>
                </div>
            </div>

        </form>

        <x-slot name="footer">
            <button type="button" @click="$dispatch('close-modal', 'modal-tambah-phu')" class="px-5 py-2.5 text-[13px] font-bold text-stone-500 hover:text-stone-800 hover:bg-stone-100 rounded-xl transition-all">Batalkan</button>
            <button type="submit" form="form-tambah-phu" class="px-6 py-2.5 text-[13px] font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl transition-all shadow-sm flex items-center gap-2">
                <svg class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Simpan Akun Laba/Rugi
            </button>
        </x-slot>
    </x-modal>

</div>
@endsection
