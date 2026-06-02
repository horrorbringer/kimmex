<?php

namespace App\Models\Concerns;

use App\Support\PublicStorage;

trait DeletesPublicUploads
{
    protected static function bootDeletesPublicUploads(): void
    {
        static::deleted(function ($model) {
            PublicStorage::delete($model->publicUploadPathsForDeletion());
        });
    }

    protected function publicUploadPathsForDeletion(): array
    {
        return collect($this->publicUploadAttributes ?? [])
            ->flatMap(fn (string $attribute) => (array) $this->getAttribute($attribute))
            ->filter()
            ->values()
            ->all();
    }
}
