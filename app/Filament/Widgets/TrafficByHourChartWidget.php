<?php

namespace App\Filament\Widgets;

use App\Models\PageView;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class TrafficByHourChartWidget extends ChartWidget
{
    protected static ?int $sort = 7;
    protected ?string $heading = 'Traffic by Hour (Last 7 Days)';
    protected int | string | array $columnSpan = 1;
    protected ?string $maxHeight = '260px';

    public static function canView(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    protected function getData(): array
    {
        $hours = PageView::selectRaw('HOUR(visited_at) as hour')
            ->selectRaw('COUNT(*) as views')
            ->where('visited_at', '>=', Carbon::now()->subDays(7))
            ->groupByRaw('HOUR(visited_at)')
            ->orderBy('hour')
            ->pluck('views', 'hour')
            ->toArray();

        $labels = [];
        $data = [];

        for ($h = 0; $h < 24; $h++) {
            $labels[] = sprintf('%02d:00', $h);
            $data[] = $hours[$h] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Visits',
                    'data' => $data,
                    'backgroundColor' => array_map(function ($value) use ($data) {
                        $max = max($data) ?: 1;
                        $opacity = 0.3 + (0.7 * ($value / $max));
                        return "rgba(99, 102, 241, {$opacity})";
                    }, $data),
                    'borderRadius' => 4,
                    'borderSkipped' => false,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => ['display' => false],
            ],
            'scales' => [
                'x' => [
                    'grid' => ['display' => false],
                    'ticks' => [
                        'maxTicksLimit' => 12,
                        'color' => '#94a3b8',
                        'font' => ['size' => 10],
                    ],
                    'border' => ['display' => false],
                ],
                'y' => [
                    'beginAtZero' => true,
                    'grid' => [
                        'color' => 'rgba(148, 163, 184, 0.1)',
                    ],
                    'ticks' => [
                        'stepSize' => 1,
                        'color' => '#94a3b8',
                        'font' => ['size' => 10],
                    ],
                    'border' => ['display' => false],
                ],
            ],
        ];
    }
}
