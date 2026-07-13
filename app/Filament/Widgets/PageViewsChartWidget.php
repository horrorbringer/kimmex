<?php

namespace App\Filament\Widgets;

use App\Models\PageView;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class PageViewsChartWidget extends ChartWidget
{
    protected static ?int $sort = 5;
    protected int | string | array $columnSpan = 'full';
    protected ?string $maxHeight = '300px';

    public function getHeading(): ?string
    {
        return __('Page Views');
    }

    public function getDescription(): ?string
    {
        return __('Last 30 days trend');
    }

    public static function canView(): bool
    {
        return true;
    }

    protected function getData(): array
    {
        $labels = [];
        $data = [];

        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $labels[] = $date->format('d M');
            $data[] = PageView::whereDate('visited_at', $date->toDateString())->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Page Views',
                    'data' => $data,
                    'borderColor' => '#6366f1',
                    'backgroundColor' => 'rgba(99, 102, 241, 0.08)',
                    'borderWidth' => 2.5,
                    'fill' => 'origin',
                    'tension' => 0.4,
                    'pointRadius' => 0,
                    'pointHoverRadius' => 6,
                    'pointHoverBackgroundColor' => '#6366f1',
                    'pointHoverBorderColor' => '#ffffff',
                    'pointHoverBorderWidth' => 3,
                ],
            ],
            'labels' => $labels,
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
                'legend' => ['display' => false],
                'tooltip' => [
                    'mode' => 'index',
                    'intersect' => false,
                    'backgroundColor' => '#1e293b',
                    'titleColor' => '#f8fafc',
                    'bodyColor' => '#e2e8f0',
                    'borderColor' => '#475569',
                    'borderWidth' => 1,
                    'padding' => 12,
                    'cornerRadius' => 8,
                    'displayColors' => false,
                ],
            ],
            'scales' => [
                'x' => [
                    'grid' => ['display' => false],
                    'ticks' => [
                        'maxTicksLimit' => 10,
                        'color' => '#94a3b8',
                        'font' => ['size' => 11],
                    ],
                    'border' => ['display' => false],
                ],
                'y' => [
                    'beginAtZero' => true,
                    'grid' => [
                        'color' => 'rgba(148, 163, 184, 0.1)',
                        'drawBorder' => false,
                    ],
                    'ticks' => [
                        'stepSize' => 1,
                        'color' => '#94a3b8',
                        'font' => ['size' => 11],
                        'padding' => 8,
                    ],
                    'border' => ['display' => false],
                ],
            ],
            'interaction' => [
                'mode' => 'nearest',
                'axis' => 'x',
                'intersect' => false,
            ],
            'elements' => [
                'line' => [
                    'borderCapStyle' => 'round',
                    'borderJoinStyle' => 'round',
                ],
            ],
        ];
    }
}
