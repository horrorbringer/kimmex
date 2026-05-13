<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

use Spatie\Translatable\HasTranslations;

class Testimonial extends Model
{
    use HasTranslations, HasUuids;

    public $translatable = ['clientName', 'clientRole', 'content'];

    protected $fillable = [
        'clientName',
        'clientRole',
        'content',
        'image',
        'rating',
        'orderIndex',
        'isFeatured',
        'isActive',
    ];

    protected $casts = [
        'isFeatured' => 'boolean',
        'isActive' => 'boolean',
    ];

    protected static function booted()
    {
        static::saved(function () {
            foreach (['en', 'km', 'kh'] as $locale) {
                \Illuminate\Support\Facades\Cache::forget("home_testimonials_array_{$locale}");
            }
        });

        static::deleted(function () {
            foreach (['en', 'km', 'kh'] as $locale) {
                \Illuminate\Support\Facades\Cache::forget("home_testimonials_array_{$locale}");
            }
        });
    }
}
