<?php

namespace Tests\Feature;

use App\Livewire\DocumentLibrary;
use App\Models\Document;
use App\Models\DocumentCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;
use Tests\TestCase;

class DocumentPageVisibilityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();
    }

    public function test_documents_page_returns_not_found_when_no_public_documents_exist(): void
    {
        $this->get('/documents')->assertNotFound();
    }

    public function test_documents_page_renders_when_public_documents_exist(): void
    {
        $category = DocumentCategory::create([
            'name' => ['en' => 'Reports', 'km' => 'Reports'],
            'slug' => 'reports',
            'isActive' => true,
        ]);

        Document::create([
            'title' => ['en' => 'Annual Report', 'km' => 'Annual Report'],
            'slug' => 'annual-report',
            'description' => ['en' => 'Company report', 'km' => 'Company report'],
            'fileUrl' => 'documents/annual-report.pdf',
            'category' => 'Reports',
            'document_category_id' => $category->id,
            'isPublic' => true,
            'isActive' => true,
        ]);

        $this->get('/documents')->assertOk();
    }

    public function test_categories_without_documents_are_hidden_from_library(): void
    {
        $categoryWithDoc = DocumentCategory::create([
            'name' => ['en' => 'ActiveCategoryAlpha', 'km' => 'ActiveCategoryAlpha'],
            'slug' => 'active-category-alpha',
            'isActive' => true,
        ]);

        $emptyCategory = DocumentCategory::create([
            'name' => ['en' => 'EmptyCategoryBeta', 'km' => 'EmptyCategoryBeta'],
            'slug' => 'empty-category-beta',
            'isActive' => true,
        ]);

        Document::create([
            'title' => ['en' => 'Active Doc', 'km' => 'Active Doc'],
            'slug' => 'active-doc',
            'description' => ['en' => 'Doc desc', 'km' => 'Doc desc'],
            'fileUrl' => 'documents/active.pdf',
            'category' => 'ActiveCategoryAlpha',
            'document_category_id' => $categoryWithDoc->id,
            'isPublic' => true,
            'isActive' => true,
        ]);

        $categoryWithDoc->refresh();
        $emptyCategory->refresh();

        Livewire::test(DocumentLibrary::class)
            ->assertSee($categoryWithDoc->getTranslation('name', app()->getLocale()))
            ->assertDontSee($emptyCategory->getTranslation('name', app()->getLocale()));
    }
}
