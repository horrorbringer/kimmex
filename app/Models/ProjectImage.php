<?php

namespace App\Models;

use App\Models\Concerns\DeletesPublicUploads;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class ProjectImage extends Model
{
    use DeletesPublicUploads, HasUuids;

    protected $fillable = ['url', 'caption', 'projectId', 'sort_order'];

    protected array $publicUploadAttributes = ['url'];

    public function project()
    {
        return $this->belongsTo(Project::class, 'projectId');
    }

    protected static function booted()
    {
        static::saved(function ($image) {
            if ($image->project) {
                foreach (['en', 'km', 'kh'] as $locale) {
                    Cache::forget("project_show_data_{$image->project->slug}_{$locale}");
                }
            }
        });

        static::deleted(function ($image) {
            if ($image->project) {
                foreach (['en', 'km', 'kh'] as $locale) {
                    Cache::forget("project_show_data_{$image->project->slug}_{$locale}");
                }
            }
        });
    }
}
