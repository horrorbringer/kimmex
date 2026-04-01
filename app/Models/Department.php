<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

use Spatie\Translatable\HasTranslations;

class Department extends Model
{
    use HasTranslations, HasUuids;

    public $translatable = ['name', 'description'];

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    public function orgUnits(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(OrgUnit::class, 'departmentId');
    }

    public function headUnit(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(OrgUnit::class, 'departmentId')
            ->whereIn('type', ['DIRECTOR', 'MANAGER', 'MANAGEMENT'])
            ->orderBy('orderIndex')
            ->with('employee');
    }

    public function jobPostings(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(JobPosting::class, 'departmentId');
    }
}
