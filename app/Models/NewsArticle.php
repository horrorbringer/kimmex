<?php

namespace App\Models;

use App\Models\Concerns\DeletesPublicUploads;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Cache;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Translatable\HasTranslations;

class NewsArticle extends Model
{
    use DeletesPublicUploads, HasTranslations, HasUuids, LogsActivity;

    public $translatable = ['title', 'excerpt', 'content', 'authorName', 'readTime', 'metaTitle', 'metaDescription', 'category'];

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'coverImage',
        'publishedAt',
        'category',
        'news_category_id',
        'tags',
        'authorId',
        'isFeatured',
        'metaTitle',
        'metaDescription',
        'authorName',
        'gallery',
        'videoUrl',
        'isTrending',
        'readTime',
        'year',
        'isActive',
    ];

    protected array $publicUploadAttributes = ['coverImage', 'gallery'];

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

        foreach (['en', 'km', 'kh'] as $locale) {
            Cache::forget("home_news_array_{$locale}");
            Cache::forget("news_index_data_{$locale}");

            foreach ($slugs as $slug) {
                Cache::forget("news_article_data_{$slug}_{$locale}");
                Cache::forget("news_related_array_{$slug}_{$locale}");
            }
        }
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll()->logOnlyDirty();
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'authorId');
    }

    public function newsCategory(): BelongsTo
    {
        return $this->belongsTo(NewsCategory::class, 'news_category_id');
    }

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'news_article_project');
    }
}
