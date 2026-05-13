<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Spatie\Translatable\HasTranslations;

class Document extends Model
{
    use HasUuids, HasTranslations;

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

    protected $casts = [
        'isPublic' => 'boolean',
        'is_featured' => 'boolean',
        'isActive' => 'boolean',
    ];

    protected static function booted()
    {
        static::saved(function () {
            foreach (['en', 'km'] as $locale) {
                Cache::forget("news_sidebar_documents_{$locale}");
            }
        });

        static::deleted(function () {
            foreach (['en', 'km'] as $locale) {
                Cache::forget("news_sidebar_documents_{$locale}");
            }
        });
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
