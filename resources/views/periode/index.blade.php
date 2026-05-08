@extends('layouts.app')

@section('title', 'Periode Pinjaman')
@section('subtitle', 'Kelola periode pembukaan pinjaman & link pengajuan anggota koperasi.')

@section('actions')
    <button type="button" x-data @click="$dispatch('open-modal', 'modal-create-periode')" class="py-2.5 px-4 rounded-xl bg-[#043d2e] hover:bg-[#043d2e]/90 text-white text-sm font-bold transition-colors flex items-center gap-2 shadow-sm">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
        </svg>
        <span>Buka Periode Baru</span>
    </button>
@endsection

@section('content')
<div x-data="{ q: '{{ request('q') }}' }" @keydown.enter.prevent="window.location.search = '?q=' + q" class="flex flex-col gap-5">
    {{-- Filter Sticky Bar Component --}}
    <x-filter-bar searchPlaceholder="Cari nama periode (Tekan Enter)..." x-model="q">
        <x-slot name="trailing">
            <div class="flex items-center gap-2.5 px-4 py-2.5 bg-white border border-stone-200 rounded-xl shadow-sm">
                <div class="relative flex h-2 w-2">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#043d2e] opacity-40"></span>
                  <span class="relative inline-flex rounded-full h-2 w-2 bg-[#043d2e]"></span>
                </div>
                <span class="text-sm font-medium text-stone-600">
                    Total Pengajuan: <span class="font-bold text-stone-900 ml-0.5">{{ $totalPengajuan }}</span>
                </span>
            </div>
        </x-slot>
    </x-filter-bar>
    <div class="bg-white rounded-2xl border border-stone-200 shadow-sm flex flex-col">
        <div class="p-5 border-b border-stone-100 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-stone-800">Daftar Periode Pinjaman</h3>
                <p class="text-[13px] text-stone-500 font-medium">Rekapitulasi seluruh periode pinjaman anggota koperasi.</p>
            </div>
            <div class="px-3 py-1.5 bg-stone-50 rounded-lg border border-stone-200">
                <span class="text-[13px] font-bold text-stone-600">{{ $periodes->total() }} Data</span>
            </div>
        </div>

        <div class="relative flex-1 flex flex-col">
            <div class="flex-1 flex flex-col justify-between">
                <x-table>
                    <x-table.thead>
                        <x-table.th>Nama Periode</x-table.th>
                        <x-table.th>Rentang Aktif</x-table.th>
                        <x-table.th class="text-center">Tahun</x-table.th>
                        <x-table.th class="text-right">Maks. /Pinjaman</x-table.th>
                        <x-table.th class="text-center">Total Pengajuan</x-table.th>
                        <x-table.th class="text-right">Aksi</x-table.th>
                    </x-table.thead>
                    <x-table.tbody>
                        @forelse($periodes as $periode)
                        <x-table.tr>
                            <x-table.td>
                                <div class="flex flex-col items-start gap-1">
                                    <span class="font-bold text-stone-800">{{ $periode->nama_periode }}</span>
                                    <span class="inline-flex items-center px-1.5 py-[1px] rounded text-[9px] font-bold uppercase tracking-wider
                                        @if($periode->statusEfektif()->value === 'buka') bg-emerald-50 text-emerald-600 border border-emerald-200
                                        @elseif($periode->statusEfektif()->value === 'terjadwal') bg-amber-50 text-amber-600 border border-amber-200
                                        @elseif($periode->statusEfektif()->value === 'tutup') bg-red-50 text-red-600 border border-red-200
                                        @else bg-stone-50 text-stone-600 border border-stone-200 @endif
                                    ">
                                        {{ $periode->statusEfektif()->label() }}
                                    </span>
                                </div>
                            </x-table.td>
                            <x-table.td>
                                <span class="font-medium text-[12px] text-stone-600">
                                    {{ $periode->tanggal_buka->format('d/m/Y') }} 
                                    @if($periode->tanggal_tutup)
                                        <span class="text-stone-300 mx-1">-</span> {{ $periode->tanggal_tutup->format('d/m/Y') }}
                                    @endif
                                </span>
                            </x-table.td>
                            <x-table.td class="text-center">
                                <span class="font-bold text-[12px] text-stone-600">
                                    {{ $periode->tahun }}
                                </span>
                            </x-table.td>
                            <x-table.td class="text-right">
                                <span class="font-mono text-[13px] font-semibold text-stone-700">
                                    {{ format_rupiah($periode->limit_per_anggota) }}
                                </span>
                            </x-table.td>
                            <x-table.td class="text-center">
                                <span class="inline-flex items-center px-2 py-1 rounded bg-blue-50 text-blue-700 text-xs font-bold font-mono border border-blue-200">
                                    {{ $periode->pinjaman_count }}
                                </span>
                            </x-table.td>
                            <x-table.td class="text-right">
                                <a href="{{ route('periode.show', $periode) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[12px] font-bold text-[#043d2e] bg-[#043d2e]/5 hover:bg-[#043d2e]/10 border border-[#043d2e]/10 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Kelola Periode
                                </a>
                            </x-table.td>
                        </x-table.tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-5 py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 rounded-2xl bg-stone-50 border border-stone-200 flex items-center justify-center mb-4">
                                        <svg class="w-8 h-8 text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <h4 class="text-stone-800 font-bold mb-1">Belum Ada Periode</h4>
                                    <p class="text-sm text-stone-500 mb-5">Buat periode pinjaman pertama agar anggota dapat mengajukan pinjaman secara online.</p>
                                    <button type="button" x-data @click="$dispatch('open-modal', 'modal-create-periode')" class="py-2 px-4 rounded-xl bg-[#043d2e] hover:bg-[#043d2e]/90 text-white text-sm font-bold transition-colors flex items-center gap-2 shadow-sm">
                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        Buka Periode Baru
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </x-table.tbody>
                </x-table>
            </div>
            
            @if($periodes->hasPages())
                <div class="px-5 py-4 border-t border-stone-200 bg-stone-50 rounded-b-2xl">
                    {{ $periodes->links() }}
                </div>
            @endif
        </div>
    </div>
    
    {{-- Modal Tambah Periode --}}
    <x-modal name="modal-create-periode" title="Buka Periode Pinjaman Baru" maxWidth="2xl">
        <form id="form-create-periode" method="POST" action="{{ route('periode.store') }}" class="contents">
            @csrf

            <div class="space-y-4">
                {{-- Nama Periode --}}
                <div class="space-y-1.5">
                    <label for="nama_periode" class="text-[11px] font-bold text-stone-500 uppercase tracking-wider block">Nama Periode <span class="text-red-500">*</span></label>
                    <input type="text" id="nama_periode" name="nama_periode" value="{{ old('nama_periode', 'Pinjaman Semester ' . (now()->month <= 6 ? 'I' : 'II') . ' ' . now()->year) }}" required
                           class="w-full px-4 py-3 bg-stone-50 border border-stone-200 rounded-xl text-stone-800 font-bold focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] outline-none transition-all shadow-sm"
                           placeholder="Contoh: Pinjaman Semester I {{ now()->year }}">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Tahun --}}
                    <div class="space-y-1.5">
                        <label for="tahun" class="text-[11px] font-bold text-stone-500 uppercase tracking-wider block">Tahun Anggaran <span class="text-red-500">*</span></label>
                        <input type="number" id="tahun" name="tahun" value="{{ old('tahun', now()->year) }}" required min="2020" max="2050"
                               class="w-full px-4 py-3 bg-stone-50 border border-stone-200 rounded-xl text-stone-800 font-bold focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] outline-none transition-all shadow-sm">
                    </div>

                    {{-- Batas Bulan Pelunasan --}}
                    <div class="space-y-1.5">
                        <label for="batas_bulan_pelunasan" class="text-[11px] font-bold text-stone-500 uppercase tracking-wider block">Batas Bulan Pelunasan <span class="text-red-500">*</span></label>
                        <select id="batas_bulan_pelunasan" name="batas_bulan_pelunasan" required
                                class="w-full px-4 py-3 bg-stone-50 border border-stone-200 rounded-xl text-stone-800 font-bold focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] outline-none transition-all shadow-sm cursor-pointer">
                            @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ old('batas_bulan_pelunasan', 11) == $i ? 'selected' : '' }}>
                                Bulan ke-{{ $i }} ({{ nama_bulan($i) }})
                            </option>
                            @endfor
                        </select>
                    </div>
                </div>

                {{-- Opsi Angsuran Bulan Berjalan --}}
                <x-toggle 
                    name="angsuran_bulan_berjalan" 
                    :checked="old('angsuran_bulan_berjalan')"
                >
                    <x-slot name="label">Angsuran Dimulai dari Bulan Pengajuan</x-slot>
                    <x-slot name="description">
                        Jika diaktifkan, angsuran pertama akan jatuh tempo di bulan yang sama saat pinjaman diajukan.
                    </x-slot>
                </x-toggle>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Tanggal Buka --}}
                    <div class="space-y-1.5">
                        <label for="tanggal_buka" class="text-[11px] font-bold text-stone-500 uppercase tracking-wider block">Tanggal Buka <span class="text-red-500">*</span></label>
                        <x-datepicker name="tanggal_buka" :value="old('tanggal_buka', now()->format('Y-m-d'))" :required="true" />
                    </div>

                    {{-- Tanggal Tutup --}}
                    <div class="space-y-1.5">
                        <label for="tanggal_tutup" class="text-[11px] font-bold text-stone-500 uppercase tracking-wider block">Tanggal Tutup <span class="text-stone-400 normal-case capitalize text-[10px]">(Opsional)</span></label>
                        <x-datepicker name="tanggal_tutup" :value="old('tanggal_tutup')" placeholder="Tidak Dibatasi" />
                    </div>
                </div>

                {{-- Konfigurasi Nominal Pinjaman --}}
                <div class="space-y-4 p-4 bg-stone-50 border border-stone-200 rounded-xl">
                    <h3 class="text-[11px] uppercase tracking-widest font-bold text-stone-600 flex items-center gap-2 mb-2">
                        Limit Pinjaman
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="space-y-1.5">
                            <label for="nominal_min" class="text-[10px] font-bold text-stone-500 uppercase tracking-wider block">Minimum <span class="text-red-500">*</span></label>
                            <x-currency-input name="nominal_min" :value="old('nominal_min', 100000)" :required="true" />
                        </div>

                        <div class="space-y-1.5">
                            <label for="limit_per_anggota" class="text-[10px] font-bold text-stone-500 uppercase tracking-wider block">Maksimum <span class="text-red-500">*</span></label>
                            <x-currency-input name="limit_per_anggota" :value="old('limit_per_anggota', 50000000)" :required="true" />
                        </div>

                        <div class="space-y-1.5">
                            <label for="kelipatan_nominal" class="text-[10px] font-bold text-stone-500 uppercase tracking-wider block">Kelipatan <span class="text-red-500">*</span></label>
                            <x-currency-input name="kelipatan_nominal" :value="old('kelipatan_nominal', 100000)" :required="true" />
                        </div>
                    </div>
                </div>

                {{-- Catatan --}}
                <div class="space-y-1.5">
                    <label for="catatan" class="text-[11px] font-bold text-stone-500 uppercase tracking-wider block">Catatan Tambahan <span class="text-stone-400 normal-case capitalize text-[10px]">(Opsional)</span></label>
                    <textarea id="catatan" name="catatan" rows="2"
                              class="w-full px-4 py-3 bg-stone-50 border border-stone-200 rounded-xl text-stone-800 text-sm focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] outline-none transition-all shadow-sm resize-none"
                              placeholder="Catatan internal pengingat untuk periode ini...">{{ old('catatan') }}</textarea>
                </div>
            </div>

            <x-slot name="footer">
                <button type="button" @click="$dispatch('close-modal', 'modal-create-periode')" class="px-4 py-2.5 text-sm font-bold text-stone-500 hover:text-stone-800 hover:bg-stone-200/50 rounded-xl transition-all">Batalkan</button>
                <button type="submit" form="form-create-periode" class="px-6 py-2.5 bg-[#043d2e] hover:bg-[#043d2e]/90 text-white text-sm font-bold rounded-xl shadow-sm transition-all active:scale-95 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Buka Periode & Generate Link
                </button>
            </x-slot>
        </form>
    </x-modal>
</div>
@endsection
