<?php

namespace App\Filament\Support;

use Filament\Forms\Components\FileUpload;

class OptimizedFileUpload
{
    /**
     * Create an optimized image upload field with best practices:
     * - Auto resize to max dimensions
     * - Compress/optimize
     * - Set reasonable file size limits
     * - Use configured disk
     */
    public static function image(string $name): FileUpload
    {
        return FileUpload::make($name)
            ->image()
            ->disk(config('filesystems.public_uploads_disk'))
            ->visibility('public')
            ->imageResizeMode('cover')
            ->imageCropAspectRatio(null)
            ->imageResizeTargetWidth('1920')
            ->imageResizeTargetHeight('1080')
            ->maxSize(5120)
            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif']);
    }

    public static function hero(string $name): FileUpload
    {
        return FileUpload::make($name)
            ->image()
            ->disk(config('filesystems.public_uploads_disk'))
            ->visibility('public')
            ->imageResizeMode('cover')
            ->imageCropAspectRatio('16:9')
            ->imageResizeTargetWidth('2560')
            ->imageResizeTargetHeight('1440')
            ->maxSize(8192)
            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp']);
    }

    public static function thumbnail(string $name): FileUpload
    {
        return FileUpload::make($name)
            ->image()
            ->disk(config('filesystems.public_uploads_disk'))
            ->visibility('public')
            ->imageResizeMode('cover')
            ->imageCropAspectRatio('1:1')
            ->imageResizeTargetWidth('400')
            ->imageResizeTargetHeight('400')
            ->maxSize(2048)
            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp']);
    }

    public static function logo(string $name): FileUpload
    {
        return FileUpload::make($name)
            ->image()
            ->disk(config('filesystems.public_uploads_disk'))
            ->visibility('public')
            ->imageResizeMode('contain')
            ->imageResizeTargetWidth('800')
            ->imageResizeTargetHeight('800')
            ->maxSize(2048)
            ->acceptedFileTypes(['image/png', 'image/svg+xml', 'image/webp']);
    }

    /**
     * Create a document upload (PDF, Word, etc.)
     */
    public static function document(string $name): FileUpload
    {
        return FileUpload::make($name)
            ->disk(config('filesystems.public_uploads_disk'))
            ->visibility('public')
            ->maxSize(20480) // 20MB max
            ->acceptedFileTypes([
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
    }
}
