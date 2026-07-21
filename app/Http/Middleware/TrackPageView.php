<?php

namespace App\Http\Middleware;

use App\Models\PageView;
use Closure;
use GeoIp2\Database\Reader;
use GeoIp2\Exception\AddressNotFoundException;
use Illuminate\Http\Request;
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
        } catch (\Throwable $e) {
            // Silently fail - don't break the page for analytics
            report($e);
        }

        return $response;
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
     * Resolve country from IP using local GeoLite2 database (instant, offline).
     * Falls back to ip-api.com if database is not available.
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

        $cacheKey = 'geo_ip_'.md5($ip);

        return cache()->remember($cacheKey, now()->addWeek(), function () use ($ip) {
            // Strategy 1: Local GeoLite2 database (instant, no network)
            $country = $this->lookupGeoLite2($ip);
            if ($country) {
                return $country;
            }

            // Strategy 2: API fallback (if DB not available)
            return $this->lookupApi($ip);
        });
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

    /**
     * Fall back to ip-api.com for country lookup.
     */
    protected function lookupApi(string $ip): ?string
    {
        try {
            $url = "http://ip-api.com/json/{$ip}?fields=status,country";
            $response = null;

            // Try curl first (more reliable on shared hosting)
            if (function_exists('curl_init')) {
                $ch = curl_init($url);
                curl_setopt_array($ch, [
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_TIMEOUT => 3,
                    CURLOPT_CONNECTTIMEOUT => 2,
                    CURLOPT_FOLLOWLOCATION => true,
                ]);
                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                if ($httpCode !== 200) {
                    $response = null;
                }
            }

            // Fallback to file_get_contents
            if (! $response) {
                $response = @file_get_contents($url, false, stream_context_create([
                    'http' => ['timeout' => 2],
                ]));
            }

            if ($response) {
                $data = json_decode($response, true);
                if (($data['status'] ?? '') === 'success') {
                    return $data['country'] ?? null;
                }
            }
        } catch (\Throwable) {
            // Silently fail
        }

        return null;
    }
}
