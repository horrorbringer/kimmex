<?php

namespace App\Observers;

use App\Jobs\GenerateSeoMeta;
use Illuminate\Database\Eloquent\Model;

class SeoMetaObserver
{
    /**
     * Handle the "saved" event.
     *
     * Dispatches the SEO meta generation job when metaTitle or metaDescription
     * is empty for the 'en' locale.
     */
    public function saved(Model $model): void
    {
        // Only process models that have metaTitle and metaDescription as translatable
        if (! property_exists($model, 'translatable') || ! is_array($model->translatable)) {
            return;
        }

        if (! in_array('metaTitle', $model->translatable) || ! in_array('metaDescription', $model->translatable)) {
            return;
        }

        $metaTitle = $model->getTranslation('metaTitle', 'en', false);
        $metaDescription = $model->getTranslation('metaDescription', 'en', false);

        if (empty($metaTitle) || empty($metaDescription)) {
            GenerateSeoMeta::dispatch(
                get_class($model),
                $model->getKey(),
            )->onQueue('default');
        }
    }
}
