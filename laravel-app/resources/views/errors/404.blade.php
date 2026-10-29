@extends('layouts.app')

@section('title', 'الصفحة غير موجودة')

@section('content')
    <div style="text-align: center; padding: 80px 20px;">
        <div style="font-size: 120px; line-height: 1;">🔍</div>
        <h1 style="color: #009639; font-size: 72px; margin: 20px 0;">404</h1>
        <h2 style="color: #333; font-size: 24px; margin-bottom: 15px;">
            الصفحة غير موجودة
        </h2>
        <p style="color: #666; font-size: 16px; margin-bottom: 30px;">
            الرابط الذي تحاول الوصول إليه غير صحيح أو تم حذفه.
        </p>
        <a href="{{ route('dashboard') }}" 
           style="background: #009639; color: white; padding: 12px 30px; border-radius: 8px; text-decoration: none; font-weight: bold; display: inline-block;">
            🏠 العودة للرئيسية
        </a>
    </div>
@endsection
