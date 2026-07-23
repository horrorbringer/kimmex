<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class ServiceDetailsDesignTest extends TestCase
{
    public function test_service_details_uses_a_polished_responsive_and_accessibile_layout(): void
    {
        $template = File::get(resource_path('views/pages/services/show.blade.php'));

        $this->assertStringContainsString('max-w-[1280px]', $template);
        $this->assertStringContainsString('rounded-2xl border border-slate-200 bg-white p-6', $template);
        $this->assertStringContainsString('grid gap-4 sm:grid-cols-2 lg:grid-cols-3', $template);
        $this->assertStringContainsString('focus-visible:ring-2 focus-visible:ring-titan-red', $template);
        $this->assertStringContainsString('bg-titan-navy p-8', $template);
    }
}
