<?php

namespace App\Filament\Widgets;

use App\Models\Inquiry;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class InquiryResponseTimeWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    public static function canView(): bool
    {
        return false; // Only shown on dedicated pages
    }

    protected function getStats(): array
    {
        // Average response time for responded inquiries
        $avgResponseTime = Inquiry::whereNotNull('responded_at')
            ->select(DB::raw('AVG(TIMESTAMPDIFF(HOUR, created_at, responded_at)) as avg_hours'))
            ->value('avg_hours');

        $avgFormatted = $avgResponseTime !== null
            ? $this->formatHours((float) $avgResponseTime)
            : __('N/A');

        // Total inquiries this month
        $totalThisMonth = Inquiry::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // Pending (unresponded) inquiries
        $pendingCount = Inquiry::whereNull('responded_at')->count();

        return [
            Stat::make(__('Avg Response Time'), $avgFormatted)
                ->description(__('Based on responded inquiries'))
                ->descriptionIcon('heroicon-m-clock')
                ->color('info'),

            Stat::make(__('Inquiries This Month'), $totalThisMonth)
                ->description(__('Total received'))
                ->descriptionIcon('heroicon-m-envelope')
                ->color('primary'),

            Stat::make(__('Pending Inquiries'), $pendingCount)
                ->description($pendingCount > 0 ? __('Awaiting response') : __('All responded'))
                ->descriptionIcon($pendingCount > 0 ? 'heroicon-m-exclamation-circle' : 'heroicon-m-check-circle')
                ->color($pendingCount > 0 ? 'warning' : 'success'),
        ];
    }

    /**
     * Format hours into a human-readable string.
     */
    private function formatHours(float $hours): string
    {
        if ($hours < 1) {
            $minutes = (int) round($hours * 60);
            return "{$minutes}m";
        }

        if ($hours < 24) {
            return round($hours, 1) . 'h';
        }

        $days = round($hours / 24, 1);
        return "{$days}d";
    }
}
