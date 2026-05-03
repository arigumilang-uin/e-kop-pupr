@extends('layouts.app')

@section('title', 'Edit Periode Pinjaman')
@section('subtitle', $periode->nama_periode)

@section('actions')
<a href="{{ route('periode.show', $periode) }}" class="py-2.5 px-4 rounded-xl bg-white border border-stone-200 hover:bg-stone-50 text-stone-700 text-sm font-bold transition-colors flex items-center gap-2 shadow-sm">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
    </svg>
    Kembali
</a>
@endsection

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden">

        {{-- Banner peringatan jika ada pengajuan --}}
        @if($adaPengajuan)
        <div class="p-4 bg-amber-50 border-b border-amber-200 flex items-start gap-3">
            <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <div>
                <p class="text-sm font-bold text-amber-800">Periode ini sudah memiliki pengajuan pinjaman</p>
                <p class="text-[13px] font-medium text-amber-600 mt-0.5 leading-relaxed">Untuk menjaga integritas data, hanya <strong>Nama Periode</strong>, <strong>Tanggal Tutup</strong>, dan <strong>Catatan Tambahan</strong> yang dapat diubah. Field konfigurasi lainnya dikunci secara aman oleh sistem.</p>
            </div>
        </div>
        @endif

        <form method="POST" action="{{ route('periode.update', $periode) }}" class="divide-y divide-stone-100 flex flex-col">
            @csrf
            @method('PUT')

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
                    <input type="text" id="nama_periode" name="nama_periode" value="{{ old('nama_periode', $periode->nama_periode) }}" required
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
                        <input type="number" id="tahun" name="tahun" value="{{ old('tahun', $periode->tahun) }}" required min="2020" max="2050"
                               class="w-full px-4 py-3 border border-stone-200 rounded-xl text-stone-800 font-bold focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] outline-none transition-all shadow-sm
                                      {{ $adaPengajuan ? 'bg-stone-100 cursor-not-allowed text-stone-500' : 'bg-stone-50' }}"
                               {{ $adaPengajuan ? 'disabled' : '' }}>
                        @error('tahun')
                        <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Batas Bulan Pelunasan --}}
                    <div class="space-y-1.5">
                        <label for="batas_bulan_pelunasan" class="text-[11px] font-bold text-stone-500 uppercase tracking-wider block">Batas Bulan Pelunasan <span class="text-red-500">*</span></label>
                        <select id="batas_bulan_pelunasan" name="batas_bulan_pelunasan" required
                                class="w-full px-4 py-3 border border-stone-200 rounded-xl text-stone-800 font-bold focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] outline-none transition-all shadow-sm
                                       {{ $adaPengajuan ? 'bg-stone-100 cursor-not-allowed text-stone-500 opacity-90' : 'bg-stone-50 cursor-pointer' }}"
                                {{ $adaPengajuan ? 'disabled' : '' }}>
                            @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ old('batas_bulan_pelunasan', $periode->batas_bulan_pelunasan) == $i ? 'selected' : '' }}>
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
                    :checked="old('angsuran_bulan_berjalan', $periode->angsuran_bulan_berjalan)"
                    :disabled="$adaPengajuan"
                >
                    <x-slot name="label">Angsuran Dimulai dari Bulan Pengajuan</x-slot>
                    <x-slot name="description">
                        Jika diaktifkan, angsuran pertama akan jatuh tempo di <strong>bulan yang sama</strong> saat pinjaman diajukan, sehingga tenor maksimal bertambah +1 bulan.
                        @if($adaPengajuan)
                        <span class="block text-[10px] text-stone-400 font-bold mt-1.5 uppercase tracking-wider">🔒 Terkunci — sudah ada pengajuan</span>
                        @endif
                    </x-slot>
                </x-toggle>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    {{-- Tanggal Buka --}}
                    <div class="space-y-1.5">
                        <label for="tanggal_buka" class="text-[11px] font-bold text-stone-500 uppercase tracking-wider block">Tanggal Buka <span class="text-red-500">*</span></label>
                        <x-datepicker name="tanggal_buka" :value="old('tanggal_buka', $periode->tanggal_buka->format('Y-m-d'))" :required="true" :disabled="$adaPengajuan" />
                        @error('tanggal_buka')
                        <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Tanggal Tutup --}}
                    <div class="space-y-1.5">
                        <label for="tanggal_tutup" class="text-[11px] font-bold text-stone-500 uppercase tracking-wider block">Tanggal Tutup <span class="text-stone-400 normal-case capitalize text-[10px]">(Opsional)</span></label>
                        <x-datepicker name="tanggal_tutup" :value="old('tanggal_tutup', $periode->tanggal_tutup?->format('Y-m-d'))" placeholder="Pilih Tanggal (Bisa Dikosongkan)" />
                        @error('tanggal_tutup')
                        <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Konfigurasi Nominal Pinjaman --}}
                <div class="space-y-4 p-5 rounded-xl border {{ $adaPengajuan ? 'bg-stone-50/50 border-stone-200' : 'bg-stone-50 border-stone-200' }}">
                    <h3 class="text-[11px] uppercase tracking-widest font-bold text-stone-600 flex items-center gap-2">
                        <svg class="w-4 h-4 text-stone-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        Limit Pinjaman
                        @if($adaPengajuan)
                        <span class="text-[10px] font-bold text-stone-400 bg-stone-200/50 px-2 py-[1px] rounded uppercase ml-2 border border-stone-200">— TERKUNCI</span>
                        @endif
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                        {{-- Nominal Minimum --}}
                        <div class="space-y-1.5">
                            <label for="nominal_min" class="text-[10px] font-bold text-stone-500 uppercase tracking-wider block">Minimum <span class="text-red-500">*</span></label>
                            <x-currency-input name="nominal_min" :value="old('nominal_min', $periode->nominal_min)" :required="true" :disabled="$adaPengajuan" />
                            @error('nominal_min')
                            <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Nominal Maksimum --}}
                        <div class="space-y-1.5">
                            <label for="limit_per_anggota" class="text-[10px] font-bold text-stone-500 uppercase tracking-wider block">Maksimum <span class="text-red-500">*</span></label>
                            <x-currency-input name="limit_per_anggota" :value="old('limit_per_anggota', $periode->limit_per_anggota)" :required="true" :disabled="$adaPengajuan" />
                            @error('limit_per_anggota')
                            <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Kelipatan Nominal --}}
                        <div class="space-y-1.5">
                            <label for="kelipatan_nominal" class="text-[10px] font-bold text-stone-500 uppercase tracking-wider block">Kelipatan <span class="text-red-500">*</span></label>
                            <x-currency-input name="kelipatan_nominal" :value="old('kelipatan_nominal', $periode->kelipatan_nominal)" :required="true" :disabled="$adaPengajuan" />
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
                              placeholder="Catatan internal pengingat untuk periode ini...">{{ old('catatan', $periode->catatan) }}</textarea>
                    @error('catatan')
                    <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="px-6 py-5 border-t border-stone-100 bg-stone-50 shrink-0 flex items-center justify-end gap-3">
                <a href="{{ route('periode.show', $periode) }}" class="px-4 py-2.5 text-sm font-bold text-stone-500 hover:text-stone-800 hover:bg-stone-200/50 rounded-xl transition-all">Batalkan</a>
                <button type="submit"
                        class="px-6 py-2.5 bg-[#043d2e] hover:bg-[#043d2e]/90 text-white text-sm font-bold rounded-xl shadow-sm transition-all active:scale-95 flex items-center gap-2">
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
