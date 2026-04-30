@props([
    'title',
    'value',
    'subtitle',
    'icon',
    'highlight' => false,
])

@php
    $baseClass = "rounded-2xl p-5 border shadow-sm transition-colors flex flex-col justify-between group relative overflow-hidden h-full ";
    $normalClass = "bg-white border-stone-200 hover:border-[#043d2e]/30";
    $highlightClass = "bg-[#043d2e] border-[#043d2e] shadow-md";

    $titleClass = $highlight ? "text-emerald-100 text-sm font-medium z-10 relative" : "text-stone-500 text-sm font-medium";
    
    // Semua ukuran font untuk text angka (value) diseragamkan
    $valueClass = $highlight ? "text-2xl lg:text-3xl font-extrabold tracking-tight text-white z-10 relative" : "text-2xl lg:text-3xl font-extrabold tracking-tight text-[#043d2e]";
    
    $subtitleClass = $highlight ? "text-emerald-200 text-xs mt-1 z-10 relative" : "text-stone-400 text-xs mt-1 font-medium";
    $iconWrapperClass = $highlight ? "w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center backdrop-blur-sm border border-white/10 shrink-0 z-10 relative" : "w-10 h-10 rounded-xl bg-stone-50 border border-stone-100 flex items-center justify-center shrink-0";
    $iconColorClass = $highlight ? "text-white" : "text-stone-400";
@endphp

<div {{ $attributes->merge(['class' => $baseClass . ($highlight ? $highlightClass : $normalClass)]) }}>
    @if($highlight)
    <div class="absolute -right-6 -top-6 w-24 h-24 bg-white/10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700"></div>
    @endif

    <div class="flex items-start justify-between mb-4 gap-3">
        <span class="{{ $titleClass }} line-clamp-2 leading-tight">{{ $title }}</span>
        <div class="{{ $iconWrapperClass }}">
            <svg class="w-5 h-5 {{ $iconColorClass }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                {!! $icon !!}
            </svg>
        </div>
    </div>
    
    <div class="mt-auto">
        <p class="{{ $valueClass }}">{{ $value }}</p>
        <p class="{{ $subtitleClass }}">{{ $subtitle }}</p>
    </div>
</div>
