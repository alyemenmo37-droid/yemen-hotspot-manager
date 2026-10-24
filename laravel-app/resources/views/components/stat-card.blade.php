@props([
    'title' => '',
    'value' => '',
    'icon' => '📊',
    'color' => '#009639',
])

<div style="background: white; padding: 20px; border-radius: 12px; text-align: center; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
    <div style="font-size: 32px; margin-bottom: 8px;">{{ $icon }}</div>
    <div style="font-size: 26px; font-weight: bold; color: {{ $color }};">{{ $value }}</div>
    <div style="color: #666; margin-top: 5px; font-size: 13px;">{{ $title }}</div>
</div>
