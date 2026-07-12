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
            PageView::create([
                'url' => $request->fullUrl(),
                'path' => '/' . ltrim($path, '/'),
                'title' => $title,
                'ip' => $request->ip(),
                'user_agent' => mb_substr($userAgent, 0, 255),
                'referer' => mb_substr($request->header('referer', ''), 0, 255) ?: null,
                'visited_at' => now(),
                'country' => null,
            ]);
        } catch (\Throwable $e) {
            // Silently fail - don't break the page for analytics
            report($e);
        }

        return $response;
    }
}
