<?php

namespace App\Observers;

use App\Models\Sector;
use Illuminate\Support\Facades\Cache;

class SectorObserver
{
    /**
     * Cache keys that depend on Sector records.
     *
     * @var array<int, string>
     */
    protected array $cacheKeys = [
        'services_sectors_array_en',
        'services_sectors_array_km',
        'services_sectors_array_kh',
        'services_sectors_data',
    ];

    public function saved(Sector $sector): void
    {
        $this->bustCache();
    }

    public function deleted(Sector $sector): void
    {
        $this->bustCache();
    }

    protected function bustCache(): void
    {
        foreach ($this->cacheKeys as $key) {
            Cache::forget($key);
        }
    }
}
