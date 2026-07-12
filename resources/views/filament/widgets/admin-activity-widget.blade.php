<x-filament-widgets::widget>
    @php
        $activities = $this->getActivities();
        $quickStats = $this->getQuickStats();
        $summaryStats = $this->getSummaryStats();
    @endphp

    <x-filament::section>
        <x-slot name="heading">
            {{ __('Admin Activity Dashboard') }}
        </x-slot>
        <x-slot name="description">
            {{ __('Recent activity and quick stats overview') }}
        </x-slot>

        {{-- Quick Stats Section --}}
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px;">
            {{-- Content Changes Today --}}
            <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px; padding: 20px; color: #ffffff;">
                <div style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.9; margin-bottom: 4px;">
                    {{ __('Content Changes Today') }}
                </div>
                <div style="font-size: 28px; font-weight: 700;">
                    {{ $quickStats['changes_today'] }}
                </div>
            </div>

            {{-- Newsletter Sends This Week --}}
            <div style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border-radius: 12px; padding: 20px; color: #ffffff;">
                <div style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.9; margin-bottom: 4px;">
                    {{ __('Newsletter Sends This Week') }}
                </div>
                <div style="font-size: 28px; font-weight: 700;">
                    {{ $quickStats['newsletter_sends_week'] }}
                </div>
            </div>

            {{-- New Subscribers This Week --}}
            <div style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); border-radius: 12px; padding: 20px; color: #ffffff;">
                <div style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.9; margin-bottom: 4px;">
                    {{ __('New Subscribers This Week') }}
                </div>
                <div style="font-size: 28px; font-weight: 700;">
                    {{ $quickStats['new_subscribers_week'] }}
                </div>
            </div>
        </div>

        {{-- Summary Stats --}}
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px; margin-bottom: 24px; padding: 16px; background-color: rgba(99, 102, 241, 0.05); border-radius: 10px; border: 1px solid rgba(99, 102, 241, 0.1);">
            <div style="text-align: center;">
                <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #6b7280; margin-bottom: 4px;">
                    {{ __('Changes Today') }}
                </div>
                <div style="font-size: 22px; font-weight: 700; color: #4f46e5;">
                    {{ $summaryStats['total_today'] }}
                </div>
            </div>
            <div style="text-align: center;">
                <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #6b7280; margin-bottom: 4px;">
                    {{ __('Changes This Week') }}
                </div>
                <div style="font-size: 22px; font-weight: 700; color: #4f46e5;">
                    {{ $summaryStats['total_week'] }}
                </div>
            </div>
            <div style="text-align: center;">
                <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #6b7280; margin-bottom: 4px;">
                    {{ __('Most Active Admin (Week)') }}
                </div>
                <div style="font-size: 14px; font-weight: 600; color: #4f46e5;">
                    {{ $summaryStats['most_active_admin'] }}
                    @if($summaryStats['most_active_admin_count'] > 0)
                        <span style="font-size: 11px; color: #6b7280; font-weight: 400;">
                            ({{ $summaryStats['most_active_admin_count'] }} {{ __('actions') }})
                        </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Activity Timeline --}}
        <div style="margin-top: 8px;">
            <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 16px; color: #1f2937;">
                {{ __('Recent Activity Timeline') }}
            </h3>

            @forelse($activities as $activity)
                <div style="display: flex; gap: 12px; margin-bottom: 16px; padding-bottom: 16px; border-bottom: 1px solid #f3f4f6;">
                    {{-- Timeline dot --}}
                    <div style="flex-shrink: 0; margin-top: 4px;">
                        <div style="width: 10px; height: 10px; border-radius: 50%; background-color: {{ $activity['event'] === 'created' ? '#10b981' : ($activity['event'] === 'deleted' ? '#ef4444' : '#6366f1') }};"></div>
                    </div>

                    {{-- Content --}}
                    <div style="flex: 1; min-width: 0;">
                        <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 6px; margin-bottom: 4px;">
                            {{-- Causer --}}
                            <span style="font-weight: 600; font-size: 13px; color: #1f2937;">
                                {{ $activity['causer_name'] }}
                            </span>

                            {{-- Event badge --}}
                            <span style="display: inline-block; padding: 2px 8px; border-radius: 9999px; font-size: 11px; font-weight: 500;
                                background-color: {{ $activity['event'] === 'created' ? '#d1fae5' : ($activity['event'] === 'deleted' ? '#fee2e2' : '#e0e7ff') }};
                                color: {{ $activity['event'] === 'created' ? '#065f46' : ($activity['event'] === 'deleted' ? '#991b1b' : '#3730a3') }};">
                                {{ ucfirst($activity['event']) }}
                            </span>

                            {{-- Subject type --}}
                            <span style="font-size: 12px; color: #6b7280;">
                                {{ $activity['subject_type'] }}
                            </span>
                        </div>

                        {{-- Timestamp --}}
                        <div style="font-size: 11px; color: #9ca3af; margin-bottom: 6px;">
                            {{ $activity['timestamp']->format('M d, Y H:i') }}
                            &middot;
                            {{ $activity['timestamp']->diffForHumans() }}
                        </div>

                        {{-- Changes --}}
                        @if(count($activity['changes']) > 0)
                            <div style="margin-top: 6px; padding: 8px 12px; background-color: #f9fafb; border-radius: 8px; border: 1px solid #e5e7eb;">
                                @foreach($activity['changes'] as $change)
                                    <div style="font-size: 12px; color: #4b5563; margin-bottom: 2px;">
                                        <span style="font-weight: 500; color: #374151;">{{ $change['field'] }}:</span>
                                        @if($change['old'] !== null)
                                            <span style="text-decoration: line-through; color: #ef4444;">{{ $change['old'] }}</span>
                                            &rarr;
                                        @endif
                                        <span style="color: #059669;">{{ $change['new'] }}</span>
                                    </div>
                                @endforeach
                                @if($activity['total_changes'] > 5)
                                    <div style="font-size: 11px; color: #9ca3af; margin-top: 4px;">
                                        {{ __('...and :count more changes', ['count' => $activity['total_changes'] - 5]) }}
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div style="text-align: center; padding: 32px 16px; color: #9ca3af;">
                    <div style="font-size: 14px;">{{ __('No activity recorded yet.') }}</div>
                </div>
            @endforelse
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
