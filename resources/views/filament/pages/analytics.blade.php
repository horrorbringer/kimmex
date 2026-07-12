<x-filament-panels::page>
    {{-- Widgets are rendered automatically via getHeaderWidgets() --}}

    <div class="mt-6" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 24px;">
        {{-- Top Pages --}}
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-document-text style="width: 18px; height: 18px;" />
                    {{ __('Top Pages (Last 30 Days)') }}
                </div>
            </x-slot>

            @if($this->getTopPages()->isEmpty())
                <p style="font-size: 0.875rem; color: var(--gray-500);">{{ __('No page views recorded yet.') }}</p>
            @else
                <div style="overflow-x: auto;">
                    <table style="width: 100%; font-size: 0.8rem; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 1px solid var(--gray-200);">
                                <th style="text-align: left; padding: 8px 12px; font-weight: 600; color: var(--gray-600);">{{ __('Page') }}</th>
                                <th style="text-align: right; padding: 8px 12px; font-weight: 600; color: var(--gray-600);">{{ __('Views') }}</th>
                                <th style="text-align: right; padding: 8px 12px; font-weight: 600; color: var(--gray-600);">{{ __('Last Visit') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($this->getTopPages() as $page)
                                <tr style="border-bottom: 1px solid var(--gray-100);">
                                    <td style="padding: 8px 12px; max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        <span style="font-weight: 500; color: var(--gray-900);">{{ $page->path }}</span>
                                        @if($page->title)
                                            <br><span style="font-size: 0.7rem; color: var(--gray-400);">{{ Str::limit($page->title, 40) }}</span>
                                        @endif
                                    </td>
                                    <td style="text-align: right; padding: 8px 12px;">
                                        <x-filament::badge color="primary" size="sm">{{ number_format($page->views) }}</x-filament::badge>
                                    </td>
                                    <td style="text-align: right; padding: 8px 12px; color: var(--gray-500); font-size: 0.75rem;">
                                        {{ \Carbon\Carbon::parse($page->last_visited)->diffForHumans() }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </x-filament::section>

        {{-- Top Referers --}}
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-arrow-top-right-on-square style="width: 18px; height: 18px;" />
                    {{ __('Top Referers (Last 30 Days)') }}
                </div>
            </x-slot>

            @if($this->getTopReferers()->isEmpty())
                <p style="font-size: 0.875rem; color: var(--gray-500);">{{ __('No referer data yet.') }}</p>
            @else
                <div style="overflow-x: auto;">
                    <table style="width: 100%; font-size: 0.8rem; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 1px solid var(--gray-200);">
                                <th style="text-align: left; padding: 8px 12px; font-weight: 600; color: var(--gray-600);">{{ __('Source') }}</th>
                                <th style="text-align: right; padding: 8px 12px; font-weight: 600; color: var(--gray-600);">{{ __('Visits') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($this->getTopReferers() as $ref)
                                <tr style="border-bottom: 1px solid var(--gray-100);">
                                    <td style="padding: 8px 12px; max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; color: var(--gray-700);">
                                        {{ $ref->referer }}
                                    </td>
                                    <td style="text-align: right; padding: 8px 12px;">
                                        <x-filament::badge color="gray" size="sm">{{ number_format($ref->visits) }}</x-filament::badge>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </x-filament::section>
    </div>
</x-filament-panels::page>
