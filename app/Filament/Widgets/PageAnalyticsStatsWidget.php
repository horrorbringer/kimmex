<?php

namespace App\Filament\Widgets;

use App\Models\PageView;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class PageAnalyticsStatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 4;

    public static function canView(): bool
    {
        return true;
    }

    protected function getStats(): array
    {
        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek();
        $startOfMonth = Carbon::now()->startOfMonth();

        $todayCount = PageView::where('visited_at', '>=', $today)->count();
        $weekCount = PageView::where('visited_at', '>=', $startOfWeek)->count();
        $monthCount = PageView::where('visited_at', '>=', $startOfMonth)->count();
        $totalCount = PageView::count();

        // Get daily trend for sparkline (last 7 days)
        $trend = [];
        for ($i = 6; $i >= 0; $i--) {
            $trend[] = PageView::whereDate('visited_at', Carbon::now()->subDays($i)->toDateString())->count();
        }

        return [
            Stat::make(__('Views Today'), number_format($todayCount))
                ->description(__('Page views today'))
                ->descriptionIcon('heroicon-o-eye')
                ->color('primary')
                ->chart($trend),

            Stat::make(__('This Week'), number_format($weekCount))
                ->description(__('Since Monday'))
                ->descriptionIcon('heroicon-o-calendar')
                ->color('success'),

            Stat::make(__('This Month'), number_format($monthCount))
                ->description(__('Since :date', ['date' => $startOfMonth->format('M 1')]))
                ->descriptionIcon('heroicon-o-chart-bar')
                ->color('warning'),

            Stat::make(__('Total'), number_format($totalCount))
                ->description(__('All time'))
                ->descriptionIcon('heroicon-o-globe-alt')
                ->color('gray'),
        ];
    }
}
