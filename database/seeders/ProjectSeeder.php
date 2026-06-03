<?php

namespace Database\Seeders;

use App\Enums\ProjectStatus;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\ProjectImage;
use App\Services\AutoTranslateService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use RuntimeException;
use ZipArchive;

class ProjectSeeder extends Seeder
{
    private const SOURCE_FILE = 'docs/Kim Mex Project List.xlsx';

    private ?AutoTranslateService $translator = null;

    public function run(): void
    {
        $categories = ProjectCategory::all()->pluck('id', 'slug')->toArray();
        $seededSlugs = [];

        foreach ($this->projectsFromSpreadsheet() as $projectData) {
            $categorySlug = $this->categorySlugFor($projectData);
            $englishProject = $this->englishProjectData($projectData);
            $slug = $this->slugFor($projectData);
            $seededSlugs[] = $slug;

            $project = Project::withoutEvents(fn () => Project::updateOrCreate(
                ['slug' => $slug],
                [
                    'title' => [
                        'en' => $englishProject['project_name'],
                        'km' => $this->translateEnglishToKhmer($englishProject['project_name'], $projectData['project_name']),
                    ],
                    'location' => [
                        'en' => $englishProject['location'],
                        'km' => $this->translateEnglishToKhmer($englishProject['location'], $projectData['location']),
                    ],
                    'client' => $englishProject['client'],
                    'scale' => $this->scaleFor($projectData),
                    'timeline' => $this->timelineFor($projectData),
                    'completionDate' => $this->dateFor($projectData['end_date']),
                    'heroImage' => $this->heroImageFor($categorySlug),
                    'category' => $categorySlug,
                    'project_category_id' => $categories[$categorySlug] ?? null,
                    'isFeatured' => $projectData['number'] <= 6,
                    'isActive' => true,
                    'status' => $this->statusFor($projectData['end_date']),
                    'description' => [
                        'en' => $this->descriptionFor($englishProject),
                        'km' => $this->translateEnglishToKhmer($this->descriptionFor($englishProject), $this->khmerDescriptionFor($projectData)),
                    ],
                    'background' => [
                        'en' => $this->backgroundFor($englishProject),
                        'km' => $this->translateEnglishToKhmer($this->backgroundFor($englishProject), $this->khmerBackgroundFor($projectData)),
                    ],
                    'objectives' => [
                        'en' => $this->objectivesFor($englishProject),
                        'km' => $this->translateEnglishToKhmer($this->objectivesFor($englishProject), $this->khmerObjectivesFor($projectData)),
                    ],
                    'designConcept' => [
                        'en' => $this->designConceptFor($englishProject, $categorySlug),
                        'km' => $this->translateEnglishToKhmer($this->designConceptFor($englishProject, $categorySlug), $this->khmerDesignConceptFor($projectData)),
                    ],
                    'scopeContributions' => [
                        'en' => $this->scopeFor($englishProject),
                        'km' => $this->khmerScopeFor($projectData),
                    ],
                    'engineeringNarrative' => [
                        'en' => $this->engineeringNarrativeFor($englishProject),
                        'km' => $this->translateEnglishToKhmer($this->engineeringNarrativeFor($englishProject), $this->khmerEngineeringNarrativeFor($projectData)),
                    ],
                ]
            ));

            $this->syncGalleryImages($project, $projectData, $englishProject, $categorySlug);
        }

        foreach (['en', 'km', 'kh'] as $locale) {
            Cache::forget("projects_index_data_{$locale}");
            Cache::forget("home_projects_array_{$locale}");
            Cache::forget("home_featured_projects_{$locale}");

            foreach ($seededSlugs as $slug) {
                Cache::forget("project_show_data_{$slug}_{$locale}");
            }
        }
    }

    /**
     * @return array<int, array{
     *     number:int,
     *     project_name:string,
     *     client:string,
     *     location:string,
     *     construction_value:string,
     *     area:string,
     *     floors:string,
     *     duration:string,
     *     start_date:string,
     *     end_date:string
     * }>
     */
    private function projectsFromSpreadsheet(): array
    {
        $path = public_path(self::SOURCE_FILE);

        if (! file_exists($path)) {
            throw new RuntimeException("Project source spreadsheet not found: {$path}");
        }

        $rows = $this->readWorksheetRows($path);
        $projects = [];

        foreach ($rows as $rowNumber => $row) {
            if ($rowNumber <= 2 || empty($row['A']) || empty($row['B'])) {
                continue;
            }

            $projects[] = [
                'number' => (int) $row['A'],
                'project_name' => trim($row['B']),
                'client' => trim($row['C'] ?? ''),
                'location' => trim($row['D'] ?? ''),
                'construction_value' => $this->formatMoney($row['E'] ?? ''),
                'area' => $this->formatArea($row['F'] ?? ''),
                'floors' => trim($row['G'] ?? ''),
                'duration' => trim($row['H'] ?? ''),
                'start_date' => trim($row['I'] ?? ''),
                'end_date' => trim($row['J'] ?? ''),
            ];
        }

        return $projects;
    }

    /**
     * Reads the first worksheet from an .xlsx file without requiring PhpSpreadsheet.
     *
     * @return array<int, array<string, string>>
     */
    private function readWorksheetRows(string $path): array
    {
        $zip = new ZipArchive();

        if ($zip->open($path) !== true) {
            throw new RuntimeException("Unable to open project spreadsheet: {$path}");
        }

        $sharedStrings = $this->readSharedStrings($zip);
        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();

        if ($sheetXml === false) {
            throw new RuntimeException('Unable to read first worksheet from project spreadsheet.');
        }

        $sheet = simplexml_load_string($sheetXml);
        $rows = [];

        foreach ($sheet->sheetData->row as $row) {
            $rowIndex = (int) $row['r'];

            foreach ($row->c as $cell) {
                $reference = (string) $cell['r'];
                $column = preg_replace('/\d+/', '', $reference);
                $value = (string) $cell->v;

                if ((string) $cell['t'] === 's') {
                    $value = $sharedStrings[(int) $value] ?? '';
                }

                $rows[$rowIndex][$column] = trim($value);
            }
        }

        ksort($rows);

        return $rows;
    }

    /**
     * @return array<int, string>
     */
    private function readSharedStrings(ZipArchive $zip): array
    {
        $xml = $zip->getFromName('xl/sharedStrings.xml');

        if ($xml === false) {
            return [];
        }

        $sharedStrings = [];
        $strings = simplexml_load_string($xml);

        foreach ($strings->si as $string) {
            $parts = [];

            if (isset($string->t)) {
                $parts[] = (string) $string->t;
            }

            foreach ($string->r ?? [] as $run) {
                $parts[] = (string) $run->t;
            }

            $sharedStrings[] = trim(implode('', $parts));
        }

        return $sharedStrings;
    }

    /**
     * @param array<string, mixed> $project
     */
    private function slugFor(array $project): string
    {
        preg_match_all('/[A-Za-z0-9]+(?:-[A-Za-z0-9]+)?/u', $project['project_name'], $matches);

        $suffix = $matches[0] ? implode('-', $matches[0]) : Str::limit($project['project_name'], 24, '');

        return Str::slug(sprintf('project-%02d-%s', $project['number'], $suffix));
    }

    /**
     * @param array<string, mixed> $project
     */
    private function categorySlugFor(array $project): string
    {
        $text = $project['project_name'].' '.$project['client'];

        return match (true) {
            Str::contains($text, ['មន្ទីរពេទ្យ', 'សុខភាព']) => 'healthcare',
            Str::contains($text, ['សាកលវិទ្យាល័យ', 'សាលា', 'វិទ្យាស្ថាន', 'NTTI', 'TVET']) => 'education',
            Str::contains($text, ['អគ្គិសនី', 'EDC']) => 'energy',
            Str::contains($text, ['SOKIMEX']) => 'commercial',
            Str::contains($text, ['ហេដ្ឋារចនាសម្ពន្ធ័', 'ចំណតសាឡង់', 'ទីលានត្រួតពិនិត្យ']) => 'infrastructure',
            default => 'government',
        };
    }

    private function heroImageFor(string $categorySlug): string
    {
        return match ($categorySlug) {
            'healthcare' => '/images/projects/dambe-clinic.jpg',
            'education' => '/images/projects/Thumbnail-8.jpg',
            'energy' => '/images/projects/Thumbnail-7.jpg',
            'commercial' => '/images/projects/customs-excise.jpg',
            'infrastructure' => '/images/projects/mondulkiri-water.jpg',
            default => '/images/projects/mpt-office.jpg',
        };
    }

    /**
     * @return array<int, string>
     */
    private function galleryImagesFor(string $categorySlug, int $projectNumber): array
    {
        $categoryImages = match ($categorySlug) {
            'healthcare' => [
                '/images/projects/dambe-clinic.jpg',
                '/images/projects/Thumbnail-4.jpg',
                '/images/projects/Thumbnail-6.jpg',
            ],
            'education' => [
                '/images/projects/Thumbnail-8.jpg',
                '/images/projects/Thumbnail-3.jpg',
                '/images/projects/Thumbnail-9.jpg',
            ],
            'energy' => [
                '/images/projects/Thumbnail-7.jpg',
                '/images/projects/Thumbnail-5.jpg',
                '/images/projects/Thumbnail-2.jpg',
            ],
            'commercial' => [
                '/images/projects/customs-excise.jpg',
                '/images/projects/nbc-branch.jpg',
                '/images/projects/Thumbnail-1.jpg',
            ],
            'infrastructure' => [
                '/images/projects/mondulkiri-water.jpg',
                '/images/projects/stung-treng-water.jpg',
                '/images/projects/Thumbnail-6.jpg',
            ],
            default => [
                '/images/projects/mpt-office.jpg',
                '/images/projects/Thumbnail-1.jpg',
                '/images/projects/Thumbnail-4.jpg',
            ],
        };

        $offset = max(0, ($projectNumber - 1) % count($categoryImages));

        return array_values(array_unique(array_merge(
            array_slice($categoryImages, $offset),
            array_slice($categoryImages, 0, $offset),
        )));
    }

    /**
     * @param array<string, mixed> $sourceProject
     * @param array<string, mixed> $englishProject
     */
    private function syncGalleryImages(Project $project, array $sourceProject, array $englishProject, string $categorySlug): void
    {
        $galleryImages = $this->galleryImagesFor($categorySlug, $sourceProject['number']);

        ProjectImage::withoutEvents(fn () => $project->images()
            ->whereIn('url', $this->seededGalleryImageCatalog())
            ->whereNotIn('url', $galleryImages)
            ->delete());

        foreach ($galleryImages as $index => $image) {
            ProjectImage::withoutEvents(fn () => ProjectImage::updateOrCreate(
                [
                    'projectId' => $project->id,
                    'url' => $image,
                ],
                [
                    'caption' => $this->galleryCaptionFor($englishProject, $index),
                    'sort_order' => $index + 1,
                ],
            ));
        }
    }

    /**
     * @return array<int, string>
     */
    private function seededGalleryImageCatalog(): array
    {
        return [
            '/images/projects/customs-excise.jpg',
            '/images/projects/dambe-clinic.jpg',
            '/images/projects/mondulkiri-water.jpg',
            '/images/projects/mpt-office.jpg',
            '/images/projects/nbc-branch.jpg',
            '/images/projects/stung-treng-water.jpg',
            '/images/projects/Thumbnail-1.jpg',
            '/images/projects/Thumbnail-2.jpg',
            '/images/projects/Thumbnail-3.jpg',
            '/images/projects/Thumbnail-4.jpg',
            '/images/projects/Thumbnail-5.jpg',
            '/images/projects/Thumbnail-6.jpg',
            '/images/projects/Thumbnail-7.jpg',
            '/images/projects/Thumbnail-8.jpg',
            '/images/projects/Thumbnail-9.jpg',
        ];
    }

    /**
     * @param array<string, mixed> $project
     */
    private function galleryCaptionFor(array $project, int $index): string
    {
        return match ($index) {
            0 => $project['project_name'].' overview',
            1 => 'Construction delivery for '.$project['client'],
            default => 'Project site in '.$project['location'],
        };
    }

    /**
     * @param array<string, mixed> $project
     */
    private function scaleFor(array $project): string
    {
        return collect([
            $this->floorLabel($project['floors']),
            $project['area'],
        ])->filter()->implode(' | ');
    }

    private function floorLabel(string $floors): string
    {
        if ($floors === '') {
            return '';
        }

        if (Str::contains($floors, 'ផ្ទាល់ដី')) {
            return 'Ground level';
        }

        if (preg_match('/\d+/u', $floors, $matches)) {
            $count = (int) $matches[0];

            return $count === 1 ? '1 floor' : "{$count} floors";
        }

        return $floors;
    }

    /**
     * @param array<string, mixed> $project
     */
    private function timelineFor(array $project): string
    {
        return collect([
            $this->monthLabel($project['start_date']),
            $this->monthLabel($project['end_date']),
        ])->filter()->implode(' - ');
    }

    private function dateFor(string $value): ?string
    {
        $parts = $this->dateParts($value);

        if (! $parts) {
            return null;
        }

        return Carbon::create($parts['year'], $parts['month'], 1)->toDateString();
    }

    private function monthLabel(string $value): string
    {
        $parts = $this->dateParts($value);

        if (! $parts) {
            return $value;
        }

        $months = [
            1 => 'Jan',
            2 => 'Feb',
            3 => 'Mar',
            4 => 'Apr',
            5 => 'May',
            6 => 'Jun',
            7 => 'Jul',
            8 => 'Aug',
            9 => 'Sep',
            10 => 'Oct',
            11 => 'Nov',
            12 => 'Dec',
        ];

        return "{$months[$parts['month']]} {$parts['year']}";
    }

    /**
     * @return array{month:int, year:int}|null
     */
    private function dateParts(string $value): ?array
    {
        if (! preg_match('/(.+?)\s+(\d{4})/u', trim($value), $matches)) {
            return null;
        }

        $month = $this->khmerMonthNumber($matches[1]);

        if (! $month) {
            return null;
        }

        return [
            'month' => $month,
            'year' => (int) $matches[2],
        ];
    }

    private function khmerMonthNumber(string $month): ?int
    {
        $month = trim($month);

        return [
            'មករា' => 1,
            'កុម្ភះ' => 2,
            'កុម្ភៈ' => 2,
            'មីនា' => 3,
            'មេសា' => 4,
            'ឧសភា' => 5,
            'មិថុនា' => 6,
            'កក្កដា' => 7,
            'សីហា' => 8,
            'កញ្ញា' => 9,
            'តុលា' => 10,
            'វិច្ឆិកា' => 11,
            'វិចិ្ឆកា' => 11,
            'ធ្នូ' => 12,
        ][$month] ?? null;
    }

    private function statusFor(string $endDate): ProjectStatus
    {
        $date = $this->dateFor($endDate);

        if (! $date) {
            return ProjectStatus::ONGOING;
        }

        return Carbon::parse($date)->endOfMonth()->isPast()
            ? ProjectStatus::COMPLETED
            : ProjectStatus::ONGOING;
    }

    /**
     * Convert the Khmer spreadsheet source into English before saving.
     *
     * The application-level saving hook auto-translates non-empty English
     * translations into Khmer, so the seeder intentionally stores only `en`.
     *
     * @param array<string, mixed> $project
     * @return array<string, mixed>
     */
    private function englishProjectData(array $project): array
    {
        return array_merge($project, [
            'project_name' => $this->translateSourceToEnglish($project['project_name']),
            'client' => $this->translateSourceToEnglish($project['client']),
            'location' => $this->translateSourceToEnglish($project['location']),
            'floors' => $this->floorLabel($project['floors']),
            'duration' => $this->englishDuration($project['duration']),
        ]);
    }

    private function translateSourceToEnglish(string $value): string
    {
        $value = trim($value);

        if ($value === '' || ! $this->containsKhmer($value)) {
            return $value;
        }

        try {
            $translated = $this->translator()->translateFrom($value, 'en', 'km');

            if (is_string($translated) && trim($translated) !== '') {
                return trim($translated);
            }
        } catch (\Throwable $exception) {
            Log::warning('Project seeder Khmer-to-English translation failed.', [
                'value' => $value,
                'error' => $exception->getMessage(),
            ]);
        }

        return $value;
    }

    private function translateEnglishToKhmer(string $value, string $fallback): string
    {
        $value = trim($value);
        $fallback = trim($fallback);

        if ($value === '') {
            return $fallback;
        }

        try {
            $translated = $this->translator()->translateFrom($value, 'km', 'en');

            if (is_string($translated) && trim($translated) !== '') {
                return trim($translated);
            }
        } catch (\Throwable $exception) {
            Log::warning('Project seeder English-to-Khmer translation failed.', [
                'value' => $value,
                'error' => $exception->getMessage(),
            ]);
        }

        return $fallback !== '' ? $fallback : $value;
    }

    private function translator(): AutoTranslateService
    {
        return $this->translator ??= app(AutoTranslateService::class);
    }

    private function containsKhmer(string $value): bool
    {
        return preg_match('/[\x{1780}-\x{17FF}]/u', $value) === 1;
    }

    private function englishDuration(string $duration): string
    {
        $duration = trim($duration);

        if ($duration === '') {
            return '';
        }

        if (preg_match('/\d+/u', $duration, $matches)) {
            $count = (int) $matches[0];

            return $count === 1 ? '1 month' : "{$count} months";
        }

        return $this->translateSourceToEnglish($duration);
    }

    /**
     * @param array<string, mixed> $project
     */
    private function descriptionFor(array $project): string
    {
        return sprintf(
            '<p>%s is a Kimmex construction project for %s, located in %s.</p>',
            $project['project_name'],
            $project['client'],
            $project['location'],
        );
    }

    /**
     * @param array<string, mixed> $project
     */
    private function khmerDescriptionFor(array $project): string
    {
        return sprintf(
            '<p>គម្រោងសាងសង់ %s សម្រាប់ %s មានទីតាំងនៅ %s។</p>',
            $project['project_name'],
            $project['client'],
            $project['location'],
        );
    }

    /**
     * @param array<string, mixed> $project
     */
    private function backgroundFor(array $project): string
    {
        $facts = array_filter([
            $project['construction_value'] ? 'Construction value: '.$project['construction_value'] : null,
            $project['area'] ? 'Built area: '.$project['area'] : null,
            $project['duration'] ? 'Planned duration: '.$project['duration'] : null,
        ]);

        return '<p>The project was planned around the client requirements, site conditions, and construction schedule for '.$project['location'].'.</p>'
            .($facts ? '<ul><li>'.implode('</li><li>', $facts).'</li></ul>' : '');
    }

    /**
     * @param array<string, mixed> $project
     */
    private function khmerBackgroundFor(array $project): string
    {
        $facts = array_filter([
            $project['construction_value'] ? 'តម្លៃសាងសង់: '.$project['construction_value'] : null,
            $project['area'] ? 'ផ្ទៃក្រឡា: '.$project['area'] : null,
            $project['duration'] ? 'រយៈពេលអនុវត្ត: '.$project['duration'] : null,
        ]);

        return '<p>គម្រោងនេះត្រូវបានរៀបចំតាមតម្រូវការរបស់ម្ចាស់គម្រោង លក្ខខណ្ឌទីតាំង និងកាលវិភាគសាងសង់នៅ '.$project['location'].'។</p>'
            .($facts ? '<ul><li>'.implode('</li><li>', $facts).'</li></ul>' : '');
    }

    /**
     * @param array<string, mixed> $project
     */
    private function objectivesFor(array $project): string
    {
        $items = [
            'Deliver the project scope with practical coordination between client, design, and site teams.',
            'Maintain construction quality, schedule discipline, and safe site execution.',
            'Provide a durable facility that supports the operational needs of '.$project['client'].'.',
        ];

        return '<ul><li>'.implode('</li><li>', $items).'</li></ul>';
    }

    /**
     * @param array<string, mixed> $project
     */
    private function khmerObjectivesFor(array $project): string
    {
        $items = [
            'អនុវត្តវិសាលភាពគម្រោងដោយសម្របសម្រួលរវាងម្ចាស់គម្រោង ក្រុមរចនា និងក្រុមការដ្ឋាន។',
            'រក្សាគុណភាពសំណង់ កាលវិភាគ និងសុវត្ថិភាពការងារនៅការដ្ឋាន។',
            'ផ្តល់អគារឬហេដ្ឋារចនាសម្ព័ន្ធដែលរឹងមាំ និងគាំទ្រតម្រូវការប្រើប្រាស់របស់ '.$project['client'].'។',
        ];

        return '<ul><li>'.implode('</li><li>', $items).'</li></ul>';
    }

    /**
     * @param array<string, mixed> $project
     */
    private function designConceptFor(array $project, string $categorySlug): string
    {
        $categoryFocus = match ($categorySlug) {
            'healthcare' => 'patient flow, service access, and maintainable building systems',
            'education' => 'learning spaces, daily circulation, and long-term campus use',
            'energy' => 'utility reliability, equipment access, and operational safety',
            'commercial' => 'public access, service efficiency, and professional presentation',
            'infrastructure' => 'public utility performance, site resilience, and maintainability',
            default => 'institutional function, public access, and long-term durability',
        };

        return '<p>The design and construction approach focused on '.$categoryFocus.'. Kimmex aligned execution with the documented scale, location, and schedule requirements.</p>';
    }

    /**
     * @param array<string, mixed> $project
     */
    private function khmerDesignConceptFor(array $project): string
    {
        return '<p>គំនិតរចនា និងការអនុវត្តសំណង់ផ្តោតលើមុខងារប្រើប្រាស់ ការចូលប្រើប្រាស់ ការថែទាំ និងភាពរឹងមាំរយៈពេលវែង។ Kimmex បានសម្របការអនុវត្តតាមទំហំ ទីតាំង និងកាលវិភាគគម្រោង។</p>';
    }

    /**
     * @param array<string, mixed> $project
     */
    private function scopeFor(array $project): string
    {
        $items = [
            'Project owner: '.$project['client'],
            'Construction location: '.$project['location'],
            'Construction value: '.$project['construction_value'],
            'Built area: '.$project['area'],
            'Floors: '.$project['floors'],
            'Duration: '.$project['duration'],
        ];

        return '<ul><li>'.implode('</li><li>', array_filter($items)).'</li></ul>';
    }

    /**
     * @param array<string, mixed> $project
     */
    private function engineeringNarrativeFor(array $project): string
    {
        return '<p>Kimmex managed construction execution through practical sequencing, site coordination, and quality checks across the project lifecycle.</p>'
            .'<p>The work required coordination of resources, materials, and schedule milestones to keep delivery aligned with client expectations.</p>';
    }

    /**
     * @param array<string, mixed> $project
     */
    private function khmerEngineeringNarrativeFor(array $project): string
    {
        return '<p>Kimmex បានគ្រប់គ្រងការអនុវត្តសំណង់តាមលំដាប់ការងារ ការសម្របសម្រួលការដ្ឋាន និងការត្រួតពិនិត្យគុណភាពក្នុងវដ្តជីវិតគម្រោង។</p>'
            .'<p>ការងារនេះត្រូវការការសម្របសម្រួលធនធាន សម្ភារៈ និងកាលវិភាគ ដើម្បីឱ្យការប្រគល់ស្របតាមការរំពឹងទុករបស់ម្ចាស់គម្រោង។</p>';
    }

    /**
     * @param array<string, mixed> $project
     */
    private function khmerScopeFor(array $project): string
    {
        $items = [
            'ម្ចាស់គម្រោង: '.$project['client'],
            'ទីតាំងសាងសង់: '.$project['location'],
            'តម្លៃសាងសង់: '.$project['construction_value'],
            'ផ្ទៃក្រឡា: '.$project['area'],
            'ចំនួនជាន់: '.$project['floors'],
            'រយៈពេល: '.$project['duration'],
        ];

        return '<ul><li>'.implode('</li><li>', array_filter($items)).'</li></ul>';
    }

    private function formatMoney(string $value): string
    {
        if ($value === '') {
            return '';
        }

        return '$'.number_format((float) $value, 2);
    }

    private function formatArea(string $value): string
    {
        if ($value === '') {
            return '';
        }

        if (is_numeric($value)) {
            return number_format((float) $value).' m²';
        }

        return $value;
    }
}
