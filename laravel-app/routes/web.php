<?php

use App\Http\Controllers\Admin\TenantController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\SubscriptionWebController;
use App\Models\Card;
use App\Models\Category;
use App\Models\User;
use App\Services\MikroTikService;
use App\Services\Radius\RadiusService;
use App\Services\TelegramService;
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
    Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:5,1')->name('login.post');
});
Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Health Check (public, minimal)
Route::get('/health', function () {
    try {
        DB::select('SELECT 1');
        Cache::put('health_ping', 'ok', 60);

        return response()->json([
            'status' => 'ok',
            'time' => now()->toDateTimeString(),
            'version' => '1.0.0',
            'app' => 'YHM',
        ]);
    } catch (Throwable $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'service unavailable',
        ], 503);
    }
})->name('health');

// ═══════════════════════════════════════
// 3. Routes محمية (تحتاج تسجيل دخول)
// ═══════════════════════════════════════
Route::middleware('auth')->group(function () {

    // Route::resource('subscriptions', \App\Http\Controllers\Api\SubscriptionWebController::class)->except(['show']);
    Route::resource('subscriptions', SubscriptionWebController::class)->except(['show']);
    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/subscriptions/pdf', [ReportController::class, 'subscriptionsPdf'])->name('subscriptions.pdf');
        Route::get('/cards/pdf', [ReportController::class, 'cardsPdf'])->name('cards.pdf');
        Route::get('/cards/csv', [ReportController::class, 'cardsCsv'])->name('cards.csv');
        Route::get('/sales/pdf', [ReportController::class, 'salesPdf'])->name('sales.pdf');
        Route::get('/usage/pdf', [ReportController::class, 'usagePdf'])->name('usage.pdf');
        Route::get('/usage/csv', [ReportController::class, 'usageCsv'])->name('usage.csv');
    });

    // Sync Accounting
    Route::post('/radius/sync-accounting', function (RadiusService $radius) {
        $stats = $radius->syncDataUsage();

        return back()->with('success',
            "تم تحديث {$stats['updated']} بطاقة، تجاوز {$stats['exceeded']}"
        );
    })->name('radius.sync-accounting');

    // Accounting Reports
    Route::get('/accounting', function (RadiusService $radius) {
        $overall = $radius->getOverallStats();
        $topConsumers = $radius->getTopConsumers(20);

        return view('accounting.index', compact('overall', 'topConsumers'));
    })->name('accounting.index');

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

    // Bulk Actions
    Route::post('/cards/bulk-action', [CardController::class, 'bulkAction'])
        ->name('cards.bulk-action');
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

    // Test Notifications (Admin only)
    Route::prefix('admin/notifications')->name('admin.notifications.')->middleware('admin')->group(function () {
        Route::post('/test', function (TelegramService $telegram) {
            $result = $telegram->test();

            return back()->with($result['success'] ? 'success' : 'error', $result['message']);
        })->name('test');

        Route::post('/test-low-stock', function (TelegramService $telegram) {
            $telegram->notifyLowStock(5, 10);

            return back()->with('success', 'تم إرسال تنبيه المخزون');
        })->name('low-stock');
    });

    // ═══════════════════════════════════════
    // Admin Routes (admin only)
    // ═══════════════════════════════════════
    Route::middleware('admin')
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {

            Route::resource('tenants', TenantController::class);
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
