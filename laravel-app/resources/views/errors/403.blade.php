@extends('layouts.app')

@section('title', 'غير مصرح')

@section('content')
    <div style="text-align: center; padding: 60px 20px;">
        <div style="font-size: 120px; line-height: 1;">🚫</div>
        <h1 style="color: #c62828; font-size: 48px; margin: 20px 0;">403</h1>
        <h2 style="color: #333; font-size: 24px;">غير مصرح لك بالدخول</h2>
        <p style="color: #666; margin: 20px 0; font-size: 16px;">
            هذه الصفحة للمسؤولين فقط.
        </p>
        <div style="margin-top: 30px;">
            <x-button href="{{ route('dashboard') }}">
                ← العودة للوحة التحكم
            </x-button>
        </div>
    </div>
@endsection
