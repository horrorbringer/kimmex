<?php

namespace App\Observers;

use App\Jobs\GenerateSitemap;
use App\Models\Service;
use Illuminate\Support\Facades\Cache;

class ServiceObserver
{
    /**
     * Cache keys that depend on Service records.
     *
     * @var array<int, string>
     */
    protected array $cacheKeys = [
        'services_index_data',
        'home_services_array_v2_en',
        'home_services_array_v2_km',
        'nav_services_en',
        'nav_services_km',
    ];

    public function saved(Service $service): void
    {
        $this->bustCache($service);
        GenerateSitemap::dispatch()->onQueue('default');
    }

    public function deleted(Service $service): void
    {
        $this->bustCache($service);
        GenerateSitemap::dispatch()->onQueue('default');
    }

    protected function bustCache(Service $service): void
    {
        foreach ($this->cacheKeys as $key) {
            Cache::forget($key);
        }

        // Also clear individual service show cache for all locales if slug is set
        if ($service->slug) {
            Cache::forget("service_show_data_{$service->slug}_en");
            Cache::forget("service_show_data_{$service->slug}_km");
            Cache::forget("service_show_data_{$service->slug}_kh");
        }
    }
}
