<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ═══════════════════════════════════════
// 1. Public Routes (بدون تسجيل دخول)
// ═══════════════════════════════════════
Route::get('/', function () {
    return auth()->check() 
        ? redirect()->route('dashboard')
        : redirect()->route('login');
})->name('home');

Route::get('/yhm', function () {
    return view('welcome');
})->name('yhm');

Route::get('/about', function () {
    return [
        'app' => 'YHM',
        'version' => '0.4',
        'framework' => app()->version(),
        'php' => phpversion(),
        'env' => app()->environment(),
        'time' => now()->toDateTimeString(),
        'timezone' => config('app.timezone'),
        'locale' => config('app.locale'),
    ];
})->name('about');

// ═══════════════════════════════════════
// 2. Auth Routes (guest only)
// ═══════════════════════════════════════
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// ═══════════════════════════════════════
// 3. Routes محمية (تحتاج تسجيل دخول)
// ═══════════════════════════════════════
Route::middleware('auth')->group(function () {
    
    // Profile
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [\App\Http\Controllers\ProfileController::class, 'show'])->name('show');
        Route::put('/update', [\App\Http\Controllers\ProfileController::class, 'update'])->name('update');
        Route::put('/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('password');
    });
    // Dashboard
    Route::get('/dashboard', function () {
        $stats = [
            'total' => \App\Models\Card::count(),
            'active' => \App\Models\Card::where('status', 'active')->count(),
            'unused' => \App\Models\Card::where('status', 'unused')->count(),
            'sales' => \App\Models\Card::where('status', '!=', 'unused')->sum('amount'),
        ];
        return view('dashboard', compact('stats'));
    })->name('dashboard');
    
    // Cards (Resource)
    Route::resource('cards', CardController::class);
    
    // تفعيل بطاقة
    Route::post('/cards/{card}/activate', [CardController::class, 'activate'])
        ->name('cards.activate');

    // Statistics
    Route::get('/statistics', [\App\Http\Controllers\StatisticsController::class, 'index'])
        ->name('statistics');
    
    // MikroTik
    Route::prefix('mikrotik')->name('mikrotik.')->group(function () {
        Route::get('/', function (\App\Services\MikroTikService $mikrotik) {
            $info = $mikrotik->getSystemInfo();
            $sessions = $mikrotik->getActiveSessions();
            return view('mikrotik.index', compact('info', 'sessions'));
        })->name('index');
        
        Route::post('/test', function (\App\Services\MikroTikService $mikrotik) {
            return response()->json($mikrotik->testConnection());
        })->name('test');
        
        Route::post('/disconnect/{card}', function ($card, \App\Services\MikroTikService $mikrotik) {
            $result = $mikrotik->disconnectUser($card);
            return back()->with($result['success'] ? 'success' : 'error', $result['message']);
        })->name('disconnect');
    });

    // ═══════════════════════════════════════
    // Admin Routes (admin only)
    // ═══════════════════════════════════════
    Route::middleware('admin')
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {
            
            // Admin Dashboard
            Route::get('/', function () {
                $stats = [
                    'total_cards' => \App\Models\Card::count(),
                    'total_users' => \App\Models\User::count(),
                    'total_sales' => \App\Models\Card::where('status', '!=', 'unused')->sum('amount'),
                    'total_categories' => \App\Models\Category::count(),
                ];
                return view('admin.dashboard', compact('stats'));
            })->name('dashboard');
            
            // Users Management (Full CRUD)
            Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
            
            // Toggle Active
            Route::post('/users/{user}/toggle', [\App\Http\Controllers\Admin\UserController::class, 'toggle'])
                ->name('users.toggle');
            
            // Settings
            Route::get('/settings', function () {
                return '<h1>الإعدادات</h1><p>قريباً...</p>';
            })->name('settings');
        });
});
