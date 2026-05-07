@php
    $stats = \App\Models\SystemSetting::get('ai_stats', []);
    $status = $stats['last_status'] ?? 'unknown';
    $today = $stats['today_count'] ?? 0;
    $total = $stats['total_count'] ?? 0;
    
    // Determine provider for quota info
    $aiSettings = \App\Models\SystemSetting::get('ai_settings', []);
    $provider = $aiSettings['provider'] ?? 'gemini';
    $quota = $provider === 'gemini' ? '15 RPM / 1,500 RPD' : 'Unlimited (Local)';
@endphp

<div class="w-full pb-8">
    <!-- DEBUG INDICATOR -->
    <div class="mb-3 text-[9px] font-bold text-gray-400/50 uppercase tracking-widest flex items-center gap-2">
        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
        AI Health Monitor Engine v2.2
    </div>
    
    <div style="display: flex; gap: 16px; flex-wrap: wrap; align-items: stretch;">
        <!-- Connection Status -->
        <div style="flex: 1; min-width: 200px; background: rgba(255,255,255,0.8); backdrop-filter: blur(12px); border: 1px solid rgba(0,0,0,0.05); border-radius: 16px; padding: 16px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div @class([
                    'w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0',
                    'bg-emerald-100 text-emerald-600' => $status === 'healthy',
                    'bg-rose-100 text-rose-600' => $status === 'error',
                    'bg-slate-100 text-slate-600' => $status !== 'healthy' && $status !== 'error',
                ])>
                    @if($status === 'healthy')
                        <svg style="width: 24px; height: 24px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    @elseif($status === 'error')
                        <svg style="width: 24px; height: 24px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                        </svg>
                    @else
                        <svg style="width: 24px; height: 24px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" />
                        </svg>
                    @endif
                </div>
                <div style="min-width: 0; flex: 1;">
                    <p style="font-size: 10px; font-weight: 800; color: var(--color-kmd-navy-subtle); text-transform: uppercase; letter-spacing: 0.1em; margin: 0;">{{ __('Status') }}</p>
                    <p style="font-size: 14px; font-weight: 900; color: var(--color-kmd-navy); margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        {{ $status === 'healthy' ? __('HEALTHY') : ($status === 'error' ? __('ERROR') : __('OFFLINE')) }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Today's Usage -->
        <div style="flex: 1; min-width: 200px; background: rgba(255,255,255,0.8); backdrop-filter: blur(12px); border: 1px solid rgba(0,0,0,0.05); border-radius: 16px; padding: 16px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 48px; height: 48px; border-radius: 12px; background: color-mix(in srgb, var(--color-kmd-blue), transparent 90%); color: var(--color-kmd-blue); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg style="width: 24px; height: 24px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.306 4.307a11.25 11.25 0 0 0 12.912-2.106L21.75 6" />
                    </svg>
                </div>
                <div>
                    <p style="font-size: 10px; font-weight: 800; color: var(--color-kmd-navy-subtle); text-transform: uppercase; letter-spacing: 0.1em; margin: 0;">{{ __('Today') }}</p>
                    <p style="font-size: 14px; font-weight: 900; color: var(--color-kmd-navy); margin: 0;">
                        {{ number_format($today) }} <span style="font-size: 10px; font-weight: 500; color: var(--color-kmd-navy-subtle);">reqs</span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Lifetime Usage -->
        <div style="flex: 1; min-width: 200px; background: rgba(255,255,255,0.8); backdrop-filter: blur(12px); border: 1px solid rgba(0,0,0,0.05); border-radius: 16px; padding: 16px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 48px; height: 48px; border-radius: 12px; background: color-mix(in srgb, var(--color-kmd-gold), transparent 90%); color: var(--color-kmd-gold); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg style="width: 24px; height: 24px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m3.75 13.5 10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z" />
                    </svg>
                </div>
                <div>
                    <p style="font-size: 10px; font-weight: 800; color: var(--color-kmd-navy-subtle); text-transform: uppercase; letter-spacing: 0.1em; margin: 0;">{{ __('Lifetime') }}</p>
                    <p style="font-size: 14px; font-weight: 900; color: var(--color-kmd-navy); margin: 0;">{{ number_format($total) }}</p>
                </div>
            </div>
        </div>

        <!-- Quota & Limits -->
        <div style="flex: 1; min-width: 200px; background: rgba(255,255,255,0.8); backdrop-filter: blur(12px); border: 1px solid rgba(0,0,0,0.05); border-radius: 16px; padding: 16px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 48px; height: 48px; border-radius: 12px; background: color-mix(in srgb, var(--color-kmd-navy), transparent 90%); color: var(--color-kmd-navy); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg style="width: 24px; height: 24px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                    </svg>
                </div>
                <div style="min-width: 0; flex: 1;">
                    <p style="font-size: 10px; font-weight: 800; color: var(--color-kmd-navy-subtle); text-transform: uppercase; letter-spacing: 0.1em; margin: 0;">{{ __('Quota') }}</p>
                    <p style="font-size: 11px; font-weight: 900; color: var(--color-kmd-navy); margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $quota }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
