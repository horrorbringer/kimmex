<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            {{ __('Recent Activity') }}
        </x-slot>

        @php $activities = $this->getActivities(); @endphp

        @if($activities->isEmpty())
            <p style="text-align: center; color: var(--gray-400); font-size: 0.875rem; padding: 24px 0;">
                {{ __('No recent activity yet.') }}
            </p>
        @else
            <div style="display: flex; flex-direction: column; gap: 2px;">
                @foreach($activities as $activity)
                    <a href="{{ $activity['url'] }}"
                       style="display: flex; align-items: center; gap: 12px; padding: 10px 12px; border-radius: 8px; text-decoration: none; transition: background 0.12s ease;"
                       onmouseover="this.style.background='rgba(99,102,241,0.04)'"
                       onmouseout="this.style.background='transparent'">

                        {{-- Icon --}}
                        <span style="font-size: 1.25rem; flex-shrink: 0; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 8px; background: {{ $activity['color'] }}10;">
                            {{ $activity['icon'] }}
                        </span>

                        {{-- Content --}}
                        <div style="flex: 1; min-width: 0;">
                            <div style="display: flex; align-items: center; gap: 6px;">
                                <span style="font-size: 0.8125rem; font-weight: 600; color: var(--gray-800); overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    {{ $activity['title'] }}
                                </span>
                                @if($activity['badge'])
                                    <span style="font-size: 0.625rem; font-weight: 700; color: white; background: {{ $activity['badge_color'] }}; padding: 1px 6px; border-radius: 999px; text-transform: uppercase; letter-spacing: 0.3px;">
                                        {{ $activity['badge'] }}
                                    </span>
                                @endif
                            </div>
                            <p style="font-size: 0.75rem; color: var(--gray-500); margin: 2px 0 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                {{ $activity['subtitle'] }}
                            </p>
                        </div>

                        {{-- Time --}}
                        <span style="font-size: 0.6875rem; color: var(--gray-400); white-space: nowrap; flex-shrink: 0;">
                            {{ $activity['time']->diffForHumans(short: true) }}
                        </span>
                    </a>
                @endforeach
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
