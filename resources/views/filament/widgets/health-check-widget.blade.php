<x-filament-widgets::widget>
    @php
        $healthData = $this->getHealthData();
        $status = $healthData['status'];
        $checks = $healthData['checks'];

        $statusColors = [
            'healthy' => ['bg' => '#059669', 'text' => '#ffffff', 'badge' => '#d1fae5'],
            'degraded' => ['bg' => '#d97706', 'text' => '#ffffff', 'badge' => '#fef3c7'],
            'unhealthy' => ['bg' => '#dc2626', 'text' => '#ffffff', 'badge' => '#fee2e2'],
        ];

        $currentColor = $statusColors[$status] ?? $statusColors['unhealthy'];
    @endphp

    <x-filament::section>
        <x-slot name="heading">
            <span style="display: inline-flex; align-items: center; gap: 8px;">
                System Health
                <span style="display: inline-block; padding: 2px 10px; border-radius: 9999px; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; background-color: {{ $currentColor['bg'] }}; color: {{ $currentColor['text'] }};">
                    {{ $status }}
                </span>
            </span>
        </x-slot>
        <x-slot name="description">
            Last checked: {{ \Carbon\Carbon::parse($healthData['timestamp'])->format('M d, Y H:i:s') }}
        </x-slot>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 12px;">
            @foreach ($checks as $check)
                @php
                    $isPass = $check['status'] === 'pass';
                    $dotColor = $isPass ? '#059669' : '#dc2626';
                    $borderColor = $isPass ? '#d1fae5' : '#fee2e2';
                    $bgColor = $isPass ? '#f0fdf4' : '#fef2f2';
                @endphp
                <div style="padding: 12px 14px; border-radius: 8px; border: 1px solid {{ $borderColor }}; background-color: {{ $bgColor }};">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                        <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background-color: {{ $dotColor }};"></span>
                        <span style="font-weight: 600; font-size: 13px; color: #1f2937;">{{ $check['name'] }}</span>
                    </div>
                    <div style="font-size: 11px; color: #6b7280; padding-left: 16px;">
                        {{ $check['message'] }}
                    </div>
                </div>
            @endforeach
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
