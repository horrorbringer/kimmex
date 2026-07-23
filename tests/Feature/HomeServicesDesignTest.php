<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class HomeServicesDesignTest extends TestCase
{
    public function test_the_home_services_show_all_service_titles_and_features_in_a_centered_odd_grid(): void
    {
        $servicesTemplate = File::get(resource_path('views/components/home/services.blade.php'));

        $this->assertStringContainsString("->orderBy('orderIndex')->get()", $servicesTemplate);
        $this->assertStringNotContainsString("->orderBy('orderIndex')->limit(4)->get()", $servicesTemplate);
        $this->assertStringContainsString('rounded-2xl border border-titan-navy/10 bg-[#F7F9FC] p-6', $servicesTemplate);
        $this->assertStringContainsString('count($services) % 2 === 1', $servicesTemplate);
        $this->assertStringContainsString('md:col-span-2 md:w-[calc(50%-0.5rem)] md:justify-self-center', $servicesTemplate);
        $this->assertStringContainsString('grid gap-3 border-t border-titan-navy/8 pt-5 sm:grid-cols-2', $servicesTemplate);
        $this->assertStringNotContainsString("\$s['desc']", $servicesTemplate);
    }
}
