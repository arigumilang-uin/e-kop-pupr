{{-- Reusable Neraca Row (single line item) --}}
<div class="group flex justify-between items-center py-2.5 px-3 hover:bg-stone-50 rounded-xl transition-colors border-b border-dashed border-stone-200">
    <div class="flex items-center gap-3">
        <div class="w-1.5 h-1.5 rounded-full bg-stone-300 group-hover:bg-[#043d2e] transition-colors"></div>
        <span class="text-sm font-medium text-stone-700">{{ $label }}</span>
    </div>
    <span class="text-[14px] font-mono font-bold text-stone-800">{{ format_rupiah($value) }}</span>
</div>
