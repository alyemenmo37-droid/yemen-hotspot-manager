<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminOnly
{
    /**
     * التحقق من أن المستخدم مسؤول
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. تأكد من تسجيل الدخول
        if (! auth()->check()) {
            return redirect()->route('login')
                ->with('error', 'يجب تسجيل الدخول أولاً');
        }

        // 2. تأكد من الدور
        $user = auth()->user();

        if (! $user->isAdmin()) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة. هذه الصفحة للمسؤولين فقط.');
        }

        // 3. تأكد من أن الحساب نشط
        if (! $user->isActive()) {
            auth()->logout();

            return redirect()->route('login')
                ->with('error', 'حسابك موقوف. تواصل مع المسؤول.');
        }

        return $next($request);
    }
}
