<?php

namespace App\Filament\Widgets;

use App\Models\ActivityLog;
use App\Models\NewsletterSend;
use App\Models\Subscriber;
use App\Models\User;
use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class AdminActivityWidget extends Widget
{
    protected string $view = 'filament.widgets.admin-activity-widget';

    protected static ?int $sort = 10;

    protected int | string | array $columnSpan = 'full';

    public function getActivities(): Collection
    {
        return ActivityLog::with('causer')
            ->latest()
            ->limit(15)
            ->get()
            ->map(function (ActivityLog $activity) {
                $properties = $activity->properties ?? collect();
                $changes = [];

                if ($properties->has('attributes')) {
                    $attributes = $properties->get('attributes', []);
                    $old = $properties->get('old', []);

                    foreach ($attributes as $key => $value) {
                        if (isset($old[$key]) && $old[$key] !== $value) {
                            $changes[] = [
                                'field' => $key,
                                'old' => $this->formatValue($old[$key]),
                                'new' => $this->formatValue($value),
                            ];
                        } elseif (!isset($old[$key])) {
                            $changes[] = [
                                'field' => $key,
                                'old' => null,
                                'new' => $this->formatValue($value),
                            ];
                        }
                    }
                }

                return [
                    'id' => $activity->id,
                    'timestamp' => $activity->created_at,
                    'causer_name' => $activity->causer?->name ?? __('System'),
                    'description' => $activity->description,
                    'event' => $activity->description,
                    'subject_type' => $activity->subject_type
                        ? class_basename($activity->subject_type)
                        : __('Unknown'),
                    'changes' => array_slice($changes, 0, 5),
                    'total_changes' => count($changes),
                ];
            });
    }

    public function getQuickStats(): array
    {
        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek();

        // Content changes today
        $changesToday = ActivityLog::whereDate('created_at', $today)->count();

        // Newsletter sends this week
        $newsletterSendsThisWeek = NewsletterSend::where('created_at', '>=', $startOfWeek)->count();

        // New subscribers this week
        $newSubscribersThisWeek = Subscriber::where('created_at', '>=', $startOfWeek)->count();

        return [
            'changes_today' => $changesToday,
            'newsletter_sends_week' => $newsletterSendsThisWeek,
            'new_subscribers_week' => $newSubscribersThisWeek,
        ];
    }

    public function getSummaryStats(): array
    {
        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek();

        // Total changes today
        $totalToday = ActivityLog::whereDate('created_at', $today)->count();

        // Total changes this week
        $totalWeek = ActivityLog::where('created_at', '>=', $startOfWeek)->count();

        // Most active admin this week
        $mostActiveAdmin = ActivityLog::where('created_at', '>=', $startOfWeek)
            ->whereNotNull('causer_id')
            ->where('causer_type', (new User)->getMorphClass())
            ->select('causer_id')
            ->selectRaw('COUNT(*) as activity_count')
            ->groupBy('causer_id')
            ->orderByDesc('activity_count')
            ->first();

        $mostActiveAdminName = __('N/A');
        $mostActiveAdminCount = 0;

        if ($mostActiveAdmin) {
            $user = User::find($mostActiveAdmin->causer_id);
            $mostActiveAdminName = $user?->name ?? __('Unknown');
            $mostActiveAdminCount = $mostActiveAdmin->activity_count;
        }

        return [
            'total_today' => $totalToday,
            'total_week' => $totalWeek,
            'most_active_admin' => $mostActiveAdminName,
            'most_active_admin_count' => $mostActiveAdminCount,
        ];
    }

    private function formatValue(mixed $value): string
    {
        if (is_null($value)) {
            return __('null');
        }

        if (is_bool($value)) {
            return $value ? __('true') : __('false');
        }

        if (is_array($value)) {
            return json_encode($value);
        }

        $str = (string) $value;

        if (strlen($str) > 50) {
            return substr($str, 0, 50) . '…';
        }

        return $str;
    }
}
