@extends('layouts.app')

@section('title', 'تسجيل الدخول')

@section('content')
    <div style="max-width: 420px; margin: 40px auto;">
        {{-- الشعار --}}
        <div style="text-align: center; margin-bottom: 30px;">
            <div style="font-size: 64px; line-height: 1;">📡</div>
            <h1 style="color: #009639; margin: 10px 0;">Yemen Hotspot Manager</h1>
            <p style="color: #666;">تسجيل الدخول إلى لوحة التحكم</p>
        </div>
        
        {{-- النموذج --}}
        <div style="background: white; padding: 30px; border-radius: 15px; box-shadow: 0 10px 40px rgba(0,0,0,0.1);">
            <form method="POST" action="{{ route('login.post') }}">
                @csrf
                
                {{-- Email --}}
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: bold;">
                        📧 البريد الإلكتروني:
                    </label>
                    <input type="email" 
                           name="email" 
                           value="{{ old('email') }}"
                           placeholder="admin@yhm.local"
                           required
                           autofocus
                           style="width: 100%; padding: 12px; border: 2px solid {{ $errors->has('email') ? '#c62828' : '#eee' }}; border-radius: 8px; font-size: 14px; box-sizing: border-box;">
                </div>
                
                {{-- Password --}}
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: bold;">
                        🔒 كلمة المرور:
                    </label>
                    <input type="password" 
                           name="password" 
                           placeholder="••••••"
                           required
                           style="width: 100%; padding: 12px; border: 2px solid {{ $errors->has('password') ? '#c62828' : '#eee' }}; border-radius: 8px; font-size: 14px; box-sizing: border-box;">
                </div>
                
                {{-- Remember --}}
                <div style="margin-bottom: 20px; display: flex; align-items: center;">
                    <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}
                           style="margin-left: 8px; width: 16px; height: 16px;">
                    <label for="remember" style="cursor: pointer; color: #666; font-size: 14px;">
                        تذكرني
                    </label>
                </div>
                
                {{-- Errors --}}
                @if($errors->any())
                    <div style="padding: 12px; background: #ffebee; color: #c62828; border-radius: 8px; margin-bottom: 20px; border-right: 4px solid #c62828;">
                        ❌ {{ $errors->first() }}
                    </div>
                @endif
                
                {{-- Submit --}}
                <button type="submit" 
                        style="width: 100%; padding: 14px; background: #009639; color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: bold; cursor: pointer; transition: background 0.2s;">
                    🚀 دخول
                </button>
            </form>
        </div>
        
        {{-- Test accounts --}}
        <div style="margin-top: 20px; padding: 15px; background: #f5f5f5; border-radius: 8px; font-size: 12px; color: #666;">
            <div style="font-weight: bold; margin-bottom: 8px;">🧪 حسابات للاختبار:</div>
            <div>👤 admin@yhm.local / admin123</div>
            <div>👤 manager@yhm.local / manager123</div>
            <div>👤 operator@yhm.local / operator123</div>
        </div>
    </div>
@endsection
