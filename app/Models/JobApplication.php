<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
 
class JobApplication extends Model
{
    use HasUuids;
    protected $fillable = [
        'jobId',
        'applicantName',
        'email',
        'phone',
        'resumeUrl',
        'coverLetter',
        'status',
        'submittedAt',
    ];

    public function job(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(JobPosting::class, 'jobId');
    }
}
