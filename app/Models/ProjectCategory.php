<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class ProjectCategory extends Model
{
    use HasTranslations;

    protected $fillable = ['name', 'slug', 'description', 'parent_id'];

    public $translatable = ['name', 'description'];

    public function parent()
    {
        return $this->belongsTo(ProjectCategory::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(ProjectCategory::class, 'parent_id');
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}
