@props([
    'name',
    'checked' => false,
    'disabled' => false,
    'value' => '1',
    'fallbackValue' => '0',
    'label' => '',
    'description' => ''
])

<div {{ $attributes->merge(['class' => 'p-4 rounded-xl border flex items-start gap-4 transition-colors ' . ($disabled ? 'bg-stone-50 border-stone-200 opacity-70' : 'bg-[#043d2e]/5 border-[#043d2e]/10')]) }}>
    <div class="pt-0.5">
        <label class="relative inline-flex items-center {{ $disabled ? 'cursor-not-allowed' : 'cursor-pointer' }}">
            <input type="hidden" name="{{ $name }}" value="{{ $fallbackValue }}" {{ $disabled ? 'disabled' : '' }}>
            <input type="checkbox" name="{{ $name }}" value="{{ $value }}"
                   {{ $checked ? 'checked' : '' }}
                   {{ $disabled ? 'disabled' : '' }}
                   class="sr-only peer">
            
            <div class="w-10 h-5 bg-stone-300 peer-focus:ring-2 peer-focus:ring-[#043d2e]/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-stone-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[#043d2e] {{ $disabled ? 'opacity-80' : '' }}"></div>
        </label>
    </div>
    
    <div class="flex-1">
        @if(isset($label) && $label !== '')
            <p class="text-[12px] font-bold text-stone-800 leading-snug">{{ $label }}</p>
        @endif
        
        @if(isset($description) && $description !== '')
            <p class="text-[11px] text-stone-500 font-medium mt-0.5 leading-relaxed">{{ $description }}</p>
        @endif
        
        {{ $slot }}
    </div>
</div>
