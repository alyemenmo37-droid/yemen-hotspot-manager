@extends('layouts.app')

@section('title', $user->name)

@section('content')
    <div style="max-width: 600px; margin: 0 auto;">
        <h1 style="color: #009639; margin-bottom: 20px;">👤 {{ $user->name }}</h1>
        
        <div style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
            <table style="width: 100%;">
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 12px 0; color: #666; font-weight: bold;">الرقم:</td>
                    <td style="padding: 12px 0;">{{ $user->id }}</td>
                </tr>
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 12px 0; color: #666; font-weight: bold;">الاسم:</td>
                    <td style="padding: 12px 0;">{{ $user->name }}</td>
                </tr>
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 12px 0; color: #666; font-weight: bold;">البريد:</td>
                    <td style="padding: 12px 0; font-family: monospace;">{{ $user->email }}</td>
                </tr>
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 12px 0; color: #666; font-weight: bold;">الدور:</td>
                    <td style="padding: 12px 0;">
                        @switch($user->role)
                            @case('admin') 👑 مسؤول @break
                            @case('manager') 👔 مشرف @break
                            @case('operator') 🔧 مشغل @break
                        @endswitch
                    </td>
                </tr>
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 12px 0; color: #666; font-weight: bold;">الحالة:</td>
                    <td style="padding: 12px 0;">{{ $user->is_active ? '✅ نشط' : '❌ موقوف' }}</td>
                </tr>
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 12px 0; color: #666; font-weight: bold;">تاريخ الإنشاء:</td>
                    <td style="padding: 12px 0;">{{ $user->created_at->format('Y-m-d H:i') }}</td>
                </tr>
                <tr>
                    <td style="padding: 12px 0; color: #666; font-weight: bold;">آخر دخول:</td>
                    <td style="padding: 12px 0;">{{ $user->last_login_at ? $user->last_login_at->format('Y-m-d H:i') : 'لم يدخل' }}</td>
                </tr>
            </table>
        </div>
        
        <div style="margin-top: 20px; display: flex; gap: 10px;">
            <a href="{{ route('admin.users.edit', $user) }}" 
               style="background: #f57c00; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: bold;">
                ✏️ تعديل
            </a>
            <a href="{{ route('admin.users.index') }}" 
               style="background: #666; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: bold;">
                ← رجوع
            </a>
        </div>
    </div>
@endsection
