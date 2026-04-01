<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

use Spatie\Translatable\HasTranslations;

class OrgUnit extends Model
{
    use HasTranslations, HasUuids;

    public $translatable = ['title'];

    protected $fillable = [
        'title',
        'type',
        'parentId',
        'employeeId',
        'departmentId',
        'orderIndex',
    ];

    public function parent(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(OrgUnit::class, 'parentId');
    }

    public function employee(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employeeId');
    }

    public function department(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Department::class, 'departmentId');
    }

    public function getPath(): string
    {
        $path = [$this->getTranslation('title', app()->getLocale())];
        $parent = $this->parent;

        while ($parent) {
            array_unshift($path, $parent->getTranslation('title', app()->getLocale()));
            $parent = $parent->parent;
        }

        return implode(' > ', $path);
    }
}
