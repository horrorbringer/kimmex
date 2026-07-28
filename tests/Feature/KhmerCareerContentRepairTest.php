<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class KhmerCareerContentRepairTest extends TestCase
{
    public function test_senior_c_developer_khmer_requirements_repair_targets_only_that_job(): void
    {
        $migration = File::get(database_path('migrations/2026_07_28_234324_fix_senior_c_developer_khmer_requirements.php'));

        $this->assertStringContainsString("->where('slug', 'senior-c-developer')", $migration);
        $this->assertStringContainsString("'requirements->km'", $migration);
        $this->assertStringContainsString('ASP.NET MVC, Entity Framework', $migration);
        $this->assertStringContainsString('MS SQL Server', $migration);
    }
}
