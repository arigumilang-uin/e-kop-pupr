@extends('layouts.app')

@section('title', 'Pengeluaran Kas Manual')
@section('subtitle', 'Catat beban operasional, belanja, dan pengeluaran lain di luar pinjaman anggota')

@section('actions')
    @can('pengeluaran.create')
    <button @click="$dispatch('open-modal', 'modal-kategori')" class="px-4 py-2.5 bg-white border border-stone-200 text-stone-700 rounded-xl text-sm font-bold hover:bg-stone-50 transition-colors shadow-sm flex items-center justify-center gap-2">
        <svg class="w-4 h-4 text-stone-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
        <span>Master Kategori</span>
    </button>
    <button @click="$dispatch('open-modal', 'modal-pengeluaran')" class="px-4 py-2.5 bg-[#043d2e] hover:bg-[#043d2e]/90 text-white rounded-xl text-sm font-bold transition-colors shadow-sm flex items-center justify-center gap-2">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        <span>Catat Pengeluaran</span>
    </button>
    @endcan
@endsection

@section('content')
<div class="space-y-6">

    {{-- Filter & Table Container --}}
    <div x-data="pengeluaranFilter()" class="flex flex-col gap-5">
        {{-- Filter Sticky Bar Component --}}
        <x-filter-bar searchPlaceholder="Cari keterangan pengeluaran..." x-model="q">
            <x-slot name="trailing">
                <div class="flex items-center gap-2.5 px-4 py-2.5 bg-white border border-stone-200 rounded-xl shadow-sm">
                    <div class="relative flex h-2 w-2">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#043d2e] opacity-40"></span>
                      <span class="relative inline-flex rounded-full h-2 w-2 bg-[#043d2e]"></span>
                    </div>
                    <span class="text-sm font-medium text-stone-600">
                        Total Pengeluaran Kas: <span class="font-bold text-stone-900 ml-0.5">{{ format_rupiah($totalPengeluaran) }}</span>
                    </span>
                </div>
            </x-slot>
            <x-slot name="indicator">
                <template x-if="activeFiltersCount > 0">
                    <span class="absolute -top-1 -right-1 w-3.5 h-3.5 bg-red-500 border-2 border-white rounded-full shadow-sm"></span>
                </template>
            </x-slot>
            
            <x-slot name="filters">
                <div class="flex flex-col gap-1.5">
                    <label class="text-[11px] font-bold text-stone-500 uppercase tracking-wider">Kategori</label>
                    <select x-model="kategori_id" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-200 rounded-xl text-sm focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] outline-none text-stone-700 cursor-pointer appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23a8a29e%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E')] bg-[length:10px_10px] bg-no-repeat bg-[position:right_12px_center] pr-8 shadow-sm">
                        <option value="">Semua Kategori</option>
                        @foreach($kategori as $kat)
                            <option value="{{ $kat->id }}">{{ $kat->nama }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Nominal --}}
                <div class="flex flex-col gap-1.5">
                    <label class="text-[11px] font-bold text-stone-500 uppercase tracking-wider">Nominal</label>
                    <select x-model="nominal" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-200 rounded-xl text-sm focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] outline-none text-stone-700 cursor-pointer appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23a8a29e%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E')] bg-[length:10px_10px] bg-no-repeat bg-[position:right_12px_center] pr-8 shadow-sm">
                        <option value="">Semua Nominal</option>
                        @foreach($nominals as $nom)
                            <option value="{{ $nom }}">{{ format_rupiah($nom) }}</option>
                        @endforeach
                    </select>
                </div>
                
                {{-- Bulan --}}
                <div class="flex flex-col gap-1.5">
                    <label class="text-[11px] font-bold text-stone-500 uppercase tracking-wider">Bulan</label>
                    <select x-model="bulan" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-200 rounded-xl text-sm focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] outline-none text-stone-700 cursor-pointer appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23a8a29e%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E')] bg-[length:10px_10px] bg-no-repeat bg-[position:right_12px_center] pr-8 shadow-sm">
                        <option value="">Semua Bulan</option>
                        @foreach(range(1, 12) as $m)
                            <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}">{{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Tahun --}}
                <div class="flex flex-col gap-1.5">
                    <label class="text-[11px] font-bold text-stone-500 uppercase tracking-wider">Tahun</label>
                    <select x-model="tahun" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-200 rounded-xl text-sm focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] outline-none text-stone-700 cursor-pointer appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23a8a29e%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E')] bg-[length:10px_10px] bg-no-repeat bg-[position:right_12px_center] pr-8 shadow-sm">
                        <option value="">Semua Tahun</option>
                        @foreach(range(date('Y'), date('Y') - 5) as $y)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                
                {{-- Reset Button --}}
                <button type="button" @click="resetFilter()" x-show="activeFiltersCount > 0" class="mt-2 w-full py-2.5 bg-red-50 text-red-600 hover:bg-red-100 rounded-xl text-sm font-bold transition-colors border border-red-200">
                    Reset Filter
                </button>
            </x-slot>
        </x-filter-bar>

        <div id="table-content-container" class="relative">
            <x-table>
                <x-table.thead>
                    <tr>
                        <x-table.th>Tanggal</x-table.th>
                        <x-table.th>Kategori</x-table.th>
                        <x-table.th>Keterangan</x-table.th>
                        <x-table.th class="text-right">Nominal</x-table.th>
                        @can('pengeluaran.delete')
                        <x-table.th class="text-right">Aksi</x-table.th>
                        @endcan
                    </tr>
                </x-table.thead>
                <x-table.tbody>
                    @forelse($pengeluaran as $p)
                    <x-table.tr class="hover:bg-stone-50/50 transition-colors">
                        <x-table.td class="align-top">
                            <div class="flex flex-col gap-0.5">
                                <span class="font-bold text-stone-800 text-[13px]">{{ $p->tanggal->translatedFormat('d M Y') }}</span>
                            </div>
                        </x-table.td>
                        <x-table.td class="align-top">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-[#043d2e]/10 text-[#043d2e] border border-[#043d2e]/20">
                                {{ $p->kategori->nama ?? '-' }}
                            </span>
                        </x-table.td>
                        <x-table.td class="align-top text-[13px] font-medium text-stone-600">
                            {{ $p->keterangan }}
                        </x-table.td>
                        <x-table.td class="align-top text-right font-mono font-bold text-stone-800 whitespace-nowrap text-[13px]">
                            {{ format_rupiah($p->nominal) }}
                        </x-table.td>
                        @can('pengeluaran.delete')
                        <x-table.td class="whitespace-nowrap text-right align-top">
                            <x-action-dropdown>
                                <form action="{{ route('pengeluaran.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus catatan pengeluaran ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <x-action-dropdown-item type="button" color="red" onclick="this.closest('form').submit()" icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>'>
                                        Hapus
                                    </x-action-dropdown-item>
                                </form>
                            </x-action-dropdown>
                        </x-table.td>
                        @endcan
                    </x-table.tr>
                    @empty
                    <x-table.tr>
                        <x-table.td colspan="{{ auth()->user()->can('pengeluaran.delete') ? 5 : 4 }}" class="px-6 py-12 text-center text-stone-500 font-medium text-sm">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="w-8 h-8 text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                Belum ada riwayat pengeluaran kas.
                            </div>
                        </x-table.td>
                    </x-table.tr>
                    @endforelse
                </x-table.tbody>
            </x-table>

            @if($pengeluaran->hasPages())
            <div class="px-6 py-4 border-t border-stone-200 bg-stone-50/50">
                {{ $pengeluaran->links() }}
            </div>
            @endif
            
            {{-- Loading Overlay --}}
            <div x-show="loading" x-transition.opacity class="absolute inset-0 bg-white/60 backdrop-blur-[1px] flex items-center justify-center z-20">
                <div class="flex items-center gap-3 px-5 py-3 bg-white rounded-2xl shadow-xl border border-stone-200">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-[#043d2e]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="font-bold text-stone-700 text-sm">Memuat data...</span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Catat Pengeluaran --}}
<x-modal name="modal-pengeluaran" title="Catat Pengeluaran Baru" maxWidth="xl">
    <form id="form-catat-pengeluaran" action="{{ route('pengeluaran.store') }}" method="POST" x-data="pengeluaranForm()" class="contents">
        @csrf
        <div class="-mx-6 -mt-5 px-6 py-4 bg-stone-50 border-b border-stone-200 mb-5">
            <label class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-1.5">Tanggal Keluar (Berlaku untuk semua catatan di bawah)</label>
            <x-datepicker name="tanggal" :value="old('tanggal', date('Y-m-d'))" :required="true" />
            @error('tanggal') <p class="text-[11px] text-red-500 mt-1 font-medium">{{ $message }}</p> @enderror
        </div>
        
        <div class="space-y-5">
            <template x-for="(item, index) in items" :key="index">
                <div class="relative p-5 rounded-2xl border border-stone-200 bg-stone-50/50">
                    <button type="button" @click="removeItem(index)" x-show="items.length > 1" class="absolute -top-3 -right-3 w-7 h-7 bg-white border border-red-200 text-red-500 rounded-full flex items-center justify-center hover:bg-red-50 hover:text-red-600 transition-colors shadow-sm z-10" title="Hapus Baris">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                    </button>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-1.5">Kategori <span x-text="index + 1"></span></label>
                            <select x-model="item.kategori_id" :name="`pengeluaran[${index}][kategori_pengeluaran_id]`" required class="w-full px-4 py-2.5 rounded-xl border border-stone-300 focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] text-sm text-stone-700 bg-white outline-none transition-all cursor-pointer appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23a8a29e%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E')] bg-[length:10px_10px] bg-no-repeat bg-[position:right_12px_center] pr-8 shadow-sm">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($kategori as $kat)
                                <option value="{{ $kat->id }}">{{ $kat->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-1.5">Nominal (Rp)</label>
                            <input type="number" x-model="item.nominal" :name="`pengeluaran[${index}][nominal]`" required min="1" class="w-full px-4 py-2.5 rounded-xl border border-stone-300 focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] text-sm text-stone-700 bg-white outline-none transition-all font-mono" placeholder="150000">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-1.5">Sumber Dana</label>
                            <select x-model="item.sumber_dana" :name="`pengeluaran[${index}][sumber_dana]`" required class="w-full px-4 py-2.5 rounded-xl border border-stone-300 focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] text-sm text-stone-700 bg-white outline-none transition-all cursor-pointer appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23a8a29e%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E')] bg-[length:10px_10px] bg-no-repeat bg-[position:right_12px_center] pr-8 shadow-sm">
                                <option value="brk">Bank BRK Syariah</option>
                                <option value="kas">Kas Tunai Bendahara</option>
                            </select>
                        </div>
                        <div class="md:col-span-3">
                            <label class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-1.5">Keterangan / Tujuan</label>
                            <input type="text" x-model="item.keterangan" :name="`pengeluaran[${index}][keterangan]`" required class="w-full px-4 py-2.5 rounded-xl border border-stone-300 focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] text-sm text-stone-700 bg-white outline-none transition-all" placeholder="Contoh: Beli kertas A4 2 rim">
                        </div>
                    </div>
                </div>
            </template>
            
            <button type="button" @click="addItem()" class="w-full py-3 border-2 border-dashed border-stone-300 rounded-xl text-stone-500 font-bold text-sm hover:bg-stone-50 hover:text-[#043d2e] hover:border-[#043d2e]/30 transition-all flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Baris Pengeluaran
            </button>
            
            @if($errors->has('pengeluaran') || $errors->has('pengeluaran.*'))
            <div class="p-3 bg-red-50 text-red-600 rounded-xl text-[11px] font-medium border border-red-200">
                Mohon periksa kembali form pengeluaran Anda. Pastikan semua field terisi dengan benar.
            </div>
            @endif
        </div>

        <x-slot name="footer">
            <div class="flex-1 text-sm font-bold text-stone-600 flex items-center gap-2">
                Total: <span class="font-mono text-lg text-[#043d2e]" x-text="new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(totalNominal)"></span>
            </div>
            <button type="button" @click="$dispatch('close-modal', 'modal-pengeluaran')" class="px-4 py-2.5 text-sm font-bold text-stone-500 hover:text-stone-800 hover:bg-stone-200/50 rounded-xl transition-all">Batal</button>
            <button type="submit" form="form-catat-pengeluaran" class="px-6 py-2.5 bg-[#043d2e] hover:bg-[#043d2e]/90 text-white text-sm font-bold rounded-xl shadow-sm transition-all active:scale-95">Simpan Semua</button>
        </x-slot>
    </form>
</x-modal>

{{-- Modal Tambah Kategori --}}
<x-modal name="modal-kategori" title="Tambah Master Kategori" maxWidth="md">
    <form id="form-tambah-kategori" action="{{ route('pengeluaran.kategori.store') }}" method="POST" class="contents">
        @csrf
        <div class="space-y-4">
            <div class="space-y-1.5">
                <label class="text-[11px] font-bold text-stone-500 uppercase tracking-wider block">Nama Kategori</label>
                <input type="text" name="nama" required class="w-full px-4 py-3 bg-stone-50 border border-stone-200 rounded-xl text-sm focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] text-stone-700 outline-none transition-all shadow-sm" placeholder="Contoh: ATK">
            </div>
            
            <div class="space-y-1.5">
                <label class="text-[11px] font-bold text-stone-500 uppercase tracking-wider block">Jenis Pengeluaran (Penting!)</label>
                <div class="p-3 bg-red-50 border border-red-100 rounded-xl mb-3 text-red-600 text-xs leading-relaxed font-medium">Hati-hati: Salah pilih Jenis akan menyebabkan Bug di Laba/Rugi.</div>
                <select name="jenis" required class="w-full px-4 py-3 bg-stone-50 border border-stone-200 rounded-xl text-sm focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] text-stone-700 outline-none transition-all shadow-sm cursor-pointer border-r-8 border-transparent">
                    <option value="beban">Beban / Belanja Operasional (MENGURANGI SHU/Laba)</option>
                    <option value="aset">Pembelian Aset / Inventaris Koperasi (TIDAK Mengurangi SHU)</option>
                </select>
            </div>
        </div>

        <x-slot name="footer">
            <button type="button" @click="$dispatch('close-modal', 'modal-kategori')" class="px-4 py-2.5 text-sm font-bold text-stone-500 hover:text-stone-800 hover:bg-stone-200/50 rounded-xl transition-all">Batal</button>
            <button type="submit" form="form-tambah-kategori" class="px-6 py-2.5 bg-[#043d2e] hover:bg-[#043d2e]/90 text-white text-sm font-bold rounded-xl shadow-sm transition-all active:scale-95">Simpan Kategori</button>
        </x-slot>
    </form>
</x-modal>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('pengeluaranForm', () => ({
            items: [{ kategori_id: '', nominal: '', keterangan: '', sumber_dana: 'brk' }],
            
            addItem() {
                this.items.push({ kategori_id: '', nominal: '', keterangan: '', sumber_dana: 'brk' });
            },
            
            removeItem(index) {
                if(this.items.length > 1) {
                    this.items.splice(index, 1);
                }
            },
            
            get totalNominal() {
                return this.items.reduce((acc, curr) => {
                    const val = parseFloat(curr.nominal);
                    return acc + (isNaN(val) ? 0 : val);
                }, 0);
            }
        }));

        Alpine.data('pengeluaranFilter', () => ({
            q: new URLSearchParams(location.search).get('q') || '',
            kategori_id: new URLSearchParams(location.search).get('kategori_id') || '',
            nominal: new URLSearchParams(location.search).get('nominal') || '',
            bulan: new URLSearchParams(location.search).get('bulan') || '',
            tahun: new URLSearchParams(location.search).get('tahun') || '',
            loading: false,
            timeout: null,
            abortController: null,
            
            init() {
                this.$watch('q', () => this.debouncedFetch());
                this.$watch('kategori_id', () => this.fetchData());
                this.$watch('nominal', () => this.fetchData());
                this.$watch('bulan', () => this.fetchData());
                this.$watch('tahun', () => this.fetchData());
            },

            get activeFiltersCount() {
                let count = 0;
                if (this.q !== '') count++;
                if (this.kategori_id !== '') count++;
                if (this.nominal !== '') count++;
                if (this.bulan !== '') count++;
                if (this.tahun !== '') count++;
                return count;
            },
            
            resetFilter() {
                this.q = '';
                this.kategori_id = '';
                this.nominal = '';
                this.bulan = '';
                this.tahun = '';
                this.fetchData();
            },

            debouncedFetch() {
                clearTimeout(this.timeout);
                this.timeout = setTimeout(() => {
                    this.fetchData();
                }, 400);
            },

            async fetchData(targetUrl = null) {
                this.loading = true;
                
                if (this.abortController) {
                    this.abortController.abort();
                }
                this.abortController = new AbortController();
                
                let url = targetUrl;
                if (!url) {
                    const params = new URLSearchParams();
                    if (this.q !== '') params.append('q', this.q);
                    if (this.kategori_id !== '') params.append('kategori_id', this.kategori_id);
                    if (this.nominal !== '') params.append('nominal', this.nominal);
                    if (this.bulan !== '') params.append('bulan', this.bulan);
                    if (this.tahun !== '') params.append('tahun', this.tahun);
                    
                    url = `${window.location.pathname}?${params.toString()}`;
                }

                try {
                    window.history.pushState({}, '', url);

                    const response = await fetch(url, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                        signal: this.abortController.signal
                    });
                    
                    const html = await response.text();
                    
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newContent = doc.getElementById('table-content-container');
                    
                    if (newContent) {
                        document.getElementById('table-content-container').innerHTML = newContent.innerHTML;
                    }
                } catch (error) {
                    if (error.name !== 'AbortError') {
                        console.error('Failed to fetch data', error);
                    }
                } finally {
                    this.loading = false;
                }
            }
        }));
    });
</script>
@endpush
@endsection
