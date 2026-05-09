@extends('layouts.app')

@section('title', 'Rincian Pinjaman Aktif')
@section('subtitle', 'Daftar pinjaman anggota yang saat ini sedang berjalan (aktif) beserta rincian angsurannya')

@section('content')

<div x-data="pinjamanFilter()" class="flex flex-col gap-5">
    {{-- Filter Sticky Bar Component --}}
    <x-filter-bar searchPlaceholder="Cari NIP atau Nama..." x-model="q">
        <x-slot name="indicator">
            <template x-if="activeFiltersCount > 0">
                <span class="absolute -top-1 -right-1 w-3.5 h-3.5 bg-red-500 border-2 border-white rounded-full shadow-sm"></span>
            </template>
        </x-slot>
        <x-slot name="filters">
            {{-- Bidang --}}
            <div class="flex flex-col gap-1.5">
                <label class="text-[11px] font-bold text-stone-500 uppercase tracking-wider">Unit/Bidang</label>
                <select x-model="bidang_id" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-200 rounded-xl text-sm focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] outline-none text-stone-700 cursor-pointer appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23a8a29e%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E')] bg-[length:10px_10px] bg-no-repeat bg-[position:right_12px_center] pr-8 shadow-sm">
                    <option value="">Semua Unit/Bidang</option>
                    @foreach($bidangs as $bidang)
                        <option value="{{ $bidang->id }}">{{ $bidang->nama_bidang }}</option>
                    @endforeach
                </select>
            </div>
            {{-- Periode --}}
            <div class="flex flex-col gap-1.5">
                <label class="text-[11px] font-bold text-stone-500 uppercase tracking-wider">Periode Pinjaman</label>
                <select x-model="periode_id" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-200 rounded-xl text-sm focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] outline-none text-stone-700 cursor-pointer appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23a8a29e%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E')] bg-[length:10px_10px] bg-no-repeat bg-[position:right_12px_center] pr-8 shadow-sm">
                    <option value="">Semua Periode</option>
                    @foreach($periodes as $p)
                        <option value="{{ $p->id }}">{{ $p->nama }}</option>
                    @endforeach
                </select>
            </div>
            {{-- Bulan Awal --}}
            <div class="flex flex-col gap-1.5">
                <label class="text-[11px] font-bold text-stone-500 uppercase tracking-wider">Bulan Awal Berjalan</label>
                <select x-model="bulan_awal" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-200 rounded-xl text-sm focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] outline-none text-stone-700 cursor-pointer appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23a8a29e%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E')] bg-[length:10px_10px] bg-no-repeat bg-[position:right_12px_center] pr-8 shadow-sm">
                    <option value="">Semua Bulan</option>
                    @foreach($bulans as $bulan)
                        <option value="{{ $bulan }}">{{ \Carbon\Carbon::parse($bulan.'-01')->translatedFormat('F Y') }}</option>
                    @endforeach
                </select>
            </div>
            {{-- Tenor --}}
            <div class="flex flex-col gap-1.5">
                <label class="text-[11px] font-bold text-stone-500 uppercase tracking-wider">Jumlah Tenor</label>
                <select x-model="tenor" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-200 rounded-xl text-sm focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] outline-none text-stone-700 cursor-pointer appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23a8a29e%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E')] bg-[length:10px_10px] bg-no-repeat bg-[position:right_12px_center] pr-8 shadow-sm">
                    <option value="">Semua Tenor</option>
                    @foreach($tenors as $t)
                        <option value="{{ $t }}">{{ $t }} Bulan</option>
                    @endforeach
                </select>
            </div>
            {{-- Nominal --}}
            <div class="flex flex-col gap-1.5">
                <label class="text-[11px] font-bold text-stone-500 uppercase tracking-wider">Nominal Pinjaman</label>
                <select x-model="nominal" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-200 rounded-xl text-sm focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] outline-none text-stone-700 cursor-pointer appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23a8a29e%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E')] bg-[length:10px_10px] bg-no-repeat bg-[position:right_12px_center] pr-8 shadow-sm">
                    <option value="">Semua Nominal</option>
                    @foreach($nominals as $n)
                        <option value="{{ $n }}">{{ format_rupiah($n) }}</option>
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
                    <x-table.th>Anggota</x-table.th>
                    <x-table.th>Ref / Pencairan</x-table.th>
                    <x-table.th>Plafon & Bunga</x-table.th>
                    <x-table.th>Angsuran P/B</x-table.th>
                    <x-table.th>Status & Progress</x-table.th>
                    @can('pinjaman.detail')
                    <x-table.th class="text-right">Aksi</x-table.th>
                    @endcan
                </tr>
            </x-table.thead>
            <x-table.tbody>
                @forelse($anggotas as $anggota)
                @php
                    $pinjamans = $anggota->pinjaman;
                    $count = $pinjamans->count();
                @endphp
                
                @foreach($pinjamans as $i => $pinjaman)
                @php
                    $lunasCount = $pinjaman->angsuran->filter(fn($a) => $a->status->value === 'lunas')->count();
                    $totalLunas = $pinjaman->angsuran->filter(fn($a) => $a->status->value === 'lunas')->sum('nominal_total');
                    $persentase = $pinjaman->tenor_bulan > 0 ? round(($lunasCount / $pinjaman->tenor_bulan) * 100) : 0;
                    $isDashed = $i < $count - 1 ? 'border-b border-dashed border-stone-100' : '';
                @endphp
                <x-table.tr class="hover:bg-stone-50/50 transition-colors {{ $isDashed }}">
                    @if($i === 0)
                    <x-table.td class="align-top bg-white group-hover:-transparent" rowspan="{{ $count }}">
                        <div class="flex flex-col gap-0.5">
                            <span class="text-stone-800 font-bold text-[13px]">{{ $anggota->nama }}</span>
                            <span class="text-[11px] font-medium text-stone-500">NIP: {{ $anggota->nip }}</span>
                            @if($count > 1)
                            <div class="mt-2">
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded-md bg-amber-50 text-amber-700 border border-amber-200 text-[9px] font-bold uppercase tracking-wide">
                                    {{ $count }} Aktif
                                </span>
                            </div>
                            @endif
                        </div>
                    </x-table.td>
                    @endif
                    <x-table.td class="align-top">
                        <div class="flex flex-col gap-0.5">
                            <span class="font-mono text-[#043d2e] font-bold text-[13px]">{{ $pinjaman->no_referensi }}</span>
                            <span class="text-[11px] font-medium text-stone-500">{{ $pinjaman->tanggal_approval->format('d M Y') }}</span>
                        </div>
                    </x-table.td>
                    <x-table.td class="align-top">
                        <div class="flex flex-col gap-0.5 max-w-[150px]">
                            <span class="font-mono text-stone-800 text-[13px] font-black">Rp {{ number_format($pinjaman->nominal_pinjaman, 0, ',', '.') }}</span>
                            <span class="text-[11px] font-medium text-stone-500">B: Rp {{ number_format($pinjaman->total_bunga, 0, ',', '.') }}</span>
                        </div>
                    </x-table.td>
                    <x-table.td class="align-top">
                        <div class="flex flex-col gap-0.5">
                            <span class="font-mono text-[#043d2e] font-bold text-[13px]">Rp {{ number_format($pinjaman->total_angsuran, 0, ',', '.') }}</span>
                            <span class="text-[11px] font-medium text-stone-500">{{ $pinjaman->tenor_bulan }} Bulan</span>
                        </div>
                    </x-table.td>
                    <x-table.td class="align-top min-w-[170px]">
                        <div class="flex flex-col gap-1.5 w-full">
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] font-bold text-stone-500 uppercase tracking-widest"><span class="text-stone-800">{{ $lunasCount }}</span>/{{ $pinjaman->tenor_bulan }} BLN</span>
                                <span class="text-[10px] font-bold {{ $persentase === 100 ? 'text-emerald-600' : 'text-[#043d2e]' }}">{{ $persentase }}%</span>
                            </div>
                            <div class="w-full bg-stone-100 rounded-full h-1.5 border border-stone-200/50">
                                <div class="bg-gradient-to-r from-emerald-500 to-[#043d2e] h-1.5 rounded-full shadow-inner relative" style="width: {{ $persentase }}%"></div>
                            </div>
                            <span class="text-[11px] font-bold text-emerald-700 font-mono mt-0.5">Lunas: Rp {{ number_format($totalLunas, 0, ',', '.') }}</span>
                        </div>
                    </x-table.td>
                    @can('pinjaman.detail')
                    <x-table.td class="whitespace-nowrap text-right text-sm align-top">
                        <x-action-dropdown>
                            <x-action-dropdown-item href="{{ route('pinjaman.show', $pinjaman->id) }}" icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>'>
                                Detail
                            </x-action-dropdown-item>
                        </x-action-dropdown>
                    </x-table.td>
                    @endcan
                </x-table.tr>
                @endforeach
                @empty
                <x-table.tr>
                    <x-table.td colspan="{{ auth()->user()->can('pinjaman.detail') ? 6 : 5 }}" class="px-6 py-12 text-center text-stone-500 font-medium text-sm">
                        <div class="flex flex-col items-center gap-2">
                            <svg class="w-8 h-8 text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                            Saat ini tidak ada anggota yang memiliki pinjaman dengan status berjalan.
                        </div>
                    </x-table.td>
                </x-table.tr>
                @endforelse
            </x-table.tbody>
        </x-table>

        @if($anggotas->hasPages())
        <div class="px-6 py-4 border-t border-stone-200 bg-stone-50/50">
            {{ $anggotas->links() }}
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

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('pinjamanFilter', () => ({
            q: new URLSearchParams(location.search).get('q') || '',
            bidang_id: new URLSearchParams(location.search).get('bidang_id') || '',
            bulan_awal: new URLSearchParams(location.search).get('bulan_awal') || '',
            tenor: new URLSearchParams(location.search).get('tenor') || '',
            nominal: new URLSearchParams(location.search).get('nominal') || '',
            periode_id: new URLSearchParams(location.search).get('periode_id') || '',
            loading: false,
            timeout: null,
            abortController: null,
            
            init() {
                this.$watch('q', () => this.debouncedFetch());
                this.$watch('bidang_id', () => this.fetchData());
                this.$watch('bulan_awal', () => this.fetchData());
                this.$watch('tenor', () => this.fetchData());
                this.$watch('nominal', () => this.fetchData());
                this.$watch('periode_id', () => this.fetchData());
            },

            get activeFiltersCount() {
                let count = 0;
                if (this.bidang_id !== '') count++;
                if (this.bulan_awal !== '') count++;
                if (this.tenor !== '') count++;
                if (this.nominal !== '') count++;
                if (this.periode_id !== '') count++;
                return count;
            },
            
            resetFilter() {
                this.q = '';
                this.bidang_id = '';
                this.bulan_awal = '';
                this.tenor = '';
                this.nominal = '';
                this.periode_id = '';
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
                    if (this.bidang_id !== '') params.append('bidang_id', this.bidang_id);
                    if (this.bulan_awal !== '') params.append('bulan_awal', this.bulan_awal);
                    if (this.tenor !== '') params.append('tenor', this.tenor);
                    if (this.nominal !== '') params.append('nominal', this.nominal);
                    if (this.periode_id !== '') params.append('periode_id', this.periode_id);
                    
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
