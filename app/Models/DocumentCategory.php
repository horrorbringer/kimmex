<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class DocumentCategory extends Model
{
    use HasTranslations;

    protected $fillable = ['name', 'slug', 'description', 'parent_id', 'icon', 'sort_order'];

    public $translatable = ['name', 'description'];

    public function parent()
    {
        return $this->belongsTo(DocumentCategory::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(DocumentCategory::class, 'parent_id');
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'document_category_id');
    }
}
