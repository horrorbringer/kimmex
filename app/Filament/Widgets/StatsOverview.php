<?php

namespace App\Filament\Widgets;

use App\Enums\JobPostingStatus;
use App\Models\Inquiry;
use App\Models\JobApplication;
use App\Models\JobPosting;
use App\Models\PageView;
use App\Models\Project;
use App\Models\Subscriber;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected ?string $pollingInterval = '60s';

    protected function getStats(): array
    {
        $stats = Cache::remember('admin_dashboard_stats', now()->addMinute(), function (): array {
            $today = now()->startOfDay();
            $monthStart = now()->startOfMonth();

            $inquirySparkline = $this->dailyCounts(Inquiry::class, 'created_at', $today);
            $appSparkline = $this->dailyCounts(JobApplication::class, 'created_at', $today);
            $viewSparkline = $this->dailyCounts(PageView::class, 'visited_at', $today);

            $projectCounts = Project::query()
                ->selectRaw("SUM(status = 'ONGOING') as active, SUM(status = 'COMPLETED') as completed")
                ->first();

            return [
                'inquiry_sparkline' => $inquirySparkline,
                'application_sparkline' => $appSparkline,
                'view_sparkline' => $viewSparkline,
                'unread_inquiries' => Inquiry::where('is_read', false)->count(),
                'inquiries_this_month' => Inquiry::whereBetween('created_at', [$monthStart, $today->copy()->addDay()])->count(),
                'applications_this_month' => JobApplication::whereBetween('created_at', [$monthStart, $today->copy()->addDay()])->count(),
                'active_projects' => (int) ($projectCounts->active ?? 0),
                'completed_projects' => (int) ($projectCounts->completed ?? 0),
                'open_jobs' => JobPosting::where('status', JobPostingStatus::OPEN)->count(),
                'subscribers' => Subscriber::active()->count(),
                'views_today' => $viewSparkline[array_key_last($viewSparkline)],
            ];
        });

        return [
            Stat::make(__('Inquiries'), $stats['inquiries_this_month'])
                ->description($stats['unread_inquiries'] > 0 ? $stats['unread_inquiries'].' '.__('unread') : __('All read ✓'))
                ->descriptionIcon($stats['unread_inquiries'] > 0 ? 'heroicon-m-envelope' : 'heroicon-m-check-circle')
                ->color($stats['unread_inquiries'] > 0 ? 'warning' : 'success')
                ->chart($stats['inquiry_sparkline']),

            Stat::make(__('Applications'), $stats['applications_this_month'])
                ->description(__('This month'))
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary')
                ->chart($stats['application_sparkline']),

            Stat::make(__('Page Views'), $stats['views_today'])
                ->description(__('Today'))
                ->descriptionIcon('heroicon-m-eye')
                ->color('info')
                ->chart($stats['view_sparkline']),

            Stat::make(__('Active Projects'), $stats['active_projects'])
                ->description($stats['completed_projects'].' '.__('completed'))
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('success'),

            Stat::make(__('Open Jobs'), $stats['open_jobs'])
                ->description(__('Accepting applications'))
                ->descriptionIcon('heroicon-m-briefcase')
                ->color('gray'),

            Stat::make(__('Subscribers'), $stats['subscribers'])
                ->description(__('Newsletter'))
                ->descriptionIcon('heroicon-m-users')
                ->color('gray'),
        ];
    }

    private function dailyCounts(string $model, string $column, Carbon $today): array
    {
        $start = $today->copy()->subDays(6);
        $counts = $model::query()
            ->whereBetween($column, [$start, $today->copy()->addDay()])
            ->selectRaw("DATE({$column}) as date, COUNT(*) as aggregate")
            ->groupBy('date')
            ->pluck('aggregate', 'date');

        return collect(range(6, 0))
            ->map(fn (int $daysAgo): int => (int) ($counts[$today->copy()->subDays($daysAgo)->toDateString()] ?? 0))
            ->all();
    }
}
