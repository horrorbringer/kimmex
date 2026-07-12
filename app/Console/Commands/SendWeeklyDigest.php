<?php

namespace App\Console\Commands;

use App\Mail\WeeklyDigestMail;
use App\Models\NewsArticle;
use App\Models\Project;
use App\Models\Subscriber;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendWeeklyDigest extends Command
{
    protected $signature = 'digest:send';

    protected $description = 'Send a weekly digest email with new articles and projects from the last 7 days';

    public function handle(): int
    {
        $since = now()->subDays(7);

        $articles = NewsArticle::where('isActive', true)
            ->where('publishedAt', '>=', $since)
            ->orderByDesc('publishedAt')
            ->get();

        $projects = Project::where('isActive', true)
            ->where('created_at', '>=', $since)
            ->orderByDesc('created_at')
            ->with('projectCategory')
            ->get();

        if ($articles->isEmpty() && $projects->isEmpty()) {
            $this->info('No new content published in the last 7 days. No digest sent.');
            Log::info('Weekly Digest: skipped — no new content.');

            return self::SUCCESS;
        }

        $this->info("Found {$articles->count()} article(s) and {$projects->count()} project(s).");

        $subscribers = Subscriber::active()
            ->get()
            ->filter(function (Subscriber $subscriber) {
                $tags = $subscriber->tags;

                // If subscriber has no tags, they receive everything
                if (empty($tags)) {
                    return true;
                }

                // Only send if subscriber has 'news' or 'general' tag
                return in_array('news', $tags) || in_array('general', $tags);
            });

        if ($subscribers->isEmpty()) {
            $this->info('No eligible subscribers found.');
            Log::info('Weekly Digest: skipped — no eligible subscribers.');

            return self::SUCCESS;
        }

        $sentCount = 0;

        foreach ($subscribers as $subscriber) {
            Mail::to($subscriber->email)->queue(
                new WeeklyDigestMail($articles, $projects, $subscriber)
            );
            $sentCount++;
        }

        $this->info("Weekly digest queued for {$sentCount} subscriber(s).");
        Log::info("Weekly Digest: sent to {$sentCount} subscriber(s).", [
            'articles_count' => $articles->count(),
            'projects_count' => $projects->count(),
        ]);

        return self::SUCCESS;
    }
}
