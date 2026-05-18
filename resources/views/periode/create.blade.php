@extends('layouts.app')

@section('title', 'Buka Periode Pinjaman Baru')
@section('subtitle', 'Buat periode & generate link pengajuan')

@section('actions')
<x-back-button fallback="{{ route('periode.index') }}" />
@endsection

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden">
        <form method="POST" action="{{ route('periode.store') }}" class="divide-y divide-stone-100 flex flex-col" x-data="periodeForm()">
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

                {{-- Bulan Potongan TPP --}}
                <div class="space-y-4 p-5 bg-stone-50 rounded-xl border border-stone-200">
                    <h3 class="text-[11px] uppercase tracking-widest font-bold text-stone-600 flex items-center gap-2">
                        <svg class="w-4 h-4 text-stone-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Rentang Potongan TPP
                    </h3>
                    <p class="text-[12px] text-stone-500 leading-relaxed -mt-1">Tentukan bulan awal dan akhir potongan TPP. Tenor yang tersedia akan otomatis dihitung dari rentang ini.</p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        {{-- Bulan Potongan Awal --}}
                        <div class="space-y-1.5">
                            <label for="bulan_potongan_awal" class="text-[10px] font-bold text-stone-500 uppercase tracking-wider block">Bulan Potongan Awal <span class="text-red-500">*</span></label>
                            <select id="bulan_potongan_awal" name="bulan_potongan_awal" required x-model="bulanAwal"
                                    class="w-full px-4 py-3 bg-white border border-stone-200 rounded-xl text-stone-800 font-bold focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] outline-none transition-all shadow-sm cursor-pointer appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23a8a29e%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E')] bg-[length:10px_10px] bg-no-repeat bg-[position:right_12px_center] pr-8">
                                @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ old('bulan_potongan_awal', 5) == $i ? 'selected' : '' }}>
                                    {{ nama_bulan($i) }}
                                </option>
                                @endfor
                            </select>
                            @error('bulan_potongan_awal')
                            <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Bulan Potongan Akhir --}}
                        <div class="space-y-1.5">
                            <label for="bulan_potongan_akhir" class="text-[10px] font-bold text-stone-500 uppercase tracking-wider block">Bulan Potongan Akhir <span class="text-red-500">*</span></label>
                            <select id="bulan_potongan_akhir" name="bulan_potongan_akhir" required x-model="bulanAkhir"
                                    class="w-full px-4 py-3 bg-white border border-stone-200 rounded-xl text-stone-800 font-bold focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] outline-none transition-all shadow-sm cursor-pointer appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23a8a29e%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E')] bg-[length:10px_10px] bg-no-repeat bg-[position:right_12px_center] pr-8">
                                @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ old('bulan_potongan_akhir', 11) == $i ? 'selected' : '' }}>
                                    {{ nama_bulan($i) }}
                                </option>
                                @endfor
                            </select>
                            @error('bulan_potongan_akhir')
                            <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Tenor Preview --}}
                    <div class="flex items-center gap-3 p-3 bg-white rounded-xl border border-stone-200">
                        <div class="relative flex h-2 w-2 shrink-0">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#043d2e] opacity-40"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-[#043d2e]"></span>
                        </div>
                        <p class="text-[12px] font-medium text-stone-600">
                            Tenor tersedia: <span class="font-bold text-[#043d2e]" x-text="tenorTersedia + ' bulan'"></span>
                            <span class="text-stone-400 ml-1" x-text="'(' + namaBulan(bulanAwal) + ' — ' + namaBulan(bulanAkhir) + ')'"></span>
                        </p>
                    </div>
                </div>

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
                        <label for="tanggal_tutup" class="text-[11px] font-bold text-stone-500 uppercase tracking-wider block">Tanggal Tutup <span class="text-red-500">*</span></label>
                        <x-datepicker name="tanggal_tutup" :value="old('tanggal_tutup')" placeholder="Pilih Tanggal" :required="true" />
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

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('periodeForm', () => ({
        bulanAwal: {{ old('bulan_potongan_awal', 5) }},
        bulanAkhir: {{ old('bulan_potongan_akhir', 11) }},

        get tenorTersedia() {
            const awal = parseInt(this.bulanAwal);
            const akhir = parseInt(this.bulanAkhir);
            return Math.max(0, akhir - awal + 1);
        },

        namaBulan(num) {
            const bulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            return bulan[parseInt(num)] || '';
        }
    }));
});
</script>
@endpush
@endsection
