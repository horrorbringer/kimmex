<?php

namespace App\Models;

use App\Models\Concerns\DeletesPublicUploads;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Translatable\HasTranslations;

class Service extends Model
{
    use DeletesPublicUploads, HasTranslations, HasUuids, LogsActivity;

    public $translatable = ['title', 'summary', 'description', 'metaTitle', 'metaDescription'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll()->logOnlyDirty();
    }

    protected $fillable = [
        'title',
        'slug',
        'icon',
        'summary',
        'description',
        'image',
        'features',
        'orderIndex',
        'isActive',
        'metaTitle',
        'metaDescription',
    ];

    protected array $publicUploadAttributes = ['image'];

    protected $casts = [
        'features' => 'array',
        'isActive' => 'boolean',
    ];

    protected static function booted()
    {
        static::saved(function ($service) {
            $slugs = array_filter(array_unique([
                $service->slug,
                $service->getOriginal('slug'),
            ]));

            foreach (['en', 'km', 'kh'] as $locale) {
                Cache::forget('nav_services_'.$locale);
                Cache::forget('home_services_array_'.$locale);
                foreach ($slugs as $slug) {
                    Cache::forget("service_show_data_{$slug}_{$locale}");
                }
            }
            Cache::forget('services_index_data');
        });

        static::deleted(function ($service) {
            $slugs = array_filter(array_unique([
                $service->slug,
                $service->getOriginal('slug'),
            ]));

            foreach (['en', 'km', 'kh'] as $locale) {
                Cache::forget('nav_services_'.$locale);
                Cache::forget('home_services_array_'.$locale);
                foreach ($slugs as $slug) {
                    Cache::forget("service_show_data_{$slug}_{$locale}");
                }
            }
            Cache::forget('services_index_data');
        });
    }
}
