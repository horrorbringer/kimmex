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

Illuminate\Support\Facades\Artisan::call('config:clear');
Illuminate\Support\Facades\Artisan::call('view:clear');
Illuminate\Support\Facades\Artisan::call('cache:clear');
Illuminate\Support\Facades\Artisan::call('filament:clear-cached-components');
Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
Illuminate\Support\Facades\Artisan::call('filament:cache-components');
Illuminate\Support\Facades\Artisan::call('filament:assets');
Illuminate\Support\Facades\Artisan::call('config:cache');
Illuminate\Support\Facades\Artisan::call('route:cache');
Illuminate\Support\Facades\Artisan::call('view:cache');

if (function_exists('opcache_reset')) {
    opcache_reset();
}

echo "deployed ok";