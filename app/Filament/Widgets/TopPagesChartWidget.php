<?php

namespace App\Filament\Widgets;

use App\Models\PageView;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class TopPagesChartWidget extends ChartWidget
{
    protected static ?int $sort = 6;

    protected int|string|array $columnSpan = 1;

    protected ?string $maxHeight = '320px';

    public function getHeading(): ?string
    {
        return __('Top Pages');
    }

    public function getDescription(): ?string
    {
        return __('Distribution (30 days)');
    }

    public static function canView(): bool
    {
        return true;
    }

    protected function getData(): array
    {
        $pages = PageView::select('path')
            ->selectRaw('COUNT(*) as views')
            ->where('visited_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('path')
            ->orderByDesc('views')
            ->limit(6)
            ->get();

        $labels = $pages->map(function ($page) {
            $path = $page->path === '/' ? 'Homepage' : ltrim($page->path, '/');

            return Str::limit($path, 20);
        })->toArray();

        $data = $pages->pluck('views')->toArray();

        $colors = [
            '#6366f1', // indigo
            '#8b5cf6', // violet
            '#ec4899', // pink
            '#f59e0b', // amber
            '#10b981', // emerald
            '#06b6d4', // cyan
        ];

        return [
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => array_slice($colors, 0, count($data)),
                    'borderWidth' => 0,
                    'hoverOffset' => 8,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'cutout' => '65%',
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                    'labels' => [
                        'padding' => 16,
                        'usePointStyle' => true,
                        'pointStyle' => 'circle',
                        'font' => ['size' => 11],
                    ],
                ],
            ],
        ];
    }
}
