@props(['disabled' => false, 'icon' => null])
<div class="relative items-center">
    @if($icon)
        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
            {!! $icon !!}
        </div>
    @endif
    <input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'w-full h-12 rounded-xl border border-slate-200/80 outline-none focus:border-slate-800 focus:ring-4 focus:ring-slate-800/5 transition-all text-[15px] font-medium text-slate-800 bg-white placeholder:text-slate-400 placeholder:font-normal' . ($icon ? ' pl-11 pr-4' : ' px-4')]) !!}>
</div>
