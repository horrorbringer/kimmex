<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;
use Spatie\Translatable\HasTranslations;

class NewsCategory extends Model
{
    use HasTranslations, HasUuids;

    public array $translatable = ['name', 'description'];

    protected $fillable = [
        'name',
        'slug',
        'description',
        'order_index',
        'is_active',
    ];

    protected $casts = [
        'order_index' => 'integer',
        'is_active' => 'boolean',
    ];

    public function newsArticles(): HasMany
    {
        return $this->hasMany(NewsArticle::class, 'news_category_id');
    }

    protected static function booted(): void
    {
        static::saved(function () {
            Cache::forget('news_categories_list_en');
            Cache::forget('news_categories_list_km');
            Cache::forget('news_categories_list_kh');
            Cache::forget('news_index_data_en');
            Cache::forget('news_index_data_km');
            Cache::forget('news_index_data_kh');
            Cache::forget('home_news_array_en');
            Cache::forget('home_news_array_km');
            Cache::forget('home_news_array_kh');
            Cache::forget('home_news_array_en_news-building-construction');
            Cache::forget('home_news_array_km_news-building-construction');
            Cache::forget('home_news_array_kh_news-building-construction');
            Cache::forget('home_news_array_en_all');
            Cache::forget('home_news_array_km_all');
            Cache::forget('home_news_array_kh_all');
        });

        static::deleted(function () {
            Cache::forget('news_categories_list_en');
            Cache::forget('news_categories_list_km');
            Cache::forget('news_categories_list_kh');
            Cache::forget('news_index_data_en');
            Cache::forget('news_index_data_km');
            Cache::forget('news_index_data_kh');
            Cache::forget('home_news_array_en');
            Cache::forget('home_news_array_km');
            Cache::forget('home_news_array_kh');
            Cache::forget('home_news_array_en_news-building-construction');
            Cache::forget('home_news_array_km_news-building-construction');
            Cache::forget('home_news_array_kh_news-building-construction');
            Cache::forget('home_news_array_en_all');
            Cache::forget('home_news_array_km_all');
            Cache::forget('home_news_array_kh_all');
        });
    }
}
