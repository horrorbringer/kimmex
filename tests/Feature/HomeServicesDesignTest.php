<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class HomeServicesDesignTest extends TestCase
{
    public function test_the_home_services_use_a_premium_capability_grid(): void
    {
        $servicesTemplate = File::get(resource_path('views/components/home/services.blade.php'));

        $this->assertStringContainsString('Integrated expertise for every stage of your project', $servicesTemplate);
        $this->assertStringContainsString('rounded-2xl border border-titan-navy/10 bg-[#F7F9FC] p-6', $servicesTemplate);
        $this->assertStringContainsString('absolute left-0 top-0 h-full w-1', $servicesTemplate);
        $this->assertStringContainsString('font-heading text-5xl font-black', $servicesTemplate);
        $this->assertStringContainsString('bg-white px-3 py-1 text-[11px]', $servicesTemplate);
    }
}
