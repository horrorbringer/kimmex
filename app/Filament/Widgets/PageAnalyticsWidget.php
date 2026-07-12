<?php

namespace App\Filament\Widgets;

use App\Models\PageView;
use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class PageAnalyticsWidget extends Widget
{
    protected string $view = 'filament.widgets.page-analytics-widget';

    protected static ?int $sort = 5;

    protected int | string | array $columnSpan = 'full';

    public function getStats(): array
    {
        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek();
        $startOfMonth = Carbon::now()->startOfMonth();

        return [
            'today' => PageView::where('visited_at', '>=', $today)->count(),
            'this_week' => PageView::where('visited_at', '>=', $startOfWeek)->count(),
            'this_month' => PageView::where('visited_at', '>=', $startOfMonth)->count(),
        ];
    }

    public function getTopPages(): Collection
    {
        return PageView::select('path')
            ->selectRaw('COUNT(*) as views')
            ->selectRaw('MAX(title) as title')
            ->where('visited_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('path')
            ->orderByDesc('views')
            ->limit(10)
            ->get();
    }
}
