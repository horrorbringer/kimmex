<?php

namespace Tests\Feature;

use App\Filament\Support\OptimizedFileUpload;
use Tests\TestCase;

class ProjectWebpUploadTest extends TestCase
{
    public function test_project_hero_and_gallery_uploads_accept_webp_images(): void
    {
        $projectUploads = [
            OptimizedFileUpload::hero('heroImage'),
            OptimizedFileUpload::image('url'),
        ];

        foreach ($projectUploads as $upload) {
            $this->assertContains('image/webp', $upload->getAcceptedFileTypes());
            $this->assertSame('image/webp', $upload->getMimeTypeMap()['webp']);
        }
    }
}
