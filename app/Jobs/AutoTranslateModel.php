<?php

namespace App\Jobs;

use App\Services\AutoTranslateService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AutoTranslateModel implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    public int $tries   = 2;
    public int $timeout = 60;

    public function __construct(
        public readonly string     $modelClass,
        public readonly int|string $modelId,
        public readonly array      $fields,
        public readonly ?string    $originalEnJson = null, // serialized original EN values
    ) {}

    public function handle(AutoTranslateService $translator): void
    {
        /** @var \Illuminate\Database\Eloquent\Model $model */
        $model = $this->modelClass::find($this->modelId);

        if (! $model) {
            return;
        }

        $originals = $this->originalEnJson ? json_decode($this->originalEnJson, true) : [];
        $changed   = false;

        foreach ($this->fields as $field) {
            $translations = $model->getTranslations($field);
            $currentEn    = $translations['en'] ?? null;

            if (empty($currentEn)) {
                continue;
            }

            $originalEn    = $originals[$field] ?? null;
            $khmerIsEmpty  = empty($translations['km']);
            $englishChanged = $currentEn !== $originalEn;

            $shouldTranslate = $model instanceof \App\Models\ProjectCategory
                ? $khmerIsEmpty
                : ($khmerIsEmpty || $englishChanged);

            if ($shouldTranslate) {
                $translated = $translator->translate($currentEn, 'km');
                if ($translated) {
                    $model->setTranslation($field, 'km', $translated);
                    $changed = true;
                }
            }
        }

        if ($changed) {
            // saveQuietly avoids re-firing the saved event and re-dispatching this job
            $model->saveQuietly();
        }
    }
}
