<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Tirta Bina Karya PUPR Riau</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine JS & Plugins -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Flatpickr for consistent datepicker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/id.js"></script>
</head>

<body class="min-h-screen font-sans transition-all duration-350" 
      :class="{
          'bg-[#f0efe9] text-stone-800': theme === 'light',
          'bg-[#fafaf9] text-stone-850': theme === 'white',
          'bg-stone-950 text-stone-100': theme === 'dark'
      }"
      x-data="{ 
          sidebarPinned: localStorage.getItem('sidebar-pinned') === 'true',
          theme: localStorage.getItem('theme') || 'light'
      }"
      x-init="
          $watch('theme', val => {
              if (val === 'dark') {
                  document.documentElement.classList.add('dark');
                  document.documentElement.classList.remove('theme-white');
                  localStorage.setItem('theme', 'dark');
              } else if (val === 'white') {
                  document.documentElement.classList.remove('dark');
                  document.documentElement.classList.add('theme-white');
                  localStorage.setItem('theme', 'white');
              } else {
                  document.documentElement.classList.remove('dark');
                  document.documentElement.classList.remove('theme-white');
                  localStorage.setItem('theme', 'light');
              }
          });
          if (theme === 'dark') {
              document.documentElement.classList.add('dark');
          } else if (theme === 'white') {
              document.documentElement.classList.add('theme-white');
          }
      "
      @sidebar-pin-changed.window="sidebarPinned = $event.detail"
      @theme-changed.window="theme = $event.detail">
    <div class="flex min-h-screen">
        {{-- Mobile Overlay --}}
        <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-[70] hidden lg:hidden" onclick="toggleSidebar()">
        </div>

        {{-- Brand Header (Always Visible on Desktop) --}}
        <div class="hidden lg:flex items-center gap-4 fixed top-6 left-[38px] z-[90] h-[72px] min-w-0 select-none">
            <img src="{{ asset('assets/images/logo_riau.png') }}" alt="Logo Riau"
                class="w-[52px] h-[52px] object-contain drop-shadow-sm shrink-0">
            <div class="flex flex-col min-w-0 pb-0.5">
                <h1 class="text-[22px] font-medium tracking-tight text-stone-800 dark:text-stone-100 leading-tight truncate transition-colors">
                    Koperasi Tirta Bina Karya
                </h1>
                <p class="text-[13px] text-stone-500 dark:text-stone-400 font-normal mt-0.5 tracking-wide truncate transition-colors">
                    Dinas PUPRPKPP Provinsi Riau
                </p>
            </div>
        </div>

        {{-- Sidebar Component --}}
        <x-backend-sidebar />

        {{-- Main Content --}}
        <main id="main-content" 
              :class="sidebarPinned ? 'lg:ml-[392px]' : 'lg:ml-[120px]'"
              class="flex-1 min-w-0 transition-all duration-300">
            {{-- Top Bar Component --}}
            <x-backend-topbar />

            {{-- Flash Messages --}}
            <div class="px-4 sm:px-6 pt-4 space-y-3 z-50">
                @if(session('success'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-[-10px]"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-300"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 translate-y-[-10px]"
                        class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm flex items-start gap-3 shadow-sm relative">
                        <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="flex-1">{{ session('success') }}</span>
                        <button @click="show = false" class="text-emerald-500 hover:text-emerald-700 focus:outline-none">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                @endif

                @if(session('error'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-[-10px]"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-300"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 translate-y-[-10px]"
                        class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm flex items-start gap-3 shadow-sm relative">
                        <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="flex-1">{{ session('error') }}</span>
                        <button @click="show = false" class="text-red-500 hover:text-red-700 focus:outline-none">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                @endif

                @if(session('warning'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-[-10px]"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-300"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 translate-y-[-10px]"
                        class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-700 text-sm flex items-start gap-3 shadow-sm relative">
                        <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span class="flex-1">{{ session('warning') }}</span>
                        <button @click="show = false" class="text-amber-500 hover:text-amber-700 focus:outline-none">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                @endif
            </div>

            {{-- Page Content --}}
            <div class="p-4 sm:p-6">
                @yield('content')
            </div>
        </main>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            const main = document.getElementById('main-content');

            // Cek jika layar adalah ukuran laptop/desktop (lg ke atas)
            if (window.innerWidth >= 1024) {
                // Logika Desktop: Ubah margin main content dan geser sidebar
                sidebar.classList.toggle('lg:-translate-x-full');
                sidebar.classList.toggle('lg:translate-x-0');

                main.classList.toggle('lg:ml-[88px]');
                main.classList.toggle('lg:ml-0');
            } else {
                // Logika Mobile: Munculkan sidebar di atas konten dengan overlay
                sidebar.classList.toggle('-translate-x-full');
                overlay.classList.toggle('hidden');
            }
        }

        // Auto-tutup sidebar mobile jika ukuran layar berubah ke desktop 
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024) {
                document.getElementById('sidebar-overlay').classList.add('hidden');
                // Pastikan kita mengembalikan statusnya ke tampilan semestinya
            }
        });
    </script>

    @stack('scripts')
</body>

</html>