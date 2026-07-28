<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class ProjectNavigationFilterTest extends TestCase
{
    public function test_project_navigation_uses_category_slugs_and_activates_the_matching_filter(): void
    {
        $header = File::get(resource_path('views/components/header.blade.php'));
        $controller = File::get(app_path('Http/Controllers/ProjectController.php'));

        $this->assertStringContainsString("'nav_categories_v2_' . \$lang", $header);
        $this->assertSame(4, substr_count($header, "category={{ urlencode(\$navCat['slug']) }}"));
        $this->assertStringNotContainsString('category_id={{', $header);
        $this->assertStringNotContainsString('status=in-progress', $header);
        $this->assertStringContainsString('status=ongoing', $header);
        $this->assertStringContainsString("\$categoryId = \$request->query('category_id');", $controller);
        $this->assertStringContainsString("\$categorySlug = \$request->string('category')->trim()->toString();", $controller);
        $this->assertStringContainsString("firstWhere('slug', \$categorySlug)", $controller);
        $this->assertStringContainsString("'selectedCategoryName',", $controller);
    }
}
