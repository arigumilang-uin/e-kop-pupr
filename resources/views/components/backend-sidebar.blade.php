<style>
    @media (min-width: 1024px) {
        /* Premium Layout Positioning & Dimensions */
        #sidebar {
            left: 0 !important;
            top: 136px !important;
            height: calc(100vh - 160px) !important;
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.02), 0 1px 2px rgba(0, 0, 0, 0.005) !important;
            border: 1px solid rgba(4, 61, 46, 0.06) !important;
            background: #ffffff !important;
            border-radius: 36px !important;
            transition: all 0.38s cubic-bezier(0.16, 1, 0.3, 1) !important;
        }

        html.theme-white #sidebar {
            background: #043d2e !important;
            border: 1px solid rgba(255, 255, 255, 0.05) !important;
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.2), 0 1px 2px rgba(0, 0, 0, 0.05) !important;
        }

        /* Text, icons and brand headers inside theme-white sidebar */
        html.theme-white #sidebar h1 {
            color: #ffffff !important;
        }
        html.theme-white #sidebar p {
            color: rgba(255, 255, 255, 0.5) !important;
        }
        html.theme-white #sidebar nav a,
        html.theme-white #sidebar nav button {
            color: rgba(255, 255, 255, 0.75) !important;
        }
        html.theme-white #sidebar nav a:hover,
        html.theme-white #sidebar nav button:hover {
            color: #ffffff !important;
            background-color: rgba(255, 255, 255, 0.08) !important;
        }
        
        /* Active item override inside theme-white sidebar (makes it high-contrast pop out card!) */
        html.theme-white #sidebar nav a[class*="bg-[#043d2e]"],
        html.theme-white #sidebar nav button[class*="bg-[#043d2e]"] {
            background: #ffffff !important;
            color: #043d2e !important;
            box-shadow: 0 8px 20px -6px rgba(0, 0, 0, 0.25) !important;
        }
        
        /* Active submenu link override inside theme-white sidebar */
        html.theme-white #sidebar nav a[class*="text-[#043d2e]"],
        html.theme-white #sidebar nav a[class*="bg-[#043d2e]/5"] {
            color: #34d399 !important; /* emerald-400 */
            background-color: rgba(52, 211, 153, 0.08) !important;
        }
        
        /* Submenu container background override */
        html.theme-white #sidebar nav div[x-show="open"] {
            background-color: rgba(0, 0, 0, 0.12) !important;
            border-radius: 12px;
            padding: 4px;
        }

        /* Submenu active item highlight */
        html.theme-white #sidebar nav div[x-show="open"] a {
            color: rgba(255, 255, 255, 0.8) !important;
        }
        html.theme-white #sidebar nav div[x-show="open"] a:hover {
            color: #ffffff !important;
            background-color: rgba(255, 255, 255, 0.05) !important;
        }
        html.theme-white #sidebar nav div[x-show="open"] a[class*="text-[#043d2e]"] {
            color: #34d399 !important; /* light emerald */
            background-color: rgba(255, 255, 255, 0.1) !important;
            font-weight: 700 !important;
        }

        /* User profile area and border */
        html.theme-white #sidebar > div {
            border-top: 1px solid rgba(255, 255, 255, 0.1) !important;
            background-color: rgba(0, 0, 0, 0.1) !important;
        }
        html.theme-white #sidebar > div p {
            color: #ffffff !important;
        }
        html.theme-white #sidebar > div p[class*="text-emerald-600"] {
            color: #34d399 !important;
        }
        html.theme-white #sidebar > div button {
            color: rgba(255, 255, 255, 0.6) !important;
        }
        html.theme-white #sidebar > div button:hover {
            color: #ff8b8b !important;
        }

        html.dark #sidebar {
            background: #171716 !important;
            border: 1px solid rgba(255, 255, 255, 0.05) !important;
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.3), 0 1px 2px rgba(0, 0, 0, 0.1) !important;
        }

        /* High-Performance GPU-Accelerated Easing (Apple/Stripe Standard) */
        #sidebar, 
        #sidebar nav a, 
        #sidebar nav button {
            transition: width 0.38s cubic-bezier(0.16, 1, 0.3, 1), 
                        padding 0.38s cubic-bezier(0.16, 1, 0.3, 1), 
                        margin 0.38s cubic-bezier(0.16, 1, 0.3, 1), 
                        border-radius 0.38s cubic-bezier(0.16, 1, 0.3, 1),
                        background-color 0.2s ease, 
                        color 0.2s ease,
                        opacity 0.25s ease !important;
            will-change: width, padding, margin, border-radius;
        }

        /* Standard Menu Item Defaults to avoid layout shifts */
        #sidebar nav a, 
        #sidebar nav button {
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: clip !important;
        }

        /* Dynamic Collapsed State styling (80px Width with High Fidelity) */
        .sidebar-collapsed-custom {
            width: 80px !important;
            border-radius: 28px !important;
        }
        
        .sidebar-collapsed-custom nav {
            padding-left: 0 !important;
            padding-right: 0 !important;
        }

        .sidebar-collapsed-custom nav a, 
        .sidebar-collapsed-custom nav button {
            justify-content: center !important;
            padding-left: 0 !important;
            padding-right: 0 !important;
            gap: 0 !important;
            width: 64px !important;
            height: 64px !important;
            margin-left: auto !important;
            margin-right: auto !important;
            border-radius: 9999px !important;
        }

        /* Globally set all primary menu icons in the sidebar to a larger 24px for gorgeous high fidelity and readability */
        #sidebar nav a svg:first-child,
        #sidebar nav button svg:first-child,
        #sidebar nav button div svg:first-child {
            width: 24px !important;
            height: 24px !important;
            flex-shrink: 0 !important;
            transition: all 0.38s cubic-bezier(0.16, 1, 0.3, 1) !important;
        }

        /* Enlarged icons specifically when sidebar is collapsed (tidak aktif) */
        .sidebar-collapsed-custom nav a svg:first-child,
        .sidebar-collapsed-custom nav button svg:first-child,
        .sidebar-collapsed-custom nav button div svg:first-child {
            width: 28px !important;
            height: 28px !important;
        }

        /* Resolve nesting inside submenu buttons so they center perfectly across all browsers (bypassing button shadow DOM bugs) */
        .sidebar-collapsed-custom nav button div {
            display: flex !important;
            justify-content: center !important;
            align-items: center !important;
            gap: 0 !important;
            width: 100% !important;
            height: 100% !important;
        }
        
        /* Smooth text fade-out matching width collapse */
        .sidebar-collapsed-custom nav a {
            font-size: 0 !important;
        }
        .sidebar-collapsed-custom nav button {
            font-size: 0 !important;
        }
        
        /* Hide ONLY the right-pointing direct child dropdown indicator arrow when collapsed */
        .sidebar-collapsed-custom nav button > svg {
            opacity: 0 !important;
            transform: scale(0) !important;
            width: 0 !important;
            margin: 0 !important;
        }

        /* Header titles collapse smoothly */
        .sidebar-collapsed-custom nav p {
            opacity: 0 !important;
            height: 0 !important;
            padding: 0 !important;
            margin: 0 !important;
            overflow: hidden !important;
            transform: translateY(-5px);
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1) !important;
        }
        
        /* Auto collapse opened submenus when sidebar collapses */
        .sidebar-collapsed-custom nav div[x-show="open"] {
            display: none !important;
        }

        /* Subtle scrollbar */
        #sidebar nav::-webkit-scrollbar {
            width: 5px !important;
        }
        #sidebar nav::-webkit-scrollbar-track {
            background: transparent !important;
        }
        #sidebar nav::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.06) !important;
            border-radius: 10px !important;
        }
        #sidebar nav::-webkit-scrollbar-thumb:hover {
            background: rgba(0, 0, 0, 0.12) !important;
        }
    }
</style>

<aside id="sidebar" 
       x-data="{ 
           isHovered: false, 
           isPinned: localStorage.getItem('sidebar-pinned') === 'true',
           hoverTimeout: null,
           init() {
               this.$watch('isPinned', value => {
                   localStorage.setItem('sidebar-pinned', value);
                   window.dispatchEvent(new CustomEvent('sidebar-pin-changed', { detail: value }));
               });
           },
           handleMouseEnter() {
               clearTimeout(this.hoverTimeout);
               this.hoverTimeout = setTimeout(() => {
                   this.isHovered = true;
               }, 120);
           },
           handleMouseLeave() {
               clearTimeout(this.hoverTimeout);
               this.hoverTimeout = setTimeout(() => {
                   this.isHovered = false;
               }, 80);
           }
       }"
       @mouseenter="handleMouseEnter()"
       @mouseleave="handleMouseLeave()"
       :class="isPinned || isHovered ? 'lg:w-[352px]' : 'sidebar-collapsed-custom'"
        class="w-72 bg-[#ffffff] dark:bg-stone-900 text-stone-800 dark:text-stone-200 flex flex-col fixed h-full z-[80]
               -translate-x-full lg:translate-x-0 transition-all duration-300 shadow-[4px_0_24px_rgba(0,0,0,0.03)] border-r border-stone-200/50 dark:border-stone-800/50
               lg:ml-6 lg:rounded-[24px] lg:shadow-[0_8px_30px_rgba(0,0,0,0.02)] lg:border lg:border-stone-200/40 dark:lg:border-stone-800/40 transition-colors">
     {{-- Logo (ONLY visible on Mobile inside sidebar drawer) --}}
     <div class="px-6 h-[100px] flex items-center gap-4 shrink-0 lg:hidden">
         <img src="{{ asset('assets/images/logo_riau.png') }}" alt="Logo Riau" class="w-12 h-12 object-contain drop-shadow-sm shrink-0">
         <div class="flex flex-col min-w-0 pb-1 mt-2.5">
             <h1 class="text-[21px] font-medium tracking-tight text-stone-800 dark:text-stone-100 leading-tight truncate">
                 Tirta Bina Karya
             </h1>
             <p class="text-[13px] text-stone-500 dark:text-stone-400 font-normal mt-0.5 truncate">
                 Dinas PUPRPKPP Prov. Riau
             </p>
         </div>
     </div>

    {{-- Navigation --}}
    <nav :class="isPinned || isHovered ? 'px-4 pt-1.5 pb-1.5' : 'lg:px-1 lg:pt-1.5 lg:pb-1.5'"
         class="flex-1 space-y-1 overflow-y-auto custom-scrollbar transition-all duration-300" 
         x-init="$el.scrollTop = sessionStorage.getItem('sidebarScroll') || 0; $el.addEventListener('scroll', () => sessionStorage.setItem('sidebarScroll', $el.scrollTop))">
        @include('layouts.partials.sidebar-nav')
    </nav>

    {{-- User Info --}}
    <div :class="isPinned || isHovered ? 'px-4 py-2.5' : 'lg:p-2.5 lg:flex lg:justify-center'"
         class="bg-black/5 dark:bg-white/5 border-t border-stone-300/30 dark:border-stone-700/30 shrink-0 transition-all duration-300">
        <div :class="isPinned || isHovered ? 'justify-between' : 'lg:justify-center lg:gap-0'"
             class="flex items-center gap-3 transition-all duration-300">
            <div :class="isPinned || isHovered ? 'w-10 h-10 rounded-xl' : 'lg:w-[52px] lg:h-[52px] lg:rounded-full lg:text-base'" 
                 class="bg-emerald-700 flex items-center justify-center text-sm font-bold text-white shrink-0 shadow-sm border border-emerald-800/20 transition-all duration-300">
                 {{ strtoupper(substr(auth()->user()->nama ?? 'U', 0, 1)) }}
            </div>
            <div x-show="isPinned || isHovered" x-transition.opacity class="flex-1 min-w-0">
                <p class="text-sm font-bold text-stone-800 dark:text-stone-200 truncate transition-colors">{{ auth()->user()->nama ?? 'Guest' }}</p>
                <p class="text-[11px] font-semibold text-emerald-600 dark:text-emerald-400 tracking-wider mt-0.5 transition-colors">{{ \App\Services\PermissionRegistry::roleLabel(auth()->user()->getRoleNames()->first() ?? 'unknown') }}</p>
            </div>
            <div x-show="isPinned || isHovered" x-transition.opacity class="flex items-center gap-1 shrink-0">
                {{-- Pin Toggle Button --}}
                <button type="button" @click="isPinned = !isPinned" 
                        :title="isPinned ? 'Lepas Sidebar (Mode Melayang)' : 'Sematkan Sidebar (Tetap Terbuka)'"
                        class="p-2 rounded-xl transition-all duration-200"
                        :class="isPinned ? 'text-emerald-700 dark:text-emerald-400 bg-emerald-500/10 hover:bg-emerald-500/20' : 'text-stone-400 dark:text-stone-500 hover:text-stone-600 dark:hover:text-stone-300 hover:bg-stone-200/50 dark:hover:bg-stone-800/50'">
                    <svg class="w-5 h-5 transition-transform duration-300" :class="isPinned ? 'rotate-[45deg] scale-110' : 'hover:scale-110'" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </button>

                {{-- Logout Button --}}
                <form action="{{ route('logout') }}" method="POST" class="shrink-0 m-0 p-0">
                    @csrf
                    <button type="submit" title="Logout" class="p-2 rounded-xl text-stone-400 dark:text-stone-500 hover:text-red-500 hover:bg-stone-200/50 dark:hover:bg-stone-800/50 transition-colors group">
                        <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                </form>
            </div>
    </div>
</aside>
