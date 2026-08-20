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

    public function test_cloudinary_image_urls_preserve_common_image_extensions(): void
    {
        foreach (['jpg', 'jpeg', 'png', 'webp', 'gif'] as $extension) {
            $path = "projects/hero-image.{$extension}";

            $this->assertSame(
                "https://res.cloudinary.com/demo-cloud/image/upload/kimmex/{$path}",
                PublicStorage::urlIfExists($path),
            );
        }
    }

    public function test_cloudinary_image_urls_generate_responsive_sources(): void
    {
        $url = 'https://res.cloudinary.com/demo-cloud/image/upload/kimmex/projects/hero.webp';

        $this->assertSame(
            'https://res.cloudinary.com/demo-cloud/image/upload/f_auto,q_auto,w_640/kimmex/projects/hero.webp 640w, https://res.cloudinary.com/demo-cloud/image/upload/f_auto,q_auto,w_1280/kimmex/projects/hero.webp 1280w',
            PublicStorage::cloudinaryResponsiveSrcset($url, [640, 1280]),
        );
        $this->assertNull(PublicStorage::cloudinaryResponsiveSrcset('/images/webp/hero/hero-1.webp'));
    }

    public function test_public_storage_uses_existing_webp_project_thumbnails(): void
    {
        $this->assertSame(
            '/images/webp/projects/Thumbnail-5.webp',
            PublicStorage::optimizedLocalImageUrl('/images/projects/Thumbnail-5.jpg'),
        );
        $this->assertSame(
            '/images/projects/missing.jpg',
            PublicStorage::optimizedLocalImageUrl('/images/projects/missing.jpg'),
        );
    }

    public function test_public_storage_generates_srcsets_for_local_project_thumbnails(): void
    {
        $this->assertSame(
            '/images/webp/projects/responsive/Thumbnail-5-160.webp 160w, /images/webp/projects/responsive/Thumbnail-5-320.webp 320w',
            PublicStorage::localResponsiveSrcset('/images/webp/projects/Thumbnail-5.webp', [160, 320]),
        );
        $this->assertNull(PublicStorage::localResponsiveSrcset('/images/projects/Thumbnail-5.jpg', [160, 320]));
    }

    public function test_cloudinary_uses_existing_public_assets_for_legacy_image_paths(): void
    {
        $this->assertSame(
            '/images/projects/Thumbnail-1.jpg',
            PublicStorage::urlIfExists('images/projects/Thumbnail-1.jpg'),
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
            'https://res.cloudinary.com/demo-cloud/image/upload/kimmex/resumes/cv.pdf',
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

    public function test_cloudinary_public_storage_urls_do_not_depend_on_an_availability_check(): void
    {
        Http::fake(function () {
            throw new ConnectionException('DNS unavailable');
        });

        $this->assertFalse(Storage::disk('cloudinary')->exists('projects/cloud-only.jpg'));
        $this->assertTrue(PublicStorage::exists('projects/cloud-only.jpg'));
        $this->assertSame(
            'https://res.cloudinary.com/demo-cloud/image/upload/kimmex/projects/cloud-only.jpg',
            PublicStorage::urlIfExists('projects/cloud-only.jpg'),
        );
    }
}
