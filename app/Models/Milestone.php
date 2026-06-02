<?php

namespace App\Models;

use App\Models\Concerns\DeletesPublicUploads;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Spatie\Translatable\HasTranslations;

class Milestone extends Model
{
    use HasUuids, HasTranslations, DeletesPublicUploads;

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
        \Illuminate\Support\Facades\Cache::forget('about_milestones_data_en');
        \Illuminate\Support\Facades\Cache::forget('about_milestones_data_kh');
        \Illuminate\Support\Facades\Cache::forget('about_milestones_data_km');
    }
}
