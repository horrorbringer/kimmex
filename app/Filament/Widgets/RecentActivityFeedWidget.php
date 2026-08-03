<?php

namespace App\Filament\Widgets;

use App\Models\Inquiry;
use App\Models\JobApplication;
use App\Models\Subscriber;
use Filament\Widgets\Widget;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class RecentActivityFeedWidget extends Widget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    protected string $view = 'filament.widgets.recent-activity-feed-widget';

    public function getActivities(): Collection
    {
        return collect(Cache::remember('admin_dashboard_recent_activity_'.app()->getLocale(), now()->addMinute(), function (): array {
            $items = collect();

            Inquiry::query()
                ->select(['id', 'name', 'subject', 'email', 'is_read', 'created_at'])
                ->latest()
                ->limit(5)
                ->get()
                ->each(function (Inquiry $inquiry) use ($items): void {
                    $items->push([
                        'type' => 'inquiry',
                        'icon' => '📬',
                        'color' => '#6366f1',
                        'title' => $inquiry->name ?? __('Anonymous'),
                        'subtitle' => $inquiry->subject ?? $inquiry->email,
                        'time' => $inquiry->created_at,
                        'url' => '/admin/inquiries/'.$inquiry->id.'/edit',
                        'badge' => $inquiry->is_read ? null : __('New'),
                        'badge_color' => '#ef4444',
                    ]);
                });

            JobApplication::query()
                ->select(['id', 'jobId', 'applicantName', 'status', 'created_at'])
                ->with('job:id,title')
                ->latest()
                ->limit(5)
                ->get()
                ->each(function (JobApplication $application) use ($items): void {
                    $items->push([
                        'type' => 'application',
                        'icon' => '👤',
                        'color' => '#10b981',
                        'title' => $application->applicantName ?? __('Applicant'),
                        'subtitle' => $application->job?->getTranslation('title', 'en') ?? __('General Application'),
                        'time' => $application->created_at,
                        'url' => '/admin/job-applications/'.$application->id.'/edit',
                        'badge' => $application->status?->value === 'PENDING' ? __('Pending') : null,
                        'badge_color' => '#f59e0b',
                    ]);
                });

            Subscriber::query()
                ->select(['id', 'name', 'email', 'created_at'])
                ->latest()
                ->limit(3)
                ->get()
                ->each(function (Subscriber $subscriber) use ($items): void {
                    $items->push([
                        'type' => 'subscriber',
                        'icon' => '🔔',
                        'color' => '#8b5cf6',
                        'title' => $subscriber->name ?? $subscriber->email,
                        'subtitle' => __('Subscribed to newsletter'),
                        'time' => $subscriber->created_at,
                        'url' => '/admin/subscribers',
                        'badge' => null,
                        'badge_color' => null,
                    ]);
                });

            return $items->sortByDesc('time')->take(10)->values()->all();
        }));
    }
}
