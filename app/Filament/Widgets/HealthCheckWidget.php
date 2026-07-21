<?php

namespace App\Filament\Widgets;

use App\Http\Controllers\HealthCheckController;
use Filament\Widgets\Widget;

class HealthCheckWidget extends Widget
{
    protected string $view = 'filament.widgets.health-check-widget';

    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return false; // Only shown on Analytics/Health pages, not dashboard
    }

    public function getHealthData(): array
    {
        $checks = HealthCheckController::runChecks();

        $failedCount = collect($checks)->where('status', 'fail')->count();

        if ($failedCount === 0) {
            $status = 'healthy';
        } elseif ($failedCount <= 2) {
            $status = 'degraded';
        } else {
            $status = 'unhealthy';
        }

        return [
            'status' => $status,
            'checks' => $checks,
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
