<?php

namespace App\Support;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PublicStorage
{
    protected const REMOTE_DISKS = ['r2', 's3', 'cloudinary'];

    public static function diskName(): string
    {
        return config('filesystems.public_uploads_disk', 'public');
    }

    public static function disk(): Filesystem
    {
        return Storage::disk(self::diskName());
    }

    public static function exists(?string $path): bool
    {
        if (! filled($path) || Str::startsWith($path, ['http://', 'https://', '/'])) {
            return false;
        }

        if (self::isRemoteDisk()) {
            return true;
        }

        return self::disk()->exists($path);
    }

    public static function url(?string $path): ?string
    {
        if (! filled($path)) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://', '/'])) {
            return $path;
        }

        if (self::usesPublicProxy()) {
            return route('media.show', ['path' => ltrim($path, '/')]);
        }

        return self::disk()->url($path);
    }

    public static function urlIfExists(?string $path, ?string $fallback = null): ?string
    {
        if (! filled($path)) {
            return $fallback;
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        if (Str::startsWith($path, '/')) {
            return file_exists(public_path(ltrim($path, '/'))) ? $path : $fallback;
        }

        return self::exists($path) ? self::url($path) : $fallback;
    }

    public static function delete(string|array|null $paths): void
    {
        $paths = collect((array) $paths)
            ->flatten()
            ->filter(fn ($path) => filled($path) && is_string($path))
            ->reject(fn (string $path) => Str::startsWith($path, ['http://', 'https://', '/']))
            ->map(fn (string $path) => ltrim($path, '/'))
            ->unique()
            ->values()
            ->all();

        if ($paths === []) {
            return;
        }

        self::disk()->delete($paths);
    }

    public static function isRemoteDisk(): bool
    {
        return in_array(self::diskName(), self::REMOTE_DISKS, true);
    }

    public static function usesPublicProxy(): bool
    {
        return self::isRemoteDisk()
            && self::diskName() !== 'cloudinary'
            && blank(config('filesystems.disks.'.self::diskName().'.url'));
    }
}
