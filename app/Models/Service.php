<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

use Spatie\Translatable\HasTranslations;

class Service extends Model
{
    use HasUuids, LogsActivity, HasTranslations;

    public $translatable = ['title', 'summary', 'description'];

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
    ];

    protected $casts = [
        'features' => 'array',
        'isActive' => 'boolean',
    ];

    protected static function booted()
    {
        static::saved(function () {
            \Illuminate\Support\Facades\Cache::forget('nav_services_en');
            \Illuminate\Support\Facades\Cache::forget('nav_services_kh');
        });

        static::deleted(function () {
            \Illuminate\Support\Facades\Cache::forget('nav_services_en');
            \Illuminate\Support\Facades\Cache::forget('nav_services_kh');
        });
    }
}
