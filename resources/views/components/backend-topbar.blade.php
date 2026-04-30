<header class="bg-[#f7f7f5]/95 backdrop-blur-xl sticky top-0 z-50">
    <div class="px-6 md:px-8 h-[92px] flex items-center justify-between gap-4 relative">
        <div class="flex items-center gap-3 min-w-0">
            {{-- Toggle Button (Khusus Mobile) --}}
            <button onclick="toggleSidebar()" class="lg:hidden p-2 -ml-2 rounded-xl bg-stone-50 text-stone-500 border border-stone-200 hover:bg-stone-100 focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition-all group">
                <svg class="w-5 h-5 text-stone-600 group-hover:text-blue-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <div class="min-w-0 pl-2 mt-2.5">
                <h2 class="text-[19px] font-medium text-stone-900 tracking-tight truncate">@yield('title', 'Dashboard')</h2>
                @hasSection('subtitle')
                <p class="text-[13.5px] font-normal text-stone-500 mt-0.5 truncate">@yield('subtitle')</p>
                @endif
            </div>
        </div>
        <div class="flex items-center gap-3 shrink-0">
            @yield('actions')
        </div>

        {{-- Garis Pemisah Presisi Tebal (Tepotong di Ujung) --}}
        <div class="absolute bottom-0 left-6 right-6 md:left-8 md:right-8 h-[2px] bg-stone-300"></div>
    </div>
</header>
