<?php

namespace Database\Seeders;

use App\Enums\ProjectStatus;
use App\Models\Project;
use App\Models\ProjectCategory;
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

            Project::withoutEvents(fn () => Project::updateOrCreate(
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
                    'scopeContributions' => [
                        'en' => $this->scopeFor($englishProject),
                        'km' => $this->khmerScopeFor($projectData),
                    ],
                ]
            ));
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
            'Construction project: %s for %s, located in %s.',
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
            'គម្រោងសាងសង់ %s សម្រាប់ %s មានទីតាំងនៅ %s។',
            $project['project_name'],
            $project['client'],
            $project['location'],
        );
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
