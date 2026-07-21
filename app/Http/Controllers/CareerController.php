<?php

namespace App\Http\Controllers;

use App\Enums\JobPostingStatus;
use App\Models\JobPosting;
use App\Support\RichContent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CareerController extends Controller
{
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
                'description' => $pickTranslation($jobDb, 'summary'),
                'responsibilities' => $pickTranslation($jobDb, 'responsibilities'),
                'requirements' => $pickTranslation($jobDb, 'requirements'),
                'benefits' => $pickTranslation($jobDb, 'benefits'),
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

        $renderRichText = fn (?string $content) => RichContent::render($content);

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
        ));
    }
}
