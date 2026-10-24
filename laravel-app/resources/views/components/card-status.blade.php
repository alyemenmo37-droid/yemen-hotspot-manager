@props(['status'])

@php
    $config = match($status) {
        'active' => ['class' => 'badge-active', 'text' => '✅ نشطة'],
        'unused' => ['class' => 'badge-unused', 'text' => '⏸️ غير مستخدمة'],
        'expired' => ['class' => 'badge-expired', 'text' => '⌛ منتهية'],
        'stopped' => ['class' => 'badge-stopped', 'text' => '🛑 موقوفة'],
        default => ['class' => '', 'text' => $status],
    };
@endphp

<span class="badge {{ $config['class'] }}" 
      style="padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: bold;">
    {{ $config['text'] }}
</span>

@push('styles')
<style>
    .badge-active { background: #e8f5e9; color: #2e7d32; }
    .badge-unused { background: #fff3e0; color: #e65100; }
    .badge-expired { background: #ffebee; color: #c62828; }
    .badge-stopped { background: #eee; color: #666; }
</style>
@endpush
