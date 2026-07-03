<?php

namespace Tests\Feature;

use App\Support\PublicStorage;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CloudinaryStorageTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'filesystems.public_uploads_disk' => 'cloudinary',
            'filesystems.disks.cloudinary.cloud_name' => 'demo-cloud',
            'filesystems.disks.cloudinary.api_key' => 'key',
            'filesystems.disks.cloudinary.api_secret' => 'secret',
            'filesystems.disks.cloudinary.folder' => 'kimmex',
            'filesystems.disks.cloudinary.resource_type' => 'auto',
            'filesystems.disks.cloudinary.upload_resource_type' => 'auto',
            'filesystems.disks.cloudinary.url' => null,
        ]);

        Storage::forgetDisk('cloudinary');
    }

    public function test_public_storage_resolves_cloudinary_image_url(): void
    {
        $this->assertSame(
            'https://res.cloudinary.com/demo-cloud/image/upload/kimmex/news/cover.jpg',
            PublicStorage::url('news/cover.jpg'),
        );
    }

    public function test_cloudinary_disk_uploads_to_auto_resource_endpoint(): void
    {
        Http::fake([
            'api.cloudinary.com/*' => Http::response(['secure_url' => 'https://res.cloudinary.com/demo-cloud/image/upload/kimmex/news/cover.jpg'], 200),
        ]);

        $this->assertTrue(Storage::disk('cloudinary')->put('news/cover.jpg', 'image-bytes'));

        Http::assertSent(fn ($request): bool => $request->url() === 'https://api.cloudinary.com/v1_1/demo-cloud/auto/upload');
    }

    public function test_public_storage_resolves_cloudinary_pdf_as_image_asset_url(): void
    {
        $this->assertSame(
            'https://res.cloudinary.com/demo-cloud/image/upload/kimmex/resumes/cv.pdf.pdf',
            PublicStorage::url('resumes/cv.pdf'),
        );
    }

    public function test_cloudinary_disk_uploads_pdf_to_image_resource_endpoint(): void
    {
        Http::fake([
            'api.cloudinary.com/*' => Http::response(['secure_url' => 'https://res.cloudinary.com/demo-cloud/image/upload/kimmex/resumes/cv.pdf'], 200),
        ]);

        $this->assertTrue(Storage::disk('cloudinary')->put('resumes/cv.pdf', 'pdf-bytes'));

        Http::assertSent(fn ($request): bool => $request->url() === 'https://api.cloudinary.com/v1_1/demo-cloud/image/upload');
    }

    public function test_cloudinary_metadata_missing_headers_does_not_crash_upload_preview(): void
    {
        Http::fake([
            'res.cloudinary.com/*' => Http::response('', 404),
        ]);

        $disk = Storage::disk('cloudinary');

        $this->assertSame(0, $disk->size('images/projects/mpt-office.jpg'));
        $this->assertSame('image/jpeg', $disk->mimeType('images/projects/mpt-office.jpg'));
        $this->assertIsInt($disk->lastModified('images/projects/mpt-office.jpg'));
    }

    public function test_cloudinary_exists_returns_false_when_cdn_is_unreachable(): void
    {
        Http::fake(function () {
            throw new ConnectionException('DNS unavailable');
        });

        $this->assertFalse(Storage::disk('cloudinary')->exists('images/projects/mpt-office.jpg'));
        $this->assertFalse(PublicStorage::exists('images/projects/mpt-office.jpg'));
    }
}
