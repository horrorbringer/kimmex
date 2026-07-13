<?php

namespace App\Filament\Widgets;

use App\Models\PageView;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class ContentSectionsChartWidget extends ChartWidget
{
    protected static ?int $sort = 12;
    protected int | string | array $columnSpan = 1;
    protected ?string $maxHeight = '320px';

    public function getHeading(): ?string
    {
        return __('Content Performance');
    }

    public function getDescription(): ?string
    {
        return __('Views by section (30 days)');
    }

    public static function canView(): bool
    {
        return true;
    }

    protected function getData(): array
    {
        $sections = [
            'Projects' => '/projects',
            'Services' => '/services',
            'News' => '/news',
            'About' => '/about',
            'Careers' => '/careers',
            'Contact' => '/contact',
            'Home' => '/',
        ];

        $since = Carbon::now()->subDays(30);
        $data = [];

        foreach ($sections as $label => $prefix) {
            if ($prefix === '/') {
                $data[] = PageView::where('path', '/')
                    ->where('visited_at', '>=', $since)
                    ->count();
            } else {
                $data[] = PageView::where('path', 'like', $prefix . '%')
                    ->where('visited_at', '>=', $since)
                    ->count();
            }
        }

        return [
            'datasets' => [
                [
                    'label' => 'Views',
                    'data' => $data,
                    'borderColor' => '#6366f1',
                    'backgroundColor' => 'rgba(99,102,241,0.2)',
                    'pointBackgroundColor' => '#6366f1',
                    'pointBorderColor' => '#fff',
                    'pointBorderWidth' => 2,
                    'pointRadius' => 5,
                ],
            ],
            'labels' => array_keys($sections),
        ];
    }

    protected function getType(): string
    {
        return 'radar';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => ['display' => false],
            ],
            'scales' => [
                'r' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 'auto',
                    ],
                    'grid' => [
                        'color' => 'rgba(148, 163, 184, 0.1)',
                    ],
                    'pointLabels' => [
                        'font' => [
                            'size' => 11,
                            'weight' => '600',
                        ],
                    ],
                ],
            ],
        ];
    }
}
