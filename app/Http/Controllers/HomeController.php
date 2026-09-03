<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Services\HomePageService;
use App\Support\PublicStorage;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct(
        protected HomePageService $homePageService,
    ) {}

    public function index(): View
    {
        $locale = app()->getLocale();
        $heroLocale = $locale === 'kh' ? 'km' : $locale;

        $priorityHeroImage = Cache::remember('hero_priority_image_'.$heroLocale, now()->addHours(6), function (): string {
            $image = Project::where('isFeatured', true)
                ->where('isActive', true)
                ->orderByDesc('created_at')
                ->value('heroImage');

            return PublicStorage::urlIfExists($image, '/images/webp/hero/hero-1.webp');
        });

        $priorityHeroImageSrcset = PublicStorage::cloudinaryResponsiveSrcset($priorityHeroImage);

        $heroSlides = $this->homePageService->getHeroSlides($locale);
        $aboutData = $this->homePageService->getAboutData($locale);
        $milestonesData = $this->homePageService->getMilestonesData($locale);
        $processes = $this->homePageService->getProcess($locale);
        $projects = $this->homePageService->getProjects($locale);
        $testimonials = $this->homePageService->getTestimonials($locale);
        $allNews = $this->homePageService->getNews($locale);
        $partners = $this->homePageService->getPartners($locale);

        return view('welcome', compact(
            'priorityHeroImage',
            'priorityHeroImageSrcset',
            'heroSlides',
            'aboutData',
            'milestonesData',
            'processes',
            'projects',
            'testimonials',
            'allNews',
            'partners'
        ));
    }
}
