<?php

namespace App\Http\Controllers;

use App\Models\NewsArticle;
use App\Support\PublicStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class NewsController extends Controller
{
    public function show(Request $request, string $slug): View|RedirectResponse
    {
        $locale = app()->getLocale();
        $fallbackImage = '/images/webp/hero/hero-3.webp';

        $resolveNewsImage = function (?string $path, string $fallback) use ($fallbackImage): string {
            if (! filled($path)) {
                return $fallback;
            }
            return PublicStorage::urlIfExists($path, $fallback);
        };

        $article = Cache::remember("news_article_data_{$slug}_{$locale}", now()->addHours(12), function () use ($slug, $locale, $fallbackImage, $resolveNewsImage): ?array {
            $articleDb = NewsArticle::where('isActive', true)
                ->where('publishedAt', '<=', now())
                ->where('slug', $slug)
                ->first();

            if (! $articleDb) {
                return null;
            }

            $excerpt = $articleDb->getTranslation('excerpt', $locale)
                ?: \Illuminate\Support\Str::limit(strip_tags($articleDb->getTranslation('content', $locale)), 180);

            return [
                'slug'            => $articleDb->slug,
                'category'        => $articleDb->getTranslation('category', $locale) ?: __('Updates'),
                'image'           => $resolveNewsImage($articleDb->coverImage, $fallbackImage),
                'title'           => $articleDb->getTranslation('title', $locale),
                'metaTitle'       => $articleDb->getTranslation('metaTitle', $locale),
                'metaDescription' => $articleDb->getTranslation('metaDescription', $locale),
                'date'            => $articleDb->publishedAt
                    ? $articleDb->publishedAt->format('M d, Y')
                    : $articleDb->created_at->format('M d, Y'),
                'publishedAt'     => ($articleDb->publishedAt ?: $articleDb->created_at)->toIso8601String(),
                'updatedAt'       => $articleDb->updated_at->toIso8601String(),
                'author'          => $articleDb->getTranslation('authorName', $locale) ?: 'Kimmex Editorial',
                'readTime'        => $articleDb->getTranslation('readTime', $locale)
                    ?: (ceil(str_word_count(strip_tags($articleDb->getTranslation('content', $locale))) / 200) . ' min read'),
                'excerpt'         => $excerpt,
                'content'         => $articleDb->getTranslation('content', $locale),
                'tags'            => is_array($articleDb->tags) && count($articleDb->tags) > 0
                    ? $articleDb->tags
                    : [$articleDb->category ?: 'News'],
                'gallery'         => collect($articleDb->gallery ?? [])
                    ->map(fn ($img) => $resolveNewsImage($img, ''))
                    ->filter()
                    ->values()
                    ->toArray(),
                'videoUrl'        => $articleDb->videoUrl,
            ];
        });

        if (! $article) {
            return redirect()->route('news.index')
                ->with('flash_warning', __('The article you were looking for could not be found.'));
        }

        $relatedData = Cache::remember("news_related_array_{$slug}_{$locale}", now()->addHours(12), function () use ($slug, $locale, $fallbackImage, $resolveNewsImage, $article): array {
            $relatedDb = NewsArticle::where('isActive', true)
                ->where('publishedAt', '<=', now())
                ->where('slug', '!=', $slug)
                ->orderByDesc('publishedAt')
                ->take(3)
                ->get();

            $related = $relatedDb->map(fn (NewsArticle $r) => [
                'slug'     => $r->slug,
                'title'    => $r->getTranslation('title', $locale),
                'date'     => $r->publishedAt ? $r->publishedAt->format('M d, Y') : $r->created_at->format('M d, Y'),
                'category' => $r->getTranslation('category', $locale) ?: __('Updates'),
                'image'    => $resolveNewsImage($r->coverImage, $fallbackImage),
            ])->toArray();

            $next = null;
            $prev = null;

            if (! empty($article['publishedAt'])) {
                $currentPublishedAt = Carbon::parse($article['publishedAt']);

                $nextDb = NewsArticle::where('isActive', true)
                    ->where('publishedAt', '<=', now())
                    ->where('publishedAt', '<', $currentPublishedAt)
                    ->orderByDesc('publishedAt')
                    ->first();

                $prevDb = NewsArticle::where('isActive', true)
                    ->where('publishedAt', '<=', now())
                    ->where('publishedAt', '>', $currentPublishedAt)
                    ->orderBy('publishedAt')
                    ->first();

                if ($nextDb) {
                    $next = ['slug' => $nextDb->slug, 'title' => $nextDb->getTranslation('title', $locale)];
                }
                if ($prevDb) {
                    $prev = ['slug' => $prevDb->slug, 'title' => $prevDb->getTranslation('title', $locale)];
                }
            }

            return compact('related', 'next', 'prev');
        });

        return view('pages.news.show', compact('article', 'relatedData', 'locale', 'fallbackImage'));
    }
}
