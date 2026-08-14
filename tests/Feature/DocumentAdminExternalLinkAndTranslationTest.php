<?php

namespace Tests\Feature;

use App\Filament\Resources\Documents\Pages\CreateDocument;
use App\Filament\Resources\Documents\Pages\EditDocument;
use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;
use Tests\TestCase;

class DocumentAdminExternalLinkAndTranslationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_can_create_document_with_dual_language_and_external_links(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $category = DocumentCategory::create([
            'name' => ['en' => 'Corporate Guidelines', 'km' => 'គោលការណ៍ណែនាំ'],
            'slug' => 'corporate-guidelines',
            'isActive' => true,
        ]);

        Livewire::test(CreateDocument::class)
            ->fillForm([
                'title_en' => 'Corporate Safety Policy 2026',
                'title_km' => 'គោលនយោបាយសុវត្ថិភាព ២០២៦',
                'slug' => 'corporate-safety-policy-2026',
                'document_category_id' => $category->id,
                'description_en' => '<p>Official safety protocol document.</p>',
                'description_km' => '<p>ឯកសារពិពណ៌នាអំពីសុវត្ថិភាព។</p>',
                'fileUrl_source' => 'url',
                'fileUrl_external' => 'https://drive.google.com/file/d/12345/view',
                'thumbnailUrl_source' => 'url',
                'thumbnailUrl_external' => 'https://images.unsplash.com/photo-1541888946425-d0fbb18086f6',
                'isPublic' => true,
                'isActive' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('documents', [
            'slug' => 'corporate-safety-policy-2026',
            'document_category_id' => $category->id,
            'fileUrl' => 'https://drive.google.com/file/d/12345/view',
            'thumbnailUrl' => 'https://images.unsplash.com/photo-1541888946425-d0fbb18086f6',
        ]);

        $doc = Document::where('slug', 'corporate-safety-policy-2026')->first();
        $this->assertNotNull($doc);
        $this->assertEquals('Corporate Safety Policy 2026', $doc->getTranslation('title', 'en'));
        $this->assertEquals('គោលនយោបាយសុវត្ថិភាព ២០២៦', $doc->getTranslation('title', 'km'));
        $this->assertStringContainsString('Official safety protocol', $doc->getTranslation('description', 'en'));
        $this->assertStringContainsString('ឯកសារពិពណ៌នា', $doc->getTranslation('description', 'km'));
    }

    public function test_can_edit_document_with_external_links_and_translations(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $category = DocumentCategory::create([
            'name' => ['en' => 'Technical Specs', 'km' => 'លក្ខណៈបច្ចេកទេស'],
            'slug' => 'technical-specs',
            'isActive' => true,
        ]);

        $doc = Document::create([
            'title' => ['en' => 'Original Title', 'km' => 'ចំណងជើងដើម'],
            'slug' => 'original-doc',
            'description' => ['en' => 'Original English Desc', 'km' => 'ការពិពណ៌នាដើម'],
            'document_category_id' => $category->id,
            'fileUrl' => 'https://example.com/docs/spec.pdf',
            'thumbnailUrl' => 'https://example.com/images/thumb.jpg',
            'isPublic' => true,
            'isActive' => true,
        ]);

        Livewire::test(EditDocument::class, ['record' => $doc->getKey()])
            ->assertSchemaStateSet([
                'title_en' => 'Original Title',
                'title_km' => 'ចំណងជើងដើម',
                'fileUrl_source' => 'url',
                'fileUrl_external' => 'https://example.com/docs/spec.pdf',
                'thumbnailUrl_source' => 'url',
                'thumbnailUrl_external' => 'https://example.com/images/thumb.jpg',
            ])
            ->fillForm([
                'title_en' => 'Updated Technical Spec 2026',
                'title_km' => 'លក្ខណៈបច្ចេកទេសកែប្រែ ២០២៦',
                'fileUrl_source' => 'url',
                'fileUrl_external' => 'https://dropbox.com/s/xyz/updated-spec.pdf',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $doc->refresh();
        $this->assertEquals('Updated Technical Spec 2026', $doc->getTranslation('title', 'en'));
        $this->assertEquals('លក្ខណៈបច្ចេកទេសកែប្រែ ២០២៦', $doc->getTranslation('title', 'km'));
        $this->assertEquals('https://dropbox.com/s/xyz/updated-spec.pdf', $doc->fileUrl);
    }

    public function test_can_render_documents_admin_list_page(): void
    {
        $admin = User::factory()->create(['role' => 'ADMIN', 'is_active' => true]);
        $this->actingAs($admin);

        $category = DocumentCategory::create([
            'name' => ['en' => 'Technical Specs', 'km' => 'លក្ខណៈបច្ចេកទេស'],
            'slug' => 'technical-specs',
            'isActive' => true,
        ]);

        Document::create([
            'title' => ['en' => 'Doc 1', 'km' => 'ឯកសារ ១'],
            'slug' => 'doc-1',
            'description' => ['en' => 'Doc 1 Desc', 'km' => 'Doc 1 Desc'],
            'document_category_id' => $category->id,
            'fileUrl' => 'https://example.com/doc1.pdf',
            'isPublic' => true,
            'isActive' => true,
        ]);

        $this->get('/admin/documents')->assertOk();
    }
}
