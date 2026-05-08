@extends('layouts.app')

@section('title', 'Riwayat Transaksi Simpanan')
@section('subtitle', 'Arsip dan alur pencatatan dana simpanan seluruh anggota secara mendetail')

@section('actions')
@endsection

@section('content')
<div x-data="riwayatFilter()" class="flex flex-col gap-5">

    {{-- Filter Sticky Bar Component --}}
    <x-filter-bar searchPlaceholder="Cari NIP atau Nama Anggota..." x-model="q">
        <x-slot name="trailing">
            <div x-data="{ totalFilter: '{{ format_rupiah($totalTransaksi) }}', show: {{ count(request()->except('page')) > 0 ? 'true' : 'false' }} }" 
                 @total-updated.window="totalFilter = $event.detail.total; show = $event.detail.show">
                 <template x-if="show">
                    <div class="flex items-center gap-2.5 px-4 py-2.5 bg-white border border-stone-200 rounded-xl shadow-sm">
                        <div class="relative flex h-2 w-2">
                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#043d2e] opacity-40"></span>
                          <span class="relative inline-flex rounded-full h-2 w-2 bg-[#043d2e]"></span>
                        </div>
                        <span class="text-sm font-medium text-stone-600">
                            Total Transaksi (Filter): <span class="font-bold text-stone-900 ml-0.5" x-text="totalFilter"></span>
                        </span>
                    </div>
                </template>
            </div>
        </x-slot>

        <x-slot name="indicator">
            <template x-if="activeFiltersCount > 0">
                <span class="absolute -top-1 -right-1 w-3.5 h-3.5 bg-red-500 border-2 border-white rounded-full shadow-sm"></span>
            </template>
        </x-slot>

        <x-slot name="filters">
            {{-- Filter Bidang --}}
            <div class="flex flex-col gap-1.5">
                <label class="text-[11px] font-bold text-stone-500 uppercase tracking-wider">Bidang / Divisi</label>
                <select x-model="bidang" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-200 rounded-xl text-sm focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] outline-none text-stone-700 cursor-pointer appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23a8a29e%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E')] bg-[length:10px_10px] bg-no-repeat bg-[position:right_12px_center] pr-8 shadow-sm">
                    <option value="">Semua Bidang</option>
                    @foreach($bidangs as $b)
                        <option value="{{ $b->id }}">{{ $b->nama_bidang }}</option>
                    @endforeach
                </select>
            </div>
            
            {{-- Filter Golongan ASN --}}
            <div class="flex flex-col gap-1.5">
                <label class="text-[11px] font-bold text-stone-500 uppercase tracking-wider">Golongan ASN</label>
                <select x-model="golongan" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-200 rounded-xl text-sm focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] outline-none text-stone-700 cursor-pointer appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23a8a29e%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E')] bg-[length:10px_10px] bg-no-repeat bg-[position:right_12px_center] pr-8 shadow-sm">
                    <option value="">Semua Gol. ASN</option>
                    @foreach(\App\Enums\GolonganAsn::cases() as $gol)
                        <option value="{{ $gol->value }}">{{ $gol->label() }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Kategori Simpanan --}}
            <div class="flex flex-col gap-1.5">
                <label class="text-[11px] font-bold text-stone-500 uppercase tracking-wider">Jenis Simpanan</label>
                <select x-model="jenis_simpanan_id" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-200 rounded-xl text-sm focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] outline-none text-stone-700 cursor-pointer appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23a8a29e%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E')] bg-[length:10px_10px] bg-no-repeat bg-[position:right_12px_center] pr-8 shadow-sm">
                    <option value="">Semua Kategori</option>
                    @foreach($jenisSimpananList as $j)
                        <option value="{{ $j->id }}">{{ $j->nama }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filter User Pencatat --}}
            <div class="flex flex-col gap-1.5">
                <label class="text-[11px] font-bold text-stone-500 uppercase tracking-wider">Pemroses Terkait</label>
                <select x-model="pencatat_id" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-200 rounded-xl text-sm focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] outline-none text-stone-700 cursor-pointer appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23a8a29e%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E')] bg-[length:10px_10px] bg-no-repeat bg-[position:right_12px_center] pr-8 shadow-sm">
                    <option value="">Semua Pemroses</option>
                    @foreach($pencatatList as $u)
                        <option value="{{ $u->id }}">{{ $u->nama }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Waktu --}}
            <div class="flex flex-col gap-1.5 md:col-span-2">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-[11px] font-bold text-stone-500 uppercase mb-1.5 px-1">Dari Tanggal</p>
                        <x-datepicker x-model="dari_tanggal" placeholder="Tidak dibatasi" />
                    </div>
                    <div>
                        <p class="text-[11px] font-bold text-stone-500 uppercase mb-1.5 px-1">Sampai Tanggal</p>
                        <x-datepicker x-model="sampai_tanggal" placeholder="Tidak dibatasi" />
                    </div>
                </div>
            </div>
            
            {{-- Tombol Reset Filter --}}
            <template x-if="activeFiltersCount > 0">
                <button type="button" @click="resetFilters()" class="mt-2 w-full md:col-span-2 py-2.5 px-4 rounded-xl bg-red-50 text-red-600 hover:bg-red-100 text-sm font-bold transition-colors">
                    Reset Semua
                </button>
            </template>
        </x-slot>
    </x-filter-bar>

    {{-- Table Area --}}
    <div class="bg-white rounded-2xl border border-stone-200 shadow-sm flex flex-col">
        <div class="relative flex-1 flex flex-col">
            <div x-show="loading" 
                 x-transition.opacity.duration.200ms
                 class="absolute inset-0 bg-white/70 backdrop-blur-[2px] z-10 flex items-start justify-center pt-24" 
                 style="display: none;">
                <div class="flex items-center gap-3 px-5 py-3 bg-white border border-stone-200 rounded-2xl shadow-xl text-sm font-bold text-stone-700">
                    <svg class="animate-spin h-5 w-5 text-[#043d2e]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Menyaring riwayat transaksi...
                </div>
            </div>
            
            <div id="table-content-container" class="flex-1 flex flex-col justify-between">
                @include('simpanan.partials.table_riwayat', ['simpanans' => $simpanans])
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('riwayatFilter', () => ({
        q: new URLSearchParams(location.search).get('q') || '',
        bidang: new URLSearchParams(location.search).get('bidang') || '',
        golongan: new URLSearchParams(location.search).get('golongan') || '',
        jenis_simpanan_id: new URLSearchParams(location.search).get('jenis_simpanan_id') || '',
        pencatat_id: new URLSearchParams(location.search).get('pencatat_id') || '',
        dari_tanggal: new URLSearchParams(location.search).get('dari_tanggal') || '',
        sampai_tanggal: new URLSearchParams(location.search).get('sampai_tanggal') || '',
        loading: false,
        timeout: null,
        abortController: null,

        init() {
            this.$watch('q', () => this.debouncedFetch());
            this.$watch('bidang', () => this.fetchData());
            this.$watch('golongan', () => this.fetchData());
            this.$watch('jenis_simpanan_id', () => this.fetchData());
            this.$watch('pencatat_id', () => this.fetchData());
            this.$watch('dari_tanggal', () => this.fetchData());
            this.$watch('sampai_tanggal', () => this.fetchData());

            window.addEventListener('riwayat-paginate', (e) => {
                this.fetchData(e.detail.url);
            });
        },

        get activeFiltersCount() {
            let count = 0;
            if (this.bidang !== '') count++;
            if (this.golongan !== '') count++;
            if (this.jenis_simpanan_id !== '') count++;
            if (this.pencatat_id !== '') count++;
            if (this.dari_tanggal !== '' || this.sampai_tanggal !== '') count++;
            return count;
        },

        resetFilters() {
            this.bidang = '';
            this.golongan = '';
            this.jenis_simpanan_id = '';
            this.pencatat_id = '';
            this.dari_tanggal = '';
            this.sampai_tanggal = '';
            this.q = '';
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
                if (this.bidang !== '') params.append('bidang', this.bidang);
                if (this.golongan !== '') params.append('golongan', this.golongan);
                if (this.jenis_simpanan_id !== '') params.append('jenis_simpanan_id', this.jenis_simpanan_id);
                if (this.pencatat_id !== '') params.append('pencatat_id', this.pencatat_id);
                if (this.dari_tanggal !== '') params.append('dari_tanggal', this.dari_tanggal);
                if (this.sampai_tanggal !== '') params.append('sampai_tanggal', this.sampai_tanggal);
                
                url = `${window.location.pathname}?${params.toString()}`;
            }

            try {
                window.history.pushState({}, '', url);

                const response = await fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    signal: this.abortController.signal
                });
                
                if (response.headers.get('content-type')?.includes('application/json')) {
                    const data = await response.json();
                    document.getElementById('table-content-container').innerHTML = data.html;
                    
                    // Trigger total event based on new payload
                    this.$dispatch('total-updated', { 
                        total: data.totalTransaksi,
                        show: this.activeFiltersCount > 0
                    });
                } else {
                    const html = await response.text();
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    
                    const newContent = doc.getElementById('table-content-container');
                    if (newContent) {
                        document.getElementById('table-content-container').innerHTML = newContent.innerHTML;
                    }
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
