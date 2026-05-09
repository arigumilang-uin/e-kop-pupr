@props(['fallback' => null])

<a href="{{ url()->previous() !== url()->current() ? url()->previous() : ($fallback ?? url('/')) }}" 
   {{ $attributes->merge(['class' => 'inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-stone-200 text-stone-600 rounded-xl text-sm font-bold hover:bg-stone-50 transition-all hover:-translate-x-1 shadow-sm']) }}>
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
    </svg>
    {{ $slot->isEmpty() ? 'Kembali' : $slot }}
</a>
