@props(['id' => 'nip', 'name' => 'nip', 'model' => 'nip', 'required' => false, 'placeholder' => 'Contoh: 19800101...'])

<div x-data="{
    get formattedNip() {
        let val = String({{ $model }} || '').replace(/[^0-9]/g, '');
        let formatted = '';
        if(val.length > 0) formatted += val.substring(0, 8);
        if(val.length > 8) formatted += ' ' + val.substring(8, 14);
        if(val.length > 14) formatted += ' ' + val.substring(14, 15);
        if(val.length > 15) formatted += ' ' + val.substring(15, 18);
        return formatted;
    },
    set formattedNip(val) {
        {{ $model }} = String(val).replace(/[^0-9]/g, '').substring(0, 18);
    }
}">
    <!-- Hidden input stores raw 18-digit value for standard form submission -->
    <input type="hidden" name="{{ $name }}" :value="{{ $model }}">
    
    <x-input id="{{ $id }}"
        type="text" 
        x-model="formattedNip" 
        maxlength="21" 
        {{ $attributes }} 
        required="{{ $required }}" 
        placeholder="{{ $placeholder }}">
        
        @if(isset($icon))
            <x-slot name="icon">
                {{ $icon }}
            </x-slot>
        @endif
    </x-input>
</div>
