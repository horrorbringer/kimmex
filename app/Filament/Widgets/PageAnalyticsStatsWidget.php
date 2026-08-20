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
        $sevenDaysAgo = Carbon::now()->subDays(6)->startOfDay();

        $earliest = min($startOfMonth, $sevenDaysAgo);

        $dailyCounts = PageView::query()
            ->where('visited_at', '>=', $earliest)
            ->selectRaw('DATE(visited_at) as view_date, COUNT(*) as aggregate')
            ->groupBy('view_date')
            ->pluck('aggregate', 'view_date');

        $totalCount = PageView::count();

        $todayKey = $today->toDateString();
        $todayCount = (int) ($dailyCounts[$todayKey] ?? 0);

        $weekCount = 0;
        for ($date = $startOfWeek->copy(); $date <= $today; $date->addDay()) {
            $weekCount += (int) ($dailyCounts[$date->toDateString()] ?? 0);
        }

        $monthCount = 0;
        for ($date = $startOfMonth->copy(); $date <= $today; $date->addDay()) {
            $monthCount += (int) ($dailyCounts[$date->toDateString()] ?? 0);
        }

        // Get daily trend for sparkline (last 7 days)
        $trend = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = Carbon::now()->subDays($i)->toDateString();
            $trend[] = (int) ($dailyCounts[$d] ?? 0);
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
