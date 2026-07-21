<?php

namespace App\Console\Commands;

use App\Models\PageView;
use Illuminate\Console\Command;

class BackfillPageViewCountry extends Command
{
    protected $signature = 'pageviews:backfill-country {--limit=500}';

    protected $description = 'Backfill country data for page views that have null country';

    public function handle(): int
    {
        $limit = (int) $this->option('limit');

        $views = PageView::whereNull('country')
            ->whereNotNull('ip')
            ->orderByDesc('visited_at')
            ->limit($limit)
            ->get();

        if ($views->isEmpty()) {
            $this->info('No page views to backfill.');

            return self::SUCCESS;
        }

        $this->info("Backfilling country for {$views->count()} page views...");

        $bar = $this->output->createProgressBar($views->count());
        $resolved = 0;
        $ipCache = [];

        foreach ($views as $view) {
            $ip = $view->ip;

            // Local/private IPs
            if (in_array($ip, ['127.0.0.1', '::1', 'localhost'])) {
                $view->update(['country' => 'Local']);
                $bar->advance();
                $resolved++;

                continue;
            }

            if (! filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                $view->update(['country' => 'Local']);
                $bar->advance();
                $resolved++;

                continue;
            }

            // Check in-memory cache
            if (isset($ipCache[$ip])) {
                $view->update(['country' => $ipCache[$ip]]);
                $bar->advance();
                $resolved++;

                continue;
            }

            // Call geo API (rate limit: ~45 requests/minute for free tier)
            try {
                $response = @file_get_contents("http://ip-api.com/json/{$ip}?fields=country", false, stream_context_create([
                    'http' => ['timeout' => 3],
                ]));

                if ($response) {
                    $data = json_decode($response, true);
                    $country = $data['country'] ?? 'Unknown';
                    $ipCache[$ip] = $country;
                    $view->update(['country' => $country]);
                    $resolved++;
                } else {
                    $view->update(['country' => 'Unknown']);
                }
            } catch (\Throwable) {
                $view->update(['country' => 'Unknown']);
            }

            $bar->advance();

            // Rate limit: ip-api.com allows 45/minute on free tier
            usleep(1500000); // 1.5 second between requests
        }

        $bar->finish();
        $this->newLine();
        $this->info("Done. Resolved {$resolved}/{$views->count()} records.");

        return self::SUCCESS;
    }
}
