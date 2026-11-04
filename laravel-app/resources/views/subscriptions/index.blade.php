@extends('layouts.app')
@section('title', 'اشتراكاتي')

@section('content')
<div style="display:flex;justify-content:space-between;margin-bottom:20px;">
    <h1 style="color:#009639;">📅 اشتراكاتي</h1>
    <a href="{{ route('subscriptions.create') }}" class="btn" style="background:#009639;color:white;padding:10px 20px;border-radius:8px;text-decoration:none;">➕ اشتراك جديد</a>
</div>

@if(session('success'))
<div style="background:#e8f5e9;color:#2e7d32;padding:15px;border-radius:8px;margin-bottom:15px;">✅ {{ session('success') }}</div>
@endif

<table style="width:100%;background:white;border-radius:12px;box-shadow:0 2px 10px rgba(0,0,0,0.08);border-collapse:collapse;">
    <thead>
        <tr style="background:#f5f5f5;">
            <th style="padding:15px;text-align:right;">#</th>
            <th style="padding:15px;text-align:right;">السرعة</th>
            <th style="padding:15px;text-align:right;">البداية</th>
            <th style="padding:15px;text-align:right;">النهاية</th>
            <th style="padding:15px;text-align:right;">الحالة</th>
        </tr>
    </thead>
    <tbody>
        @forelse($subs as $sub)
        <tr style="border-bottom:1px solid #eee;">
            <td style="padding:15px;">{{ $sub->id }}</td>
            <td style="padding:15px;font-weight:bold;">{{ $sub->speedLevel->name ?? '-' }}</td>
            <td style="padding:15px;">{{ $sub->starts_at->format('Y-m-d') }}</td>
            <td style="padding:15px;">{{ $sub->ends_at->format('Y-m-d') }}</td>
            <td style="padding:15px;">
                <span style="background:{{ $sub->isActive() ? '#e8f5e9' : '#ffebee' }};color:{{ $sub->isActive() ? '#2e7d32' : '#c62828' }};padding:4px 12px;border-radius:20px;font-size:12px;">
                    {{ $sub->isActive() ? '✅ نشط' : '⌛ منتهي' }}
                </span>
            </td>
        </tr>
        @empty
        <tr><td colspan="5" style="padding:40px;text-align:center;color:#999;">لا توجد اشتراكات</td></tr>
        @endforelse
    </tbody>
</table>
@endsection
