<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Support\PublicStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class DocumentController extends Controller
{
    public function show(Request $request, string $slug): View|RedirectResponse
    {
        $locale = app()->getLocale();

        $doc = Cache::remember("document_show_data_{$slug}_{$locale}", now()->addHours(12), function () use ($slug, $locale): ?array {
            $d = Document::with('documentCategory')
                ->publiclyVisible()
                ->where('slug', $slug)
                ->first();

            if (! $d) {
                return null;
            }

            return [
                'id'                    => $d->id,
                'document_category_id'  => $d->document_category_id,
                'title'                 => $d->getTranslation('title', $locale),
                'description'           => $d->getTranslation('description', $locale),
                'thumbnailUrl'          => $d->thumbnailUrl,
                'is_featured'           => $d->is_featured,
                'fileUrl'               => $d->fileUrl,
                'fileType'              => $d->fileType,
                'fileSize'              => $d->fileSize,
                'downloadCount'         => $d->downloadCount,
                'date'                  => $d->created_at->format('M Y'),
                'created_at_formatted'  => $d->created_at->format('M Y'),
                'categoryName'          => $d->documentCategory
                    ? $d->documentCategory->getTranslation('name', $locale)
                    : $d->category,
            ];
        });

        if (! $doc) {
            return redirect()->route('documents')
                ->with('flash_warning', __('The document you were looking for could not be found.'));
        }

        $relatedDocs = Cache::remember("document_related_{$doc['id']}_{$locale}", now()->addHours(12), function () use ($doc, $locale): array {
            return Document::with('documentCategory')
                ->publiclyVisible()
                ->where('document_category_id', $doc['document_category_id'])
                ->where('id', '!=', $doc['id'])
                ->latest()
                ->take(3)
                ->get()
                ->map(fn (Document $r) => [
                    'slug'         => $r->slug,
                    'title'        => $r->getTranslation('title', $locale),
                    'thumbnailUrl' => $r->thumbnailUrl,
                    'fileType'     => $r->fileType,
                    'fileSize'     => $r->fileSize,
                    'categoryName' => $r->documentCategory
                        ? $r->documentCategory->getTranslation('name', $locale)
                        : $r->category,
                ])->all();
        });

        $categoryName = $doc['categoryName'];
        $thumbnailUrl = PublicStorage::urlIfExists($doc['thumbnailUrl']);

        return view('pages.documents.show', compact('doc', 'relatedDocs', 'categoryName', 'thumbnailUrl', 'locale'));
    }
}
