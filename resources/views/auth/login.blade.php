@extends('layouts.guest')

@section('title', 'Login')

@section('content')
<div class="w-full max-w-md">
    {{-- Logo & Header --}}
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-500 to-cyan-400 mb-4 shadow-lg shadow-blue-500/25">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-white">e-Koperasi</h1>
        <p class="text-slate-400 text-sm mt-1">Koperasi Simpan Pinjam — Dinas PUPR Provinsi Riau</p>
    </div>

    {{-- Login Card --}}
    <div class="bg-white/5 backdrop-blur-xl rounded-2xl border border-white/10 p-8 shadow-2xl">
        <h2 class="text-lg font-semibold text-white mb-6">Masuk ke Sistem</h2>

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            {{-- Username --}}
            <div>
                <label for="username" class="block text-sm font-medium text-slate-300 mb-1.5">Username</label>
                <input
                    type="text"
                    id="username"
                    name="username"
                    value="{{ old('username') }}"
                    autofocus
                    required
                    placeholder="Masukkan username"
                    class="w-full px-4 py-3 rounded-xl bg-white/5 border border-white/10 text-white placeholder-slate-500
                           focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50
                           transition-all duration-200 text-sm"
                >
            </div>

            {{-- Password --}}
            <div>
                <label for="password" class="block text-sm font-medium text-slate-300 mb-1.5">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                    placeholder="Masukkan password"
                    class="w-full px-4 py-3 rounded-xl bg-white/5 border border-white/10 text-white placeholder-slate-500
                           focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50
                           transition-all duration-200 text-sm"
                >
            </div>

            {{-- Remember --}}
            <div class="flex items-center gap-2">
                <input type="checkbox" id="remember" name="remember"
                       class="w-4 h-4 rounded border-white/20 bg-white/5 text-blue-500 focus:ring-blue-500/50 focus:ring-offset-0">
                <label for="remember" class="text-sm text-slate-400">Ingat saya</label>
            </div>

            {{-- Error Messages --}}
            @if($errors->any())
            <div class="p-3.5 rounded-xl bg-red-500/10 border border-red-500/20">
                @foreach($errors->all() as $error)
                <p class="text-red-300 text-sm flex items-start gap-2">
                    <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ $error }}
                </p>
                @endforeach
            </div>
            @endif

            {{-- Submit --}}
            <button type="submit"
                    class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-blue-600 to-blue-500 text-white font-medium text-sm
                           hover:from-blue-500 hover:to-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-500/50
                           transition-all duration-200 shadow-lg shadow-blue-500/25 hover:shadow-blue-500/40
                           active:scale-[0.98]">
                Masuk
            </button>
        </form>
    </div>

    {{-- Footer --}}
    <p class="text-center text-slate-500 text-xs mt-6">
        &copy; {{ date('Y') }} Koperasi Simpan Pinjam PKPP — Dinas PUPR Provinsi Riau
    </p>
</div>
@endsection
