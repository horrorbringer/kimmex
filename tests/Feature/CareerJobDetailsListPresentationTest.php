<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class CareerJobDetailsListPresentationTest extends TestCase
{
    public function test_job_details_use_a_compact_label_and_value_list(): void
    {
        $careerDetailsPage = File::get(resource_path('views/pages/careers/show.blade.php'));
        $careersPage = File::get(resource_path('views/pages/careers.blade.php'));

        $this->assertStringContainsString('divide-y divide-gray-100', $careerDetailsPage);
        $this->assertStringContainsString('flex items-center justify-between gap-4 py-3', $careerDetailsPage);
        $this->assertStringContainsString('max-w-[60%] text-right text-sm font-semibold', $careerDetailsPage);
        $this->assertStringContainsString("{{ __('Apply for this role') }}", $careerDetailsPage);
        $this->assertStringContainsString("{{ __('This role is currently open.') }}", $careerDetailsPage);
        $this->assertStringContainsString('{{ $postedRelative }}', $careerDetailsPage);
        $this->assertStringContainsString('relativeTime(date)', $careersPage);
        $this->assertStringContainsString('x-text="relativeTime(job.postedAt || job.postedDate)"', $careersPage);
    }
}
