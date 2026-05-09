@extends('layouts.app')

@section('title', 'Buku Simpanan Anggota')
@section('subtitle', 'Rincian akumulasi simpanan pokok, wajib, Thn. 2025, SWP, dan Bonus SHU. Dana hanya dapat ditarik penuh saat anggota keluar.')

@section('actions')
    <x-export-dropdown 
        :excelRoute="route('simpanan.export.excel')" 
        :pdfRoute="route('simpanan.export.pdf')" 
    />
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
    <x-modal name="modal-catat-simpanan" title="Catat Uang Masuk Simpanan" maxWidth="md">
        <form id="form-catat-simpanan" action="{{ route('simpanan.store') }}" method="POST" class="contents">
            @csrf
            <input type="hidden" name="anggota_id" :value="selectedAnggotaId">
            
            <div class="mb-5 bg-emerald-50 border border-emerald-100 rounded-xl p-3 flex items-center gap-3 shadow-sm">
                 <div class="w-10 h-10 bg-emerald-200/50 text-emerald-700 rounded-lg flex items-center justify-center font-bold border border-emerald-200">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                 </div>
                 <div>
                      <p class="text-[10px] uppercase font-bold text-emerald-600 tracking-wider mb-0.5">Informasi Anggota</p>
                      <p class="font-black text-emerald-900 text-sm tracking-tight" x-text="selectedAnggotaNama"></p>
                 </div>
            </div>

            <div class="space-y-1.5">
                <select name="jenis_simpanan_id" required 
                        @change="selectedKode = $event.target.options[$event.target.selectedIndex].dataset.kode || ''; nominal = $event.target.options[$event.target.selectedIndex].dataset.nominal || ''"
                        class="w-full px-4 py-3 bg-stone-50 border border-stone-200 rounded-xl text-sm focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] text-stone-700 font-bold cursor-pointer outline-none transition-all shadow-sm">
                    <option value="" disabled selected>-- Pilih Kategori Simpanan --</option>
                    @foreach($jenisSimpananList->where('kode', '!=', 'SWP') as $j)
                        <option value="{{ $j->id }}" data-kode="{{ $j->kode }}" data-nominal="{{ $j->kode == 'POKOK' ? $nominalPokok : ($j->kode == 'WAJIB' ? $nominalWajib : '') }}">{{ $j->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-1.5 mt-5" x-show="selectedKode === 'WAJIB'" x-cloak style="display: none;">
                <label class="text-[11px] font-bold text-stone-500 uppercase tracking-wider block">Untuk Bulan & Tahun Transaksi</label>
                <div class="flex items-center gap-3">
                    <select name="bulan_untuk" class="w-full px-4 py-3 bg-stone-50 border border-stone-200 rounded-xl text-sm focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] outline-none text-stone-700 font-bold shadow-sm" :required="selectedKode === 'WAJIB'">
                        <option value="">-- Bulan --</option>
                        @for($m=1; $m<=12; $m++)
                            <option value="{{ $m }}">{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                        @endfor
                    </select>
                    <select name="tahun_untuk" class="w-full px-4 py-3 bg-stone-50 border border-stone-200 rounded-xl text-sm focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] outline-none text-stone-700 font-bold shadow-sm" :required="selectedKode === 'WAJIB'">
                        <option value="">-- Tahun --</option>
                        @for($y=date('Y')-2; $y<=date('Y')+2; $y++)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            <div class="mt-5" x-show="selectedKode === 'POKOK'" x-cloak style="display: none;">
                <template x-if="selectedAnggotaPokokPaid">
                    <div class="px-4 py-3 rounded-xl bg-orange-50 border border-orange-200 text-orange-700 text-sm font-medium flex gap-2">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="leading-relaxed">Anggota ini <span class="font-bold uppercase tracking-wider">sudah melunasi</span> Simpanan Pokok. Sistem tidak menyarankan setoran tambahan kecuali kondisi anomali.</span>
                    </div>
                </template>
                <template x-if="!selectedAnggotaPokokPaid">
                    <div class="px-4 py-3 rounded-xl bg-blue-50 border border-blue-200 text-blue-700 text-sm font-medium flex gap-2">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Wajib dibayarkan sekali di awal oleh anggota. Nilai ketetapan saat ini: <span class="font-bold">Rp</span><span class="font-bold font-mono" x-text="new Intl.NumberFormat('id-ID').format(nominal || 0)"></span>.</span>
                    </div>
                </template>
            </div>

            <div class="space-y-1.5 mt-5">
                <label class="text-[11px] font-bold text-stone-500 uppercase tracking-wider block">Nominal Rupiah (Rp)</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-stone-500 font-bold font-mono">Rp</span>
                    <input type="number" name="nominal" x-model="nominal" required min="1000" :readonly="selectedKode === 'POKOK' || selectedKode === 'WAJIB'" class="w-full pl-11 pr-4 py-3 bg-stone-50 border border-stone-200 rounded-xl text-stone-800 font-black font-mono focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] outline-none transition-all shadow-sm read-only:bg-stone-100 read-only:text-stone-500 read-only:border-stone-200 read-only:cursor-not-allowed" placeholder="0">
                </div>
            </div>
            
            <div class="space-y-1.5 mt-5">
                <label class="text-[11px] font-bold text-stone-500 uppercase tracking-wider block">Tanggal Transaksi</label>
                <x-datepicker name="tanggal" :value="date('Y-m-d')" :required="true" />
            </div>

            <div class="space-y-1.5 mt-5">
                <label class="text-[11px] font-bold text-stone-500 uppercase tracking-wider block">Keterangan Opsional</label>
                <textarea name="keterangan" rows="2" class="w-full px-4 py-3 bg-stone-50 border border-stone-200 rounded-xl text-sm focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] outline-none text-stone-700 font-medium transition-all shadow-sm" placeholder="Contoh: Titipan tunai melalui pengurus..."></textarea>
            </div>

            <x-slot name="footer">
                <button type="button" @click="$dispatch('close-modal', 'modal-catat-simpanan')" class="px-4 py-2.5 text-sm font-bold text-stone-500 hover:text-stone-800 hover:bg-stone-200/50 rounded-xl transition-all">Batalkan</button>
                <button type="submit" form="form-catat-simpanan" class="px-6 py-2.5 bg-[#043d2e] hover:bg-[#043d2e]/90 text-white text-sm font-bold rounded-xl shadow-sm transition-all active:scale-95 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Setorkan Dana
                </button>
            </x-slot>
        </form>
    </x-modal>
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
        selectedAnggotaPokokPaid: false,
        selectedKode: '',
        nominal: '',

        openModal(id, nama, pokokPaid = false) {
            this.selectedAnggotaId = id;
            this.selectedAnggotaNama = nama;
            this.selectedAnggotaPokokPaid = pokokPaid;
            this.selectedKode = '';
            this.nominal = '';
            this.$dispatch('open-modal', 'modal-catat-simpanan');
        },
        closeModal() {
            this.selectedAnggotaId = '';
            this.selectedAnggotaNama = '';
            this.$dispatch('close-modal', 'modal-catat-simpanan');
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

        getFilterParams() {
            const params = new URLSearchParams();
            if (this.q !== '') params.append('q', this.q);
            if (this.bidang !== '') params.append('bidang', this.bidang);
            if (this.golongan !== '') params.append('golongan', this.golongan);
            if (this.dari_tanggal !== '') params.append('dari_tanggal', this.dari_tanggal);
            if (this.sampai_tanggal !== '') params.append('sampai_tanggal', this.sampai_tanggal);
            return params;
        },

        updateExportLinks() {
            const params = this.getFilterParams();
            const qs = params.toString();
            const excelLink = document.getElementById('export-excel-link');
            const pdfLink = document.getElementById('export-pdf-link');
            if (excelLink) excelLink.href = `{{ route('simpanan.export.excel') }}${qs ? '?' + qs : ''}`;
            if (pdfLink) pdfLink.href = `{{ route('simpanan.export.pdf') }}${qs ? '?' + qs : ''}`;
        },

        async fetchData(targetUrl = null) {
            this.loading = true;
            
            if (this.abortController) {
                this.abortController.abort();
            }
            this.abortController = new AbortController();
            
            let url = targetUrl;
            if (!url) {
                const params = this.getFilterParams();
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
                    this.updateExportLinks();
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
