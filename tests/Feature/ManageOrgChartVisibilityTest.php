<?php

namespace Tests\Feature;

use App\Filament\Pages\ManageOrgChart;
use App\Models\Employee;
use App\Models\OrgUnit;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;
use Tests\TestCase;

class ManageOrgChartVisibilityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()->setLocale('en');
    }

    public function test_org_chart_is_visible_on_about_page_by_default(): void
    {
        $response = $this->get('/about');
        $response->assertStatus(200);
        $response->assertSee('Organization Structure');
    }

    public function test_org_chart_can_be_hidden_via_org_chart_visible_setting(): void
    {
        SystemSetting::set('organization_profile', [
            'org_chart_visible' => false,
            'org_chart_type' => 'dynamic',
        ]);
        Cache::forget('about_page_en');

        $response = $this->get('/about');
        $response->assertStatus(200);
        $response->assertDontSee('id="leadership"', false);
    }

    public function test_org_chart_can_be_hidden_via_none_chart_type(): void
    {
        SystemSetting::set('organization_profile', [
            'org_chart_visible' => true,
            'org_chart_type' => 'none',
        ]);
        Cache::forget('about_page_en');

        $response = $this->get('/about');
        $response->assertStatus(200);
        $response->assertDontSee('id="leadership"', false);
    }

    public function test_inactive_org_unit_is_hidden_from_public_about_page_but_present_in_admin(): void
    {
        $employee = Employee::create(['name' => 'Secret Consultant', 'role' => 'Consultant']);

        $activeUnit = OrgUnit::create([
            'title' => ['en' => 'Public Leader'],
            'type' => 'EXECUTIVE',
            'isActive' => true,
            'orderIndex' => 1,
        ]);

        $inactiveUnit = OrgUnit::create([
            'title' => ['en' => 'Hidden Leader'],
            'type' => 'STAFF',
            'employeeId' => $employee->id,
            'isActive' => false,
            'orderIndex' => 2,
        ]);

        Cache::forget('about_page_en');
        Cache::forget('about_orgchart_en');

        $response = $this->get('/about');
        $response->assertStatus(200);
        $response->assertSee('Public Leader');
        $response->assertDontSee('Secret Consultant');

        $admin = User::factory()->create(['role' => 'ADMIN']);
        $this->actingAs($admin);

        $component = Livewire::test(ManageOrgChart::class);
        $chartData = $component->get('chartData');

        $unitIds = collect($chartData)->pluck('id')->all();
        $this->assertContains($activeUnit->id, $unitIds);
        $this->assertContains($inactiveUnit->id, $unitIds);
    }

    public function test_admin_can_save_display_settings_in_manage_org_chart(): void
    {
        $admin = User::factory()->create(['role' => 'ADMIN']);
        $this->actingAs($admin);

        Livewire::test(ManageOrgChart::class)
            ->set('data.org_chart_visible', false)
            ->set('data.org_chart_type', 'none')
            ->call('saveDisplaySettings')
            ->assertHasNoFormErrors()
            ->assertNotified(__('Display Settings Saved'));

        $settings = SystemSetting::get('organization_profile');
        $this->assertFalse($settings['org_chart_visible']);
        $this->assertEquals('none', $settings['org_chart_type']);
    }
}
