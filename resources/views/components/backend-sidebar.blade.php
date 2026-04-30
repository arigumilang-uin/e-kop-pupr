<aside id="sidebar" class="w-72 bg-[#fafafa] text-stone-800 flex flex-col fixed h-full z-[80]
           -translate-x-full lg:translate-x-0 transition-transform duration-300 shadow-[4px_0_24px_rgba(0,0,0,0.03)] border-r border-stone-200/50">
    {{-- Logo --}}
    <div class="px-6 h-[92px] flex items-center gap-3 shrink-0">
        <img src="{{ asset('assets/images/logo_riau.png') }}" alt="Logo Riau" class="w-10 h-10 object-contain drop-shadow-sm">
        <div class="flex flex-col min-w-0 pb-1 mt-2.5">
            <h1 class="text-[19px] font-medium tracking-tight text-stone-800 leading-tight truncate">
                Tirta Bina Karya
            </h1>
            <p class="text-[11px] text-stone-500 font-semibold mt-0.5 truncate">
                Dinas PUPRPKPP Prov. Riau
            </p>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 pt-8 pb-5 px-4 space-y-1 overflow-y-auto custom-scrollbar" 
         x-data x-init="$el.scrollTop = sessionStorage.getItem('sidebarScroll') || 0; $el.addEventListener('scroll', () => sessionStorage.setItem('sidebarScroll', $el.scrollTop))">
        @include('layouts.partials.sidebar-nav')
    </nav>

    {{-- User Info --}}
    <div class="p-5 bg-stone-50/50 shrink-0">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-700 flex items-center justify-center text-sm font-bold text-white shrink-0 shadow-sm border border-emerald-800/20">
                {{ strtoupper(substr(auth()->user()->nama ?? 'U', 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-stone-800 truncate">{{ auth()->user()->nama ?? 'Guest' }}</p>
                <p class="text-[11px] font-semibold text-emerald-600 tracking-wider mt-0.5">{{ auth()->user()?->role?->value ?? 'Admin' }}</p>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" title="Logout" class="p-2 rounded-xl text-stone-400 hover:text-red-500 hover:bg-stone-200/50 transition-colors group">
                    <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>
</aside>

{{-- Floating Desktop Toggle (visually attached to sidebar edge) --}}
<button id="desktop-toggle-btn" onclick="toggleSidebar()" class="hidden lg:flex fixed left-72 -ml-3.5 top-[32px] w-7 h-7 bg-white border border-stone-200 rounded-full items-center justify-center text-stone-500 hover:text-stone-800 shadow-sm z-[90] transition-all duration-300">
    <svg id="sidebar-toggle-icon" class="w-4 h-4 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
    </svg>
</button>
