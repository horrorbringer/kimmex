<?php

namespace App\Models;

use App\Models\Concerns\DeletesPublicUploads;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Spatie\Translatable\HasTranslations;

class Document extends Model
{
    use HasUuids, HasTranslations, DeletesPublicUploads;

    public $translatable = ['title', 'description'];

    protected $fillable = [
        'title',
        'slug',
        'description',
        'fileUrl',
        'fileSize',
        'fileType',
        'thumbnailUrl',
        'category',
        'document_category_id',
        'departmentId',
        'isPublic',
        'is_featured',
        'downloadCount',
        'isActive',
    ];

    protected array $publicUploadAttributes = ['fileUrl', 'thumbnailUrl'];

    protected $casts = [
        'isPublic' => 'boolean',
        'is_featured' => 'boolean',
        'isActive' => 'boolean',
    ];

    protected static function booted()
    {
        static::saved(function () {
            static::clearPublicDocumentCaches();
        });

        static::deleted(function () {
            static::clearPublicDocumentCaches();
        });
    }

    public function scopePubliclyVisible(Builder $query): Builder
    {
        return $query
            ->where('isActive', true)
            ->where(function (Builder $query) {
                $query
                    ->whereNull('isPublic')
                    ->orWhere('isPublic', true)
                    ->orWhere('isPublic', 1);
            })
            ->whereHas('documentCategory', function (Builder $query) {
                $query->where('isActive', true);
            });
    }

    public static function publicDocumentsExist(): bool
    {
        return Cache::remember('public_documents_available', now()->addHours(12), function () {
            return static::query()->publiclyVisible()->exists();
        });
    }

    public static function clearPublicDocumentCaches(): void
    {
        Cache::forget('public_documents_available');

        foreach (['en', 'km'] as $locale) {
            Cache::forget("news_sidebar_documents_{$locale}");
        }
    }

    public function documentCategory(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(DocumentCategory::class, 'document_category_id');
    }

    public function department(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Department::class, 'departmentId');
    }
}
