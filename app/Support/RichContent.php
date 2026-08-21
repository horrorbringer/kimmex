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

                // Check if existing src is a temporary Livewire preview URL
                $isLivewireTempUrl = $existingSrc && (
                    str_contains($existingSrc, '/preview-file/') ||
                    str_contains($existingSrc, 'livewire-') ||
                    str_contains($existingSrc, '/livewire/')
                );

                // Extract data-id (canonical relative path or UUID stored by Filament)
                $path = null;
                if (preg_match('/data-id=["\']([^"\']+)["\']/', $attrs, $idMatch)) {
                    $path = $idMatch[1];
                }

                // If src is missing OR is a temporary Livewire URL, we must resolve the permanent URL
                if (! $existingSrc || $isLivewireTempUrl) {
                    $lookupPath = $path;

                    // If path is a UUID or missing, extract filename from the temporary Livewire URL
                    if ((! $lookupPath || ! str_contains($lookupPath, '.')) && $existingSrc) {
                        $parsedPath = parse_url($existingSrc, PHP_URL_PATH) ?? '';
                        $filename = basename($parsedPath);
                        if (filled($filename) && str_contains($filename, '.')) {
                            $lookupPath = $filename;
                        }
                    }

                    if ($lookupPath) {
                        $url = static::resolveImageUrl($lookupPath);
                        if ($url) {
                            if ($existingSrc !== null) {
                                $attrs = preg_replace('/src=["\'][^"\']*["\']/', 'src="'.e($url).'"', $attrs);
                            } else {
                                $attrs = ' src="'.e($url).'" '.$attrs;
                            }
                        }
                    }
                }

                // Ensure lazy loading and async decoding for frontend performance
                if (! str_contains($attrs, 'loading=')) {
                    $attrs .= ' loading="lazy"';
                }
                if (! str_contains($attrs, 'decoding=')) {
                    $attrs .= ' decoding="async"';
                }

                // Clean duplicate whitespace and ensure proper HTML tag format
                $cleanAttrs = trim(preg_replace('/\s+/', ' ', $attrs));

                return '<img '.$cleanAttrs.'>';
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
            return $path;
        }

        // Try direct path on active storage disk
        if (PublicStorage::exists($path)) {
            return PublicStorage::url($path);
        }

        // If path is just a filename, check within standard rich content directory
        if (! str_starts_with($path, 'news/content/')) {
            $contentPath = 'news/content/'.$path;
            if (PublicStorage::exists($contentPath)) {
                return PublicStorage::url($contentPath);
            }
        }

        // Fallback: local public disk
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->url($path);
        }

        if (! str_starts_with($path, 'news/content/') && Storage::disk('public')->exists('news/content/'.$path)) {
            return Storage::disk('public')->url('news/content/'.$path);
        }

        // If active disk generates a URL for the path
        $url = PublicStorage::url(str_starts_with($path, 'news/content/') ? $path : 'news/content/'.$path);
        if ($url) {
            return $url;
        }

        // Fall back to existing src if valid and not a temp URL
        if (filled($existingSrc) && ! str_contains($existingSrc, '/preview-file/')) {
            return $existingSrc;
        }

        return null;
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

    /**
     * Extract all public storage image paths from rich editor content / translatable arrays.
     */
    public static function extractImagePaths(mixed $content): array
    {
        if (empty($content)) {
            return [];
        }

        $htmlStrings = [];
        if (is_array($content)) {
            $htmlStrings = array_values(array_filter($content, 'is_string'));
        } elseif (is_string($content)) {
            $decoded = json_decode($content, true);
            if (is_array($decoded)) {
                $htmlStrings = array_values(array_filter($decoded, 'is_string'));
            } else {
                $htmlStrings = [$content];
            }
        }

        $paths = [];
        foreach ($htmlStrings as $html) {
            // 1. Extract canonical relative path from data-id
            if (preg_match_all('/data-id=["\']([^"\']+)["\']/', $html, $idMatches)) {
                foreach ($idMatches[1] as $id) {
                    if (str_contains($id, '.') && ! Str::startsWith($id, ['http://', 'https://'])) {
                        $paths[] = ltrim($id, '/');
                    }
                }
            }

            // 2. Extract relative path from src (Cloudinary or local storage URLs)
            if (preg_match_all('/src=["\']([^"\']+)["\']/', $html, $srcMatches)) {
                foreach ($srcMatches[1] as $src) {
                    if (preg_match('#(?:kimmex_website/|/storage/)([\w\-]+(?:/[\w\-]+)*\.[a-z0-9]+)#i', $src, $m)) {
                        $clean = preg_replace('/(\.(avif|webp|png|jpg|jpeg|gif))\1+/i', '$1', $m[1]) ?? $m[1];
                        $paths[] = ltrim($clean, '/');
                    }
                }
            }
        }

        return array_values(array_unique(array_filter($paths)));
    }
}
