<?php

namespace Tests\Feature;

use App\Enums\ProjectStatus;
use App\Models\Project;
use App\Models\ProjectCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class AboutProjectJourneyLineChartTest extends TestCase
{
    use RefreshDatabase;

    public function test_about_page_renders_project_journey_line_chart_and_stats(): void
    {
        Queue::fake();

        $category = ProjectCategory::create([
            'name' => ['en' => 'Commercial High-Rise', 'km' => 'អគារពាណិជ្ជកម្ម'],
            'slug' => 'commercial-high-rise',
        ]);

        Project::create([
            'title' => ['en' => 'Vattanac Tower Subcontract', 'km' => 'គម្រោងវឌ្ឍនៈ'],
            'slug' => 'vattanac-tower-subcontract',
            'client' => 'Vattanac Properties',
            'location' => ['en' => 'Phnom Penh', 'km' => 'ភ្នំពេញ'],
            'completionDate' => '2024-05-15',
            'status' => ProjectStatus::COMPLETED,
            'project_category_id' => $category->id,
            'isActive' => true,
        ]);

        $response = $this->get('/about?lang=en');

        $response->assertStatus(200);
        $response->assertSee('OUR JOURNEY');
        $response->assertSee('Company Milestones');
        $response->assertSee('kimmexCanvasJsChart');
    }

    public function test_about_page_renders_in_khmer_locale_cleanly(): void
    {
        $response = $this->get('/about?lang=km');

        $response->assertStatus(200);
    }
}
