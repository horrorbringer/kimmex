<?php

namespace App\Filament\Widgets;

use App\Models\Inquiry;
use App\Models\JobApplication;
use App\Models\Subscriber;
use Filament\Widgets\Widget;
use Illuminate\Support\Collection;

class RecentActivityFeedWidget extends Widget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    protected string $view = 'filament.widgets.recent-activity-feed-widget';

    public function getActivities(): Collection
    {
        $items = collect();

        // Recent inquiries
        Inquiry::latest()->limit(5)->get()->each(function ($inquiry) use ($items) {
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

        // Recent job applications
        JobApplication::with('job')->latest()->limit(5)->get()->each(function ($app) use ($items) {
            $items->push([
                'type' => 'application',
                'icon' => '👤',
                'color' => '#10b981',
                'title' => $app->name ?? __('Applicant'),
                'subtitle' => $app->job?->getTranslation('title', 'en') ?? __('General Application'),
                'time' => $app->created_at,
                'url' => '/admin/job-applications/'.$app->id.'/edit',
                'badge' => $app->status?->value === 'PENDING' ? __('Pending') : null,
                'badge_color' => '#f59e0b',
            ]);
        });

        // Recent subscribers
        Subscriber::latest()->limit(3)->get()->each(function ($sub) use ($items) {
            $items->push([
                'type' => 'subscriber',
                'icon' => '🔔',
                'color' => '#8b5cf6',
                'title' => $sub->name ?? $sub->email,
                'subtitle' => __('Subscribed to newsletter'),
                'time' => $sub->created_at,
                'url' => '/admin/subscribers',
                'badge' => null,
                'badge_color' => null,
            ]);
        });

        return $items->sortByDesc('time')->take(10)->values();
    }
}
