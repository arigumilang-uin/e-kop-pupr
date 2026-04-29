<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ASSET PUPR Riau</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;1,400;1,600&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Alpine JS -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        [x-cloak] { display: none !important; }

        .mesh-gradient-bg {
            background-color: #0f172a;
            background-image: 
                radial-gradient(at 0% 0%, rgba(30, 58, 138, 0.6) 0, transparent 50%), 
                radial-gradient(at 100% 0%, rgba(8, 145, 178, 0.4) 0, transparent 50%), 
                radial-gradient(at 100% 100%, rgba(15, 23, 42, 1) 0, transparent 50%),
                radial-gradient(at 0% 100%, rgba(30, 58, 138, 0.5) 0, transparent 50%);
        }

        .float-circle {
            animation: float 8s ease-in-out infinite;
        }

        .float-circle-delay {
            animation: float 10s ease-in-out infinite reverse;
        }

        @keyframes float {
            0% { transform: translate(-50%, 0px); }
            50% { transform: translate(-50%, -20px); }
            100% { transform: translate(-50%, 0px); }
        }

        .animate-enter {
            animation: fadeUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
            transform: translateY(20px);
        }

        .delay-100 { animation-delay: 100ms; }
        .delay-300 { animation-delay: 300ms; }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body style="margin: 0; padding: 0; font-family: 'Plus Jakarta Sans', sans-serif;">

    <!-- Full page mesh gradient background -->
    <div class="mesh-gradient-bg" style="min-height: 100vh; display: flex; position: relative; overflow: hidden;">
        
        <!-- Left Side: Branding Area -->
        <div class="hidden lg:flex" style="width: 55%; flex-direction: column; padding: 40px 60px;">
            
            <!-- Logo + Nama -->
            <div style="display: flex; align-items: center; gap: 16px;">
                <picture>
                    <img src="{{ asset('assets/images/logo_riau.png') }}" alt="Logo Riau" style="width: 56px; height: 56px; object-fit: contain;">
                </picture>
                <div>
                    <div style="color: white; font-size: 18px; font-weight: 600; letter-spacing: -0.3px;">Dinas PUPR PKPP Provinsi Riau</div>
                    <div style="color: rgba(255,255,255,0.6); font-size: 12px; font-weight: 400;">Koperasi Simpan Pinjam</div>
                </div>
            </div>
            
            <!-- Branding text -->
            <div style="flex: 1; display: flex; align-items: center; justify-content: center; position: relative; padding-left: 40px;">
                <!-- Lingkaran dekorasi biru/cyan -->
                <div class="float-circle" style="position: absolute; width: 550px; height: 550px; background: rgba(37, 99, 235, 0.4); border-radius: 50%; left: 55%; transform: translateX(-50%); filter: blur(40px);"></div>
                <div class="float-circle-delay" style="position: absolute; width: 420px; height: 420px; background: rgba(6, 182, 212, 0.3); border-radius: 50%; left: 55%; transform: translateX(-50%); filter: blur(30px);"></div>
                
                <!-- Teks branding -->
                <div class="animate-enter delay-100" style="position: relative; z-index: 10; text-align: left; margin-left: 280px;">
                    <p style="color: rgba(255,255,255,0.95); font-family: 'Playfair Display', serif; font-size: 36px; margin: 0 0 8px 0; font-weight: 400; font-style: italic; letter-spacing: 0.5px; text-shadow: 0 2px 10px rgba(0,0,0,0.1);">Selamat Datang di</p>
                    
                    <h1 style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 120px; font-weight: 800; margin: 0 0 24px 0; letter-spacing: -4px; line-height: 0.85; white-space: nowrap;
                        background: linear-gradient(180deg, #ffffff 30%, #93c5fd 100%); 
                        -webkit-background-clip: text; 
                        -webkit-text-fill-color: transparent; 
                        filter: drop-shadow(0 4px 25px rgba(0,0,0,0.2));">ASSET</h1>
                    
                    <p style="color: rgba(255,255,255,0.85); font-family: 'Plus Jakarta Sans', sans-serif; font-size: 18px; margin: 0; letter-spacing: 0.5px; line-height: 1.5; font-weight: 400; text-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                        Accurate Savings, Secure Execution & Transaction<br>Koperasi Simpan Pinjam PUPR PKPP Riau
                    </p>
                </div>
            </div>
            
        </div>
        
        <!-- Right Side: Login Form -->
        <div class="w-full flex flex-col items-center justify-start min-h-screen relative box-border p-5 pt-[115px] pb-0 lg:w-[45%] lg:justify-center lg:p-12 lg:min-h-screen">
            
            <!-- Mobile Header Top Right (Absolute) -->
            <div class="flex lg:hidden animate-enter" style="position: absolute; top: 24px; right: 24px; align-items: center; gap: 12px; z-index: 50; text-align: right;">
                <div>
                    <h1 style="color: white; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 13px; font-weight: 700; margin: 0; line-height: 1.2; text-shadow: 0 1px 2px rgba(0,0,0,0.2);">Dinas PUPR PKPP Riau</h1>
                    <p style="color: rgba(255,255,255,0.9); font-size: 11px; margin: 0; font-weight: 400; text-shadow: 0 1px 2px rgba(0,0,0,0.1);">Koperasi Simpan Pinjam</p>
                </div>
                <picture>
                    <img src="{{ asset('assets/images/logo_riau.png') }}" alt="Logo" style="width: 36px; height: 36px; object-fit: contain; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));">
                </picture>
            </div>
            
            <div class="animate-enter delay-300" style="width: 100%; max-width: 380px;">
                
                <!-- Mobile Branding Center -->
                <div class="flex lg:hidden animate-enter flex-row" style="align-items: center; justify-content: center; gap: 14px; margin-bottom: 24px; margin-top: 16px;">
                    <h1 style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 40px; font-weight: 800; margin: 0; letter-spacing: -2px; line-height: 1; color: white; text-shadow: 0 4px 10px rgba(0,0,0,0.15); white-space: nowrap;">ASSET</h1>
                    <div style="width: 1px; height: 32px; background: rgba(255,255,255,0.4);"></div>
                    <p style="color: rgba(255,255,255,0.95); font-size: 11px; margin: 0; line-height: 1.35; font-weight: 400; text-align: left; max-width: 140px; text-shadow: 0 1px 2px rgba(0,0,0,0.1);">
                        Accurate Savings,Secure Execution & Transaction
                    </p>
                </div>
                
                <!-- White Login Card -->
                <div style="background: rgba(255, 255, 255, 0.98); backdrop-filter: blur(20px); border-radius: 24px; box-shadow: 0 40px 80px -12px rgba(0, 0, 0, 0.4), 0 12px 24px -4px rgba(0, 0, 0, 0.15), inset 0 2px 2px rgba(255, 255, 255, 0.9); display: flex; flex-direction: column; justify-content: center; border: 1px solid rgba(255,255,255,0.8);" class="min-h-auto p-6 md:p-8">
                    
                    <div class="text-center mb-6">
                        <h2 class="text-2xl" style="color: #0f172a; font-weight: 800; margin: 0; letter-spacing: -0.5px; font-family: 'Plus Jakarta Sans', sans-serif;">Akses Pengurus</h2>
                        <p class="text-slate-500 text-sm mt-1">Silakan masuk dengan akun anda</p>
                    </div>

                    @if($errors->any())
                        <div x-data="{ show: true }" 
                             x-show="show" 
                             x-transition:leave="transition ease-in duration-200" 
                             class="mb-5" 
                             style="background: #fff1f2; border: 1px solid #ffe4e6; border-left: 4px solid #f43f5e; border-radius: 12px; padding: 12px; position: relative;">
                            
                            <div style="display: flex; align-items: start; gap: 12px;">
                                <div style="flex-shrink: 0; background: #ffe4e6; padding: 6px; border-radius: 8px;">
                                    <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                </div>
                                <div style="flex: 1; min-width: 0; padding-top: 2px;">
                                    <ul style="margin: 0; padding-left: 0; list-style: none; font-size: 13px; color: #9f1239; line-height: 1.5; font-weight: 600;">
                                        @foreach ($errors->all() as $error)
                                            <li style="margin-bottom: 2px;">{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                <button @click="show = false" type="button" 
                                        style="background: none; border: none; color: #f43f5e; opacity: 0.5; padding: 4px; cursor: pointer; border-radius: 6px;" 
                                        onmouseover="this.style.opacity='1'; this.style.backgroundColor='#ffe4e6'" 
                                        onmouseout="this.style.opacity='0.5'; this.style.backgroundColor='transparent'">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('login') }}" method="POST" 
                          x-data="{ 
                              showPassword: false, 
                              isLoading: false,
                              loadingText: 'Memeriksa...'
                          }"
                          @submit="isLoading = true">
                        @csrf

                        <div class="mb-4">
                            <label for="username" class="text-sm" style="display: block; font-weight: 600; color: #334155; margin-bottom: 8px;">Username</label>
                            <div class="relative">
                                <input type="text" id="username" name="username" value="{{ old('username') }}" required autofocus 
                                    class="w-full text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50"
                                    style="padding: 12px 14px 12px 46px; border: 1px solid #cbd5e1; background-color: #ffffff; border-radius: 12px; transition: all 0.2s;"
                                    placeholder="Masukkan username"
                                    :readonly="isLoading"
                                    :class="{ 'bg-slate-100 cursor-not-allowed': isLoading }">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                </div>
                            </div>
                        </div>

                        <div class="mb-5">
                            <label for="password" class="text-sm" style="display: block; font-weight: 600; color: #334155; margin-bottom: 8px;">Password</label>
                            <div class="relative">
                                <input :type="showPassword ? 'text' : 'password'" id="password" name="password" required 
                                    class="w-full text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50"
                                    style="padding: 12px 46px 12px 46px; border: 1px solid #cbd5e1; background-color: #ffffff; border-radius: 12px; transition: all 0.2s;"
                                    placeholder="Masukkan password"
                                    :readonly="isLoading"
                                    :class="{ 'bg-slate-100 cursor-not-allowed': isLoading }">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                </div>
                                <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 transition-colors" :disabled="isLoading">
                                    <svg class="w-5 h-5" x-show="!showPassword" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <svg class="w-5 h-5" x-show="showPassword" x-cloak fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                </button>
                            </div>
                        </div>

                        <div class="flex items-center justify-between mb-6" x-data="{ checked: false }">
                            <label class="flex items-center cursor-pointer select-none group">
                                <div class="relative flex items-center justify-center w-[18px] h-[18px]">
                                    <input id="remember" name="remember" type="checkbox" x-model="checked" class="absolute inset-0 w-full h-full opacity-0 z-10 cursor-pointer">
                                    <div class="w-full h-full rounded-md border-2 transition-all duration-200 flex items-center justify-center shadow-sm"
                                         :class="checked ? 'bg-blue-600 border-blue-600' : 'bg-white border-slate-300 group-hover:border-slate-400'">
                                        <svg x-show="checked" 
                                             class="w-2.5 h-2.5 text-white" 
                                             viewBox="0 0 12 9" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M10.6666 1.5L4.24992 7.91667L1.33325 5" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                </div>
                                <span class="ml-2 text-sm text-slate-500 font-medium group-hover:text-slate-700 transition-colors">Ingat saya</span>
                            </label>
                            
                            {{-- Optional forgot password link if you want --}}
                            {{-- <a href="#" class="text-sm text-blue-600 font-medium hover:text-blue-700 hover:underline transition-colors">Lupa Password?</a> --}}
                        </div>

                        <button type="submit" 
                            class="text-[14px] p-3.5 w-full flex items-center justify-center gap-2 rounded-xl text-white font-bold transition-all"
                            style="background: linear-gradient(135deg, #2563eb, #3b82f6); box-shadow: 0 8px 16px -4px rgba(37, 99, 235, 0.4);"
                            :class="{ 'opacity-80 cursor-wait': isLoading }"
                            :disabled="isLoading"
                            @mouseover="if(!isLoading) { $el.style.transform='translateY(-1px)'; $el.style.boxShadow='0 12px 20px -4px rgba(37, 99, 235, 0.5)'; }"
                            @mouseout="$el.style.transform='translateY(0)'; $el.style.boxShadow='0 8px 16px -4px rgba(37, 99, 235, 0.4)';">
                            
                            <span x-show="!isLoading" class="flex items-center gap-2">
                                MASUK SEKARANG
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                            </span>
                            
                            <span x-show="isLoading" x-cloak class="flex items-center gap-2">
                                <svg class="animate-spin w-5 h-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span x-text="loadingText"></span>
                            </span>
                        </button>
                    </form>

                    <!-- Divider -->
                    <div class="flex items-center gap-3 my-6">
                        <div class="flex-1 h-px bg-slate-200"></div>
                        <span class="text-slate-400 text-[10px] font-medium uppercase tracking-wider">Akses Publik</span>
                        <div class="flex-1 h-px bg-slate-200"></div>
                    </div>

                    <!-- Action buttons for public -->
                    <div class="grid grid-cols-2 gap-3">
                        <a href="{{ route('simulasi') }}" 
                           class="flex flex-col items-center justify-center p-3 rounded-xl border border-slate-200 hover:border-blue-300 hover:bg-blue-50 transition-colors group">
                            <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 mb-2 group-hover:scale-110 transition-transform">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            </div>
                            <span class="text-[11px] font-semibold text-slate-600 group-hover:text-blue-700 text-center leading-tight">Simulasi<br>Pinjaman</span>
                        </a>
                        <a href="{{ route('pinjaman.guest.status') }}" 
                           class="flex flex-col items-center justify-center p-3 rounded-xl border border-slate-200 hover:border-cyan-300 hover:bg-cyan-50 transition-colors group">
                            <div class="w-8 h-8 rounded-full bg-cyan-100 flex items-center justify-center text-cyan-600 mb-2 group-hover:scale-110 transition-transform">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <span class="text-[11px] font-semibold text-slate-600 group-hover:text-cyan-700 text-center leading-tight">Cek Status<br>Pengajuan</span>
                        </a>
                    </div>

                </div>
                
                {{-- Footer Text Mobile --}}
                <p class="text-center text-[11px] text-slate-400 mt-6 lg:hidden">
                    &copy; {{ date('Y') }} Koperasi Simpan Pinjam<br>Dinas PUPR PKPP Provinsi Riau
                </p>
                
            </div>
        </div>
    </div>

</body>
</html>
