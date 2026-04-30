@props(['href', 'icon' => null, 'color' => 'stone'])

@php
    $baseClasses = "group flex items-center w-full px-4 py-2.5 text-sm font-medium transition-colors text-left";
    
    if ($color === 'red') {
        $colorClasses = "text-red-600 hover:bg-red-50 hover:text-red-700";
    } elseif ($color === 'emerald') {
        $colorClasses = "text-emerald-600 hover:bg-emerald-50 hover:text-emerald-700";
    } else {
        // default stone/neutral
        $colorClasses = "text-stone-600 hover:bg-[#043d2e]/5 hover:text-[#043d2e]";
    }
@endphp

@if(isset($attributes['type']) && $attributes['type'] === 'button')
    <button {{ $attributes->merge(['class' => "$baseClasses $colorClasses"]) }}>
        @if($icon)
            <svg class="mr-3 w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                {!! $icon !!}
            </svg>
        @endif
        {{ $slot }}
    </button>
@else
    <a href="{{ $href ?? '#' }}" {{ $attributes->merge(['class' => "$baseClasses $colorClasses"]) }}>
        @if($icon)
            <svg class="mr-3 w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                {!! $icon !!}
            </svg>
        @endif
        {{ $slot }}
    </a>
@endif
