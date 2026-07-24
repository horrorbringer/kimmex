<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class OrganizationStructureScalabilityTest extends TestCase
{
    public function test_dynamic_organization_structure_uses_an_expandable_searchable_hierarchy(): void
    {
        $aboutPage = File::get(resource_path('views/pages/about.blade.php'));
        $orgNode = File::get(resource_path('views/components/about/org-node.blade.php'));

        $this->assertStringContainsString("CustomEvent('org-search'", $aboutPage);
        $this->assertStringContainsString("CustomEvent('org-expand-all')", $aboutPage);
        $this->assertStringContainsString("CustomEvent('org-collapse-all')", $aboutPage);
        $this->assertStringContainsString("'showChildren' => false", $aboutPage);
        $this->assertStringContainsString('grid grid-cols-1 md:grid-cols-2', $aboutPage);
        $this->assertStringContainsString("__('Leadership & Departments')", $aboutPage);
        $this->assertStringContainsString('data-org-search="{{ $searchText($node) }}"', $orgNode);
        $this->assertStringContainsString('@org-search.window="filter($event.detail)"', $orgNode);
        $this->assertStringContainsString('x-collapse', $orgNode);
        $this->assertStringContainsString('$canExpand = $hasChildren && $showChildren;', $orgNode);
        $this->assertStringNotContainsString('hidden md:flex flex-col items-center w-full', $orgNode);

        Blade::compileString($orgNode);

        $this->addToAssertionCount(1);
    }
}
