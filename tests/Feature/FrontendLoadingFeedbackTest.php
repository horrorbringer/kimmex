<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class FrontendLoadingFeedbackTest extends TestCase
{
    public function test_the_frontend_includes_loading_feedback_for_navigation_and_images(): void
    {
        $layout = File::get(resource_path('views/components/layouts/app.blade.php'));
        $scripts = File::get(resource_path('js/app.js'));
        $styles = File::get(resource_path('css/app.css'));

        $this->assertStringContainsString('id="page-loading-bar"', $layout);
        $this->assertStringContainsString('document.documentElement.dataset.pageLoading', $scripts);
        $this->assertStringContainsString("event.target.closest('a[href]')", $scripts);
        $this->assertStringContainsString("window.addEventListener('pageshow'", $scripts);
        $this->assertStringContainsString("image.dataset.imageLoaded = 'true'", $layout);
        $this->assertStringNotContainsString('new IntersectionObserver', $layout);
        $this->assertStringContainsString('#page-loading-bar', $styles);
        $this->assertStringContainsString("html[data-page-loading='true'] #page-loading-bar", $styles);
        $this->assertStringContainsString("img[data-image-loaded='true']", $styles);
    }
}
