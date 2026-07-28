<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class ServiceValueDeliveredLayoutTest extends TestCase
{
    public function test_value_delivered_cards_are_centered_and_larger(): void
    {
        $template = File::get(resource_path('views/pages/services/show.blade.php'));

        $this->assertStringContainsString('max-w-6xl mx-auto py-12 md:py-16', $template);
        $this->assertStringContainsString('grid max-w-6xl grid-cols-1 gap-5 mx-auto md:grid-cols-2 md:gap-6 xl:grid-cols-4', $template);
        $this->assertStringContainsString('min-h-[230px] p-6 md:p-8 text-center rounded-xl', $template);
        $this->assertStringContainsString('w-12 h-12 bg-gray-50 border border-gray-100 rounded-full flex items-center justify-center mx-auto mb-7', $template);
        $this->assertStringContainsString('text-xl md:text-2xl', $template);
    }
}
