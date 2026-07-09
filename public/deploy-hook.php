<?php
$expectedToken = require __DIR__.'/../deploy-token.php';
if (!hash_equals($expectedToken, $_GET['token'] ?? '')) {
    http_response_code(403);
    exit('forbidden');
}

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);

// Filament + Livewire published assets (icons, css, js)
Illuminate\Support\Facades\Artisan::call('filament:assets');
Illuminate\Support\Facades\Artisan::call('livewire:publish', ['--assets' => true]);

// Storage symlink — see note below, often fails on shared hosting
Illuminate\Support\Facades\Artisan::call('storage:link');

Illuminate\Support\Facades\Artisan::call('config:cache');
Illuminate\Support\Facades\Artisan::call('route:cache');
Illuminate\Support\Facades\Artisan::call('view:cache');

// Optional: speeds up Filament panel boot
Illuminate\Support\Facades\Artisan::call('icons:cache');

echo "deployed ok";