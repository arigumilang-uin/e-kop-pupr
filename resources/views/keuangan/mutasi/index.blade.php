@extends('layouts.app')

@section('title', 'Mutasi Kas & Bank')
@section('subtitle', 'Pindah buku antara rekening Bank BRK Syariah dan Kas Tunai Bendahara')

@section('actions')
    @can('pengeluaran.create')
    <button @click="$dispatch('open-modal', 'modal-mutasi')" class="px-4 py-2.5 bg-[#043d2e] hover:bg-[#043d2e]/90 text-white rounded-xl text-sm font-bold transition-colors shadow-sm flex items-center justify-center gap-2">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
        <span>Catat Mutasi Baru</span>
    </button>
    @endcan
@endsection

@section('content')
<div class="space-y-6">

    {{-- Filter & Table Container --}}
    <div x-data="mutasiFilter()" class="flex flex-col gap-5">
        {{-- Filter Sticky Bar Component --}}
        <x-filter-bar searchPlaceholder="Cari keterangan mutasi..." x-model="q">
            
            <x-slot name="filters">
                <div class="flex flex-col gap-1.5">
                    <label class="text-[11px] font-bold text-stone-500 uppercase tracking-wider">Jenis Mutasi</label>
                    <select x-model="jenis_mutasi" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-200 rounded-xl text-sm focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] outline-none text-stone-700 cursor-pointer appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23a8a29e%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E')] bg-[length:10px_10px] bg-no-repeat bg-[position:right_12px_center] pr-8 shadow-sm">
                        <option value="">Semua Jenis</option>
                        <option value="brk_ke_kas">Tarik Tunai (Bank BRK ➔ Kas Tunai)</option>
                        <option value="kas_ke_brk">Setor Tunai (Kas Tunai ➔ Bank BRK)</option>
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
                        <x-table.th>Jenis Mutasi</x-table.th>
                        <x-table.th>Keterangan</x-table.th>
                        <x-table.th class="text-right">Nominal</x-table.th>
                        @can('pengeluaran.delete')
                        <x-table.th class="text-right">Aksi</x-table.th>
                        @endcan
                    </tr>
                </x-table.thead>
                <x-table.tbody>
                    @forelse($mutasi as $m)
                    <x-table.tr class="hover:bg-stone-50/50 transition-colors">
                        <x-table.td class="align-top">
                            <div class="flex flex-col gap-0.5">
                                <span class="font-bold text-stone-800 text-[13px]">{{ $m->tanggal->translatedFormat('d M Y') }}</span>
                            </div>
                        </x-table.td>
                        <x-table.td class="align-top">
                            @if($m->jenis_mutasi == 'brk_ke_kas')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200/50">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                                Tarik Tunai (Bank BRK ➔ Kas)
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200/50">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                                Setor Tunai (Kas ➔ Bank BRK)
                            </span>
                            @endif
                        </x-table.td>
                        <x-table.td class="align-top text-[13px] font-medium text-stone-600">
                            {{ $m->keterangan }}
                        </x-table.td>
                        <x-table.td class="align-top text-right font-mono font-bold text-stone-800 whitespace-nowrap text-[13px]">
                            {{ format_rupiah($m->nominal) }}
                        </x-table.td>
                        @can('pengeluaran.delete')
                        <x-table.td class="whitespace-nowrap text-right align-top">
                            <x-action-dropdown>
                                <form action="{{ route('mutasi-rekening.destroy', $m->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus catatan mutasi ini? Neraca akan kembali bergeser mengikuti perubahan ini.')">
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
                                <svg class="w-8 h-8 text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                Belum ada riwayat mutasi kas & bank.
                            </div>
                        </x-table.td>
                    </x-table.tr>
                    @endforelse
                </x-table.tbody>
            </x-table>

            @if($mutasi->hasPages())
            <div class="px-6 py-4 border-t border-stone-200 bg-stone-50/50">
                {{ $mutasi->links() }}
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

{{-- Modal Catat Mutasi --}}
<x-modal name="modal-mutasi" title="Catat Mutasi Pindah Buku" maxWidth="xl">
    <form id="form-catat-mutasi" action="{{ route('mutasi-rekening.store') }}" method="POST" class="contents">
        @csrf
        <div class="-mx-6 -mt-5 px-6 py-4 bg-stone-50 border-b border-stone-200 mb-5">
            <label class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-1.5">Tanggal Mutasi</label>
            <x-datepicker name="tanggal" :value="old('tanggal', date('Y-m-d'))" :required="true" />
            @error('tanggal') <p class="text-[11px] text-red-500 mt-1 font-medium">{{ $message }}</p> @enderror
        </div>
        
        <div class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-1.5">Arah Mutasi / Pindah Buku</label>
                <select name="jenis_mutasi" required class="w-full px-4 py-2.5 rounded-xl border border-stone-300 focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] text-sm text-stone-700 bg-white outline-none transition-all cursor-pointer">
                    <option value="" disabled selected>-- Pilih Arah Perpindahan --</option>
                    <option value="brk_ke_kas">Tarik Tunai (Saldo Bank BRK ➔ Kas Tunai Bendahara)</option>
                    <option value="kas_ke_brk">Setor Tunai (Kas Tunai Bendahara ➔ Saldo Bank BRK)</option>
                </select>
                @error('jenis_mutasi') <p class="text-[11px] text-red-500 mt-1 font-medium">{{ $message }}</p> @enderror
            </div>
            
            <div>
                <label class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-1.5">Nominal Mutasi (Rp)</label>
                <input type="number" name="nominal" required min="1" class="w-full px-4 py-2.5 rounded-xl border border-stone-300 focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] text-sm text-stone-700 bg-white outline-none transition-all font-mono" placeholder="Contoh: 1000000">
                @error('nominal') <p class="text-[11px] text-red-500 mt-1 font-medium">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-1.5">Keterangan / Tujuan</label>
                <input type="text" name="keterangan" required class="w-full px-4 py-2.5 rounded-xl border border-stone-300 focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] text-sm text-stone-700 bg-white outline-none transition-all" placeholder="Contoh: Tarik tunai untuk operasional harian kantor">
                @error('keterangan') <p class="text-[11px] text-red-500 mt-1 font-medium">{{ $message }}</p> @enderror
            </div>
        </div>

        <x-slot name="footer">
            <button type="button" @click="$dispatch('close-modal', 'modal-mutasi')" class="px-4 py-2.5 text-sm font-bold text-stone-500 hover:text-stone-800 hover:bg-stone-200/50 rounded-xl transition-all">Batal</button>
            <button type="submit" form="form-catat-mutasi" class="px-6 py-2.5 bg-[#043d2e] hover:bg-[#043d2e]/90 text-white text-sm font-bold rounded-xl shadow-sm transition-all active:scale-95">Simpan Mutasi</button>
        </x-slot>
    </form>
</x-modal>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('mutasiFilter', () => ({
            q: new URLSearchParams(location.search).get('q') || '',
            jenis_mutasi: new URLSearchParams(location.search).get('jenis_mutasi') || '',
            bulan: new URLSearchParams(location.search).get('bulan') || '',
            tahun: new URLSearchParams(location.search).get('tahun') || '',
            loading: false,
            timeout: null,
            abortController: null,
            
            init() {
                this.$watch('q', () => this.debouncedFetch());
                this.$watch('jenis_mutasi', () => this.fetchData());
                this.$watch('bulan', () => this.fetchData());
                this.$watch('tahun', () => this.fetchData());
            },

            get activeFiltersCount() {
                let count = 0;
                if (this.q !== '') count++;
                if (this.jenis_mutasi !== '') count++;
                if (this.bulan !== '') count++;
                if (this.tahun !== '') count++;
                return count;
            },
            
            resetFilter() {
                this.q = '';
                this.jenis_mutasi = '';
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
                    if (this.jenis_mutasi !== '') params.append('jenis_mutasi', this.jenis_mutasi);
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
