{{-- Sidebar Navigation Partial (reusable) --}}
@php
    $isActive = fn(string ...$routes) => collect($routes)->contains(fn($r) => request()->routeIs($r)) 
        ? 'bg-blue-600/20 text-blue-300 font-medium' 
        : 'text-slate-300 hover:bg-slate-800 hover:text-white';
@endphp

{{-- Dashboard --}}
<a href="{{ route('dashboard') }}"
   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-all duration-200 {{ $isActive('dashboard') }}">
    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
    </svg>
    Dashboard
</a>

{{-- Data Master --}}
<p class="px-3 pt-4 pb-1 text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Data Master</p>

<a href="{{ route('anggota.index') }}"
   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-all duration-200 {{ $isActive('anggota.*') }}">
    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
    </svg>
    Anggota
</a>

{{-- Keuangan --}}
<p class="px-3 pt-4 pb-1 text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Keuangan</p>

<a href="{{ route('simpanan.index') }}"
   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-all duration-200 {{ $isActive('simpanan.*') }}">
    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    Simpanan
</a>

<a href="{{ route('periode.index') }}"
   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-all duration-200 {{ $isActive('periode.*') }}">
    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
    </svg>
    Periode Pinjaman
</a>

<a href="{{ route('pinjaman.index') }}"
   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-all duration-200 {{ $isActive('pinjaman.*') }}">
    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
    </svg>
    Pinjaman / Approval
</a>

<a href="{{ route('potongan.index') }}"
   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-all duration-200 {{ $isActive('potongan.*') }}">
    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
    </svg>
    Potongan Bulanan
</a>

{{-- Persetujuan (Pimpinan Only) --}}
@if(auth()->user()->isPimpinan())
<p class="px-3 pt-4 pb-1 text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Persetujuan</p>

<a href="#" {{-- route('approval.index') — TODO --}}
   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-all duration-200 text-slate-300 hover:bg-slate-800 hover:text-white">
    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    Approval Pengaturan
</a>
@endif

{{-- Sistem --}}
<p class="px-3 pt-4 pb-1 text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Sistem</p>

<a href="{{ route('pengaturan.index') }}"
   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-all duration-200 {{ $isActive('pengaturan.*') }}">
    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
    </svg>
    Pengaturan
</a>

<a href="{{ route('log.index') }}"
   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-all duration-200 {{ $isActive('log.*') }}">
    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
    </svg>
    Log Aktivitas
</a>
