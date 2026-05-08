@props([
    'title' => '',
    'subtitle' => '',
])

<div class="px-6 py-5 border-b border-stone-100 bg-stone-50 flex items-center justify-between shrink-0">
    <div>
        <h3 class="text-base font-bold text-stone-800">{{ $title }}</h3>
        @if($subtitle)
            <p class="text-[13px] text-stone-500 font-medium mt-0.5">{{ $subtitle }}</p>
        @endif
    </div>
    <button type="button" @click="closeModal()" class="text-stone-400 hover:bg-stone-200 hover:text-stone-600 p-2 rounded-xl transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>
</div>
