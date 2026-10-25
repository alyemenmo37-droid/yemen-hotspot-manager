<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * عرض صفحة تسجيل الدخول
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }
    
    /**
     * معالجة تسجيل الدخول
     */
    public function login(Request $request)
    {
        // 1. التحقق من المدخلات
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:6'],
        ], [
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.email' => 'البريد الإلكتروني غير صالح',
            'password.required' => 'كلمة المرور مطلوبة',
            'password.min' => 'كلمة المرور 6 أحرف على الأقل',
        ]);
        
        // 2. محاولة تسجيل الدخول
        $remember = $request->boolean('remember');
        
        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            
            // تحديث آخر تسجيل دخول
            $user = Auth::user();
            $user->update(['last_login_at' => now()]);
            
            return redirect()
                ->intended(route('dashboard'))
                ->with('success', "مرحباً بك {$user->name} 👋");
        }
        
        // 3. فشل
        return back()
            ->withInput($request->only('email', 'remember'))
            ->withErrors([
                'email' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة',
            ]);
    }
    
    /**
     * تسجيل الخروج
     */
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()
            ->route('login')
            ->with('success', 'تم تسجيل الخروج بنجاح');
    }
}
