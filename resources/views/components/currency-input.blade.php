@props([
    'name',
    'value' => '',
    'required' => false,
    'placeholder' => '0',
    'min' => null,
    'max' => null,
    'step' => null,
    'disabled' => false
])

<div x-data="currencyInput('{{ $value }}')" class="relative flex items-center">
    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
        <span class="text-stone-400 font-bold text-sm">Rp</span>
    </div>
    
    <!-- Input yang terlihat (Formatted) -->
    <input type="text" 
           x-model="formattedValue" 
           @input="formatInput" 
           @blur="formatInput"
           {{ $required ? 'required' : '' }} 
           {{ $disabled ? 'disabled' : '' }}
           placeholder="{{ $placeholder }}"
           class="w-full pl-[2.25rem] pr-4 py-2.5 bg-stone-50 border border-stone-200 rounded-xl text-stone-800 font-mono font-bold focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] outline-none shadow-sm transition-all text-sm {{ $disabled ? 'opacity-70 cursor-not-allowed bg-stone-100' : '' }}">
           
    <!-- Hidden input yang mensubmit data asli (Raw Numeric) -->
    <input type="hidden" name="{{ $name }}" x-model="rawValue" {{ $disabled ? 'disabled' : '' }}>
</div>

@once
@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('currencyInput', (initialValue) => ({
        rawValue: initialValue || '',
        formattedValue: initialValue ? new Intl.NumberFormat('id-ID').format(initialValue) : '',
        
        formatInput(e) {
            let val = e.target.value.replace(/[^0-9]/g, '');
            if(val === '') {
                this.rawValue = '';
                this.formattedValue = '';
                return;
            }
            // Parse to int to remove leading zeros
            val = parseInt(val, 10).toString();
            this.rawValue = val;
            this.formattedValue = new Intl.NumberFormat('id-ID').format(val);
        }
    }))
})
</script>
@endpush
@endonce
