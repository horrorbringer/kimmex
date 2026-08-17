<x-filament-panels::page>
    @php
        $stats = $this->getStats();
        $missingTranslations = $this->getMissingTranslations();
    @endphp

    {{-- Summary Stats --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; margin-bottom: 24px;">
        <x-filament::section>
            <div style="text-align: center;">
                <div style="font-size: 2rem; font-weight: 700; color: var(--gray-900);">{{ $stats['total'] }}</div>
                <div style="font-size: 0.8rem; color: var(--gray-500); margin-top: 4px;">{{ __('Total Records') }}</div>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div style="text-align: center;">
                <div style="font-size: 2rem; font-weight: 700; color: #10b981;">{{ $stats['fullyTranslated'] }}</div>
                <div style="font-size: 0.8rem; color: var(--gray-500); margin-top: 4px;">{{ __('Fully Translated') }}</div>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div style="text-align: center;">
                <div style="font-size: 2rem; font-weight: 700; color: #f59e0b;">{{ $stats['partiallyTranslated'] }}</div>
                <div style="font-size: 0.8rem; color: var(--gray-500); margin-top: 4px;">{{ __('Partially Translated') }}</div>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div style="text-align: center;">
                <div style="font-size: 2rem; font-weight: 700; color: #ef4444;">{{ $stats['notTranslated'] }}</div>
                <div style="font-size: 0.8rem; color: var(--gray-500); margin-top: 4px;">{{ __('Not Translated') }}</div>
            </div>
        </x-filament::section>
    </div>

    {{-- Translate All Button --}}
    @if(count($missingTranslations) > 0)
        <div style="margin-bottom: 24px; display: flex; align-items: center; gap: 12px;">
            <x-filament::button
                wire:click="translateAll"
                color="primary"
                icon="heroicon-o-language"
            >
                {{ __('Translate All') }} ({{ count($missingTranslations) }} {{ __('records') }})
            </x-filament::button>
            <span style="font-size: 0.8rem; color: var(--gray-500);">
                {{ __('Dispatches auto-translation jobs for all records missing Khmer translations.') }}
            </span>
        </div>
    @endif

    {{-- Missing Translations Table --}}
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-heroicon-o-language style="width: 18px; height: 18px;" />
                {{ __('🇰🇭 Records Missing Khmer Translations') }}
            </div>
        </x-slot>

        @if(count($missingTranslations) === 0)
            <div style="text-align: center; padding: 32px 16px;">
                <x-heroicon-o-check-circle style="width: 48px; height: 48px; color: #10b981; margin: 0 auto 12px;" />
                <p style="font-size: 1rem; font-weight: 600; color: var(--gray-900);">{{ __('All records are fully translated!') }}</p>
                <p style="font-size: 0.8rem; color: var(--gray-500);">{{ __('Every tracked field has a Khmer translation.') }}</p>
            </div>
        @else
            <div style="overflow-x: auto;">
                <table style="width: 100%; font-size: 0.8rem; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--gray-200);">
                            <th style="text-align: left; padding: 10px 12px; font-weight: 600; color: var(--gray-600);">{{ __('Model') }}</th>
                            <th style="text-align: left; padding: 10px 12px; font-weight: 600; color: var(--gray-600);">{{ __('🇬🇧 Title (English)') }}</th>
                            <th style="text-align: left; padding: 10px 12px; font-weight: 600; color: var(--gray-600);">{{ __('🇰🇭 Missing Fields') }}</th>
                            <th style="text-align: center; padding: 10px 12px; font-weight: 600; color: var(--gray-600);">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($missingTranslations as $item)
                            <tr style="border-bottom: 1px solid var(--gray-100);">
                                <td style="padding: 10px 12px;">
                                    <x-filament::badge color="info" size="sm">
                                        {{ $item['modelLabel'] }}
                                    </x-filament::badge>
                                </td>
                                <td style="padding: 10px 12px; font-weight: 500; color: var(--gray-900); max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    {{ \Illuminate\Support\Str::limit($item['recordTitle'], 50) }}
                                </td>
                                <td style="padding: 10px 12px;">
                                    <div style="display: flex; flex-wrap: wrap; gap: 4px;">
                                        @foreach($item['missingFields'] as $field)
                                            <x-filament::badge color="warning" size="sm">
                                                {{ $field }}
                                            </x-filament::badge>
                                        @endforeach
                                    </div>
                                </td>
                                <td style="padding: 10px 12px; text-align: center;">
                                    <a href="{{ $item['editUrl'] }}" style="color: var(--primary-600); font-weight: 500; text-decoration: none; font-size: 0.8rem;">
                                        {{ __('Translate') }} →
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-filament::section>
</x-filament-panels::page>
