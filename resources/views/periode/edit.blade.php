@extends('layouts.app')

@section('title', 'Edit Periode Pinjaman')
@section('subtitle', $periode->nama_periode)

@section('actions')
<x-back-button fallback="{{ route('periode.show', $periode) }}" />
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

        <form method="POST" action="{{ route('periode.update', $periode) }}" class="divide-y divide-stone-100 flex flex-col" x-data="periodeEditForm()">
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

                {{-- Bulan Potongan TPP --}}
                <div class="space-y-4 p-5 rounded-xl border {{ $adaPengajuan ? 'bg-stone-50/50 border-stone-200' : 'bg-stone-50 border-stone-200' }}">
                    <h3 class="text-[11px] uppercase tracking-widest font-bold text-stone-600 flex items-center gap-2">
                        <svg class="w-4 h-4 text-stone-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Rentang Potongan TPP
                        @if($adaPengajuan)
                        <span class="text-[10px] font-bold text-stone-400 bg-stone-200/50 px-2 py-[1px] rounded uppercase ml-2 border border-stone-200">— TERKUNCI</span>
                        @endif
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-stone-500 uppercase tracking-wider block">Bulan Potongan Awal <span class="text-red-500">*</span></label>
                            <select name="bulan_potongan_awal" required x-model="bulanAwal"
                                    class="w-full px-4 py-3 border border-stone-200 rounded-xl text-stone-800 font-bold focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] outline-none transition-all shadow-sm
                                           {{ $adaPengajuan ? 'bg-stone-100 cursor-not-allowed text-stone-500 opacity-90' : 'bg-white cursor-pointer' }}"
                                    {{ $adaPengajuan ? 'disabled' : '' }}>
                                @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ old('bulan_potongan_awal', $periode->bulan_potongan_awal) == $i ? 'selected' : '' }}>{{ nama_bulan($i) }}</option>
                                @endfor
                            </select>
                            @error('bulan_potongan_awal')
                            <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-stone-500 uppercase tracking-wider block">Bulan Potongan Akhir <span class="text-red-500">*</span></label>
                            <select name="bulan_potongan_akhir" required x-model="bulanAkhir"
                                    class="w-full px-4 py-3 border border-stone-200 rounded-xl text-stone-800 font-bold focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] outline-none transition-all shadow-sm
                                           {{ $adaPengajuan ? 'bg-stone-100 cursor-not-allowed text-stone-500 opacity-90' : 'bg-white cursor-pointer' }}"
                                    {{ $adaPengajuan ? 'disabled' : '' }}>
                                @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ old('bulan_potongan_akhir', $periode->bulan_potongan_akhir) == $i ? 'selected' : '' }}>{{ nama_bulan($i) }}</option>
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
                        <x-datepicker name="tanggal_buka" :value="old('tanggal_buka', $periode->tanggal_buka->format('Y-m-d'))" :required="true" :disabled="$adaPengajuan" />
                        @error('tanggal_buka')
                        <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Tanggal Tutup --}}
                    <div class="space-y-1.5">
                        <label for="tanggal_tutup" class="text-[11px] font-bold text-stone-500 uppercase tracking-wider block">Tanggal Tutup <span class="text-red-500">*</span></label>
                        <x-datepicker name="tanggal_tutup" :value="old('tanggal_tutup', $periode->tanggal_tutup?->format('Y-m-d'))" :required="true" />
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
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-stone-500 uppercase tracking-wider block">Minimum <span class="text-red-500">*</span></label>
                            <x-currency-input name="nominal_min" :value="old('nominal_min', $periode->nominal_min)" :required="true" :disabled="$adaPengajuan" />
                            @error('nominal_min')
                            <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-stone-500 uppercase tracking-wider block">Maksimum <span class="text-red-500">*</span></label>
                            <x-currency-input name="limit_per_anggota" :value="old('limit_per_anggota', $periode->limit_per_anggota)" :required="true" :disabled="$adaPengajuan" />
                            @error('limit_per_anggota')
                            <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-stone-500 uppercase tracking-wider block">Kelipatan <span class="text-red-500">*</span></label>
                            <x-currency-input name="kelipatan_nominal" :value="old('kelipatan_nominal', $periode->kelipatan_nominal)" :required="true" :disabled="$adaPengajuan" />
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
                <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('periode.show', $periode) }}" class="px-4 py-2.5 text-sm font-bold text-stone-500 hover:text-stone-800 hover:bg-stone-200/50 rounded-xl transition-all">Batalkan</a>
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

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('periodeEditForm', () => ({
        bulanAwal: {{ old('bulan_potongan_awal', $periode->bulan_potongan_awal ?? 5) }},
        bulanAkhir: {{ old('bulan_potongan_akhir', $periode->bulan_potongan_akhir ?? 11) }},

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
