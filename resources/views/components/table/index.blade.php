<div class="w-full overflow-x-auto lg:overflow-x-visible">
    <table {{ $attributes->merge(['class' => 'w-full text-left text-sm text-stone-600 whitespace-nowrap']) }}>
        {{ $slot }}
    </table>
</div>
