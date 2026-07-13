<?php

namespace App\Filament\Widgets;

use App\Models\PageView;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class DeviceDistributionChartWidget extends ChartWidget
{
    protected static ?int $sort = 8;
    protected int | string | array $columnSpan = 1;
    protected ?string $maxHeight = '280px';

    public function getHeading(): ?string
    {
        return __('Device Distribution');
    }

    public function getDescription(): ?string
    {
        return __('Last 30 days');
    }

    public static function canView(): bool
    {
        return false; // Only shown on Analytics page
    }

    protected function getData(): array
    {
        $views = PageView::where('visited_at', '>=', Carbon::now()->subDays(30))
            ->whereNotNull('user_agent')
            ->pluck('user_agent');

        $devices = ['Desktop' => 0, 'Mobile' => 0, 'Tablet' => 0, 'Bot' => 0];

        foreach ($views as $ua) {
            $ua = strtolower($ua);
            if (Str::contains($ua, ['bot', 'crawler', 'spider', 'slurp', 'googlebot'])) {
                $devices['Bot']++;
            } elseif (Str::contains($ua, ['ipad', 'tablet', 'kindle'])) {
                $devices['Tablet']++;
            } elseif (Str::contains($ua, ['mobile', 'iphone', 'android', 'phone'])) {
                $devices['Mobile']++;
            } else {
                $devices['Desktop']++;
            }
        }

        // Remove zero entries
        $devices = array_filter($devices, fn($v) => $v > 0);

        $colorMap = [
            'Desktop' => '#6366f1',
            'Mobile' => '#10b981',
            'Tablet' => '#f59e0b',
            'Bot' => '#94a3b8',
        ];

        return [
            'datasets' => [
                [
                    'data' => array_values($devices),
                    'backgroundColor' => array_map(fn($k) => $colorMap[$k] ?? '#6366f1', array_keys($devices)),
                    'borderWidth' => 2,
                    'borderColor' => '#ffffff',
                    'hoverOffset' => 8,
                ],
            ],
            'labels' => array_map(fn($k) => __($k), array_keys($devices)),
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
