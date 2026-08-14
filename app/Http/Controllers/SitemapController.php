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
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class SitemapController extends Controller
{
    /**
     * Display human-readable HTML sitemap.
     */
    public function index()
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
