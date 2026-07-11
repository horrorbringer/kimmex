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

// 1. Clear ALL caches first
$run('config:clear');
$run('route:clear');
$run('view:clear');
$run('cache:clear');
$run('event:clear');

// 2. Clear Filament component cache (delete cached manifest file directly as fallback)
try {
    $run('filament:optimize-clear');
} catch (\Throwable $e) {
    // Fallback: manually delete the cached components file
    $cachePath = base_path('bootstrap/cache/filament');
    if (is_dir($cachePath)) {
        array_map('unlink', glob("{$cachePath}/*.php"));
        $output[] = "filament:optimize-clear: manual cleanup done";
    }
}

// 3. Run migrations
$run('migrate', ['--force' => true]);

// 4. Rebuild caches
$run('filament:cache-components');
$run('filament:assets');
$run('config:cache');
$run('route:cache');
$run('view:cache');

// 5. Reset OPcache
if (function_exists('opcache_reset')) {
    opcache_reset();
    $output[] = "opcache: reset";
}

echo "deployed ok\n\n" . implode("\n", $output);

// Debug: verify Subscribers resource file exists
echo "\n\n--- FILE CHECK ---\n";
$subscriberPath = __DIR__ . '/../kimmex_app/app/Filament/Resources/Subscribers/SubscriberResource.php';
echo "SubscriberResource.php: " . (file_exists($subscriberPath) ? 'EXISTS (' . filesize($subscriberPath) . ' bytes)' : 'MISSING') . "\n";
$subscriberModel = __DIR__ . '/../kimmex_app/app/Models/Subscriber.php';
echo "Subscriber.php model: " . (file_exists($subscriberModel) ? 'EXISTS' : 'MISSING') . "\n";

// Try to load the class and catch the actual error
echo "\n--- CLASS LOAD TEST ---\n";
try {
    $class = \App\Filament\Resources\Subscribers\SubscriberResource::class;
    echo "Class loadable: YES\n";
    echo "Extends Resource: " . (is_subclass_of($class, \Filament\Resources\Resource::class) ? 'YES' : 'NO') . "\n";
    echo "Model: " . ($class::getModel() ?? 'null') . "\n";
    echo "Nav group: " . ($class::getNavigationGroup() ?? 'null') . "\n";
    echo "Nav label: " . ($class::getNavigationLabel() ?? 'null') . "\n";
} catch (\Throwable $e) {
    echo "Class load ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

// Check if Filament discovers it
echo "\n--- FILAMENT DISCOVERY ---\n";
try {
    $panel = \Filament\Facades\Filament::getPanel('admin');
    $resources = $panel->getResources();
    $found = array_filter($resources, fn($r) => str_contains($r, 'Subscriber'));
    echo "Filament discovers it: " . (!empty($found) ? 'YES → ' . implode(', ', $found) : 'NO') . "\n";
    echo "Total resources: " . count($resources) . "\n";
} catch (\Throwable $e) {
    echo "Filament check error: " . $e->getMessage() . "\n";
}