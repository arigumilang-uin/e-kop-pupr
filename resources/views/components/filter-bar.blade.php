@props(['searchPlaceholder' => 'Cari...'])

<style>
    html.theme-white .filter-bar-custom {
        background-color: #fafaf9 !important;
    }
</style>

<div class="filter-bar-custom sticky top-[120px] z-30 -mx-4 sm:-mx-6 px-4 sm:px-6 py-4 bg-[#f0efe9] dark:bg-stone-950 transition-all duration-200">
    @if(isset($header))
        <div class="mb-4">
            {{ $header }}
        </div>
    @endif
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        {{-- Search and filters wrapper (Left Aligned) --}}
        <div class="flex items-center gap-3 w-full lg:w-auto lg:flex-1">
            {{-- Search Box --}}
            <div class="flex-1 lg:flex-none lg:w-80 xl:w-96 relative">
                <input type="text" {{ $attributes }} placeholder="{{ $searchPlaceholder }}"
                       class="w-full pl-10 pr-4 py-2.5 bg-white dark:bg-stone-900 border border-stone-200 dark:border-stone-800 rounded-xl text-sm focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] transition-all outline-none shadow-sm placeholder:text-stone-400 text-stone-700 dark:text-stone-250">
                <svg class="w-5 h-5 text-stone-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>

            {{-- Filter Dropdown Button & Popover --}}
            @if(isset($filters))
            <div class="relative shrink-0" x-data="{ openFilter: false }" @click.away="openFilter = false">
                <button @click="openFilter = !openFilter" type="button" class="relative flex items-center justify-center w-[44px] sm:w-[46px] lg:w-auto lg:px-4 h-[44px] sm:h-[46px] bg-white dark:bg-stone-900 border border-stone-200 dark:border-stone-800 text-stone-600 dark:text-stone-300 rounded-xl hover:bg-stone-50 dark:hover:bg-stone-800 focus:ring-2 focus:ring-[#043d2e]/20 transition-all shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    <span class="hidden lg:ml-2 lg:block text-sm font-semibold">Filter Data</span>
                    
                    {{-- Slot Indicator Tanda Merah Aktif --}}
                    @if(isset($indicator))
                        {{ $indicator }}
                    @endif
                </button>

                <!-- Dropdown Menu -->
                <div x-show="openFilter" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                     x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                     class="absolute right-0 top-full mt-3 w-[320px] sm:w-[360px] md:w-[420px] p-5 bg-white dark:bg-stone-900 border border-stone-200 dark:border-stone-800 rounded-2xl shadow-xl z-50 flex flex-col gap-4 origin-top-right ring-1 ring-black/5"
                     style="display: none;">
                    
                    <div class="flex items-center justify-between border-b border-stone-100 dark:border-stone-800 pb-3 -mx-5 px-5 -mt-2">
                        <h3 class="text-sm font-bold text-stone-800 dark:text-stone-200">Filter Spesifik</h3>
                        <button @click="openFilter = false" type="button" class="text-stone-400 hover:text-stone-600 dark:hover:text-stone-300 bg-stone-50 dark:bg-stone-800 hover:bg-stone-100 dark:hover:bg-stone-750 p-1 rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    
                    <div class="flex flex-col gap-4">
                        {{ $filters }}
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- Slot Trailing & Action Buttons (Moved from top bar!) --}}
        <div class="shrink-0 w-full lg:w-auto flex items-center lg:justify-end gap-3.5">
            @if(isset($trailing))
                {{ $trailing }}
            @endif
            
            @hasSection('actions')
                <div class="flex items-center gap-2.5">
                    @yield('actions')
                </div>
            @endif
        </div>
    </div>
</div>
