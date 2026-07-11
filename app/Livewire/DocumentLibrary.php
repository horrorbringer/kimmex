<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Document;
use App\Models\DocumentCategory;
use Illuminate\Support\Facades\Cache;

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
        $categories = Cache::remember('document_library_categories_v2_' . app()->getLocale(), now()->addMinutes(10), function () {
            return DocumentCategory::where('isActive', true)
                ->orderBy('name->en')
                ->get()
                ->map(fn ($cat) => [
                    'id' => (string) $cat->id,
                    'name' => $cat->getTranslation('name', app()->getLocale()),
                ])
                ->all();
        });

        $query = Document::with('documentCategory')->publiclyVisible();

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
            'totalDocuments' => Cache::remember('document_library_total_documents', now()->addMinutes(10), fn () => Document::publiclyVisible()->count()),
            'totalCategories' => Cache::remember('document_library_total_categories', now()->addMinutes(10), fn () => DocumentCategory::where('isActive', true)->count()),
        ]);
    }
}
