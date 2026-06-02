<?php

namespace Tests\Feature;

use App\Models\Document;
use App\Models\DocumentCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
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
}
