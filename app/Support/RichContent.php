<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class RichContent
{
    /**
     * Resolve all <img> src attributes inside rich editor HTML.
     *
     * Strategy:
     * 1. If the src already starts with http(s) and is reachable → keep it
     * 2. Try the active disk URL → if reachable, use it
     * 3. Fall back to local 'public' disk URL → if reachable, use it
     * 4. File genuinely missing → remove the broken <img> tag
     */
    public static function resolveImages(string $html): string
    {
        if (! str_contains($html, '<img')) {
            return $html;
        }

        return preg_replace_callback(
            '/<img([^>]*)>/i',
            function (array $matches) {
                $attrs = $matches[1];

                // Extract data-id (canonical relative path stored by Filament)
                if (! preg_match('/data-id=["\']([^"\']+)["\']/', $attrs, $idMatch)) {
                    return $matches[0]; // no data-id — leave untouched
                }

                $path = $idMatch[1];
                $url = static::resolveImageUrl($path);

                if (! $url) {
                    // File not found anywhere — remove the broken img tag
                    return '';
                }

                // Replace or insert src
                if (preg_match('/src=["\'][^"\']*["\']/', $attrs)) {
                    $attrs = preg_replace('/src=["\'][^"\']*["\']/', 'src="'.e($url).'"', $attrs);
                } else {
                    $attrs = 'src="'.e($url).'" '.$attrs;
                }

                return '<img'.$attrs.'>';
            },
            $html
        ) ?? $html;
    }

    /**
     * Resolve a file path to a publicly accessible URL.
     * Tries active disk first, falls back to local storage.
     */
    public static function resolveImageUrl(string $path): ?string
    {
        // Try active disk (Cloudinary, R2, local, etc.)
        $diskUrl = PublicStorage::url($path);
        if ($diskUrl && static::urlIsReachable($diskUrl)) {
            return $diskUrl;
        }

        // Fallback: local public disk (files uploaded before disk switch)
        if (PublicStorage::isRemoteDisk()) {
            $localUrl = Storage::disk('public')->url($path);
            if ($localUrl && Storage::disk('public')->exists($path)) {
                return $localUrl;
            }
        }

        return null;
    }

    /**
     * Quick HTTP HEAD check to verify URL is reachable.
     * Cached per request to avoid repeated network calls for the same URL.
     */
    protected static array $urlCache = [];

    protected static function urlIsReachable(string $url): bool
    {
        if (isset(static::$urlCache[$url])) {
            return static::$urlCache[$url];
        }

        try {
            $status = Http::timeout(5)->head($url)->status();

            return static::$urlCache[$url] = ($status >= 200 && $status < 400);
        } catch (\Throwable) {
            return static::$urlCache[$url] = false;
        }
    }

    /**
     * Sanitise and render rich editor HTML for safe frontend output.
     * Handles both plain text and HTML content gracefully.
     */
    public static function render(?string $content): string
    {
        $content = trim((string) $content);

        if ($content === '') {
            return '';
        }

        if (preg_match('/<\s*[a-z][\s\S]*>/i', $content)) {
            return static::resolveImages($content);
        }

        return '<p>'.e($content).'</p>';
    }

    /**
     * Render project rich content with additional HTML cleanup for
     * list nesting, empty paragraphs, and heading artefacts.
     */
    public static function renderProject(?string $content, string $mode = 'auto'): string
    {
        $content = trim((string) $content);

        if ($content === '') {
            return '';
        }

        // Strip empty paragraphs
        $content = preg_replace('#<p>(?:\s|&nbsp;|<br\s*/?>)*</p>#i', '', $content) ?? $content;
        // Unwrap <p> inside <li>
        $content = preg_replace('#<li>\s*<p>(.*?)</p>\s*</li>#is', '<li>$1</li>', $content) ?? $content;

        $hasListMarkup = str_contains($content, '<ul') || str_contains($content, '<ol');
        if ($hasListMarkup) {
            $content = preg_replace('#</p>\s*<p>#i', '</li><li>', $content) ?? $content;
            $content = preg_replace('#</p>\s*<li>#i', '</li><li>', $content) ?? $content;
            $content = preg_replace('#</p>\s*</li>#i', '</li>', $content) ?? $content;
        }

        // Fix heading artefacts
        $content = preg_replace('#<h3><br\s*/?>#i', '<h3>', $content) ?? $content;
        $content = preg_replace('#<h4><br\s*/?>#i', '<h4>', $content) ?? $content;
        $content = preg_replace('#</h[1-6]>\s*<p>\s*<strong>#i', '</h4><p><strong>', $content) ?? $content;

        if (preg_match('/<\s*[a-z][\s\S]*>/i', $content)) {
            return static::resolveImages($content);
        }

        $lines = preg_split('/\R+/u', $content, -1, PREG_SPLIT_NO_EMPTY) ?: [];

        if (in_array($mode, ['list', 'auto'], true) && count($lines) > 1) {
            $items = array_map(fn (string $line): string => '<li>'.e(trim($line)).'</li>', $lines);

            return '<ul>'.implode('', $items).'</ul>';
        }

        if (count($lines) > 1) {
            return implode('', array_map(fn (string $line): string => '<p>'.e(trim($line)).'</p>', $lines));
        }

        return '<p>'.e($content).'</p>';
    }
}
