<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class PwaAssetPathsTest extends TestCase
{
    public function test_the_root_service_worker_rewrites_vite_build_paths(): void
    {
        $routes = File::get(base_path('routes/web.php'));
        $viteConfig = File::get(base_path('vite.config.js'));

        $this->assertStringContainsString("'./workbox-'", $routes);
        $this->assertStringContainsString("'/build/workbox-'", $routes);
        $this->assertStringContainsString("'url:\"assets/'", $routes);
        $this->assertStringContainsString("'url:\"/build/assets/'", $routes);
        $this->assertStringContainsString('modifyURLPrefix', $viteConfig);
        $this->assertStringContainsString("'': '/build/'", $viteConfig);
    }
}
