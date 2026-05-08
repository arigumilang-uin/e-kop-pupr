@props([
    'cancelText' => 'Batal',
    'submitText' => 'Simpan',
    'submitColor' => 'primary', // primary, red, emerald
    'hideCancel' => false,
])

@php
$colorClass = match($submitColor) {
    'red' => 'bg-red-600 hover:bg-red-700 text-white',
    'emerald' => 'bg-emerald-600 hover:bg-emerald-700 text-white',
    default => 'bg-[#043d2e] hover:bg-[#043d2e]/90 text-white',
};
@endphp

<div class="px-6 py-5 border-t border-stone-100 bg-stone-50 shrink-0 flex items-center justify-end gap-3">
    @if(!$hideCancel)
    <button type="button" @click="closeModal()" class="px-4 py-2.5 text-sm font-bold text-stone-500 hover:text-stone-800 hover:bg-stone-200/50 rounded-xl transition-all">{{ $cancelText }}</button>
    @endif
    <button type="submit" class="px-6 py-2.5 {{ $colorClass }} text-sm font-bold rounded-xl shadow-sm transition-all active:scale-95" :disabled="modalSubmitting">
        <span x-show="!modalSubmitting">{{ $submitText }}</span>
        <span x-show="modalSubmitting" class="flex items-center gap-2">
            <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Memproses...
        </span>
    </button>
</div>
