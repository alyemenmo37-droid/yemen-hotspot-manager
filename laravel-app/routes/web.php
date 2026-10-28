<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StatisticsController;
use App\Models\Card;
use App\Models\Category;
use App\Models\User;
use App\Services\MikroTikService;
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
        Route::get('/', [ProfileController::class, 'show'])->name('show');
        Route::put('/update', [ProfileController::class, 'update'])->name('update');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password');
    });
    // Dashboard
    Route::get('/dashboard', function () {
        $stats = [
            'total' => Card::count(),
            'active' => Card::where('status', 'active')->count(),
            'unused' => Card::where('status', 'unused')->count(),
            'sales' => Card::where('status', '!=', 'unused')->sum('amount'),
        ];

        return view('dashboard', compact('stats'));
    })->name('dashboard');

    // Cards (Resource)
    Route::resource('cards', CardController::class);

    // تفعيل بطاقة
    Route::post('/cards/{card}/activate', [CardController::class, 'activate'])
        ->name('cards.activate');

    // Statistics
    Route::get('/statistics', [StatisticsController::class, 'index'])
        ->name('statistics');

    // MikroTik
    Route::prefix('mikrotik')->name('mikrotik.')->group(function () {
        Route::get('/', function (MikroTikService $mikrotik) {
            $info = $mikrotik->getSystemInfo();
            $sessions = $mikrotik->getActiveSessions();

            return view('mikrotik.index', compact('info', 'sessions'));
        })->name('index');

        Route::post('/test', function (MikroTikService $mikrotik) {
            return response()->json($mikrotik->testConnection());
        })->name('test');

        Route::post('/disconnect/{card}', function ($card, MikroTikService $mikrotik) {
            $result = $mikrotik->disconnectUser($card);
            $key = $result['success'] ? 'success' : 'error';
            $message = $result['message'] ?? ($result['success'] ? 'تم الفصل' : 'فشل الفصل');

            return back()->with($key, $message);
        })->name('disconnect');

        Route::post('/connect/{card}', function ($card, MikroTikService $mikrotik) {
            $result = $mikrotik->connectUser($card);
            $key = $result['success'] ? 'success' : 'error';
            $message = $result['message'] ?? ($result['success'] ? 'تم الاتصال' : 'فشل الاتصال');

            return back()->with($key, $message);
        })->name('connect');

        Route::post('/sync', function (MikroTikService $mikrotik) {
            $cards = Card::all();
            $success = 0;

            foreach ($cards as $card) {
                $result = $mikrotik->addUser($card->number, $card->speed);
                if ($result['success']) {
                    $success++;
                }
            }

            return back()->with('success', "تم مزامنة $success بطاقة");
        })->name('sync');
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
                    'total_cards' => Card::count(),
                    'total_users' => User::count(),
                    'total_sales' => Card::where('status', '!=', 'unused')->sum('amount'),
                    'total_categories' => Category::count(),
                ];

                return view('admin.dashboard', compact('stats'));
            })->name('dashboard');

            // Users Management (Full CRUD)
            Route::resource('users', UserController::class);

            // Toggle Active
            Route::post('/users/{user}/toggle', [UserController::class, 'toggle'])
                ->name('users.toggle');

            // Settings
            Route::get('/settings', function () {
                return '<h1>الإعدادات</h1><p>قريباً...</p>';
            })->name('settings');
        });
});
