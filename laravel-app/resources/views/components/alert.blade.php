@props([
    'type' => 'info',
    'dismissible' => false,
])

@php
    $icons = [
        'success' => '✅',
        'error' => '❌',
        'warning' => '⚠️',
        'info' => 'ℹ️',
    ];
    
    $icon = $icons[$type] ?? 'ℹ️';
@endphp

<div {{ $attributes->merge(['class' => "alert alert-$type"]) }}>
    {{ $icon }} {{ $slot }}
</div>
