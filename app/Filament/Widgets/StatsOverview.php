<?php

namespace App\Filament\Widgets;

use App\Models\Inquiry;
use App\Models\JobApplication;
use App\Models\NewsArticle;
use App\Models\PageView;
use App\Models\Project;
use App\Models\JobPosting;
use App\Models\Subscriber;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // Sparkline data: last 7 days
        $inquirySparkline = $this->last7Days(Inquiry::class);
        $appSparkline = $this->last7Days(JobApplication::class);
        $viewSparkline = $this->last7DaysViews();

        // Key numbers
        $unreadInquiries = Inquiry::where('is_read', false)->count();
        $inquiriesThisMonth = Inquiry::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)->count();

        $appsThisMonth = JobApplication::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)->count();

        $activeProjects = Project::where('status', 'ONGOING')->count();
        $openJobs = JobPosting::where('status', \App\Enums\JobPostingStatus::OPEN)->count();
        $subscribers = Subscriber::active()->count();
        $viewsToday = PageView::where('visited_at', '>=', Carbon::today())->count();

        return [
            Stat::make(__('Inquiries'), $inquiriesThisMonth)
                ->description($unreadInquiries > 0 ? $unreadInquiries . ' ' . __('unread') : __('All read ✓'))
                ->descriptionIcon($unreadInquiries > 0 ? 'heroicon-m-envelope' : 'heroicon-m-check-circle')
                ->color($unreadInquiries > 0 ? 'warning' : 'success')
                ->chart($inquirySparkline),

            Stat::make(__('Applications'), $appsThisMonth)
                ->description(__('This month'))
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary')
                ->chart($appSparkline),

            Stat::make(__('Page Views'), $viewsToday)
                ->description(__('Today'))
                ->descriptionIcon('heroicon-m-eye')
                ->color('info')
                ->chart($viewSparkline),

            Stat::make(__('Active Projects'), $activeProjects)
                ->description(Project::where('status', 'COMPLETED')->count() . ' ' . __('completed'))
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('success'),

            Stat::make(__('Open Jobs'), $openJobs)
                ->description(__('Accepting applications'))
                ->descriptionIcon('heroicon-m-briefcase')
                ->color('gray'),

            Stat::make(__('Subscribers'), $subscribers)
                ->description(__('Newsletter'))
                ->descriptionIcon('heroicon-m-users')
                ->color('gray'),
        ];
    }

    private function last7Days(string $model): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $data[] = $model::whereDate('created_at', Carbon::now()->subDays($i)->toDateString())->count();
        }
        return $data;
    }

    private function last7DaysViews(): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $data[] = PageView::whereDate('visited_at', Carbon::now()->subDays($i)->toDateString())->count();
        }
        return $data;
    }
}
