<?php

namespace App\Http\Controllers;

use App\Enums\ProjectStatus;
use App\Models\NewsArticle;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Support\PublicStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(Request $request): View
    {
        $locale = app()->getLocale();
        $contentLocale = $locale === 'kh' ? 'km' : $locale;
        $fallbackImage = '/images/webp/projects/Thumbnail-5.webp';

        // Validate filter inputs
        $year = $request->query('year');
        $status = $request->query('status');
        $categoryId = $request->query('category_id');
        $categorySlug = $request->string('category')->trim()->toString();

        // Load the category once, so both old category_id URLs and readable category slugs work.
        $projectCategories = ProjectCategory::where('isActive', true)->get();
        $selectedCategory = $categorySlug !== ''
            ? $projectCategories->firstWhere('slug', $categorySlug)
            : $projectCategories->firstWhere('id', $categoryId);

        if ($selectedCategory) {
            $categoryId = $selectedCategory->id;
            $categorySlug = $selectedCategory->slug;
        }

        $selectedCategoryName = $selectedCategory?->localizedName($contentLocale);

        // Build the query with server-side filters
        $query = Project::where('isActive', true)->with('projectCategory');

        if ($year && is_numeric($year)) {
            $query->whereYear('completionDate', (int) $year);
        }

        if ($status && in_array(strtoupper($status), array_column(ProjectStatus::cases(), 'value'))) {
            $query->where('status', strtoupper($status));
        }

        if ($categoryId) {
            $query->where('project_category_id', $categoryId);
        }

        $projectsDb = $query->orderBy('created_at', 'desc')->get();

        $categoryLookup = $projectCategories->flatMap(function ($category) {
            return [
                strtolower($category->slug) => $category,
                strtolower(Str::slug($category->getTranslation('name', 'en', false))) => $category,
            ];
        });

        $localizedCategoryName = function ($project) use ($contentLocale, $categoryLookup) {
            if ($project->projectCategory) {
                return $project->projectCategory->localizedName($contentLocale);
            }

            $legacyCategory = trim((string) $project->category);
            $legacyKey = strtolower(Str::slug($legacyCategory));
            $matchedCategory = $categoryLookup->get(strtolower($legacyCategory)) ?: $categoryLookup->get($legacyKey);

            return $matchedCategory
                ? $matchedCategory->localizedName($contentLocale)
                : ($legacyCategory ? __(Str::headline($legacyCategory)) : __('General'));
        };

        // Build filter option lists (always from full dataset for consistent UI)
        $allProjects = Project::where('isActive', true)->with('projectCategory')->orderBy('created_at', 'desc')->get();

        $categories = $allProjects->map($localizedCategoryName)->unique()->sort()->values()->toArray();
        $locations = $allProjects->map(fn ($p) => $p->getTranslation('location', $contentLocale))->filter()->unique()->sort()->values()->toArray();
        $availableStatusValues = $allProjects
            ->map(fn (Project $project): ?string => $project->status?->value)
            ->filter()
            ->unique();
        $statusOptions = collect(ProjectStatus::cases())
            ->filter(fn (ProjectStatus $status): bool => $availableStatusValues->contains($status->value))
            ->map(fn (ProjectStatus $status): array => ['value' => $status->value, 'label' => $status->getLabel()])
            ->values()
            ->all();

        // Extract available years from completionDate
        $years = $allProjects
            ->filter(fn ($p) => $p->completionDate !== null)
            ->map(fn ($p) => $p->completionDate->year)
            ->unique()
            ->sortDesc()
            ->values()
            ->toArray();

        // Build category options with id and name
        $categoryOptions = $projectCategories->map(fn ($cat) => [
            'id' => $cat->id,
            'slug' => $cat->slug,
            'name' => $cat->localizedName($contentLocale),
        ])->sortBy('name')->values()->toArray();

        // Map projects to view data
        $projects = $projectsDb->map(function ($p) use ($fallbackImage, $contentLocale, $localizedCategoryName) {
            return [
                'id' => $p->slug,
                'title' => $p->getTranslation('title', $contentLocale),
                'featured' => (bool) $p->isFeatured,
                'location' => $p->getTranslation('location', $contentLocale),
                'type' => $localizedCategoryName($p),
                'category_id' => $p->project_category_id,
                'status' => $p->status ? $p->status->getLabel() : __('Unknown'),
                'status_value' => $p->status?->value,
                'year' => $p->completionDate?->year,
                'image' => PublicStorage::urlIfExists($p->heroImage, $fallbackImage),
                'summary' => strip_tags($p->getTranslation('description', $contentLocale)),
            ];
        })->toArray();

        // Fallback for empty DB
        if (count($projects) === 0 && ! $year && ! $status && ! $categoryId) {
            $projects = [
                [
                    'id' => 'mef',
                    'title' => __('Ministry of Economy Building'),
                    'featured' => true,
                    'location' => __('Phnom Penh'),
                    'type' => __('Government'),
                    'category_id' => null,
                    'status' => __('Completed'),
                    'status_value' => 'COMPLETED',
                    'year' => 2024,
                    'image' => '/images/webp/projects/Thumbnail-1.webp',
                    'summary' => __('Kimmex built legacy facility.'),
                ],
            ];
        }

        return view('pages.projects.index', compact(
            'projects',
            'categories',
            'locations',
            'statusOptions',
            'categoryOptions',
            'years',
            'year',
            'status',
            'categoryId',
            'categorySlug',
            'selectedCategoryName',
        ));
    }

    public function show(Request $request, string $slug): View|RedirectResponse
    {
        $locale = app()->getLocale();
        $contentLocale = $locale === 'kh' ? 'km' : $locale;
        $defaultProjectImage = '/images/webp/projects/Thumbnail-1.webp';

        $resolveContent = function (Project $projectDb, string $field, array $fallbackLocales = ['en']) use ($contentLocale): string {
            $locales = array_values(array_unique(array_filter(array_merge([$contentLocale], $fallbackLocales))));

            foreach ($locales as $candidateLocale) {
                $candidate = $projectDb->getTranslation($field, $candidateLocale);

                if (! is_string($candidate)) {
                    continue;
                }

                $candidate = trim($candidate);
                if ($candidate !== '') {
                    return $candidate;
                }
            }

            return '';
        };

        $project = Cache::remember("project_show_data_{$slug}_{$contentLocale}", now()->addHours(12), function () use ($slug, $contentLocale, $resolveContent, $defaultProjectImage): ?array {
            $projectDb = Project::where('isActive', true)
                ->where('slug', $slug)
                ->with(['projectCategory', 'images'])
                ->first();

            if (! $projectDb) {
                return null;
            }

            return [
                'id' => $projectDb->slug,
                'slug' => $projectDb->slug,
                'title' => $resolveContent($projectDb, 'title'),
                'type' => $projectDb->projectCategory
                    ? $projectDb->projectCategory->localizedName($contentLocale)
                    : ($projectDb->category ?: __('Infrastructure')),
                'location' => $resolveContent($projectDb, 'location'),
                'status' => $projectDb->status?->getLabel() ?: __('Completed'),
                'date' => $projectDb->completionDate?->format('F Y') ?: __('Oct 2026'),
                'client' => $projectDb->client ?: __('Ministry of Economy and Finance'),
                'built_area' => $projectDb->scale ?: __('50,000 SQM'),
                'contract_value' => __('Contact for Details'),
                'year' => $projectDb->timeline ?: __('2023 - 2026'),
                'heroImage' => PublicStorage::urlIfExists($projectDb->heroImage, $defaultProjectImage),

                'narrative' => [
                    'description' => $resolveContent($projectDb, 'description'),
                    'background' => $resolveContent($projectDb, 'background'),
                    'objectives' => $resolveContent($projectDb, 'objectives'),
                    'design_concept' => $resolveContent($projectDb, 'designConcept'),
                    'engineering_narrative' => $resolveContent($projectDb, 'engineeringNarrative'),
                ],

                'scope' => $resolveContent($projectDb, 'scopeContributions'),

                'images' => $projectDb->images
                    ->map(fn ($img) => PublicStorage::urlIfExists($img->url))
                    ->filter()
                    ->values()
                    ->toArray(),

                'related' => Project::where('isActive', true)
                    ->where('id', '!=', $projectDb->id)
                    ->where('status', $projectDb->status)
                    ->with('projectCategory')
                    ->take(3)
                    ->get()
                    ->map(fn (Project $p) => [
                        'id' => $p->slug,
                        'title' => $resolveContent($p, 'title'),
                        'type' => $p->projectCategory
                            ? $p->projectCategory->localizedName($contentLocale)
                            : ($p->category ?: __('Infrastructure')),
                        'image' => PublicStorage::urlIfExists($p->heroImage, '/images/webp/projects/Thumbnail-5.webp'),
                    ])->toArray(),

                'newsArticles' => $projectDb->newsArticles()
                    ->where('isActive', true)
                    ->where('publishedAt', '<=', now())
                    ->orderByDesc('publishedAt')
                    ->get()
                    ->map(fn (NewsArticle $a) => [
                        'slug' => $a->slug,
                        'title' => $a->getTranslation('title', $contentLocale),
                        'category' => $a->getTranslation('category', $contentLocale) ?: __('Updates'),
                        'coverImage' => PublicStorage::urlIfExists($a->coverImage, ''),
                        'publishedAt' => $a->publishedAt?->format('M d, Y'),
                    ])
                    ->toArray(),
            ];
        });

        if (! $project) {
            return redirect()->route('projects.index')
                ->with('flash_warning', __('The project you were looking for could not be found.'));
        }

        $project['heroImage'] = $project['heroImage'] ?: $defaultProjectImage;

        return view('pages.projects.show', compact('project', 'contentLocale', 'defaultProjectImage'));
    }
}
