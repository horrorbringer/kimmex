<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Document;
use App\Models\DocumentCategory;

class DocumentLibrary extends Component
{
    use WithPagination;

    public $search = '';
    public $activeTabId = 'all';

    public function updatingSearch()
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
        $categories = DocumentCategory::where('isActive', true)->orderBy('name->en')->get();

        $query = Document::with('documentCategory')
            ->where('isActive', true)
            ->where(function ($q) {
                // Adjust if your isPublic defaults to true or 1.
                $q->whereNull('isPublic')->orWhere('isPublic', true)->orWhere('isPublic', 1);
            })
            ->whereHas('documentCategory', function ($q) {
                $q->where('isActive', true);
            });

        if ($this->activeTabId !== 'all') {
            $query->where('document_category_id', $this->activeTabId);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title->en', 'like', '%' . $this->search . '%')
                    ->orWhere('title->km', 'like', '%' . $this->search . '%')
                    ->orWhere('description->en', 'like', '%' . $this->search . '%')
                    ->orWhere('description->km', 'like', '%' . $this->search . '%');
            });
        }

        return view('livewire.document-library', [
            'documents' => $query->orderByDesc('is_featured')->latest()->paginate(12),
            'categories' => $categories,
            'totalDocuments' => Document::where('isActive', true)->whereHas('documentCategory', fn($q) => $q->where('isActive', true))->count(),
            'totalCategories' => DocumentCategory::where('isActive', true)->count(),
        ]);
    }
}
