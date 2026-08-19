<?php

namespace App\Services;

use App\Models\MethodologyStep;
use App\Models\Milestone;
use App\Models\NewsArticle;
use App\Models\Partner;
use App\Models\Project;
use App\Models\Service;
use App\Models\SystemSetting;
use App\Models\Testimonial;
use App\Support\PublicStorage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class HomePageService
{
    public const CACHE_TTL_HOURS = 12;

    /**
     * Get Home About section data.
     */
    public function getAboutData(?string $locale = null): array
    {
        $locale = $locale ?? app()->getLocale();
        $localeKey = $locale === 'kh' ? 'km' : $locale;

        return Cache::remember('home_about_data_'.$localeKey, now()->addHours(self::CACHE_TTL_HOURS), function () use ($localeKey): array {
            $brandProfile = SystemSetting::get('brand_identity', []);
            $orgProfile = SystemSetting::get('organization_profile', []);
            $brand = $brandProfile[$localeKey] ?? ($brandProfile['en'] ?? []);
            $org = $orgProfile[$localeKey] ?? ($orgProfile['en'] ?? []);

            $aboutLargeImage = '/images/webp/projects/Thumbnail-2.webp';
            $aboutTopImage = '/images/webp/projects/Thumbnail-3.webp';
            $aboutBottomImage = '/images/webp/projects/Thumbnail-4.webp';

            return [
                'story' => $brand['company_story'] ?? __("With over 25 years of experience, we have established ourselves as Cambodia's most trusted construction partner, delivering projects that stand the test of time."),
                'tagline' => $org['tagline'] ?? __("Cambodia's Premier Construction Partner"),
                'aboutLargeImage' => $aboutLargeImage,
                'aboutTopImage' => $aboutTopImage,
                'aboutBottomImage' => $aboutBottomImage,
                'aboutLargeImageSrcset' => PublicStorage::localResponsiveSrcset($aboutLargeImage, [320, 640, 960]),
                'aboutTopImageSrcset' => PublicStorage::localResponsiveSrcset($aboutTopImage, [320, 640, 960]),
                'aboutBottomImageSrcset' => PublicStorage::localResponsiveSrcset($aboutBottomImage, [320, 640, 960]),
            ];
        });
    }

    /**
     * Get Hero Carousel slides.
     */
    public function getHeroSlides(?string $locale = null): array
    {
        $locale = $locale ?? app()->getLocale();
        $contentLocale = $locale === 'kh' ? 'km' : $locale;
        $fallbackImage = '/images/webp/projects/Thumbnail-5.webp';

        $featuredProjects = Cache::remember('hero_featured_projects_'.$contentLocale, now()->addHours(6), function () use ($fallbackImage, $contentLocale): array {
            return Project::where('isFeatured', true)
                ->where('isActive', true)
                ->with('projectCategory')
                ->orderByDesc('created_at')
                ->take(5)
                ->get()
                ->map(function (Project $p, int $index) use ($fallbackImage, $contentLocale): array {
                    return [
                        'id' => $index + 1,
                        'image' => PublicStorage::urlIfExists($p->heroImage, $fallbackImage),
                        'subtitle' => $p->projectCategory ? $p->projectCategory->getTranslation('name', $contentLocale) : ($p->category ?: __('Featured Project')),
                        'title' => $p->getTranslation('title', $contentLocale) ?: $p->getTranslation('title', 'en'),
                        'desc' => Str::limit(strip_tags((string) ($p->getTranslation('description', $contentLocale) ?: $p->getTranslation('description', 'en'))), 120),
                        'link' => '/projects/'.$p->slug,
                    ];
                })->toArray();
        });

        if (count($featuredProjects) > 0) {
            $slides = $featuredProjects;
        } else {
            $slides = [
                [
                    'id' => 1,
                    'image' => '/images/webp/hero/hero-1.webp',
                    'subtitle' => __('Government Infrastructure'),
                    'title' => __('Ministry of Economy'),
                    'desc' => __('Over 25 years of excellence in building the future of Cambodia. We deliver high-quality infrastructure.'),
                    'link' => '/projects',
                ],
                [
                    'id' => 2,
                    'image' => '/images/webp/hero/hero-2.webp',
                    'subtitle' => __('Water Infrastructure'),
                    'title' => __('Khleang Toeuk WTP'),
                    'desc' => __('Ensuring clean and accessible water solutions through state-of-the-art treatment facilities and engineering.'),
                    'link' => '/projects',
                ],
                [
                    'id' => 3,
                    'image' => '/images/webp/hero/hero-3.webp',
                    'subtitle' => __('Infrastructure Protection'),
                    'title' => __('Mekong Bank Protection'),
                    'desc' => __('Securing vulnerable riverbanks and developing resilient infrastructure to protect communities and commerce.'),
                    'link' => '/projects',
                ],
            ];
        }

        return array_map(function (array $slide): array {
            $slide['srcset'] = PublicStorage::cloudinaryResponsiveSrcset($slide['image']);

            return $slide;
        }, $slides);
    }

    /**
     * Get Milestones formatted with SVG road geometry calculations.
     */
    public function getMilestonesData(?string $locale = null): array
    {
        $locale = $locale ?? app()->getLocale();
        $contentLocale = $locale === 'kh' ? 'km' : $locale;

        $milestones = Cache::remember('home_milestones_'.$contentLocale, now()->addHours(self::CACHE_TTL_HOURS), function () use ($contentLocale): array {
            return Milestone::query()
                ->where('isActive', true)
                ->orderBy('sortOrder')
                ->get()
                ->map(function (Milestone $milestone, int $index) use ($contentLocale): array {
                    $fallbackImage = '/images/webp/projects/Thumbnail-'.(($index % 6) + 1).'.webp';

                    return [
                        'year' => $milestone->year,
                        'title' => $milestone->getTranslation('title', 'en') ?: $milestone->getTranslation('title', $contentLocale, false),
                        'description' => Str::limit(trim(strip_tags((string) ($milestone->getTranslation('description', 'en') ?: $milestone->getTranslation('description', $contentLocale, false)))), 96),
                        'detail' => $milestone->getTranslation('detailed_description', 'en') ?: $milestone->getTranslation('detailed_description', $contentLocale, false),
                        'image' => PublicStorage::urlIfExists($milestone->image, $fallbackImage),
                    ];
                })
                ->values()
                ->all();
        });

        $milestones = array_map(function (array $milestone): array {
            $milestone['image'] = PublicStorage::optimizedLocalImageUrl($milestone['image']);
            $milestone['imageSrcset'] = PublicStorage::cloudinaryResponsiveSrcset($milestone['image'], [160, 320])
                ?? PublicStorage::localResponsiveSrcset($milestone['image'], [160, 320]);

            return $milestone;
        }, $milestones);

        $roadColors = ['#174EA6', '#296DD3', '#2E8CE0', '#1D9D8E', '#18A957', '#D89D13', '#EC7625', '#CF1C5B'];
        $roadHeight = 600;
        $roadWidth = max(1440, count($milestones) * 280);
        $roadStartX = 48;
        $roadEndX = $roadWidth - 48;
        $roadStops = [];
        $roadPath = '';
        $previousStop = null;

        foreach ($milestones as $index => $milestone) {
            $x = 280 + (($roadWidth - 560) * $index / max(1, count($milestones) - 1));
            $y = $index % 2 === 0 ? 300 : 400;
            $roadStops[] = ['x' => $x, 'y' => $y, 'cardOffset' => $index % 2 === 0 ? -145 : 85];

            if ($previousStop === null) {
                $roadPath = "M{$roadStartX} {$y} L{$x} {$y}";
            } else {
                $controlX = ($previousStop['x'] + $x) / 2;
                $controlY = $index % 2 === 0 ? 500 : 200;
                $roadPath .= " Q{$controlX} {$controlY} {$x} {$y}";
            }

            $previousStop = ['x' => $x, 'y' => $y];
        }

        if ($previousStop !== null) {
            $roadPath .= " L{$roadEndX} {$previousStop['y']}";
        }

        return compact('milestones', 'roadColors', 'roadHeight', 'roadWidth', 'roadStartX', 'roadEndX', 'roadStops', 'roadPath');
    }

    /**
     * Get process methodology steps.
     */
    public function getProcess(?string $locale = null): array
    {
        $locale = $locale ?? app()->getLocale();

        $processes = Cache::remember('process_index_array_'.$locale, now()->addHours(self::CACHE_TTL_HOURS), function () use ($locale): array {
            $processDb = MethodologyStep::where('isActive', true)->orderBy('orderIndex')->get();

            return $processDb->map(function ($step, int $index) use ($locale): array {
                $description = $step->getTranslation('description', $locale) ?: $step->getTranslation('description', 'en');

                return [
                    'step' => str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT),
                    'icon' => $step->icon ?: 'lucide-check-circle',
                    'title' => $step->getTranslation('title', $locale) ?: $step->getTranslation('title', 'en'),
                    'desc' => trim(strip_tags((string) $description)),
                ];
            })->toArray();
        });

        if (empty($processes)) {
            $processes = [
                ['icon' => 'lucide-clipboard-check', 'step' => '01', 'title' => __('Initial Consultation'), 'desc' => __('We meet to understand your goals, timeline, and budget requirements.')],
                ['icon' => 'lucide-ruler', 'step' => '02', 'title' => __('Design & Planning'), 'desc' => __('Our architects and engineers draft blueprints and 3D models.')],
                ['icon' => 'lucide-hammer', 'step' => '03', 'title' => __('Execution'), 'desc' => __('Ground breaks and our professional workforce builds the vision.')],
                ['icon' => 'lucide-check-circle-2', 'step' => '04', 'title' => __('Final Handover'), 'desc' => __('Quality reviews are conducted before we hand over keys.')],
            ];
        }

        return $processes;
    }

    /**
     * Get featured homepage projects.
     */
    public function getProjects(?string $locale = null): array
    {
        $locale = $locale ?? app()->getLocale();
        $fallbackImage = '/images/webp/projects/Thumbnail-5.webp';

        $projects = Cache::remember('home_projects_array_'.$locale, now()->addHours(self::CACHE_TTL_HOURS), function () use ($fallbackImage, $locale): array {
            $projectsDb = Project::where('isActive', true)
                ->with('projectCategory')
                ->orderBy('isFeatured', 'desc')
                ->orderBy('created_at', 'desc')
                ->take(3)
                ->get();

            return $projectsDb->map(function (Project $p) use ($fallbackImage, $locale): array {
                return [
                    'slug' => $p->slug,
                    'image' => PublicStorage::urlIfExists($p->heroImage, $fallbackImage),
                    'type' => $p->projectCategory ? $p->projectCategory->localizedName($locale) : ($p->category ?: __('Infrastructure')),
                    'title' => $p->getTranslation('title', $locale),
                    'location' => $p->getTranslation('location', $locale),
                    'status' => strtoupper($p->status->value ?? $p->status ?? 'COMPLETED'),
                ];
            })->toArray();
        });

        if (empty($projects)) {
            $projects = [
                ['slug' => 'mef', 'image' => '/images/webp/projects/Thumbnail-1.webp', 'type' => __('Government'), 'title' => __('Ministry of Economy Building'), 'location' => __('Phnom Penh'), 'status' => __('COMPLETED')],
                ['slug' => 'water', 'image' => '/images/webp/projects/Thumbnail-2.webp', 'type' => __('Infrastructure'), 'title' => __('Water Treatment Plant'), 'location' => __('Siem Reap'), 'status' => __('COMPLETED')],
                ['slug' => 'bank', 'image' => '/images/webp/projects/Thumbnail-3.webp', 'type' => __('Commercial'), 'title' => __('Commercial Bank HQ'), 'location' => __('Phnom Penh'), 'status' => __('ONGOING')],
            ];
        }

        return array_map(function (array $project): array {
            $project['image'] = PublicStorage::optimizedLocalImageUrl($project['image']);
            $project['imageSrcset'] = PublicStorage::cloudinaryResponsiveSrcset($project['image'], [640, 960, 1440])
                ?? PublicStorage::localResponsiveSrcset($project['image'], [320, 640]);

            return $project;
        }, $projects);
    }

    /**
     * Get featured testimonials.
     */
    public function getTestimonials(?string $locale = null): array
    {
        $locale = $locale ?? app()->getLocale();
        $lang = $locale === 'km' ? 'kh' : $locale;

        return Cache::remember('home_testimonials_array_'.$locale, now()->addHours(self::CACHE_TTL_HOURS), function () use ($lang): array {
            $testimonialsDb = Testimonial::where('isActive', true)->where('isFeatured', true)->orderBy('orderIndex')->take(3)->get();

            if ($testimonialsDb->count() > 0) {
                return $testimonialsDb->map(function (Testimonial $t) use ($lang): array {
                    return [
                        'quote' => strip_tags((string) ($t->getTranslation('content', $lang) ?: $t->getTranslation('content', 'en'))),
                        'rating' => $t->rating ?? 5,
                        'author' => $t->getTranslation('clientName', $lang) ?: $t->getTranslation('clientName', 'en'),
                        'role' => $t->getTranslation('clientRole', $lang) ?: $t->getTranslation('clientRole', 'en'),
                    ];
                })->toArray();
            }

            return [];
        });
    }

    /**
     * Get latest news insights.
     */
    public function getNews(?string $locale = null): array
    {
        $locale = $locale ?? app()->getLocale();
        $fallbackImage = '/images/webp/projects/Thumbnail-5.webp';

        $allNews = Cache::remember('home_news_array_'.$locale, now()->addHours(self::CACHE_TTL_HOURS), function () use ($fallbackImage, $locale): array {
            $newsDb = NewsArticle::where('isActive', true)
                ->where('publishedAt', '<=', now())
                ->orderBy('publishedAt', 'desc')
                ->take(3)
                ->get();

            return $newsDb->map(function (NewsArticle $n) use ($fallbackImage, $locale): array {
                return [
                    'id' => $n->slug,
                    'image' => PublicStorage::urlIfExists($n->coverImage, $fallbackImage),
                    'date' => $n->publishedAt ? $n->publishedAt->format('M d, Y') : $n->created_at->format('M d, Y'),
                    'title' => $n->getTranslation('title', $locale),
                    'category' => $n->getTranslation('category', $locale) ?: __('Updates'),
                ];
            })->toArray();
        });

        if (empty($allNews)) {
            $allNews = [
                ['id' => 'safety', 'category' => __('Updates'), 'image' => '/images/webp/projects/Thumbnail-6.webp', 'title' => __('Kimmex Safety Milestone at HQ'), 'date' => 'MAR 30, 2026'],
                ['id' => 'tech', 'category' => __('Milestone'), 'image' => '/images/webp/projects/Thumbnail-5.webp', 'title' => __('New MEP Integration Techniques'), 'date' => 'MAR 15, 2026'],
                ['id' => 'award', 'category' => __('Award'), 'image' => '/images/webp/projects/Thumbnail-4.webp', 'title' => __('Excellence in Construction 2026'), 'date' => 'MAR 05, 2026'],
            ];
        }

        return $allNews;
    }

    /**
     * Get partners list.
     */
    public function getPartners(?string $locale = null): array
    {
        $locale = $locale ?? app()->getLocale();
        $fallbacks = [1, 2, 3, 4, 5, 6, 7, 9, 10, 11];

        $partners = Cache::remember('home_partners_array_v3_'.$locale, now()->addHours(self::CACHE_TTL_HOURS), function () use ($fallbacks, $locale): array {
            return Partner::query()
                ->where('isActive', true)
                ->orderBy('orderIndex')
                ->get()
                ->map(function (Partner $partner, int $index) use ($fallbacks, $locale): array {
                    $fallbackLogo = '/partners/'.$fallbacks[$index % count($fallbacks)].'.png';
                    $logo = $partner->logoUrl;

                    return [
                        'name' => $partner->getTranslation('name', $locale),
                        'logo' => $logo === 'partners/placeholder.png'
                            ? $fallbackLogo
                            : PublicStorage::urlIfExists($logo, $fallbackLogo),
                        'website' => $partner->website,
                    ];
                })
                ->all();
        });

        if ($partners === []) {
            $partners = collect($fallbacks)
                ->map(fn (int $fallback): array => [
                    'name' => __('Partner'),
                    'logo' => '/partners/'.$fallback.'.png',
                    'website' => null,
                ])
                ->all();
        }

        return $partners;
    }

    /**
     * Get services list for homepage.
     */
    public function getServices(?string $locale = null): array
    {
        $locale = $locale ?? app()->getLocale();
        $lang = $locale === 'km' ? 'kh' : $locale;

        $services = Cache::remember('home_services_array_v2_'.$locale, now()->addHours(self::CACHE_TTL_HOURS), function () use ($lang, $locale): array {
            $servicesDb = Service::where('isActive', true)->orderBy('orderIndex')->get();

            return $servicesDb->map(function (Service $s) use ($lang, $locale): array {
                $features = is_array($s->features) ? $s->features : [];
                $mappedFeatures = array_map(function ($f) use ($lang, $locale) {
                    if (! is_array($f)) {
                        return $f;
                    }

                    $feature = $f['name'] ?? $f[$lang] ?? $f['en'] ?? '';

                    return is_array($feature) ? ($feature[$locale] ?? $feature['en'] ?? '') : $feature;
                }, $features);

                return [
                    'title' => $s->getTranslation('title', $locale),
                    'features' => array_values(array_filter($mappedFeatures)),
                    'slug' => $s->slug,
                ];
            })->toArray();
        });

        if (empty($services)) {
            $services = [
                ['title' => __('Design & Build'), 'features' => [__('Architectural Planning'), __('3D Modeling'), __('Turnkey Solutions')], 'slug' => 'design-and-build'],
                ['title' => __('Construction'), 'features' => [__('High-Rise Buildings'), __('Commercial Spaces'), __('Quality Assurance')], 'slug' => 'construction'],
                ['title' => __('MEP Systems'), 'features' => [__('HVAC Installations'), __('Electrical Grids'), __('Smart Building')], 'slug' => 'mep-systems'],
                ['title' => __('Infrastructure'), 'features' => [__('Slope Protection'), __('Water Treatment'), __('Road Paving')], 'slug' => 'infrastructure'],
            ];
        }

        return $services;
    }
}
