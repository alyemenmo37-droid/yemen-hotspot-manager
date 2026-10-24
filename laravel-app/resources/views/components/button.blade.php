@props([
    'type' => 'button',
    'href' => null,
    'variant' => 'primary',
])

@php
    $classes = 'btn';
    if ($variant === 'secondary') $classes .= ' btn-secondary';
    if ($variant === 'danger') $classes .= ' btn-danger';
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
