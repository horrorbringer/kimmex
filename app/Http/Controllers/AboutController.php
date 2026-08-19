<?php

namespace App\Http\Controllers;

use App\Enums\ProjectStatus;
use App\Models\Milestone;
use App\Models\OrgUnit;
use App\Models\Project;
use App\Models\SystemSetting;
use App\Support\PublicStorage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class AboutController extends Controller
{
    public function index()
    {
        $locale = app()->getLocale();
        $localeKey = $locale === 'kh' ? 'km' : $locale;

        $brandProfile = SystemSetting::get('brand_identity', []);
        $brand = $brandProfile[$localeKey] ?? ($brandProfile['en'] ?? []);
        $ceoName = $brandProfile['ceo_name'] ?? 'Okhna. TOUCH KIM';

        $aboutHeroImage = $brandProfile['about_hero_image'] ?? null;
        $aboutHeroImageUrl = '/images/webp/hero/hero-1.webp';
        if (filled($aboutHeroImage)) {
            $aboutHeroImageUrl = PublicStorage::urlIfExists($aboutHeroImage, $aboutHeroImageUrl);
        }

        $resolveImage = fn (?string $image, string $fallback): string => filled($image) ? PublicStorage::urlIfExists($image, $fallback) : $fallback;

        $defaultSectionImages = [
            '/images/webp/projects/Thumbnail-1.webp',
            '/images/webp/projects/Thumbnail-3.webp',
            '/images/webp/projects/Thumbnail-2.webp',
            '/images/webp/projects/Thumbnail-4.webp',
        ];

        $aboutSectionImages = array_map(
            fn (string $fallback, int $index): string => $resolveImage(
                $brandProfile['about_section_images'][$index] ?? null, $fallback
            ),
            $defaultSectionImages,
            array_keys($defaultSectionImages)
        );

        $aboutData = [
            'story' => $brand['company_story'] ?? __('Since our humble beginnings, KIM MEX Construction has grown into a premier partner...'),
            'values' => array_map(function ($v) {
                $icon = $v['icon'] ?? 'lucide-shield';
                if (! preg_match('/^[a-zA-Z0-9\-]+$/', $icon)) {
                    $icon = 'lucide-shield';
                }

                return [
                    'title' => $v['title'] ?? '',
                    'content' => $v['description'] ?? '',
                    'icon' => $icon,
                    'image' => PublicStorage::urlIfExists($v['image'] ?? null),
                ];
            }, $brand['values_list'] ?? []),
        ];

        if (empty($aboutData['values'])) {
            $aboutData['values'] = [
                ['title' => __('Safety First'), 'content' => __('We maintain a strict zero-incident policy on all construction sites.'), 'icon' => 'lucide-heart', 'image' => null],
                ['title' => __('Quality Excellence'), 'content' => __('Utilizing premium materials and rigorous QA workflows.'), 'icon' => 'lucide-award', 'image' => null],
                ['title' => __('Integrity'), 'content' => __('Honest and transparent communication with all our clients.'), 'icon' => 'lucide-shield', 'image' => null],
                ['title' => __('Innovation'), 'content' => __('Leveraging the latest in 3D modeling and MEP system architecture.'), 'icon' => 'lucide-lightbulb', 'image' => null],
            ];
        }

        $milestoneFallback = [
            ['year' => '1999', 'title' => __('Company Founded'), 'desc' => __('Started as a small dedicated engineering firm.'), 'detail' => '', 'has_detail' => false, 'is_featured' => true, 'image' => '/images/webp/projects/Thumbnail-1.webp'],
            ['year' => '2010', 'title' => __('First Mega Project'), 'desc' => __('Secured our first major government infrastructure contract.'), 'detail' => '', 'has_detail' => false, 'is_featured' => false, 'image' => '/images/webp/projects/Thumbnail-2.webp'],
            ['year' => '2026', 'title' => __('Industry Leaders'), 'desc' => __('Recognized as the top infrastructure firm in the Kingdom of Cambodia.'), 'detail' => '', 'has_detail' => false, 'is_featured' => true, 'image' => '/images/webp/projects/Thumbnail-3.webp'],
        ];

        $milestonesDb = Milestone::where('isActive', true)->orderBy('sortOrder')->orderBy('year')->get();
        if ($milestonesDb->isEmpty()) {
            $milestones = $milestoneFallback;
        } else {
            $milestones = $milestonesDb->values()->map(function (Milestone $m, int $index) use ($localeKey) {
                $title = $m->getTranslation('title', $localeKey) ?: $m->getTranslation('title', 'en');
                $desc = $m->getTranslation('description', $localeKey) ?: $m->getTranslation('description', 'en');
                $detail = $m->getTranslation('detailed_description', $localeKey) ?: $m->getTranslation('detailed_description', 'en');
                $hasDetail = filled(trim(strip_tags((string) $detail)));
                $fallbackImage = '/images/webp/projects/Thumbnail-'.(($index % 6) + 1).'.webp';

                return [
                    'year' => $m->year,
                    'title' => $title,
                    'desc' => $desc,
                    'detail' => $hasDetail ? $detail : '',
                    'has_detail' => $hasDetail,
                    'is_featured' => $m->isFeatured,
                    'image' => PublicStorage::urlIfExists($m->image, $fallbackImage),
                ];
            })->toArray();
        }

        $orgChart = Cache::remember('about_orgchart_'.$localeKey, 43200, function () use ($localeKey) {
            $unitsByParent = OrgUnit::where('isActive', true)
                ->with(['employee', 'department'])
                ->orderBy('orderIndex')
                ->get()
                ->groupBy(fn (OrgUnit $unit): string => (string) ($unit->parentId ?? '__root__'));

            $buildNode = null;
            $buildNode = function ($unit) use (&$buildNode, $unitsByParent, $localeKey) {
                $name = $unit->employee?->name ?? $unit->getTranslation('title', $localeKey);
                $role = $unit->employee?->role ?? $unit->getTranslation('title', $localeKey);
                $rawType = strtoupper($unit->type);
                $type = match ($rawType) {
                    'STAFF' => 'staff',
                    'DEPARTMENT' => 'department',
                    'OFFICE' => 'office',
                    default => 'staff',
                };
                $lowRole = strtolower($role);
                if (str_contains($lowRole, 'ceo') || str_contains($lowRole, 'chief')) {
                    $type = 'ceo';
                } elseif (str_contains($lowRole, 'director') || str_contains($lowRole, 'manager')) {
                    $type = 'director';
                }
                $employeeImage = $unit->employee?->image;
                $employeeImage = PublicStorage::urlIfExists($employeeImage);

                return [
                    'name' => $name,
                    'role' => $role,
                    'type' => $type,
                    'image' => $employeeImage,
                    'phone' => $unit->employee?->phone,
                    'bio' => $unit->employee?->bio,
                    'children' => $unitsByParent->get((string) $unit->id, collect())
                        ->map(fn ($child) => $buildNode($child))
                        ->toArray(),
                ];
            };

            $roots = $unitsByParent->get('__root__', collect());
            if ($roots->isEmpty()) {
                return [
                    'name' => 'Sok Visal', 'role' => __('CEO (Not Configured)'), 'type' => 'ceo',
                    'image' => null, 'bio' => __('To show your team here, please add Employee and Org Unit records in the admin panel.'), 'children' => [],
                ];
            }
            if ($roots->count() === 1) {
                return $buildNode($roots->first());
            }
            $profile = SystemSetting::get('organization_profile', []);
            $companyName = $profile[$localeKey]['company_name'] ?? 'Kimmex Group';

            return [
                'name' => $companyName, 'role' => __('Organization Structure'), 'type' => 'office',
                'children' => $roots->map(fn ($root) => $buildNode($root))->toArray(),
            ];
        });

        $orgProfile = SystemSetting::get('organization_profile', []);
        $orgChartVisible = (bool) ($orgProfile['org_chart_visible'] ?? true);
        $orgChartType = $orgProfile['org_chart_type'] ?? 'dynamic';
        if (! $orgChartVisible) {
            $orgChartType = 'none';
        }
        $orgChartImage = $orgProfile['org_chart_image'] ?? null;
        $orgChartPdf = $orgProfile['org_chart_pdf'] ?? null;

        if ($orgChartImage && ! Str::startsWith($orgChartImage, ['http://', 'https://', '/'])) {
            $orgChartImage = PublicStorage::urlIfExists($orgChartImage);
        }
        if ($orgChartPdf && ! Str::startsWith($orgChartPdf, ['http://', 'https://', '/'])) {
            $orgChartPdf = PublicStorage::urlIfExists($orgChartPdf);
        }

        $tagline = $orgProfile[$localeKey]['tagline'] ?? "Cambodia's Premier Construction Partner";

        // Query all active projects for the Project Journey & Clean Line Chart
        $allProjectsDb = Project::where('isActive', true)
            ->with('projectCategory')
            ->orderBy('completionDate', 'asc')
            ->orderBy('created_at', 'asc')
            ->get();

        $projectsByYear = [];
        $runningTotal = 0;

        foreach ($allProjectsDb as $proj) {
            $year = $proj->completionDate ? $proj->completionDate->format('Y') : date('Y', strtotime($proj->created_at));
            if (! isset($projectsByYear[$year])) {
                $projectsByYear[$year] = [
                    'year' => (string) $year,
                    'count' => 0,
                    'projects' => [],
                ];
            }
            $projectsByYear[$year]['count']++;
            $projectsByYear[$year]['projects'][] = [
                'id' => $proj->id,
                'slug' => $proj->slug,
                'title' => $proj->getTranslation('title', $localeKey) ?: $proj->getTranslation('title', 'en'),
                'location' => $proj->getTranslation('location', $localeKey) ?: $proj->getTranslation('location', 'en'),
                'client' => $proj->client,
                'status' => $proj->status?->value ?? 'COMPLETED',
                'status_label' => $proj->status?->getLabel() ?? __('Completed'),
                'category' => $proj->projectCategory?->getTranslation('name', $localeKey) ?: ($proj->projectCategory?->getTranslation('name', 'en') ?: __('General Construction')),
                'image' => PublicStorage::urlIfExists($proj->heroImage, '/images/webp/projects/Thumbnail-1.webp'),
                'year' => $year,
            ];
        }

        // Ensure chronological ordering of years
        ksort($projectsByYear);

        $timelinePoints = [];
        $runningCumulative = 0;
        foreach ($projectsByYear as $yr => $data) {
            $runningCumulative += $data['count'];
            $timelinePoints[] = [
                'year' => (string) $yr,
                'count' => $data['count'],
                'cumulative' => $runningCumulative,
                'projects' => $data['projects'],
            ];
        }

        $allProjectsFlat = $allProjectsDb->map(function ($proj) use ($localeKey) {
            $year = $proj->completionDate ? $proj->completionDate->format('Y') : date('Y', strtotime($proj->created_at));

            return [
                'id' => $proj->id,
                'slug' => $proj->slug,
                'title' => $proj->getTranslation('title', $localeKey) ?: $proj->getTranslation('title', 'en'),
                'location' => $proj->getTranslation('location', $localeKey) ?: $proj->getTranslation('location', 'en'),
                'client' => $proj->client,
                'status' => $proj->status?->value ?? 'COMPLETED',
                'status_label' => $proj->status?->getLabel() ?? __('Completed'),
                'category' => $proj->projectCategory?->getTranslation('name', $localeKey) ?: ($proj->projectCategory?->getTranslation('name', 'en') ?: __('General Construction')),
                'image' => PublicStorage::urlIfExists($proj->heroImage, '/images/webp/projects/Thumbnail-1.webp'),
                'year' => $year,
            ];
        })->toArray();

        $projectJourneyStats = [
            'total_projects' => $allProjectsDb->count(),
            'completed_projects' => $allProjectsDb->where('status', ProjectStatus::COMPLETED)->count(),
            'ongoing_projects' => $allProjectsDb->where('status', ProjectStatus::ONGOING)->count(),
            'start_year' => ! empty($timelinePoints) ? $timelinePoints[0]['year'] : '2021',
            'latest_year' => ! empty($timelinePoints) ? end($timelinePoints)['year'] : '2027',
        ];

        $canvasJsCumulativeData = array_map(fn ($pt) => [
            'label' => (string) $pt['year'],
            'y' => (int) $pt['cumulative'],
        ], $timelinePoints);

        return view('pages.about', compact(
            'locale', 'localeKey',
            'brandProfile', 'brand',
            'ceoName', 'aboutHeroImageUrl',
            'aboutSectionImages', 'aboutData',
            'milestones', 'orgChart',
            'orgProfile', 'orgChartVisible', 'orgChartType', 'orgChartImage', 'orgChartPdf',
            'tagline',
            'timelinePoints', 'allProjectsFlat', 'projectJourneyStats',
            'canvasJsCumulativeData',
        ));
    }
}
