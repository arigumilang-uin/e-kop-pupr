<header x-data="{ mobileMenuOpen: false }" class="bg-white/80 backdrop-blur-xl sticky top-0 z-50 border-b border-slate-200/50">
    <div class="max-w-6xl mx-auto px-6 py-3 flex items-center justify-between">
        <!-- Brand -->
        <div class="flex items-center gap-3 md:gap-4">
            <picture class="shrink-0">
                <img src="{{ asset('assets/images/logo_riau.png') }}" alt="Logo Riau" class="w-10 h-10 md:w-12 md:h-12 object-contain">
            </picture>
            <div class="flex flex-col justify-center">
                <span class="text-[13px] md:text-[15px] font-bold text-slate-900 tracking-tight leading-tight">Dinas PUPR PKPP Provinsi Riau</span>
                <span class="text-[11px] md:text-[12px] font-medium text-slate-500 leading-tight">Koperasi Simpan Pinjam</span>
            </div>
        </div>

        <!-- Desktop Nav -->
        <nav class="hidden md:flex items-center gap-6 text-[13px] font-semibold text-slate-500">
            <a class="{{ request()->routeIs('pinjaman.guest.form') ? 'text-slate-900' : 'hover:text-slate-800' }} transition-colors" href="{{ route('pinjaman.guest.form_redirect') }}">Form Pengajuan</a>
            <a class="{{ request()->routeIs('pinjaman.guest.status') ? 'text-slate-900' : 'hover:text-slate-800' }} transition-colors" href="{{ route('pinjaman.guest.status') }}">Status Pengajuan</a>
            <a class="{{ request()->routeIs('simulasi') ? 'text-slate-900' : 'hover:text-slate-800' }} transition-colors" href="{{ route('simulasi') }}">Simulasi Publik</a>
        </nav>

        <!-- Mobile Menu Toggle Button -->
        <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden p-2.5 -mr-2 text-slate-600 hover:bg-slate-100 rounded-lg outline-none focus:ring-2 focus:ring-slate-200 transition-colors">
            <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
            <svg x-show="mobileMenuOpen" x-cloak class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <!-- Mobile Navigation Menu Dropdown -->
    <div x-show="mobileMenuOpen" x-cloak 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="md:hidden absolute top-full left-0 w-full bg-white border-b border-slate-200 shadow-xl pb-4 pt-2 px-4 shadow-slate-900/10">
        
        <div class="flex flex-col gap-1.5">
            <a href="{{ route('pinjaman.guest.form_redirect') }}" class="block px-4 py-3 rounded-xl text-[14px] font-semibold {{ request()->routeIs('pinjaman.guest.form') ? 'bg-slate-100 text-slate-900' : 'text-slate-500 hover:bg-slate-50' }}">
                Form Pengajuan
            </a>
            <a href="{{ route('pinjaman.guest.status') }}" class="block px-4 py-3 rounded-xl text-[14px] font-semibold {{ request()->routeIs('pinjaman.guest.status') ? 'bg-slate-100 text-slate-900' : 'text-slate-500 hover:bg-slate-50' }}">
                Status Pengajuan
            </a>
            <a href="{{ route('simulasi') }}" class="block px-4 py-3 rounded-xl text-[14px] font-semibold {{ request()->routeIs('simulasi') ? 'bg-slate-100 text-slate-900' : 'text-slate-500 hover:bg-slate-50' }}">
                Simulasi Publik
            </a>
        </div>
    </div>
</header>
