@extends('layouts.app')

@section('title', 'الملف الشخصي')

@section('content')
    <div style="max-width: 800px; margin: 0 auto;">
        <h1 style="color: #009639; margin-bottom: 20px;">👤 الملف الشخصي</h1>
        
        {{-- Header Card --}}
        <div style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); margin-bottom: 20px;">
            <div style="display: flex; align-items: center; gap: 20px; flex-wrap: wrap;">
                <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #009639, #007a2e); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 32px; font-weight: bold;">
                    {{ $user->initials() }}
                </div>
                <div style="flex: 1;">
                    <h2 style="color: #333; margin-bottom: 5px;">{{ $user->name }}</h2>
                    <p style="color: #666; margin-bottom: 10px;">{{ $user->email }}</p>
                    @php
                        $roleColors = [
                            'admin' => ['bg' => '#ffebee', 'color' => '#c62828', 'label' => '👑 مسؤول'],
                            'manager' => ['bg' => '#fff3e0', 'color' => '#e65100', 'label' => '👔 مشرف'],
                            'operator' => ['bg' => '#e3f2fd', 'color' => '#1565c0', 'label' => '🔧 مشغل'],
                        ];
                        $rc = $roleColors[$user->role] ?? ['bg' => '#eee', 'color' => '#666', 'label' => $user->role];
                    @endphp
                    <span style="background: {{ $rc['bg'] }}; color: {{ $rc['color'] }}; padding: 5px 15px; border-radius: 20px; font-size: 13px; font-weight: bold;">
                        {{ $rc['label'] }}
                    </span>
                </div>
            </div>
            
            <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee; display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; font-size: 13px; color: #666;">
                <div>
                    <strong style="color: #333;">📅 تاريخ الإنشاء:</strong><br>
                    {{ $user->created_at->format('Y-m-d') }}
                </div>
                <div>
                    <strong style="color: #333;">🕐 آخر دخول:</strong><br>
                    {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'لم يدخل بعد' }}
                </div>
            </div>
        </div>
        
        {{-- Update Basic Info --}}
        <div style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); margin-bottom: 20px;">
            <h3 style="color: #009639; margin-bottom: 20px;">📝 البيانات الأساسية</h3>
            
            <form method="POST" action="{{ route('profile.update') }}">
                @csrf
                @method('PUT')
                
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: bold;">الاسم:</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                           style="width: 100%; padding: 12px; border: 2px solid {{ $errors->has('name') ? '#c62828' : '#eee' }}; border-radius: 8px; box-sizing: border-box;">
                    @error('name')
                        <div style="color: #c62828; font-size: 13px; margin-top: 5px;">❌ {{ $message }}</div>
                    @enderror
                </div>
                
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: bold;">البريد الإلكتروني:</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                           style="width: 100%; padding: 12px; border: 2px solid {{ $errors->has('email') ? '#c62828' : '#eee' }}; border-radius: 8px; box-sizing: border-box;">
                    @error('email')
                        <div style="color: #c62828; font-size: 13px; margin-top: 5px;">❌ {{ $message }}</div>
                    @enderror
                </div>
                
                <button type="submit" style="background: #009639; color: white; padding: 12px 25px; border: none; border-radius: 8px; font-weight: bold; cursor: pointer;">
                    💾 حفظ التغييرات
                </button>
            </form>
        </div>
        
        {{-- Change Password --}}
        <div style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
            <h3 style="color: #c62828; margin-bottom: 20px;">🔐 تغيير كلمة المرور</h3>
            
            <form method="POST" action="{{ route('profile.password') }}">
                @csrf
                @method('PUT')
                
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: bold;">كلمة المرور الحالية:</label>
                    <input type="password" name="current_password" required
                           style="width: 100%; padding: 12px; border: 2px solid {{ $errors->has('current_password') ? '#c62828' : '#eee' }}; border-radius: 8px; box-sizing: border-box;">
                    @error('current_password')
                        <div style="color: #c62828; font-size: 13px; margin-top: 5px;">❌ {{ $message }}</div>
                    @enderror
                </div>
                
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: bold;">كلمة المرور الجديدة:</label>
                    <input type="password" name="password" required
                           style="width: 100%; padding: 12px; border: 2px solid {{ $errors->has('password') ? '#c62828' : '#eee' }}; border-radius: 8px; box-sizing: border-box;">
                    <small style="color: #666; font-size: 12px;">8 أحرف على الأقل، تحتوي على أحرف وأرقام</small>
                    @error('password')
                        <div style="color: #c62828; font-size: 13px; margin-top: 5px;">❌ {{ $message }}</div>
                    @enderror
                </div>
                
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: bold;">تأكيد كلمة المرور:</label>
                    <input type="password" name="password_confirmation" required
                           style="width: 100%; padding: 12px; border: 2px solid #eee; border-radius: 8px; box-sizing: border-box;">
                </div>
                
                <button type="submit" style="background: #c62828; color: white; padding: 12px 25px; border: none; border-radius: 8px; font-weight: bold; cursor: pointer;">
                    🔐 تغيير كلمة المرور
                </button>
            </form>
        </div>
    </div>
@endsection
