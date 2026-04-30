@extends('layouts.app')

@section('title', 'Buku Simpanan Anggota')
@section('subtitle', 'Rincian akumulasi simpanan pokok, wajib, Thn. 2025, SWP, dan Bonus SHU. Dana hanya dapat ditarik penuh saat anggota keluar.')

@section('actions')
<div class="flex items-center gap-3">
    <div class="hidden sm:flex items-center gap-2.5 px-3 py-2 bg-stone-100 rounded-xl border border-stone-200/80 shadow-sm relative overflow-hidden">
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-5"></div>
        <div class="relative z-10 flex min-h-[1.5rem] items-center gap-2">
            <span class="text-[11px] font-bold text-stone-500 uppercase tracking-wider hidden lg:inline pt-0.5">Total Simpanan Tampil</span>
            <span id="grand-total-badge" class="text-base font-black font-mono text-[#043d2e] tracking-tight bg-[#043d2e]/5 px-2 py-0.5 rounded shadow-sm border border-[#043d2e]/10">{{ format_rupiah($grandTotal) }}</span>
        </div>
    </div>
</div>
@endsection

@section('content')
<div x-data="simpananFilter()" class="flex flex-col gap-5">
    {{-- Filter Sticky Bar Component --}}
    <x-filter-bar searchPlaceholder="Cari NIP atau Nama Anggota..." x-model="q">
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

            {{-- Filter Waktu --}}
            <div class="flex flex-col gap-1.5 md:col-span-2">
                <label class="text-[11px] font-bold text-stone-500 uppercase tracking-wider">Rentang Waktu</label>
                <div class="flex flex-col sm:flex-row items-center gap-2">
                    <input type="date" x-model="dari_tanggal" class="w-full px-3 py-2 bg-stone-50 border border-stone-200 rounded-xl text-sm focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] outline-none text-stone-700 font-medium">
                    <span class="text-stone-400 font-medium text-xs hidden sm:block">s/d</span>
                    <input type="date" x-model="sampai_tanggal" class="w-full px-3 py-2 bg-stone-50 border border-stone-200 rounded-xl text-sm focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] outline-none text-stone-700 font-medium">
                </div>
            </div>
            
            {{-- Tombol Reset Filter --}}
            <template x-if="activeFiltersCount > 0">
                <button type="button" @click="bidang = ''; golongan = ''; dari_tanggal = ''; sampai_tanggal = ''; q = ''" class="mt-2 w-full md:col-span-2 py-2.5 px-4 rounded-xl bg-red-50 text-red-600 hover:bg-red-100 text-sm font-bold transition-colors">
                    Reset Semua
                </button>
            </template>
        </x-slot>
    </x-filter-bar>

    {{-- Main Table Area --}}
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
                    Menghitung kalkulasi...
                </div>
            </div>
            
            <div id="table-content-container" class="flex-1 flex flex-col justify-between">
                @include('simpanan.partials.table', ['anggotas' => $anggotas])
            </div>
        </div>
    </div>

    {{-- Modal Tambah Simpanan Manual --}}
    <template x-teleport="body">
        <div x-show="modalOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-0" style="display: none;">
            <div x-show="modalOpen" x-transition.opacity class="fixed inset-0 bg-stone-900/60 backdrop-blur-sm" @click="closeModal()"></div>
            
            <div x-show="modalOpen" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative bg-white rounded-2xl border border-stone-200 shadow-2xl w-full max-w-md overflow-hidden z-10 flex flex-col max-h-screen">
                
                <form action="{{ route('simpanan.store') }}" method="POST" class="flex flex-col flex-1 overflow-hidden">
                    @csrf
                    <input type="hidden" name="anggota_id" :value="selectedAnggotaId">
                    
                    <div class="px-6 py-5 border-b border-stone-100 bg-stone-50 flex items-center justify-between shrink-0">
                        <div>
                            <h3 class="text-base font-bold text-stone-800">Catat Uang Masuk Simpanan</h3>
                            <p class="text-[13px] text-[#043d2e] font-bold mt-0.5" x-text="selectedAnggotaNama"></p>
                        </div>
                        <button type="button" @click="closeModal()" class="text-stone-400 hover:bg-stone-200 hover:text-stone-600 p-2 rounded-xl transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div class="p-6 space-y-5 overflow-y-auto custom-scrollbar">
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-stone-500 uppercase tracking-wider block">Kategori Simpanan</label>
                            <select name="jenis_simpanan_id" required class="w-full px-4 py-3 bg-stone-50 border border-stone-200 rounded-xl text-sm focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] text-stone-700 font-semibold cursor-pointer outline-none transition-all shadow-sm">
                                <option value="" disabled selected>Pilih Kategori...</option>
                                @foreach($jenisSimpananList as $j)
                                    <option value="{{ $j->id }}">{{ $j->nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-stone-500 uppercase tracking-wider block">Nominal Rupiah (Rp)</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-stone-400 font-bold">Rp</span>
                                <input type="number" name="nominal" required min="1000" class="w-full pl-11 pr-4 py-3 bg-stone-50 border border-stone-200 rounded-xl text-stone-800 font-black font-mono focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] outline-none transition-all shadow-sm" placeholder="0">
                            </div>
                        </div>
                        
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-stone-500 uppercase tracking-wider block">Tanggal Transaksi</label>
                            <input type="date" name="tanggal" required value="{{ date('Y-m-d') }}" class="w-full px-4 py-3 bg-stone-50 border border-stone-200 rounded-xl text-sm font-semibold text-stone-700 focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] outline-none transition-all shadow-sm">
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-stone-500 uppercase tracking-wider block">Keterangan Opsional</label>
                            <textarea name="keterangan" rows="2" class="w-full px-4 py-3 bg-stone-50 border border-stone-200 rounded-xl text-sm focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] outline-none text-stone-700 transition-all shadow-sm" placeholder="Catatan tambahan..."></textarea>
                        </div>
                    </div>

                    <div class="px-6 py-5 border-t border-stone-100 bg-stone-50 shrink-0 flex items-center justify-end gap-3">
                        <button type="button" @click="closeModal()" class="px-4 py-2.5 text-sm font-bold text-stone-500 hover:text-stone-800 hover:bg-stone-200/50 rounded-xl transition-all">Batalkan</button>
                        <button type="submit" class="px-6 py-2.5 bg-[#043d2e] hover:bg-[#043d2e]/90 text-white text-sm font-bold rounded-xl shadow-sm transition-all active:scale-95">Setorkan Dana</button>
                    </div>
                </form>
            </div>
        </div>
    </template>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('simpananFilter', () => ({
        q: new URLSearchParams(location.search).get('q') || '',
        bidang: new URLSearchParams(location.search).get('bidang') || '',
        golongan: new URLSearchParams(location.search).get('golongan') || '',
        dari_tanggal: new URLSearchParams(location.search).get('dari_tanggal') || '',
        sampai_tanggal: new URLSearchParams(location.search).get('sampai_tanggal') || '',
        loading: false,
        timeout: null,
        abortController: null,
        
        modalOpen: false,
        selectedAnggotaId: '',
        selectedAnggotaNama: '',

        openModal(id, nama) {
            this.selectedAnggotaId = id;
            this.selectedAnggotaNama = nama;
            this.modalOpen = true;
        },
        closeModal() {
            this.modalOpen = false;
            this.selectedAnggotaId = '';
            this.selectedAnggotaNama = '';
        },

        init() {
            this.$watch('q', () => this.debouncedFetch());
            this.$watch('bidang', () => this.fetchData());
            this.$watch('golongan', () => this.fetchData());
            this.$watch('dari_tanggal', () => this.fetchData());
            this.$watch('sampai_tanggal', () => this.fetchData());

            window.addEventListener('simpanan-paginate', (e) => {
                this.fetchData(e.detail.url);
            });
        },

        get activeFiltersCount() {
            let count = 0;
            if (this.bidang !== '') count++;
            if (this.golongan !== '') count++;
            if (this.dari_tanggal !== '' || this.sampai_tanggal !== '') count++;
            return count;
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
                    
                    const badge = document.getElementById('grand-total-badge');
                    if (badge) {
                        badge.innerText = data.grandTotal;
                    }
                } else {
                    const html = await response.text();
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    
                    const newContent = doc.getElementById('table-content-container');
                    if (newContent) {
                        document.getElementById('table-content-container').innerHTML = newContent.innerHTML;
                    }
                    
                    const newBadge = doc.getElementById('grand-total-badge');
                    const oldBadge = document.getElementById('grand-total-badge');
                    if (newBadge && oldBadge) {
                        oldBadge.innerText = newBadge.innerText;
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
