<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * التحقق من دور واحد أو أكثر
     * 
     * الاستخدام: middleware('role:admin,manager')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        
        $user = auth()->user();
        
        if (!in_array($user->role, $roles, true)) {
            abort(403, 'غير مصرح لك بالوصول');
        }
        
        return $next($request);
    }
}
