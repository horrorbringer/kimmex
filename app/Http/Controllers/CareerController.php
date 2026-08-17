<?php

namespace App\Http\Controllers;

use App\Enums\JobPostingStatus;
use App\Models\JobPosting;
use App\Models\SystemSetting;
use App\Support\PublicStorage;
use App\Support\RichContent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CareerController extends Controller
{
    public function index(): View
    {
        $locale = app()->getLocale();

        $jobs = Cache::remember('careers_jobs_data_'.$locale, now()->addHours(12), function () use ($locale): array {
            $jobsDb = JobPosting::where('status', JobPostingStatus::OPEN)
                ->with('department')
                ->orderBy('created_at', 'desc')
                ->get();

            return $jobsDb->map(function (JobPosting $j) use ($locale): array {
                $deptName = $j->department ? $j->department->getTranslation('name', $locale) : __('General');

                return [
                    'id' => $j->id,
                    'slug' => $j->slug,
                    'title' => $j->getTranslation('title', $locale),
                    'dept' => $deptName,
                    'loc' => $j->getTranslation('location', $locale),
                    'type' => __(str_replace('_', ' ', Str::title(strtolower($j->type ?? 'FULL_TIME')))),
                    'salary' => $j->getTranslation('salary', $locale) ?: __('Negotiable'),
                    'experience' => $j->getTranslation('experience', $locale) ?: __('2-3 Years'),
                    'postedDate' => $j->created_at ? $j->created_at->format('M d, Y') : now()->format('M d, Y'),
                    'postedAt' => $j->created_at?->toIso8601String(),
                    'tags' => [$deptName],
                    'summary' => Str::limit(strip_tags((string) $j->getTranslation('summary', $locale)), 150),
                ];
            })->toArray();
        });

        if (empty($jobs)) {
            $jobs = [
                [
                    'id' => 'gen',
                    'slug' => 'gen',
                    'title' => __('Visionary Talent'),
                    'dept' => __('General'),
                    'loc' => __('Phnom Penh'),
                    'type' => __('Full-time'),
                    'salary' => __('Competitive'),
                    'experience' => __('Mixed'),
                    'postedDate' => now()->format('M d, Y'),
                    'postedAt' => now()->toIso8601String(),
                    'tags' => [__('Hiring')],
                    'summary' => __('We are always looking for exceptional engineers and managers.'),
                ],
            ];
        }

        $categories = array_values(array_unique(array_merge([__('All')], array_column($jobs, 'dept'))));
        $locations = array_values(array_unique(array_merge([__('All Locations')], array_column($jobs, 'loc'))));

        return view('pages.careers', compact('jobs', 'categories', 'locations'));
    }

    public function show(Request $request, string $slug): View|RedirectResponse
    {
        $job = Cache::remember("career_job_show_data_{$slug}_".app()->getLocale(), now()->addHours(12), function () use ($slug): ?array {
            $jobDb = JobPosting::where('status', JobPostingStatus::OPEN)->where('slug', $slug)->first();

            if (! $jobDb) {
                return null;
            }

            $pickTranslation = function ($model, string $field, array $fallbackLocales = []) {
                $translations = $model->getTranslations($field);
                $locales = $fallbackLocales ?: [app()->getLocale(), 'km', 'kh', 'en'];

                foreach ($locales as $locale) {
                    $value = trim((string) ($translations[$locale] ?? ''));

                    if ($value !== '' && ! str_contains($value, "\u{FFFD}")) {
                        return $value;
                    }
                }

                return trim((string) ($translations['km'] ?? $translations['kh'] ?? $translations['en'] ?? ''));
            };

            return [
                'id' => $jobDb->id,
                'title' => $pickTranslation($jobDb, 'title'),
                'dept' => $jobDb->department ? $pickTranslation($jobDb->department, 'name') : __('General'),
                'loc' => $pickTranslation($jobDb, 'location'),
                'type' => __($jobDb->type ?? 'FULL_TIME'),
                'salary' => $pickTranslation($jobDb, 'salary') ?: __('Negotiable'),
                'experience' => $pickTranslation($jobDb, 'experience') ?: __('2-3 Years'),
                'postedDate' => $jobDb->created_at ? $jobDb->created_at->format('M d, Y') : now()->format('M d, Y'),
                'postedAt' => $jobDb->created_at?->toIso8601String(),
                'description' => $pickTranslation($jobDb, 'summary'),
                'responsibilities' => $pickTranslation($jobDb, 'responsibilities'),
                'requirements' => $pickTranslation($jobDb, 'requirements'),
                'benefits' => $pickTranslation($jobDb, 'benefits'),
                'telegramQr' => $jobDb->telegramQr,
                'telegramUrl' => $jobDb->telegramUrl,
                'telegramChannelId' => $jobDb->telegramChannelId,
            ];
        });

        // Special general application fallback
        if (! $job && $slug === 'gen') {
            $job = [
                'id' => 'gen',
                'title' => __('Visionary Talent'),
                'dept' => __('General'),
                'loc' => __('Phnom Penh'),
                'type' => __('Full-time'),
                'salary' => __('Competitive'),
                'experience' => __('Mixed'),
                'postedDate' => now()->format('M d, Y'),
                'postedAt' => now()->toIso8601String(),
                'description' => __('We are always looking for exceptional engineers and managers. Even if there is no specific opening that matches your profile, we encourage you to submit your general application.'),
                'responsibilities' => '<ul><li>'.__('Willingness to learn and grow within the Kimmex ecosystem').'</li><li>'.__('Contributing to various projects across departments').'</li><li>'.__('Maintaining professional excellence in all tasks').'</li></ul>',
                'requirements' => '<ul><li>'.__('Strong technical background in engineering or construction').'</li><li>'.__('Passion for innovation and quality').'</li><li>'.__('Excellent teamwork and communication skills').'</li></ul>',
                'benefits' => '<ul><li>'.__('Competitive compensation package').'</li><li>'.__('Continuous professional development').'</li><li>'.__('Opportunity to work on landmark projects').'</li></ul>',
            ];
        }

        if (! $job) {
            return redirect()->route('careers')
                ->with('flash_warning', __('The job posting you were looking for could not be found.'));
        }

        $heroSummary = Str::limit(strip_tags($job['description'] ?? ''), 180);
        $pageTitle = $job['title'] ?? __('Job Details');
        $pageDesc = $heroSummary ?: __('Join our team of experts in the construction and investment industry.');
        $canonicalUrl = $slug === 'gen' ? url('/careers/gen') : route('careers.show', ['slug' => $slug]);
        $selectedTelegramChannel = collect(SystemSetting::get('career_telegram_channels', []))
            ->first(fn ($channel): bool => is_array($channel) && ($channel['id'] ?? null) === ($job['telegramChannelId'] ?? null));
        $telegramQrPath = $selectedTelegramChannel['qr'] ?? $job['telegramQr'] ?? null;
        $telegramQrUrl = PublicStorage::urlIfExists($telegramQrPath);
        $telegramUrl = trim((string) ($selectedTelegramChannel['url'] ?? $job['telegramUrl'] ?? ''));
        $telegramUrl = Str::startsWith($telegramUrl, ['https://', 'http://']) ? $telegramUrl : null;
        $postedRelative = Carbon::parse($job['postedAt'] ?? $job['postedDate'] ?? now())
            ->locale(app()->getLocale())
            ->diffForHumans(null, null, false, 1, Carbon::JUST_NOW);

        $renderRichText = fn (?string $content) => RichContent::renderProject($content);

        $renderParagraphContent = function (?string $content) {
            $content = trim((string) $content);

            if ($content === '') {
                return '';
            }

            if (preg_match('/<\s*(p|h[1-6]|blockquote|table|img|br)\b/i', $content)) {
                return $content;
            }

            $content = preg_replace('/\s+/u', ' ', $content) ?: $content;

            return '<p>'.e($content).'</p>';
        };

        return view('pages.careers.show', compact(
            'job',
            'slug',
            'heroSummary',
            'pageTitle',
            'pageDesc',
            'canonicalUrl',
            'renderRichText',
            'renderParagraphContent',
            'telegramQrUrl',
            'telegramUrl',
            'postedRelative',
        ));
    }
}
