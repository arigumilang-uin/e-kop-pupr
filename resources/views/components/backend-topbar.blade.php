<style>
    /* Hide topbar actions if a filter-bar exists on the page */
    body:has(.sticky.top-\[92px\]) #topbar-actions-desktop,
    body:has(.sticky.top-\[92px\]) #topbar-actions-mobile {
        display: none !important;
    }
</style>

<header x-data="{ theme: localStorage.getItem('theme') || 'light' }"
        @theme-changed.window="theme = $event.detail"
        :class="{
            'bg-[#f0efe9]/95 border-b border-stone-200/40': theme === 'light',
            'bg-white/95 border-b border-stone-200/50': theme === 'white',
            'bg-stone-950/95 border-b border-stone-900/65': theme === 'dark'
        }"
        class="backdrop-blur-xl sticky top-0 z-50 transition-all duration-300">
    <div class="px-6 md:px-8 h-[92px] flex items-center justify-between gap-4 relative">
        {{-- Mobile & Desktop Left Container --}}
        <div class="flex items-center gap-3 min-w-0 flex-1">
            {{-- Toggle Button (Khusus Mobile) --}}
            <button onclick="toggleSidebar()" class="lg:hidden p-2 -ml-2 rounded-xl bg-stone-50 dark:bg-stone-900 text-stone-500 dark:text-stone-400 border border-stone-200 dark:border-stone-800 hover:bg-stone-100 dark:hover:bg-stone-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition-all group">
                <svg class="w-5 h-5 text-stone-600 dark:text-stone-400 group-hover:text-blue-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            
            {{-- Title ONLY visible on Mobile here --}}
            <div class="min-w-0 pl-1 lg:hidden">
                <h2 class="text-[18px] font-bold text-stone-900 dark:text-white tracking-tight truncate leading-tight transition-colors">@yield('title', 'Dashboard')</h2>
            </div>
            
            {{-- Desktop Actions (Placed on the left of the header on desktop!) --}}
            <div class="hidden lg:flex items-center gap-2.5 transition-all duration-300"
                 :class="sidebarPinned ? 'lg:pl-[492px]' : 'lg:pl-[240px]'"
                 id="topbar-actions-desktop"
                 x-data="{ sidebarPinned: localStorage.getItem('sidebar-pinned') === 'true' }"
                 @sidebar-pin-changed.window="sidebarPinned = $event.detail">
                @yield('actions')
            </div>
        </div>

        {{-- Mobile & Desktop Right Container --}}
        <div class="shrink-0 flex items-center gap-3 md:gap-4 min-w-0">
            
            {{-- Mobile Actions Container (Symmetrically aligned on the right side on mobile!) --}}
            <div class="flex lg:hidden items-center gap-2">
                {{-- Segmented Theme Controller (Mobile) --}}
                <div x-data="{ 
                        theme: localStorage.getItem('theme') || 'light',
                        setTheme(newTheme) {
                            this.theme = newTheme;
                            $dispatch('theme-changed', newTheme);
                        }
                     }"
                     @theme-changed.window="theme = $event.detail"
                     class="h-[34px] flex items-center bg-stone-200/50 dark:bg-stone-900/60 px-0.5 rounded-lg border border-stone-200/60 dark:border-stone-800 shadow-inner">
                    
                    {{-- Cream Theme --}}
                    <button type="button" @click="setTheme('light')" :class="theme === 'light' ? 'bg-white dark:bg-stone-800 text-amber-600 dark:text-amber-400 shadow-sm border border-stone-200/40 dark:border-stone-700/50' : 'text-stone-500'" class="h-[28px] w-[28px] rounded-md transition-all duration-200 focus:outline-none flex items-center justify-center">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m0 13.5V21M4.95 4.95l1.59 1.59m10.92 10.92l1.59 1.59M3 12h2.25m13.5 0H21M5.75 18.25l1.59-1.59m10.92-10.92l1.59-1.59M12 7.5a4.5 4.5 0 100 9 4.5 4.5 0 000-9z" />
                        </svg>
                    </button>

                    {{-- Pure White Theme --}}
                    <button type="button" @click="setTheme('white')" :class="theme === 'white' ? 'bg-white dark:bg-stone-800 text-[#043d2e] dark:text-emerald-450 shadow-sm border border-stone-200/40 dark:border-stone-700/50' : 'text-stone-500'" class="h-[28px] w-[28px] rounded-md transition-all duration-200 focus:outline-none flex items-center justify-center">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 17.93c-3.95-.49-7-3.85-7-7.93s3.05-7.44 7-7.93v15.86z"/>
                        </svg>
                    </button>

                    {{-- Dark Theme --}}
                    <button type="button" @click="setTheme('dark')" :class="theme === 'dark' ? 'bg-white dark:bg-stone-800 text-yellow-500 dark:text-yellow-400 shadow-sm border border-stone-200/40 dark:border-stone-700/50' : 'text-stone-500'" class="h-[28px] w-[28px] rounded-md transition-all duration-200 focus:outline-none flex items-center justify-center">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
                        </svg>
                    </button>
                </div>

                {{-- Notification Dropdown (Mobile) --}}
                <div class="relative" x-data="{ open: false }" @click.away="open = false" @keydown.escape.window="open = false">
                    <button type="button" 
                            @click="open = !open"
                            class="relative h-[34px] w-[34px] flex items-center justify-center rounded-lg border border-stone-200/60 dark:border-stone-800 bg-white dark:bg-stone-900 text-stone-500 dark:text-stone-400 hover:text-stone-900 dark:hover:text-stone-200 hover:bg-stone-50 dark:hover:bg-stone-800 shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-[#043d2e]/20"
                            title="Notifikasi Pengajuan Pinjaman">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                        </svg>
                        @if($pendingPinjaman->count() > 0)
                        <span class="absolute -top-1 -right-1 flex h-4 w-4 items-center justify-center rounded-full bg-rose-500 text-[8px] font-black text-white ring-1 ring-white dark:ring-stone-950 animate-bounce">
                            {{ $pendingPinjaman->count() }}
                        </span>
                        @endif
                    </button>

                    {{-- Dropdown Card (Mobile) --}}
                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                         x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                         class="absolute right-0 mt-2 w-72 rounded-xl shadow-xl bg-white dark:bg-stone-900 border border-stone-250/60 dark:border-stone-850 ring-1 ring-black/5 z-50 origin-top-right overflow-hidden"
                         style="display: none;">
                         
                         <div class="px-4 py-3 border-b border-stone-100 dark:border-stone-850/80 bg-stone-50/50 dark:bg-stone-900/50 flex items-center justify-between">
                             <div class="flex items-center gap-1.5">
                                 <span class="text-xs font-bold text-stone-850 dark:text-stone-200">Notifikasi</span>
                                 @if($pendingPinjaman->count() > 0)
                                 <span class="px-1.5 py-0.5 rounded-full bg-rose-500/10 dark:bg-rose-500/20 text-rose-600 dark:text-rose-450 text-[9px] font-bold">
                                     {{ $pendingPinjaman->count() }} Baru
                                 </span>
                                 @endif
                             </div>
                             @if($pendingPinjaman->count() > 0)
                             <a href="{{ route('pinjaman.index') }}" class="text-[10px] font-bold text-[#043d2e] dark:text-emerald-450 hover:underline">
                                 Semua
                             </a>
                             @endif
                         </div>

                         <div class="max-h-[260px] overflow-y-auto divide-y divide-stone-100 dark:divide-stone-850/65">
                             @forelse($pendingPinjaman as $pjm)
                             <a href="{{ route('pinjaman.index') }}?q={{ $pjm->anggota->nama }}" class="block px-4 py-3 hover:bg-stone-50/50 dark:hover:bg-stone-850/30 transition-colors">
                                 <div class="flex gap-2.5">
                                     <div class="w-7 h-7 rounded-full bg-[#043d2e]/10 dark:bg-emerald-500/15 flex items-center justify-center shrink-0 text-[#043d2e] dark:text-emerald-400">
                                         <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                             <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                         </svg>
                                     </div>
                                     <div class="flex-grow min-w-0">
                                         <div class="flex justify-between items-start gap-1.5">
                                             <p class="text-[11px] font-bold text-stone-850 dark:text-stone-200 truncate leading-tight">{{ $pjm->anggota->nama }}</p>
                                             <span class="text-[8px] font-medium text-stone-400 dark:text-stone-500 shrink-0">{{ $pjm->tanggal_pengajuan->diffForHumans() }}</span>
                                         </div>
                                         <p class="text-[10px] text-stone-500 dark:text-stone-400 mt-0.5">Pinjaman <span class="font-bold text-stone-800 dark:text-stone-100">Rp {{ number_format($pjm->nominal_pinjaman, 0, ',', '.') }}</span></p>
                                     </div>
                                 </div>
                             </a>
                             @empty
                             <div class="px-4 py-6 text-center text-stone-400 dark:text-stone-500">
                                 <p class="text-[11px] font-medium">Tidak ada pengajuan baru</p>
                             </div>
                             @endforelse
                         </div>
                    </div>
                </div>
            </div>

            {{-- Desktop Title & Subtitle + Dark Mode (Placed on the far right on desktop, perfectly centered!) --}}
            <div class="hidden lg:flex items-center gap-3.5">
                <div class="flex flex-col items-end text-right min-w-0">
                    <h2 class="text-[22px] font-medium text-stone-900 dark:text-white tracking-tight truncate transition-colors">@yield('title', 'Dashboard')</h2>
                    @hasSection('subtitle')
                    <p class="text-[13px] font-normal text-stone-500 dark:text-stone-400 mt-0.5 truncate transition-colors">@yield('subtitle')</p>
                    @endif
                </div>

                {{-- Segmented Theme Controller (Desktop) --}}
                <div x-data="{ 
                        theme: localStorage.getItem('theme') || 'light',
                        setTheme(newTheme) {
                            this.theme = newTheme;
                            $dispatch('theme-changed', newTheme);
                        }
                     }"
                     @theme-changed.window="theme = $event.detail"
                     class="h-[42px] flex items-center bg-stone-200/50 dark:bg-stone-900/60 px-1 rounded-xl border border-stone-200/60 dark:border-stone-800 shadow-inner">
                    
                    {{-- Cream Theme --}}
                    <button type="button" 
                            @click="setTheme('light')"
                            :class="theme === 'light' ? 'bg-white dark:bg-stone-800 text-amber-600 dark:text-amber-400 shadow-sm border border-stone-200/40 dark:border-stone-700/50' : 'text-stone-500 hover:text-stone-850 dark:hover:text-stone-300'"
                            class="h-[34px] w-[34px] rounded-lg transition-all duration-200 focus:outline-none flex items-center justify-center relative group"
                            title="Tema Cream Warm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m0 13.5V21M4.95 4.95l1.59 1.59m10.92 10.92l1.59 1.59M3 12h2.25m13.5 0H21M5.75 18.25l1.59-1.59m10.92-10.92l1.59-1.59M12 7.5a4.5 4.5 0 100 9 4.5 4.5 0 000-9z" />
                        </svg>
                        <span class="absolute bottom-full mb-2 hidden group-hover:block bg-stone-900 text-white text-[9px] py-1 px-1.5 rounded-md whitespace-nowrap z-50">Cream Warm</span>
                    </button>

                    {{-- Pure White Theme --}}
                    <button type="button" 
                            @click="setTheme('white')"
                            :class="theme === 'white' ? 'bg-white dark:bg-stone-800 text-[#043d2e] dark:text-emerald-400 shadow-sm border border-stone-200/40 dark:border-stone-700/50' : 'text-stone-500 hover:text-stone-850 dark:hover:text-stone-300'"
                            class="h-[34px] w-[34px] rounded-lg transition-all duration-200 focus:outline-none flex items-center justify-center relative group"
                            title="Tema Putih Bersih">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 17.93c-3.95-.49-7-3.85-7-7.93s3.05-7.44 7-7.93v15.86z"/>
                        </svg>
                        <span class="absolute bottom-full mb-2 hidden group-hover:block bg-stone-900 text-white text-[9px] py-1 px-1.5 rounded-md whitespace-nowrap z-50">Putih Bersih</span>
                    </button>

                    {{-- Dark Theme --}}
                    <button type="button" 
                            @click="setTheme('dark')"
                            :class="theme === 'dark' ? 'bg-white dark:bg-stone-800 text-yellow-500 dark:text-yellow-400 shadow-sm border border-stone-200/40 dark:border-stone-700/50' : 'text-stone-500 hover:text-stone-850 dark:hover:text-stone-300'"
                            class="h-[34px] w-[34px] rounded-lg transition-all duration-200 focus:outline-none flex items-center justify-center relative group"
                            title="Tema Gelap Premium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
                        </svg>
                        <span class="absolute bottom-full mb-2 hidden group-hover:block bg-stone-900 text-white text-[9px] py-1 px-1.5 rounded-md whitespace-nowrap z-50">Tema Gelap</span>
                    </button>
                </div>

                {{-- Notification Dropdown (Desktop) --}}
                <div class="relative" x-data="{ open: false }" @click.away="open = false" @keydown.escape.window="open = false">
                    <button type="button" 
                            @click="open = !open"
                            class="relative h-[42px] w-[42px] flex items-center justify-center rounded-xl border border-stone-200/60 dark:border-stone-800 bg-white dark:bg-stone-900 text-stone-500 dark:text-stone-400 hover:text-stone-900 dark:hover:text-stone-200 hover:bg-stone-50 dark:hover:bg-stone-800 shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-[#043d2e]/20"
                            title="Notifikasi Pengajuan Pinjaman">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                        </svg>
                        @if($pendingPinjaman->count() > 0)
                        <span class="absolute -top-1.5 -right-1.5 flex h-5 w-5 items-center justify-center rounded-full bg-rose-500 text-[10px] font-black text-white ring-2 ring-white dark:ring-stone-950 animate-bounce">
                            {{ $pendingPinjaman->count() }}
                        </span>
                        @endif
                    </button>

                    {{-- Dropdown Card (Desktop) --}}
                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                         x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                         class="absolute right-0 mt-2.5 w-96 rounded-2xl shadow-xl bg-white dark:bg-stone-900 border border-stone-250/60 dark:border-stone-800 ring-1 ring-black/5 z-50 origin-top-right overflow-hidden"
                         style="display: none;">
                         
                         <div class="px-5 py-4 border-b border-stone-100 dark:border-stone-850/80 bg-stone-50/50 dark:bg-stone-900/50 flex items-center justify-between">
                             <div class="flex items-center gap-2">
                                 <span class="text-sm font-bold text-stone-800 dark:text-stone-200">Notifikasi</span>
                                 @if($pendingPinjaman->count() > 0)
                                 <span class="px-2 py-0.5 rounded-full bg-rose-500/10 dark:bg-rose-500/20 text-rose-600 dark:text-rose-450 text-[10px] font-bold">
                                     {{ $pendingPinjaman->count() }} Baru
                                 </span>
                                 @endif
                             </div>
                             @if($pendingPinjaman->count() > 0)
                             <a href="{{ route('pinjaman.index') }}" class="text-[11px] font-bold text-[#043d2e] dark:text-emerald-400 hover:underline">
                                 Lihat Semua
                             </a>
                             @endif
                         </div>

                         <div class="max-h-[320px] overflow-y-auto divide-y divide-stone-100 dark:divide-stone-850/65 custom-scrollbar">
                             @forelse($pendingPinjaman as $pjm)
                             <a href="{{ route('pinjaman.index') }}?q={{ $pjm->anggota->nama }}" class="block px-5 py-3.5 hover:bg-stone-50/50 dark:hover:bg-stone-850/30 transition-colors">
                                 <div class="flex gap-3">
                                     <div class="w-8 h-8 rounded-full bg-[#043d2e]/10 dark:bg-emerald-500/15 flex items-center justify-center shrink-0 text-[#043d2e] dark:text-emerald-400">
                                         <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                             <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                         </svg>
                                     </div>
                                     <div class="flex-grow min-w-0">
                                         <div class="flex justify-between items-start gap-2">
                                             <p class="text-xs font-bold text-stone-800 dark:text-stone-200 truncate leading-tight">{{ $pjm->anggota->nama }}</p>
                                             <span class="text-[9px] font-medium text-stone-400 dark:text-stone-500 shrink-0">{{ $pjm->tanggal_pengajuan->diffForHumans() }}</span>
                                         </div>
                                         <p class="text-[11px] text-stone-500 dark:text-stone-400 mt-1">Mengajukan pinjaman sebesar <span class="font-bold text-stone-850 dark:text-stone-100">Rp {{ number_format($pjm->nominal_pinjaman, 0, ',', '.') }}</span></p>
                                         <p class="text-[9px] text-[#043d2e] dark:text-emerald-400/90 font-bold mt-1 uppercase tracking-wider">Menunggu Persetujuan</p>
                                     </div>
                                 </div>
                             </a>
                             @empty
                             <div class="px-5 py-8 text-center text-stone-400 dark:text-stone-500">
                                 <svg class="w-8 h-8 mx-auto mb-2 text-stone-300 dark:text-stone-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                     <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                 </svg>
                                 <p class="text-xs font-medium">Tidak ada pengajuan pinjaman baru</p>
                              </div>
                              @endforelse
                         </div>
                    </div>
                </div>
            </div>

            {{-- Mobile Actions dropdown (ONLY visible on Mobile) --}}
            <div class="lg:hidden" id="topbar-actions-mobile" x-data>
                @hasSection('actions')
                    <div class="relative inline-block text-left" x-data="{ open: false }" @click.away="open = false" @keydown.escape.window="open = false">
                        <button type="button" @click="open = !open" class="p-2.5 bg-white dark:bg-stone-900 border border-stone-200 dark:border-stone-850 text-stone-600 dark:text-stone-400 hover:text-stone-900 dark:hover:text-stone-200 hover:bg-stone-50 dark:hover:bg-stone-800 rounded-xl transition-all shadow-sm focus:outline-none focus:ring-2 focus:ring-[#043d2e]/20">
                            <span class="sr-only">Buka menu aksi</span>
                            <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                            </svg>
                        </button>
                        
                        <div x-show="open" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                             x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                             class="absolute right-0 top-full mt-2 w-56 rounded-2xl shadow-xl bg-white dark:bg-stone-900 border border-stone-100 dark:border-stone-800 ring-1 ring-black/5 p-2 z-50 origin-top-right flex flex-col gap-1.5 [&>a]:w-full [&>a]:justify-start [&>form]:w-full [&>form>button]:w-full [&>form>button]:justify-start [&_button]:w-full [&_button]:justify-start"
                             style="display: none;">
                            @yield('actions')
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Garis Pemisah Presisi (Menyesuaikan Pinning Sidebar) --}}
        <div class="absolute bottom-0 right-6 md:right-8 h-[2px] bg-stone-300 dark:bg-stone-800/80 transition-all duration-300"
             :class="sidebarPinned ? 'left-6 md:left-8' : 'left-6 md:left-1'"
             x-data="{ sidebarPinned: localStorage.getItem('sidebar-pinned') === 'true' }"
             @sidebar-pin-changed.window="sidebarPinned = $event.detail"></div>
    </div>
</header>
