<?php

namespace App\Support;

use App\Models\PageView;
use Illuminate\Support\Facades\Cache;

class PageViewCounter
{
    public static function count(string $path): int
    {
        $path = self::normalizePath($path);

        return Cache::remember(
            self::cacheKey($path),
            now()->addMinutes(10),
            fn (): int => PageView::where('path', $path)->count(),
        );
    }

    public static function normalizePath(string $path): string
    {
        return '/'.ltrim($path, '/');
    }

    protected static function cacheKey(string $path): string
    {
        return 'page_view_count_'.md5($path);
    }
}
