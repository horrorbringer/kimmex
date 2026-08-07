<?php

namespace App\Jobs;

use App\Models\SystemSetting;
use App\Services\AutoTranslateService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class TranslateSystemSettings implements ShouldQueue
{
    use Queueable;

    public int $tries = 2;

    public int $timeout = 120;

    /**
     * @param  array<string, mixed>  $organizationEnglish
     * @param  array<string, mixed>  $brandEnglish
     */
    public function __construct(
        public readonly array $organizationEnglish,
        public readonly array $brandEnglish,
    ) {}

    public function handle(AutoTranslateService $translator): void
    {
        $organization = SystemSetting::get('organization_profile', []);

        if (($organization['en'] ?? []) === $this->organizationEnglish) {
            $existingKm = array_filter($organization['km'] ?? [], fn ($val) => filled($val));
            $autoTranslatedKm = $translator->translateArray($this->organizationEnglish, [], 'km');
            $organization['km'] = array_merge($autoTranslatedKm, $existingKm);
            SystemSetting::set('organization_profile', $organization);
        }

        $brand = SystemSetting::get('brand_identity', []);

        if (($brand['en'] ?? []) === $this->brandEnglish) {
            $existingKm = array_filter($brand['km'] ?? [], fn ($val) => filled($val));
            $autoTranslatedKm = $translator->translateArray($this->brandEnglish, ['icon', 'image'], 'km');
            $brand['km'] = array_merge($autoTranslatedKm, $existingKm);
            SystemSetting::set('brand_identity', $brand);
        }
    }
}
