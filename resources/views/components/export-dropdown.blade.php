@props(['excelRoute', 'pdfRoute'])

<div x-data="{ openExport: false }" class="relative w-full md:w-auto">
    <button @click="openExport = !openExport" type="button" class="w-full py-2.5 px-4 rounded-xl bg-white border border-stone-200 hover:bg-stone-50 text-stone-700 text-sm font-bold transition-colors flex items-center justify-between gap-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-[#043d2e]/20">
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Unduh Laporan
        </div>
        <svg class="w-4 h-4 transition-transform shrink-0" :class="openExport && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
    </button>
    
    <div x-show="openExport" @click.away="openExport = false" x-transition x-cloak
         class="absolute right-0 md:right-0 left-0 md:left-auto mt-2 md:w-48 rounded-xl bg-white border border-stone-200 shadow-xl z-50 overflow-hidden flex flex-col"
         style="display: none;">
        <a id="export-excel-link" href="{{ $excelRoute }}" class="flex items-center gap-3 px-4 py-3 text-sm text-emerald-700 hover:bg-emerald-50 font-bold transition-colors border-b border-stone-100 w-full">
            <span class="w-7 h-7 rounded bg-emerald-100 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </span>
            Export Excel
        </a>
        <a id="export-pdf-link" href="{{ $pdfRoute }}" class="flex items-center gap-3 px-4 py-3 text-sm text-red-600 hover:bg-red-50 font-bold transition-colors w-full">
            <span class="w-7 h-7 rounded bg-red-100 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
            </span>
            Export PDF
        </a>
    </div>
</div>
