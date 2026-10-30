<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * تسجيل الدخول
     * 
     * POST /api/login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:100'],
        ], [
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.email' => 'البريد الإلكتروني غير صالح',
            'password.required' => 'كلمة المرور مطلوبة',
        ]);
        
        $user = User::where('email', $request->email)->first();
        
        // تحقق من كلمة المرور
        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['البريد الإلكتروني أو كلمة المرور غير صحيحة'],
            ]);
        }
        
        // تحقق من الحساب
        if (!$user->isActive()) {
            throw ValidationException::withMessages([
                'email' => ['حسابك موقوف'],
            ]);
        }
        
        // حذف التوكنات القديمة (اختياري)
        $user->tokens()->delete();
        
        // إنشاء توكن جديد
        $deviceName = $request->device_name ?? 'API Client';
        $token = $user->createToken($deviceName, ['*'])->plainTextToken;
        
        // تحديث آخر دخول
        $user->update(['last_login_at' => now()]);
        
        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل الدخول',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
            'token' => $token,
            'token_type' => 'Bearer',
        ]);
    }
    
    /**
     * تسجيل الخروج
     * 
     * POST /api/logout
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل الخروج',
        ]);
    }
    
    /**
     * معلومات المستخدم الحالي
     * 
     * GET /api/me
     */
    public function me(Request $request)
    {
        $user = $request->user();
        
        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'is_active' => $user->is_active,
                'last_login_at' => $user->last_login_at,
            ],
        ]);
    }
}
