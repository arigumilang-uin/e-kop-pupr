@props(['variant' => 'primary', 'type' => 'button'])
@php
    $base = "inline-flex items-center justify-center gap-2 h-12 px-6 rounded-xl font-bold text-[14px] transition-all active:scale-[0.98] outline-none disabled:opacity-50 disabled:cursor-not-allowed";
    $variants = [
        'primary' => 'bg-slate-900 hover:bg-slate-800 text-white shadow-md shadow-slate-900/10 focus:ring-4 focus:ring-slate-900/20',
        'blue'    => 'bg-blue-600 hover:bg-blue-700 text-white shadow-md shadow-blue-600/10 focus:ring-4 focus:ring-blue-600/20',
        'outline' => 'bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 shadow-sm focus:ring-4 focus:ring-slate-200/50',
    ];
@endphp
<button type="{{ $type }}" {{ $attributes->merge(['class' => $base . ' ' . $variants[$variant]]) }}>
    {{ $slot }}
</button>
