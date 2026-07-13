<?php

namespace App\Http\Middleware;

use App\Models\PageView;
use Closure;
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
            if (str_starts_with('/' . $path, $skipPath)) {
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
            $country = $this->resolveCountry($request->ip());

            PageView::create([
                'url' => $request->fullUrl(),
                'path' => '/' . ltrim($path, '/'),
                'title' => $title,
                'ip' => $request->ip(),
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
     * Resolve country from IP address using free ip-api.com (non-blocking, cached).
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

        $cacheKey = 'geo_ip_' . md5($ip);

        return cache()->remember($cacheKey, now()->addDay(), function () use ($ip) {
            try {
                $response = @file_get_contents("http://ip-api.com/json/{$ip}?fields=country", false, stream_context_create([
                    'http' => ['timeout' => 2],
                ]));

                if ($response) {
                    $data = json_decode($response, true);
                    return $data['country'] ?? null;
                }
            } catch (\Throwable) {
                // Silently fail
            }

            return null;
        });
    }
}
