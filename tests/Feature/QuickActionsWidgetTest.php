<?php

namespace Tests\Feature;

use App\Filament\Widgets\QuickActionsWidget;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class QuickActionsWidgetTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_displays_the_primary_admin_actions(): void
    {
        app()->setLocale('km');

        Livewire::test(QuickActionsWidget::class)
            ->assertSee('សកម្មភាពរហ័ស')
            ->assertSee('អត្ថបទថ្មី')
            ->assertSeeHtml('fi-btn')
            ->assertSeeHtml('href="/admin/news-articles/create"')
            ->assertSeeHtml('href="/admin/projects/create"')
            ->assertSeeHtml('href="/admin/job-postings/create"')
            ->assertSeeHtml('href="/admin/send-newsletter"')
            ->assertSeeHtml('href="/admin/inquiries"')
            ->assertSeeHtml('href="/admin/analytics"');

        $this->assertSame('full', (new QuickActionsWidget)->getColumnSpan());
    }
}
