<?php

namespace App\Models;

use App\Models\Concerns\DeletesPublicUploads;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

#[Fillable(['name', 'email', 'phone', 'subject', 'message', 'attachment_url', 'ip_address', 'status', 'is_read', 'responded_at'])]
class Inquiry extends Model
{
    use DeletesPublicUploads, HasUuids, LogsActivity;

    protected array $publicUploadAttributes = ['attachment_url'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll()->logOnlyDirty();
    }

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
            'responded_at' => 'datetime',
        ];
    }
}
