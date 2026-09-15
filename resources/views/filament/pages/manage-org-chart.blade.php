<x-filament-panels::page>
    <div class="space-y-3" x-data="{
        activeTab: 'tree',
        search: ''
    }">
        @php
            $countNodes = function (array $nodes) use (&$countNodes): int {
                return collect($nodes)->sum(fn ($node) => 1 + $countNodes($node['children'] ?? []));
            };

            $maxDepth = function (array $nodes, int $depth = 1) use (&$maxDepth): int {
                if (empty($nodes)) {
                    return 0;
                }

                return collect($nodes)->max(fn ($node) => max($depth, $maxDepth($node['children'] ?? [], $depth + 1)));
            };

            $rootCount = count($chartData);
            $unitCount = $countNodes($chartData);
            $depthCount = $maxDepth($chartData);
        @endphp

        <style>
            :root {
                --org-navy: #0B2B5C;
                --org-navy-light: #0E3A7A;
                --org-blue: #185FA5;
                --org-red: #E31E24;
                --org-border: #e2e8f0;
                --org-muted: #64748b;
                --org-panel: #ffffff;
                --org-canvas: #f8fafc;
            }

            .org-card-container {
                background: #ffffff;
                border: 1px solid var(--org-border);
                border-radius: 0.625rem;
                overflow: hidden;
                box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
            }

            /* Compact View Switcher Bar */
            .org-view-tabs {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 0.5rem;
                flex-wrap: wrap;
                background: #ffffff;
                padding: 0.45rem 0.75rem;
                border: 1px solid var(--org-border);
                border-radius: 0.5rem;
            }

            .org-tab-btn {
                display: inline-flex;
                align-items: center;
                gap: 0.375rem;
                padding: 0.35rem 0.625rem;
                border-radius: 0.375rem;
                font-size: 0.75rem;
                font-weight: 700;
                border: 1px solid transparent;
                cursor: pointer;
                transition: all 0.15s ease;
            }

            .org-tab-btn.is-active {
                background: var(--org-navy);
                color: #ffffff !important;
                box-shadow: 0 1px 2px rgba(11, 43, 92, 0.15);
            }

            .org-tab-btn:not(.is-active) {
                background: #f8fafc;
                color: #475569;
                border-color: var(--org-border);
            }

            .org-tab-btn:not(.is-active):hover {
                background: #eef4fb;
                color: var(--org-navy);
                border-color: #cbd5e1;
            }

            .org-tab-badge {
                font-size: 0.625rem;
                font-weight: 800;
                padding: 0.05rem 0.35rem;
                border-radius: 9999px;
            }

            .org-tab-btn.is-active .org-tab-badge {
                background: rgba(255, 255, 255, 0.2);
                color: #ffffff;
            }

            .org-tab-btn:not(.is-active) .org-tab-badge {
                background: #e2e8f0;
                color: #475569;
            }

            /* Compact Tree List Layout */
            .org-tree-list-wrapper {
                padding: 0.75rem 1rem;
                display: flex;
                flex-direction: column;
                gap: 0.35rem;
            }

            .org-tree-toolbar {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 0.5rem;
                flex-wrap: wrap;
                margin-bottom: 0.375rem;
            }

            .org-search-box {
                position: relative;
                display: flex;
                align-items: center;
                min-width: 13rem;
                max-width: 18rem;
                flex: 1;
            }

            .org-search-icon {
                position: absolute;
                left: 0.65rem;
                color: var(--org-muted);
                pointer-events: none;
            }

            .org-tree-search-input {
                width: 100%;
                height: 2rem;
                border: 1px solid #cbd5e1;
                border-radius: 0.375rem;
                padding: 0 0.5rem 0 2rem;
                font-size: 0.75rem;
                color: var(--org-navy);
                background: #ffffff;
                box-sizing: border-box;
                transition: all 0.15s ease;
            }

            .org-tree-search-input:focus {
                outline: none;
                border-color: var(--org-blue);
                box-shadow: 0 0 0 2px rgba(24, 95, 165, 0.15);
            }

            /* Small & Compact Tree Card */
            .org-item-card {
                background: #ffffff;
                border: 1px solid var(--org-border);
                border-radius: 0.45rem;
                padding: 0.35rem 0.625rem;
                display: flex;
                align-items: center;
                gap: 0.5rem;
                transition: all 0.15s ease;
                box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
                min-height: 2.625rem;
            }

            .org-item-card:hover {
                border-color: #94a3b8;
                box-shadow: 0 2px 6px rgba(11, 43, 92, 0.05);
            }

            .org-item-card.is-root {
                border-left: 3px solid var(--org-navy);
                background: #fafcff;
            }

            .org-item-card.is-inactive {
                opacity: 0.65;
                border-style: dashed;
                background: #fdfdfd;
            }

            .org-expand-btn {
                width: 1.35rem;
                height: 1.35rem;
                display: grid;
                place-items: center;
                border-radius: 0.25rem;
                background: #f1f5f9;
                color: #475569;
                border: none;
                cursor: pointer;
                flex-shrink: 0;
                transition: all 0.15s;
            }

            .org-expand-btn:hover {
                background: #e2e8f0;
                color: var(--org-navy);
            }

            .org-avatar-box {
                width: 1.75rem;
                height: 1.75rem;
                border-radius: 9999px;
                background: #eef4fb;
                color: var(--org-navy);
                font-weight: 800;
                font-size: 0.6875rem;
                display: grid;
                place-items: center;
                flex-shrink: 0;
                border: 1px solid #cbd5e1;
                overflow: hidden;
            }

            .org-item-details {
                min-width: 0;
                flex: 1;
            }

            .org-person-name {
                font-size: 0.8125rem;
                font-weight: 700;
                color: var(--org-navy);
                line-height: 1.2;
            }

            .org-position-title {
                font-size: 0.6875rem;
                font-style: italic;
                color: #64748b;
                margin: 0.05rem 0 0 0;
                line-height: 1.2;
            }

            .org-badge-type {
                font-size: 0.5625rem;
                font-weight: 700;
                padding: 0.05rem 0.35rem;
                border-radius: 0.25rem;
                text-transform: uppercase;
                background: #f1f5f9;
                color: var(--org-blue);
                letter-spacing: 0.02em;
            }

            .org-badge-children {
                font-size: 0.5625rem;
                font-weight: 700;
                padding: 0.05rem 0.35rem;
                border-radius: 9999px;
                background: #eef4fb;
                color: var(--org-navy);
            }

            .org-badge-hidden {
                font-size: 0.5625rem;
                font-weight: 700;
                padding: 0.05rem 0.35rem;
                border-radius: 9999px;
                background: #fef2f2;
                color: #dc2626;
            }

            .org-item-actions {
                display: flex;
                align-items: center;
                gap: 0.25rem;
                flex-shrink: 0;
                margin-left: auto;
            }

            .org-icon-btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 1.625rem;
                height: 1.625rem;
                border-radius: 0.35rem;
                border: 1px solid #e2e8f0;
                background: #ffffff;
                color: #64748b;
                cursor: pointer;
                transition: all 0.15s ease;
            }

            .org-icon-btn:hover {
                border-color: #94a3b8;
                color: var(--org-navy);
                background: #f8fafc;
            }

            .org-icon-btn.is-add {
                color: var(--org-blue);
                background: #f0f7ff;
                border-color: #bfdbfe;
            }

            .org-icon-btn.is-add:hover {
                background: var(--org-blue);
                color: #ffffff;
                border-color: var(--org-blue);
            }

            .org-icon-btn.is-delete:hover {
                background: #fef2f2;
                color: #dc2626;
                border-color: #fca5a5;
            }

            /* D3 Visual Diagram Styles */
            .kimmex-org-chart {
                min-height: 40rem;
                border: 1px solid var(--org-border);
                border-radius: 0.5rem;
                background:
                    radial-gradient(circle at 1px 1px, rgba(148, 163, 184, 0.2) 1px, transparent 0) 0 0 / 24px 24px,
                    #f8fafc;
                overflow: hidden;
            }

            .kimmex-org-chart svg { cursor: grab; }
            .kimmex-org-chart svg:active { cursor: grabbing; }
            .kimmex-org-chart .link { stroke: #0B2B5C !important; stroke-width: 1.5px !important; }

            .kimmex-org-chart__node {
                box-sizing: border-box;
                display: flex;
                align-items: center;
                height: 96px;
                padding: 0.75rem 0.875rem;
                gap: 0.75rem;
                border: 1px solid var(--org-border);
                border-top: 3px solid var(--org-navy);
                border-radius: 0.5rem;
                background: #ffffff;
                box-shadow: 0 3px 8px rgba(15, 23, 42, 0.05);
                cursor: pointer;
            }

            .kimmex-org-chart__avatar {
                display: grid;
                width: 2.5rem;
                height: 2.5rem;
                place-items: center;
                overflow: hidden;
                flex-shrink: 0;
                border-radius: 9999px;
                background: #eff6ff;
                border: 2px solid #ffffff;
                box-shadow: 0 0 0 1px var(--org-border);
                color: var(--org-navy);
                font-size: 0.75rem;
                font-weight: 800;
            }

            .kimmex-org-chart__avatar img { width: 100%; height: 100%; object-fit: cover; }
            .kimmex-org-chart__content { min-width: 0; flex: 1; }
            .kimmex-org-chart__name { margin: 0; color: var(--org-navy); font-size: 0.8125rem; font-weight: 800; line-height: 1.25; }
            .kimmex-org-chart__title { margin: 0.15rem 0 0; color: var(--org-muted); font-size: 0.6875rem; font-weight: 600; line-height: 1.35; }
            .kimmex-org-chart__footer { display: flex; align-items: center; justify-content: space-between; margin-top: 0.3rem; }
            .kimmex-org-chart__type { display: inline-block; color: var(--org-blue); font-size: 0.5625rem; font-weight: 800; letter-spacing: 0.05em; text-transform: uppercase; background: #f1f5f9; padding: 0.05rem 0.35rem; border-radius: 0.25rem; }
            .kimmex-org-chart__badge { display: inline-block; padding: 0.1rem 0.35rem; border-radius: 9999px; font-size: 0.5625rem; font-weight: 800; text-transform: uppercase; line-height: 1; }
            .kimmex-org-chart__badge--visible { color: #166534; background: #dcfce7; }
            .kimmex-org-chart__badge--hidden { color: #991b1b; background: #fee2e2; }
            .kimmex-org-chart__virtual-root { display: grid; height: 44px; place-items: center; border-radius: 9999px; color: #ffffff; background: var(--org-navy); font-size: 0.75rem; font-weight: 900; letter-spacing: 0.05em; text-transform: uppercase; }
            .kimmex-org-chart__toggle { display: grid; width: 1.5rem; height: 1.5rem; place-items: center; border: 2px solid #ffffff; border-radius: 9999px; color: #ffffff; background: var(--org-navy); font-size: 0.875rem; font-weight: 900; box-shadow: 0 2px 5px rgba(15, 23, 42, 0.15); }

            .org-empty-state {
                border: 1px dashed var(--org-border);
                border-radius: 0.5rem;
                padding: 2.5rem 1.25rem;
                color: var(--org-muted);
                text-align: center;
                background: #ffffff;
            }
        </style>

        {{-- Clean View Switcher Tab Navigation --}}
        <div class="org-view-tabs">
            <div style="display: flex; align-items: center; gap: 0.375rem;">
                <button type="button"
                        @click="activeTab = 'tree'"
                        class="org-tab-btn"
                        :class="activeTab === 'tree' ? 'is-active' : ''">
                    <x-heroicon-o-bars-3-bottom-left style="width: 14px; height: 14px;" />
                    <span>{{ __('Hierarchy Tree') }}</span>
                    <span class="org-tab-badge">{{ $unitCount }}</span>
                </button>

                <button type="button"
                        @click="activeTab = 'chart'; $nextTick(() => window.dispatchEvent(new Event('resize')))"
                        class="org-tab-btn"
                        :class="activeTab === 'chart' ? 'is-active' : ''">
                    <x-heroicon-o-presentation-chart-line style="width: 14px; height: 14px;" />
                    <span>{{ __('Diagram Canvas') }}</span>
                    <span class="org-tab-badge">{{ __('Interactive builder') }}</span>
                </button>
            </div>

            <div style="display: flex; align-items: center; gap: 0.75rem; font-size: 0.6875rem; color: #64748b; font-weight: 600;">
                <span class="sr-only">{{ __('Website display settings') }}</span>
                <span>{{ $rootCount }} {{ __('Root') }}</span> • <span>{{ $unitCount }} {{ __('Positions') }}</span> • <span>{{ $depthCount }} {{ __('Tiers') }}</span>
            </div>
        </div>

        {{-- TAB 1: Hierarchy Tree (Clean & Compact) --}}
        <div x-show="activeTab === 'tree'" class="org-card-container">
            <div class="org-tree-list-wrapper">
                <div class="org-tree-toolbar">
                    <div class="org-search-box">
                        <x-heroicon-o-magnifying-glass class="org-search-icon" style="width: 14px; height: 14px;" />
                        <input x-model="search"
                               type="search"
                               class="org-tree-search-input"
                               placeholder="{{ __('Search by name, role, tier...') }}" />
                    </div>

                    <div style="font-size: 0.6875rem; color: #64748b;">
                        <span>{{ __('Click') }} <strong>+</strong> {{ __('to add subordinate') }}</span>
                    </div>
                </div>

                @if(empty($chartData))
                    <div class="org-empty-state">
                        <x-heroicon-o-building-office-2 style="width: 36px; height: 36px; margin: 0 auto 10px; color: var(--org-navy);" />
                        <h3 style="color: var(--org-navy); font-size: 0.9375rem; font-weight: 800; margin: 0;">{{ __('No organizational positions yet') }}</h3>
                        <p style="margin: 0.35rem auto 1rem; font-size: 0.75rem; color: #64748b; max-width: 400px;">
                            {{ __('Get started instantly in 1 click by applying a corporate template, or add a root unit.') }}
                        </p>
                        <div style="display: flex; align-items: center; justify-content: center; gap: 0.5rem; flex-wrap: wrap;">
                            <x-filament::button wire:click="mountAction('loadTemplate')" icon="heroicon-o-sparkles" color="success" size="sm">
                                {{ __('Load Template (1-Click)') }}
                            </x-filament::button>
                            <x-filament::button wire:click="mountAction('addRoot')" icon="heroicon-o-plus" color="primary" size="sm">
                                {{ __('Add Root Position') }}
                            </x-filament::button>
                        </div>
                    </div>
                @else
                    <div style="display: flex; flex-direction: column; gap: 0.375rem;">
                        @foreach($chartData as $rootNode)
                            @include('filament.pages.partials.org-tree-item', ['node' => $rootNode, 'depth' => 0])
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- TAB 2: Diagram Chart (Interactive Canvas) --}}
        <div x-show="activeTab === 'chart'"
             class="org-card-container"
             data-org-chart
             data-chart-data="{{ json_encode($chartData, JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_HEX_TAG) }}"
             x-on:org-chart:edit.window="$wire.mountAction('edit', $event.detail)"
             style="padding: 1rem;">

            @if(empty($chartData))
                <div class="org-empty-state">
                    <x-heroicon-o-building-office-2 style="width: 32px; height: 32px; margin: 0 auto 10px; color: var(--org-navy);" />
                    <h3 style="color: var(--org-navy); font-size: 0.875rem; font-weight: 800; margin: 0;">{{ __('No organizational units yet') }}</h3>
                    <p style="margin: 0.25rem 0 0; font-size: 0.75rem;">{{ __('Use Add Root Position to start building the hierarchy.') }}</p>
                </div>
            @else
                <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem; margin-bottom: 0.75rem; flex-wrap: wrap;">
                    <div class="org-search-box">
                        <x-heroicon-o-magnifying-glass class="org-search-icon" style="width: 14px; height: 14px;" />
                        <label class="sr-only" for="org-chart-search">{{ __('Search organization chart') }}</label>
                        <input id="org-chart-search" type="search" class="org-tree-search-input" data-org-chart-search placeholder="{{ __('Search in diagram...') }}" />
                    </div>
                    <span class="org-chart-search-status" data-org-chart-search-status style="font-size: 0.6875rem; font-weight: 600; color: var(--org-muted);"></span>

                    <div style="display: flex; align-items: center; gap: 0.25rem; margin-left: auto;">
                        <button type="button" class="org-icon-btn" data-org-chart-action="fit" title="{{ __('Fit to Screen') }}">
                            <x-heroicon-o-arrows-pointing-in style="width: 14px; height: 14px;" />
                        </button>
                        <button type="button" class="org-icon-btn" data-org-chart-action="zoomIn" title="{{ __('Zoom In') }}">
                            <x-heroicon-o-plus style="width: 14px; height: 14px;" />
                        </button>
                        <button type="button" class="org-icon-btn" data-org-chart-action="zoomOut" title="{{ __('Zoom Out') }}">
                            <x-heroicon-o-minus style="width: 14px; height: 14px;" />
                        </button>
                        <button type="button" class="org-icon-btn" data-org-chart-action="reset" title="{{ __('Reset View') }}">
                            <x-heroicon-o-arrow-path style="width: 14px; height: 14px;" />
                        </button>
                    </div>
                </div>

                <div class="kimmex-org-chart" data-org-chart-canvas wire:ignore></div>
            @endif
        </div>
    </div>

    {{-- D3 script asset --}}
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/d3@7" defer></script>
        @vite('resources/js/admin-org-chart.js')
    @endpush
</x-filament-panels::page>
