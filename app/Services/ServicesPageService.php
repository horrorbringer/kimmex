<?php

namespace App\Services;

use App\Models\MethodologyStep;
use App\Models\Sector;
use App\Models\Service;
use App\Support\PublicStorage;
use Illuminate\Support\Facades\Cache;

class ServicesPageService
{
    /**
     * Cache TTL in seconds (12 hours).
     */
    protected const CACHE_TTL_SECONDS = 43200;

    /**
     * Get active services with caching and fallback.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getServices(): array
    {
        $services = Cache::remember('services_index_data', now()->addSeconds(self::CACHE_TTL_SECONDS), function (): array {
            $servicesDb = Service::where('isActive', true)->orderBy('orderIndex')->get();

            return $servicesDb->map(function ($service): array {
                return [
                    'id' => $service->slug,
                    'icon' => $service->icon ?: 'lucide-hammer',
                    'title' => [
                        'en' => $service->getTranslation('title', 'en'),
                        'kh' => $service->getTranslation('title', 'km'),
                    ],
                    'desc' => [
                        'en' => strip_tags((string) $service->getTranslation('description', 'en')),
                        'kh' => strip_tags((string) $service->getTranslation('description', 'km')),
                    ],
                    'image' => PublicStorage::urlIfExists($service->image, '/images/webp/projects/Thumbnail-1.webp'),
                    'features' => is_array($service->features) ? $service->features : [],
                ];
            })->toArray();
        });

        if (empty($services)) {
            return $this->getFallbackServices();
        }

        return $services;
    }

    /**
     * Get active methodology/process steps with caching and fallback.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getProcess(): array
    {
        $locale = app()->getLocale();

        $process = Cache::remember('services_process_array_'.$locale, now()->addSeconds(self::CACHE_TTL_SECONDS), function (): array {
            $processDb = MethodologyStep::where('isActive', true)->orderBy('orderIndex')->get();

            return $processDb->map(function ($step, $index): array {
                return [
                    'step' => str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT),
                    'icon' => $step->icon ?: 'lucide-check-circle',
                    'title' => [
                        'en' => $step->getTranslation('title', 'en'),
                        'kh' => $step->getTranslation('title', 'km'),
                    ],
                    'desc' => [
                        'en' => trim(strip_tags((string) $step->getTranslation('description', 'en'))),
                        'kh' => trim(strip_tags((string) $step->getTranslation('description', 'km'))),
                    ],
                ];
            })->toArray();
        });

        if (count($process) < 3) {
            return $this->getFallbackProcess();
        }

        return $process;
    }

    /**
     * Get active industry sectors with caching and fallback.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getSectors(string $locale): array
    {
        $lang = $locale === 'km' ? 'kh' : $locale;

        $sectors = Cache::remember('services_sectors_array_'.$lang, now()->addSeconds(self::CACHE_TTL_SECONDS), function (): array {
            $sectorsDb = Sector::where('isActive', true)->orderBy('orderIndex')->get();

            if ($sectorsDb->isEmpty()) {
                return [];
            }

            return $sectorsDb->map(function ($sector): array {
                return [
                    'title' => [
                        'en' => $sector->getTranslation('title', 'en'),
                        'kh' => $sector->getTranslation('title', 'km') ?: $sector->getTranslation('title', 'en'),
                    ],
                    'image' => PublicStorage::urlIfExists($sector->image, '/images/webp/projects/Thumbnail-1.webp'),
                    'icon' => $sector->icon ?: 'lucide-building',
                ];
            })->toArray();
        });

        if (empty($sectors)) {
            return $this->getFallbackSectors();
        }

        return $sectors;
    }

    /**
     * Map process steps count to safe, static Tailwind grid column class.
     */
    public function getProcessGridColsClass(int $count): string
    {
        return match (min(max($count, 1), 5)) {
            1 => 'lg:grid-cols-1',
            2 => 'lg:grid-cols-2',
            3 => 'lg:grid-cols-3',
            4 => 'lg:grid-cols-4',
            default => 'lg:grid-cols-5',
        };
    }

    /**
     * Fallback services array when database query is empty.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function getFallbackServices(): array
    {
        return [
            [
                'id' => 'design-and-build',
                'icon' => 'lucide-pen-tool',
                'title' => ['en' => 'Design & Build', 'kh' => 'រចនា និងសាងសង់'],
                'desc' => [
                    'en' => 'End-to-end construction solutions from architectural design through to project completion.',
                    'kh' => 'ដំណោះស្រាយសំណង់ពីការរចនាស្ថាបត្យកម្មរហូតដល់ការបញ្ចប់គម្រោង។',
                ],
                'image' => '/images/webp/projects/Thumbnail-1.webp',
                'features' => [['name' => 'Detail Design'], ['name' => 'Civil Work'], ['name' => 'MEP Work']],
            ],
            [
                'id' => 'construction',
                'icon' => 'lucide-hammer',
                'title' => ['en' => 'Construction', 'kh' => 'សាងសង់'],
                'desc' => [
                    'en' => 'Premium civil construction services across Cambodia specializing in robust concrete work.',
                    'kh' => 'សេវាកម្មសំណង់ស៊ីវិលលំដាប់ខ្ពស់។',
                ],
                'image' => '/images/webp/projects/Thumbnail-2.webp',
                'features' => [['name' => 'High-Rise Buildings'], ['name' => 'Commercial Spaces'], ['name' => 'Quality Assurance']],
            ],
            [
                'id' => 'project-management',
                'icon' => 'lucide-clipboard-check',
                'title' => ['en' => 'Project Management', 'kh' => 'ការគ្រប់គ្រងគម្រោង'],
                'desc' => [
                    'en' => 'Expert oversight ensuring on-time delivery, quality control, and safety compliance.',
                    'kh' => 'ការត្រួតពិនិត្យជំនាញធានាការផ្តល់ទាន់ពេល និងសុវត្ថិភាព។',
                ],
                'image' => '/images/webp/projects/Thumbnail-3.webp',
                'features' => [['name' => 'Scheduling'], ['name' => 'Quality Control'], ['name' => 'Safety']],
            ],
            [
                'id' => 'consultants',
                'icon' => 'lucide-lightbulb',
                'title' => ['en' => 'Consultants', 'kh' => 'ទីប្រឹក្សា'],
                'desc' => [
                    'en' => 'Professional consulting services including project feasibility and structural analysis.',
                    'kh' => 'សេវាកម្មប្រឹក្សាវិជ្ជាជីវៈ។',
                ],
                'image' => '/images/webp/projects/Thumbnail-4.webp',
                'features' => [['name' => 'Feasibility'], ['name' => 'Design Consulting'], ['name' => 'Analysis']],
            ],
        ];
    }

    /**
     * Fallback process array when database query has fewer than 3 steps.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function getFallbackProcess(): array
    {
        return [
            ['step' => '01', 'icon' => 'lucide-users', 'title' => ['en' => 'Consultation', 'kh' => 'ការពិគ្រោះ'], 'desc' => ['en' => 'We clarify project goals and requirements.', 'kh' => 'យើងកំណត់គោលដៅគម្រោង។']],
            ['step' => '02', 'icon' => 'lucide-ruler', 'title' => ['en' => 'Planning & Design', 'kh' => 'ការរៀបចំផែនការ'], 'desc' => ['en' => 'Design direction, timeline, and budget.', 'kh' => 'ទិសដៅរចនា កាលវិភាគ និងថវិកា។']],
            ['step' => '03', 'icon' => 'lucide-hard-hat', 'title' => ['en' => 'Execution', 'kh' => 'ការអនុវត្ត'], 'desc' => ['en' => 'Construction moves according to plan.', 'kh' => 'សំណង់ដំណើរការតាមផែនការ។']],
            ['step' => '04', 'icon' => 'lucide-shield-check', 'title' => ['en' => 'Quality Control', 'kh' => 'ត្រួតពិនិត្យគុណភាព'], 'desc' => ['en' => 'Each stage checked against standards.', 'kh' => 'រាល់ដំណាក់កាលត្រូវបានពិនិត្យ។']],
            ['step' => '05', 'icon' => 'lucide-check-circle-2', 'title' => ['en' => 'Handover', 'kh' => 'ការប្រគល់'], 'desc' => ['en' => 'Final inspection and handover.', 'kh' => 'ការត្រួតពិនិត្យ និងប្រគល់។']],
        ];
    }

    /**
     * Fallback sectors array when database query is empty.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function getFallbackSectors(): array
    {
        return [
            ['title' => ['en' => 'Government', 'kh' => 'រដ្ឋាភិបាល'], 'image' => '/images/webp/projects/Thumbnail-1.webp', 'icon' => 'lucide-landmark'],
            ['title' => ['en' => 'Education', 'kh' => 'អប់រំ'], 'image' => '/images/webp/projects/Thumbnail-2.webp', 'icon' => 'lucide-graduation-cap'],
            ['title' => ['en' => 'Commercial', 'kh' => 'ពាណិជ្ជកម្ម'], 'image' => '/images/webp/projects/Thumbnail-3.webp', 'icon' => 'lucide-building'],
            ['title' => ['en' => 'Infrastructure', 'kh' => 'ហេដ្ឋារចនាសម្ព័ន្ធ'], 'image' => '/images/webp/projects/Thumbnail-6.webp', 'icon' => 'lucide-route'],
        ];
    }
}
