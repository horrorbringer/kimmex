<?php

namespace App\Services;

use App\Enums\ProjectStatus;
use App\Models\ProjectCategory;
use App\Models\Service;
use Illuminate\Support\Facades\Cache;

class NavigationService
{
    public const CACHE_TTL_HOURS = 12;

    /**
     * Get categorized project filters for navigation dropdown.
     *
     * @return array{completed: array<int, array{slug: string, name: string}>, ongoing: array<int, array{slug: string, name: string}>}
     */
    public function getNavProjectFilters(?string $locale = null): array
    {
        $lang = $locale ?? app()->getLocale();

        return Cache::remember('nav_project_filters_v1_'.$lang, now()->addHours(self::CACHE_TTL_HOURS), function () use ($lang): array {
            $categoriesForStatus = function (string $status) use ($lang): array {
                return ProjectCategory::where('isActive', true)
                    ->whereHas('projects', fn ($query) => $query
                        ->where('isActive', true)
                        ->where('status', $status))
                    ->get()
                    ->sortBy(fn ($category) => $category->localizedName($lang))
                    ->map(fn ($category): array => [
                        'slug' => $category->slug,
                        'name' => $category->localizedName($lang),
                    ])
                    ->values()
                    ->all();
            };

            return [
                'completed' => $categoriesForStatus(ProjectStatus::COMPLETED->value),
                'ongoing' => $categoriesForStatus(ProjectStatus::ONGOING->value),
            ];
        });
    }

    /**
     * Get active services for navigation dropdown.
     *
     * @return array<int, array{slug: string, title: string}>
     */
    public function getNavServices(?string $locale = null): array
    {
        $lang = $locale ?? app()->getLocale();

        return Cache::remember('nav_services_'.$lang, now()->addHours(self::CACHE_TTL_HOURS), function () use ($lang): array {
            return Service::where('isActive', true)
                ->orderBy('orderIndex')
                ->orderBy('id')
                ->get()
                ->map(fn (Service $svc): array => [
                    'slug' => $svc->slug,
                    'title' => $svc->getTranslation('title', $lang),
                ])
                ->values()
                ->all();
        });
    }

    /**
     * Get active services for the footer.
     *
     * @return array<int, array{slug: string, title: string}>
     */
    public function getFooterServices(?string $locale = null, int $limit = 6): array
    {
        $services = $this->getNavServices($locale);

        return array_slice($services, 0, $limit);
    }
}
