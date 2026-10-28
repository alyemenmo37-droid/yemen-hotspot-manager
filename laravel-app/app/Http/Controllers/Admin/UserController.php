<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * قائمة المستخدمين
     */
    public function index()
    {
        $users = User::orderByDesc('created_at')->paginate(15);

        $stats = [
            'total' => User::count(),
            'admins' => User::where('role', 'admin')->count(),
            'active' => User::where('is_active', true)->count(),
            'inactive' => User::where('is_active', false)->count(),
        ];

        return view('admin.users.index', compact('users', 'stats'));
    }

    /**
     * نموذج إضافة مستخدم
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * حفظ مستخدم جديد
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', Password::min(8)->letters()->numbers()],
            'role' => ['required', 'in:admin,manager,operator'],
            'is_active' => ['boolean'],
        ], [
            'name.required' => 'الاسم مطلوب',
            'email.required' => 'البريد مطلوب',
            'email.email' => 'البريد غير صالح',
            'email.unique' => 'البريد مستخدم مسبقاً',
            'password.required' => 'كلمة المرور مطلوبة',
            'role.required' => 'الدور مطلوب',
            'role.in' => 'الدور غير صالح',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'is_active' => $request->boolean('is_active', true),
            'email_verified_at' => now(),
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', "تم إضافة المستخدم {$validated['name']}");
    }

    /**
     * عرض مستخدم
     */
    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    /**
     * نموذج تعديل
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * حفظ التعديل
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'unique:users,email,'.$user->id],
            'role' => ['required', 'in:admin,manager,operator'],
            'is_active' => ['boolean'],
            'password' => ['nullable', 'string', Password::min(8)->letters()->numbers()],
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'is_active' => $request->boolean('is_active'),
        ];

        // كلمة المرور اختيارية
        if (! empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        return redirect()
            ->route('admin.users.index')
            ->with('success', "تم تعديل المستخدم {$user->name}");
    }

    /**
     * حذف مستخدم
     */
    public function destroy(User $user)
    {
        // لا يمكن حذف نفسك
        if ($user->id === auth()->id()) {
            return back()->with('error', 'لا يمكنك حذف حسابك الخاص!');
        }

        // لا يمكن حذف آخر admin
        if ($user->isAdmin() && User::where('role', 'admin')->count() <= 1) {
            return back()->with('error', 'لا يمكن حذف آخر مسؤول!');
        }

        $name = $user->name;
        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', "تم حذف المستخدم $name");
    }

    /**
     * تفعيل/إيقاف
     */
    public function toggle(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'لا يمكنك إيقاف حسابك الخاص!');
        }

        $user->update(['is_active' => ! $user->is_active]);

        $status = $user->is_active ? 'تفعيل' : 'إيقاف';

        return back()->with('success', "تم $status المستخدم {$user->name}");
    }
}
