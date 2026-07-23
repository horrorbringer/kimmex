<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class HomeProcessDesignTest extends TestCase
{
    public function test_the_home_process_uses_a_connected_step_card_design(): void
    {
        $processTemplate = File::get(resource_path('views/components/home/process.blade.php'));

        $this->assertStringContainsString('border-b border-titan-navy/10 pb-10', $processTemplate);
        $this->assertStringContainsString('lg:grid-cols-4', $processTemplate);
        $this->assertStringContainsString('h-1 w-10 bg-titan-red', $processTemplate);
        $this->assertStringContainsString('font-heading text-5xl font-black', $processTemplate);
        $this->assertStringContainsString('motion-reduce:transition-none', $processTemplate);
    }
}
