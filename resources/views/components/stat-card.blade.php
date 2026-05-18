@props([
    'title',
    'value',
    'subtitle' => '',
    'icon',
    'highlight' => false,
])

@php
    $baseClass = "rounded-2xl border shadow-sm transition-colors group relative h-full " . (isset($popover) ? 'cursor-pointer select-none ' : '');
    $normalClass = "bg-white border-stone-200 hover:border-[#043d2e]/30";
    $highlightClass = "bg-[#043d2e] border-[#043d2e] shadow-md";

    $titleClass = $highlight ? "text-emerald-100 text-sm font-medium z-10 relative" : "text-stone-500 text-sm font-medium";
    
    // Semua ukuran font untuk text angka (value) diseragamkan
    $valueClass = $highlight ? "text-2xl lg:text-3xl font-extrabold tracking-tight text-white z-10 relative" : "text-2xl lg:text-3xl font-extrabold tracking-tight text-[#043d2e]";
    
    $subtitleClass = $highlight ? "text-emerald-200 text-xs mt-1 z-10 relative" : "text-stone-400 text-xs mt-1 font-medium";
    $iconWrapperClass = $highlight ? "w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center backdrop-blur-sm border border-white/10 shrink-0 z-10 relative" : "w-10 h-10 rounded-xl bg-stone-50 border border-stone-100 flex items-center justify-center shrink-0";
    $iconColorClass = $highlight ? "text-white " . (isset($popover) ? "group-hover:scale-110 transition-transform" : "") : "text-stone-400 " . (isset($popover) ? "group-hover:text-[#043d2e] transition-colors" : "");
@endphp

<div @if(isset($popover)) x-data="{ openPopover: false }" @click.away="openPopover = false" @click="openPopover = !openPopover" @endif {{ $attributes->merge(['class' => $baseClass . ($highlight ? $highlightClass : $normalClass)]) }}>
    
    @if($highlight)
    {{-- Effect Layer With Overflow Hidden --}}
    <div class="absolute inset-0 rounded-2xl overflow-hidden pointer-events-none">
        <div class="absolute inset-0 bg-gradient-to-br from-white/10 to-transparent"></div>
        <div class="absolute -right-6 -top-6 w-24 h-24 bg-white/10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700"></div>
    </div>
    @endif

    <div class="p-5 flex flex-col justify-between h-full relative z-10">
        <div class="flex items-start justify-between mb-4 gap-3">
            <span class="{{ $titleClass }} line-clamp-2 leading-tight flex items-center gap-1.5">
                {{ $title }}
                @if(isset($popover))
                <svg class="w-3.5 h-3.5 {{ $highlight ? 'text-emerald-300' : 'text-stone-400' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                @endif
            </span>
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

    @if(isset($popover))
    {{-- Popover Render --}}
    <div x-show="openPopover" 
         style="display: none;"
         x-transition:enter="transition ease-out duration-200 origin-top-left"
         x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150 origin-top-left"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
         class="absolute top-16 left-6 z-50 min-w-[240px] bg-white rounded-2xl shadow-xl shadow-stone-900/10 border border-stone-200/60 p-4 ring-1 ring-black/5"
         @click.stop>
        
        @if(isset($popoverTitle))
        <h4 class="text-[10px] sm:text-[11px] font-bold text-stone-400 uppercase tracking-wider mb-3">{{ $popoverTitle }}</h4>
        @endif
        
        <div class="flex flex-col gap-3">
            {{ $popover }}
        </div>
    </div>
    @endif
</div>
