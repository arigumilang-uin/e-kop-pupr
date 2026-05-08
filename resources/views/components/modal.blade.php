{{--
    Reusable Modal Component
    
    Usage:
    <x-modal name="my-modal" title="Title" subtitle="Optional subtitle" maxWidth="lg">
        <p>Modal body content here</p>
        
        <x-slot name="footer">
            <button @click="$dispatch('close-modal', 'my-modal')">Cancel</button>
            <button type="submit">Save</button>
        </x-slot>
    </x-modal>
    
    Open with: $dispatch('open-modal', 'my-modal')
    Close with: $dispatch('close-modal', 'my-modal')
    
    Note: This component is self-contained with its own x-data scope.
    It can be placed anywhere in the page — no parent x-data required.
    The modal renders at its DOM position but uses fixed positioning to overlay the page.
--}}

@props([
    'name',
    'title' => '',
    'subtitle' => '',
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

<div
    x-data="{ show: false }"
    x-on:open-modal.window="if ($event.detail === '{{ $name }}') { show = true; document.body.classList.add('overflow-hidden') }"
    x-on:close-modal.window="if ($event.detail === '{{ $name }}') { show = false; document.body.classList.remove('overflow-hidden') }"
    x-on:keydown.escape.window="if (show) { show = false; document.body.classList.remove('overflow-hidden') }"
    x-show="show"
    x-cloak
    class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-0"
    style="display: none;"
>
    {{-- Backdrop --}}
    <div 
        x-show="show" 
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0" 
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100" 
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-stone-900/60 backdrop-blur-sm" 
        @click="show = false; document.body.classList.remove('overflow-hidden')"
    ></div>
    
    {{-- Modal Panel --}}
    <div 
        x-show="show" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        class="relative bg-white rounded-2xl border border-stone-200 shadow-2xl w-full {{ $maxWidthClass }} overflow-hidden z-10 flex flex-col max-h-[90vh]"
    >
        {{-- Header --}}
        @if($title)
        <div class="px-6 py-5 bg-[#043d2e] border-b border-[#022a20] flex items-center justify-between shrink-0">
            <div>
                <h3 class="text-base font-bold text-white">{{ $title }}</h3>
                @if($subtitle)
                    <p class="text-[13px] text-emerald-300 font-bold mt-0.5">{{ $subtitle }}</p>
                @endif
            </div>
            <button type="button" @click="show = false; document.body.classList.remove('overflow-hidden')" class="text-emerald-300 hover:bg-emerald-800 hover:text-white p-2 rounded-xl transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        @endif

        {{-- Body --}}
        <div class="p-6 space-y-5 overflow-y-auto custom-scrollbar">
            {{ $slot }}
        </div>

        {{-- Footer --}}
        @if(isset($footer))
        <div class="px-6 py-5 border-t border-stone-100 bg-stone-50 shrink-0 flex items-center justify-end gap-3">
            {{ $footer }}
        </div>
        @endif
    </div>
</div>
