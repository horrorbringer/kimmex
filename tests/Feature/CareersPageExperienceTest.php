<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class CareersPageExperienceTest extends TestCase
{
    public function test_careers_page_prioritizes_open_roles_and_explains_the_application_flow(): void
    {
        $careersPage = File::get(resource_path('views/pages/careers.blade.php'));

        $this->assertStringContainsString('order-2 scroll-mt-24', $careersPage);
        $this->assertStringContainsString("__('Apply in 3 simple steps')", $careersPage);
        $this->assertStringContainsString("__('Choose a role')", $careersPage);
        $this->assertStringContainsString("__('Read the job details')", $careersPage);
        $this->assertStringContainsString("__('Send your CV')", $careersPage);
        $this->assertStringContainsString("__('View Job Details')", $careersPage);
        $this->assertStringContainsString("__('You only need your name, contact details, and CV.')", $careersPage);
        $this->assertStringNotContainsString('min-h-[292px]', $careersPage);
        $this->assertStringNotContainsString('min-h-10 text-sm', $careersPage);
        $this->assertStringContainsString("{{ __('Open') }}", $careersPage);
        $this->assertStringContainsString('group-hover:bg-titan-red', $careersPage);
        $this->assertStringContainsString('focus-visible:ring-titan-red', $careersPage);
        $this->assertStringContainsString("placeholder=\"{{ __('Search jobs') }}\"", $careersPage);
        $this->assertStringContainsString('<select x-model="filterDept"', $careersPage);
        $this->assertStringContainsString('<select x-model="filterLoc"', $careersPage);
        $this->assertStringContainsString('md:grid-cols-[minmax(0,1fr)_11rem_11rem_auto]', $careersPage);
        $this->assertStringNotContainsString('<!-- Department Tabs -->', $careersPage);
    }
}
