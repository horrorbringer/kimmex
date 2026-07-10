<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Support\PublicStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function show(Request $request, string $slug): View|RedirectResponse
    {
        $lang = app()->getLocale() === 'km' ? 'kh' : app()->getLocale();

        $fallbackImages = [
            'design-and-build'   => '/images/webp/projects/Thumbnail-1.webp',
            'construction'       => '/images/webp/projects/Thumbnail-1.webp',
            'project-management' => '/images/webp/projects/Thumbnail-3.webp',
            'consultants'        => '/images/webp/projects/Thumbnail-4.webp',
        ];

        $service = Cache::remember("service_show_data_{$slug}_{$lang}", now()->addHours(12), function () use ($slug, $fallbackImages): ?array {
            $serviceDb = Service::where('slug', $slug)->where('isActive', true)->first();

            if (! $serviceDb) {
                return null;
            }

            return [
                'id'         => $serviceDb->slug,
                'title'      => [
                    'en' => $serviceDb->getTranslation('title', 'en'),
                    'kh' => $serviceDb->getTranslation('title', 'km'),
                ],
                'desc'       => [
                    'en' => strip_tags($serviceDb->getTranslation('description', 'en')),
                    'kh' => strip_tags($serviceDb->getTranslation('description', 'km')),
                ],
                'image'      => PublicStorage::urlIfExists($serviceDb->image, $fallbackImages[$slug] ?? null),
                'scopeItems' => is_array($serviceDb->features)
                    ? array_map(
                        fn ($f) => ['en' => $f['name'] ?? '', 'kh' => $f['name'] ?? ''],
                        $serviceDb->features,
                    )
                    : [],
            ];
        });

        if (! $service) {
            return redirect()->route('services.index')
                ->with('flash_warning', __('The service you were looking for could not be found.'));
        }

        return view('pages.services.show', compact('service', 'lang', 'slug'));
    }
}
