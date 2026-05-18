@extends('layouts.app')

@section('title', 'Data Anggota')
@section('subtitle', 'Daftar keseluruhan anggota Koperasi Tirta Bina Karya')

@section('actions')
    @can('anggota.create')
    <button @click="$dispatch('open-modal', 'modal-tambah-anggota')" class="py-2.5 px-4 rounded-xl bg-[#043d2e] hover:bg-[#043d2e]/90 text-white text-sm font-bold transition-colors flex items-center gap-2 shadow-sm">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
        </svg>
        <span>Tambah Anggota</span>
    </button>
    @endcan
@endsection

@section('content')
<div x-data="anggotaPage()" class="flex flex-col gap-5">
    {{-- Filter Sticky Bar Component --}}
    <x-filter-bar searchPlaceholder="Cari berdasarkan NIP atau Nama..." x-model="q">
        <x-slot name="trailing">
            <div class="flex items-center gap-2.5 px-4 py-2.5 bg-white border border-stone-200 rounded-xl shadow-sm">
                <div class="relative flex h-2 w-2">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#043d2e] opacity-40"></span>
                  <span class="relative inline-flex rounded-full h-2 w-2 bg-[#043d2e]"></span>
                </div>
                <span class="text-sm font-medium text-stone-600">
                    Total Anggota: <span id="total-anggota-badge" class="font-bold text-stone-900 ml-0.5">{{ $anggotas->total() }}</span>
                </span>
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
                <label class="text-[11px] font-bold text-stone-500 uppercase tracking-wider">Bidang Dinas</label>
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

            {{-- Filter Status --}}
            <div class="flex flex-col gap-1.5">
                <label class="text-[11px] font-bold text-stone-500 uppercase tracking-wider">Status Keanggotaan</label>
                <select x-model="status" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-200 rounded-xl text-sm focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] outline-none text-stone-700 cursor-pointer appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23a8a29e%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E')] bg-[length:10px_10px] bg-no-repeat bg-[position:right_12px_center] pr-8 shadow-sm">
                    <option value="">Semua Status</option>
                    @foreach(\App\Enums\StatusAnggota::cases() as $st)
                        <option value="{{ $st->value }}">{{ ucfirst($st->value) }}</option>
                    @endforeach
                </select>
            </div>
            
            {{-- Tombol Reset Filter --}}
            <template x-if="activeFiltersCount > 0">
                <button type="button" @click="bidang = ''; golongan = ''; status = ''; q = ''" class="mt-2 w-full py-2.5 px-4 rounded-xl bg-red-50 text-red-600 hover:bg-red-100 text-sm font-bold transition-colors">
                    Reset Semua Filter
                </button>
            </template>
        </x-slot>
    </x-filter-bar>

    {{-- Main Table Area --}}
    <div class="bg-white rounded-2xl border border-stone-200 shadow-sm flex flex-col">
        {{-- Loading Overlay and Table Container --}}
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
                Memuat data...
            </div>
        </div>
        
        <div id="table-content-container" class="flex-1 flex flex-col justify-between">
            @include('anggota.partials.table', ['anggotas' => $anggotas])
        </div>
    </div>
</div>

    {{-- ============================================ --}}
    {{-- MODAL: Tambah Anggota Baru --}}
    {{-- ============================================ --}}
    <x-modal name="modal-tambah-anggota" title="Tambah Anggota Baru" maxWidth="xl">
        <form id="form-tambah-anggota" action="{{ route('anggota.store') }}" method="POST" x-data="{ nip: '' }" class="contents">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                {{-- NIP --}}
                <div class="sm:col-span-2 space-y-1.5">
                    <label class="text-[11px] font-bold text-stone-500 uppercase tracking-wider block">NIP Anggota <span class="text-red-500">*</span></label>
                    <x-nip-input name="nip" model="nip" placeholder="Masukkan 18 digit angka NIP..." required="true" class="bg-stone-50 text-stone-800 font-mono tracking-wider focus:bg-white focus:ring-[#043d2e]/20 focus:border-[#043d2e]" />
                    @error('nip') <p class="text-xs text-red-500 font-medium">{{ $message }}</p> @enderror
                </div>
                
                {{-- Nama --}}
                <div class="space-y-1.5">
                    <label class="text-[11px] font-bold text-stone-500 uppercase tracking-wider block">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="nama" value="{{ old('nama') }}" required placeholder="Contoh: Budi Santoso"
                           class="w-full px-4 py-3 bg-stone-50 border border-stone-200 rounded-xl text-sm focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] outline-none text-stone-700 transition-all shadow-sm">
                    @error('nama') <p class="text-xs text-red-500 font-medium">{{ $message }}</p> @enderror
                </div>

                {{-- Golongan ASN --}}
                <div class="space-y-1.5">
                    <label class="text-[11px] font-bold text-stone-500 uppercase tracking-wider block">Golongan ASN <span class="text-red-500">*</span></label>
                    <select name="golongan_asn" required class="w-full px-4 py-3 bg-stone-50 border border-stone-200 rounded-xl text-sm focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] outline-none text-stone-700 cursor-pointer appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23a8a29e%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E')] bg-[length:10px_10px] bg-no-repeat bg-[position:right_12px_center] pr-8 shadow-sm transition-all">
                        <option value="">-- Pilih Golongan --</option>
                        @foreach(\App\Enums\GolonganAsn::cases() as $gol)
                            <option value="{{ $gol->value }}">{{ $gol->label() }}</option>
                        @endforeach
                    </select>
                    @error('golongan_asn') <p class="text-xs text-red-500 font-medium">{{ $message }}</p> @enderror
                </div>

                {{-- Bidang --}}
                <div class="space-y-1.5">
                    <label class="text-[11px] font-bold text-stone-500 uppercase tracking-wider block">Bidang Dinas / Unit <span class="text-red-500">*</span></label>
                    <select name="bidang_id" required class="w-full px-4 py-3 bg-stone-50 border border-stone-200 rounded-xl text-sm focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] outline-none text-stone-700 cursor-pointer appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23a8a29e%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E')] bg-[length:10px_10px] bg-no-repeat bg-[position:right_12px_center] pr-8 shadow-sm transition-all">
                        <option value="">-- Pilih Ruang Lingkup --</option>
                        @foreach($bidangs as $bidang)
                            <option value="{{ $bidang->id }}">{{ $bidang->nama_bidang }}</option>
                        @endforeach
                    </select>
                    @error('bidang_id') <p class="text-xs text-red-500 font-medium">{{ $message }}</p> @enderror
                </div>

                {{-- No HP --}}
                <div class="space-y-1.5">
                    <label class="text-[11px] font-bold text-stone-500 uppercase tracking-wider block">No Handphone (WA)</label>
                    <input type="text" name="no_hp" value="{{ old('no_hp') }}" placeholder="Contoh: 08123456789"
                           class="w-full px-4 py-3 bg-stone-50 border border-stone-200 rounded-xl text-sm focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] outline-none text-stone-700 transition-all shadow-sm">
                    @error('no_hp') <p class="text-xs text-red-500 font-medium">{{ $message }}</p> @enderror
                </div>

                {{-- Tanggal Masuk --}}
                <div class="space-y-1.5">
                    <label class="text-[11px] font-bold text-stone-500 uppercase tracking-wider block">Tanggal Masuk Koperasi</label>
                    <x-datepicker name="tanggal_masuk" :value="old('tanggal_masuk', now()->format('Y-m-d'))" />
                    @error('tanggal_masuk') <p class="text-xs text-red-500 font-medium">{{ $message }}</p> @enderror
                </div>

                {{-- Konfigurasi Potongan TPP --}}
                <div class="sm:col-span-2 mt-2 p-5 bg-stone-50/80 rounded-xl border border-stone-200 space-y-4">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-stone-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <h3 class="text-[11px] uppercase tracking-widest font-bold text-stone-600">Mulai Potongan TPP</h3>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Potongan Simpanan Pokok --}}
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-stone-500 uppercase tracking-wider block">Simpanan Pokok <span class="text-red-500">*</span></label>
                            <div class="grid grid-cols-2 gap-2">
                                <select name="tpp_mulai_pokok" required class="w-full px-3 py-2.5 bg-white border border-stone-200 rounded-xl text-xs focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] outline-none text-stone-700 cursor-pointer appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23a8a29e%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E')] bg-[length:10px_10px] bg-no-repeat bg-[position:right_10px_center] pr-6 shadow-sm">
                                    @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ old('tpp_mulai_pokok', now()->addMonth()->month) == $m ? 'selected' : '' }}>{{ nama_bulan($m) }}</option>
                                    @endfor
                                </select>
                                <select name="tpp_tahun_pokok" required class="w-full px-3 py-2.5 bg-white border border-stone-200 rounded-xl text-xs focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] outline-none text-stone-700 cursor-pointer appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23a8a29e%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E')] bg-[length:10px_10px] bg-no-repeat bg-[position:right_10px_center] pr-6 shadow-sm">
                                    @foreach(range(now()->year, now()->year + 2) as $y)
                                    <option value="{{ $y }}" {{ old('tpp_tahun_pokok', now()->addMonth()->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Potongan Simpanan Wajib --}}
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-stone-500 uppercase tracking-wider block">Simpanan Wajib <span class="text-red-500">*</span></label>
                            <div class="grid grid-cols-2 gap-2">
                                <select name="tpp_mulai_wajib" required class="w-full px-3 py-2.5 bg-white border border-stone-200 rounded-xl text-xs focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] outline-none text-stone-700 cursor-pointer appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23a8a29e%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E')] bg-[length:10px_10px] bg-no-repeat bg-[position:right_10px_center] pr-6 shadow-sm">
                                    @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ old('tpp_mulai_wajib', now()->addMonth()->month) == $m ? 'selected' : '' }}>{{ nama_bulan($m) }}</option>
                                    @endfor
                                </select>
                                <select name="tpp_tahun_wajib" required class="w-full px-3 py-2.5 bg-white border border-stone-200 rounded-xl text-xs focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] outline-none text-stone-700 cursor-pointer appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23a8a29e%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E')] bg-[length:10px_10px] bg-no-repeat bg-[position:right_10px_center] pr-6 shadow-sm">
                                    @foreach(range(now()->year, now()->year + 2) as $y)
                                    <option value="{{ $y }}" {{ old('tpp_tahun_wajib', now()->addMonth()->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <x-slot name="footer">
                <button type="button" @click="$dispatch('close-modal', 'modal-tambah-anggota')" class="px-4 py-2.5 text-sm font-bold text-stone-500 hover:text-stone-800 hover:bg-stone-200/50 rounded-xl transition-all">Batalkan</button>
                <button type="submit" form="form-tambah-anggota" class="px-6 py-2.5 bg-[#043d2e] hover:bg-[#043d2e]/90 text-white text-sm font-bold rounded-xl shadow-sm transition-all active:scale-95">Simpan Anggota Baru</button>
            </x-slot>
        </form>
    </x-modal>

    {{-- ============================================ --}}
    {{-- MODAL: Edit Anggota --}}
    {{-- ============================================ --}}
    <x-modal name="modal-edit-anggota" title="Edit Data Anggota" maxWidth="xl">
        <form id="form-edit-anggota" :action="editFormAction" method="POST" x-data="{ nip: '' }" x-init="
            $watch('editData', (val) => { if(val) nip = val.nip || ''; })
        " class="contents">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                {{-- NIP --}}
                <div class="sm:col-span-2 space-y-1.5">
                    <label class="text-[11px] font-bold text-stone-500 uppercase tracking-wider block">NIP Anggota <span class="text-red-500">*</span></label>
                    <x-nip-input name="nip" model="nip" placeholder="Masukkan 18 digit angka NIP..." required="true" class="bg-stone-50 text-stone-800 font-mono tracking-wider focus:bg-white focus:ring-[#043d2e]/20 focus:border-[#043d2e]" />
                </div>
                
                {{-- Nama --}}
                <div class="space-y-1.5">
                    <label class="text-[11px] font-bold text-stone-500 uppercase tracking-wider block">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="nama" :value="editData?.nama" required placeholder="Contoh: Budi Santoso"
                           class="w-full px-4 py-3 bg-stone-50 border border-stone-200 rounded-xl text-sm focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] outline-none text-stone-700 transition-all shadow-sm">
                </div>

                {{-- Golongan ASN --}}
                <div class="space-y-1.5">
                    <label class="text-[11px] font-bold text-stone-500 uppercase tracking-wider block">Golongan ASN <span class="text-red-500">*</span></label>
                    <select name="golongan_asn" required x-effect="if(editData) $el.value = editData.golongan_asn || ''" class="w-full px-4 py-3 bg-stone-50 border border-stone-200 rounded-xl text-sm focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] outline-none text-stone-700 cursor-pointer appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23a8a29e%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E')] bg-[length:10px_10px] bg-no-repeat bg-[position:right_12px_center] pr-8 shadow-sm transition-all">
                        <option value="">-- Pilih Golongan --</option>
                        @foreach(\App\Enums\GolonganAsn::cases() as $gol)
                            <option value="{{ $gol->value }}">{{ $gol->label() }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Bidang --}}
                <div class="space-y-1.5">
                    <label class="text-[11px] font-bold text-stone-500 uppercase tracking-wider block">Bidang Dinas / Unit <span class="text-red-500">*</span></label>
                    <select name="bidang_id" required x-effect="if(editData) $el.value = editData.bidang_id || ''" class="w-full px-4 py-3 bg-stone-50 border border-stone-200 rounded-xl text-sm focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] outline-none text-stone-700 cursor-pointer appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23a8a29e%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E')] bg-[length:10px_10px] bg-no-repeat bg-[position:right_12px_center] pr-8 shadow-sm transition-all">
                        <option value="">-- Pilih Ruang Lingkup --</option>
                        @foreach($bidangs as $bdg)
                            <option value="{{ $bdg->id }}">{{ $bdg->nama_bidang }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- No HP --}}
                <div class="space-y-1.5">
                    <label class="text-[11px] font-bold text-stone-500 uppercase tracking-wider block">No Handphone (WA)</label>
                    <input type="text" name="no_hp" :value="editData?.no_hp" placeholder="Contoh: 08123456789"
                           class="w-full px-4 py-3 bg-stone-50 border border-stone-200 rounded-xl text-sm focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] outline-none text-stone-700 transition-all shadow-sm">
                </div>
            </div>

            <x-slot name="footer">
                <button type="button" @click="$dispatch('close-modal', 'modal-edit-anggota')" class="px-4 py-2.5 text-sm font-bold text-stone-500 hover:text-stone-800 hover:bg-stone-200/50 rounded-xl transition-all">Batalkan</button>
                <button type="submit" form="form-edit-anggota" class="px-6 py-2.5 bg-[#043d2e] hover:bg-[#043d2e]/90 text-white text-sm font-bold rounded-xl shadow-sm transition-all active:scale-95">Simpan Perubahan</button>
            </x-slot>
        </form>
    </x-modal>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('anggotaPage', () => ({
        // Filter state
        q: new URLSearchParams(location.search).get('q') || '',
        bidang: new URLSearchParams(location.search).get('bidang') || '',
        golongan: new URLSearchParams(location.search).get('golongan') || '',
        status: new URLSearchParams(location.search).get('status') || '',
        loading: false,
        timeout: null,
        abortController: null,

        // Edit modal state
        editData: null,
        editFormAction: '',

        openEditModal(anggota) {
            this.editData = anggota;
            this.editFormAction = `{{ url('/anggota') }}/${anggota.id}`;
            this.$dispatch('open-modal', 'modal-edit-anggota');
        },

        init() {
            this.$watch('q', () => this.debouncedFetch());
            this.$watch('bidang', () => this.fetchData());
            this.$watch('golongan', () => this.fetchData());
            this.$watch('status', () => this.fetchData());

            window.addEventListener('anggota-paginate', (e) => {
                this.fetchData(e.detail.url);
            });
        },

        get activeFiltersCount() {
            let count = 0;
            if (this.bidang !== '') count++;
            if (this.golongan !== '') count++;
            if (this.status !== '') count++;
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
                if (this.status !== '') params.append('status', this.status);
                
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
                
                const newBadge = doc.getElementById('total-anggota-badge');
                const oldBadge = document.getElementById('total-anggota-badge');
                if (newBadge && oldBadge) {
                    oldBadge.innerText = newBadge.innerText;
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
