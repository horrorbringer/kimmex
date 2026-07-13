<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(\Illuminate\Foundation\Inspiring::quote());
})->purpose('Display an inspiring quote');

// Process queued jobs every minute (for cPanel servers without a persistent queue worker)
Schedule::command('queue:work --stop-when-empty --tries=3 --timeout=60')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground();

// Regenerate sitemap daily (also triggered on model save via CacheBusterObserver)
Schedule::command('sitemap:generate')
    ->daily()
    ->withoutOverlapping();

// Prune old page view analytics (keep 90 days)
Schedule::command('analytics:prune --days=90')
    ->weekly()
    ->withoutOverlapping();

// Backfill country data for page views (resolve IPs → country)
Schedule::command('pageviews:backfill-country --limit=50')
    ->hourly()
    ->withoutOverlapping();

// Send weekly digest email every Monday at 8:00 AM
Schedule::command('digest:send')
    ->weeklyOn(1, '8:00')
    ->withoutOverlapping();

// Uptime ping — check /health every 5 minutes and alert on failure
Schedule::command('uptime:check')
    ->everyFiveMinutes()
    ->withoutOverlapping();

// Rotate Laravel log — keep it under control
Schedule::call(function () {
    $logFile = storage_path('logs/laravel.log');
    if (file_exists($logFile) && filesize($logFile) > 5 * 1024 * 1024) { // > 5 MB
        $archive = storage_path('logs/laravel-' . now()->format('Y-m-d') . '.log');
        rename($logFile, $archive);
        file_put_contents($logFile, '');

        // Keep only last 5 archived logs
        $archives = glob(storage_path('logs/laravel-*.log'));
        rsort($archives);
        foreach (array_slice($archives, 5) as $old) {
            unlink($old);
        }
    }
})->daily()->name('log-rotation');

