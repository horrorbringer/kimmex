<?php
$expectedToken = require __DIR__.'/../kimmex_app/deploy-token.php';

if (!hash_equals($expectedToken, $_GET['token'] ?? '')) {
    http_response_code(403);
    exit('forbidden');
}

require __DIR__.'/../kimmex_app/vendor/autoload.php';
$app = require_once __DIR__.'/../kimmex_app/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$output = [];
$run = function (string $command, array $params = []) use (&$output) {
    Illuminate\Support\Facades\Artisan::call($command, $params);
    $result = trim(Illuminate\Support\Facades\Artisan::output());
    $output[] = "{$command}: {$result}";
};

// 1. Clear ALL caches
$run('config:clear');
$run('route:clear');
$run('view:clear');
$run('cache:clear');
$run('event:clear');

// 1a. Clear specific caches that may hold stale data
try {
    \Illuminate\Support\Facades\Cache::forget('about_orgchart_en');
    \Illuminate\Support\Facades\Cache::forget('about_orgchart_km');
    \Illuminate\Support\Facades\Cache::forget('about_milestones_data_en');
    \Illuminate\Support\Facades\Cache::forget('about_milestones_data_km');
    $output[] = "page caches: cleared";
} catch (\Throwable $e) {
    $output[] = "page caches: skip";
}

// 1c. Backfill NULL country in page_views (quick, no API calls)
try {
    $updated = \Illuminate\Support\Facades\DB::table('page_views')
        ->whereNull('country')
        ->update(['country' => 'Cambodia']);
    $output[] = "pageviews country backfill: {$updated} rows set to Cambodia";
} catch (\Throwable $e) {
    $output[] = "pageviews country backfill: skip ({$e->getMessage()})";
}

// 1b. Clear stale sessions (prevents incomplete object errors after updates)
try {
    \Illuminate\Support\Facades\DB::table('sessions')->truncate();
    $output[] = "sessions: cleared";
} catch (\Throwable $e) {
    $output[] = "sessions: skip ({$e->getMessage()})";
}

// 2. Clear Filament cache
try {
    $run('filament:optimize-clear');
} catch (\Throwable $e) {
    $cachePath = base_path('bootstrap/cache/filament');
    if (is_dir($cachePath)) {
        array_map('unlink', glob("{$cachePath}/*.php"));
    }
    $output[] = "filament:optimize-clear: manual cleanup";
}

// 3. Migrations
$run('migrate', ['--force' => true]);

// 4. Rebuild caches
$run('config:cache');
$run('filament:assets');
$run('view:cache');

// 4b. Clear OPcache to ensure fresh PHP code
if (function_exists('opcache_reset')) {
    opcache_reset();
    $output[] = "opcache: reset";
}

echo "deployed ok\n\n" . implode("\n", $output);

// Quick check
echo "\n\n--- CHECK ---\n";
$providerPath = __DIR__ . '/../kimmex_app/app/Providers/Filament/AdminPanelProvider.php';
echo "Explicit SubscriberResource in provider: " . (str_contains(file_get_contents($providerPath), 'SubscriberResource::class') ? 'YES' : 'NO — push to master needed') . "\n";
$panel = \Filament\Facades\Filament::getPanel('admin');
echo "Total resources: " . count($panel->getResources()) . "\n";
