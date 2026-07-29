<x-filament-panels::page>
    <div class="space-y-6">
        @php
            $theme = \App\Models\SystemSetting::get('theme_settings', []);
            $primaryColor = $theme['primary_color'] ?? '#D4A017'; 
            $primaryHover = $theme['primary_color_hover'] ?? '#B8890F'; 
            $secondaryColor = $theme['secondary_color'] ?? '#0B2B5C'; 
            $secondaryHover = $theme['secondary_color_hover'] ?? '#0E3A7A'; 

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
                --org-accent: {{ $primaryColor }};
                --org-accent-hover: {{ $primaryHover }};
                --org-navy: {{ $secondaryColor }};
                --org-navy-soft: {{ $secondaryHover }};
                --org-border: #e2e8f0;
                --org-muted: #64748b;
                --org-panel: #ffffff;
                --org-canvas: #f8fafc;
            }

            .org-chart-wrapper {
                background: var(--org-canvas);
                border: 1px solid var(--org-border);
                border-radius: 1rem;
                overflow: hidden;
            }

            .org-chart-toolbar,
            .org-chart-settings,
            .org-chart-board {
                background: var(--org-panel);
            }

            .org-chart-toolbar {
                display: grid;
                gap: 1rem;
                grid-template-columns: minmax(0, 1fr) auto;
                align-items: center;
                padding: 1rem 1.25rem;
                border-bottom: 1px solid var(--org-border);
            }

            .org-chart-title {
                display: flex;
                align-items: center;
                gap: 0.75rem;
                min-width: 0;
            }

            .org-chart-title-icon {
                width: 2.5rem;
                height: 2.5rem;
                display: grid;
                place-items: center;
                border-radius: 0.75rem;
                color: var(--org-navy);
                background: color-mix(in srgb, var(--org-accent) 16%, white);
                flex: none;
            }

            .org-chart-title h2 {
                color: var(--org-navy);
                font-size: 1rem;
                font-weight: 800;
                line-height: 1.25;
                margin: 0;
            }

            .org-chart-title p {
                color: var(--org-muted);
                font-size: 0.8125rem;
                line-height: 1.4;
                margin: 0.125rem 0 0;
            }

            .org-chart-mode {
                display: inline-flex;
                align-items: center;
                min-height: 1.75rem;
                margin-top: 0.5rem;
                padding: 0.25rem 0.625rem;
                border-radius: 999px;
                color: var(--org-navy);
                background: color-mix(in srgb, var(--org-accent) 13%, white);
                font-size: 0.6875rem;
                font-weight: 800;
            }

            .org-chart-tip {
                max-width: 18rem;
                color: var(--org-muted);
                font-size: 0.75rem;
                line-height: 1.45;
                text-align: right;
            }

            .org-chart-stats {
                display: grid;
                grid-template-columns: repeat(3, minmax(5.5rem, 1fr));
                gap: 0.625rem;
                padding: 0 1.25rem 1.25rem;
                background: var(--org-panel);
                border-bottom: 1px solid var(--org-border);
            }

            .org-stat {
                border: 1px solid var(--org-border);
                border-radius: 0.75rem;
                padding: 0.75rem;
                background: #fff;
            }

            .org-stat-value {
                color: var(--org-navy);
                display: block;
                font-size: 1.125rem;
                font-weight: 800;
                line-height: 1;
            }

            .org-stat-label {
                color: var(--org-muted);
                display: block;
                font-size: 0.6875rem;
                font-weight: 700;
                margin-top: 0.35rem;
                text-transform: uppercase;
            }

            .org-chart-settings {
                padding: 1.25rem;
                border-bottom: 1px solid var(--org-border);
            }

            .org-chart-settings-card {
                border: 1px solid var(--org-border);
                border-radius: 0.875rem;
                overflow: hidden;
                background: #fff;
            }

            .org-chart-settings-card summary {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 1rem;
                padding: 0.875rem 1rem;
                color: var(--org-navy);
                cursor: pointer;
                font-size: 0.8125rem;
                font-weight: 800;
                list-style: none;
            }

            .org-chart-settings-card summary::-webkit-details-marker { display: none; }

            .org-chart-settings-card summary::after {
                content: '+';
                display: grid;
                width: 1.5rem;
                height: 1.5rem;
                place-items: center;
                border-radius: 999px;
                color: var(--org-navy);
                background: #f1f5f9;
                font-size: 1rem;
            }

            .org-chart-settings-card[open] summary::after { content: '−'; }

            .org-chart-settings-body {
                padding: 1rem;
                border-top: 1px solid var(--org-border);
            }

            .org-chart-settings-body > p {
                margin: 0 0 1rem;
                color: var(--org-muted);
                font-size: 0.75rem;
            }

            .org-chart-board {
                padding: 1.25rem;
            }

            .org-chart-controls {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                gap: 0.5rem;
                margin-bottom: 1rem;
            }

            .org-chart-search {
                width: min(100%, 17rem);
                min-height: 2.5rem;
                margin-right: auto;
                border: 1px solid var(--org-border);
                border-radius: 0.625rem;
                padding: 0 0.75rem;
                color: var(--org-navy);
                font-size: 0.8125rem;
            }

            .org-chart-search-status {
                color: var(--org-muted);
                font-size: 0.6875rem;
                font-weight: 700;
            }

            .org-chart-control {
                display: inline-flex;
                align-items: center;
                gap: 0.375rem;
                min-height: 2.5rem;
                border: 1px solid var(--org-border);
                border-radius: 0.625rem;
                padding: 0.5rem 0.75rem;
                color: var(--org-navy);
                background: #fff;
                font-size: 0.75rem;
                font-weight: 800;
                cursor: pointer;
                transition: border-color 0.2s ease, background-color 0.2s ease;
            }

            .org-chart-control svg { width: 1rem; height: 1rem; }

            .org-chart-control:hover {
                border-color: var(--org-accent);
                background: color-mix(in srgb, var(--org-accent) 8%, #fff);
            }

            .kimmex-org-chart {
                min-height: 45rem;
                overflow: hidden;
                border: 1px solid var(--org-border);
                border-radius: 0.875rem;
                background:
                    radial-gradient(circle at 1px 1px, rgba(148, 163, 184, 0.25) 1px, transparent 0) 0 0 / 20px 20px,
                    #f8fafc;
            }

            .kimmex-org-chart svg {
                cursor: grab;
            }

            .kimmex-org-chart svg:active {
                cursor: grabbing;
            }

            .kimmex-org-chart .link {
                stroke: color-mix(in srgb, var(--org-navy) 25%, #cbd5e1) !important;
                stroke-width: 1.5px !important;
            }

            .kimmex-org-chart__node {
                box-sizing: border-box;
                display: flex;
                align-items: center;
                height: 106px;
                padding: 0.875rem;
                gap: 0.75rem;
                border: 1px solid var(--org-border);
                border-radius: 0.875rem;
                background: #fff;
                box-shadow: 0 6px 18px rgba(15, 23, 42, 0.08);
                transition: box-shadow 0.2s ease, transform 0.2s ease;
            }

            .kimmex-org-chart__node:hover {
                box-shadow: 0 12px 28px rgba(15, 23, 42, 0.14);
                transform: translateY(-2px);
            }

            .kimmex-org-chart__avatar {
                display: grid;
                width: 3rem;
                height: 3rem;
                place-items: center;
                overflow: hidden;
                flex: none;
                border-radius: 999px;
                color: var(--org-navy);
                background: color-mix(in srgb, var(--org-accent) 14%, #fff);
                font-size: 0.75rem;
                font-weight: 800;
            }

            .kimmex-org-chart__avatar img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .kimmex-org-chart__content { min-width: 0; }
            .kimmex-org-chart__name { margin: 0; color: var(--org-navy); font-size: 0.875rem; font-weight: 800; line-height: 1.25; }
            .kimmex-org-chart__title { margin: 0.25rem 0 0; color: var(--org-muted); font-size: 0.6875rem; font-weight: 700; line-height: 1.35; }
            .kimmex-org-chart__type { display: inline-block; margin-top: 0.5rem; color: var(--org-accent); font-size: 0.5625rem; font-weight: 900; letter-spacing: 0.08em; text-transform: uppercase; }
            .kimmex-org-chart__virtual-root { display: grid; height: 48px; place-items: center; border-radius: 999px; color: #fff; background: var(--org-navy); font-size: 0.75rem; font-weight: 900; letter-spacing: 0.05em; text-transform: uppercase; }
            .kimmex-org-chart__toggle { display: grid; width: 1.75rem; height: 1.75rem; place-items: center; border: 2px solid #fff; border-radius: 999px; color: #fff; background: var(--org-accent); font-size: 1rem; font-weight: 900; box-shadow: 0 2px 8px rgba(15, 23, 42, 0.18); }

            .org-tree {
                max-width: 64rem;
                margin: 0 auto;
            }

            .org-tree-root {
                display: grid;
                gap: 0.625rem;
            }

            .node-card {
                background: #fff;
                border: 1px solid var(--org-border);
                border-radius: 0.875rem;
                padding: 0.875rem;
                display: flex;
                align-items: center;
                gap: 0.75rem;
                min-height: 4.5rem;
                position: relative;
                box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
                transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
            }

            .node-card:hover {
                border-color: color-mix(in srgb, var(--org-navy) 30%, var(--org-border));
                box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
                transform: translateY(-1px);
            }

            .node-drag-handle,
            .node-toggle-placeholder {
                color: #94a3b8;
                width: 1.75rem;
                height: 1.75rem;
                display: grid;
                place-items: center;
                border-radius: 0.5rem;
                flex: none;
            }

            .node-drag-handle {
                cursor: grab;
                background: #f8fafc;
            }

            .node-drag-handle:hover {
                color: var(--org-navy);
                background: #eef2f7;
            }

            .sortable-handle {
                cursor: grab;
            }

            .sortable-handle:active {
                cursor: grabbing;
            }

            .node-toggle-placeholder {
                background: transparent;
            }

            .node-avatar {
                width: 2.75rem;
                height: 2.75rem;
                border-radius: 50%;
                flex-shrink: 0;
                display: flex;
                align-items: center;
                justify-content: center;
                background: color-mix(in srgb, var(--org-accent) 14%, #f8fafc);
                overflow: hidden;
                border: 2px solid #fff;
                box-shadow: 0 0 0 1px var(--org-border);
            }

            .node-avatar img,
            .node-avatar svg {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .node-role-list {
                color: var(--org-muted);
                font-size: 0.6875rem;
                font-weight: 700;
                text-transform: uppercase;
                white-space: nowrap;
                flex: none;
            }

            .children-container {
                position: relative;
                display: grid;
                gap: 0.625rem;
                margin-left: 3.25rem;
                padding-left: 1.25rem;
                padding-top: 0.625rem;
            }

            .children-container::before {
                content: '';
                position: absolute;
                left: 0;
                top: 0;
                bottom: 2.25rem;
                border-left: 1px solid var(--org-border);
            }

            .children-container .node-card::before {
                content: '';
                position: absolute;
                left: -1.25rem;
                top: 50%;
                width: 1.25rem;
                border-top: 1px solid var(--org-border);
                transform: translateY(-50%);
            }

            .node-name {
                color: var(--org-navy);
                font-weight: 800;
                font-size: 0.875rem;
                line-height: 1.25;
                margin: 0 !important;
            }

            .node-role {
                color: var(--org-muted);
                font-size: 0.6875rem;
                font-weight: 700;
                text-transform: uppercase;
                margin: 0 !important;
                margin-top: 0.25rem !important;
            }

            .avatar-circle {
                width: 2.75rem !important;
                height: 2.75rem !important;
                border-radius: 50% !important;
                object-fit: cover;
                border: 2px solid #fff;
                box-shadow: 0 0 0 1px var(--org-border);
                flex-shrink: 0;
                display: flex;
                align-items: center;
                justify-content: center;
                background: color-mix(in srgb, var(--org-accent) 14%, #f8fafc);
                overflow: hidden;
            }

            .avatar-circle img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }
            
            .avatar-initials {
                color: var(--org-navy);
                font-weight: 800;
                font-size: 0.75rem;
            }

            .node-content {
                min-width: 0;
                flex: 1;
            }

            .node-title-row {
                display: flex;
                align-items: center;
                gap: 0.5rem;
                min-width: 0;
            }

            .node-title-row .node-name {
                overflow-wrap: anywhere;
            }

            .node-type-pill {
                color: var(--org-navy);
                background: #f8fafc;
                border: 1px solid var(--org-border);
                border-radius: 999px;
                display: inline-flex;
                align-items: center;
                min-height: 1.375rem;
                padding: 0.125rem 0.5rem;
                font-size: 0.625rem;
                font-weight: 800;
                text-transform: uppercase;
                white-space: nowrap;
                flex: none;
            }

            .node-actions {
                display: flex;
                align-items: center;
                gap: 0.375rem;
                flex: none;
            }

            .node-content {
                width: 2rem;
                height: 2rem;
                display: grid;
                place-items: center;
                border-radius: 0.5rem;
                transition: all 0.2s;
                background: #f8fafc;
                color: var(--org-muted);
                border: 1px solid transparent;
                cursor: pointer;
            }

            .node-action-btn:hover {
                background: color-mix(in srgb, var(--org-navy) 8%, white);
                color: var(--org-navy);
                border-color: color-mix(in srgb, var(--org-navy) 18%, var(--org-border));
            }

            .node-action-btn.btn-danger:hover {
                background: #fef2f2;
                color: #b91c1c;
                border-color: #fecaca;
            }

            .org-empty-state {
                border: 1px dashed var(--org-border);
                border-radius: 0.875rem;
                padding: 3rem 1rem;
                color: var(--org-muted);
                text-align: center;
                background: #fff;
            }

            .org-empty-state-icon {
                width: 3rem;
                height: 3rem;
                border-radius: 0.875rem;
                display: grid;
                place-items: center;
                margin: 0 auto 1rem;
                color: var(--org-navy);
                background: color-mix(in srgb, var(--org-accent) 14%, white);
            }

            .org-empty-state h3 {
                color: var(--org-navy);
                font-size: 0.9375rem;
                font-weight: 800;
                margin: 0;
            }

            .org-empty-state p {
                margin: 0.35rem 0 0;
                font-size: 0.8125rem;
            }

            .org-btn-primary {
                background-color: var(--org-navy);
                color: white !important;
                min-height: 2.5rem;
                padding: 0.625rem 1rem;
                border-radius: 0.625rem;
                font-weight: 800;
                font-size: 0.8125rem;
                border: none;
                cursor: pointer;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 0.5rem;
                box-shadow: 0 1px 2px rgba(15, 23, 42, 0.12);
                transition: all 0.2s ease;
            }

            .org-btn-primary:hover {
                background-color: var(--org-navy-soft);
                transform: translateY(-1px);
            }

            .org-btn-primary:disabled {
                cursor: not-allowed;
                opacity: 0.65;
                transform: none;
            }
            
            .org-icon-sm { width: 1.25rem !important; height: 1.25rem !important; }
            .org-icon-md { width: 1.5rem !important; height: 1.5rem !important; }
            .org-icon-lg { width: 2rem !important; height: 2rem !important; }

            .org-chevron {
                transition: transform 0.2s ease;
                color: var(--org-muted);
            }
            .org-chevron-open { transform: rotate(0deg); }
            .org-chevron-closed { transform: rotate(-90deg); }

            .node-action-btn {
                width: 2rem;
                height: 2rem;
                display: grid;
                place-items: center;
                border-radius: 0.5rem;
                transition: all 0.15s ease;
                background: #f8fafc;
                color: var(--org-muted);
                border: 1px solid transparent;
                cursor: pointer;
            }

            .node-action-btn:hover {
                background: color-mix(in srgb, var(--org-navy) 8%, white);
                color: var(--org-navy);
                border-color: color-mix(in srgb, var(--org-navy) 18%, var(--org-border));
            }

            .node-action-btn.btn-danger:hover {
                background: #fef2f2;
                color: #dc2626;
                border-color: #fecaca;
            }

            .node-child-count {
                background: color-mix(in srgb, var(--org-accent) 12%, white);
                color: var(--org-navy);
                font-size: 0.6875rem;
                font-weight: 800;
                min-width: 1.5rem;
                height: 1.5rem;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                border-radius: 999px;
                padding: 0 0.375rem;
                cursor: pointer;
                flex-shrink: 0;
            }

            .node-actions {
                display: flex;
                align-items: center;
                gap: 0.25rem;
                flex-shrink: 0;
                opacity: 0;
                transition: opacity 0.15s ease;
            }

            .node-card:hover .node-actions {
                opacity: 1;
            }

            .sortable-ghost .node-card {
                border-color: var(--org-accent);
                background: color-mix(in srgb, var(--org-accent) 10%, white);
            }

            @media (max-width: 768px) {
                .org-chart-toolbar,
                .org-chart-stats {
                    grid-template-columns: 1fr;
                }

                .org-chart-toolbar {
                    align-items: stretch;
                }

                .org-chart-tip { max-width: none; text-align: left; }

                .org-chart-search {
                    width: 100%;
                    margin-right: 0;
                }

                .kimmex-org-chart {
                    min-height: 34rem;
                }

                .node-card {
                    align-items: flex-start;
                    flex-wrap: wrap;
                }

                .node-actions {
                    width: 100%;
                    justify-content: flex-end;
                    padding-left: 5.25rem;
                }

                .node-children {
                    margin-left: 1rem;
                    padding-left: 1rem;
                }
            }
        </style>

        <div class="org-chart-wrapper"
             data-org-chart
             data-chart-data="{{ json_encode($chartData, JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_HEX_TAG) }}"
             x-data
             x-on:org-chart:edit.window="$wire.mountAction('edit', $event.detail)">
            <div class="org-chart-toolbar">
                <div class="org-chart-title">
                    <div class="org-chart-title-icon">
                        <x-heroicon-o-presentation-chart-line class="org-icon-md" />
                    </div>
                    <div>
                        <h2>{{ __('Organization Chart') }}</h2>
                        <p>{{ __('Browse the team structure and click any card to edit it.') }}</p>
                        <span class="org-chart-mode">{{ __('Interactive builder') }}</span>
                    </div>
                </div>
                <span class="org-chart-tip">{{ __('Search a person or role, then use the chart controls to focus on the hierarchy.') }}</span>
            </div>

            <div class="org-chart-stats">
                <div class="org-stat">
                    <span class="org-stat-value">{{ $rootCount }}</span>
                    <span class="org-stat-label">{{ __('Root Units') }}</span>
                </div>
                <div class="org-stat">
                    <span class="org-stat-value">{{ $unitCount }}</span>
                    <span class="org-stat-label">{{ __('Total Units') }}</span>
                </div>
                <div class="org-stat">
                    <span class="org-stat-value">{{ $depthCount }}</span>
                    <span class="org-stat-label">{{ __('Levels') }}</span>
                </div>
            </div>

            <div class="org-chart-settings">
                <details class="org-chart-settings-card">
                    <summary>{{ __('Website display settings') }}</summary>
                    <form wire:submit="saveDisplaySettings" class="org-chart-settings-body">
                        <p>{{ __('Choose whether the public About page shows this interactive chart, an image, or a PDF.') }}</p>
                        {{ $this->form }}
                        <div class="mt-5 flex justify-end">
                            <button type="submit" class="org-btn-primary" wire:loading.attr="disabled" wire:target="saveDisplaySettings">
                                <x-heroicon-o-check class="org-icon-sm" />
                                {{ __('Save display') }}
                            </button>
                        </div>
                    </form>
                </details>
            </div>

            <div class="org-chart-board">
                @if(empty($chartData))
                    <div class="org-empty-state">
                        <div class="org-empty-state-icon">
                            <x-heroicon-o-building-office-2 class="org-icon-lg" />
                        </div>
                        <h3>{{ __('No organizational units yet') }}</h3>
                        <p>{{ __('Use Add Root Unit above to start building the hierarchy.') }}</p>
                    </div>
                @else
                    <div class="org-chart-controls">
                        <label class="sr-only" for="org-chart-search">{{ __('Search organization chart') }}</label>
                        <input id="org-chart-search" type="search" class="org-chart-search" data-org-chart-search placeholder="{{ __('Search people or positions') }}" />
                        <span class="org-chart-search-status" data-org-chart-search-status></span>
                        <button type="button" class="org-chart-control" data-org-chart-action="fit"><x-heroicon-o-arrows-pointing-in class="org-icon-sm" />{{ __('Fit') }}</button>
                        <button type="button" class="org-chart-control" data-org-chart-action="expand"><x-heroicon-o-plus class="org-icon-sm" />{{ __('Expand') }}</button>
                        <button type="button" class="org-chart-control" data-org-chart-action="collapse"><x-heroicon-o-minus class="org-icon-sm" />{{ __('Collapse') }}</button>
                        <button type="button" class="org-chart-control" data-org-chart-action="fullscreen"><x-heroicon-o-arrows-pointing-out class="org-icon-sm" />{{ __('Fullscreen') }}</button>
                        <button type="button" class="org-chart-control" data-org-chart-action="download"><x-heroicon-o-arrow-down-tray class="org-icon-sm" />{{ __('PNG') }}</button>
                    </div>
                    <div class="kimmex-org-chart" data-org-chart-canvas wire:ignore></div>
                @endif
            </div>
        </div>
    </div>
    @vite('resources/js/admin-org-chart.js')
</x-filament-panels::page>
