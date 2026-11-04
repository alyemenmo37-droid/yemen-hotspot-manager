<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ═══════════════════════════════════════
// Scheduled Tasks
// ═══════════════════════════════════════

// Backup يومياً (3 صباحاً)
Schedule::command('backup:database --compress --keep=7')
    ->dailyAt('03:00')
    ->withoutOverlapping();

// Sync Accounting كل 5 دقائق
Schedule::command('radius:sync-accounting')
    ->everyFiveMinutes()
    ->withoutOverlapping();

// Check Limits كل 5 دقائق
Schedule::command('radius:check-limits')
    ->everyFiveMinutes()
    ->withoutOverlapping();

// Health Check كل ساعة
Schedule::command('health:check')
    ->hourly()
    ->withoutOverlapping();
