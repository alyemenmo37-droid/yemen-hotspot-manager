<?php

use Illuminate\Support\Facades\Route;

// الصفحة الرئيسية
Route::get('/', function () {
    return view('welcome');
});

// صفحة YHM
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
                    <span class="badge">✅ Laravel 11</span>
                    <span class="badge">✅ PHP 8.5</span>
                    <span class="badge">✅ MySQL</span>
                </div>
                <p style="margin-top: 40px;">
                    <small>صُنع بحب في اليمن 🇾🇪</small>
                </p>
            </div>
        </body>
        </html>
    ';
});

// صفحة معلومات
Route::get('/about', function () {
    return [
        'app' => 'YHM',
        'version' => '0.3',
        'framework' => app()->version(),
        'php' => phpversion(),
        'env' => app()->environment(),
        'time' => now()->toDateTimeString(),
    ];
});

// صفحة مع اسم
Route::get('/hello/{name}', function (string $name) {
    return "مرحباً يا $name!";
});
