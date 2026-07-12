<x-filament-panels::page>
    <div class="fi-page-content-ctn">

        {{-- Header Actions --}}
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-bug-ant style="width: 20px; height: 20px; color: #dc2626;" />
                    {{ __('Application Log Viewer') }}
                    <x-filament::badge color="gray" size="sm">{{ $this->logFileSize }}</x-filament::badge>
                </div>
            </x-slot>
            <x-slot name="description">
                {{ __('View and manage the application error log. Showing the most recent entries from storage/logs/laravel.log.') }}
            </x-slot>
            <x-slot name="headerEnd">
                <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                    <x-filament::button
                        wire:click="refresh"
                        color="gray"
                        size="sm"
                        icon="heroicon-o-arrow-path"
                    >
                        {{ __('Refresh') }}
                    </x-filament::button>

                    <x-filament::button
                        wire:click="downloadLog"
                        color="info"
                        size="sm"
                        icon="heroicon-o-arrow-down-tray"
                    >
                        {{ __('Download') }}
                    </x-filament::button>

                    <x-filament::button
                        wire:click="clearLog"
                        wire:confirm="{{ __('Are you sure you want to clear the log file? This action cannot be undone.') }}"
                        color="danger"
                        size="sm"
                        icon="heroicon-o-trash"
                    >
                        {{ __('Clear Log') }}
                    </x-filament::button>
                </div>
            </x-slot>

            {{-- Search & Filters --}}
            <div style="display: flex; flex-wrap: wrap; gap: 12px; align-items: center;">
                {{-- Search Input --}}
                <div style="flex: 1; min-width: 250px;">
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="{{ __('Search log entries...') }}"
                        class="fi-input"
                        style="width: 100%; padding: 8px 12px; border: 1px solid var(--gray-300); border-radius: 6px; font-size: 0.875rem;"
                    />
                </div>

                {{-- Level Filter Buttons --}}
                <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                    <x-filament::button
                        wire:click="setLevel('all')"
                        size="sm"
                        :color="$levelFilter === 'all' ? 'primary' : 'gray'"
                    >
                        {{ __('All') }}
                    </x-filament::button>

                    <x-filament::button
                        wire:click="setLevel('error')"
                        size="sm"
                        :color="$levelFilter === 'error' ? 'danger' : 'gray'"
                    >
                        {{ __('Error') }}
                    </x-filament::button>

                    <x-filament::button
                        wire:click="setLevel('warning')"
                        size="sm"
                        :color="$levelFilter === 'warning' ? 'warning' : 'gray'"
                    >
                        {{ __('Warning') }}
                    </x-filament::button>

                    <x-filament::button
                        wire:click="setLevel('info')"
                        size="sm"
                        :color="$levelFilter === 'info' ? 'info' : 'gray'"
                    >
                        {{ __('Info') }}
                    </x-filament::button>

                    <x-filament::button
                        wire:click="setLevel('debug')"
                        size="sm"
                        :color="$levelFilter === 'debug' ? 'gray' : 'gray'"
                    >
                        {{ __('Debug') }}
                    </x-filament::button>
                </div>
            </div>
        </x-filament::section>

        {{-- Log Entries --}}
        <div style="margin-top: 16px;">
            <x-filament::section>
                <x-slot name="heading">
                    <div class="flex items-center gap-2">
                        <x-heroicon-o-document-text style="width: 18px; height: 18px;" />
                        {{ __('Log Entries') }}
                        <x-filament::badge color="gray" size="sm">
                            {{ $this->totalEntries }} {{ __('entries') }}
                        </x-filament::badge>
                    </div>
                </x-slot>

                @if(count($this->paginatedEntries) === 0)
                    <div style="text-align: center; padding: 40px 20px; color: var(--gray-400);">
                        <x-heroicon-o-document-magnifying-glass style="width: 48px; height: 48px; margin: 0 auto 12px; opacity: 0.5;" />
                        <p style="font-size: 0.875rem;">{{ __('No log entries found.') }}</p>
                    </div>
                @else
                    <div style="max-height: 600px; overflow-y: auto; border-radius: 8px;">
                        @foreach($this->paginatedEntries as $index => $entry)
                            @php
                                $levelColors = [
                                    'ERROR' => ['bg' => '#fef2f2', 'border' => '#fecaca', 'badge' => 'danger', 'text' => '#991b1b'],
                                    'CRITICAL' => ['bg' => '#fef2f2', 'border' => '#fecaca', 'badge' => 'danger', 'text' => '#991b1b'],
                                    'ALERT' => ['bg' => '#fef2f2', 'border' => '#fecaca', 'badge' => 'danger', 'text' => '#991b1b'],
                                    'EMERGENCY' => ['bg' => '#fef2f2', 'border' => '#fecaca', 'badge' => 'danger', 'text' => '#991b1b'],
                                    'WARNING' => ['bg' => '#fffbeb', 'border' => '#fde68a', 'badge' => 'warning', 'text' => '#92400e'],
                                    'NOTICE' => ['bg' => '#fffbeb', 'border' => '#fde68a', 'badge' => 'warning', 'text' => '#92400e'],
                                    'INFO' => ['bg' => '#eff6ff', 'border' => '#bfdbfe', 'badge' => 'info', 'text' => '#1e40af'],
                                    'DEBUG' => ['bg' => '#f9fafb', 'border' => '#e5e7eb', 'badge' => 'gray', 'text' => '#374151'],
                                ];
                                $colors = $levelColors[$entry['level']] ?? $levelColors['DEBUG'];
                            @endphp

                            <div
                                x-data="{ expanded: false }"
                                style="border: 1px solid {{ $colors['border'] }}; background: {{ $colors['bg'] }}; border-radius: 6px; padding: 12px 16px; margin-bottom: 8px;"
                            >
                                {{-- Entry Header --}}
                                <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                                    {{-- Expand toggle (only if has stack trace) --}}
                                    @if(!empty($entry['stackTrace']))
                                        <button
                                            x-on:click="expanded = !expanded"
                                            style="background: none; border: none; cursor: pointer; padding: 2px; display: flex; align-items: center;"
                                            title="{{ __('Toggle stack trace') }}"
                                        >
                                            <svg x-show="!expanded" xmlns="http://www.w3.org/2000/svg" style="width: 16px; height: 16px; color: {{ $colors['text'] }};" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                            </svg>
                                            <svg x-show="expanded" xmlns="http://www.w3.org/2000/svg" style="width: 16px; height: 16px; color: {{ $colors['text'] }};" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                            </svg>
                                        </button>
                                    @else
                                        <span style="width: 16px; display: inline-block;"></span>
                                    @endif

                                    {{-- Level Badge --}}
                                    <x-filament::badge :color="$colors['badge']" size="sm">
                                        {{ $entry['level'] }}
                                    </x-filament::badge>

                                    {{-- Timestamp --}}
                                    <span style="font-size: 0.75rem; color: var(--gray-500); white-space: nowrap; font-family: monospace;">
                                        {{ $entry['timestamp'] }}
                                    </span>

                                    {{-- Message --}}
                                    <span style="font-size: 0.8125rem; color: {{ $colors['text'] }}; flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        {{ \Illuminate\Support\Str::limit($entry['message'], 150) }}
                                    </span>
                                </div>

                                {{-- Stack Trace (Collapsible) --}}
                                @if(!empty($entry['stackTrace']))
                                    <div x-show="expanded" x-collapse style="margin-top: 10px;">
                                        <pre style="background: #1e293b; color: #e2e8f0; padding: 12px; border-radius: 6px; font-size: 0.7rem; font-family: 'JetBrains Mono', 'Fira Code', monospace; overflow-x: auto; white-space: pre-wrap; word-break: break-word; max-height: 300px; overflow-y: auto; line-height: 1.5;">{{ $entry['stackTrace'] }}</pre>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 16px; padding-top: 12px; border-top: 1px solid var(--gray-200);">
                        <span style="font-size: 0.8125rem; color: var(--gray-500);">
                            {{ __('Page :current of :total', ['current' => $page, 'total' => $this->totalPages]) }}
                            &middot;
                            {{ __('Showing :from-:to of :total entries', [
                                'from' => (($page - 1) * $perPage) + 1,
                                'to' => min($page * $perPage, $this->totalEntries),
                                'total' => $this->totalEntries,
                            ]) }}
                        </span>

                        <div style="display: flex; gap: 6px;">
                            <x-filament::button
                                wire:click="previousPage"
                                size="sm"
                                color="gray"
                                :disabled="$page <= 1"
                                icon="heroicon-o-chevron-left"
                            >
                                {{ __('Previous') }}
                            </x-filament::button>

                            <x-filament::button
                                wire:click="nextPage"
                                size="sm"
                                color="gray"
                                :disabled="$page >= $this->totalPages"
                                icon="heroicon-o-chevron-right"
                                icon-position="after"
                            >
                                {{ __('Next') }}
                            </x-filament::button>
                        </div>
                    </div>
                @endif
            </x-filament::section>
        </div>

    </div>
</x-filament-panels::page>
