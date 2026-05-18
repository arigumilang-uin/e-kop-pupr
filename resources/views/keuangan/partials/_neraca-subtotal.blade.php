{{-- Reusable Neraca Subtotal Row --}}
<div class="flex justify-between items-center py-3 px-3 bg-stone-100/50 rounded-xl mt-3 border border-stone-200 border-dashed">
    <span class="text-[12px] font-black text-stone-600 uppercase tracking-wider pl-4">{{ $label }}</span>
    <span class="text-[14px] font-mono font-black text-stone-800">{{ format_rupiah($value) }}</span>
</div>
