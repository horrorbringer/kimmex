<?php

namespace App\Support;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PublicStorage
{
    protected const REMOTE_DISKS = ['r2', 's3'];

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

        return self::disk()->url($path);
    }

    public static function isRemoteDisk(): bool
    {
        return in_array(self::diskName(), self::REMOTE_DISKS, true);
    }
}
