@props(['orgChart', 'cardStyle' => 'floating'])

@if(!empty($orgChart))
<div x-data="{
        scale: 1,
        naturalWidth: 0,
        naturalHeight: 0,

        updateFit() {
            const vp = this.$refs.viewport;
            const root = this.$refs.treeRoot;
            if (!vp || !root) return;

            // Capture true unscaled dimensions
            const w = Math.max(root.scrollWidth || 0, root.offsetWidth || 0);
            const h = Math.max(root.scrollHeight || 0, root.offsetHeight || 0);
            if (w > this.naturalWidth) this.naturalWidth = w;
            if (h > this.naturalHeight) this.naturalHeight = h;

            const baseW = this.naturalWidth || w;
            const pad = window.innerWidth < 640 ? 16 : 32;
            const available = vp.clientWidth - pad;

            if (baseW > 0 && available > 0) {
                if (baseW > available) {
                    // Exact scale to fit viewport width with zero horizontal scroll
                    this.scale = +(available / baseW).toFixed(4);
                } else {
                    this.scale = 1;
                }
            }
        },

        init() {
            this.$nextTick(() => {
                this.updateFit();
                setTimeout(() => this.updateFit(), 60);
                setTimeout(() => this.updateFit(), 200);
            });

            if (window.ResizeObserver) {
                const ro = new ResizeObserver(() => this.updateFit());
                ro.observe(this.$refs.viewport);
            }
            window.addEventListener('resize', () => this.updateFit());
        }
    }"
    class="relative w-full flex justify-center py-2">

    {{-- Seamless Auto-Fit Flowchart Container with Zero Horizontal Scrollbar --}}
    <div x-ref="viewport"
         class="org-tree-viewport w-full overflow-hidden rounded-2xl border border-slate-200/90 bg-gradient-to-b from-slate-50/50 via-white to-slate-50/30 py-8 px-2 sm:px-4 shadow-xs flex justify-center items-start">
        <div class="org-tree-canvas flex justify-center items-start overflow-visible transition-all duration-200"
             :style="naturalHeight > 0 && scale < 1 ? 'height: ' + Math.ceil(naturalHeight * scale) + 'px;' : ''">
            <div :style="'transform: scale(' + scale + '); transform-origin: top center; transition: transform 0.2s ease-out;'"
                 class="w-max shrink-0 flex flex-col items-center justify-start text-center">
                <ul x-ref="treeRoot"
                    class="org-tree-root !p-0 !m-0 !list-none inline-flex justify-center items-start mx-auto w-max">
                    @include('components.about.tree-node', ['node' => $orgChart, 'level' => 0, 'cardStyle' => $cardStyle])
                </ul>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Scoped Styles for Crisp Organizational Tree Lines --}}
<style>
/* Disable horizontal scrolling completely for a clean, non-scrolling fit */
.org-tree-viewport {
    overflow-x: hidden !important;
    overflow-y: hidden !important;
}

/* Tree Structure Lists */
.org-tree-root,
.org-tree-children {
    display: flex !important;
    flex-wrap: nowrap !important;
    justify-content: center !important;
    align-items: flex-start !important;
    padding: 0 !important;
    margin: 0 auto !important;
    list-style: none !important;
    position: relative !important;
    width: max-content !important;
}

.org-tree-children {
    padding-top: 16px !important;
}

/* Compact spacing between items to fit screen smoothly */
.org-tree-item {
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
    flex-shrink: 0 !important;
    position: relative !important;
    padding: 16px 6px 0 6px !important;
    list-style: none !important;
}

/* Card wrappers always sit above connector lines */
.org-card-wrapper {
    position: relative !important;
    z-index: 2 !important;
}

/* Horizontal & Vertical Connector Lines on li items */
.org-tree-item::before,
.org-tree-item::after {
    content: '' !important;
    position: absolute !important;
    top: 0 !important;
    right: 50% !important;
    border-top: 2px solid #0B2B5C !important;
    width: 50% !important;
    height: 16px !important;
    z-index: 1 !important;
}

.org-tree-item::after {
    right: auto !important;
    left: 50% !important;
    border-left: 2px solid #0B2B5C !important;
}

/* Clean corner lines for first and last child in a multi-child row */
.org-tree-item:first-child:not(:only-child)::before {
    border: 0 none !important;
}
.org-tree-item:first-child:not(:only-child)::after {
    border-top-left-radius: 6px !important;
}
.org-tree-item:last-child:not(:only-child)::after {
    border: 0 none !important;
}
.org-tree-item:last-child:not(:only-child)::before {
    border-right: 2px solid #0B2B5C !important;
    border-top-right-radius: 6px !important;
}

/* Single child items (straight continuous line down, no horizontal bar) */
.org-tree-item:only-child::before {
    display: none !important;
}
.org-tree-item:only-child {
    padding-top: 16px !important;
}
.org-tree-item:only-child::after {
    display: block !important;
    content: '' !important;
    position: absolute !important;
    top: 0 !important;
    left: 50% !important;
    border: none !important;
    border-left: 2px solid #0B2B5C !important;
    width: 0 !important;
    height: 16px !important;
    transform: translateX(-50%) !important;
    z-index: 1 !important;
}

/* Root item should have NO top connectors */
.org-tree-root > .org-tree-item {
    padding-top: 0 !important;
}
.org-tree-root > .org-tree-item::before,
.org-tree-root > .org-tree-item::after {
    display: none !important;
}

/* Vertical line coming out from the bottom of parent card */
.org-card-wrapper.has-children::after {
    content: '' !important;
    position: absolute !important;
    bottom: -16px !important;
    left: 50% !important;
    border-left: 2px solid #0B2B5C !important;
    width: 0 !important;
    height: 16px !important;
    transform: translateX(-50%) !important;
    z-index: 1 !important;
}
</style>
