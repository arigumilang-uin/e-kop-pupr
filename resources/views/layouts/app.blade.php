<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — e-Koperasi PUPR Riau</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 font-[Inter]">
    <div class="flex min-h-screen">
        {{-- Mobile Overlay --}}
        <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-30 hidden lg:hidden" onclick="toggleSidebar()"></div>

        {{-- Sidebar --}}
        <aside id="sidebar" class="w-64 bg-slate-900 text-white flex flex-col fixed h-full z-40
                                   -translate-x-full lg:translate-x-0 transition-transform duration-300">
            {{-- Logo --}}
            <div class="p-5 border-b border-slate-700/50">
                <h1 class="text-lg font-bold bg-gradient-to-r from-blue-400 to-cyan-300 bg-clip-text text-transparent">
                    e-Koperasi
                </h1>
                <p class="text-[11px] text-slate-400 mt-0.5">PUPR Provinsi Riau</p>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 py-4 px-3 space-y-1 overflow-y-auto">
                @include('layouts.partials.sidebar-nav')
            </nav>

            {{-- User Info --}}
            <div class="p-4 border-t border-slate-700/50">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-blue-500 to-cyan-400 flex items-center justify-center text-sm font-bold text-white shrink-0">
                        {{ strtoupper(substr(auth()->user()->nama, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-white truncate">{{ auth()->user()->nama }}</p>
                        <p class="text-[11px] text-slate-400 capitalize">{{ auth()->user()->role->value }}</p>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" title="Logout" class="p-1.5 rounded-lg text-slate-400 hover:text-red-400 hover:bg-slate-800 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        {{-- Main Content --}}
        <main id="main-content" class="flex-1 lg:ml-64 min-w-0 transition-all duration-300">
            {{-- Top Bar --}}
            <header class="bg-white border-b border-slate-200 sticky top-0 z-10">
                <div class="px-4 sm:px-6 py-4 flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3 min-w-0">
                        {{-- Toggle Button (Selalu Tampil) --}}
                        <button onclick="toggleSidebar()" class="p-2 -ml-2 rounded-lg text-slate-500 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>
                        <div class="min-w-0">
                            <h2 class="text-lg font-semibold text-slate-800 truncate">@yield('title', 'Dashboard')</h2>
                            @hasSection('subtitle')
                            <p class="text-sm text-slate-500 mt-0.5 truncate">@yield('subtitle')</p>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center gap-3 shrink-0">
                        @yield('actions')
                    </div>
                </div>
            </header>

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
            
            main.classList.toggle('lg:ml-64');
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
</body>
</html>
