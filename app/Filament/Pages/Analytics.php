<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\BrowserDistributionChartWidget;
use App\Filament\Widgets\ContentSectionsChartWidget;
use App\Filament\Widgets\CountryDistributionChartWidget;
use App\Filament\Widgets\DeviceDistributionChartWidget;
use App\Filament\Widgets\PageAnalyticsStatsWidget;
use App\Filament\Widgets\PageViewsChartWidget;
use App\Filament\Widgets\TopPagesChartWidget;
use App\Filament\Widgets\TrafficByHourChartWidget;
use App\Filament\Widgets\TrafficSourcesChartWidget;
use App\Filament\Widgets\WeeklyComparisonChartWidget;
use App\Models\PageView;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class Analytics extends Page
{
    protected string $view = 'filament.pages.analytics';

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-chart-bar';
    }

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return __('Administration');
    }

    public static function getNavigationLabel(): string
    {
        return __('Page Analytics');
    }

    public static function getNavigationSort(): ?int
    {
        return 98;
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->isAdmin() || auth()->user()?->isEditor();
    }

    protected function getHeaderWidgets(): array
    {
        return [
            PageAnalyticsStatsWidget::class,
            PageViewsChartWidget::class,
            TopPagesChartWidget::class,
            TrafficSourcesChartWidget::class,
            DeviceDistributionChartWidget::class,
            CountryDistributionChartWidget::class,
            TrafficByHourChartWidget::class,
            BrowserDistributionChartWidget::class,
            ContentSectionsChartWidget::class,
            WeeklyComparisonChartWidget::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int|array
    {
        return [
            'default' => 1,
            'md' => 2,
            'xl' => 2,
        ];
    }

    public function getTopPages(): Collection
    {
        return PageView::select('path')
            ->selectRaw('COUNT(*) as views')
            ->selectRaw('MAX(title) as title')
            ->selectRaw('MAX(visited_at) as last_visited')
            ->where('visited_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('path')
            ->orderByDesc('views')
            ->limit(20)
            ->get();
    }

    public function getTopReferers(): Collection
    {
        return PageView::select('referer')
            ->selectRaw('COUNT(*) as visits')
            ->whereNotNull('referer')
            ->where('referer', '!=', '')
            ->where('visited_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('referer')
            ->orderByDesc('visits')
            ->limit(10)
            ->get();
    }
}
