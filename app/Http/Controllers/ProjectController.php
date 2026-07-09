<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Support\PublicStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function show(Request $request, string $slug): View|RedirectResponse
    {
        $locale = app()->getLocale();
        $contentLocale = $locale === 'kh' ? 'km' : $locale;
        $defaultProjectImage = '/images/webp/projects/Thumbnail-1.webp';

        $resolveContent = function (Project $projectDb, string $field, array $fallbackLocales = ['en']) use ($contentLocale): string {
            $locales = array_values(array_unique(array_filter(array_merge([$contentLocale], $fallbackLocales))));

            foreach ($locales as $candidateLocale) {
                $candidate = $projectDb->getTranslation($field, $candidateLocale);

                if (! is_string($candidate)) {
                    continue;
                }

                $candidate = trim($candidate);
                if ($candidate !== '') {
                    return $candidate;
                }
            }

            return '';
        };

        $project = Cache::remember("project_show_data_{$slug}_{$contentLocale}", now()->addHours(12), function () use ($slug, $contentLocale, $resolveContent, $defaultProjectImage): ?array {
            $projectDb = Project::where('isActive', true)
                ->where('slug', $slug)
                ->with(['projectCategory', 'images'])
                ->first();

            if (! $projectDb) {
                return null;
            }

            return [
                'id'             => $projectDb->slug,
                'slug'           => $projectDb->slug,
                'title'          => $resolveContent($projectDb, 'title'),
                'type'           => $projectDb->projectCategory
                    ? $projectDb->projectCategory->localizedName($contentLocale)
                    : ($projectDb->category ?: __('Infrastructure')),
                'location'       => $resolveContent($projectDb, 'location'),
                'status'         => $projectDb->status?->getLabel() ?: __('Completed'),
                'date'           => $projectDb->completionDate?->format('F Y') ?: __('Oct 2026'),
                'client'         => $projectDb->client ?: __('Ministry of Economy and Finance'),
                'built_area'     => $projectDb->scale ?: __('50,000 SQM'),
                'contract_value' => __('Contact for Details'),
                'year'           => $projectDb->timeline ?: __('2023 - 2026'),
                'heroImage'      => PublicStorage::urlIfExists($projectDb->heroImage, $defaultProjectImage),

                'narrative' => [
                    'description'            => $resolveContent($projectDb, 'description'),
                    'background'             => $resolveContent($projectDb, 'background'),
                    'objectives'             => $resolveContent($projectDb, 'objectives'),
                    'design_concept'         => $resolveContent($projectDb, 'designConcept'),
                    'engineering_narrative'  => $resolveContent($projectDb, 'engineeringNarrative'),
                ],

                'scope' => $resolveContent($projectDb, 'scopeContributions'),

                'images' => $projectDb->images
                    ->map(fn ($img) => PublicStorage::urlIfExists($img->url))
                    ->filter()
                    ->values()
                    ->toArray(),

                'related' => Project::where('isActive', true)
                    ->where('id', '!=', $projectDb->id)
                    ->where('status', $projectDb->status)
                    ->with('projectCategory')
                    ->take(3)
                    ->get()
                    ->map(fn (Project $p) => [
                        'id'    => $p->slug,
                        'title' => $resolveContent($p, 'title'),
                        'type'  => $p->projectCategory
                            ? $p->projectCategory->localizedName($contentLocale)
                            : ($p->category ?: __('Infrastructure')),
                        'image' => PublicStorage::urlIfExists($p->heroImage, '/images/webp/projects/Thumbnail-5.webp'),
                    ])->toArray(),
            ];
        });

        if (! $project) {
            return redirect()->route('projects.index')
                ->with('flash_warning', __('The project you were looking for could not be found.'));
        }

        $project['heroImage'] = $project['heroImage'] ?: $defaultProjectImage;

        return view('pages.projects.show', compact('project', 'contentLocale', 'defaultProjectImage'));
    }
}
