<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Service;
use App\Services\ServicesPageService;
use App\Support\PublicStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function __construct(
        protected ServicesPageService $servicesPageService,
    ) {}

    public function index(): View
    {
        $locale = app()->getLocale();
        $lang = $locale === 'km' ? 'kh' : $locale;
        $services = $this->servicesPageService->getServices();
        $process = $this->servicesPageService->getProcess();
        $sectors = $this->servicesPageService->getSectors($locale);
        $processGridColsClass = $this->servicesPageService->getProcessGridColsClass(count($process));

        return view('pages.services', compact('services', 'process', 'sectors', 'lang', 'processGridColsClass'));
    }

    public function show(Request $request, string $slug): View|RedirectResponse
    {
        $lang = app()->getLocale() === 'km' ? 'kh' : app()->getLocale();

        $fallbackImages = [
            'design-and-build' => '/images/webp/projects/Thumbnail-1.webp',
            'construction' => '/images/webp/projects/Thumbnail-1.webp',
            'project-management' => '/images/webp/projects/Thumbnail-3.webp',
            'consultants' => '/images/webp/projects/Thumbnail-4.webp',
        ];

        $service = Cache::remember("service_show_data_{$slug}_{$lang}", now()->addHours(12), function () use ($slug, $fallbackImages): ?array {
            $serviceDb = Service::where('slug', $slug)->where('isActive', true)->first();

            if (! $serviceDb) {
                return null;
            }

            return [
                'id' => $serviceDb->slug,
                'title' => [
                    'en' => $serviceDb->getTranslation('title', 'en'),
                    'kh' => $serviceDb->getTranslation('title', 'km'),
                ],
                'summary' => [
                    'en' => strip_tags($serviceDb->getTranslation('summary', 'en') ?: $serviceDb->getTranslation('description', 'en')),
                    'kh' => strip_tags($serviceDb->getTranslation('summary', 'km') ?: $serviceDb->getTranslation('description', 'km')),
                ],
                'description' => [
                    'en' => $serviceDb->getTranslation('description', 'en'),
                    'kh' => $serviceDb->getTranslation('description', 'km'),
                ],
                'metaTitle' => [
                    'en' => $serviceDb->getTranslation('metaTitle', 'en'),
                    'kh' => $serviceDb->getTranslation('metaTitle', 'km'),
                ],
                'metaDescription' => [
                    'en' => $serviceDb->getTranslation('metaDescription', 'en'),
                    'kh' => $serviceDb->getTranslation('metaDescription', 'km'),
                ],
                'icon' => $serviceDb->icon,
                'image' => PublicStorage::urlIfExists($serviceDb->image, $fallbackImages[$slug] ?? null),
                'scopeItems' => (function () use ($serviceDb): array {
                    $enFeatures = $serviceDb->getTranslation('features', 'en');
                    $kmFeatures = $serviceDb->getTranslation('features', 'km') ?: $serviceDb->getTranslation('features', 'kh');

                    if (! is_array($enFeatures) || empty($enFeatures)) {
                        $enFeatures = is_array($serviceDb->features) ? $serviceDb->features : [];
                    }
                    if (! is_array($kmFeatures) || empty($kmFeatures)) {
                        $kmFeatures = $enFeatures;
                    }

                    $scopeItems = [];
                    $maxCount = max(count((array) $enFeatures), count((array) $kmFeatures));

                    for ($i = 0; $i < $maxCount; $i++) {
                        $itemEn = $enFeatures[$i] ?? null;
                        $itemKm = $kmFeatures[$i] ?? $itemEn;

                        $nameEn = '';
                        if (is_array($itemEn)) {
                            $nameEn = $itemEn['name'] ?? $itemEn['en'] ?? '';
                        } elseif (is_string($itemEn)) {
                            $nameEn = $itemEn;
                        }

                        $nameKh = '';
                        if (is_array($itemKm)) {
                            $nameKh = $itemKm['name_kh'] ?? $itemKm['name_km'] ?? $itemKm['kh'] ?? $itemKm['km'] ?? $itemKm['name'] ?? '';
                        } elseif (is_string($itemKm)) {
                            $nameKh = $itemKm;
                        }

                        if ($nameEn !== '' || $nameKh !== '') {
                            $transKh = __($nameEn ?: $nameKh, [], 'km');
                            $scopeItems[] = [
                                'en' => $nameEn ?: $nameKh,
                                'kh' => ($nameKh && $nameKh !== $nameEn) ? $nameKh : ($transKh !== ($nameEn ?: $nameKh) ? $transKh : ($nameEn ?: $nameKh)),
                            ];
                        }
                    }

                    return $scopeItems;
                })(),
            ];
        });

        if (! $service) {
            return redirect()->route('services.index')
                ->with('flash_warning', __('The service you were looking for could not be found.'));
        }

        $contentLocale = $lang === 'kh' ? 'km' : $lang;
        $featuredProjects = Cache::remember("service_featured_projects_{$lang}", now()->addHours(6), function () use ($contentLocale): array {
            return Project::query()
                ->where('isActive', true)
                ->with('projectCategory')
                ->orderByDesc('isFeatured')
                ->orderByDesc('completionDate')
                ->limit(2)
                ->get()
                ->map(fn (Project $project): array => [
                    'slug' => $project->slug,
                    'title' => $project->getTranslation('title', $contentLocale) ?: $project->getTranslation('title', 'en'),
                    'category' => $project->projectCategory?->localizedName($contentLocale) ?: ($project->category ?: __('Project')),
                    'location' => $project->getTranslation('location', $contentLocale) ?: $project->getTranslation('location', 'en'),
                    'image' => PublicStorage::urlIfExists($project->heroImage, '/images/project-placeholder.svg'),
                ])
                ->all();
        });

        return view('pages.services.show', compact('service', 'lang', 'slug', 'featuredProjects'));
    }
}
