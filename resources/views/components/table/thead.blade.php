@props(['sticky' => true, 'topOffset' => 'lg:top-[168px]'])

<thead {{ $attributes->merge(['class' => ($sticky ? "lg:sticky {$topOffset} z-20 " : '') . 'shadow-sm border-b border-stone-200 bg-stone-50']) }}>
    <tr class="divide-x divide-stone-200">
        {{ $slot }}
    </tr>
</thead>
