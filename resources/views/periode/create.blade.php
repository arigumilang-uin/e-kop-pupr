@extends('layouts.app')

@section('title', 'Buka Periode Pinjaman Baru')
@section('subtitle', 'Buat periode & generate link pengajuan')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <form method="POST" action="{{ route('periode.store') }}" class="divide-y divide-slate-100">
            @csrf

            <div class="p-6 space-y-5">
                {{-- Nama Periode --}}
                <div>
                    <label for="nama_periode" class="block text-sm font-medium text-slate-700 mb-1.5">Nama Periode <span class="text-red-500">*</span></label>
                    <input type="text" id="nama_periode" name="nama_periode" value="{{ old('nama_periode', 'Pinjaman Semester ' . (now()->month <= 6 ? 'I' : 'II') . ' ' . now()->year) }}" required
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-800
                                  focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all"
                           placeholder="Contoh: Pinjaman Semester I 2026">
                    @error('nama_periode')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    {{-- Tahun --}}
                    <div>
                        <label for="tahun" class="block text-sm font-medium text-slate-700 mb-1.5">Tahun <span class="text-red-500">*</span></label>
                        <input type="number" id="tahun" name="tahun" value="{{ old('tahun', $defaults['tahun']) }}" required min="2020" max="2050"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-800
                                      focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                        @error('tahun')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Batas Bulan Pelunasan --}}
                    <div>
                        <label for="batas_bulan_pelunasan" class="block text-sm font-medium text-slate-700 mb-1.5">Batas Bulan Pelunasan <span class="text-red-500">*</span></label>
                        <select id="batas_bulan_pelunasan" name="batas_bulan_pelunasan" required
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-800
                                       focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                            @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ old('batas_bulan_pelunasan', $defaults['batas_bulan']) == $i ? 'selected' : '' }}>
                                Bulan ke-{{ $i }} ({{ nama_bulan($i) }})
                            </option>
                            @endfor
                        </select>
                        @error('batas_bulan_pelunasan')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    {{-- Tanggal Buka --}}
                    <div>
                        <label for="tanggal_buka" class="block text-sm font-medium text-slate-700 mb-1.5">Tanggal Buka <span class="text-red-500">*</span></label>
                        <input type="date" id="tanggal_buka" name="tanggal_buka" value="{{ old('tanggal_buka', now()->format('Y-m-d')) }}" required
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-800
                                      focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                        @error('tanggal_buka')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Tanggal Tutup --}}
                    <div>
                        <label for="tanggal_tutup" class="block text-sm font-medium text-slate-700 mb-1.5">Tanggal Tutup <span class="text-slate-400">(Opsional)</span></label>
                        <input type="date" id="tanggal_tutup" name="tanggal_tutup" value="{{ old('tanggal_tutup') }}"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-800
                                      focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                        @error('tanggal_tutup')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Limit Per Anggota --}}
                <div>
                    <label for="limit_per_anggota" class="block text-sm font-medium text-slate-700 mb-1.5">Limit Pinjaman Per Anggota (Rp) <span class="text-red-500">*</span></label>
                    <input type="number" id="limit_per_anggota" name="limit_per_anggota" value="{{ old('limit_per_anggota', 50000000) }}" required min="100000" step="100000"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-800 font-mono
                                  focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                    @error('limit_per_anggota')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Catatan --}}
                <div>
                    <label for="catatan" class="block text-sm font-medium text-slate-700 mb-1.5">Catatan <span class="text-slate-400">(Opsional)</span></label>
                    <textarea id="catatan" name="catatan" rows="3"
                              class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-800
                                     focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all resize-none"
                              placeholder="Catatan internal untuk periode ini">{{ old('catatan') }}</textarea>
                    @error('catatan')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="p-6 bg-slate-50 flex items-center justify-between gap-4">
                <a href="{{ route('periode.index') }}" class="text-sm text-slate-500 hover:text-slate-700 transition-colors">← Batal</a>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-blue-600 text-white text-sm font-medium
                               hover:bg-blue-500 transition-all shadow-sm">
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
