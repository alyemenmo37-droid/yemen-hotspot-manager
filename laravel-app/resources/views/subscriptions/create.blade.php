@extends('layouts.app')
@section('title', 'اشتراك جديد')
@section('content')
<div style="max-width:500px;margin:0 auto;">
    <h1 style="color:#009639;">➕ اشتراك جديد</h1>
    <form method="POST" action="{{ route('subscriptions.store') }}" style="background:white;padding:30px;border-radius:12px;box-shadow:0 2px 10px rgba(0,0,0,0.08);">
        @csrf
        <label style="display:block;margin-bottom:8px;font-weight:bold;">اختر السرعة:</label>
        <select name="speed_level_id" required style="width:100%;padding:12px;border:2px solid #eee;border-radius:8px;box-sizing:border-box;margin-bottom:15px;">
            @foreach($speedLevels as $level)
            <option value="{{ $level->id }}">{{ $level->name }} — {{ $level->rate_limit }} — {{ $level->price }} ريال</option>
            @endforeach
        </select>
        <button type="submit" style="background:#009639;color:white;padding:12px 20px;border:none;border-radius:8px;font-weight:bold;cursor:pointer;">اشترك</button>
        <a href="{{ route('subscriptions.index') }}" style="margin-left:10px;color:#666;">إلغاء</a>
    </form>
</div>
@endsection
