<?php

namespace App\Filament\Widgets;

use App\Models\PageView;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class TrafficSourcesChartWidget extends ChartWidget
{
    protected static ?int $sort = 9;
    protected int | string | array $columnSpan = 1;
    protected ?string $maxHeight = '320px';

    public function getHeading(): ?string
    {
        return __('Traffic Sources');
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
        $views = PageView::where('visited_at', '>=', Carbon::now()->subDays(30))
            ->selectRaw('referer, COUNT(*) as total')
            ->groupBy('referer')
            ->get();

        $sources = [
            'Direct' => 0,
            'Google' => 0,
            'Facebook' => 0,
            'Other' => 0,
        ];

        foreach ($views as $view) {
            $referer = strtolower($view->referer ?? '');

            if (empty($referer) || $referer === '' || Str::contains($referer, [request()->getHost()])) {
                $sources['Direct'] += $view->total;
            } elseif (Str::contains($referer, ['google'])) {
                $sources['Google'] += $view->total;
            } elseif (Str::contains($referer, ['facebook', 'fb.com', 'fb.me'])) {
                $sources['Facebook'] += $view->total;
            } else {
                $sources['Other'] += $view->total;
            }
        }

        // Remove zero entries
        $sources = array_filter($sources, fn($v) => $v > 0);

        if (empty($sources)) {
            $sources = ['No data' => 1];
        }

        $colorMap = [
            'Direct' => '#6366f1',
            'Google' => '#10b981',
            'Facebook' => '#3b82f6',
            'Other' => '#f59e0b',
            'No data' => '#e2e8f0',
        ];

        return [
            'datasets' => [
                [
                    'data' => array_values($sources),
                    'backgroundColor' => array_map(fn($k) => $colorMap[$k] ?? '#94a3b8', array_keys($sources)),
                    'borderWidth' => 2,
                    'borderColor' => '#ffffff',
                    'hoverOffset' => 8,
                ],
            ],
            'labels' => array_map(fn($k) => __($k), array_keys($sources)),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'cutout' => '60%',
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                    'labels' => [
                        'padding' => 16,
                        'usePointStyle' => true,
                        'pointStyle' => 'circle',
                        'font' => ['size' => 11, 'weight' => 500],
                    ],
                ],
            ],
        ];
    }
}
