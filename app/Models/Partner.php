<?php

namespace App\Models;

use App\Models\Concerns\DeletesPublicUploads;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Spatie\Translatable\HasTranslations;

class Partner extends Model
{
    use DeletesPublicUploads, HasTranslations, HasUuids;

    public $translatable = ['name'];

    protected $fillable = [
        'name',
        'logoUrl',
        'website',
        'type',
        'orderIndex',
        'isActive',
    ];

    protected array $publicUploadAttributes = ['logoUrl'];

    protected $casts = [
        'isActive' => 'boolean',
    ];

    protected static function booted()
    {
        static::saved(function () {
            foreach (['en', 'km', 'kh'] as $locale) {
                Cache::forget("home_partners_array_{$locale}");
                Cache::forget("home_partners_array_v2_{$locale}");
                Cache::forget("home_partners_array_v3_{$locale}");
            }
            Cache::forget('home_partners_array');
            Cache::forget('home_partners_array_v2');
        });

        static::deleted(function () {
            foreach (['en', 'km', 'kh'] as $locale) {
                Cache::forget("home_partners_array_{$locale}");
                Cache::forget("home_partners_array_v2_{$locale}");
                Cache::forget("home_partners_array_v3_{$locale}");
            }
            Cache::forget('home_partners_array');
            Cache::forget('home_partners_array_v2');
        });
    }
}
