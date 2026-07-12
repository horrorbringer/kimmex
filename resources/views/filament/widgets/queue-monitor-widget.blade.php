<x-filament-widgets::widget>
    @php $stats = $this->getQueueStats(); @endphp

    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <span style="font-size: 1.1rem;">⚙️</span>
                {{ __('Queue Monitor') }}
                @if($stats['pending'] === 0 && $stats['failed'] === 0)
                    <x-filament::badge color="success" size="sm">{{ __('Idle') }}</x-filament::badge>
                @elseif($stats['warning'])
                    <x-filament::badge color="warning" size="sm">{{ __('Attention') }}</x-filament::badge>
                @else
                    <x-filament::badge color="primary" size="sm">{{ __('Processing') }}</x-filament::badge>
                @endif
            </div>
        </x-slot>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 12px;">
            {{-- Pending --}}
            <div style="text-align: center; padding: 12px; border-radius: 8px; background: {{ $stats['pending'] > 0 ? 'rgba(99,102,241,0.06)' : 'var(--gray-50, #f9fafb)' }}; border: 1px solid {{ $stats['pending'] > 0 ? 'rgba(99,102,241,0.15)' : 'var(--gray-200)' }};">
                <span style="display: block; font-size: 1.5rem; font-weight: 800; color: {{ $stats['pending'] > 0 ? '#6366f1' : 'var(--gray-400)' }};">
                    {{ $stats['pending'] }}
                </span>
                <span style="font-size: 0.6875rem; font-weight: 600; text-transform: uppercase; color: var(--gray-500);">
                    {{ __('Pending') }}
                </span>
            </div>

            {{-- Failed --}}
            <div style="text-align: center; padding: 12px; border-radius: 8px; background: {{ $stats['failed'] > 0 ? 'rgba(239,68,68,0.06)' : 'var(--gray-50, #f9fafb)' }}; border: 1px solid {{ $stats['failed'] > 0 ? 'rgba(239,68,68,0.15)' : 'var(--gray-200)' }};">
                <span style="display: block; font-size: 1.5rem; font-weight: 800; color: {{ $stats['failed'] > 0 ? '#ef4444' : 'var(--gray-400)' }};">
                    {{ $stats['failed'] }}
                </span>
                <span style="font-size: 0.6875rem; font-weight: 600; text-transform: uppercase; color: var(--gray-500);">
                    {{ __('Failed') }}
                </span>
            </div>

            {{-- Oldest --}}
            <div style="text-align: center; padding: 12px; border-radius: 8px; background: var(--gray-50, #f9fafb); border: 1px solid var(--gray-200);">
                <span style="display: block; font-size: 1rem; font-weight: 700; color: var(--gray-700);">
                    {{ $stats['oldestAge'] ?? '—' }}
                </span>
                <span style="font-size: 0.6875rem; font-weight: 600; text-transform: uppercase; color: var(--gray-500);">
                    {{ __('Oldest Job') }}
                </span>
            </div>

            {{-- Queues --}}
            @foreach($stats['byQueue'] as $queue => $count)
                <div style="text-align: center; padding: 12px; border-radius: 8px; background: rgba(16,185,129,0.06); border: 1px solid rgba(16,185,129,0.15);">
                    <span style="display: block; font-size: 1.5rem; font-weight: 800; color: #10b981;">
                        {{ $count }}
                    </span>
                    <span style="font-size: 0.6875rem; font-weight: 600; text-transform: uppercase; color: var(--gray-500);">
                        {{ $queue }}
                    </span>
                </div>
            @endforeach
        </div>

        {{-- Failed Jobs --}}
        @if(!empty($stats['recentFailed']))
            <div style="margin-top: 16px; border-top: 1px solid var(--gray-200); padding-top: 12px;">
                <p style="font-size: 0.75rem; font-weight: 700; color: #ef4444; text-transform: uppercase; margin-bottom: 8px;">
                    {{ __('Recent Failures') }}
                </p>
                @foreach($stats['recentFailed'] as $job)
                    <div style="padding: 8px 10px; margin-bottom: 4px; border-radius: 6px; background: rgba(239,68,68,0.04); border: 1px solid rgba(239,68,68,0.1);">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-size: 0.8125rem; font-weight: 600; color: var(--gray-800);">{{ $job['name'] }}</span>
                            <span style="font-size: 0.6875rem; color: var(--gray-400);">{{ $job['failed_at'] }}</span>
                        </div>
                        <p style="font-size: 0.7rem; color: #ef4444; margin: 2px 0 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                            {{ $job['error'] }}
                        </p>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Quick Actions --}}
        <div style="margin-top: 12px; display: flex; gap: 8px; flex-wrap: wrap;">
            <x-filament::button
                size="sm"
                color="gray"
                icon="heroicon-o-play"
                wire:click="$dispatch('processQueue')"
                tag="a"
                href="/admin/artisan-console"
            >
                {{ __('Process Queue') }}
            </x-filament::button>
            @if($stats['failed'] > 0)
                <x-filament::button
                    size="sm"
                    color="warning"
                    icon="heroicon-o-arrow-path"
                    tag="a"
                    href="/admin/artisan-console"
                >
                    {{ __('Retry Failed') }}
                </x-filament::button>
            @endif
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
