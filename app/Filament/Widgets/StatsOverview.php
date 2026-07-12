<?php

namespace App\Filament\Widgets;

use App\Models\Inquiry;
use App\Models\JobApplication;
use App\Models\NewsArticle;
use App\Models\Project;
use App\Models\JobPosting;
use App\Models\Testimonial;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // Projects
        $totalProjects      = Project::count();
        $ongoingProjects    = Project::where('status', 'ONGOING')->count();
        $completedProjects  = Project::where('status', 'COMPLETED')->count();

        // Inquiries — this month vs last month
        $inquiriesThisMonth = Inquiry::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)->count();
        $inquiriesLastMonth = Inquiry::whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)->count();
        $unreadInquiries    = Inquiry::where('is_read', false)->count();

        // Job Applications — this month vs last month
        $appsThisMonth = JobApplication::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)->count();
        $appsLastMonth = JobApplication::whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)->count();

        // News
        $publishedNews      = NewsArticle::where('isActive', true)->count();
        $newsThisMonth      = NewsArticle::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)->count();

        // Active job postings
        $activeJobPostings  = JobPosting::where('status', \App\Enums\JobPostingStatus::OPEN)->count();

        // Trend helpers
        $inquiryTrend   = $this->trendDescription($inquiriesThisMonth, $inquiriesLastMonth);
        $appTrend       = $this->trendDescription($appsThisMonth, $appsLastMonth);

        return [
            Stat::make(__('Total Projects'), $totalProjects)
                ->description("{$ongoingProjects} ongoing · {$completedProjects} completed")
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('primary'),

            Stat::make(__('Inquiries This Month'), $inquiriesThisMonth)
                ->description($inquiryTrend['text'] . ' · ' . $unreadInquiries . ' unread')
                ->descriptionIcon($inquiryTrend['icon'])
                ->color($inquiryTrend['color']),

            Stat::make(__('Job Applications This Month'), $appsThisMonth)
                ->description($appTrend['text'])
                ->descriptionIcon($appTrend['icon'])
                ->color($appTrend['color']),

            Stat::make(__('Published News'), $publishedNews)
                ->description($newsThisMonth . ' published this month')
                ->descriptionIcon('heroicon-m-newspaper')
                ->color('info'),

            Stat::make(__('Open Job Postings'), $activeJobPostings)
                ->description(__('Currently accepting applications'))
                ->descriptionIcon('heroicon-m-briefcase')
                ->color('success'),

            Stat::make(__('Unread Inquiries'), $unreadInquiries)
                ->description($unreadInquiries > 0 ? __('Pending your attention') : __('All caught up!'))
                ->descriptionIcon($unreadInquiries > 0 ? 'heroicon-m-envelope' : 'heroicon-m-check-circle')
                ->color($unreadInquiries > 0 ? 'warning' : 'success'),
        ];
    }

    private function trendDescription(int $current, int $previous): array
    {
        if ($previous === 0) {
            return [
                'text'  => $current > 0 ? "+{$current} vs last month" : 'No data last month',
                'icon'  => $current > 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-minus',
                'color' => $current > 0 ? 'success' : 'gray',
            ];
        }

        $diff    = $current - $previous;
        $percent = round(abs($diff) / $previous * 100);

        if ($diff > 0) {
            return [
                'text'  => "+{$percent}% vs last month",
                'icon'  => 'heroicon-m-arrow-trending-up',
                'color' => 'success',
            ];
        }

        if ($diff < 0) {
            return [
                'text'  => "-{$percent}% vs last month",
                'icon'  => 'heroicon-m-arrow-trending-down',
                'color' => 'danger',
            ];
        }

        return [
            'text'  => 'Same as last month',
            'icon'  => 'heroicon-m-minus',
            'color' => 'gray',
        ];
    }
}
