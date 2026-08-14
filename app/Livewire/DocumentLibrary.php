<?php

namespace App\Livewire;

use App\Models\Document;
use App\Models\DocumentCategory;
use Livewire\Component;
use Livewire\WithPagination;

class DocumentLibrary extends Component
{
    use WithPagination;

    public $search = '';

    public $activeTabId = 'all';

    public $sortBy = 'latest';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSortBy()
    {
        $this->resetPage();
    }

    public function setTab($tabId)
    {
        $this->activeTabId = $tabId;
        $this->resetPage();
    }

    public function render()
    {
        $locale = app()->getLocale();

        $categories = DocumentCategory::where('isActive', true)
            ->whereHas('documents', function ($query) {
                $query->publiclyVisible();
            })
            ->orderBy('sort_order')
            ->get()
            ->map(fn ($cat) => [
                'id' => (string) $cat->id,
                'name' => $cat->getTranslation('name', $locale) ?: $cat->getTranslation('name', 'en'),
            ])
            ->all();

        $query = Document::with('documentCategory')->publiclyVisible();

        if ($this->activeTabId !== 'all') {
            $query->where('document_category_id', $this->activeTabId);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title->en', 'like', '%'.$this->search.'%')
                    ->orWhere('title->km', 'like', '%'.$this->search.'%')
                    ->orWhere('description->en', 'like', '%'.$this->search.'%')
                    ->orWhere('description->km', 'like', '%'.$this->search.'%');
            });
        }

        $query->orderByDesc('is_featured');

        match ($this->sortBy) {
            'oldest' => $query->oldest(),
            'title_asc' => $query->orderBy('title->'.$locale, 'asc'),
            'title_desc' => $query->orderBy('title->'.$locale, 'desc'),
            default => $query->latest(),
        };

        return view('livewire.document-library', [
            'documents' => $query->paginate(12),
            'categories' => $categories,
            'totalDocuments' => Document::publiclyVisible()->count(),
            'totalCategories' => count($categories),
        ]);
    }
}
