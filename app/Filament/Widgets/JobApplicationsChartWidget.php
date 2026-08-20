<?php

namespace App\Filament\Widgets;

use App\Models\JobApplication;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class JobApplicationsChartWidget extends ChartWidget
{
    protected static ?int $sort = 5;

    protected ?string $heading = 'Applications';

    protected int|string|array $columnSpan = 'half';

    protected ?string $maxHeight = '200px';

    public static function canView(): bool
    {
        return false; // Replaced by CombinedTrendChartWidget
    }

    protected function getData(): array
    {
        $startOfMonth = Carbon::now()->subMonths(5)->startOfMonth();

        $appCounts = JobApplication::query()
            ->where('created_at', '>=', $startOfMonth)
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as ym, COUNT(*) as aggregate")
            ->groupBy('ym')
            ->pluck('aggregate', 'ym');

        $labels = [];
        $data = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $labels[] = $month->format('M');
            $data[] = (int) ($appCounts[$month->format('Y-m')] ?? 0);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Applications',
                    'data' => $data,
                    'backgroundColor' => 'rgba(16, 185, 129, 0.8)',
                    'borderColor' => 'rgba(16, 185, 129, 1)',
                    'borderWidth' => 0,
                    'borderRadius' => 6,
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
                    'border' => ['display' => false],
                ],
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => ['stepSize' => 1],
                    'grid' => ['color' => 'rgba(148, 163, 184, 0.1)'],
                    'border' => ['display' => false],
                ],
            ],
        ];
    }
}
