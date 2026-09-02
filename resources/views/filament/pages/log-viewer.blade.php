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

            {{-- Action Buttons --}}
            <div style="display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 16px;">
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
                    color="danger"
                    size="sm"
                    icon="heroicon-o-trash"
                >
                    {{ __('Clear Log') }}
                </x-filament::button>
            </div>

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
                                x-data="{ expanded: false, copied: false }"
                                style="border: 1px solid {{ $colors['border'] }}; background: {{ $colors['bg'] }}; border-radius: 6px; padding: 12px 16px; margin-bottom: 8px;"
                            >
                                {{-- Entry Header --}}
                                <div style="display: flex; align-items: center; gap: 10px; flex-wrap: nowrap; min-width: 0;">
                                    {{-- Expand toggle (only if has stack trace) --}}
                                    @if(!empty($entry['stackTrace']))
                                        <button
                                            type="button"
                                            x-on:click="expanded = !expanded"
                                            style="background: none; border: none; cursor: pointer; padding: 2px; display: flex; align-items: center; flex-shrink: 0;"
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
                                        <span style="width: 16px; display: inline-block; flex-shrink: 0;"></span>
                                    @endif

                                    {{-- Level Badge --}}
                                    <div style="flex-shrink: 0;">
                                        <x-filament::badge :color="$colors['badge']" size="sm">
                                            {{ $entry['level'] }}
                                        </x-filament::badge>
                                    </div>

                                    {{-- Timestamp --}}
                                    <span style="font-size: 0.75rem; color: var(--gray-500); white-space: nowrap; font-family: monospace; flex-shrink: 0;">
                                        {{ $entry['timestamp'] }}
                                    </span>

                                    {{-- Message (Full text in DOM for complete copying and selection, CSS ellipsis for display) --}}
                                    <span
                                        title="{{ $entry['message'] }}"
                                        x-tooltip="{
                                            content: @js($entry['message']),
                                            theme: $store.theme
                                        }"
                                        style="font-size: 0.8125rem; color: {{ $colors['text'] }}; flex: 1; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; cursor: default; user-select: text;"
                                    >{{ $entry['message'] }}</span>

                                    {{-- Quick Copy Action Button --}}
                                    <button
                                        type="button"
                                        x-on:click="
                                            const textToCopy = @js($entry['message'] . (!empty($entry['stackTrace']) ? "\n\n" . $entry['stackTrace'] : ''));
                                            if (navigator.clipboard && window.isSecureContext) {
                                                navigator.clipboard.writeText(textToCopy);
                                            } else {
                                                const textarea = document.createElement('textarea');
                                                textarea.value = textToCopy;
                                                textarea.style.position = 'fixed';
                                                textarea.style.opacity = '0';
                                                document.body.appendChild(textarea);
                                                textarea.focus();
                                                textarea.select();
                                                document.execCommand('copy');
                                                document.body.removeChild(textarea);
                                            }
                                            copied = true;
                                            setTimeout(() => copied = false, 2000);
                                        "
                                        style="background: none; border: none; cursor: pointer; padding: 3px 6px; display: inline-flex; align-items: center; gap: 4px; color: var(--gray-500); border-radius: 4px; flex-shrink: 0; font-size: 0.7rem;"
                                        class="hover:bg-black/5 dark:hover:bg-white/5 transition"
                                        title="{{ __('Copy full log message') }}"
                                    >
                                        <svg x-show="!copied" xmlns="http://www.w3.org/2000/svg" style="width: 13px; height: 13px;" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.849A2.25 2.25 0 0 0 13.5 2.25h-3c-1.03 0-1.9.693-2.166 1.599m7.332 0c.055.194.084.4.084.615v0a.75.75 0 0 1-.75.75H9a.75.75 0 0 1-.75-.75v0c0-.215.03-.42.084-.615m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 0 1-2.25 2.25H6.75A2.25 2.25 0 0 1 4.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 0 1 1.927-.184" />
                                        </svg>
                                        <svg x-show="copied" style="display: none; width: 13px; height: 13px; color: #16a34a;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                        </svg>
                                        <span x-show="copied" style="display: none; color: #16a34a; font-weight: bold; font-size: 0.7rem;">{{ __('Copied!') }}</span>
                                    </button>
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
