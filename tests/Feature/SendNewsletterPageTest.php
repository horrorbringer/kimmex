<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class SendNewsletterPageTest extends TestCase
{
    public function test_the_newsletter_page_groups_the_sending_workflow_into_sections(): void
    {
        $page = File::get(app_path('Filament/Pages/SendNewsletter.php'));

        $this->assertStringContainsString("Section::make(__('Newsletter'))", $page);
        $this->assertStringContainsString("Section::make(__('Subscribers'))", $page);
        $this->assertStringContainsString("Section::make(__('A/B Test'))", $page);
        $this->assertStringContainsString('Grid::make(2)', $page);
        $this->assertStringContainsString('->columns(1)', $page);
    }
}
