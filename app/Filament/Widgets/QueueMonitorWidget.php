<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class QueueMonitorWidget extends Widget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected string $view = 'filament.widgets.queue-monitor-widget';

    protected static ?int $pollingInterval = 10;

    public function getQueueStats(): array
    {
        $pending = DB::table('jobs')->count();
        $failed = DB::table('failed_jobs')->count();

        // Jobs by queue
        $byQueue = DB::table('jobs')
            ->selectRaw('queue, COUNT(*) as count')
            ->groupBy('queue')
            ->pluck('count', 'queue')
            ->toArray();

        // Oldest job age
        $oldestJob = DB::table('jobs')->min('created_at');
        $oldestAge = $oldestJob ? now()->diffForHumans(Carbon::createFromTimestamp($oldestJob), short: true) : null;

        // Recent failed jobs
        $recentFailed = DB::table('failed_jobs')
            ->latest('failed_at')
            ->limit(3)
            ->get(['payload', 'failed_at', 'exception'])
            ->map(function ($job) {
                $payload = json_decode($job->payload, true);

                return [
                    'name' => class_basename($payload['displayName'] ?? 'Unknown'),
                    'failed_at' => Carbon::parse($job->failed_at)->diffForHumans(short: true),
                    'error' => Str::limit(explode("\n", $job->exception)[0] ?? '', 60),
                ];
            })->toArray();

        return [
            'pending' => $pending,
            'failed' => $failed,
            'byQueue' => $byQueue,
            'oldestAge' => $oldestAge,
            'recentFailed' => $recentFailed,
            'healthy' => $pending < 50 && $failed === 0,
            'warning' => $pending >= 50 || $failed > 0,
        ];
    }
}
