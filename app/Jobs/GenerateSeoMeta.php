<?php

namespace App\Jobs;

use App\Services\AIGeneratorService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class GenerateSeoMeta implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 30;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected string $modelClass,
        protected string $modelId,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(AIGeneratorService $aiService): void
    {
        /** @var Model $model */
        $model = $this->modelClass::find($this->modelId);

        if (! $model) {
            return;
        }

        $title = $model->getTranslation('title', 'en', false);

        if (empty($title)) {
            return;
        }

        $contentSnippet = $this->getContentSnippet($model);
        $updated = false;

        // Generate metaTitle if blank for 'en'
        $currentMetaTitle = $model->getTranslation('metaTitle', 'en', false);
        if (empty($currentMetaTitle)) {
            $generatedTitle = $aiService->generateContent(
                $title,
                'seo meta title',
                'Generate a concise SEO meta title (max 60 chars) for: ' . $title . '. Return ONLY the meta title text, nothing else. No quotes, no labels, no explanation.',
            );

            if ($generatedTitle) {
                $generatedTitle = Str::limit(trim($generatedTitle), 60, '');
                $model->setTranslation('metaTitle', 'en', $generatedTitle);
                $updated = true;
            }
        }

        // Generate metaDescription if blank for 'en'
        $currentMetaDescription = $model->getTranslation('metaDescription', 'en', false);
        if (empty($currentMetaDescription)) {
            $generatedDescription = $aiService->generateContent(
                $title,
                'seo meta description',
                'Generate a concise SEO meta description (max 160 chars) for: ' . $contentSnippet . '. Return ONLY the meta description text, nothing else. No quotes, no labels, no explanation.',
            );

            if ($generatedDescription) {
                $generatedDescription = Str::limit(trim($generatedDescription), 160, '');
                $model->setTranslation('metaDescription', 'en', $generatedDescription);
                $updated = true;
            }
        }

        if ($updated) {
            $model->saveQuietly();
        }
    }

    /**
     * Get a content snippet from the model for meta description generation.
     */
    protected function getContentSnippet(Model $model): string
    {
        // Try excerpt first, then description, then content
        foreach (['excerpt', 'summary', 'description', 'content'] as $field) {
            if (in_array($field, $model->translatable ?? []) || in_array($field, $model->getFillable())) {
                $value = $model->getTranslation($field, 'en', false) ?? '';
                if (! empty($value)) {
                    // Strip HTML tags and limit length
                    return Str::limit(strip_tags($value), 300, '');
                }
            }
        }

        // Fallback to title
        return $model->getTranslation('title', 'en', false) ?? '';
    }
}
