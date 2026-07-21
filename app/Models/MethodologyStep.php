<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Spatie\Translatable\HasTranslations;

class MethodologyStep extends Model
{
    use HasTranslations, HasUuids;

    public $translatable = ['title', 'description'];

    protected $fillable = [
        'title',
        'description',
        'icon',
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
                Cache::forget('process_index_array_'.$locale);
                Cache::forget('services_process_array_'.$locale);
            }
        });

        static::deleted(function () {
            foreach (['en', 'km', 'kh'] as $locale) {
                Cache::forget('process_index_array_'.$locale);
                Cache::forget('services_process_array_'.$locale);
            }
        });
    }
}
