@extends('layouts.app')

@section('title', 'إضافة مستخدم')

@section('content')
    <div style="max-width: 600px; margin: 0 auto;">
        <h1 style="color: #009639; margin-bottom: 20px;">➕ إضافة مستخدم جديد</h1>
        
        <div style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf
                
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: bold;">الاسم:</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           style="width: 100%; padding: 12px; border: 2px solid {{ $errors->has('name') ? '#c62828' : '#eee' }}; border-radius: 8px; box-sizing: border-box;">
                    @error('name')
                        <div style="color: #c62828; font-size: 13px; margin-top: 5px;">❌ {{ $message }}</div>
                    @enderror
                </div>
                
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: bold;">البريد الإلكتروني:</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           style="width: 100%; padding: 12px; border: 2px solid {{ $errors->has('email') ? '#c62828' : '#eee' }}; border-radius: 8px; box-sizing: border-box;">
                    @error('email')
                        <div style="color: #c62828; font-size: 13px; margin-top: 5px;">❌ {{ $message }}</div>
                    @enderror
                </div>
                
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: bold;">كلمة المرور:</label>
                    <input type="password" name="password" required
                           style="width: 100%; padding: 12px; border: 2px solid {{ $errors->has('password') ? '#c62828' : '#eee' }}; border-radius: 8px; box-sizing: border-box;">
                    @error('password')
                        <div style="color: #c62828; font-size: 13px; margin-top: 5px;">❌ {{ $message }}</div>
                    @enderror
                </div>
                
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: bold;">الدور:</label>
                    <select name="role" required
                            style="width: 100%; padding: 12px; border: 2px solid #eee; border-radius: 8px; box-sizing: border-box;">
                        <option value="operator" {{ old('role') === 'operator' ? 'selected' : '' }}>🔧 مشغل</option>
                        <option value="manager" {{ old('role') === 'manager' ? 'selected' : '' }}>👔 مشرف</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>👑 مسؤول</option>
                    </select>
                </div>
                
                <div style="margin-bottom: 20px;">
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }}>
                        <span style="font-weight: bold;">الحساب نشط</span>
                    </label>
                </div>
                
                <div style="display: flex; gap: 10px;">
                    <button type="submit" style="flex: 1; background: #009639; color: white; padding: 12px; border: none; border-radius: 8px; font-weight: bold; cursor: pointer;">
                        💾 حفظ
                    </button>
                    <a href="{{ route('admin.users.index') }}" 
                       style="padding: 12px 20px; background: #666; color: white; border-radius: 8px; text-decoration: none; font-weight: bold;">
                        إلغاء
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
