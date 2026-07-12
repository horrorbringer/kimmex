<x-filament-widgets::widget>
    @php
        $stats = $this->getStats();
        $topPages = $this->getTopPages();
    @endphp

    <x-filament::section>
        <x-slot name="heading">
            {{ __('Page Analytics') }}
        </x-slot>
        <x-slot name="description">
            {{ __('Visitor traffic overview') }}
        </x-slot>

        {{-- Stats Cards --}}
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; margin-bottom: 24px;">
            <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px; padding: 20px; color: #ffffff;">
                <div style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.9; margin-bottom: 4px;">
                    {{ __('Today') }}
                </div>
                <div style="font-size: 28px; font-weight: 700;">
                    {{ number_format($stats['today']) }}
                </div>
            </div>

            <div style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border-radius: 12px; padding: 20px; color: #ffffff;">
                <div style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.9; margin-bottom: 4px;">
                    {{ __('This Week') }}
                </div>
                <div style="font-size: 28px; font-weight: 700;">
                    {{ number_format($stats['this_week']) }}
                </div>
            </div>

            <div style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); border-radius: 12px; padding: 20px; color: #ffffff;">
                <div style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.9; margin-bottom: 4px;">
                    {{ __('This Month') }}
                </div>
                <div style="font-size: 28px; font-weight: 700;">
                    {{ number_format($stats['this_month']) }}
                </div>
            </div>
        </div>

        {{-- Top Pages Table --}}
        <div style="margin-top: 16px;">
            <h4 style="font-size: 14px; font-weight: 600; margin-bottom: 12px; color: inherit;">
                {{ __('Top 10 Pages (Last 30 Days)') }}
            </h4>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                    <thead>
                        <tr style="border-bottom: 2px solid rgba(99, 102, 241, 0.2);">
                            <th style="text-align: left; padding: 8px 12px; font-weight: 600;">#</th>
                            <th style="text-align: left; padding: 8px 12px; font-weight: 600;">{{ __('Page') }}</th>
                            <th style="text-align: right; padding: 8px 12px; font-weight: 600;">{{ __('Views') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topPages as $index => $page)
                            <tr style="border-bottom: 1px solid rgba(99, 102, 241, 0.08);">
                                <td style="padding: 8px 12px; color: rgba(107, 114, 128, 0.8);">{{ $index + 1 }}</td>
                                <td style="padding: 8px 12px;">
                                    <div style="font-weight: 500;">{{ $page->path }}</div>
                                    @if($page->title)
                                        <div style="font-size: 11px; color: rgba(107, 114, 128, 0.7); margin-top: 2px;">{{ Str::limit($page->title, 50) }}</div>
                                    @endif
                                </td>
                                <td style="padding: 8px 12px; text-align: right;">
                                    <x-filament::badge color="primary">
                                        {{ number_format($page->views) }}
                                    </x-filament::badge>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="padding: 20px; text-align: center; color: rgba(107, 114, 128, 0.7);">
                                    {{ __('No page views recorded yet.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
