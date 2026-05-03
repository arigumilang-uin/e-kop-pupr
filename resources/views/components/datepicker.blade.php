@props([
    'name' => '',
    'value' => '',
    'required' => false,
    'disabled' => false,
    'placeholder' => 'Pilih Tanggal'
])

<div x-data="datepickerInit('{{ $value }}')" class="relative">
    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none z-10">
        <svg class="w-4 h-4 text-stone-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
    </div>
    
    <input type="text" 
           x-ref="picker"
           name="{{ $name }}"
           value="{{ $value }}"
           {{ $required ? 'required' : '' }}
           {{ $disabled ? 'disabled' : '' }}
           placeholder="{{ $placeholder }}"
           {{ $attributes->except(['class', 'name', 'value', 'required', 'disabled', 'placeholder']) }}
           class="w-full pl-10 pr-3 py-2.5 bg-stone-50 border border-stone-200 rounded-xl text-stone-800 font-bold focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] outline-none shadow-sm transition-all text-sm {{ $disabled ? 'cursor-not-allowed opacity-70 bg-stone-100' : 'bg-white cursor-pointer hover:bg-stone-50' }}">
</div>

@once
@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('datepickerInit', (initVal) => ({
        init() {
            flatpickr(this.$refs.picker, {
                dateFormat: "Y-m-d", // Value sent to the database
                altInput: true,      // Visible fake input
                altFormat: "d/m/Y",  // Visible format to user: DD/MM/YYYY
                defaultDate: initVal || null,
                locale: "id",        // Indonesian locale
                disableMobile: "true",
                onChange: function(selectedDates, dateStr, instance) {
                    // Trigger input event manually so x-model detects the change when altInput is true
                    instance.element.dispatchEvent(new Event('input', { bubbles: true }));
                }
            });
        }
    }));
});
</script>
<style>
/* Hide the original input slightly more predictably if needed, though flatpickr usually does this */
.flatpickr-mobile {
    display: none !important; 
}
</style>
@endpush
@endonce
