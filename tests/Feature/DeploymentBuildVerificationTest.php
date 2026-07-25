<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class DeploymentBuildVerificationTest extends TestCase
{
    public function test_deployment_requires_the_compiled_vite_stylesheet(): void
    {
        $deployHook = File::get(public_path('deploy-hook.php'));
        $workflow = File::get(base_path('.github/workflows/deploy.yml'));

        $this->assertStringContainsString("public_path('build/manifest.json')", $deployHook);
        $this->assertStringContainsString("['resources/css/app.css']['file']", $deployHook);
        $this->assertStringContainsString("public_path('build/'.\$cssAsset)", $deployHook);
        $this->assertStringContainsString('compiled Vite stylesheet is missing', $deployHook);
        $this->assertStringContainsString('npm ci && npm run build', $workflow);
        $this->assertStringContainsString('curl -fsS', $workflow);
    }
}
