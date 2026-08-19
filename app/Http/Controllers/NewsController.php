<?php

namespace App\Http\Controllers;

use App\Enums\JobPostingStatus;
use App\Models\Document;
use App\Models\JobPosting;
use App\Models\NewsArticle;
use App\Support\PublicStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\View\View;

class NewsController extends Controller
{
    public function index(): View
    {
        $locale = app()->getLocale();
        $fallbackImage = '/images/webp/hero/hero-3.webp';

        $newsArticles = Cache::remember("news_index_data_{$locale}", now()->addHours(12), function () use ($locale, $fallbackImage): array {
            return NewsArticle::where('isActive', true)
                ->where('publishedAt', '<=', now())
                ->orderByDesc('isFeatured')
                ->orderByDesc('publishedAt')
                ->get()
                ->map(function (NewsArticle $n) use ($locale, $fallbackImage): array {
                    $excerpt = $n->getTranslation('excerpt', $locale)
                        ?: Str::limit(strip_tags((string) $n->getTranslation('content', $locale)), 160);
                    $catName = $n->newsCategory
                        ? ($n->newsCategory->getTranslation('name', $locale) ?: $n->newsCategory->getTranslation('name', 'en'))
                        : ($n->getTranslation('category', $locale) ?: __('Updates'));
                    $catSlug = $n->newsCategory
                        ? $n->newsCategory->slug
                        : Str::slug((string) ($n->getTranslation('category', 'en') ?: 'updates'));

                    $authorName = $n->getTranslation('authorName', $locale)
                        ?: 'Kimmex';
                    $readTime = $n->getTranslation('readTime', $locale) ?: '3 min read';
                    $dateObj = $n->publishedAt ?? $n->created_at;

                    return [
                        'slug' => $n->slug,
                        'category' => $catName,
                        'categorySlug' => $catSlug,
                        'image' => PublicStorage::urlIfExists($n->coverImage, $fallbackImage),
                        'title' => $n->getTranslation('title', $locale),
                        'date' => $dateObj ? $dateObj->format('M d, Y') : '',
                        'dateUpper' => $dateObj ? strtoupper($dateObj->format('M d, Y')) : '',
                        'excerpt' => $excerpt,
                        'authorName' => $authorName,
                        'readTime' => $readTime,
                        'isFeatured' => (bool) $n->isFeatured,
                    ];
                })->toArray();
        });

        $allArticles = collect($newsArticles);
        $featured = $allArticles->first();
        $gridArticles = $allArticles->slice(1)->values();
        $totalArticles = count($newsArticles);

        $categoryCounts = collect($newsArticles)->groupBy('category')->map->count();
        $categories = array_values(array_filter(array_unique(array_column($newsArticles, 'category')), fn ($c) => ! empty(trim((string) $c)) && ! is_numeric($c)));

        $sidebarHeadlines = $gridArticles->slice(0, 4)->values();

        $sidebarDocs = $this->getSidebarDocs($locale);
        $sidebarJobs = $this->getSidebarJobs($locale);

        $perPage = 9;

        return view('pages.news.index', compact(
            'newsArticles',
            'allArticles',
            'featured',
            'gridArticles',
            'totalArticles',
            'categoryCounts',
            'categories',
            'sidebarHeadlines',
            'sidebarDocs',
            'sidebarJobs',
            'locale',
            'fallbackImage',
            'perPage'
        ));
    }

    public function show(Request $request, string $slug): View|RedirectResponse
    {
        $locale = app()->getLocale();
        $fallbackImage = '/images/webp/hero/hero-3.webp';

        $resolveNewsImage = function (?string $path, string $fallback): string {
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
                ?: strip_tags($articleDb->getTranslation('content', $locale));

            $relatedProjects = $articleDb->projects()
                ->where('isActive', true)
                ->get()
                ->map(fn ($project) => [
                    'slug' => $project->slug,
                    'title' => $project->getTranslation('title', $locale),
                    'heroImage' => PublicStorage::urlIfExists($project->heroImage, ''),
                    'location' => $project->getTranslation('location', $locale),
                ])
                ->toArray();

            return [
                'slug' => $articleDb->slug,
                'category' => $articleDb->newsCategory ? ($articleDb->newsCategory->getTranslation('name', $locale) ?: $articleDb->newsCategory->getTranslation('name', 'en')) : ($articleDb->getTranslation('category', $locale) ?: __('Updates')),
                'image' => $resolveNewsImage($articleDb->coverImage, $fallbackImage),
                'title' => $articleDb->getTranslation('title', $locale),
                'metaTitle' => $articleDb->getTranslation('metaTitle', $locale),
                'metaDescription' => $articleDb->getTranslation('metaDescription', $locale),
                'date' => $articleDb->publishedAt
                    ? $articleDb->publishedAt->format('M d, Y')
                    : $articleDb->created_at->format('M d, Y'),
                'publishedAt' => ($articleDb->publishedAt ?: $articleDb->created_at)->toIso8601String(),
                'updatedAt' => $articleDb->updated_at->toIso8601String(),
                'author' => $articleDb->getTranslation('authorName', $locale) ?: 'Kimmex Editorial',
                'readTime' => $articleDb->getTranslation('readTime', $locale)
                    ?: (ceil(str_word_count(strip_tags($articleDb->getTranslation('content', $locale))) / 200).' min read'),
                'excerpt' => $excerpt,
                'content' => $articleDb->getTranslation('content', $locale),
                'tags' => is_array($articleDb->tags) && count($articleDb->tags) > 0
                    ? $articleDb->tags
                    : [$articleDb->category ?: 'News'],
                'gallery' => collect($articleDb->gallery ?? [])
                    ->map(fn ($img) => $resolveNewsImage($img, ''))
                    ->filter()
                    ->values()
                    ->toArray(),
                'videoUrl' => $articleDb->videoUrl,
                'relatedProjects' => $relatedProjects,
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
                'slug' => $r->slug,
                'title' => $r->getTranslation('title', $locale),
                'date' => $r->publishedAt ? $r->publishedAt->format('M d, Y') : $r->created_at->format('M d, Y'),
                'category' => $r->getTranslation('category', $locale) ?: __('Updates'),
                'image' => $resolveNewsImage($r->coverImage, $fallbackImage),
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
                    $next = [
                        'slug' => $nextDb->slug,
                        'title' => $nextDb->getTranslation('title', $locale),
                        'image' => $resolveNewsImage($nextDb->coverImage, $fallbackImage),
                        'category' => $nextDb->newsCategory ? ($nextDb->newsCategory->getTranslation('name', $locale) ?: $nextDb->newsCategory->getTranslation('name', 'en')) : ($nextDb->getTranslation('category', $locale) ?: __('Updates')),
                        'date' => $nextDb->publishedAt ? $nextDb->publishedAt->format('M d, Y') : $nextDb->created_at->format('M d, Y'),
                    ];
                }
                if ($prevDb) {
                    $prev = [
                        'slug' => $prevDb->slug,
                        'title' => $prevDb->getTranslation('title', $locale),
                        'image' => $resolveNewsImage($prevDb->coverImage, $fallbackImage),
                        'category' => $prevDb->newsCategory ? ($prevDb->newsCategory->getTranslation('name', $locale) ?: $prevDb->newsCategory->getTranslation('name', 'en')) : ($prevDb->getTranslation('category', $locale) ?: __('Updates')),
                        'date' => $prevDb->publishedAt ? $prevDb->publishedAt->format('M d, Y') : $prevDb->created_at->format('M d, Y'),
                    ];
                }
            }

            return compact('related', 'next', 'prev');
        });

        $sidebarDocs = $this->getSidebarDocs($locale);
        $sidebarJobs = $this->getSidebarJobs($locale);

        return view('pages.news.show', compact('article', 'relatedData', 'sidebarDocs', 'sidebarJobs', 'locale', 'fallbackImage', 'slug'));
    }

    /**
     * Get cached sidebar documents.
     */
    protected function getSidebarDocs(string $locale): array
    {
        return Cache::remember("news_sidebar_documents_{$locale}", now()->addHours(12), function () use ($locale): array {
            return Document::with('documentCategory')->publiclyVisible()->latest()->take(3)->get()
                ->map(fn (Document $d): array => [
                    'slug' => $d->slug,
                    'title' => $d->getTranslation('title', $locale),
                    'category' => $d->documentCategory ? $d->documentCategory->getTranslation('name', $locale) : ($d->category ?: __('Documents')),
                    'fileType' => $d->fileType ?: 'PDF',
                    'fileSize' => $d->fileSize,
                ])->toArray();
        });
    }

    /**
     * Get cached sidebar job postings.
     */
    protected function getSidebarJobs(string $locale): array
    {
        return Cache::remember("news_sidebar_jobs_{$locale}", now()->addHours(12), function () use ($locale): array {
            return JobPosting::where('status', JobPostingStatus::OPEN)->with('department')->orderByDesc('created_at')->take(3)->get()
                ->map(fn (JobPosting $j): array => [
                    'slug' => $j->slug,
                    'title' => $j->getTranslation('title', $locale),
                    'dept' => $j->department ? $j->department->getTranslation('name', $locale) : __('General'),
                    'location' => $j->getTranslation('location', $locale),
                    'type' => __(str_replace('_', ' ', Str::title(strtolower($j->type ?? 'FULL_TIME')))),
                    'salary' => $j->getTranslation('salary', $locale) ?: __('Negotiable'),
                ])->toArray();
        });
    }
}
