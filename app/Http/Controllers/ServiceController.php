<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Service;
use App\Support\PublicStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class ServiceController extends Controller
{
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
                'icon' => $serviceDb->icon,
                'image' => PublicStorage::urlIfExists($serviceDb->image, $fallbackImages[$slug] ?? null),
                'scopeItems' => is_array($serviceDb->features)
                    ? array_map(
                        fn ($f) => ['en' => $f['name'] ?? '', 'kh' => $f['name'] ?? ''],
                        $serviceDb->features,
                    )
                    : [],
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
