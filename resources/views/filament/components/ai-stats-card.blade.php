@php
    $stats = \App\Models\SystemSetting::get('ai_stats', []);
    $status = $stats['last_status'] ?? 'unknown';
    $total = $stats['total_count'] ?? 0;
@endphp

<div class="rounded-lg border border-gray-200 bg-white p-4">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div @class([
                'flex h-10 w-10 items-center justify-center rounded-lg',
                'bg-success-100 text-success-600' => $status === 'healthy',
                'bg-danger-100 text-danger-600' => $status === 'error',
                'bg-gray-100 text-gray-600' => $status !== 'healthy' && $status !== 'error',
            ])>
                @if($status === 'healthy')
                    <x-filament::icon
                        icon="heroicon-m-check-circle"
                        class="h-5 w-5"
                    />
                @elseif($status === 'error')
                    <x-filament::icon
                        icon="heroicon-m-exclamation-circle"
                        class="h-5 w-5"
                    />
                @else
                    <x-filament::icon
                        icon="heroicon-m-question-mark-circle"
                        class="h-5 w-5"
                    />
                @endif
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">{{ __('AI Status') }}</p>
                <p class="text-lg font-semibold text-gray-900">
                    {{ $status === 'healthy' ? __('Connected') : ($status === 'error' ? __('Error') : __('Offline')) }}
                </p>
            </div>
        </div>
        <div class="text-right">
            <p class="text-sm font-medium text-gray-500">{{ __('Total Usage') }}</p>
            <p class="text-lg font-semibold text-gray-900">{{ number_format($total) }}</p>
        </div>
    </div>
</div>
