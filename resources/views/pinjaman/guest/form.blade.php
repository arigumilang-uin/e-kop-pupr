@extends('layouts.guest')

@section('title', 'Pengajuan Pinjaman - Tirta Bina Karya PUPR PKPP')

@section('content')
<!-- Minimalist Top Navigation Component -->
<x-guest-nav />

<main class="max-w-6xl mx-auto px-4 py-8 md:py-16"
    x-data="simulasiPinjaman({
        bungaPersen: {{ $pengaturan['bunga_persen'] }},
        swpPersen: {{ $pengaturan['swp_persen'] }},
        resikoPersen: {{ $pengaturan['resiko_persen'] }},
        adminPersen: {{ $pengaturan['admin_persen'] }},
        nominal: {{ old('nominal_pinjaman', $pengaturan['nominal_min']) }},
        tenor: {{ old('tenor_bulan', min(12, $pengaturan['tenor_maks'])) }},
        minNominal: {{ $pengaturan['nominal_min'] }},
        maxNominal: {{ $pengaturan['limit'] }},
        stepNominal: {{ $pengaturan['kelipatan'] }}
    })">
    
    <!-- Premium Header Area -->
    <div class="mb-10 text-left max-w-2xl">
        <h1 class="font-['Plus_Jakarta_Sans'] text-3xl md:text-4xl font-extrabold tracking-tight text-[#0f172a] mb-4">
            Portal Pengajuan Pinjaman
        </h1>
        
        <p class="text-slate-500 text-[14px] leading-relaxed">
            Lengkapi formulir di bawah ini untuk mengajukan pinjaman pada <span class="font-semibold text-slate-700">Periode {{ $periode->nama_periode }}</span>. Sistem akan mensimulasikan rincian angsuran secara real-time.
        </p>

        {{-- Info Periode Aktif --}}
        <div class="mt-4 flex flex-wrap items-center gap-3">
            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-100 border border-slate-200 text-slate-600 text-xs">
                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span>Buka: <strong>{{ $periode->tanggal_buka->translatedFormat('d F Y') }}</strong></span>
            </div>
            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-100 border border-slate-200 text-slate-600 text-xs">
                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Tutup: <strong>{{ $periode->tanggal_tutup->translatedFormat('d F Y') }}</strong></span>
            </div>
            @php $sisaHari = now()->startOfDay()->diffInDays($periode->tanggal_tutup, false); @endphp
            @if($sisaHari >= 0 && $sisaHari <= 7)
            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-amber-50 border border-amber-200 text-amber-700 text-xs font-semibold">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                {{ $sisaHari == 0 ? 'Hari terakhir!' : $sisaHari . ' hari lagi ditutup' }}
            </div>
            @endif
        </div>
    </div>

    @if($errors->any() || session('error'))
    <div class="mb-8 p-5 rounded-2xl bg-red-50/50 border border-red-100 flex items-start gap-4">
        <div class="bg-white p-2 rounded-full shadow-sm shrink-0">
            <svg class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <div>
            <h3 class="text-red-800 font-bold text-[14px] mb-1.5">Gagal memproses pengajuan</h3>
            <ul class="text-red-600/80 text-[13px] list-disc list-inside space-y-1">
                @if(session('error'))
                    <li>{{ session('error') }}</li>
                @endif
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    <form method="POST" action="{{ route('pinjaman.guest.review', $periode->token) }}" class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        @csrf

        <!-- Left Column: Modern Forms -->
        <div class="lg:col-span-7 space-y-8">
            
            <!-- Section 1: Data Keanggotaan -->
            <x-card>
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-700">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                    </div>
                    <h2 class="text-lg font-bold text-slate-900 tracking-tight">Data Keanggotaan</h2>
                </div>
                
                <div>
                    <x-label for="nip">Nomor Induk Pegawai (NIP)</x-label>
                    <x-nip-input id="nip" name="nip" model="nip" required="true" placeholder="Contoh: 19800101...">
                        <x-slot name="icon">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </x-slot>
                    </x-nip-input>
                    <p class="mt-2 text-[12px] font-medium" :class="(nip.length > 0 && nip.length !== 18) ? 'text-red-500' : 'text-slate-500'">
                        <span x-show="nip.length === 0 || nip.length === 18">Sistem secara otomatis memvalidasi NIP menggunakan data anggota koperasi.</span>
                        <span x-show="nip.length > 0 && nip.length !== 18">NIP harus tepat 18 digit angka (saat ini <span x-text="nip.length"></span> digit).</span>
                    </p>
                </div>
            </x-card>

            <!-- Section 2: Rekening Pencairan -->
            <x-card>
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-700">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    </div>
                    <h2 class="text-lg font-bold text-slate-900 tracking-tight">Rekening Pencairan</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="md:col-span-2">
                        <x-label for="nama_bank">Nama Bank</x-label>
                        <x-input id="nama_bank" name="nama_bank" value="{{ old('nama_bank', 'BRK Syariah') }}" list="bank-list" autocomplete="off" required placeholder="Ketik atau pilih nama bank..."/>
                        <datalist id="bank-list">
                            <option value="BRK Syariah"></option>
                            <option value="Bank Mandiri"></option>
                            <option value="Bank Negara Indonesia (BNI)"></option>
                            <option value="Bank Rakyat Indonesia (BRI)"></option>
                            <option value="Bank Syariah Indonesia (BSI)"></option>
                            <option value="Bank Central Asia (BCA)"></option>
                            <option value="Bank Tabungan Negara (BTN)"></option>
                            <option value="Bank Mega"></option>
                            <option value="Bank CIMB Niaga"></option>
                            <option value="Bank Danamon"></option>
                            <option value="Bank BTPN"></option>
                            <option value="Bank Muamalat"></option>
                        </datalist>
                    </div>
                    <div>
                        <x-label for="no_rekening">Nomor Rekening</x-label>
                        <x-input id="no_rekening" name="no_rekening" x-model="no_rekening" @input="no_rekening = no_rekening.replace(/[^0-9]/g, '')" required placeholder="Digit angka saja"/>
                        <p class="mt-1 text-[11px] text-red-500" x-show="no_rekening.length > 0 && no_rekening.length < 4">Nomor rekening tidak valid.</p>
                    </div>
                    <div>
                        <x-label for="nama_rekening">Nama Pemilik Rekening</x-label>
                        <x-input id="nama_rekening" name="nama_rekening" value="{{ old('nama_rekening') }}" required placeholder="Sesuai buku tabungan"/>
                    </div>
                </div>
            </x-card>

            <!-- Section 3: Konfigurasi Pinjaman -->
            <x-card>
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-700">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                    </div>
                    <h2 class="text-lg font-bold text-slate-900 tracking-tight">Konfigurasi Pinjaman</h2>
                </div>
                <div class="space-y-8">
                    <div>
                        <x-label for="nominal_pinjaman" class="mb-3">Nominal Pinjaman (Rp)</x-label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 font-mono font-bold text-slate-400">Rp</span>
                            <input type="text" inputmode="numeric" id="nominal_pinjaman_display" required x-model="nominalDisplay" @input="updateNominal($event.target.value)"
                                   class="w-full h-16 pl-12 pr-4 rounded-xl border border-slate-200/80 outline-none focus:border-slate-800 focus:ring-4 focus:ring-slate-800/5 transition-all text-2xl font-mono font-bold text-slate-900 bg-slate-50"/>
                            <!-- Hidden actual value for submission -->
                            <input type="hidden" name="nominal_pinjaman" :value="nominal">
                        </div>
                        
                        <p class="mt-2 text-[12px] text-red-500 font-medium" x-show="nominalError !== ''" x-text="nominalError" x-transition></p>
                        <div class="flex justify-between mt-2.5 text-[11px] text-slate-500 uppercase tracking-widest font-bold">
                            <span>Min: <span x-text="formatRp(minNominal)"></span></span>
                            <span>Maks: <span x-text="formatRp(maxNominal)"></span></span>
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1" x-show="stepNominal > 1">
                            Kelipatan: <span class="font-mono" x-text="formatRp(stepNominal)"></span>
                        </p>
                    </div>
                    <div>
                        <div class="flex justify-between items-end mb-4">
                            <x-label for="tenor_bulan" class="!mb-0">Tenor Pinjaman</x-label>
                            <span class="text-2xl font-mono font-bold text-slate-900" x-text="tenor"><span class="text-[13px] font-['Inter'] text-slate-500 font-semibold ml-1">Bulan</span></span>
                        </div>
                        <div class="px-1">
                            <input type="range" id="tenor_bulan" name="tenor_bulan" required
                                   min="{{ $pengaturan['tenor_min'] }}" max="{{ $pengaturan['tenor_maks'] }}" 
                                   x-model="tenor" class="w-full h-6 cursor-pointer appearance-none bg-transparent outline-none [&::-webkit-slider-runnable-track]:h-[2px] [&::-webkit-slider-runnable-track]:bg-slate-400 [&::-webkit-slider-runnable-track]:rounded-full [&::-moz-range-track]:h-[2px] [&::-moz-range-track]:bg-slate-400 [&::-moz-range-track]:rounded-full [&::-webkit-slider-thumb]:appearance-none [&::-webkit-slider-thumb]:w-6 [&::-webkit-slider-thumb]:h-6 [&::-webkit-slider-thumb]:rounded-full [&::-webkit-slider-thumb]:bg-slate-900 [&::-webkit-slider-thumb]:ring-4 [&::-webkit-slider-thumb]:ring-slate-900/10 [&::-webkit-slider-thumb]:cursor-pointer [&::-webkit-slider-thumb]:-mt-[11px] [&::-moz-range-thumb]:w-6 [&::-moz-range-thumb]:h-6 [&::-moz-range-thumb]:rounded-full [&::-moz-range-thumb]:bg-slate-900 [&::-moz-range-thumb]:border-none [&::-moz-range-thumb]:cursor-pointer transition-all"/>
                            <div class="flex justify-between mt-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                <span>{{ $pengaturan['tenor_min'] }} Bln</span>
                                <span>{{ $pengaturan['tenor_maks'] }} Bln</span>
                            </div>
                        </div>
                    </div>
                </div>
            </x-card>
            
            @if(session('needs_override_confirmation'))
            <div class="bg-amber-50 rounded-2xl p-6 border border-amber-200 text-amber-900">
                <div class="flex gap-4">
                    <svg class="w-6 h-6 text-amber-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <div>
                        <h3 class="font-bold mb-1">Konfirmasi Pengajuan Tambahan</h3>
                        <p class="text-amber-800 text-sm mb-4 leading-relaxed">
                            {{ session('needs_override_confirmation') }}
                        </p>
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="checkbox" name="confirm_override" value="1" required
                                   class="w-5 h-5 rounded border-amber-300 bg-white text-amber-600 focus:ring-amber-500">
                            <span class="text-[13px] font-bold text-amber-900 group-hover:text-amber-700 transition-colors">Saya mengerti dan tetap ajukan pinjaman ini</span>
                        </label>
                    </div>
                </div>
            </div>
            @endif

        </div>

        <!-- Right Column: Premium Summary -->
        <div class="lg:col-span-5">
            <div class="bg-slate-900 rounded-[28px] p-8 text-white shadow-2xl lg:sticky lg:top-24 relative overflow-hidden">
                <!-- Abstract Glow -->
                <div class="absolute -top-24 -right-24 w-64 h-64 bg-slate-800 rounded-full blur-3xl opacity-50"></div>

                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-8 pb-6 border-b border-white/10">
                        <h2 class="text-xl font-bold tracking-tight">Invoice Simulasi</h2>
                        <svg class="w-6 h-6 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    
                    <div class="space-y-8">
                        <div>
                            <span class="block text-[10px] uppercase tracking-widest text-slate-400 font-bold mb-2">Pokok Pinjaman</span>
                            <div class="text-[28px] font-mono font-extrabold tracking-tight" x-text="formatRp(nominal)"></div>
                        </div>

                        <!-- Ketentuan -->
                        <div class="bg-white/5 rounded-2xl p-5 space-y-3 border border-white/5">
                            <div class="flex justify-between items-center text-[13px]">
                                <span class="text-slate-400 font-medium">Total Bunga (<span x-text="formatPersen({{ $pengaturan['bunga_persen'] }}) + '%'"></span>)</span>
                                <span class="font-mono text-emerald-400 font-semibold" x-text="formatRp(totalBunga)"></span>
                            </div>
                            <div class="flex justify-between items-center text-[13px]">
                                <span class="text-slate-400 font-medium">Tenor</span>
                                <span class="font-mono font-semibold" x-text="tenor + ' Bln'"></span>
                            </div>
                            <div class="pt-3 mt-3 border-t border-white/10 flex flex-col justify-center gap-1.5">
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-sm font-bold text-slate-200">Angsuran / Bulan</span>
                                    <span class="font-mono font-bold text-lg text-white" x-text="formatRp(totalAngsuran)"></span>
                                </div>
                                <div class="flex justify-between items-center text-[11px] text-slate-400 font-mono tracking-wide">
                                    <span>Pokok</span>
                                    <span x-text="formatRp(angsuranPokok)"></span>
                                </div>
                                <div class="flex justify-between items-center text-[11px] text-slate-400 font-mono tracking-wide">
                                    <span>Bunga</span>
                                    <span x-text="formatRp(angsuranBunga)"></span>
                                </div>
                            </div>
                            <div class="pt-3 mt-3 border-t border-white/10 flex justify-between items-center">
                                <span class="text-[13px] font-medium text-slate-300">Total Pengembalian</span>
                                <span class="font-mono font-bold text-emerald-400" x-text="formatRp(totalBayar)"></span>
                            </div>
                        </div>

                        <!-- Deduction (Accordion like view) -->
                        <div class="space-y-4">
                            <h3 class="text-[11px] font-bold text-slate-500 uppercase tracking-widest flex items-center justify-between">
                                <span>Potongan Awal (Deduction)</span>
                                <span class="text-amber-400" x-text="formatPersen(totalPersenPotongan) + '%'"></span>
                            </h3>
                            <div class="space-y-2.5">
                                <div class="flex justify-between text-[13px] text-slate-400">
                                    <span>SWP (<span x-text="formatPersen({{ $pengaturan['swp_persen'] }})"></span>%)</span>
                                    <span class="font-mono" x-text="formatRp(potonganSwp)"></span>
                                </div>
                                <div class="flex justify-between text-[13px] text-slate-400">
                                    <span>Dana Resiko (<span x-text="formatPersen({{ $pengaturan['resiko_persen'] }})"></span>%)</span>
                                    <span class="font-mono" x-text="formatRp(potonganResiko)"></span>
                                </div>
                                <div class="flex justify-between text-[13px] text-slate-400">
                                    <span>Administrasi (<span x-text="formatPersen({{ $pengaturan['admin_persen'] }})"></span>%)</span>
                                    <span class="font-mono" x-text="formatRp(potonganAdmin)"></span>
                                </div>
                            </div>
                            <div class="flex justify-between items-center pt-3 border-t border-white/10 text-sm">
                                <span class="font-semibold text-slate-300">Total Potongan</span>
                                <span class="font-mono font-bold text-white text-base" x-text="'- ' + formatRp(totalPotongan)"></span>
                            </div>
                        </div>

                        <!-- Pencairan -->
                        <div class="pt-6 border-t border-white/10">
                            <span class="block text-[11px] font-bold text-emerald-400 tracking-widest uppercase mb-1">Pencairan Bersih (Diterima)</span>
                            <div class="text-3xl font-mono font-extrabold text-white" x-text="formatRp(danaDiterima)"></div>
                        </div>

                        <!-- Action -->
                        <div class="pt-6">
                            <button type="submit" 
                                    :disabled="!isValid"
                                    :class="!isValid ? 'opacity-50 cursor-not-allowed bg-slate-300 text-slate-500' : 'bg-white text-slate-900 hover:bg-slate-100'"
                                    class="w-full h-14 rounded-xl font-bold text-[15px] transition-all flex items-center justify-center gap-2 group outline-none shadow-xl shadow-white/10">
                                Lanjut & Periksa Data
                                <svg class="w-5 h-5 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <footer class="mt-20 text-center space-y-3">
        <x-guest-footer />
        <div class="flex items-center justify-center gap-2 text-slate-400">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            <span class="text-[11px] font-bold tracking-widest uppercase">Secure Encrypted Portal</span>
        </div>
    </footer>
</main>

<!-- Alpine JS untuk Kalkulasi Dinamis -->
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('simulasiPinjaman', (config) => ({
            nip: '{{ old('nip', '') }}',
            no_rekening: '{{ old('no_rekening', '') }}',
            nominal: config.nominal,
            nominalDisplay: config.nominal.toString().replace(/\B(?=(\d{3})+(?!\d))/g, "."),
            tenor: config.tenor,
            minNominal: config.minNominal,
            maxNominal: config.maxNominal,
            stepNominal: config.stepNominal,
            nominalError: '',

            updateNominal(val) {
                let raw = String(val).replace(/[^0-9]/g, '');
                if(raw === '') raw = '0';
                
                let num = parseInt(raw, 10);
                this.nominal = num;
                this.nominalDisplay = num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                
                this.nominalError = '';
                if (this.nominal > 0) {
                    if (this.nominal < this.minNominal) {
                        this.nominalError = `Minimum pinjaman adalah ${this.formatRp(this.minNominal)}.`;
                    } else if (this.nominal > this.maxNominal) {
                        this.nominalError = `Maksimum pinjaman adalah ${this.formatRp(this.maxNominal)}.`;
                    } else if (this.nominal % this.stepNominal !== 0) {
                        this.nominalError = `Nominal harus kelipatan ${this.formatRp(this.stepNominal)}.`;
                    }
                }
            },
            
            get isValid() {
                let validNominal = this.nominal >= this.minNominal && this.nominal <= this.maxNominal && (this.nominal % this.stepNominal === 0);
                return validNominal && this.tenor >= 1 && this.nip.length === 18 && this.no_rekening.length >= 4;
            },
            
            get totalBunga() {
                return (this.nominal * (config.bungaPersen / 100));
            },
            
            get angsuranPokok() {
                return (this.nominal > 0) ? (this.nominal / this.tenor) : 0;
            },
            
            get angsuranBunga() {
                return (this.nominal > 0) ? (this.totalBunga / this.tenor) : 0;
            },
            
            get totalAngsuran() {
                return this.angsuranPokok + this.angsuranBunga;
            },

            get totalBayar() {
                return this.nominal + this.totalBunga;
            },
            
            get potonganSwp() {
                return (this.nominal > 0) ? (this.nominal * (config.swpPersen / 100)) : 0;
            },

            get potonganResiko() {
                return (this.nominal > 0) ? (this.nominal * (config.resikoPersen / 100)) : 0;
            },

            get potonganAdmin() {
                return (this.nominal > 0) ? (this.nominal * (config.adminPersen / 100)) : 0;
            },

            get totalPersenPotongan() {
                return config.swpPersen + config.resikoPersen + config.adminPersen;
            },

            get totalPotongan() {
                return this.potonganSwp + this.potonganResiko + this.potonganAdmin;
            },
            
            get danaDiterima() {
                return (this.nominal > 0) ? (this.nominal - this.totalPotongan) : 0;
            },
            
            // Formatters
            formatRp(val) {
                return 'Rp ' + Math.round(val || 0).toLocaleString('id-ID');
            },

            formatRpPendek(val) {
                return Math.round(val || 0).toLocaleString('id-ID');
            },

            formatPersen(val) {
                let num = parseFloat(val);
                if (isNaN(num)) return '0';
                return Number.isInteger(num) ? num.toString() : num.toFixed(2).replace(/\.?0+$/, '');
            }
        }));
    });
</script>
@endsection
