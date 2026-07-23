<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class HomeServicesDesignTest extends TestCase
{
    public function test_the_home_services_show_all_service_titles_and_features_in_a_centered_odd_grid(): void
    {
        $servicesTemplate = File::get(resource_path('views/components/home/services.blade.php'));

        $this->assertStringContainsString("home_services_array_v2_'.app()->getLocale()", $servicesTemplate);
        $this->assertStringContainsString("->orderBy('orderIndex')->get()", $servicesTemplate);
        $this->assertStringNotContainsString("->orderBy('orderIndex')->limit(4)->get()", $servicesTemplate);
        $this->assertStringContainsString('relative overflow-hidden bg-[#f8fafc] py-20 md:py-28', $servicesTemplate);
        $this->assertStringContainsString('rounded-3xl border border-slate-200 bg-white/95 p-7', $servicesTemplate);
        $this->assertStringContainsString('count($services) % 2 === 1', $servicesTemplate);
        $this->assertStringContainsString('md:col-span-2 md:w-[calc(50%-0.5rem)] md:justify-self-center', $servicesTemplate);
        $this->assertStringContainsString('grid gap-2.5 border-t border-slate-200 pt-6 sm:grid-cols-2', $servicesTemplate);
        $this->assertStringContainsString('focus-visible:ring-2 focus-visible:ring-titan-red', $servicesTemplate);
        $this->assertStringContainsString('View :service service details', $servicesTemplate);
        $this->assertStringContainsString("str_pad(\$index + 1, 2, '0', STR_PAD_LEFT)", $servicesTemplate);
        $this->assertStringContainsString("\$f['name'] ?? \$f[\$lang] ?? \$f['en'] ?? ''", $servicesTemplate);
        $this->assertStringNotContainsString("\$s['desc']", $servicesTemplate);
    }
}
