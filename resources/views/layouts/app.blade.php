<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Tirta Bina Karya PUPR Riau</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Alpine JS & Plugins -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Flatpickr for consistent datepicker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/id.js"></script>
</head>
<body class="min-h-screen bg-[#f7f7f5] font-sans">
    <div class="flex min-h-screen">
        {{-- Mobile Overlay --}}
        <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-[70] hidden lg:hidden" onclick="toggleSidebar()"></div>

        {{-- Sidebar Component --}}
        <x-backend-sidebar />

        {{-- Main Content --}}
        <main id="main-content" class="flex-1 lg:ml-72 min-w-0 transition-all duration-300">
            {{-- Top Bar Component --}}
            <x-backend-topbar />

            {{-- Flash Messages --}}
            <div class="px-4 sm:px-6 pt-4">
                @if(session('success'))
                <div class="mb-4 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm flex items-start gap-3">
                    <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
                @endif

                @if(session('error'))
                <div class="mb-4 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm flex items-start gap-3">
                    <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
                @endif

                @if(session('warning'))
                <div class="mb-4 p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-700 text-sm flex items-start gap-3">
                    <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <span>{{ session('warning') }}</span>
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
            // Tailwind class 'lg:translate-x-0' adalah default, kita override dengan toggle '-translate-x-full'
            sidebar.classList.toggle('lg:-translate-x-full');
            sidebar.classList.toggle('lg:translate-x-0');
            
            main.classList.toggle('lg:ml-72');
            main.classList.toggle('lg:ml-0');

            const toggleIcon = document.getElementById('sidebar-toggle-icon');
            if (toggleIcon) toggleIcon.classList.toggle('rotate-180');
            
            const toggleBtn = document.getElementById('desktop-toggle-btn');
            if (toggleBtn) {
                toggleBtn.classList.toggle('left-72');
                toggleBtn.classList.toggle('left-0');
                toggleBtn.classList.toggle('-ml-3.5');
                toggleBtn.classList.toggle('ml-4');
            }
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
