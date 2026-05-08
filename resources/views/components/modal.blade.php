@props([
    'name' => '',
    'maxWidth' => 'md',
])

@php
$maxWidthClass = match($maxWidth) {
    'sm' => 'sm:max-w-sm',
    'md' => 'sm:max-w-md',
    'lg' => 'sm:max-w-lg',
    'xl' => 'sm:max-w-xl',
    '2xl' => 'sm:max-w-2xl',
    '3xl' => 'sm:max-w-3xl',
    '4xl' => 'sm:max-w-4xl',
    default => 'sm:max-w-md',
};
@endphp

<template x-teleport="body">
    <div x-show="modalName === '{{ $name }}'" 
         class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-0" 
         style="display: none;"
         x-cloak>
        
        {{-- Backdrop --}}
        <div x-show="modalName === '{{ $name }}'" 
             x-transition.opacity.duration.200ms 
             class="fixed inset-0 bg-stone-900/60 backdrop-blur-sm" 
             @click="closeModal()"></div>
        
        {{-- Panel --}}
        <div x-show="modalName === '{{ $name }}'"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="relative bg-white rounded-2xl border border-stone-200 shadow-2xl w-full {{ $maxWidthClass }} overflow-hidden z-10 flex flex-col max-h-[90vh]">
            
            {{ $slot }}
        </div>
    </div>
</template>
