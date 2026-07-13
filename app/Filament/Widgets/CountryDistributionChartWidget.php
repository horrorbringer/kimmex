<?php

namespace App\Filament\Widgets;

use App\Models\PageView;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class CountryDistributionChartWidget extends ChartWidget
{
    protected static ?int $sort = 10;
    protected int | string | array $columnSpan = 1;
    protected ?string $maxHeight = '320px';

    public function getHeading(): ?string
    {
        return __('Visitors by Country');
    }

    public function getDescription(): ?string
    {
        return __('Last 30 days');
    }

    public static function canView(): bool
    {
        return true;
    }

    protected function getData(): array
    {
        $countries = PageView::select('country')
            ->selectRaw('COUNT(*) as total')
            ->where('visited_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('country')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        if ($countries->isEmpty()) {
            return [
                'datasets' => [
                    [
                        'data' => [1],
                        'backgroundColor' => ['#e2e8f0'],
                        'borderWidth' => 0,
                    ],
                ],
                'labels' => [__('No data')],
            ];
        }

        $colors = [
            '#6366f1', // indigo
            '#10b981', // emerald
            '#f59e0b', // amber
            '#ec4899', // pink
            '#3b82f6', // blue
            '#8b5cf6', // violet
            '#14b8a6', // teal
            '#f97316', // orange
        ];

        $labels = $countries->pluck('country')->map(fn($c) => $c ?: __('Unknown'))->toArray();
        $data = $countries->pluck('total')->toArray();

        return [
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => array_slice($colors, 0, count($data)),
                    'borderWidth' => 2,
                    'borderColor' => '#ffffff',
                    'hoverOffset' => 8,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                    'labels' => [
                        'padding' => 14,
                        'usePointStyle' => true,
                        'pointStyle' => 'circle',
                        'font' => ['size' => 11, 'weight' => 500],
                    ],
                ],
            ],
        ];
    }
}
