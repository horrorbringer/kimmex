<x-filament-panels::page>
    <div class="org-chart-wrapper" x-data="orgChartManager()" @node-added.window="handleNodeAdded($event.detail)">
        <style>
            .org-chart-wrapper {
                max-width: 1200px;
                margin: 0 auto;
                font-family: 'Inter', sans-serif, system-ui;
            }

            .page-subtitle {
                margin-top: -1.75rem;
                margin-bottom: 2.5rem;
                font-size: 0.875rem;
                color: #6b7280;
                font-weight: 500;
            }

            .view-switcher {
                display: flex;
                gap: 0.5rem;
                margin-bottom: 2.5rem;
                background: #f1f5f9;
                padding: 0.25rem;
                border-radius: 9999px;
                width: fit-content;
                border: 1px solid #e2e8f0;
            }

            .switcher-btn {
                padding: 0.6rem 1.75rem;
                border-radius: 9999px;
                font-size: 13px;
                font-weight: 800;
                transition: all 300ms ease;
                cursor: pointer;
                color: #64748b;
                text-transform: uppercase;
                letter-spacing: 0.05em;
            }

            .switcher-btn.active {
                background: white;
                color: #be1e2d;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            }

            .hierarchy-card {
                background: white;
                border: 1px solid #e5e7eb;
                border-radius: 2rem;
                box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.04);
                overflow: hidden;
                min-height: 500px;
                margin-bottom: 2.5rem;
                position: relative;
            }

            .section-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 2.5rem 2.5rem 1.5rem;
                border-bottom: 1px solid #f8fafc;
            }

            .section-label {
                font-size: 11px;
                font-weight: 900;
                text-transform: uppercase;
                letter-spacing: 0.3em;
                color: #94a3b8;
                display: flex;
                align-items: center;
                gap: 0.875rem;
            }

            .tree-container {
                padding: 1.5rem 2.5rem 4rem;
            }

            .node-card {
                display: flex;
                align-items: center;
                gap: 1.25rem;
                padding: 1.25rem 2rem;
                border: 1px solid #f1f5f9;
                border-radius: 1.5rem;
                background: white;
                transition: all 300ms ease;
                margin-top: 1rem;
                position: relative;
                cursor: pointer;
            }

            .node-card:hover {
                border-color: #e2e8f0;
                box-shadow: 0 10px 20px -5px rgba(0, 0, 0, 0.04);
            }

            .level-0>.node-card,
            .level-1>.node-card {
                border-radius: 1.5rem;
            }

            .level-0 .node-avatar,
            .level-1 .node-avatar {
                width: 3.5rem;
                height: 3.5rem;
                border-radius: 9999px;
                border: 3px solid white;
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            }

            .level-deep>.node-card {
                border-radius: 1.25rem;
                border: 1.5px solid #f1f5f9;
                padding: 1.125rem 2rem;
            }

            .level-deep .node-avatar {
                width: 2.75rem;
                height: 2.75rem;
                border-radius: 0.75rem;
                background: #f8fafc;
                overflow: hidden;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .level-deep .node-name {
                font-size: 13px !important;
                letter-spacing: 0.05em;
                font-weight: 800;
                color: #1e293b;
            }

            .node-avatar {
                flex-shrink: 0;
                display: flex;
                align-items: center;
                justify-content: center;
                overflow: hidden;
                background: #f8fafc;
            }

            .node-name {
                font-size: 0.9375rem;
                font-weight: 800;
                color: #1e293b;
                text-transform: uppercase;
                margin: 0;
            }

            .node-role-list {
                font-size: 13px;
                font-style: italic;
                color: #94a3b8;
                font-weight: 600;
                text-align: right;
                min-width: 150px;
            }

            .drag-handle {
                color: #e2e8f0;
                cursor: grab;
                padding: 0.5rem;
                margin-left: -1rem;
            }

            .drag-handle:hover {
                color: #be1e2d;
            }

            .children-nested {
                margin-left: 4.5rem;
                padding-left: 2rem;
                border-left: 2px solid #f9fafb;
                min-height: 1px;
            }

            .card-actions {
                display: flex;
                gap: 0.25rem;
                margin-left: 1rem;
            }

            .action-btn {
                padding: 0.5rem;
                border-radius: 0.6rem;
                color: #94a3b8;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                transition: all 200ms;
            }

            .action-btn:hover {
                background: #f1f5f9;
                color: #be1e2d;
            }

            .action-btn.primary {
                color: #be1e2d;
                background: #fff5f5;
                border: 1px solid #fee2e2;
            }

            .action-btn.delete:hover {
                color: #f43f5e;
                background: #fff1f2;
            }

            .processing-overlay {
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(255, 255, 255, 0.7);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 50;
                backdrop-filter: blur(2px);
                border-radius: inherit;
            }

            .visual-preview {
                display: flex;
                flex-direction: column;
                align-items: center;
                padding: 5rem 3rem;
                background: #fdfdfd;
                background-image: radial-gradient(#e5e7eb 1px, transparent 1px);
                background-size: 30px 30px;
                overflow-x: auto;
            }

            .viz-node {
                display: flex;
                flex-direction: column;
                align-items: center;
                position: relative;
            }

            .viz-card {
                background: white;
                border: 1px solid #e5e7eb;
                border-radius: 1.25rem;
                padding: 1.5rem;
                text-align: center;
                width: 240px;
                box-shadow: 0 15px 25px -5px rgba(0, 0, 0, 0.05);
                position: relative;
                z-index: 10;
            }

            .viz-avatar {
                width: 5.5rem;
                height: 5.5rem;
                border-radius: 9999px;
                margin: 0 auto 1.5rem;
                border: 3px solid white;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
                overflow: hidden;
                background: #f8fafc;
            }

            .viz-role-badge {
                background: #be1e2d;
                color: white;
                display: inline-block;
                padding: 0.35rem 1rem;
                border-radius: 9999px;
                font-size: 10px;
                font-weight: 800;
                text-transform: uppercase;
                margin-bottom: 0.625rem;
            }

            .viz-name {
                font-size: 13px;
                font-weight: 900;
                color: #1e293b;
                text-transform: uppercase;
                margin: 0;
            }

            .viz-children {
                display: flex;
                gap: 4rem;
                position: relative;
                padding-top: 4rem;
            }

            .viz-children::before {
                content: "";
                position: absolute;
                top: 0;
                left: 50%;
                width: 2px;
                height: 4rem;
                background: #D1D5DB;
            }

            .viz-joint {
                position: absolute;
                top: -1rem;
                left: calc(50% - 0.5rem);
                width: 1rem;
                height: 1rem;
                background: #f97316;
                border: 2px solid white;
                border-radius: 9999px;
                z-index: 20;
            }

            [x-cloak] {
                display: none !important;
            }
        </style>

        <div class="flex items-center justify-between">
            <p class="page-subtitle">{{ __('Organizational management with real-time sync.') }}</p>
            <div class="view-switcher">
                <div class="switcher-btn" :class="viewMode === 'editor' ? 'active' : ''" @click="viewMode = 'editor'">
                    {{ __('Management') }}</div>
                <div class="switcher-btn" :class="viewMode === 'preview' ? 'active' : ''" @click="viewMode = 'preview'">
                    {{ __('Live Preview') }}</div>
            </div>
        </div>

        <div class="hierarchy-card">
            <!-- Loading Indicator -->
            <div wire:loading class="processing-overlay">
                <div class="flex flex-col items-center gap-4">
                    <div class="w-12 h-12 border-4 border-[#be1e2d] border-t-transparent rounded-full animate-spin">
                    </div>
                    <span
                        class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">{{ __('Syncing Hierarchy...') }}</span>
                </div>
            </div>

            <div class="section-header">
                <span class="section-label">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                        <circle cx="11" cy="11" r="8" />
                        <path d="m21 21-4.3-4.3" />
                    </svg>
                    <span x-text="viewMode === 'editor' ? 'DATA ENTRY' : 'DESIGN PREVIEW'"></span>
                </span>

                <button x-show="viewMode === 'editor'" type="button" @click="saveOrder()"
                    class="bg-[#be1e2d] text-white px-6 py-2 rounded-full text-[11px] font-bold uppercase tracking-widest hover:bg-black transition-all shadow-lg hover:shadow-[#be1e2d]/20">
                    {{ __('Save Display Order') }}
                </button>
            </div>

            <div x-show="viewMode === 'editor'" x-transition class="tree-container">
                <div id="root-sortable" class="sortable-list">
                    @include('filament.pages.org-chart-node', ['nodes' => $tree, 'level' => 0, 'parentId' => null])
                </div>
            </div>

            <div x-show="viewMode === 'preview'" x-transition class="visual-preview">
                <template x-for="node in tree" :key="node.id">
                    <div class="viz-node" :class="node.children && node.children.length ? 'has-children' : ''">
                        <div class="viz-card">
                            <div class="viz-avatar"><template x-if="node.image"><img :src="node.image"
                                        onerror="this.src='https://ui-avatars.com/api/?name=User&background=f8fafc&color=cbd5e1'" /></template>
                            </div>
                            <div class="viz-role-badge" x-text="node.role"></div>
                            <h4 class="viz-name" x-text="node.name"></h4>
                        </div>
                        <div x-show="node.children && node.children.length" class="viz-children">
                            <div class="viz-joint"></div>
                            <template x-for="child in node.children" :key="child.id">
                                <div class="viz-node"
                                    :class="child.children && child.children.length ? 'has-children' : ''">
                                    <div class="viz-card" style="width: 210px;">
                                        <div class="viz-avatar" style="width: 4.5rem; height: 4.5rem;"><template
                                                x-if="child.image"><img :src="child.image"
                                                    onerror="this.src='https://ui-avatars.com/api/?name=User&background=f8fafc&color=cbd5e1'" /></template>
                                        </div>
                                        <div class="viz-role-badge" x-text="child.role"></div>
                                        <h4 class="viz-name" style="font-size: 12px;" x-text="child.name"></h4>
                                    </div>
                                    <div x-show="child.children && child.children.length" class="viz-children">
                                        <div class="viz-joint"></div>
                                        <template x-for="grand in child.children" :key="grand.id">
                                            <div class="viz-node">
                                                <div class="viz-card" style="width: 180px; padding: 1rem;">
                                                    <div class="viz-avatar" style="width: 3.5rem; height: 3.5rem;">
                                                        <template x-if="grand.image"><img :src="grand.image"
                                                                onerror="this.src='https://ui-avatars.com/api/?name=User&background=f8fafc&color=cbd5e1'" /></template>
                                                    </div>
                                                    <div class="viz-role-badge" x-text="grand.role"
                                                        style="font-size: 8px; padding: 0.2rem 0.6rem;"></div>
                                                    <h4 class="viz-name" style="font-size: 11px;" x-text="grand.name">
                                                    </h4>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </div>
        <x-filament-actions::modals />
    </div>

    @once
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
        <script>
            function orgChartManager() {
                return {
                    tree: @entangle('tree'),
                    expanded: {},
                    viewMode: 'editor',

                    init() {
                        this.$nextTick(() => this.initSortable());
                        this.$watch('tree', () => this.$nextTick(() => this.initSortable()));
                        window.addEventListener('node-added', (e) => {
                            if (e.detail.parentId) { this.expanded[e.detail.parentId] = true; }
                        });
                    },

                    initSortable() {
                        document.querySelectorAll('.sortable-list').forEach(el => {
                            Sortable.create(el, { group: 'nested', animation: 300, handle: '.drag-handle' });
                        });
                    },

                    saveOrder() {
                        const gather = (el) => {
                            return Array.from(el.children).map(i => {
                                const nest = i.querySelector('.children-nested');
                                return { id: i.dataset.id, children: nest ? gather(nest) : [] };
                            });
                        };
                        const data = gather(document.getElementById('root-sortable'));
                        this.$wire.updateHierarchy(data);
                    }
                }
            }
        </script>
    @endonce
</x-filament-panels::page>