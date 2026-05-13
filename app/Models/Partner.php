<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

use Spatie\Translatable\HasTranslations;

class Partner extends Model
{
    use HasTranslations, HasUuids;

    public $translatable = ['name'];

    protected $fillable = [
        'name',
        'logoUrl',
        'website',
        'type',
        'orderIndex',
        'isActive',
    ];

    protected $casts = [
        'isActive' => 'boolean',
    ];

    protected static function booted()
    {
        static::saved(function () {
            foreach (['en', 'km', 'kh'] as $locale) {
                \Illuminate\Support\Facades\Cache::forget("home_partners_array_{$locale}");
            }
            \Illuminate\Support\Facades\Cache::forget('home_partners_array');
            \Illuminate\Support\Facades\Cache::forget('home_partners_array_v2');
        });

        static::deleted(function () {
            foreach (['en', 'km', 'kh'] as $locale) {
                \Illuminate\Support\Facades\Cache::forget("home_partners_array_{$locale}");
            }
            \Illuminate\Support\Facades\Cache::forget('home_partners_array');
            \Illuminate\Support\Facades\Cache::forget('home_partners_array_v2');
        });
    }
}
