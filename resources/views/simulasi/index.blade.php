@extends('layouts.guest')
@section('title', 'Simulasi Publik - Tirta Bina Karya PUPR PKPP')

@section('content')
<x-guest-nav />

<main class="max-w-6xl mx-auto px-4 py-8 md:py-16"
    x-data="simulasiPublik({
        bungaPersen: {{ $pengaturan['bunga_persen'] }},
        swpPersen: {{ $pengaturan['swp_persen'] }},
        resikoPersen: {{ $pengaturan['resiko_persen'] }},
        adminPersen: {{ $pengaturan['admin_persen'] }},
        nominal: 5000000,
        tenor: 8
    })">
    
    <!-- Premium Header Area -->
    <div class="mb-10 text-left max-w-2xl">
        <h1 class="font-['Plus_Jakarta_Sans'] text-3xl md:text-4xl font-extrabold tracking-tight text-[#0f172a] mb-4">
            Simulasi Pinjaman Publik
        </h1>
        <p class="text-slate-500 text-[14px] leading-relaxed">
            Eksplorasi kalkulasi pinjaman secara mandiri. Gunakan penggeser interaktif atau input angka untuk mengetahui skema pembiayaan paling optimal.
        </p>
    </div>

    <!-- Grid Layout (Identical to Form) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Kiri: Konfigurasi Pinjaman & Jadwal Angsuran -->
        <div class="lg:col-span-7 space-y-8">
            <x-card class="bg-white">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-700">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                    </div>
                    <h2 class="text-lg font-bold text-slate-900 tracking-tight">Konfigurasi Simulasi</h2>
                </div>
                
                <div class="space-y-8">
                    <!-- Input Nominal -->
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <label class="text-sm font-semibold text-slate-700">Nominal Pinjaman (Rp)</label>
                        </div>
                        <div class="relative flex items-center">
                            <div class="absolute left-0 pl-4 text-slate-500 font-bold">Rp</div>
                            <input type="text"
                                x-model="nominalDisplay"
                                @input="updateNominal($event.target.value)"
                                class="w-full h-14 pl-12 pr-4 bg-slate-50 border border-slate-200 rounded-xl text-lg font-mono font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-slate-900 transition-colors">
                        </div>
                        <p class="text-[12px] font-medium text-slate-500 mt-2 px-1">Ketik nominal tanpa titik atau koma.</p>
                    </div>

                    <!-- Input Tenor -->
                    <div>
                        <div class="flex justify-between items-center mb-4">
                            <label class="text-sm font-semibold text-slate-700">Tenor Pinjaman</label>
                            <div class="px-3 py-1 bg-slate-100 rounded-lg border border-slate-200">
                                <span class="font-mono font-bold text-slate-900" x-text="tenor"></span>
                                <span class="text-xs font-medium text-slate-500 ml-1">Bulan</span>
                            </div>
                        </div>
                        <div class="px-2">
                            <input type="range" 
                                x-model.number="tenor" 
                                min="{{ $pengaturan['tenor_min'] }}" 
                                max="12" 
                                step="1"
                                class="w-full h-6 bg-transparent appearance-none cursor-pointer [&::-webkit-slider-runnable-track]:h-[2px] [&::-webkit-slider-runnable-track]:bg-slate-400 [&::-webkit-slider-runnable-track]:rounded-full [&::-moz-range-track]:h-[2px] [&::-moz-range-track]:bg-slate-400 [&::-moz-range-track]:rounded-full [&::-webkit-slider-thumb]:appearance-none [&::-webkit-slider-thumb]:w-6 [&::-webkit-slider-thumb]:h-6 [&::-webkit-slider-thumb]:rounded-full [&::-webkit-slider-thumb]:bg-slate-900 [&::-webkit-slider-thumb]:ring-4 [&::-webkit-slider-thumb]:ring-slate-900/10 [&::-webkit-slider-thumb]:cursor-pointer [&::-webkit-slider-thumb]:-mt-[11px] [&::-moz-range-thumb]:w-6 [&::-moz-range-thumb]:h-6 [&::-moz-range-thumb]:rounded-full [&::-moz-range-thumb]:bg-slate-900 [&::-moz-range-thumb]:border-none [&::-moz-range-thumb]:cursor-pointer transition-all">
                            <div class="flex justify-between mt-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                <span>{{ $pengaturan['tenor_min'] }} Bln</span>
                                <span>12 Bln</span>
                            </div>
                        </div>
                    </div>
                </div>
            </x-card>
            
            <x-card class="bg-white overflow-hidden p-0 sm:p-0 md:p-0 border border-slate-200 shadow-sm">
                 <div class="flex items-center gap-3 p-6 md:p-8 border-b border-slate-100">
                    <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-700">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </div>
                    <h2 class="text-lg font-bold text-slate-900 tracking-tight">Tabel Amortisasi (Jadwal Angsuran)</h2>
                </div>
                
                <div class="overflow-x-auto w-full">
                    <table class="w-full text-left text-sm border-collapse min-w-[500px]">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200">
                                <th class="px-6 py-4 text-[11px] text-slate-500 font-bold uppercase tracking-widest whitespace-nowrap">Ke</th>
                                <th class="px-6 py-4 text-[11px] text-slate-500 font-bold uppercase tracking-widest whitespace-nowrap">Pokok</th>
                                <th class="px-6 py-4 text-[11px] text-slate-500 font-bold uppercase tracking-widest whitespace-nowrap">Bunga</th>
                                <th class="px-6 py-4 text-[11px] text-slate-500 font-bold uppercase tracking-widest whitespace-nowrap">Total</th>
                                <th class="px-6 py-4 text-[11px] text-slate-500 font-bold uppercase tracking-widest whitespace-nowrap text-right">Sisa Pokok</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <template x-for="row in jadwalAngsuran" :key="row.bulan">
                                <tr class="hover:bg-slate-50/50 transition-colors" :class="row.isLast ? 'bg-emerald-50/30' : ''">
                                    <td class="px-6 py-3.5 text-[13px] font-bold text-slate-900" x-text="'Bulan ' + row.bulan"></td>
                                    <td class="px-6 py-3.5 text-[13px] font-mono text-slate-600" x-text="formatRp(row.pokok)"></td>
                                    <td class="px-6 py-3.5 text-[13px] font-mono text-slate-600" x-text="formatRp(row.bunga)"></td>
                                    <td class="px-6 py-3.5 text-[13px] font-mono font-bold text-[#0f172a]" x-text="formatRp(row.total)"></td>
                                    <td class="px-6 py-3.5 text-[13px] font-mono text-right" :class="row.isLast ? 'text-emerald-600 font-bold' : 'text-slate-500'" x-text="formatRp(row.sisa)"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>

        <!-- Kanan: Invoice Simulasi -->
        <div class="lg:col-span-5 h-full">
            <div class="sticky top-28 space-y-6">
                <!-- Rincian Identik dengan form.blade.php -->
                <div class="bg-[#0f172a] rounded-[24px] p-6 lg:p-8 text-white relative overflow-hidden shadow-2xl shadow-slate-900/20">
                    <div class="absolute -right-20 -top-20 opacity-[0.03] pointer-events-none">
                        <svg width="300" height="300" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2L2 22h20L12 2zm0 4.5l6.5 13.5h-13L12 6.5z"/></svg>
                    </div>
                    
                    <div class="relative z-10 flex flex-col h-full space-y-8">
                        <div>
                            <div class="flex items-center gap-2 mb-2 text-blue-400">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <h3 class="text-xs font-bold tracking-widest uppercase">Invoice Simulasi</h3>
                            </div>
                        </div>

                        <div>
                            <p class="text-slate-400 text-[13px] font-medium mb-1">Pokok Pinjaman</p>
                            <div class="text-[32px] font-mono font-extrabold text-white tracking-tight" x-text="formatRp(nominal)"></div>
                        </div>

                        <div class="bg-white/5 rounded-2xl p-5 space-y-3 border border-white/5">
                            <div class="flex justify-between items-center text-[13px]">
                                <span class="text-slate-400 font-medium">Total Bunga (<span x-text="formatPersen(configVals.bungaPersen) + '%'"></span>)</span>
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

                        <div class="bg-white/5 rounded-2xl border border-white/5 overflow-hidden">
                            <div class="px-5 py-3 border-b border-white/5 bg-white/5">
                                <span class="text-slate-300 text-[11px] font-bold tracking-widest uppercase">Potongan Awal (Deduction)</span>
                            </div>
                            <div class="p-5 space-y-3">
                                <div class="flex justify-between items-center text-[13px]">
                                    <span class="text-slate-400 font-medium">SWP (<span x-text="formatPersen(configVals.swpPersen) + '%'"></span>)</span>
                                    <span class="font-mono text-slate-300 font-semibold" x-text="formatRp(potonganSwp)"></span>
                                </div>
                                <div class="flex justify-between items-center text-[13px]">
                                    <span class="text-slate-400 font-medium">Dana Resiko (<span x-text="formatPersen(configVals.resikoPersen) + '%'"></span>)</span>
                                    <span class="font-mono text-slate-300 font-semibold" x-text="formatRp(potonganResiko)"></span>
                                </div>
                                <div class="flex justify-between items-center text-[13px]">
                                    <span class="text-slate-400 font-medium">Administrasi (<span x-text="formatPersen(configVals.adminPersen) + '%'"></span>)</span>
                                    <span class="font-mono text-slate-300 font-semibold" x-text="formatRp(potonganAdmin)"></span>
                                </div>
                                <div class="pt-3 mt-3 border-t border-white/10 flex justify-between items-center">
                                    <span class="text-[12px] font-bold text-slate-400">Total Potongan</span>
                                    <span class="font-mono font-bold text-red-400 text-sm" x-text="'- ' + formatRp(totalPotongan)"></span>
                                </div>
                            </div>
                        </div>

                        <div class="bg-emerald-500/10 border border-emerald-500/20 rounded-2xl p-5 relative overflow-hidden group">
                            <div class="absolute inset-0 bg-gradient-to-r from-emerald-500/0 via-emerald-500/5 to-emerald-500/0 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-1000"></div>
                            <p class="text-emerald-400/80 text-[12px] font-bold tracking-widest uppercase mb-1">Pencairan Bersih (Diterima)</p>
                            <div class="text-3xl font-mono font-extrabold text-white" x-text="formatRp(danaDiterima)"></div>
                        </div>
                        
                        <div class="pt-4">
                            <a href="{{ route('pinjaman.guest.form_redirect') }}" class="w-full text-center h-14 rounded-xl font-bold text-[15px] bg-white text-slate-900 border border-transparent shadow-lg hover:bg-slate-100 transition-all flex items-center justify-center gap-2 group">
                                Buat Pengajuan Resmi
                                <svg class="w-5 h-5 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l7-7m-7 7h18"/></svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <footer class="mt-20 text-center space-y-3">
        <x-guest-footer />
    </footer>
</main>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('simulasiPublik', (config) => ({
            configVals: config,
            nominal: config.nominal,
            nominalDisplay: config.nominal.toString().replace(/\B(?=(\d{3})+(?!\d))/g, "."),
            tenor: config.tenor,

            updateNominal(val) {
                let raw = String(val).replace(/[^0-9]/g, '');
                if(raw === '') raw = '0';
                
                let num = parseInt(raw, 10);
                this.nominal = num;
                this.nominalDisplay = num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            },
            
            get totalBunga() {
                return (this.nominal * (this.configVals.bungaPersen / 100));
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
                return (this.nominal > 0) ? (this.nominal * (this.configVals.swpPersen / 100)) : 0;
            },
            get potonganResiko() {
                return (this.nominal > 0) ? (this.nominal * (this.configVals.resikoPersen / 100)) : 0;
            },
            get potonganAdmin() {
                return (this.nominal > 0) ? (this.nominal * (this.configVals.adminPersen / 100)) : 0;
            },
            get totalPersenPotongan() {
                return this.configVals.swpPersen + this.configVals.resikoPersen + this.configVals.adminPersen;
            },
            get totalPotongan() {
                return this.potonganSwp + this.potonganResiko + this.potonganAdmin;
            },
            get danaDiterima() {
                return (this.nominal > 0) ? (this.nominal - this.totalPotongan) : 0;
            },
            
            get jadwalAngsuran() {
                if (this.nominal <= 0) return [];
                let sisaPokok = this.nominal;
                let rows = [];
                for (let i = 1; i <= this.tenor; i++) {
                    sisaPokok -= this.angsuranPokok;
                    if (sisaPokok < 0) sisaPokok = 0;
                    rows.push({
                        bulan: i,
                        pokok: this.angsuranPokok,
                        bunga: this.angsuranBunga,
                        total: this.totalAngsuran,
                        sisa: sisaPokok,
                        isLast: i === parseInt(this.tenor)
                    });
                }
                return rows;
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
