<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\JobApplication;
use App\Models\Inquiry;
use App\Models\Project;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Projects', Project::count())
                ->description('All active and past projects')
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('primary'),
            Stat::make('New Job Applications', JobApplication::where('created_at', '>=', now()->subDays(7))->count())
                ->description('Received in the last 7 days')
                ->descriptionIcon('heroicon-m-document-check')
                ->color('success'),
            Stat::make('Unread Inquiries', Inquiry::where('is_read', false)->count())
                ->description('Pending attention')
                ->descriptionIcon('heroicon-m-envelope')
                ->color('warning'),
        ];
    }
}
