<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RichContent
{
    /**
     * Resolve all <img> src attributes inside rich editor HTML.
     *
     * Strategy:
     * 1. If data-id is present, resolve its public URL via PublicStorage / Storage disk
     * 2. If already a valid absolute URL or root-relative src, keep and sanitize it
     * 3. Ensure responsive and accessible image attributes
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

                // Extract existing src
                $existingSrc = null;
                if (preg_match('/src=["\']([^"\']*)["\']/', $attrs, $srcMatch)) {
                    $existingSrc = $srcMatch[1];
                }

                // Extract data-id (canonical relative path stored by Filament)
                $path = null;
                if (preg_match('/data-id=["\']([^"\']+)["\']/', $attrs, $idMatch)) {
                    $path = $idMatch[1];
                }

                $url = null;
                if ($path) {
                    $url = static::resolveImageUrl($path, $existingSrc);
                } elseif ($existingSrc) {
                    $url = static::normalizeCloudinaryImageUrl($existingSrc);
                }

                if ($url) {
                    if ($existingSrc !== null) {
                        $attrs = preg_replace('/src=["\'][^"\']*["\']/', 'src="'.e($url).'"', $attrs);
                    } else {
                        $attrs = 'src="'.e($url).'" '.$attrs;
                    }
                }

                // Ensure lazy loading and async decoding for frontend performance
                if (! str_contains($attrs, 'loading=')) {
                    $attrs .= ' loading="lazy"';
                }
                if (! str_contains($attrs, 'decoding=')) {
                    $attrs .= ' decoding="async"';
                }

                return '<img'.$attrs.'>';
            },
            $html
        ) ?? $html;
    }

    /**
     * Resolve a file path to a publicly accessible URL.
     * Tries active disk first, falls back to local storage and existing src.
     */
    public static function resolveImageUrl(string $path, ?string $existingSrc = null): ?string
    {
        if (Str::startsWith($path, ['http://', 'https://', '/'])) {
            return static::normalizeCloudinaryImageUrl($path);
        }

        // Try PublicStorage first (checks disk exist or returns proxy/disk url)
        if (PublicStorage::exists($path)) {
            return static::normalizeCloudinaryImageUrl(PublicStorage::url($path));
        }

        // Fallback: local public disk
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->url($path);
        }

        // If active disk generates a URL
        $url = PublicStorage::url($path);
        if ($url) {
            return static::normalizeCloudinaryImageUrl($url);
        }

        // Fall back to existing src if valid
        if (filled($existingSrc)) {
            return static::normalizeCloudinaryImageUrl($existingSrc);
        }

        return null;
    }

    /**
     * Normalize Cloudinary image URLs so that extensionless/raw UUID endpoints
     * are correctly served via the /image/upload/ delivery route.
     */
    public static function normalizeCloudinaryImageUrl(?string $url): ?string
    {
        if (! filled($url)) {
            return $url;
        }

        if (str_contains($url, 'res.cloudinary.com')) {
            // Fix double extensions (e.g. .avif.avif -> .avif, .webp.webp -> .webp)
            $url = preg_replace('/(\.(avif|webp|png|jpg|jpeg|gif))\1+/i', '$1', $url) ?? $url;

            if (str_contains($url, '/raw/upload/')) {
                $ext = Str::lower(pathinfo(parse_url($url, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION));
                if (! in_array($ext, ['pdf', 'zip', 'doc', 'docx', 'xls', 'xlsx', 'csv'], true)) {
                    $url = str_replace('/raw/upload/', '/image/upload/', $url);
                }
            }
        }

        return $url;
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
            $html = static::resolveImages($content);

            return static::preventCodeTranslation($html);
        }

        return '<p>'.e($content).'</p>';
    }

    /**
     * Mark <pre> and <code> elements with translate="no" and class="notranslate"
     * to prevent browser translation tools from altering source code.
     */
    public static function preventCodeTranslation(string $html): string
    {
        $html = preg_replace_callback('/<pre\b([^>]*)>/i', function ($matches) {
            $attrs = $matches[1];
            if (! str_contains($attrs, 'translate=')) {
                $attrs .= ' translate="no"';
            }
            if (str_contains($attrs, 'class="') || str_contains($attrs, "class='")) {
                $attrs = preg_replace('/class=(["\'])(.*?)\1/i', 'class=$1$2 notranslate$1', $attrs);
            } else {
                $attrs .= ' class="notranslate"';
            }

            return '<pre'.$attrs.'>';
        }, $html) ?? $html;

        $html = preg_replace_callback('/<code\b([^>]*)>/i', function ($matches) {
            $attrs = $matches[1];
            if (! str_contains($attrs, 'translate=')) {
                $attrs .= ' translate="no"';
            }
            if (str_contains($attrs, 'class="') || str_contains($attrs, "class='")) {
                $attrs = preg_replace('/class=(["\'])(.*?)\1/i', 'class=$1$2 notranslate$1', $attrs);
            } else {
                $attrs .= ' class="notranslate"';
            }

            return '<code'.$attrs.'>';
        }, $html) ?? $html;

        return $html;
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

        $content = static::removeMalformedAnchors($content);
        $content = static::normalizeBulletParagraphs($content);

        // Strip empty paragraphs
        $content = preg_replace('#<p>(?:\s|&nbsp;|<br\s*/?>)*</p>#i', '', $content) ?? $content;
        // Unwrap <p> inside <li>
        $content = preg_replace('#<li>\s*<p>(.*?)</p>\s*</li>#is', '<li>$1</li>', $content) ?? $content;

        // Repair a legacy editor typo before the browser parses it. Without this,
        // a malformed <urli><li> sequence can leak a list item into the remaining
        // page markup and collapse unrelated sections into a narrow column.
        $content = preg_replace('#<urli\b[^>]*>\s*<li\b[^>]*>#iu', '</li><li>', $content) ?? $content;
        $content = preg_replace('#</li>\s*</urli>#iu', '</li>', $content) ?? $content;
        $content = preg_replace('#<urli\b[^>]*>#iu', '<ul>', $content) ?? $content;
        $content = preg_replace('#</urli>#iu', '</ul>', $content) ?? $content;

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

    /**
     * Remove only malformed link opening tags while retaining their visible text.
     * Legacy translated content occasionally contains an unclosed href quote.
     */
    protected static function removeMalformedAnchors(string $content): string
    {
        return preg_replace_callback(
            '#<a\b([^>]*)>#iu',
            function (array $matches): string {
                return preg_match('#\bhref\s*=\s*([\'\"])[^\'\"]*\1#iu', $matches[1])
                    ? $matches[0]
                    : '';
            },
            $content,
        ) ?? $content;
    }

    /**
     * Convert legacy bullet text separated by line breaks into semantic lists.
     */
    protected static function normalizeBulletParagraphs(string $content): string
    {
        return preg_replace_callback(
            '#<p>(.*?)</p>#isu',
            function (array $matches): string {
                $items = preg_split('#<br\s*/?>#i', $matches[1]) ?: [];

                if (count($items) < 2 || collect($items)->contains(fn (string $item): bool => ! preg_match('/^\s*(?:•|&bull;|[-–])\s+/u', $item))) {
                    return $matches[0];
                }

                $items = array_map(
                    fn (string $item): string => preg_replace('/^\s*(?:•|&bull;|[-–])\s+/u', '', trim($item)) ?? trim($item),
                    $items,
                );

                return '<ul><li>'.implode('</li><li>', $items).'</li></ul>';
            },
            $content,
        ) ?? $content;
    }
}
