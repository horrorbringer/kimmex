<?php

namespace App\Models;

use App\Models\Concerns\DeletesPublicUploads;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
 
class JobApplication extends Model
{
    use HasUuids, DeletesPublicUploads;

    protected array $publicUploadAttributes = ['resumeUrl'];
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
