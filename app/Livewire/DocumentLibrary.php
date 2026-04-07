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
    public $activeTab = 'All Types';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function render()
    {
        $categories = DocumentCategory::pluck('name')->toArray();
        array_unshift($categories, 'All Types');

        $query = Document::query()
            ->where(function ($q) {
                // Adjust if your isPublic defaults to true or 1.
                $q->whereNull('isPublic')->orWhere('isPublic', true)->orWhere('isPublic', 1);
            });

        if ($this->activeTab !== 'All Types') {
            $query->whereHas('documentCategory', function ($q) {
                $q->where('name', 'like', '%' . $this->activeTab . '%');
            });
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%')
                  ->orWhere('category', 'like', '%' . $this->search . '%');
            });
        }

        return view('livewire.document-library', [
            'documents' => $query->latest()->paginate(12),
            'tabs' => $categories,
            'totalDocuments' => Document::count(),
            'totalCategories' => DocumentCategory::count(),
        ]);
    }
}
