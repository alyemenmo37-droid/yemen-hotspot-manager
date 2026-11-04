@extends('layouts.app')
@section('title', 'إدارة الشبكات')

@section('content')
<div style="display:flex;justify-content:space-between;margin-bottom:20px;">
    <h1 style="color:#009639;">🏢 إدارة الشبكات (Tenants)</h1>
    <a href="{{ route('admin.tenants.create') }}" class="btn" style="background:#009639;color:white;padding:10px 20px;border-radius:8px;text-decoration:none;">➕ إضافة شبكة</a>
</div>

@if(session('success'))
<div style="background:#e8f5e9;color:#2e7d32;padding:15px;border-radius:8px;margin-bottom:15px;">✅ {{ session('success') }}</div>
@endif

<table style="width:100%;background:white;border-radius:12px;box-shadow:0 2px 10px rgba(0,0,0,0.08);border-collapse:collapse;">
    <thead>
        <tr style="background:#f5f5f5;">
            <th style="padding:15px;text-align:right;">#</th>
            <th style="padding:15px;text-align:right;">الاسم</th>
            <th style="padding:15px;text-align:right;">Domain</th>
            <th style="padding:15px;text-align:right;">المستخدمون</th>
            <th style="padding:15px;text-align:right;">البطاقات</th>
            <th style="padding:15px;text-align:right;">إجراءات</th>
        </tr>
    </thead>
    <tbody>
        @foreach($tenants as $tenant)
        <tr style="border-bottom:1px solid #eee;">
            <td style="padding:15px;">{{ $tenant->id }}</td>
            <td style="padding:15px;font-weight:bold;">{{ $tenant->name }}</td>
            <td style="padding:15px;font-family:monospace;">{{ $tenant->domain ?? '-' }}</td>
            <td style="padding:15px;">{{ $tenant->users_count }}</td>
            <td style="padding:15px;">{{ $tenant->cards_count }}</td>
            <td style="padding:15px;">
                <a href="{{ route('admin.tenants.edit', $tenant) }}" style="color:#f57c00;">تعديل</a>
                |
                <form method="POST" action="{{ route('admin.tenants.destroy', $tenant) }}" style="display:inline;" onsubmit="return confirm('حذف؟')">
                    @csrf @method('DELETE')
                    <button type="submit" style="background:none;border:none;color:#c62828;cursor:pointer;">حذف</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
