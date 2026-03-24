<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ProjectImage extends Model
{
    use HasUuids;

    protected $fillable = ['url', 'caption', 'projectId'];

    public function project()
    {
        return $this->belongsTo(Project::class, 'projectId');
    }
}
