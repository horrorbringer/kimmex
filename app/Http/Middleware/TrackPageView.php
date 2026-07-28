<?php

namespace App\Http\Middleware;

use App\Models\PageView;
use App\Support\PageViewCounter;
use Closure;
use GeoIp2\Database\Reader;
use GeoIp2\Exception\AddressNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class TrackPageView
{
    /**
     * Paths to skip tracking.
     */
    protected array $skipPaths = [
        '/admin',
        '/livewire',
        '/_debugbar',
    ];

    /**
     * Static asset extensions to skip.
     */
    protected array $skipExtensions = [
        '.js', '.css', '.ico', '.png', '.jpg', '.jpeg', '.svg', '.woff', '.woff2', '.ttf', '.gif', '.webp', '.map',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only track GET requests
        if ($request->method() !== 'GET') {
            return $response;
        }

        $path = $request->path();

        // Skip excluded paths
        foreach ($this->skipPaths as $skipPath) {
            if (str_starts_with('/'.$path, $skipPath)) {
                return $response;
            }
        }

        // Skip static assets
        $lowerPath = strtolower($path);
        foreach ($this->skipExtensions as $ext) {
            if (str_ends_with($lowerPath, $ext)) {
                return $response;
            }
        }

        // Skip bots
        $userAgent = $request->userAgent() ?? '';
        if (preg_match('/bot|crawl|spider|slurp|bingpreview|facebookexternalhit/i', $userAgent)) {
            return $response;
        }

        // Only track successful HTML responses
        if ($response->getStatusCode() !== 200) {
            return $response;
        }

        // Extract page title from response content
        $title = null;
        $content = $response->getContent();
        if ($content && preg_match('/<title[^>]*>(.*?)<\/title>/is', $content, $matches)) {
            $title = html_entity_decode(trim($matches[1]), ENT_QUOTES, 'UTF-8');
            $title = mb_substr($title, 0, 255);
        }

        // Record the page view directly (shared hosting - no queue)
        try {
            $realIp = $this->getRealIp($request);

            if (! Cache::add($this->deduplicationKey($path, $realIp), true, now()->addMinutes(30))) {
                return $response;
            }

            $country = $this->resolveCountry($realIp) ?? 'Unknown';

            PageView::create([
                'url' => $request->fullUrl(),
                'path' => '/'.ltrim($path, '/'),
                'title' => $title,
                'ip' => $realIp,
                'user_agent' => mb_substr($userAgent, 0, 255),
                'referer' => mb_substr($request->header('referer', ''), 0, 255) ?: null,
                'visited_at' => now(),
                'country' => $country,
            ]);

            PageViewCounter::forget('/'.ltrim($path, '/'));
        } catch (\Throwable $e) {
            Cache::forget($this->deduplicationKey($path, $realIp ?? $request->ip()));

            // Silently fail - don't break the page for analytics
            report($e);
        }

        return $response;
    }

    /**
     * Build a privacy-safe key that accepts only one view per page and visitor
     * within a short period, without storing an additional raw IP address.
     */
    protected function deduplicationKey(string $path, ?string $ip): string
    {
        return 'page_view_recent_'.hash('sha256', $path.'|'.($ip ?? 'unknown'));
    }

    /**
     * Get the real client IP, accounting for proxies (Cloudflare, cPanel, etc.)
     */
    protected function getRealIp(Request $request): string
    {
        // Cloudflare
        if ($cf = $request->header('CF-Connecting-IP')) {
            return $cf;
        }

        // Standard proxy headers
        if ($forwarded = $request->header('X-Forwarded-For')) {
            return trim(explode(',', $forwarded)[0]);
        }

        if ($realIp = $request->header('X-Real-IP')) {
            return $realIp;
        }

        return $request->ip();
    }

    /**
     * Resolve country from the local GeoLite2 database without blocking the response.
     */
    protected function resolveCountry(?string $ip): ?string
    {
        if (! $ip || in_array($ip, ['127.0.0.1', '::1', 'localhost'])) {
            return 'Local';
        }

        // Check if it's a private IP
        if (! filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return 'Local';
        }

        if (! file_exists(storage_path('app/geoip/GeoLite2-Country.mmdb'))) {
            return 'Unknown';
        }

        $cacheKey = 'geo_ip_'.md5($ip);

        return cache()->remember($cacheKey, now()->addWeek(), fn (): ?string => $this->lookupGeoLite2($ip));
    }

    /**
     * Look up country using local MaxMind GeoLite2 database.
     */
    protected function lookupGeoLite2(string $ip): ?string
    {
        $dbPath = storage_path('app/geoip/GeoLite2-Country.mmdb');

        if (! file_exists($dbPath)) {
            return null;
        }

        try {
            $reader = new Reader($dbPath);
            $record = $reader->country($ip);

            return $record->country->name;
        } catch (AddressNotFoundException) {
            return 'Unknown';
        } catch (\Throwable) {
            return null;
        }
    }
}
