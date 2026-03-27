<x-filament-panels::page>
    <div class="space-y-12">
        <!-- HEADER DESCRIPTION -->
        <div class="flex flex-col gap-2">
            <h2 class="text-2xl font-black text-titan-navy tracking-tight">
                {{ __('Org Chart Management') }}
            </h2>
            <p class="text-sm text-titan-navy/50 font-medium leading-relaxed">
                {{ __('Manage the hierarchical structure of Kimmex Construction.') }}
            </p>
        </div>

        <!-- HIERARCHY EDITOR SECTION -->
        <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-12 relative overflow-hidden">
            <div class="flex items-center gap-3 mb-10 opacity-30">
                <x-lucide-search class="w-4 h-4" />
                <span class="text-[10px] font-black uppercase tracking-[0.2em]">{{ __('HIERARCHY EDITOR') }}</span>
            </div>

            <!-- TREE CONTAINER -->
            <div x-data="orgTreeEditor({ tree: @entangle('tree') })" class="space-y-6">
                <!-- ROOT NODES -->
                <div x-ref="root" class="space-y-4">
                    <template x-for="(node, index) in tree" :key="node.id">
                        <div :data-id="node.id" class="group/node">
                            <!-- NODE CARD -->
                            <div
                                class="flex items-center gap-6 p-6 bg-white border border-gray-100 rounded-[1.5rem] shadow-sm hover:shadow-xl hover:border-titan-red/20 transition-all duration-300 relative z-10">
                                <div
                                    class="w-2 h-10 flex flex-col gap-1 items-center justify-center opacity-0 group-hover/node:opacity-30 cursor-grab active:cursor-grabbing">
                                    <div class="w-1 h-1 rounded-full bg-titan-navy"></div>
                                    <div class="w-1 h-1 rounded-full bg-titan-navy"></div>
                                    <div class="w-1 h-1 rounded-full bg-titan-navy"></div>
                                </div>

                                <div
                                    class="w-12 h-12 rounded-full overflow-hidden shrink-0 border-2 border-white shadow-lg shadow-gray-200">
                                    <template x-if="node.image">
                                        <img :src="node.image" class="w-full h-full object-cover" />
                                    </template>
                                    <template x-if="!node.image">
                                        <div
                                            class="w-full h-full bg-titan-navy flex items-center justify-center text-white/20">
                                            <x-lucide-user class="w-6 h-6" />
                                        </div>
                                    </template>
                                </div>

                                <div class="flex flex-col min-w-0 flex-1">
                                    <h4 x-text="node.name"
                                        class="text-sm font-black text-titan-navy truncate tracking-tight uppercase">
                                    </h4>
                                    <span x-text="node.role"
                                        class="text-[9px] font-black italic text-titan-red/60 uppercase tracking-widest mt-1 truncate"></span>
                                </div>

                                <div class="text-[9px] font-bold text-gray-300 italic px-4">
                                    <span x-text="node.role"></span>
                                </div>

                                <button
                                    class="p-2 rounded-xl hover:bg-gray-50 opacity-0 group-hover/node:opacity-100 transition-opacity">
                                    <x-lucide-chevron-right class="w-4 h-4 text-titan-navy" />
                                </button>
                            </div>

                            <!-- CHILDREN (Recursive) -->
                            <div class="ml-16 mt-4 pl-12 border-l border-gray-100 space-y-4 child-container"
                                :data-parent="node.id">
                                <template x-for="(child, cIdx) in node.children" :key="child.id">
                                    <div :data-id="child.id" class="group/child">
                                        <div
                                            class="flex items-center gap-6 p-4 bg-gray-50 border border-gray-100 rounded-[1.2rem] shadow-sm hover:shadow-md hover:border-titan-red/10 transition-all duration-300 cursor-grab active:cursor-grabbing">
                                            <div class="w-10 h-10 rounded-full overflow-hidden shrink-0">
                                                <template x-if="child.image">
                                                    <img :src="child.image" class="w-full h-full object-cover" />
                                                </template>
                                                <template x-if="!child.image">
                                                    <div
                                                        class="w-full h-full bg-titan-navy/10 flex items-center justify-center text-titan-navy/40">
                                                        <x-lucide-user class="w-5 h-5" />
                                                    </div>
                                                </template>
                                            </div>
                                            <div class="flex flex-col min-w-0 flex-1">
                                                <h5 x-text="child.name"
                                                    class="text-xs font-black text-titan-navy truncate lowercase first-letter:uppercase">
                                                </h5>
                                                <span x-text="child.role"
                                                    class="text-[8px] font-bold text-titan-navy/40 truncate italic mt-0.5"></span>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                                <div x-show="!node.children || node.children.length === 0"
                                    class="flex items-center gap-2 p-3 rounded-xl border border-dashed border-gray-100">
                                    <span
                                        class="text-[8px] font-bold uppercase tracking-widest text-gray-300 italic">{{ __('Drop units here to assign them...') }}</span>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- EDITOR CONTROLS -->
        <div
            class="bg-titan-navy rounded-[1.5rem] p-10 flex flex-col md:flex-row items-center justify-between gap-12 shadow-2xl relative overflow-hidden">
            <!-- Glass Decorative -->
            <div class="absolute -right-20 -top-20 w-80 h-80 bg-white/5 rounded-full blur-3xl pointer-events-none">
            </div>

            <div class="flex items-center gap-8 relative z-10">
                <div
                    class="w-16 h-16 rounded-[1.2rem] bg-white/5 flex items-center justify-center border border-white/10 shrink-0">
                    <x-lucide-shield class="w-8 h-8 text-titan-red" />
                </div>
                <div class="flex flex-col gap-2">
                    <h3 class="text-xl font-black text-white tracking-tight">
                        {{ __('Editor Controls') }}
                    </h3>
                    <p class="text-sm text-white/40 leading-relaxed font-medium max-w-xl">
                        {{ __('This panel allows you to modify the core hierarchy of Kimmex. Changes are persistent and will be reflected on the live website immediately upon save.') }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-6 relative z-10">
                <div class="flex flex-col items-end gap-1">
                    <span class="text-3xl font-black text-white tracking-tighter italic">Live</span>
                    <span
                        class="text-[8px] font-black uppercase tracking-[0.3em] text-white/30">{{ __('STATUS') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- SORTABLE JS & SCRIPTS -->
    @once
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('orgTreeEditor', ({ tree }) => ({
                    tree: tree,
                    init() {
                        this.initSortable();
                    },
                    initSortable() {
                        const containers = document.querySelectorAll('.child-container, [x-ref="root"]');

                        containers.forEach(el => {
                            new Sortable(el, {
                                group: 'nested',
                                animation: 350,
                                fallbackOnBody: true,
                                swapThreshold: 0.65,
                                forceFallback: true,
                                ghostClass: 'opacity-40',
                                dragClass: 'shadow-2xl',
                                onEnd: (evt) => {
                                    // Normally I would rebuild the structure here and @this.updateHierarchy(...)
                                    console.log('Moved', evt.item, 'from', evt.from, 'to', evt.to);
                                }
                            });
                        });
                    }
                }));
            });
        </script>
    @endonce

    <style>
        .child-container {
            min-height: 40px;
        }
    </style>
</x-filament-panels::page>