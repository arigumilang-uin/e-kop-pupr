{{-- Sidebar Navigation Partial (reusable) --}}
@php
    $isGroupActive = fn(array $routes) => collect($routes)->contains(fn($r) => request()->routeIs($r));
    
    // Custom check for query parameters (tab)
    $isTabActive = fn($tab) => request()->routeIs('parameter.index') && request('tab', 'neraca') === $tab;
    
    $isNeracaGroupActive = fn() => request()->routeIs('keuangan.neraca') || $isTabActive('neraca');
    $isShuGroupActive = fn() => request()->routeIs(['keuangan.laporan', 'keuangan.phu', 'keuangan.shu', 'shu.kewajiban.*']) || $isTabActive('phu');

    $singleActive = fn($route) => request()->routeIs($route) 
        ? 'bg-[#043d2e] dark:bg-emerald-800 text-white font-semibold shadow-md shadow-[#043d2e]/20 dark:shadow-emerald-800/10' 
        : 'text-stone-600 dark:text-stone-400 font-medium hover:bg-stone-100 dark:hover:bg-stone-800 hover:text-stone-900 dark:hover:text-white transition-colors';
        
    $groupHeaderClass = fn(array $routes, $customActive = false) => ($isGroupActive($routes) || $customActive)
        ? 'bg-[#043d2e] dark:bg-emerald-800 text-white font-semibold shadow-md shadow-[#043d2e]/20 dark:shadow-emerald-800/10'
        : 'text-stone-600 dark:text-stone-400 font-medium hover:bg-stone-100 dark:hover:bg-stone-800 hover:text-stone-900 dark:hover:text-white border border-transparent transition-colors';
        
    $subActive = fn($route, $customActive = false) => (request()->routeIs($route) || $customActive)
        ? 'text-[#043d2e] dark:text-emerald-400 font-semibold bg-[#043d2e]/5 dark:bg-emerald-500/5'
        : 'text-stone-500 dark:text-stone-400 font-normal hover:text-[#043d2e] dark:hover:text-emerald-300 hover:bg-stone-100/50 dark:hover:bg-stone-800/50 transition-colors';
@endphp

{{-- Dashboard --}}
<div class="mb-5">
    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[14.5px] transition-all duration-200 {{ $singleActive('dashboard') }}">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
        Dashboard
    </a>
</div>

{{-- Data Master / Anggota --}}
@can('anggota.view')
<p class="px-3 pt-2 pb-1.5 text-[10px] font-bold text-stone-400 uppercase tracking-widest">Master Data</p>
<div class="space-y-1 mb-5">
    <a href="{{ route('anggota.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[14.5px] transition-all duration-200 {{ $singleActive('anggota.*') }}">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        Anggota
    </a>
</div>
@endcan

{{-- Main Operasional --}}
@canany(['simpanan.view', 'potongan.view', 'pinjaman.view', 'pengeluaran.view', 'void.view', 'simulasi.aliran_dana'])
<p class="px-3 pt-2 pb-1.5 text-[10px] font-bold text-stone-400 uppercase tracking-widest">Operasional</p>
<div class="space-y-2 mb-5">
    
    {{-- Simpanan --}}
    @can('simpanan.view')
    <div>
        <a href="{{ route('simpanan.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[14.5px] transition-all duration-200 {{ $singleActive('simpanan.*') }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Simpanan
        </a>
    </div>
    @endcan

    {{-- Grup Manajemen Pinjaman --}}
    @canany(['pinjaman.view', 'pinjaman.aktif', 'periode.view'])
    <div x-data="{ open: {{ $isGroupActive(['periode.*', 'pinjaman.*']) ? 'true' : 'false' }} }">
        <button @click="open = !open" class="flex items-center justify-between w-full px-3 py-2.5 rounded-xl text-[14.5px] transition-all duration-200 focus:outline-none {{ $groupHeaderClass(['periode.*', 'pinjaman.*']) }}">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Pinjaman
            </div>
            <svg class="w-4 h-4 opacity-70 transition-transform duration-300" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
        </button>
        <div x-show="open" x-collapse class="pl-11 pr-2 mt-1 space-y-0.5">
            @can('periode.view')
            <a href="{{ route('periode.index') }}" class="block px-3 py-2.5 rounded-lg text-[14px] {{ $subActive('periode.*') }}">Periode Pinjaman</a>
            @endcan
            @can('pinjaman.view')
            <a href="{{ route('pinjaman.index') }}" class="block px-3 py-2.5 rounded-lg text-[14px] {{ request()->routeIs('pinjaman.index', 'pinjaman.show') ? 'text-[#043d2e] font-semibold bg-[#043d2e]/5' : 'text-stone-500 font-normal hover:text-[#043d2e] hover:bg-stone-100/50' }}">Pengajuan Pinjaman</a>
            @endcan
            @can('pinjaman.aktif')
            <a href="{{ route('pinjaman.aktif') }}" class="block px-3 py-2.5 rounded-lg text-[14px] {{ $subActive('pinjaman.aktif') }}">Pinjaman Aktif</a>
            @endcan
        </div>
    </div>
    @endcanany

    {{-- Grup Kas & Arus Dana --}}
    @canany(['pengeluaran.view', 'simulasi.aliran_dana'])
    <div x-data="{ open: {{ $isGroupActive(['pengeluaran.*', 'mutasi-rekening.*', 'keuangan.simulasi']) ? 'true' : 'false' }} }">
        <button @click="open = !open" class="flex items-center justify-between w-full px-3 py-2.5 rounded-xl text-[14.5px] transition-all duration-200 focus:outline-none {{ $groupHeaderClass(['pengeluaran.*', 'mutasi-rekening.*', 'keuangan.simulasi']) }}">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Kas & Arus Dana
            </div>
            <svg class="w-4 h-4 opacity-70 transition-transform duration-300" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
        </button>
        <div x-show="open" x-collapse class="pl-11 pr-2 mt-1 space-y-0.5">
            @can('pengeluaran.view')
            <a href="{{ route('pengeluaran.index') }}" class="block px-3 py-2.5 rounded-lg text-[14px] {{ $subActive('pengeluaran.*') }}">Biaya Operasional</a>
            <a href="{{ route('mutasi-rekening.index') }}" class="block px-3 py-2.5 rounded-lg text-[14px] {{ $subActive('mutasi-rekening.*') }}">Mutasi / Pindah Buku</a>
            @endcan
            @can('simulasi.aliran_dana')
            <a href="{{ route('keuangan.simulasi') }}" class="block px-3 py-2.5 rounded-lg text-[14px] {{ $subActive('keuangan.simulasi') }}">Proyeksi Aliran Dana</a>
            @endcan
        </div>
    </div>
    @endcanany

    {{-- Piutang Lain-Lain --}}
    @can('piutang_eksternal.view')
    <div>
        <a href="{{ route('piutang-eksternal.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[14.5px] transition-all duration-200 {{ $singleActive('piutang-eksternal.*') }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            Piutang Lain-Lain
        </a>
    </div>
    @endcan

    {{-- Potongan TPP --}}
    @can('potongan.view')
    <div>
        <a href="{{ route('potongan.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[14.5px] transition-all duration-200 {{ $singleActive('potongan.*') }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
            </svg>
            Potongan TPP
        </a>
    </div>
    @endcan

    {{-- Pembatalan Transaksi --}}
    @can('void.view')
    <div>
        <a href="{{ route('void.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[14.5px] transition-all duration-200 relative {{ $singleActive('void.*') }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Pembatalan Transaksi
            @if($pendingVoidCount > 0)
            <span class="absolute right-2 top-1/2 -translate-y-1/2 w-5 h-5 bg-red-500 text-white text-[10px] font-black rounded-full flex items-center justify-center shadow-sm">{{ $pendingVoidCount }}</span>
            @endif
        </a>
    </div>
    @endcan

</div>
@endcanany

{{-- Laporan & Akuntabilitas --}}
@canany(['laporan.ringkasan', 'laporan.neraca', 'laporan.arsip', 'simulasi.shu', 'shu.manage', 'parameter_neraca.manage'])
<p class="px-3 pt-2 pb-1.5 text-[10px] font-bold text-stone-400 uppercase tracking-widest">Tutup Buku & Laporan</p>
<div class="space-y-2 mb-5">
    
    {{-- GRUP NERACA --}}
    @canany(['laporan.neraca', 'parameter_neraca.manage'])
    <div x-data="{ open: {{ $isNeracaGroupActive() ? 'true' : 'false' }} }">
        <button @click="open = !open" class="flex items-center justify-between w-full px-3 py-2.5 rounded-xl text-[14.5px] transition-all duration-200 focus:outline-none {{ $groupHeaderClass([], $isNeracaGroupActive()) }}">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Laporan Neraca
            </div>
            <svg class="w-4 h-4 opacity-70 transition-transform duration-300" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
        </button>
        <div x-show="open" x-collapse class="pl-11 pr-2 mt-1 space-y-0.5">
            @can('laporan.neraca')
            <a href="{{ route('keuangan.neraca') }}" class="block px-3 py-2.5 rounded-lg text-[14px] {{ $subActive('keuangan.neraca') }}">Neraca Keuangan</a>
            @endcan
            @can('parameter_neraca.manage')
            <a href="{{ route('parameter.index', ['tab' => 'neraca']) }}" class="block px-3 py-2.5 rounded-lg text-[14px] {{ $subActive('', $isTabActive('neraca')) }}">Fondasi & Parameter</a>
            @endcan
        </div>
    </div>
    @endcanany

    {{-- GRUP SHU --}}
    @canany(['laporan.ringkasan', 'simulasi.shu', 'shu.manage', 'parameter_neraca.manage'])
    <div x-data="{ open: {{ $isShuGroupActive() ? 'true' : 'false' }} }">
        <button @click="open = !open" class="flex items-center justify-between w-full px-3 py-2.5 rounded-xl text-[14.5px] transition-all duration-200 focus:outline-none {{ $groupHeaderClass([], $isShuGroupActive()) }}">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/>
                </svg>
                Sisa Hasil Usaha (SHU)
            </div>
            <svg class="w-4 h-4 opacity-70 transition-transform duration-300" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
        </button>
        <div x-show="open" x-collapse class="pl-11 pr-2 mt-1 space-y-0.5">
            @can('laporan.ringkasan')
            <a href="{{ route('keuangan.laporan') }}" class="block px-3 py-2.5 rounded-lg text-[14px] {{ $subActive('keuangan.laporan') }}">Ringkasan Aset</a>
            @endcan
            @can('laporan.neraca')
            <a href="{{ route('keuangan.phu') }}" class="block px-3 py-2.5 rounded-lg text-[14px] {{ $subActive('keuangan.phu') }}">Laporan Laba/Rugi</a>
            @endcan
            @can('simulasi.shu')
            <a href="{{ route('keuangan.shu') }}" class="block px-3 py-2.5 rounded-lg text-[14px] {{ $subActive('keuangan.shu') }}">Simulasi Pembagian</a>
            @endcan
            @can('shu.manage')
            <a href="{{ route('shu.kewajiban.index') }}" class="block px-3 py-2.5 rounded-lg text-[14px] {{ $subActive('shu.kewajiban.*') }}">Kewajiban Dana SHU</a>
            @endcan
            @can('parameter_neraca.manage')
            <a href="{{ route('parameter.index', ['tab' => 'phu']) }}" class="block px-3 py-2.5 rounded-lg text-[14px] {{ $subActive('', $isTabActive('phu')) }}">Parameter Laba/Rugi</a>
            @endcan
        </div>
    </div>
    @endcanany

    {{-- Arsip Laporan --}}
    @can('laporan.arsip')
    <div>
        <a href="{{ route('arsip.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[14.5px] transition-all duration-200 {{ $singleActive('arsip.index') }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
            </svg>
            Arsip Dokumen Laporan
        </a>
    </div>
    @endcan

</div>
@endcanany

{{-- Sistem --}}
@canany(['pengaturan.view', 'user.view', 'role.view', 'log.view'])
<p class="px-3 pt-2 pb-1.5 text-[10px] font-bold text-stone-400 uppercase tracking-widest">Sistem Konfigurasi</p>
<div class="space-y-1 mb-6">
    
    @can('user.view')
    <a href="{{ route('users.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[14.5px] transition-all duration-200 {{ $singleActive('users.*') }}">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
        </svg>
        Pengurus
    </a>
    @endcan

    @can('role.view')
    <a href="{{ route('roles.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[14.5px] transition-all duration-200 {{ $singleActive('roles.*') }}">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
        </svg>
        Role & Akses
    </a>
    @endcan

    @can('log.view')
    <a href="{{ route('log.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[14.5px] transition-all duration-200 {{ $singleActive('log.*') }}">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        Log Aktivitas
    </a>
    @endcan

    @can('pengaturan.view')
    <a href="{{ route('pengaturan.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[14.5px] transition-all duration-200 {{ $singleActive('pengaturan.*') }}">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        Pengaturan Utama
    </a>
    @endcan

</div>
@endcanany
