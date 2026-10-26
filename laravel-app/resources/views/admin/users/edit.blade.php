@extends('layouts.app')

@section('title', "تعديل {$user->name}")

@section('content')
    <div style="max-width: 600px; margin: 0 auto;">
        <h1 style="color: #f57c00; margin-bottom: 20px;">✏️ تعديل المستخدم</h1>
        
        <div style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
            <form method="POST" action="{{ route('admin.users.update', $user) }}">
                @csrf
                @method('PUT')
                
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: bold;">الاسم:</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                           style="width: 100%; padding: 12px; border: 2px solid #eee; border-radius: 8px; box-sizing: border-box;">
                </div>
                
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: bold;">البريد:</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                           style="width: 100%; padding: 12px; border: 2px solid #eee; border-radius: 8px; box-sizing: border-box;">
                </div>
                
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: bold;">كلمة المرور الجديدة (اختياري):</label>
                    <input type="password" name="password"
                           style="width: 100%; padding: 12px; border: 2px solid #eee; border-radius: 8px; box-sizing: border-box;">
                    <small style="color: #666; font-size: 12px;">اتركه فارغاً لعدم التغيير</small>
                </div>
                
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: bold;">الدور:</label>
                    <select name="role" required
                            style="width: 100%; padding: 12px; border: 2px solid #eee; border-radius: 8px; box-sizing: border-box;">
                        <option value="operator" {{ $user->role === 'operator' ? 'selected' : '' }}>🔧 مشغل</option>
                        <option value="manager" {{ $user->role === 'manager' ? 'selected' : '' }}>👔 مشرف</option>
                        <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>👑 مسؤول</option>
                    </select>
                </div>
                
                <div style="margin-bottom: 20px;">
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="checkbox" name="is_active" value="1" {{ $user->is_active ? 'checked' : '' }}>
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
