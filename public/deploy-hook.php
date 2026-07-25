<?php

use Filament\Facades\Filament;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

$expectedToken = require __DIR__.'/../kimmex_app/deploy-token.php';

if (! hash_equals($expectedToken, $_GET['token'] ?? '')) {
    http_response_code(403);
    exit('forbidden');
}

require __DIR__.'/../kimmex_app/vendor/autoload.php';
$app = require_once __DIR__.'/../kimmex_app/bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$output = [];

// A stale Vite hot file makes @vite() point production visitors at a local
// development server instead of the compiled /build assets.
$viteHotFiles = array_unique([__DIR__.'/hot', public_path('hot')]);

foreach ($viteHotFiles as $viteHotFile) {
    if (is_file($viteHotFile) && ! unlink($viteHotFile)) {
        http_response_code(500);
        exit('deployment failed: unable to remove stale Vite hot file');
    }
}

$output[] = 'Vite hot file: cleared';
$manifestPath = public_path('build/manifest.json');

if (! is_readable($manifestPath)) {
    http_response_code(500);
    exit('deployment failed: Vite manifest is missing at public/build/manifest.json');
}

$manifest = json_decode((string) file_get_contents($manifestPath), true);
$cssAsset = $manifest['resources/css/app.css']['file'] ?? null;

if (! is_string($cssAsset) || ! is_readable(public_path('build/'.$cssAsset))) {
    http_response_code(500);
    exit('deployment failed: compiled Vite stylesheet is missing');
}

$output[] = "frontend build: verified ({$cssAsset})";
$run = function (string $command, array $params = []) use (&$output) {
    Artisan::call($command, $params);
    $result = trim(Artisan::output());
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
    Cache::forget('about_orgchart_en');
    Cache::forget('about_orgchart_km');
    Cache::forget('about_milestones_data_en');
    Cache::forget('about_milestones_data_km');
    Cache::forget('project_categories_active_en');
    Cache::forget('project_categories_active_km');
    Cache::forget('projects_all_active');
    $output[] = 'page caches: cleared';
} catch (Throwable $e) {
    $output[] = 'page caches: skip';
}

// 1c. Clear stale geo-IP cache so fresh lookups happen
try {
    // Remove cached geo results that returned null
    DB::table('cache')
        ->where('key', 'like', '%geo_ip_%')
        ->delete();
    $output[] = 'geo-ip cache: cleared';
} catch (Throwable $e) {
    $output[] = 'geo-ip cache: skip';
}

// 1b. Clear stale sessions (prevents incomplete object errors after updates)
try {
    DB::table('sessions')->truncate();
    $output[] = 'sessions: cleared';
} catch (Throwable $e) {
    $output[] = "sessions: skip ({$e->getMessage()})";
}

// 2. Clear Filament cache
try {
    $run('filament:optimize-clear');
} catch (Throwable $e) {
    $cachePath = base_path('bootstrap/cache/filament');
    if (is_dir($cachePath)) {
        array_map('unlink', glob("{$cachePath}/*.php"));
    }
    $output[] = 'filament:optimize-clear: manual cleanup';
}

// 3. Migrations
$run('migrate', ['--force' => true]);

// 4. Rebuild caches
$run('config:cache');
$run('route:cache');
$run('filament:assets');
$run('view:cache');

// 4b. Clear OPcache to ensure fresh PHP code
if (function_exists('opcache_reset')) {
    opcache_reset();
    $output[] = 'opcache: reset';
}

echo "deployed ok\n\n".implode("\n", $output);

// Quick check
echo "\n\n--- CHECK ---\n";
$providerPath = __DIR__.'/../kimmex_app/app/Providers/Filament/AdminPanelProvider.php';
echo 'Explicit SubscriberResource in provider: '.(str_contains(file_get_contents($providerPath), 'SubscriberResource::class') ? 'YES' : 'NO — push to master needed')."\n";
$panel = Filament::getPanel('admin');
echo 'Total resources: '.count($panel->getResources())."\n";
