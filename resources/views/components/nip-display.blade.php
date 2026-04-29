@props(['value'])
@php
    $n = preg_replace('/[^0-9]/', '', $value);
    $formattedNip = $n;
    if (strlen($n) === 18) {
        $formattedNip = substr($n, 0, 8) . ' ' . substr($n, 8, 6) . ' ' . substr($n, 14, 1) . ' ' . substr($n, 15, 3);
    }
@endphp
<span {{ $attributes }}>{{ $formattedNip }}</span>
