<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CardController;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ═══════════════════════════════════════
// 1. الصفحة الرئيسية
// ═══════════════════════════════════════
Route::get('/', function () {
    return view('welcome');
})->name('home');


// ═══════════════════════════════════════
// 2. صفحة YHM الترحيبية
// ═══════════════════════════════════════
Route::get('/yhm', function () {
    return '
        <!DOCTYPE html>
        <html lang="ar" dir="rtl">
        <head>
            <meta charset="UTF-8">
            <title>YHM</title>
            <style>
                body {
                    font-family: Arial;
                    background: linear-gradient(135deg, #009639, #007a2e);
                    min-height: 100vh;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    margin: 0;
                    color: white;
                    text-align: center;
                }
                h1 { font-size: 48px; }
                p { font-size: 20px; margin: 20px 0; }
                .badge {
                    background: white;
                    color: #009639;
                    padding: 10px 20px;
                    border-radius: 20px;
                    display: inline-block;
                    margin: 10px;
                    font-weight: bold;
                }
            </style>
        </head>
        <body>
            <div>
                <h1>📡 Yemen Hotspot Manager</h1>
                <p>الإصدار: v0.3 - Laravel Edition</p>
                <div>
                    <span class="badge">✅ Laravel ' . app()->version() . '</span>
                    <span class="badge">✅ PHP ' . PHP_VERSION . '</span>
                    <span class="badge">✅ MySQL</span>
                </div>
                <p style="margin-top: 40px;">
                    <small>صُنع بحب في اليمن 🇾🇪</small>
                </p>
                <p style="margin-top: 20px;">
                    <a href="/cards" style="color: white; text-decoration: underline;">← لوحة البطاقات</a>
                </p>
            </div>
        </body>
        </html>
    ';
})->name('yhm');


// ═══════════════════════════════════════
// 3. Routes البطاقات (Resource)
// ═══════════════════════════════════════
Route::resource('cards', CardController::class);

/*
 * هذا الأمر يُنشئ تلقائياً 7 routes:
 * 
 * GET    /cards              → index   (عرض الكل)
 * GET    /cards/create       → create  (نموذج إضافة)
 * POST   /cards              → store   (حفظ)
 * GET    /cards/{id}         → show    (عرض واحد)
 * GET    /cards/{id}/edit    → edit    (نموذج تعديل)
 * PUT    /cards/{id}         → update  (حفظ التعديل)
 * DELETE /cards/{id}         → destroy (حذف)
 */


// ═══════════════════════════════════════
// 4. Route مع parameter
// ═══════════════════════════════════════
Route::get('/hello/{name}', function (string $name) {
    return "مرحباً يا $name!";
})->name('hello');


// ═══════════════════════════════════════
// 5. Route مع constraint (تقييد)
// ═══════════════════════════════════════
Route::get('/card/{number}', function (string $number) {
    return "البطاقة رقم: $number";
})->where('number', '[0-9]{10}')->name('card.lookup');


// ═══════════════════════════════════════
// 6. Route Groups
// ═══════════════════════════════════════
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return 'لوحة الإدارة';
    })->name('dashboard');
    
    Route::get('/users', function () {
        return 'إدارة المستخدمين';
    })->name('users');
});


// ═══════════════════════════════════════
// 7. Route مع معلومات
// ═══════════════════════════════════════
Route::get('/about', function () {
    return [
        'app' => 'YHM',
        'version' => '0.3',
        'framework' => app()->version(),
        'php' => phpversion(),
        'env' => app()->environment(),
        'time' => now()->toDateTimeString(),
        'timezone' => config('app.timezone'),
        'locale' => config('app.locale'),
    ];
})->name('about');
