@props(['sticky' => true])

<thead {{ $attributes->merge(['class' => ($sticky ? 'sticky top-[168px] z-20 ' : '') . 'shadow-sm border-b border-stone-200 bg-stone-50']) }}>
    <tr class="divide-x divide-stone-200">
        {{ $slot }}
    </tr>
</thead>
