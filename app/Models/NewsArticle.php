<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

use Spatie\Translatable\HasTranslations;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

class NewsArticle extends Model
{
    use LogsActivity, HasTranslations, HasUuids;

    public $translatable = ['title', 'excerpt', 'content', 'authorName', 'readTime', 'metaTitle', 'metaDescription', 'category'];

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'coverImage',
        'publishedAt',
        'category',
        'tags',
        'authorId',
        'isFeatured',
        'metaTitle',
        'metaDescription',
        'authorName',
        'gallery',
        'isTrending',
        'readTime',
        'year',
        'isActive',
    ];

    protected $casts = [
        'gallery' => 'array',
        'tags' => 'array',
        'isFeatured' => 'boolean',
        'isTrending' => 'boolean',
        'publishedAt' => 'datetime',
        'isActive' => 'boolean',
    ];

    protected static function booted()
    {
        static::saved(function (NewsArticle $article) {
            $article->forgetFrontendCaches();
        });

        static::deleted(function (NewsArticle $article) {
            $article->forgetFrontendCaches();
        });
    }

    public function forgetFrontendCaches(): void
    {
        $slugs = array_filter(array_unique([
            $this->slug,
            $this->getOriginal('slug'),
        ]));

        foreach (['en', 'km'] as $locale) {
            \Illuminate\Support\Facades\Cache::forget("home_news_array_{$locale}");
            \Illuminate\Support\Facades\Cache::forget("news_index_data_{$locale}");

            foreach ($slugs as $slug) {
                \Illuminate\Support\Facades\Cache::forget("news_article_data_{$slug}_{$locale}");
                \Illuminate\Support\Facades\Cache::forget("news_related_array_{$slug}_{$locale}");
            }
        }
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll()->logOnlyDirty();
    }

    public function author(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Employee::class, 'authorId');
    }
}
