<?php

namespace App\Filament\Widgets;

use App\Models\Inquiry;
use App\Models\JobApplication;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class CombinedTrendChartWidget extends ChartWidget
{
    protected static ?int $sort = 3;

    protected ?string $heading = 'Inquiries & Applications Trend';

    protected int|string|array $columnSpan = 1;

    protected ?string $maxHeight = '220px';

    public static function canView(): bool
    {
        return false; // Available on Analytics page only
    }

    protected function getData(): array
    {
        $labels = [];
        $inquiryData = [];
        $appData = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $labels[] = $month->format('M');
            $inquiryData[] = Inquiry::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
            $appData[] = JobApplication::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Inquiries',
                    'data' => $inquiryData,
                    'borderColor' => '#6366f1',
                    'backgroundColor' => 'rgba(99, 102, 241, 0.06)',
                    'fill' => true,
                    'tension' => 0.4,
                    'borderWidth' => 2.5,
                    'pointRadius' => 4,
                    'pointBackgroundColor' => '#6366f1',
                    'pointBorderColor' => '#ffffff',
                    'pointBorderWidth' => 2,
                ],
                [
                    'label' => 'Applications',
                    'data' => $appData,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.06)',
                    'fill' => true,
                    'tension' => 0.4,
                    'borderWidth' => 2.5,
                    'pointRadius' => 4,
                    'pointBackgroundColor' => '#10b981',
                    'pointBorderColor' => '#ffffff',
                    'pointBorderWidth' => 2,
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
                'legend' => [
                    'position' => 'top',
                    'align' => 'end',
                    'labels' => [
                        'usePointStyle' => true,
                        'pointStyle' => 'circle',
                        'padding' => 16,
                        'font' => ['size' => 11],
                    ],
                ],
            ],
            'scales' => [
                'x' => [
                    'grid' => ['display' => false],
                    'border' => ['display' => false],
                    'ticks' => ['color' => '#94a3b8', 'font' => ['size' => 11]],
                ],
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => ['stepSize' => 1, 'color' => '#94a3b8', 'font' => ['size' => 11]],
                    'grid' => ['color' => 'rgba(148, 163, 184, 0.08)'],
                    'border' => ['display' => false],
                ],
            ],
        ];
    }
}
