@extends('layouts.app')

@section('title', 'Dashboard')
@section('subtitle', 'Selamat datang, ' . auth()->user()->nama)

@section('content')
<div class="space-y-6">
    
    {{-- ZONA 1: Ringkasan Utama (4 Kartu Persegi Panjang) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-6">
        {{-- Card 1: Piutang Koperasi --}}
        <div class="bg-white dark:bg-stone-900 border border-transparent dark:border-stone-800/40 rounded-[24px] p-6 shadow-sm hover:shadow-md transition-all duration-300 flex items-center justify-between gap-4">
            <div class="min-w-0">
                <span class="text-stone-400 dark:text-stone-500 text-[11px] font-bold uppercase tracking-widest block">Piutang Koperasi</span>
                <p class="text-2xl sm:text-3xl lg:text-[23px] xl:text-[25px] 2xl:text-[28px] font-black tracking-tight text-[#043d2e] dark:text-emerald-400 leading-none mt-2 truncate transition-colors">{{ format_rupiah($stats['piutang_berjalan']) }}</p>
                <p class="text-[10px] text-stone-500 dark:text-stone-400 mt-2 font-medium">Sisa pokok berjalan aktif</p>
            </div>
            <div class="shrink-0 text-[#043d2e]/80 dark:text-emerald-400/80">
                <svg class="w-16 h-9" viewBox="0 0 100 40" fill="none" stroke="currentColor" stroke-width="4.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M 0 30 Q 20 8 40 22 T 80 15 T 100 5"></path>
                </svg>
            </div>
        </div>

        {{-- Card 2: Simpanan Anggota --}}
        <div class="bg-white dark:bg-stone-900 border border-transparent dark:border-stone-800/40 rounded-[24px] p-6 shadow-sm hover:shadow-md transition-all duration-300 flex items-center justify-between gap-4">
            <div class="min-w-0">
                <span class="text-stone-400 dark:text-stone-500 text-[11px] font-bold uppercase tracking-widest block">Simpanan Anggota</span>
                <p class="text-2xl sm:text-3xl lg:text-[23px] xl:text-[25px] 2xl:text-[28px] font-black tracking-tight text-[#043d2e] dark:text-emerald-400 leading-none mt-2 truncate transition-colors">{{ format_rupiah($stats['total_simpanan']) }}</p>
                <p class="text-[10px] text-stone-500 dark:text-stone-400 mt-2 font-medium">Titipan dana anggota terhimpun</p>
            </div>
            <div class="shrink-0 text-[#043d2e]/80 dark:text-emerald-400/80">
                <svg class="w-16 h-9" viewBox="0 0 100 40" fill="none" stroke="currentColor" stroke-width="4.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M 0 35 Q 25 35 50 20 T 100 5"></path>
                </svg>
            </div>
        </div>

        {{-- Card 3: Total Aset (HIGHLIGHT FOREST GREEN) --}}
        <div x-data="{ openPopover: false }" @click.away="openPopover = false" @click="openPopover = !openPopover" class="bg-[#043d2e] dark:bg-emerald-955 border border-transparent dark:border-emerald-800/20 rounded-[24px] p-6 shadow-md shadow-emerald-955/20 hover:bg-[#033024] hover:shadow-lg transition-all duration-300 flex items-center justify-between gap-4 cursor-pointer text-white relative select-none">
            <div class="min-w-0">
                <div class="flex items-center gap-2">
                    <span class="text-emerald-200 text-[11px] font-bold uppercase tracking-widest">Total Aset</span>
                    <svg class="w-3.5 h-3.5 text-emerald-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>
                <p class="text-2xl sm:text-3xl lg:text-[23px] xl:text-[25px] 2xl:text-[28px] font-black tracking-tight text-white leading-none mt-2 truncate">{{ format_rupiah($stats['total_aset']) }}</p>
                <p class="text-[10px] text-emerald-200/75 mt-2 font-medium">Posisi laporan aset saat ini</p>
            </div>
            <div class="shrink-0 text-emerald-300">
                <svg class="w-16 h-9" viewBox="0 0 100 40" fill="none" stroke="currentColor" stroke-width="4.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M 0 25 Q 30 5 60 30 T 100 8"></path>
                </svg>
            </div>
            
            {{-- Popover --}}
            <div x-show="openPopover" style="display: none;" class="absolute top-full left-0 mt-2 z-50 min-w-[240px] bg-white dark:bg-stone-900 rounded-2xl shadow-xl border border-stone-200 dark:border-stone-850 p-4 ring-1 ring-black/5" @click.stop>
                <h4 class="text-[10px] font-bold text-stone-400 dark:text-stone-500 uppercase tracking-wider mb-3">Rincian Total Aset</h4>
                <div class="flex flex-col gap-2.5">
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-stone-600 dark:text-stone-300 font-medium flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-emerald-500 block"></span> Harta Lancar</span>
                        <span class="font-mono text-[#043d2e] dark:text-emerald-400 font-bold truncate pl-4">{{ format_rupiah($stats['breakdown_aset']['harta_lancar']) }}</span>
                    </div>
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-stone-600 dark:text-stone-300 font-medium flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-purple-500 block"></span> Penyertaan</span>
                        <span class="font-mono text-[#043d2e] dark:text-emerald-400 font-bold truncate pl-4">{{ format_rupiah($stats['breakdown_aset']['penyertaan']) }}</span>
                    </div>
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-stone-600 dark:text-stone-300 font-medium flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-amber-500 block"></span> Harta Tetap</span>
                        <span class="font-mono text-[#043d2e] dark:text-emerald-400 font-bold truncate pl-4">{{ format_rupiah($stats['breakdown_aset']['harta_tetap']) }}</span>
                    </div>
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-stone-600 dark:text-stone-300 font-medium flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-rose-500 block"></span> Harta Lain-lain</span>
                        <span class="font-mono text-[#043d2e] dark:text-emerald-400 font-bold truncate pl-4">{{ format_rupiah($stats['breakdown_aset']['harta_lain']) }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 4: Anggota Koperasi --}}
        <div class="bg-white dark:bg-stone-900 border border-transparent dark:border-stone-800/40 rounded-[24px] p-6 shadow-sm hover:shadow-md transition-all duration-300 flex items-center justify-between gap-4">
            <div class="min-w-0">
                <span class="text-stone-400 dark:text-stone-500 text-[11px] font-bold uppercase tracking-widest block">Anggota Koperasi</span>
                <p class="text-2xl sm:text-3xl lg:text-[23px] xl:text-[25px] 2xl:text-[28px] font-black tracking-tight text-[#043d2e] dark:text-emerald-400 leading-none mt-2 truncate transition-colors">{{ $stats['total_anggota'] }}</p>
                <p class="text-[10px] text-stone-500 dark:text-stone-400 mt-2 font-medium">Jumlah anggota terdaftar aktif</p>
            </div>
            <div class="flex -space-x-1 shrink-0">
                <div class="w-8 h-8 rounded-full bg-[#043d2e]/20 border-2 border-white dark:border-stone-900 flex items-center justify-center text-[9px] font-black text-[#043d2e]">PK</div>
                <div class="w-8 h-8 rounded-full bg-amber-100 border-2 border-white dark:border-stone-900 flex items-center justify-center text-[9px] font-black text-amber-800">PP</div>
                <div class="w-8 h-8 rounded-full bg-emerald-100 border-2 border-white dark:border-stone-900 flex items-center justify-center text-[9px] font-black text-emerald-800">RU</div>
            </div>
        </div>
    </div>

    {{-- ZONA 2: Grafik Analitik Keuangan (2 Kolom Seimbang) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        {{-- Chart A: Arus Kas Bulanan (Spans 2 columns) --}}
        <div class="bg-white dark:bg-stone-900 border border-transparent dark:border-stone-800/40 rounded-[28px] p-6 lg:p-8 shadow-sm lg:col-span-2 flex flex-col justify-between h-[390px] transition-colors">
            {{-- Header --}}
            <div class="flex items-start justify-between mb-4 shrink-0">
                <div>
                    <h3 class="text-stone-800 dark:text-stone-200 font-bold text-base flex items-center gap-2 transition-colors">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#043d2e] dark:bg-emerald-500"></span>
                        Arus Kas Bulanan ({{ date('Y') }})
                    </h3>
                    <p class="text-stone-400 text-xs mt-0.5">Pemantauan siklus uang Koperasi bulanan</p>
                </div>
                
                {{-- Legend Indicator --}}
                <div class="flex items-center gap-2 bg-stone-50 dark:bg-stone-850 px-3 py-1.5 rounded-xl border border-stone-100 dark:border-stone-800 shadow-inner shrink-0 text-[10px] font-extrabold text-stone-500 dark:text-stone-400 transition-colors">
                    <span class="w-2 h-2 rounded-full bg-[#043d2e] dark:bg-emerald-500"></span>
                    <span>Pemasukan</span>
                    <span class="w-2 h-2 rounded-full bg-[#f43f5e] ml-2"></span>
                    <span>Pengeluaran</span>
                </div>
            </div>
            
            {{-- Horizontal Metrics Panel --}}
            <div class="grid grid-cols-2 gap-4 mb-4 shrink-0 border-b border-stone-50 dark:border-stone-800/60 pb-3.5 w-full">
                {{-- Metrik Pemasukan --}}
                <div class="flex flex-col p-3 px-4 bg-[#043d2e]/5 dark:bg-emerald-500/5 border border-[#043d2e]/10 dark:border-emerald-500/10 rounded-2xl shadow-sm">
                    <div class="flex items-center justify-between gap-1.5 text-[10px] text-stone-500 dark:text-stone-400 font-bold uppercase tracking-wider">
                        <span>Pemasukan</span>
                        <span class="text-[#043d2e] dark:text-emerald-400 font-black bg-[#043d2e]/10 dark:bg-emerald-500/10 px-1.5 py-0.5 rounded transition-colors">+{{ number_format($stats['rasio_likuiditas'] ?? 0, 1) }}%</span>
                    </div>
                    <p class="text-[17px] font-black text-[#043d2e] dark:text-emerald-400 leading-tight mt-1.5 transition-colors">{{ format_rupiah(array_sum($monthlyFlow['pemasukan'])) }}</p>
                </div>
                
                {{-- Metrik Pengeluaran --}}
                <div class="flex flex-col p-3 px-4 bg-rose-500/5 border border-rose-500/10 dark:border-rose-500/10 rounded-2xl shadow-sm">
                    <div class="flex items-center justify-between gap-1.5 text-[10px] text-stone-500 dark:text-stone-400 font-bold uppercase tracking-wider">
                        <span>Pengeluaran</span>
                        <span class="text-rose-500 dark:text-rose-455 font-black bg-rose-50 dark:bg-rose-500/10 px-1.5 py-0.5 rounded transition-colors">-{{ number_format($stats['rasio_piutang'] ?? 0, 1) }}%</span>
                    </div>
                    <p class="text-[17px] font-black text-rose-600 dark:text-rose-400 leading-tight mt-1.5 transition-colors">{{ format_rupiah(array_sum($monthlyFlow['pengeluaran'])) }}</p>
                </div>
            </div>
            
            {{-- Chart Canvas --}}
            <div class="flex-1 relative w-full min-h-[180px]">
                <canvas id="cashflowChart"></canvas>
            </div>
        </div>

        {{-- Chart B: Komposisi Simpanan (Spans 1 column) --}}
        <div class="bg-white dark:bg-stone-900 border border-transparent dark:border-stone-800/40 rounded-[28px] p-6 shadow-sm flex flex-col justify-between transition-colors">
            <div class="mb-4">
                <h3 class="text-stone-800 dark:text-stone-200 font-bold text-base transition-colors">Komposisi Dana Simpanan</h3>
                <p class="text-stone-400 text-xs mt-0.5">Berdasarkan klasifikasi simpanan anggota</p>
            </div>
            
            <div class="flex-1 min-h-[220px] relative flex flex-col justify-center">
                <div class="relative w-full h-[180px]">
                    <canvas id="simpananChart"></canvas>
                </div>
                
                {{-- Select Filters --}}
                <div class="flex gap-2 mt-4">
                    <select id="filterBidang" class="flex-1 px-3 py-2 rounded-xl border border-stone-200 dark:border-stone-800 text-[11px] text-stone-600 dark:text-stone-300 focus:ring-2 focus:ring-[#043d2e]/20 outline-none truncate bg-stone-50 dark:bg-stone-850 font-bold cursor-pointer transition-colors">
                        <option value="">Semua Bidang</option>
                        @foreach($bidangs as $bidang)
                            <option value="{{ $bidang->id }}">{{ $bidang->nama_bidang }}</option>
                        @endforeach
                    </select>
                    <select id="filterGolongan" class="flex-1 px-3 py-2 rounded-xl border border-stone-200 dark:border-stone-800 text-[11px] text-stone-600 dark:text-stone-300 focus:ring-2 focus:ring-[#043d2e]/20 outline-none truncate bg-stone-50 dark:bg-stone-850 font-bold cursor-pointer transition-colors">
                        <option value="">Semua Golongan</option>
                        @foreach(\App\Enums\GolonganAsn::cases() as $gol)
                            <option value="{{ $gol->value }}">{{ $gol->label() }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- ZONA 3: Layard Digital Cards & Transfer Lists (3 Kolom Seimbang) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Column A: Dompet Kas Koperasi --}}
        <div class="bg-white dark:bg-stone-900 border border-transparent dark:border-stone-800/40 rounded-[28px] p-6 shadow-sm flex flex-col justify-between transition-colors">
            <div class="mb-4">
                <h3 class="text-stone-800 dark:text-stone-200 font-bold text-base transition-colors">Dompet Kas Koperasi</h3>
                <p class="text-stone-400 text-xs mt-0.5">Distribusi saldo likuid tersimpan</p>
            </div>
            
            {{-- Stacked Digital Card Graphics --}}
            <div x-data="{ activeCard: 'brk' }" class="relative h-[210px] w-full mt-4 flex items-center justify-center">
                {{-- Back Card (Kas Tunai Bendahara) --}}
                <div @click="activeCard = activeCard === 'brk' ? 'kas' : 'brk'" 
                     :class="activeCard === 'kas' ? 'z-20 scale-100 translate-y-3 -rotate-2 shadow-xl opacity-100' : 'z-10 scale-95 -translate-y-6 rotate-2 shadow-lg opacity-85'"
                     class="absolute w-full h-[160px] bg-[#676359] dark:bg-stone-800 text-white rounded-3xl p-5 border border-stone-750/10 dark:border-stone-700/20 transition-all duration-500 flex flex-col justify-between select-none cursor-pointer">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-[9px] text-stone-300 font-bold uppercase tracking-wider">Kas Tunai Bendahara</p>
                            <p class="text-base font-black mt-1">{{ format_rupiah($stats['saldo_kas_tunai']) }}</p>
                        </div>
                        <span class="text-base text-stone-300">💵</span>
                    </div>
                    <div class="flex justify-between items-end">
                        <p class="font-mono text-[9px] text-stone-300">•••• •••• •••• 0002</p>
                        <p class="text-[8px] text-stone-300 uppercase font-black tracking-widest">KAS TUNAI</p>
                    </div>
                </div>
                
                {{-- Front Card (Bank BRK Syariah) --}}
                <div @click="activeCard = activeCard === 'brk' ? 'kas' : 'brk'"
                     :class="activeCard === 'brk' ? 'z-20 scale-100 translate-y-3 -rotate-2 shadow-xl opacity-100' : 'z-10 scale-95 -translate-y-6 rotate-2 shadow-lg opacity-85'"
                     class="absolute w-full h-[160px] bg-[#043d2e] dark:bg-[#033024] text-white rounded-3xl p-5 border border-emerald-955/10 dark:border-emerald-800/20 transition-all duration-500 flex flex-col justify-between select-none cursor-pointer">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-[9px] text-emerald-200 font-bold uppercase tracking-wider">Bank BRK Syariah</p>
                            <p class="text-lg font-black mt-1">{{ format_rupiah($stats['saldo_bank_brk']) }}</p>
                        </div>
                        <span class="text-lg">🏛️</span>
                    </div>
                    <div class="flex justify-between items-end">
                        <p class="font-mono text-xs text-emerald-100">•••• •••• •••• 0001</p>
                        <p class="text-[8px] text-emerald-200/80 uppercase font-black tracking-widest">BRK SYARIAH</p>
                    </div>
                </div>
            </div>
            
            {{-- Quick action links removed as requested --}}
            <div class="mt-4"></div>
        </div>

        {{-- Column B: Log Aktivitas Terbaru --}}
        <div class="bg-white dark:bg-stone-900 border border-transparent dark:border-stone-800/40 rounded-[28px] p-6 shadow-sm flex flex-col justify-between transition-colors">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="text-stone-800 dark:text-stone-200 font-bold text-base transition-colors">Aktivitas Terbaru</h3>
                    <p class="text-stone-400 text-xs mt-0.5">Catatan audit log aktivitas admin terbaru</p>
                </div>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider bg-stone-100 dark:bg-stone-800 text-stone-600 dark:text-stone-400 transition-colors">
                    Log Aktif
                </span>
            </div>

            <div class="flex-1 flex flex-col justify-center min-h-[220px]">
                @if($recentActivities->isEmpty())
                <div class="text-center p-6 flex flex-col justify-center items-center">
                    <div class="w-12 h-12 rounded-2xl bg-stone-50 dark:bg-stone-850 flex items-center justify-center mb-3 shadow-inner text-xl">
                        📋
                    </div>
                    <p class="text-stone-500 dark:text-stone-400 text-xs font-bold">Log Bersih</p>
                    <p class="text-stone-400 dark:text-stone-500 text-[10px] mt-0.5">Belum ada aktivitas admin tercatat.</p>
                </div>
                @else
                <div class="space-y-3.5 max-h-[245px] overflow-y-auto pr-1">
                    @foreach($recentActivities->take(4) as $log)
                    <div class="flex items-start justify-between gap-3 p-3 rounded-2xl bg-stone-50 dark:bg-stone-850 hover:bg-stone-100/50 dark:hover:bg-stone-800/50 transition-colors">
                        <div class="flex items-start gap-3 min-w-0">
                            <div class="w-8 h-8 rounded-xl bg-[#043d2e]/10 dark:bg-emerald-500/10 text-[#043d2e] dark:text-emerald-400 font-black text-[10px] flex items-center justify-center shrink-0 shadow-sm mt-0.5">
                                {{ strtoupper(substr($log->user->nama ?? 'Sys', 0, 2)) }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-stone-800 dark:text-stone-200 leading-snug truncate transition-colors">{{ $log->user->nama ?? 'Sistem' }}</p>
                                <p class="text-[10px] text-stone-500 dark:text-stone-400 leading-snug mt-0.5 font-medium line-clamp-2">{{ $log->deskripsi }}</p>
                            </div>
                        </div>
                        <span class="text-[9px] font-bold text-stone-400 shrink-0 text-right whitespace-nowrap mt-0.5">
                            {{ $log->created_at->diffForHumans() }}
                        </span>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            <a href="/log-aktivitas" class="mt-4 w-full py-2.5 text-center text-xs font-bold text-stone-600 dark:text-stone-400 bg-stone-100 dark:bg-stone-850 hover:bg-stone-200 dark:hover:bg-stone-800 transition-colors rounded-2xl block">
                Seluruh Log Aktivitas
            </a>
        </div>

        {{-- Column C: Kesehatan Koperasi --}}
        <div class="bg-white dark:bg-stone-900 border border-transparent dark:border-stone-800/40 rounded-[28px] p-6 shadow-sm flex flex-col justify-between transition-colors">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="text-stone-800 dark:text-stone-200 font-bold text-base transition-colors">Kesehatan Koperasi</h3>
                    <p class="text-stone-400 text-xs mt-0.5">Rasio keuangan koperasi berjalan</p>
                </div>
            </div>

            <div class="flex-1 flex flex-col justify-between min-h-[220px]">
                {{-- Financial Health Metrics --}}
                <div class="grid grid-cols-2 gap-4 my-2">
                    {{-- Liquidity ratio circle --}}
                    <div class="flex flex-col items-center justify-center p-3 rounded-2xl bg-stone-50 dark:bg-stone-850 transition-colors">
                        <div class="relative w-16 h-16 flex items-center justify-center">
                            <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                                <path class="text-stone-200 dark:text-stone-750" stroke-width="3" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                <path class="text-[#043d2e] dark:text-emerald-400" stroke-dasharray="{{ min(100, $stats['rasio_likuiditas']) }}, 100" stroke-width="3.5" stroke-linecap="round" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            </svg>
                            <span class="absolute text-[11px] font-black text-stone-800 dark:text-white">{{ number_format($stats['rasio_likuiditas'], 0) }}%</span>
                        </div>
                        <span class="text-[9px] font-bold text-stone-400 dark:text-stone-500 uppercase tracking-wider mt-2.5 text-center">Rasio Likuiditas</span>
                    </div>
                    
                    {{-- Debt ratio circle --}}
                    <div class="flex flex-col items-center justify-center p-3 rounded-2xl bg-stone-50 dark:bg-stone-850 transition-colors">
                        <div class="relative w-16 h-16 flex items-center justify-center">
                            <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                                <path class="text-stone-200 dark:text-stone-750" stroke-width="3" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                <path class="text-amber-500" stroke-dasharray="{{ min(100, $stats['rasio_piutang']) }}, 100" stroke-width="3.5" stroke-linecap="round" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            </svg>
                            <span class="absolute text-[11px] font-black text-stone-800 dark:text-white">{{ number_format($stats['rasio_piutang'], 0) }}%</span>
                        </div>
                        <span class="text-[9px] font-bold text-stone-400 dark:text-stone-500 uppercase tracking-wider mt-2.5 text-center">Rasio Piutang</span>
                    </div>
                </div>

                {{-- Total Profit label --}}
                <div class="bg-[#043d2e]/5 dark:bg-emerald-500/5 p-4 rounded-2xl border border-[#043d2e]/10 dark:border-emerald-500/10 flex items-center justify-between mt-3">
                    <div>
                        <span class="text-[9px] font-bold text-[#043d2e]/80 dark:text-emerald-400/80 uppercase tracking-widest block">Total Pendapatan</span>
                        <span class="text-base font-black text-[#043d2e] dark:text-emerald-400 mt-1 block transition-colors">{{ format_rupiah($stats['total_pendapatan'] ?? 0) }}</span>
                    </div>
                    <span class="text-2xl">📈</span>
                </div>
            </div>

            <a href="/keuangan/neraca" class="mt-4 w-full py-2.5 text-center text-xs font-bold text-[#043d2e] dark:text-emerald-400 bg-[#043d2e]/10 dark:bg-emerald-500/10 hover:bg-[#043d2e]/20 dark:hover:bg-emerald-500/20 transition-all rounded-2xl block">
                Lihat Laporan Neraca
            </a>
        </div>
        
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Styling Global Chart.js
    Chart.defaults.font.family = "'Outfit', sans-serif";
    Chart.defaults.color = 'rgba(120, 113, 108, 0.7)'; // stone-500 equivalent transparent
    Chart.defaults.scale.grid.color = 'rgba(120, 113, 108, 0.08)'; // transparent stone grid
    
    // CHART A: Cashflow (Line Chart with smooth curves and modern gradients)
    const ctxCashflow = document.getElementById('cashflowChart');
    if (ctxCashflow) {
        const ctx = ctxCashflow.getContext('2d');
        
        let gradientPemasukan = 'rgba(4, 61, 46, 0.1)';
        let gradientPengeluaran = 'rgba(244, 63, 94, 0.1)';
        
        if (ctx) {
            gradientPemasukan = ctx.createLinearGradient(0, 0, 0, 220);
            gradientPemasukan.addColorStop(0, 'rgba(4, 61, 46, 0.25)');
            gradientPemasukan.addColorStop(1, 'rgba(4, 61, 46, 0.01)');

            gradientPengeluaran = ctx.createLinearGradient(0, 0, 0, 220);
            gradientPengeluaran.addColorStop(0, 'rgba(244, 63, 94, 0.25)');
            gradientPengeluaran.addColorStop(1, 'rgba(244, 63, 94, 0.01)');
        }

        new Chart(ctxCashflow, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'],
                datasets: [
                    {
                        label: 'Pemasukan',
                        data: @json($monthlyFlow['pemasukan']),
                        borderColor: '#043d2e', 
                        backgroundColor: gradientPemasukan,
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#043d2e',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 7,
                    },
                    {
                        label: 'Pengeluaran',
                        data: @json($monthlyFlow['pengeluaran']),
                        borderColor: '#f43f5e', // rose-500
                        backgroundColor: gradientPengeluaran,
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#f43f5e',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 7,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: 'rgba(28, 25, 23, 0.95)', // text-stone-900
                        titleFont: { size: 13, family: "'Outfit', sans-serif", weight: 'bold' },
                        bodyFont: { size: 13, family: "'Outfit', sans-serif" },
                        padding: 12,
                        cornerRadius: 12,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) { label += ': '; }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false }
                    },
                    y: {
                        beginAtZero: true,
                        border: { display: false },
                        ticks: {
                            callback: function(value) {
                                if (value === 0) return '0';
                                return value >= 1000000 ? (value / 1000000).toFixed(0) + ' Jt' : value;
                            }
                        }
                    }
                }
            }
        });
    }

    // CHART B: Komposisi Simpanan (Horizontal Bar Chart)
    const ctxSimpanan = document.getElementById('simpananChart');
    if (ctxSimpanan) {
        const simpananDataRaw = @json($stats['breakdown_simpanan']);
        // Format array output from DB
        let simpananLabels = simpananDataRaw.map(item => item.nama);
        let simpananValues = simpananDataRaw.map(item => item.total);

        // Jika kosong, berikan placeholder
        if (simpananLabels.length === 0) {
            simpananLabels = ['Belum Ada Data'];
            simpananValues = [0];
        }

        const simpananChartObj = new Chart(ctxSimpanan, {
            type: 'bar',
            data: {
                labels: simpananLabels,
                datasets: [{
                    label: 'Total Dana',
                    data: simpananValues,
                    backgroundColor: document.documentElement.classList.contains('dark') ? '#10b981' : '#043d2e', // Emerald in Dark Mode, Hunter Green in Light Mode
                    borderRadius: 8,
                    barThickness: 'flex',
                    maxBarThickness: 16
                }]
            },
            options: {
                indexAxis: 'y', // Convert to horizontal bar
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(28, 25, 23, 0.95)',
                        padding: 12,
                        cornerRadius: 12,
                        callbacks: {
                            label: function(context) {
                                return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(context.raw);
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: { color: 'rgba(120, 113, 108, 0.08)' },
                        border: { display: false },
                        ticks: {
                            callback: function(value) {
                                if (value === 0) return '0';
                                return value >= 1000000 ? (value / 1000000).toFixed(0) + ' Jt' : value;
                            }
                        }
                    },
                    y: {
                        grid: { display: false },
                        border: { display: false },
                        ticks: {
                            font: { weight: '600', size: 10 },
                            color: 'rgba(120, 113, 108, 0.85)' // stone-700 equivalent transparent
                        }
                    }
                }
            }
        });

        // Filter Logic (AJAX / Fetch)
        const filterBidang = document.getElementById('filterBidang');
        const filterGolongan = document.getElementById('filterGolongan');

        const fetchSimpananData = async () => {
            const params = new URLSearchParams();
            if (filterBidang.value) params.append('bidang_id', filterBidang.value);
            if (filterGolongan.value) params.append('golongan_asn', filterGolongan.value);

            try {
                const res = await fetch(`/dashboard/simpanan-data?${params.toString()}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const data = await res.json();
                
                let newLabels = data.map(item => item.nama);
                let newValues = data.map(item => item.total);

                if (newLabels.length === 0) {
                    newLabels = ['Data Kosong'];
                    newValues = [0];
                }

                simpananChartObj.data.labels = newLabels;
                simpananChartObj.data.datasets[0].data = newValues;
                simpananChartObj.update();
            } catch (error) {
                console.error('Gagal mengambil data simpanan tersaring:', error);
            }
        };

        filterBidang.addEventListener('change', fetchSimpananData);
        filterGolongan.addEventListener('change', fetchSimpananData);
    }
});
</script>
@endpush