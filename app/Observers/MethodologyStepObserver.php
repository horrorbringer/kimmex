<?php

namespace App\Observers;

use App\Models\MethodologyStep;
use Illuminate\Support\Facades\Cache;

class MethodologyStepObserver
{
    /**
     * Cache keys that depend on MethodologyStep records.
     *
     * @var array<int, string>
     */
    protected array $cacheKeys = [
        'services_process_array_en',
        'services_process_array_km',
        'services_process_array_kh',
        'process_index_array_en',
        'process_index_array_km',
    ];

    public function saved(MethodologyStep $step): void
    {
        $this->bustCache();
    }

    public function deleted(MethodologyStep $step): void
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
