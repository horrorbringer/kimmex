<?php

namespace Tests\Feature;

use App\Jobs\AutoTranslateModel;
use App\Models\Service;
use App\Models\SystemSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ServiceFeatureAutoTranslationTest extends TestCase
{
    use RefreshDatabase;

    public function test_creating_a_service_queues_feature_translation_with_its_other_translatable_fields(): void
    {
        Queue::fake();
        SystemSetting::set('ai_settings', ['auto_translate' => true]);

        Service::create([
            'title' => ['en' => 'Design and Build'],
            'slug' => 'design-and-build',
            'summary' => ['en' => 'A complete construction service.'],
            'description' => ['en' => '<p>Detailed service description.</p>'],
            'features' => ['en' => [['name' => 'Architectural planning']]],
            'isActive' => true,
        ]);

        Queue::assertPushed(AutoTranslateModel::class, function (AutoTranslateModel $job): bool {
            return in_array('title', $job->fields, true)
                && in_array('summary', $job->fields, true)
                && in_array('description', $job->fields, true)
                && in_array('features', $job->fields, true);
        });
    }
}
