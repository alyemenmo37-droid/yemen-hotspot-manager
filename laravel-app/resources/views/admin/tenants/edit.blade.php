@extends('layouts.app')
@section('title', 'تعديل شبكة')
@section('content')
<div style="max-width:500px;margin:0 auto;">
    <h1 style="color:#f57c00;">✏️ تعديل الشبكة</h1>
    <form method="POST" action="{{ route('admin.tenants.update', $tenant) }}" style="background:white;padding:30px;border-radius:12px;box-shadow:0 2px 10px rgba(0,0,0,0.08);">
        @csrf @method('PUT')
        <label style="display:block;margin-bottom:8px;font-weight:bold;">الاسم:</label>
        <input type="text" name="name" value="{{ $tenant->name }}" required style="width:100%;padding:12px;border:2px solid #eee;border-radius:8px;box-sizing:border-box;margin-bottom:15px;">

        <label style="display:block;margin-bottom:8px;font-weight:bold;">Domain:</label>
        <input type="text" name="domain" value="{{ $tenant->domain }}" style="width:100%;padding:12px;border:2px solid #eee;border-radius:8px;box-sizing:border-box;margin-bottom:15px;">

        <button type="submit" style="background:#009639;color:white;padding:12px 20px;border:none;border-radius:8px;font-weight:bold;cursor:pointer;">حفظ</button>
        <a href="{{ route('admin.tenants.index') }}" style="margin-left:10px;color:#666;">إلغاء</a>
    </form>
</div>
@endsection
