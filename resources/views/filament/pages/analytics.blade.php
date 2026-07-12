<x-filament-panels::page>
    @php
        $stats = $this->getStats();
        $topPages = $this->getTopPages();
        $topReferers = $this->getTopReferers();
        $dailyViews = $this->getDailyViews();
    @endphp

    {{-- Stats Overview --}}
    <x-filament::section>
        <x-slot name="heading">
            {{ __('Traffic Overview') }}
        </x-slot>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px;">
            <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px; padding: 20px; color: #ffffff;">
                <div style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.9; margin-bottom: 4px;">
                    {{ __('Today') }}
                </div>
                <div style="font-size: 32px; font-weight: 700;">
                    {{ number_format($stats['today']) }}
                </div>
            </div>

            <div style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border-radius: 12px; padding: 20px; color: #ffffff;">
                <div style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.9; margin-bottom: 4px;">
                    {{ __('This Week') }}
                </div>
                <div style="font-size: 32px; font-weight: 700;">
                    {{ number_format($stats['this_week']) }}
                </div>
            </div>

            <div style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); border-radius: 12px; padding: 20px; color: #ffffff;">
                <div style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.9; margin-bottom: 4px;">
                    {{ __('This Month') }}
                </div>
                <div style="font-size: 32px; font-weight: 700;">
                    {{ number_format($stats['this_month']) }}
                </div>
            </div>

            <div style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); border-radius: 12px; padding: 20px; color: #ffffff;">
                <div style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.9; margin-bottom: 4px;">
                    {{ __('All Time') }}
                </div>
                <div style="font-size: 32px; font-weight: 700;">
                    {{ number_format($stats['total']) }}
                </div>
            </div>
        </div>
    </x-filament::section>

    {{-- Daily Views Chart --}}
    <x-filament::section>
        <x-slot name="heading">
            {{ __('Views Per Day (Last 30 Days)') }}
        </x-slot>

        @if($dailyViews->isNotEmpty())
            @php
                $maxViews = $dailyViews->max('views') ?: 1;
            @endphp
            <div style="display: flex; align-items: flex-end; gap: 4px; height: 200px; padding: 16px 0;">
                @foreach($dailyViews as $day)
                    @php
                        $heightPercent = ($day->views / $maxViews) * 100;
                        $date = \Carbon\Carbon::parse($day->date);
                    @endphp
                    <div style="flex: 1; display: flex; flex-direction: column; align-items: center; height: 100%;">
                        <div style="flex: 1; display: flex; align-items: flex-end; width: 100%;">
                            <div
                                style="width: 100%; height: {{ $heightPercent }}%; background: linear-gradient(180deg, #667eea 0%, #764ba2 100%); border-radius: 4px 4px 0 0; min-height: 2px; transition: height 0.3s;"
                                title="{{ $date->format('M d') }}: {{ $day->views }} views"
                            ></div>
                        </div>
                        @if($loop->index % 5 === 0 || $loop->last)
                            <div style="font-size: 10px; color: rgba(107, 114, 128, 0.7); margin-top: 6px; white-space: nowrap;">
                                {{ $date->format('M d') }}
                            </div>
                        @else
                            <div style="margin-top: 6px; height: 12px;"></div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div style="padding: 40px; text-align: center; color: rgba(107, 114, 128, 0.7);">
                {{ __('No data available yet.') }}
            </div>
        @endif
    </x-filament::section>

    {{-- Two Column Layout: Top Pages & Top Referers --}}
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
        {{-- Top Pages --}}
        <x-filament::section>
            <x-slot name="heading">
                {{ __('Top Pages') }}
            </x-slot>

            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                    <thead>
                        <tr style="border-bottom: 2px solid rgba(99, 102, 241, 0.2);">
                            <th style="text-align: left; padding: 8px 12px; font-weight: 600;">#</th>
                            <th style="text-align: left; padding: 8px 12px; font-weight: 600;">{{ __('Path') }}</th>
                            <th style="text-align: right; padding: 8px 12px; font-weight: 600;">{{ __('Views') }}</th>
                            <th style="text-align: right; padding: 8px 12px; font-weight: 600;">{{ __('Last Visit') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topPages as $index => $page)
                            <tr style="border-bottom: 1px solid rgba(99, 102, 241, 0.08);">
                                <td style="padding: 8px 12px; color: rgba(107, 114, 128, 0.8);">{{ $index + 1 }}</td>
                                <td style="padding: 8px 12px;">
                                    <div style="font-weight: 500;">{{ $page->path }}</div>
                                    @if($page->title)
                                        <div style="font-size: 11px; color: rgba(107, 114, 128, 0.7); margin-top: 2px;">{{ Str::limit($page->title, 40) }}</div>
                                    @endif
                                </td>
                                <td style="padding: 8px 12px; text-align: right;">
                                    <x-filament::badge color="primary">
                                        {{ number_format($page->views) }}
                                    </x-filament::badge>
                                </td>
                                <td style="padding: 8px 12px; text-align: right; font-size: 12px; color: rgba(107, 114, 128, 0.7);">
                                    {{ $page->last_visited ? \Carbon\Carbon::parse($page->last_visited)->diffForHumans() : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="padding: 20px; text-align: center; color: rgba(107, 114, 128, 0.7);">
                                    {{ __('No page views recorded yet.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-filament::section>

        {{-- Top Referers --}}
        <x-filament::section>
            <x-slot name="heading">
                {{ __('Top Referers') }}
            </x-slot>

            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                    <thead>
                        <tr style="border-bottom: 2px solid rgba(99, 102, 241, 0.2);">
                            <th style="text-align: left; padding: 8px 12px; font-weight: 600;">#</th>
                            <th style="text-align: left; padding: 8px 12px; font-weight: 600;">{{ __('Referer') }}</th>
                            <th style="text-align: right; padding: 8px 12px; font-weight: 600;">{{ __('Visits') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topReferers as $index => $referer)
                            <tr style="border-bottom: 1px solid rgba(99, 102, 241, 0.08);">
                                <td style="padding: 8px 12px; color: rgba(107, 114, 128, 0.8);">{{ $index + 1 }}</td>
                                <td style="padding: 8px 12px;">
                                    <div style="font-weight: 500; word-break: break-all;">{{ Str::limit($referer->referer, 60) }}</div>
                                </td>
                                <td style="padding: 8px 12px; text-align: right;">
                                    <x-filament::badge color="success">
                                        {{ number_format($referer->visits) }}
                                    </x-filament::badge>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="padding: 20px; text-align: center; color: rgba(107, 114, 128, 0.7);">
                                    {{ __('No referer data recorded yet.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
