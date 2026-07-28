<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class OrgChartD3IntegrationTest extends TestCase
{
    public function test_admin_org_chart_uses_the_lazy_d3_canvas_and_existing_filament_edit_action(): void
    {
        $package = json_decode(File::get(base_path('package.json')), true, flags: JSON_THROW_ON_ERROR);
        $script = File::get(resource_path('js/admin-org-chart.js'));
        $view = File::get(resource_path('views/filament/pages/manage-org-chart.blade.php'));
        $page = File::get(app_path('Filament/Pages/ManageOrgChart.php'));

        $this->assertArrayHasKey('d3-org-chart', $package['dependencies']);
        $this->assertStringContainsString("import { OrgChart } from 'd3-org-chart'", $script);
        $this->assertStringContainsString("new CustomEvent('org-chart:edit'", $script);
        $this->assertStringContainsString('chart.exportImg', $script);
        $this->assertStringContainsString('data-org-chart-canvas wire:ignore', $view);
        $this->assertStringContainsString("x-on:org-chart:edit.window=\"\$wire.mountAction('edit', \$event.detail)\"", $view);
        $this->assertStringContainsString("@vite('resources/js/admin-org-chart.js')", $view);
        $this->assertStringContainsString("\$this->dispatch('chartUpdated', chartData: \$this->chartData)", $page);
    }
}
