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
        $orgTree = File::get(resource_path('views/components/about/org-tree.blade.php'));
        $treeNode = File::get(resource_path('views/components/about/tree-node.blade.php'));

        $this->assertStringContainsString('x-about.org-tree', $aboutPage);
        $this->assertStringContainsString('org-tree-viewport', $orgTree);
        $this->assertStringContainsString('org-tree-root', $orgTree);
        $this->assertStringContainsString('org-tree-card', $treeNode);
        $this->assertStringContainsString('org-tree-canvas', $orgTree);
        $this->assertStringNotContainsString('email', $treeNode);
        $this->assertStringNotContainsString('phone', $treeNode);
        $this->assertStringNotContainsString('bio', $treeNode);

        Blade::compileString($orgTree);
        Blade::compileString($treeNode);

        $this->addToAssertionCount(1);
    }
}
