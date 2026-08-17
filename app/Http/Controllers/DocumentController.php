<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Support\PublicStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DocumentController extends Controller
{
    public function index(): View
    {
        abort_unless(Document::publicDocumentsExist(), 404);

        return view('pages.documents');
    }

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
                'id' => $d->id,
                'document_category_id' => $d->document_category_id,
                'title' => $d->getTranslation('title', $locale) ?: $d->getTranslation('title', 'en'),
                'description' => $d->getTranslation('description', $locale) ?: $d->getTranslation('description', 'en'),
                'thumbnailUrl' => $d->thumbnailUrl,
                'is_featured' => $d->is_featured,
                'fileUrl' => $d->fileUrl,
                'fileType' => $d->fileType,
                'fileSize' => $d->fileSize,
                'downloadCount' => $d->downloadCount,
                'date' => $d->created_at->format('M d, Y'),
                'created_at_formatted' => $d->created_at->format('M Y'),
                'categoryName' => $d->documentCategory
                    ? ($d->documentCategory->getTranslation('name', $locale) ?: $d->documentCategory->getTranslation('name', 'en'))
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
                ->map(fn (Document $r): array => [
                    'slug' => $r->slug,
                    'title' => $r->getTranslation('title', $locale) ?: $r->getTranslation('title', 'en'),
                    'thumbnailUrl' => $r->thumbnailUrl,
                    'fileType' => $r->fileType,
                    'fileSize' => $r->fileSize,
                    'date' => $r->created_at->format('M Y'),
                    'categoryName' => $r->documentCategory
                        ? ($r->documentCategory->getTranslation('name', $locale) ?: $r->documentCategory->getTranslation('name', 'en'))
                        : $r->category,
                ])->all();
        });

        $categoryName = $doc['categoryName'] ?? __('Documents');
        $thumbnailUrl = PublicStorage::urlIfExists($doc['thumbnailUrl'] ?? null);
        $fileUrl = PublicStorage::urlIfExists($doc['fileUrl'] ?? null);

        $isExternal = filled($fileUrl) && Str::startsWith($fileUrl, ['http://', 'https://']);
        $host = $isExternal ? (parse_url($fileUrl, PHP_URL_HOST) ?? '') : '';
        $cloudProvider = null;
        $embedUrl = null;

        if ($isExternal) {
            if (str_contains($host, 'drive.google.com') || str_contains($host, 'docs.google.com')) {
                $cloudProvider = 'Google Drive';
                if (preg_match('/\/file\/d\/([a-zA-Z0-9_-]+)/', $fileUrl, $matches)) {
                    $embedUrl = "https://drive.google.com/file/d/{$matches[1]}/preview";
                }
            } elseif (str_contains($host, 'dropbox.com')) {
                $cloudProvider = 'Dropbox';
            } elseif (str_contains($host, 'onedrive') || str_contains($host, 'sharepoint')) {
                $cloudProvider = 'Microsoft OneDrive';
            }
        } elseif (filled($fileUrl) && str_ends_with(strtolower($fileUrl), '.pdf')) {
            $embedUrl = $fileUrl;
        }

        return view('pages.documents.show', compact(
            'doc',
            'relatedDocs',
            'categoryName',
            'thumbnailUrl',
            'fileUrl',
            'isExternal',
            'host',
            'cloudProvider',
            'embedUrl',
            'locale',
            'slug'
        ));
    }
}
