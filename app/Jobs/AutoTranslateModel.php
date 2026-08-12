<?php

namespace App\Jobs;

use App\Models\ProjectCategory;
use App\Observers\CacheBusterObserver;
use App\Services\AutoTranslateService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AutoTranslateModel implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    public int $timeout = 60;

    public function __construct(
        public readonly string $modelClass,
        public readonly int|string $modelId,
        public readonly array $fields,
        public readonly ?string $originalEnJson = null, // serialized original EN values
    ) {}

    public function handle(AutoTranslateService $translator): void
    {
        /** @var Model $model */
        $model = $this->modelClass::find($this->modelId);

        if (! $model) {
            return;
        }

        $originals = $this->originalEnJson ? json_decode($this->originalEnJson, true) : [];
        $changed = false;

        foreach ($this->fields as $field) {
            $translations = $model->getTranslations($field);
            $currentEn = $translations['en'] ?? null;

            if (empty($currentEn)) {
                continue;
            }

            // Rich-editor HTML is safely translated through AutoTranslateService preserving tags
            $isHtml = is_string($currentEn) && $translator->containsHtml($currentEn);

            $originalEn = $originals[$field] ?? null;
            $khmerIsEmpty = empty($translations['km']);
            $englishChanged = $currentEn !== $originalEn;

            $shouldTranslate = $model instanceof ProjectCategory
                ? $khmerIsEmpty
                : ($khmerIsEmpty || $englishChanged);

            if ($shouldTranslate) {
                $translated = is_array($currentEn)
                    ? $translator->translateArray($currentEn, [], 'km')
                    : $translator->translate($currentEn, 'km');
                if ($translated) {
                    $model->setTranslation($field, 'km', $translated);
                    $changed = true;
                }
            }
        }

        if ($changed) {
            // saveQuietly avoids re-firing the saved event and re-dispatching this job
            $model->saveQuietly();

            // Invalidate frontend caches after background translation updates
            app(CacheBusterObserver::class)->saved($model);
        }
    }
}
