<?php

namespace App\Filament\Pages;

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
        return auth()->user()?->isAdmin() ?? false;
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\PageAnalyticsStatsWidget::class,
            \App\Filament\Widgets\PageViewsChartWidget::class,
            \App\Filament\Widgets\TopPagesChartWidget::class,
            \App\Filament\Widgets\TrafficByHourChartWidget::class,
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
