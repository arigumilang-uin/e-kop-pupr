<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Tirta Bina Karya PUPRPKPP Riau</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;1,400;1,600&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Alpine JS -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        [x-cloak] { display: none !important; }

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
<body class="bg-[#f8fafc] font-['Plus_Jakarta_Sans',sans-serif] m-0 p-0 text-slate-900 flex flex-col min-h-screen">

    <x-guest-nav />

    <div class="flex-1 flex relative overflow-hidden">
        
        <!-- Left Side: Branding Area -->
        <div class="hidden lg:flex" style="width: 55%; flex-direction: column; padding: 40px 60px;">
            
            <!-- Branding text -->
            <div style="flex: 1; display: flex; align-items: center; justify-content: flex-start; position: relative; padding-left: 120px;">
                <!-- Teks branding -->
                <div class="animate-enter delay-100" style="position: relative; z-index: 10; text-align: left;">
                    <p style="color: #475569; font-family: 'Playfair Display', serif; font-size: 36px; margin: 0 0 8px 0; font-weight: 600; font-style: italic; letter-spacing: 0.5px;">Selamat Datang di</p>
                    
                    <h1 style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 80px; font-weight: 800; margin: 0 0 20px 0; letter-spacing: -2px; line-height: 0.85; white-space: nowrap; color: #0f172a;">Tirta Bina Karya</h1>
                    <div style="width: 60px; height: 4px; background: #0f172a; margin-bottom: 24px; border-radius: 2px;"></div>
                    
                    <p style="color: #475569; font-size: 18px; margin: 0; line-height: 1.6; font-weight: 500; max-width: 500px;">
                        Koperasi Simpan Pinjam Konsumen di Dinas PUPRPKPP Provinsi Riau
                    </p>
                </div>
            </div>
            
        </div>
        
        <!-- Right Side: Login Form -->
        <div class="w-full lg:w-[45%] flex flex-col items-center justify-start lg:justify-center relative p-6 pt-[60px] lg:p-12 z-10 w-full">
            
            <div class="animate-enter delay-300 w-full max-w-[440px] relative">
                
                <!-- Ambient Glow behind the box -->
                <div class="absolute -inset-1 bg-gradient-to-r from-slate-400 to-slate-200 rounded-[32px] blur-xl opacity-30"></div>

                <!-- Mobile Branding Center -->
                <div class="flex lg:hidden flex-col items-center justify-center gap-2 mb-8 mt-4 relative z-10">
                    <h1 class="font-['Plus_Jakarta_Sans'] text-[32px] font-extrabold m-0 tracking-tight text-[#0f172a] whitespace-nowrap">Tirta Bina Karya</h1>
                </div>
                
                <!-- Dark Premium Login Card -->
                <div class="bg-[#0b1121] rounded-[28px] p-8 lg:p-10 text-white relative overflow-hidden border border-white/10 shadow-[0_20px_40px_-15px_rgba(0,0,0,0.5)]">
                    
                    <!-- Decorative subtle grid/lines -->
                    <div class="absolute inset-0 bg-[linear-gradient(to_right,#ffffff05_1px,transparent_1px),linear-gradient(to_bottom,#ffffff05_1px,transparent_1px)] bg-[size:24px_24px] pointer-events-none"></div>
                    <div class="absolute top-0 right-0 w-64 h-64 bg-blue-500/10 rounded-full blur-3xl -translate-y-1/2 translate-x-1/3 pointer-events-none"></div>

                    <div class="relative z-10 flex flex-col h-full">
                        
                        <!-- Header Box -->
                        <div class="text-center mb-10">
                            <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-white/5 border border-white/10 text-white mb-4">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </div>
                            <h2 class="text-xl font-bold tracking-tight text-white m-0 font-['Plus_Jakarta_Sans']">Akses Pengurus</h2>
                            <p class="text-slate-400 text-[13px] mt-2 font-medium">Silakan masuk menggunakan kredensial anda</p>
                        </div>

                        @if($errors->any())
                            <div x-data="{ show: true }" 
                                 x-show="show" 
                                 x-transition:leave="transition ease-in duration-200" 
                                 class="mb-6 bg-red-500/10 border border-red-500/20 rounded-2xl p-4 relative backdrop-blur-sm">
                                <div class="flex items-start gap-3">
                                    <div class="shrink-0 bg-red-500/20 p-2 rounded-xl">
                                        <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    </div>
                                    <div class="flex-1 min-w-0 pt-0.5">
                                        <ul class="m-0 p-0 list-none text-[13px] text-red-300 leading-relaxed font-medium">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
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

                            <!-- Form Group Username -->
                            <div class="mb-5 group">
                                <div class="relative flex items-center bg-[#1e293b]/50 border border-white/10 rounded-2xl focus-within:border-white/30 focus-within:bg-white/5 transition-all w-full">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-500 group-focus-within:text-white transition-colors duration-300">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    </div>
                                    <input type="text" id="username" name="username" value="{{ old('username') }}" required autofocus placeholder=" "
                                        class="w-full pt-[26px] pb-[10px] pl-[48px] pr-4 text-[14px] text-white bg-transparent outline-none peer"
                                        :readonly="isLoading"
                                        :class="{ 'opacity-50 cursor-not-allowed': isLoading }">
                                    <label for="username" 
                                        class="absolute left-[48px] text-slate-400 peer-placeholder-shown:top-[18px] peer-placeholder-shown:text-[14px] top-[8px] text-[10px] font-bold uppercase peer-focus:top-[8px] peer-focus:text-[10px] peer-focus:font-bold peer-focus:uppercase peer-focus:text-emerald-400 transition-all pointer-events-none">
                                        Username
                                    </label>
                                </div>
                            </div>

                            <!-- Form Group Password -->
                            <div class="mb-6 group">
                                <div class="relative flex items-center bg-[#1e293b]/50 border border-white/10 rounded-2xl focus-within:border-white/30 focus-within:bg-white/5 transition-all w-full">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-500 group-focus-within:text-white transition-colors duration-300">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                    </div>
                                    <input :type="showPassword ? 'text' : 'password'" id="password" name="password" required placeholder=" "
                                        class="w-full pt-[26px] pb-[10px] pl-[48px] pr-12 text-[14px] text-white bg-transparent outline-none peer"
                                        :readonly="isLoading"
                                        :class="{ 'opacity-50 cursor-not-allowed': isLoading }">
                                    <label for="password" 
                                        class="absolute left-[48px] text-slate-400 peer-placeholder-shown:top-[18px] peer-placeholder-shown:text-[14px] top-[8px] text-[10px] font-bold uppercase peer-focus:top-[8px] peer-focus:text-[10px] peer-focus:font-bold peer-focus:uppercase peer-focus:text-emerald-400 transition-all pointer-events-none">
                                        Password
                                    </label>
                                    <button type="button" @click="showPassword = !showPassword" tabindex="-1"
                                        class="absolute right-4 text-slate-500 hover:text-white focus:outline-none transition-colors" 
                                        :disabled="isLoading">
                                        <svg class="w-5 h-5" x-show="!showPassword" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <svg class="w-5 h-5" x-show="showPassword" x-cloak fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Remember Me -->
                            <div class="flex items-center justify-between mb-8" x-data="{ checked: false }">
                                <label class="flex items-center cursor-pointer select-none group">
                                    <div class="relative flex items-center justify-center w-5 h-5">
                                        <input id="remember" name="remember" type="checkbox" x-model="checked" class="absolute inset-0 w-full h-full opacity-0 z-10 cursor-pointer">
                                        <div class="w-full h-full rounded-[6px] border border-slate-600 transition-all duration-200 flex items-center justify-center group-hover:border-slate-500"
                                             :class="checked ? 'bg-white border-white group-hover:bg-white' : 'bg-transparent group-hover:bg-white/5'">
                                            <svg x-show="checked" class="w-3 h-3 text-[#0b1121]" viewBox="0 0 12 9" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M10.6666 1.5L4.24992 7.91667L1.33325 5" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <span class="ml-3 text-[13px] font-semibold text-slate-400 group-hover:text-white transition-colors">Ingat Sesi Saya</span>
                                </label>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" 
                                class="w-full h-[54px] rounded-2xl font-bold text-[14px] bg-white text-[#0b1121] border border-transparent shadow-[0_0_20px_rgba(255,255,255,0.15)] hover:shadow-[0_0_30px_rgba(255,255,255,0.3)] hover:scale-[1.02] active:scale-[0.98] transition-all duration-300 flex items-center justify-center gap-2 group"
                                :class="{ 'opacity-80 cursor-wait': isLoading }"
                                :disabled="isLoading">
                                
                                <span x-show="!isLoading" class="flex items-center gap-2 tracking-wide">
                                    MASUK KE DASHBOARD
                                    <svg class="w-5 h-5 transition-transform duration-300 group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 12h14M12 5l7 7-7 7"/></svg>
                                </span>
                                
                                <span x-show="isLoading" x-cloak class="flex items-center gap-2">
                                    <svg class="animate-spin w-5 h-5 text-[#0b1121]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span x-text="loadingText"></span>
                                </span>
                            </button>
                        </form>

                    </div>
                </div>
                
                <!-- Footer Mobile Text -->
                <x-guest-footer class="mt-8 lg:hidden" />
                
            </div>
        </div>
    </div>

</body>
</html>
