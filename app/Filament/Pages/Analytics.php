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

    public function getStats(): array
    {
        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek();
        $startOfMonth = Carbon::now()->startOfMonth();

        return [
            'today' => PageView::where('visited_at', '>=', $today)->count(),
            'this_week' => PageView::where('visited_at', '>=', $startOfWeek)->count(),
            'this_month' => PageView::where('visited_at', '>=', $startOfMonth)->count(),
            'total' => PageView::count(),
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

    public function getDailyViews(): Collection
    {
        return PageView::selectRaw('DATE(visited_at) as date')
            ->selectRaw('COUNT(*) as views')
            ->where('visited_at', '>=', Carbon::now()->subDays(30))
            ->groupByRaw('DATE(visited_at)')
            ->orderBy('date')
            ->get();
    }
}
