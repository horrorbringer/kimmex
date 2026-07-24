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
            $organization['km'] = $translator->translateArray($this->organizationEnglish, [], 'km');
            SystemSetting::set('organization_profile', $organization);
        }

        $brand = SystemSetting::get('brand_identity', []);

        if (($brand['en'] ?? []) === $this->brandEnglish) {
            $brand['km'] = $translator->translateArray($this->brandEnglish, ['icon', 'image'], 'km');
            SystemSetting::set('brand_identity', $brand);
        }
    }
}
