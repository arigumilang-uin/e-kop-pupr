<style>
    /* Hide topbar actions if a filter-bar exists on the page */
    body:has(.sticky.top-\[92px\]) #topbar-actions-desktop,
    body:has(.sticky.top-\[92px\]) #topbar-actions-mobile {
        display: none !important;
    }
</style>

<header class="bg-[#f0efe9]/95 backdrop-blur-xl sticky top-0 z-50">
    <div class="px-6 md:px-8 h-[92px] flex items-center justify-between gap-4 relative">
        {{-- Mobile & Desktop Left Container --}}
        <div class="flex items-center gap-3 min-w-0">
            {{-- Toggle Button (Khusus Mobile) --}}
            <button onclick="toggleSidebar()" class="lg:hidden p-2 -ml-2 rounded-xl bg-stone-50 text-stone-500 border border-stone-200 hover:bg-stone-100 focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition-all group">
                <svg class="w-5 h-5 text-stone-600 group-hover:text-blue-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            
            {{-- Title ONLY visible on Mobile here --}}
            <div class="min-w-0 pl-2 mt-2.5 lg:hidden">
                <h2 class="text-[22px] font-medium text-stone-900 tracking-tight truncate">@yield('title', 'Dashboard')</h2>
                @hasSection('subtitle')
                <p class="text-[13px] font-normal text-stone-500 mt-0.5 truncate">@yield('subtitle')</p>
                @endif
            </div>

            {{-- Desktop Actions (Placed on the left of the header on desktop!) --}}
            <div class="hidden lg:flex items-center gap-2.5 transition-all duration-300"
                 :class="sidebarPinned ? 'lg:pl-[492px]' : 'lg:pl-[208px]'"
                 id="topbar-actions-desktop"
                 x-data="{ sidebarPinned: localStorage.getItem('sidebar-pinned') === 'true' }"
                 @sidebar-pin-changed.window="sidebarPinned = $event.detail">
                @yield('actions')
            </div>
        </div>

        {{-- Mobile & Desktop Right Container --}}
        <div class="shrink-0 flex items-center gap-4 min-w-0">
            {{-- Desktop Title & Subtitle (Placed on the far right on desktop!) --}}
            <div class="hidden lg:flex flex-col items-end text-right min-w-0 mt-2.5">
                <h2 class="text-[22px] font-medium text-stone-900 tracking-tight truncate">@yield('title', 'Dashboard')</h2>
                @hasSection('subtitle')
                <p class="text-[13px] font-normal text-stone-500 mt-0.5 truncate">@yield('subtitle')</p>
                @endif
            </div>

            {{-- Mobile Actions dropdown (ONLY visible on Mobile) --}}
            <div class="lg:hidden" id="topbar-actions-mobile" x-data>
                @hasSection('actions')
                    <div class="relative inline-block text-left" x-data="{ open: false }" @click.away="open = false" @keydown.escape.window="open = false">
                        <button type="button" @click="open = !open" class="p-2.5 bg-white border border-stone-200 text-stone-600 hover:text-stone-900 hover:bg-stone-50 rounded-xl transition-all shadow-sm focus:outline-none focus:ring-2 focus:ring-[#043d2e]/20">
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
                             class="absolute right-0 top-full mt-2 w-56 rounded-2xl shadow-xl bg-white border border-stone-100 ring-1 ring-black/5 p-2 z-50 origin-top-right flex flex-col gap-1.5 [&>a]:w-full [&>a]:justify-start [&>form]:w-full [&>form>button]:w-full [&>form>button]:justify-start [&_button]:w-full [&_button]:justify-start"
                             style="display: none;">
                            @yield('actions')
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Garis Pemisah Presisi (Menyesuaikan Pinning Sidebar) --}}
        <div class="absolute bottom-0 right-6 md:right-8 h-[2px] bg-stone-300 transition-all duration-300"
             :class="sidebarPinned ? 'left-6 md:left-8' : 'left-6 md:left-1'"
             x-data="{ sidebarPinned: localStorage.getItem('sidebar-pinned') === 'true' }"
             @sidebar-pin-changed.window="sidebarPinned = $event.detail"></div>
    </div>
</header>
