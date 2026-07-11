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