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
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25A2.25 2.25 0 0113.5 8.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
        </svg>
        Dashboard
    </a>
</div>

{{-- Data Master / Anggota --}}
@can('anggota.view')
<p class="px-3 pt-2 pb-1.5 text-[10px] font-bold text-stone-400 uppercase tracking-widest">Master Data</p>
<div class="space-y-1 mb-5">
    <a href="{{ route('anggota.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[14.5px] transition-all duration-200 {{ $singleActive('anggota.*') }}">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5zm6-10.125a1.875 1.875 0 11-3.75 0 1.875 1.875 0 013.75 0zm-3.75 5.625c0-1.036.84-1.875 1.875-1.875h0c1.036 0 1.875.84 1.875 1.875v1.125H6.75v-1.125z" />
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
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
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
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
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
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" />
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
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25M16.5 7.5V18a2.25 2.25 0 002.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 002.25 2.25h13.5M6 7.5h3v3H6v-3z" />
            </svg>
            Piutang Lain-Lain
        </a>
    </div>
    @endcan

    {{-- Potongan TPP --}}
    @can('potongan.view')
    <div>
        <a href="{{ route('potongan.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[14.5px] transition-all duration-200 {{ $singleActive('potongan.*') }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Potongan TPP
        </a>
    </div>
    @endcan

    {{-- Pembatalan Transaksi --}}
    @can('void.view')
    <div>
        <a href="{{ route('void.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[14.5px] transition-all duration-200 relative {{ $singleActive('void.*') }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
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
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
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
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 107.5 7.5h-7.5V6z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0013.5 3v7.5z" />
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
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.625-1.219l-1.636-2.863a1.5 1.5 0 00-1.309-.75H8.25m13.5 3v6a2.25 2.25 0 01-2.25 2.25H4.5A2.25 2.25 0 012.25 18v-6M16.5 12H12m0 0v3.75m0-3.75l-1.5 1.5m1.5-1.5l1.5 1.5" />
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
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
        </svg>
        Pengurus
    </a>
    @endcan

    @can('role.view')
    <a href="{{ route('roles.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[14.5px] transition-all duration-200 {{ $singleActive('roles.*') }}">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.57-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
        </svg>
        Role & Akses
    </a>
    @endcan

    @can('log.view')
    <a href="{{ route('log.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[14.5px] transition-all duration-200 {{ $singleActive('log.*') }}">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        Log Aktivitas
    </a>
    @endcan

    @can('pengaturan.view')
    <a href="{{ route('pengaturan.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[14.5px] transition-all duration-200 {{ $singleActive('pengaturan.*') }}">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.43l-1.003.828c-.293.241-.438.613-.43.992a7.723 7.723 0 010 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.954.26 1.43l-1.297 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.43l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.991l-1.004-.827a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.28z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
        </svg>
        Pengaturan Utama
    </a>
    @endcan

</div>
@endcanany
