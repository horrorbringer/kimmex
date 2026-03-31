<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

use Spatie\Translatable\HasTranslations;

class JobPosting extends Model
{
    use HasTranslations, HasUuids;

    public $translatable = ['title', 'location', 'summary', 'requirements', 'benefits', 'experience', 'salary', 'responsibilities'];

    protected $fillable = [
        'title',
        'slug',
        'departmentId',
        'location',
        'type',
        'summary',
        'requirements',
        'benefits',
        'closingDate',
        'experience',
        'salary',
        'responsibilities',
        'isActive',
    ];

    public function department(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Department::class, 'departmentId');
    }
}
