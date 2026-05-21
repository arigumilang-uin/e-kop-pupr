@props(['sticky' => true, 'topOffset' => 'lg:top-[168px]'])

<thead {{ $attributes->merge(['class' => ($sticky ? "lg:sticky {$topOffset} z-20 " : '') . 'shadow-sm border-b border-stone-200 dark:border-stone-800 bg-stone-50 dark:bg-stone-850 transition-colors']) }}>
    <tr class="divide-x divide-stone-200 dark:divide-stone-800">
        {{ $slot }}
    </tr>
</thead>
