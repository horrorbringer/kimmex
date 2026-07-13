<?php

namespace App\Filament\Widgets;

use App\Models\PageView;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class BrowserDistributionChartWidget extends ChartWidget
{
    protected static ?int $sort = 10;
    protected int | string | array $columnSpan = 1;
    protected ?string $maxHeight = '320px';

    public function getHeading(): ?string
    {
        return __('Browser Distribution');
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
        $browsers = [
            'Chrome' => 0,
            'Safari' => 0,
            'Firefox' => 0,
            'Edge' => 0,
            'Brave' => 0,
            'Opera' => 0,
            'Samsung' => 0,
            'Vivaldi' => 0,
            'Arc' => 0,
            'Other' => 0,
        ];

        $userAgents = PageView::where('visited_at', '>=', Carbon::now()->subDays(30))
            ->whereNotNull('user_agent')
            ->pluck('user_agent');

        foreach ($userAgents as $ua) {
            $uaLower = strtolower($ua);

            if (str_contains($uaLower, 'edg')) {
                $browsers['Edge']++;
            } elseif (str_contains($uaLower, 'brave')) {
                $browsers['Brave']++;
            } elseif (str_contains($uaLower, 'vivaldi')) {
                $browsers['Vivaldi']++;
            } elseif (str_contains($uaLower, 'arc/')) {
                $browsers['Arc']++;
            } elseif (str_contains($uaLower, 'samsungbrowser')) {
                $browsers['Samsung']++;
            } elseif (str_contains($uaLower, 'firefox')) {
                $browsers['Firefox']++;
            } elseif (str_contains($uaLower, 'opr') || str_contains($uaLower, 'opera')) {
                $browsers['Opera']++;
            } elseif (str_contains($uaLower, 'chrome') || str_contains($uaLower, 'chromium')) {
                $browsers['Chrome']++;
            } elseif (str_contains($uaLower, 'safari')) {
                $browsers['Safari']++;
            } else {
                $browsers['Other']++;
            }
        }

        // Remove browsers with zero visits
        $browsers = array_filter($browsers, fn($v) => $v > 0);

        $colors = [
            'Chrome' => '#4285f4',
            'Safari' => '#006cff',
            'Firefox' => '#ff7139',
            'Edge' => '#0078d7',
            'Brave' => '#fb542b',
            'Opera' => '#ff1b2d',
            'Samsung' => '#1428a0',
            'Vivaldi' => '#ef3939',
            'Arc' => '#8b5cf6',
            'Other' => '#94a3b8',
        ];

        return [
            'datasets' => [
                [
                    'data' => array_values($browsers),
                    'backgroundColor' => array_map(fn($k) => $colors[$k] ?? '#94a3b8', array_keys($browsers)),
                    'borderWidth' => 2,
                    'borderColor' => '#ffffff',
                ],
            ],
            'labels' => array_keys($browsers),
        ];
    }

    protected function getType(): string
    {
        return 'polarArea';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                    'labels' => [
                        'pointStyle' => 'circle',
                        'usePointStyle' => true,
                        'font' => ['size' => 11],
                    ],
                ],
            ],
        ];
    }
}
