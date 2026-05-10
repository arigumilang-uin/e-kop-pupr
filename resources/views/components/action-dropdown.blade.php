<div class="relative inline-block text-left" x-data="{ open: false }" @click.away="open = false" @keydown.escape.window="open = false">
    <div>
        <button type="button" @click="open = !open" class="p-2 text-stone-400 hover:text-stone-800 hover:bg-stone-100 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-[#043d2e]/20" aria-expanded="true" aria-haspopup="true">
            <span class="sr-only">Buka opsi aksi</span>
            <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
            </svg>
        </button>
    </div>

    <div x-show="open" 
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="transform opacity-0 scale-95 translate-y-1"
         x-transition:enter-end="transform opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="transform opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="transform opacity-0 scale-95 translate-y-1"
         class="absolute right-8 top-0 mt-0 w-max min-w-[12rem] rounded-xl shadow-lg bg-white ring-1 ring-black/5 divide-y divide-stone-100 z-50 origin-top-right"
         style="display: none;">
        <div class="py-1">
            {{ $slot }}
        </div>
    </div>
</div>
