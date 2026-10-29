<?php

use Illuminate\Support\Facades\Schedule;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// فحص الجلسات كل 5 دقائق
Schedule::command('radius:check-limits')
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->onSuccess(function () {
        \Log::info('RADIUS check completed');
    });

// مزامنة الاستهلاك كل 5 دقائق
Schedule::command('radius:sync-accounting')
    ->everyFiveMinutes()
    ->withoutOverlapping();

// نسخ احتياطي يومي (3 صباحاً)
Schedule::command('backup:database --compress --keep=7')
    ->dailyAt('03:00')
    ->withoutOverlapping();

// فحص صحة كل ساعة
Schedule::command('health:check')
    ->hourly()
    ->withoutOverlapping();
