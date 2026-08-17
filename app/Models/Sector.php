<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Spatie\Translatable\HasTranslations;

class Sector extends Model
{
    use HasFactory, HasTranslations, HasUuids;

    public $translatable = ['title', 'description'];

    protected $fillable = [
        'title',
        'description',
        'icon',
        'image',
        'orderIndex',
        'isActive',
    ];

    protected $casts = [
        'isActive' => 'boolean',
        'orderIndex' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saved(function () {
            foreach (['en', 'km', 'kh'] as $locale) {
                Cache::forget('services_sectors_array_'.$locale);
            }
            Cache::forget('services_sectors_data');
        });

        static::deleted(function () {
            foreach (['en', 'km', 'kh'] as $locale) {
                Cache::forget('services_sectors_array_'.$locale);
            }
            Cache::forget('services_sectors_data');
        });
    }
}
