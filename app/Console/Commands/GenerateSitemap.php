<?php

namespace App\Console\Commands;

use App\Enums\JobPostingStatus;
use App\Models\Document;
use App\Models\JobPosting;
use App\Models\NewsArticle;
use App\Models\Project;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';

    protected $description = 'Generate a static sitemap.xml file in the public directory';

    public function handle(): int
    {
        $this->info('Generating sitemap...');

        $urls = $this->collectUrls();
        $xml = $this->buildXml($urls);

        $path = public_path('sitemap.xml');
        file_put_contents($path, $xml);

        $this->info("Sitemap generated with {$urls->count()} URLs at: {$path}");

        return self::SUCCESS;
    }

    protected function collectUrls(): Collection
    {
        $urls = collect();

        $add = function (string $loc, ?Carbon $lastmod = null, string $changefreq = 'monthly', string $priority = '0.7') use ($urls) {
            $urls->push([
                'loc' => url($loc),
                'lastmod' => $lastmod?->toAtomString(),
                'changefreq' => $changefreq,
                'priority' => $priority,
            ]);
        };

        // Static pages
        $add('/', null, 'weekly', '1.0');
        $add('/about', null, 'monthly', '0.8');
        $add('/contact', null, 'monthly', '0.7');

        // Services index
        $serviceLastModified = Service::where('isActive', true)->max('updated_at');
        $add('/services', $serviceLastModified ? Carbon::parse($serviceLastModified) : null, 'weekly', '0.9');

        // Projects index
        $projectLastModified = Project::where('isActive', true)->max('updated_at');
        $add('/projects', $projectLastModified ? Carbon::parse($projectLastModified) : null, 'weekly', '0.9');

        // News index
        $newsLastModified = NewsArticle::where('isActive', true)->where('publishedAt', '<=', now())->max('updated_at');
        $add('/news', $newsLastModified ? Carbon::parse($newsLastModified) : null, 'weekly', '0.8');

        // Careers index
        $jobLastModified = JobPosting::where('status', JobPostingStatus::OPEN)->max('updated_at');
        $add('/careers', $jobLastModified ? Carbon::parse($jobLastModified) : null, 'weekly', '0.7');

        // Documents index (only if public documents exist)
        $documentLastModified = Document::publiclyVisible()->max('updated_at');
        if ($documentLastModified) {
            $add('/documents', Carbon::parse($documentLastModified), 'weekly', '0.7');
        }

        // Individual active services
        Service::where('isActive', true)->select('slug', 'updated_at')->orderBy('orderIndex')->lazy()
            ->each(fn (Service $service) => $add("/services/{$service->slug}", $service->updated_at, 'monthly', '0.8'));

        // Individual active projects
        Project::where('isActive', true)->select('slug', 'updated_at')->latest('updated_at')->lazy()
            ->each(fn (Project $project) => $add("/projects/{$project->slug}", $project->updated_at, 'monthly', '0.8'));

        // Published news articles
        NewsArticle::where('isActive', true)->where('publishedAt', '<=', now())->select('slug', 'updated_at')->orderByDesc('publishedAt')->lazy()
            ->each(fn (NewsArticle $article) => $add("/news/{$article->slug}", $article->updated_at, 'weekly', '0.7'));

        // Public documents
        Document::publiclyVisible()->select('slug', 'updated_at')->latest('updated_at')->lazy()
            ->each(fn (Document $document) => $add("/documents/{$document->slug}", $document->updated_at, 'monthly', '0.6'));

        // Open job postings
        JobPosting::where('status', 'OPEN')->select('slug', 'updated_at')->latest('updated_at')->lazy()
            ->each(fn (JobPosting $job) => $add("/careers/{$job->slug}", $job->updated_at, 'weekly', '0.6'));

        return $urls;
    }

    protected function buildXml(Collection $urls): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";

        foreach ($urls as $url) {
            $xml .= "    <url>\n";
            $xml .= '        <loc>'.htmlspecialchars($url['loc'], ENT_XML1, 'UTF-8')."</loc>\n";
            if (! empty($url['lastmod'])) {
                $xml .= "        <lastmod>{$url['lastmod']}</lastmod>\n";
            }
            $xml .= "        <changefreq>{$url['changefreq']}</changefreq>\n";
            $xml .= "        <priority>{$url['priority']}</priority>\n";
            $xml .= "    </url>\n";
        }

        $xml .= '</urlset>'."\n";

        return $xml;
    }
}
