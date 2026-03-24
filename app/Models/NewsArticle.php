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

    public $translatable = ['title', 'excerpt', 'content', 'authorName', 'readTime'];

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
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll()->logOnlyDirty();
    }

    public function author(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Employee::class, 'authorId');
    }
}
