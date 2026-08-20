<?php

namespace App\Filesystem;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use League\Flysystem\Config;
use League\Flysystem\DirectoryAttributes;
use League\Flysystem\FileAttributes;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\UnableToCopyFile;
use League\Flysystem\UnableToCreateDirectory;
use League\Flysystem\UnableToDeleteDirectory;
use League\Flysystem\UnableToDeleteFile;
use League\Flysystem\UnableToMoveFile;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\UnableToWriteFile;
use League\Flysystem\Visibility;

class CloudinaryAdapter implements FilesystemAdapter
{
    public function __construct(private readonly array $config) {}

    public function fileExists(string $path): bool
    {
        try {
            $publicId = $this->publicId($path);
            $resourceType = $this->resourceTypeForPath($path);
            $type = in_array($resourceType, ['image', 'video', 'raw'], true) ? $resourceType : 'image';

            $response = Http::timeout($this->timeout())
                ->withBasicAuth($this->apiKey(), $this->apiSecret())
                ->get($this->apiEndpoint("resources/{$type}/upload/".rawurlencode($publicId)));

            return $response->successful();
        } catch (\Throwable) {
            return false;
        }
    }

    public function directoryExists(string $path): bool
    {
        return $path === '';
    }

    public function write(string $path, string $contents, Config $config): void
    {
        $stream = fopen('php://temp', 'r+');

        if ($stream === false) {
            throw UnableToWriteFile::atLocation($path, 'Unable to open temporary stream.');
        }

        fwrite($stream, $contents);
        rewind($stream);

        try {
            $this->writeStream($path, $stream, $config);
        } finally {
            fclose($stream);
        }
    }

    public function writeStream(string $path, $contents, Config $config): void
    {
        $path = $this->normalizePath($path);
        $params = $this->signedUploadParams($path);

        $response = Http::timeout($this->timeout())
            ->attach('file', $contents, basename($path))
            ->post($this->uploadEndpoint($path), $params);

        if (! $response->successful()) {
            throw UnableToWriteFile::atLocation($path, $response->body());
        }
    }

    public function read(string $path): string
    {
        $response = Http::timeout($this->timeout())->get($this->getUrl($path));

        if (! $response->successful()) {
            throw UnableToReadFile::fromLocation($path, $response->body());
        }

        return $response->body();
    }

    public function readStream(string $path)
    {
        $stream = fopen('php://temp', 'r+');

        if ($stream === false) {
            throw UnableToReadFile::fromLocation($path, 'Unable to open temporary stream.');
        }

        fwrite($stream, $this->read($path));
        rewind($stream);

        return $stream;
    }

    public function delete(string $path): void
    {
        $path = $this->normalizePath($path);

        foreach ($this->deleteResourceTypes($path) as $resourceType) {
            $params = $this->signedParams([
                'invalidate' => 'true',
                'public_id' => $this->publicId($path),
                'timestamp' => time(),
            ]);

            $response = Http::timeout($this->timeout())
                ->asForm()
                ->post($this->apiEndpoint("{$resourceType}/destroy"), $params);

            if ($response->successful()) {
                return;
            }
        }

        throw UnableToDeleteFile::atLocation($path, 'Cloudinary destroy request failed.');
    }

    public function deleteDirectory(string $path): void
    {
        throw UnableToDeleteDirectory::atLocation($path, 'Cloudinary folder deletes are not supported by this adapter.');
    }

    public function createDirectory(string $path, Config $config): void
    {
        if ($path === '') {
            return;
        }

        throw UnableToCreateDirectory::atLocation($path, 'Cloudinary creates folders from public IDs automatically.');
    }

    public function setVisibility(string $path, string $visibility): void
    {
        //
    }

    public function visibility(string $path): FileAttributes
    {
        return new FileAttributes($path, null, Visibility::PUBLIC);
    }

    public function mimeType(string $path): FileAttributes
    {
        return new FileAttributes($path, null, Visibility::PUBLIC, null, $this->head($path, 'Content-Type') ?: $this->guessMimeType($path));
    }

    public function lastModified(string $path): FileAttributes
    {
        $lastModified = $this->head($path, 'Last-Modified');

        return new FileAttributes($path, null, Visibility::PUBLIC, $lastModified ? strtotime($lastModified) : time(), $this->guessMimeType($path));
    }

    public function fileSize(string $path): FileAttributes
    {
        return new FileAttributes($path, (int) ($this->head($path, 'Content-Length') ?: 0), Visibility::PUBLIC, null, $this->guessMimeType($path));
    }

    public function listContents(string $path, bool $deep): iterable
    {
        return [new DirectoryAttributes($path)];
    }

    public function move(string $source, string $destination, Config $config): void
    {
        try {
            $this->write($destination, $this->read($source), $config);
            $this->delete($source);
        } catch (\Throwable $exception) {
            throw UnableToMoveFile::fromLocationTo($source, $destination, $exception);
        }
    }

    public function copy(string $source, string $destination, Config $config): void
    {
        try {
            $this->write($destination, $this->read($source), $config);
        } catch (\Throwable $exception) {
            throw UnableToCopyFile::fromLocationTo($source, $destination, $exception);
        }
    }

    public function getUrl(string $path): string
    {
        $path = $this->normalizePath($path);
        $baseUrl = rtrim((string) ($this->config['url'] ?? ''), '/');

        if ($baseUrl !== '') {
            return $baseUrl.'/'.str_replace('%2F', '/', rawurlencode($this->deliveryPublicId($path)));
        }

        $cloudName = $this->cloudName();
        $resourceType = $this->resourceTypeForPath($path);
        $cloud = rawurlencode($cloudName);
        $publicId = str_replace('%2F', '/', rawurlencode($this->deliveryPublicId($path)));

        return "https://res.cloudinary.com/{$cloud}/{$resourceType}/upload/{$publicId}";
    }

    private function head(string $path, string $header): ?string
    {
        try {
            $response = Http::timeout($this->timeout())->head($this->getUrl($path));
        } catch (\Throwable) {
            return null;
        }

        if (! $response->successful()) {
            return null;
        }

        return $response->header($header);
    }

    private function signedUploadParams(string $path): array
    {
        return $this->signedParams([
            'invalidate' => 'true',
            'overwrite' => 'true',
            'public_id' => $this->publicId($path),
            'timestamp' => time(),
        ]);
    }

    private function signedParams(array $params): array
    {
        ksort($params);

        $payload = collect($params)
            ->map(fn ($value, string $key): string => $key.'='.$value)
            ->implode('&');

        $params['api_key'] = $this->apiKey();
        $params['signature'] = sha1($payload.$this->apiSecret());

        return $params;
    }

    private function uploadEndpoint(string $path): string
    {
        return $this->apiEndpoint($this->uploadResourceType($path).'/upload');
    }

    private function apiEndpoint(string $path): string
    {
        return sprintf(
            'https://api.cloudinary.com/v1_1/%s/%s',
            rawurlencode($this->cloudName()),
            ltrim($path, '/'),
        );
    }

    private function uploadResourceType(string $path): string
    {
        $configured = (string) ($this->config['upload_resource_type'] ?? 'auto');

        if ($configured !== 'auto') {
            return $configured;
        }

        return Str::lower(pathinfo($path, PATHINFO_EXTENSION)) === 'pdf'
            ? 'image'
            : 'auto';
    }

    private function resourceTypeForPath(string $path): string
    {
        $configured = (string) ($this->config['resource_type'] ?? 'auto');

        if ($configured !== 'auto') {
            return $configured;
        }

        $extension = Str::lower(pathinfo($path, PATHINFO_EXTENSION));

        return match (true) {
            in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif', 'svg', 'bmp', 'tif', 'tiff', 'pdf'], true) => 'image',
            in_array($extension, ['mp4', 'mov', 'webm', 'm4v', 'avi'], true) => 'video',
            default => 'raw',
        };
    }

    private function deleteResourceTypes(string $path): array
    {
        $resourceType = $this->resourceTypeForPath($path);

        return $resourceType === 'auto' ? ['image', 'video', 'raw'] : [$resourceType];
    }

    private function publicId(string $path): string
    {
        $folder = trim((string) ($this->config['folder'] ?? ''), '/');
        $path = $this->normalizePath($path);

        return $folder === '' ? $path : $folder.'/'.$path;
    }

    private function deliveryPublicId(string $path): string
    {
        $publicId = $this->publicId($path);
        $extension = Str::lower(pathinfo($path, PATHINFO_EXTENSION));

        return $extension !== '' ? $publicId.'.'.$extension : $publicId;
    }

    private function normalizePath(string $path): string
    {
        return ltrim(str_replace('\\', '/', $path), '/');
    }

    private function guessMimeType(string $path): string
    {
        return match (Str::lower(pathinfo($path, PATHINFO_EXTENSION))) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'svg' => 'image/svg+xml',
            'pdf' => 'application/pdf',
            default => 'application/octet-stream',
        };
    }

    private function cloudName(): string
    {
        return (string) $this->config['cloud_name'];
    }

    private function apiKey(): string
    {
        return (string) $this->config['api_key'];
    }

    private function apiSecret(): string
    {
        return (string) $this->config['api_secret'];
    }

    private function timeout(): int
    {
        return (int) ($this->config['timeout'] ?? 30);
    }
}
