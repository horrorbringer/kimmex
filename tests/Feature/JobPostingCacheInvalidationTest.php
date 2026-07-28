<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class JobPostingCacheInvalidationTest extends TestCase
{
    public function test_saving_a_job_clears_its_public_detail_cache_for_every_locale(): void
    {
        $model = File::get(app_path('Models/JobPosting.php'));

        $this->assertStringContainsString('static::saved(function (self $job): void {', $model);
        $this->assertStringContainsString("static::clearPublicCaches(\$job, \$job->getPrevious('slug'))", $model);
        $this->assertStringContainsString('Cache::forget("career_job_show_data_{$slug}_{$locale}")', $model);
        $this->assertStringContainsString("foreach (['en', 'km', 'kh'] as \$locale)", $model);
    }
}
