<x-filament-panels::page>
    <div class="space-y-8">
        <style>
            :root {
                --titan-red: #E31E24;
                --titan-red-hover: #C2191F;
                --titan-navy: #1a1a2e;
                --titan-navy-light: #2D3E5D;
                --titan-gray: #6B7280;
                --card-bg: #ffffff;
                --tab-bg: #f1f5f9;
            }

            /* Container & Layout */
            .org-chart-wrapper { background: #fcfcfc; padding: 2rem; border-radius: 2.5rem; border: 1px solid #f1f5f9; }
            
            /* Generic Helper Classes (since TW utilities might be purged) */
            .org-flex { display: flex; align-items: center; }
            .org-justify-between { justify-content: space-between; }
            .org-mb-8 { margin-bottom: 2rem; }
            .org-rounded-xl { border-radius: 0.75rem; }
            .org-shadow-lg { box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); }
            
            /* Card Styling */
            .node-card {
                background: white;
                border: 1px solid #f1f5f9;
                border-radius: 1.25rem;
                padding: 1.25rem;
                margin-bottom: 0.75rem;
                display: flex;
                align-items: center;
                box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
                transition: all 0.3s;
            }
            
            .node-card:hover {
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
                transform: translateX(4px);
            }

            .node-name {
                color: var(--titan-navy);
                font-weight: 900;
                text-transform: uppercase;
                font-size: 0.8rem;
                margin: 0 !important;
                letter-spacing: -0.01em;
            }

            .node-role {
                color: #94a3b8;
                font-size: 0.6rem;
                font-weight: 600;
                font-style: italic;
                text-transform: uppercase;
                letter-spacing: 0.05em;
                margin: 0 !important;
                opacity: 0.8;
            }

            /* Avatar Circle Fixed */
            .avatar-circle {
                width: 3rem !important;
                height: 3rem !important;
                border-radius: 50% !important;
                object-fit: cover;
                border: 2px solid white;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
                flex-shrink: 0;
                display: flex;
                align-items: center;
                justify-content: center;
                background: #f8fafc;
                overflow: hidden;
                margin-right: 1.25rem;
            }
            
            .avatar-initials {
                color: #64748b;
                font-weight: 800;
                font-size: 0.75rem;
            }

            /* Tree Connections */
            .node-children {
                position: relative;
                margin-left: 3.5rem;
                padding-left: 1.5rem;
            }

            .node-children::before {
                content: '';
                position: absolute;
                left: 0;
                top: -0.75rem;
                bottom: 1.75rem; /* Terminates at the last child's hook */
                width: 2px;
                border-left: 2px dashed #e2e8f0;
            }

            /* Horizontal Hook for children */
            .node-card { position: relative; }
            .node-children .node-card::before {
                content: '';
                position: absolute;
                left: -1.5rem;
                top: 50%;
                width: 1.5rem;
                height: 2px;
                border-top: 2px dashed #e2e8f0;
                transform: translateY(-50%);
            }
            
            /* Action Buttons */
            .node-action-btn {
                padding: 0.5rem;
                border-radius: 0.75rem;
                transition: all 0.2s;
                background: #f8fafc;
                color: #94a3b8;
                border: none;
                cursor: pointer;
            }
            .node-action-btn:hover { background: #f1f5f9; color: var(--titan-navy); }
            .node-action-btn.btn-red:hover { background: #fee2e2; color: var(--titan-red); }
            
            /* Icon Sizing (Bypass TW Purge) */
            .org-icon-sm { width: 1.25rem !important; height: 1.25rem !important; }
            .org-icon-md { width: 1.5rem !important; height: 1.5rem !important; }
            .org-icon-lg { width: 2rem !important; height: 2rem !important; }
            
            /* Action Buttons Header */
            .org-btn-primary {
                background-color: var(--titan-navy);
                color: white !important;
                padding: 0.75rem 2rem;
                border-radius: 0.75rem;
                font-weight: 700;
                font-size: 0.875rem;
                border: none;
                cursor: pointer;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
                transition: all 0.2s;
            }
            .org-btn-primary:hover { opacity: 0.9; transform: translateY(-1px); }
        </style>

        <div class="org-chart-wrapper">
            <!-- Header -->
            <div class="flex justify-between items-center mb-12 border-b border-gray-50 pb-8">
                <div class="flex items-center gap-4 text-gray-400">
                    <x-heroicon-o-magnifying-glass class="org-icon-md" />
                    <span class="font-black uppercase tracking-[0.3em] text-xs">{{ __('MANAGEMENT') }}</span>
                </div>
                <button 
                    onclick="triggerSave()" 
                    class="org-btn-primary flex items-center gap-2"
                    id="save-btn"
                >
                    <span id="save-btn-text">{{ __('Save Display Order') }}</span>
                    <div id="save-spinner" class="hidden animate-spin w-4 h-4 border-2 border-white border-t-transparent rounded-full"></div>
                </button>
            </div>

            <!-- Tree List -->
            <div class="max-w-4xl mx-auto space-y-4" x-ref="treeRoot">
                @foreach($chartData as $node)
                    <x-org.chart-node :node="$node" />
                @endforeach
            </div>

            @push('scripts')
                <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
                <script>
                    document.addEventListener('livewire:load', function () {
                        const initAll = () => {
                            initSortable(document.querySelector('[x-ref="treeRoot"]'));
                            document.querySelectorAll('.children-container').forEach(initSortable);
                        };

                        const initSortable = (el) => {
                            if (!el || el.sortable) return;
                            el.sortable = new Sortable(el, {
                                group: 'nested',
                                animation: 150,
                                fallbackOnBody: true,
                                swapThreshold: 0.65,
                                handle: '.cursor-grab',
                                ghostClass: 'bg-titan-navy/5',
                                onEnd: function (evt) {
                                    // Optional: Mark as dirty
                                }
                            });
                        };

                        const serializeTree = (root) => {
                            if (!root) return [];
                            return Array.from(root.children)
                                .filter(el => el.hasAttribute('data-id'))
                                .map(el => {
                                    const id = el.getAttribute('data-id');
                                    const childContainer = el.querySelector('.children-container');
                                    return {
                                        id: id,
                                        children: childContainer ? serializeTree(childContainer) : []
                                    };
                                });
                        };

                        window.triggerSave = function() {
                            const btn = document.getElementById('save-btn');
                            const text = document.getElementById('save-btn-text');
                            const spinner = document.getElementById('save-spinner');

                            btn.disabled = true;
                            text.innerText = "{{ __('Saving...') }}";
                            spinner.classList.remove('hidden');

                            let data = serializeTree(document.querySelector('[x-ref="treeRoot"]'));
                            @this.call('saveOrder', data).then(() => {
                                btn.disabled = false;
                                text.innerText = "{{ __('Save Display Order') }}";
                                spinner.classList.add('hidden');
                            });
                        };

                        initAll();

                        // Re-init after Livewire updates
                        Livewire.on('chartUpdated', () => {
                            setTimeout(initAll, 100);
                        });
                    });
                </script>
            @endpush

            @if(empty($chartData))
                <div class="text-center py-24 text-titan-navy/30 italic">
                    {{ __('No organizational units found. Start by adding a root node.') }}
                </div>
            @endif
        </div>
    </div>
</x-filament-panels::page>