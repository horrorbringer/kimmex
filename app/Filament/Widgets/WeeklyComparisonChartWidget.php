<?php

namespace App\Filament\Widgets;

use App\Models\PageView;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class WeeklyComparisonChartWidget extends ChartWidget
{
    protected static ?int $sort = 11;
    protected int | string | array $columnSpan = 'full';
    protected ?string $maxHeight = '280px';

    public function getHeading(): ?string
    {
        return __('Weekly Comparison');
    }

    public function getDescription(): ?string
    {
        return __('This week vs last week');
    }

    public static function canView(): bool
    {
        return true;
    }

    protected function getData(): array
    {
        $startOfThisWeek = Carbon::now()->startOfWeek();
        $startOfLastWeek = Carbon::now()->startOfWeek()->subWeek();

        $thisWeekData = [];
        $lastWeekData = [];

        for ($i = 0; $i < 7; $i++) {
            $thisWeekData[] = PageView::whereDate('visited_at', $startOfThisWeek->copy()->addDays($i))
                ->count();

            $lastWeekData[] = PageView::whereDate('visited_at', $startOfLastWeek->copy()->addDays($i))
                ->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'This Week',
                    'data' => $thisWeekData,
                    'borderColor' => '#6366f1',
                    'backgroundColor' => 'rgba(99,102,241,0.08)',
                    'fill' => 'origin',
                    'tension' => 0.4,
                    'pointRadius' => 4,
                    'pointHoverRadius' => 7,
                ],
                [
                    'label' => 'Last Week',
                    'data' => $lastWeekData,
                    'borderColor' => '#94a3b8',
                    'backgroundColor' => 'rgba(148,163,184,0.05)',
                    'borderDash' => [5, 5],
                    'fill' => 'origin',
                    'tension' => 0.4,
                    'pointRadius' => 3,
                    'pointHoverRadius' => 6,
                ],
            ],
            'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'top',
                ],
            ],
            'scales' => [
                'x' => [
                    'grid' => ['display' => false],
                ],
                'y' => [
                    'beginAtZero' => true,
                    'grid' => [
                        'color' => 'rgba(148, 163, 184, 0.1)',
                    ],
                ],
            ],
        ];
    }
}
