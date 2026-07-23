<?php

namespace App\Models;

use App\Models\Concerns\DeletesPublicUploads;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Spatie\Translatable\HasTranslations;

class Milestone extends Model
{
    use DeletesPublicUploads, HasTranslations, HasUuids;

    public $translatable = ['title', 'description', 'detailed_description'];

    protected $fillable = [
        'year',
        'title',
        'description',
        'detailed_description',
        'image',
        'sortOrder',
        'isActive',
    ];

    protected array $publicUploadAttributes = ['image'];

    protected $casts = [
        'isActive' => 'boolean',
    ];

    protected static function booted()
    {
        static::saved(fn () => static::clearMilestoneCache());
        static::deleted(fn () => static::clearMilestoneCache());
    }

    protected static function clearMilestoneCache()
    {
        Cache::forget('about_milestones_data_en');
        Cache::forget('about_milestones_data_kh');
        Cache::forget('about_milestones_data_km');
        Cache::forget('home_milestones_en');
        Cache::forget('home_milestones_kh');
        Cache::forget('home_milestones_km');
        Cache::forget('about_page_en');
        Cache::forget('about_page_kh');
        Cache::forget('about_page_km');
    }
}
