<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * عرض الملف الشخصي
     */
    public function show()
    {
        $user = Auth::user();

        return view('profile.show', compact('user'));
    }

    /**
     * تحديث البيانات الأساسية
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email,'.$user->id],
        ], [
            'name.required' => 'الاسم مطلوب',
            'name.max' => 'الاسم 100 حرف كحد أقصى',
            'email.required' => 'البريد مطلوب',
            'email.email' => 'البريد غير صالح',
            'email.unique' => 'البريد مستخدم مسبقاً',
        ]);

        $user->update($validated);

        return back()->with('success', '✅ تم تحديث بياناتك بنجاح');
    }

    /**
     * تغيير كلمة المرور
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                Password::min(8)->letters()->numbers(),
            ],
        ], [
            'current_password.required' => 'كلمة المرور الحالية مطلوبة',
            'password.required' => 'كلمة المرور الجديدة مطلوبة',
            'password.min' => 'كلمة المرور 8 أحرف على الأقل',
            'password.confirmed' => 'كلمتا المرور غير متطابقتين',
        ]);

        // تحقق من كلمة المرور الحالية
        if (! Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors([
                'current_password' => '❌ كلمة المرور الحالية غير صحيحة',
            ]);
        }

        // تحديث كلمة المرور
        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', '🔐 تم تغيير كلمة المرور بنجاح');
    }
}
