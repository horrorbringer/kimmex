<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Spatie\Translatable\HasTranslations;

class JobPosting extends Model
{
    use HasTranslations;

    public $translatable = ['title', 'location', 'summary', 'requirements', 'benefits', 'experience', 'salary', 'responsibilities'];

    protected $fillable = [
        'title',
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
