@extends('layouts.app')

@section('title', 'Edit Periode Pinjaman')
@section('subtitle', $periode->nama_periode)

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

        {{-- Banner peringatan jika ada pengajuan --}}
        @if($adaPengajuan)
        <div class="p-4 bg-amber-50 border-b border-amber-200 flex items-start gap-3">
            <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <div>
                <p class="text-sm font-semibold text-amber-800">Periode ini sudah memiliki pengajuan pinjaman</p>
                <p class="text-xs text-amber-600 mt-0.5">Untuk menjaga integritas data, hanya <strong>Nama Periode</strong>, <strong>Tanggal Tutup</strong>, dan <strong>Catatan</strong> yang dapat diubah. Field konfigurasi lainnya dikunci.</p>
            </div>
        </div>
        @endif

        <form method="POST" action="{{ route('periode.update', $periode) }}" class="divide-y divide-slate-100">
            @csrf
            @method('PUT')

            <div class="p-6 space-y-5">
                @if(session('error'))
                <div class="p-4 rounded-xl bg-red-50 border border-red-200 flex items-start gap-3">
                    <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <p class="text-[13px] font-semibold text-red-800">{{ session('error') }}</p>
                </div>
                @endif

                {{-- Nama Periode --}}
                <div>
                    <label for="nama_periode" class="block text-sm font-medium text-slate-700 mb-1.5">Nama Periode <span class="text-red-500">*</span></label>
                    <input type="text" id="nama_periode" name="nama_periode" value="{{ old('nama_periode', $periode->nama_periode) }}" required
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
                        <input type="number" id="tahun" name="tahun" value="{{ old('tahun', $periode->tahun) }}" required min="2020" max="2050"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-800
                                      focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all
                                      {{ $adaPengajuan ? 'bg-slate-100 cursor-not-allowed' : '' }}"
                               {{ $adaPengajuan ? 'disabled' : '' }}>
                        @error('tahun')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Batas Bulan Pelunasan --}}
                    <div>
                        <label for="batas_bulan_pelunasan" class="block text-sm font-medium text-slate-700 mb-1.5">Batas Bulan Pelunasan <span class="text-red-500">*</span></label>
                        <select id="batas_bulan_pelunasan" name="batas_bulan_pelunasan" required
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-800
                                       focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all
                                       {{ $adaPengajuan ? 'bg-slate-100 cursor-not-allowed' : '' }}"
                                {{ $adaPengajuan ? 'disabled' : '' }}>
                            @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ old('batas_bulan_pelunasan', $periode->batas_bulan_pelunasan) == $i ? 'selected' : '' }}>
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
                        <input type="date" id="tanggal_buka" name="tanggal_buka" value="{{ old('tanggal_buka', $periode->tanggal_buka->format('Y-m-d')) }}" required
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-800
                                      focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all
                                      {{ $adaPengajuan ? 'bg-slate-100 cursor-not-allowed' : '' }}"
                               {{ $adaPengajuan ? 'disabled' : '' }}>
                        @error('tanggal_buka')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Tanggal Tutup --}}
                    <div>
                        <label for="tanggal_tutup" class="block text-sm font-medium text-slate-700 mb-1.5">Tanggal Tutup <span class="text-red-500">*</span></label>
                        <input type="date" id="tanggal_tutup" name="tanggal_tutup" value="{{ old('tanggal_tutup', $periode->tanggal_tutup->format('Y-m-d')) }}" required
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-800
                                      focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                        @error('tanggal_tutup')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Konfigurasi Nominal Pinjaman --}}
                <div class="space-y-4 p-4 rounded-xl border {{ $adaPengajuan ? 'bg-slate-50 border-slate-200' : 'bg-blue-50/50 border-blue-100' }}">
                    <h3 class="text-sm font-semibold text-slate-700 flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        Konfigurasi Nominal Pinjaman
                        @if($adaPengajuan)
                        <span class="text-xs text-slate-400 font-normal ml-1">— Dikunci</span>
                        @endif
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        {{-- Nominal Minimum --}}
                        <div>
                            <label for="nominal_min" class="block text-sm font-medium text-slate-700 mb-1.5">Minimum (Rp) <span class="text-red-500">*</span></label>
                            <input type="number" id="nominal_min" name="nominal_min" value="{{ old('nominal_min', $periode->nominal_min) }}" required min="100000" step="50000"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-800 font-mono
                                          focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all
                                          {{ $adaPengajuan ? 'bg-slate-100 cursor-not-allowed' : '' }}"
                                   {{ $adaPengajuan ? 'disabled' : '' }}>
                            @error('nominal_min')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Nominal Maksimum --}}
                        <div>
                            <label for="limit_per_anggota" class="block text-sm font-medium text-slate-700 mb-1.5">Maksimum (Rp) <span class="text-red-500">*</span></label>
                            <input type="number" id="limit_per_anggota" name="limit_per_anggota" value="{{ old('limit_per_anggota', $periode->limit_per_anggota) }}" required min="100000" step="100000"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-800 font-mono
                                          focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all
                                          {{ $adaPengajuan ? 'bg-slate-100 cursor-not-allowed' : '' }}"
                                   {{ $adaPengajuan ? 'disabled' : '' }}>
                            @error('limit_per_anggota')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Kelipatan Nominal --}}
                        <div>
                            <label for="kelipatan_nominal" class="block text-sm font-medium text-slate-700 mb-1.5">Kelipatan (Rp) <span class="text-red-500">*</span></label>
                            <input type="number" id="kelipatan_nominal" name="kelipatan_nominal" value="{{ old('kelipatan_nominal', $periode->kelipatan_nominal) }}" required min="50000" step="50000"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-800 font-mono
                                          focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all
                                          {{ $adaPengajuan ? 'bg-slate-100 cursor-not-allowed' : '' }}"
                                   {{ $adaPengajuan ? 'disabled' : '' }}>
                            <p class="text-slate-400 text-[11px] mt-1">Pengajuan harus kelipatan nilai ini</p>
                            @error('kelipatan_nominal')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Catatan --}}
                <div>
                    <label for="catatan" class="block text-sm font-medium text-slate-700 mb-1.5">Catatan <span class="text-slate-400">(Opsional)</span></label>
                    <textarea id="catatan" name="catatan" rows="3"
                              class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-800
                                     focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all resize-none"
                              placeholder="Catatan internal untuk periode ini">{{ old('catatan', $periode->catatan) }}</textarea>
                    @error('catatan')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="p-6 bg-slate-50 flex items-center justify-between gap-4">
                <a href="{{ route('periode.show', $periode) }}" class="text-sm text-slate-500 hover:text-slate-700 transition-colors">← Batal</a>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-blue-600 text-white text-sm font-medium
                               hover:bg-blue-500 transition-all shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
