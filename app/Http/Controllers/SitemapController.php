<?php

namespace App\Http\Controllers;

use App\Enums\JobPostingStatus;
use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\JobPosting;
use App\Models\NewsArticle;
use App\Models\NewsCategory;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Service;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SitemapController extends Controller
{
    /**
     * Generate search-engine XML sitemap.
     */
    public function xml(): Response
    {
        $urls = collect();
        $add = function (string $loc, ?CarbonInterface $lastmod = null, string $changefreq = 'monthly', string $priority = '0.7') use ($urls) {
            $urls->push([
                'loc' => url($loc),
                'lastmod' => $lastmod?->toAtomString(),
                'changefreq' => $changefreq,
                'priority' => $priority,
            ]);
        };

        $add('/', null, 'weekly', '1.0');
        $add('/about', null, 'monthly', '0.8');
        $serviceLastModified = Service::where('isActive', true)->max('updated_at');
        $projectLastModified = Project::where('isActive', true)->max('updated_at');
        $newsLastModified = NewsArticle::where('isActive', true)->where('publishedAt', '<=', now())->max('updated_at');
        $jobLastModified = JobPosting::where('status', JobPostingStatus::OPEN)->max('updated_at');
        $documentLastModified = Document::publiclyVisible()->max('updated_at');

        $add('/services', $serviceLastModified ? Carbon::parse($serviceLastModified) : null, 'weekly', '0.9');
        $add('/projects', $projectLastModified ? Carbon::parse($projectLastModified) : null, 'weekly', '0.9');
        $add('/news', $newsLastModified ? Carbon::parse($newsLastModified) : null, 'weekly', '0.8');
        $add('/careers', $jobLastModified ? Carbon::parse($jobLastModified) : null, 'weekly', '0.7');
        $add('/contact', null, 'monthly', '0.7');
        $add('/sitemap', null, 'weekly', '0.7');

        if ($documentLastModified) {
            $add('/documents', $documentLastModified ? Carbon::parse($documentLastModified) : null, 'weekly', '0.7');
        }

        Service::where('isActive', true)->select('slug', 'updated_at')->orderBy('orderIndex')->lazy()
            ->each(fn (Service $service) => $add(route('services.show', ['slug' => $service->slug], false), $service->updated_at, 'monthly', '0.8'));

        Project::where('isActive', true)->select('slug', 'updated_at')->latest('updated_at')->lazy()
            ->each(fn (Project $project) => $add(route('projects.show', ['slug' => $project->slug], false), $project->updated_at, 'monthly', '0.8'));

        NewsArticle::where('isActive', true)->where('publishedAt', '<=', now())->select('slug', 'updated_at')->orderByDesc('publishedAt')->lazy()
            ->each(fn (NewsArticle $article) => $add(route('news.show', ['slug' => $article->slug], false), $article->updated_at, 'weekly', '0.7'));

        Document::publiclyVisible()->select('slug', 'updated_at')->latest('updated_at')->lazy()
            ->each(fn (Document $document) => $add(route('documents.show', ['slug' => $document->slug], false), $document->updated_at, 'monthly', '0.6'));

        JobPosting::where('status', JobPostingStatus::OPEN)->select('slug', 'updated_at')->latest('updated_at')->lazy()
            ->each(fn (JobPosting $job) => $add(route('careers.show', ['slug' => $job->slug], false), $job->updated_at, 'weekly', '0.6'));

        return response()
            ->view('sitemap', ['urls' => $urls], 200)
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    /**
     * Display human-readable HTML sitemap.
     */
    public function index(): View
    {
        $locale = app()->getLocale();

        $data = Cache::remember("html_sitemap_data_{$locale}", now()->addHours(6), function () use ($locale) {
            // 1. Services
            $services = Service::where('isActive', true)
                ->orderBy('orderIndex')
                ->get()
                ->map(fn (Service $s) => [
                    'title' => $s->getTranslation('title', $locale) ?: $s->getTranslation('title', 'en'),
                    'slug' => $s->slug,
                    'url' => route('services.show', ['slug' => $s->slug]),
                    'description' => Str::limit(strip_tags($s->getTranslation('description', $locale) ?: ''), 120),
                ]);

            // 2. Project Categories & Projects
            $projectCategories = ProjectCategory::where('isActive', true)
                ->orderBy('id')
                ->with(['projects' => fn ($q) => $q->where('isActive', true)->orderByDesc('created_at')])
                ->get()
                ->map(fn (ProjectCategory $c) => [
                    'name' => $c->getTranslation('name', $locale) ?: $c->getTranslation('name', 'en'),
                    'slug' => $c->slug,
                    'url' => route('projects.index').'?category='.$c->slug,
                    'projects' => $c->projects->map(fn (Project $p) => [
                        'title' => $p->getTranslation('title', $locale) ?: $p->getTranslation('title', 'en'),
                        'slug' => $p->slug,
                        'url' => route('projects.show', ['slug' => $p->slug]),
                        'location' => $p->getTranslation('location', $locale) ?: $p->getTranslation('location', 'en'),
                    ]),
                ]);

            $uncategorizedProjects = Project::where('isActive', true)
                ->whereNull('project_category_id')
                ->latest()
                ->get()
                ->map(fn (Project $p) => [
                    'title' => $p->getTranslation('title', $locale) ?: $p->getTranslation('title', 'en'),
                    'slug' => $p->slug,
                    'url' => route('projects.show', ['slug' => $p->slug]),
                    'location' => $p->getTranslation('location', $locale) ?: $p->getTranslation('location', 'en'),
                ]);

            // 3. News Categories & Articles
            $newsCategories = NewsCategory::orderBy('order_index')
                ->get()
                ->map(fn (NewsCategory $nc) => [
                    'name' => $nc->getTranslation('name', $locale) ?: $nc->getTranslation('name', 'en'),
                    'slug' => $nc->slug,
                    'url' => route('news.index').'?category='.$nc->slug,
                ]);

            $newsArticles = NewsArticle::where('isActive', true)
                ->where('publishedAt', '<=', now())
                ->orderByDesc('publishedAt')
                ->take(30)
                ->get()
                ->map(fn (NewsArticle $n) => [
                    'title' => $n->getTranslation('title', $locale) ?: $n->getTranslation('title', 'en'),
                    'slug' => $n->slug,
                    'url' => route('news.show', ['slug' => $n->slug]),
                    'date' => $n->publishedAt ? $n->publishedAt->format('M d, Y') : $n->created_at->format('M d, Y'),
                    'category' => $n->newsCategory ? ($n->newsCategory->getTranslation('name', $locale) ?: $n->newsCategory->getTranslation('name', 'en')) : ($n->getTranslation('category', $locale) ?: __('News')),
                ]);

            // 4. Job Openings
            $jobs = JobPosting::where('status', JobPostingStatus::OPEN)
                ->with('department')
                ->orderByDesc('created_at')
                ->get()
                ->map(fn (JobPosting $j) => [
                    'title' => $j->getTranslation('title', $locale) ?: $j->getTranslation('title', 'en'),
                    'slug' => $j->slug,
                    'url' => route('careers.show', ['slug' => $j->slug]),
                    'dept' => $j->department ? ($j->department->getTranslation('name', $locale) ?: $j->department->getTranslation('name', 'en')) : __('General'),
                    'location' => $j->getTranslation('location', $locale) ?: $j->getTranslation('location', 'en'),
                ]);

            // 5. Documents
            $hasPublicDocs = Document::publicDocumentsExist();
            $documents = [];
            $documentCategories = [];

            if ($hasPublicDocs) {
                $documentCategories = DocumentCategory::where('isActive', true)
                    ->orderBy('sort_order')
                    ->get()
                    ->map(fn (DocumentCategory $dc) => [
                        'name' => $dc->getTranslation('name', $locale) ?: $dc->getTranslation('name', 'en'),
                        'slug' => $dc->slug,
                    ]);

                $documents = Document::publiclyVisible()
                    ->with('documentCategory')
                    ->latest()
                    ->take(20)
                    ->get()
                    ->map(fn (Document $d) => [
                        'title' => $d->getTranslation('title', $locale) ?: $d->getTranslation('title', 'en'),
                        'slug' => $d->slug,
                        'url' => route('documents.show', ['slug' => $d->slug]),
                        'category' => $d->documentCategory ? ($d->documentCategory->getTranslation('name', $locale) ?: $d->documentCategory->getTranslation('name', 'en')) : ($d->category ?: __('Documents')),
                    ]);
            }

            return [
                'services' => $services->toArray(),
                'projectCategories' => $projectCategories->map(function ($c) {
                    $c['projects'] = is_array($c['projects']) ? $c['projects'] : $c['projects']->toArray();

                    return $c;
                })->toArray(),
                'uncategorizedProjects' => $uncategorizedProjects->toArray(),
                'newsCategories' => $newsCategories->toArray(),
                'newsArticles' => $newsArticles->toArray(),
                'jobs' => $jobs->toArray(),
                'hasPublicDocs' => (bool) $hasPublicDocs,
                'documentCategories' => is_array($documentCategories) ? $documentCategories : (is_object($documentCategories) ? $documentCategories->toArray() : []),
                'documents' => is_array($documents) ? $documents : (is_object($documents) ? $documents->toArray() : []),
            ];
        });

        return view('pages.sitemap', $data);
    }
}
