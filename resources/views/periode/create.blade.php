@extends('layouts.app')

@section('title', 'Buka Periode Pinjaman Baru')
@section('subtitle', 'Buat periode & generate link pengajuan')

@section('actions')
<x-back-button fallback="{{ route('periode.index') }}" />
@endsection

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden">
        <form method="POST" action="{{ route('periode.store') }}" class="divide-y divide-stone-100 flex flex-col">
            @csrf

            <div class="p-6 space-y-5">
                @if(session('error'))
                <div class="p-4 rounded-xl bg-red-50 border border-red-200 flex items-start gap-3">
                    <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <p class="text-[13px] font-bold text-red-800">{{ session('error') }}</p>
                </div>
                @endif
                {{-- Nama Periode --}}
                <div class="space-y-1.5">
                    <label for="nama_periode" class="text-[11px] font-bold text-stone-500 uppercase tracking-wider block">Nama Periode <span class="text-red-500">*</span></label>
                    <input type="text" id="nama_periode" name="nama_periode" value="{{ old('nama_periode', 'Pinjaman Semester ' . (now()->month <= 6 ? 'I' : 'II') . ' ' . now()->year) }}" required
                           class="w-full px-4 py-3 bg-stone-50 border border-stone-200 rounded-xl text-stone-800 font-bold focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] outline-none transition-all shadow-sm"
                           placeholder="Contoh: Pinjaman Semester I 2026">
                    @error('nama_periode')
                    <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    {{-- Tahun --}}
                    <div class="space-y-1.5">
                        <label for="tahun" class="text-[11px] font-bold text-stone-500 uppercase tracking-wider block">Tahun Anggaran <span class="text-red-500">*</span></label>
                        <input type="number" id="tahun" name="tahun" value="{{ old('tahun', $defaults['tahun']) }}" required min="2020" max="2050"
                               class="w-full px-4 py-3 bg-stone-50 border border-stone-200 rounded-xl text-stone-800 font-bold focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] outline-none transition-all shadow-sm">
                        @error('tahun')
                        <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Batas Bulan Pelunasan --}}
                    <div class="space-y-1.5">
                        <label for="batas_bulan_pelunasan" class="text-[11px] font-bold text-stone-500 uppercase tracking-wider block">Batas Bulan Pelunasan <span class="text-red-500">*</span></label>
                        <select id="batas_bulan_pelunasan" name="batas_bulan_pelunasan" required
                                class="w-full px-4 py-3 bg-stone-50 border border-stone-200 rounded-xl text-stone-800 font-bold focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] outline-none transition-all shadow-sm cursor-pointer">
                            @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ old('batas_bulan_pelunasan', $defaults['batas_bulan']) == $i ? 'selected' : '' }}>
                                Bulan ke-{{ $i }} ({{ nama_bulan($i) }})
                            </option>
                            @endfor
                        </select>
                        @error('batas_bulan_pelunasan')
                        <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Opsi Angsuran Bulan Berjalan --}}
                <x-toggle 
                    name="angsuran_bulan_berjalan" 
                    :checked="old('angsuran_bulan_berjalan')"
                >
                    <x-slot name="label">Angsuran Dimulai dari Bulan Pengajuan</x-slot>
                    <x-slot name="description">
                        Jika diaktifkan, angsuran pertama akan jatuh tempo di <strong>bulan yang sama</strong> saat pinjaman diajukan, sehingga tenor maksimal bertambah +1 bulan. Misalnya: batas November, pengajuan April → tenor maks 8 bulan (April s/d November), bukan 7 bulan (Mei s/d November).
                    </x-slot>
                </x-toggle>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    {{-- Tanggal Buka --}}
                    <div class="space-y-1.5">
                        <label for="tanggal_buka" class="text-[11px] font-bold text-stone-500 uppercase tracking-wider block">Tanggal Buka <span class="text-red-500">*</span></label>
                        <x-datepicker name="tanggal_buka" :value="old('tanggal_buka', now()->format('Y-m-d'))" :required="true" />
                        @error('tanggal_buka')
                        <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Tanggal Tutup --}}
                    <div class="space-y-1.5">
                        <label for="tanggal_tutup" class="text-[11px] font-bold text-stone-500 uppercase tracking-wider block">Tanggal Tutup <span class="text-stone-400 capitalize normal-case text-[10px]">(Opsional)</span></label>
                        <x-datepicker name="tanggal_tutup" :value="old('tanggal_tutup')" placeholder="Pilih Tanggal (Bisa Dikosongkan)" />
                        @error('tanggal_tutup')
                        <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Konfigurasi Nominal Pinjaman --}}
                <div class="space-y-4 p-5 bg-stone-50 rounded-xl border border-stone-200">
                    <h3 class="text-[11px] uppercase tracking-widest font-bold text-stone-600 flex items-center gap-2">
                        <svg class="w-4 h-4 text-stone-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        Limit Pinjaman
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                        {{-- Nominal Minimum --}}
                        <div class="space-y-1.5">
                            <label for="nominal_min" class="text-[10px] font-bold text-stone-500 uppercase tracking-wider block">Minimum <span class="text-red-500">*</span></label>
                            <x-currency-input name="nominal_min" :value="old('nominal_min', 100000)" :required="true" />
                            @error('nominal_min')
                            <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Nominal Maksimum (limit_per_anggota) --}}
                        <div class="space-y-1.5">
                            <label for="limit_per_anggota" class="text-[10px] font-bold text-stone-500 uppercase tracking-wider block">Maksimum <span class="text-red-500">*</span></label>
                            <x-currency-input name="limit_per_anggota" :value="old('limit_per_anggota', 50000000)" :required="true" />
                            @error('limit_per_anggota')
                            <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Kelipatan Nominal --}}
                        <div class="space-y-1.5">
                            <label for="kelipatan_nominal" class="text-[10px] font-bold text-stone-500 uppercase tracking-wider block">Kelipatan <span class="text-red-500">*</span></label>
                            <x-currency-input name="kelipatan_nominal" :value="old('kelipatan_nominal', 100000)" :required="true" />
                            <p class="text-stone-400 text-[10px] font-bold mt-1.5 leading-tight flex items-center gap-1">
                                <svg class="w-3 h-3 text-stone-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                Syarat kelipatan pengajuan
                            </p>
                            @error('kelipatan_nominal')
                            <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Catatan --}}
                <div class="space-y-1.5">
                    <label for="catatan" class="text-[11px] font-bold text-stone-500 uppercase tracking-wider block">Catatan Tambahan <span class="text-stone-400 normal-case capitalize text-[10px]">(Opsional)</span></label>
                    <textarea id="catatan" name="catatan" rows="3"
                              class="w-full px-4 py-3 bg-stone-50 border border-stone-200 rounded-xl text-stone-800 text-sm focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] outline-none transition-all shadow-sm resize-none"
                              placeholder="Catatan internal pengingat untuk periode ini...">{{ old('catatan') }}</textarea>
                    @error('catatan')
                    <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="px-6 py-5 border-t border-stone-100 bg-stone-50 shrink-0 flex items-center justify-end gap-3">
                <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('periode.index') }}" class="px-4 py-2.5 text-sm font-bold text-stone-500 hover:text-stone-800 hover:bg-stone-200/50 rounded-xl transition-all">Batalkan</a>
                <button type="submit"
                        class="px-6 py-2.5 bg-[#043d2e] hover:bg-[#043d2e]/90 text-white text-sm font-bold rounded-xl shadow-sm transition-all active:scale-95 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Buka Periode & Generate Link
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
