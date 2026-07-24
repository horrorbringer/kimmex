@props(['node', 'level' => 0, 'small' => false, 'showChildren' => true])

@php
    $hasChildren = ! empty($node['children']);
    $isCEO = $level === 0;
    $isDepartment = ($node['type'] ?? '') === 'department';
    $childCount = $hasChildren ? count($node['children']) : 0;
    $canExpand = $hasChildren && $showChildren;

    $searchText = function (array $branch) use (&$searchText): string {
        $text = implode(' ', array_filter([
            $branch['name'] ?? null,
            $branch['role'] ?? null,
            $branch['type'] ?? null,
        ]));

        foreach ($branch['children'] ?? [] as $child) {
            $text .= ' '.$searchText($child);
        }

        return $text;
    };
@endphp

<div class="w-full"
    data-org-node
    data-org-search="{{ $searchText($node) }}"
    x-data="{
        open: {{ $isCEO ? 'true' : 'false' }},
        visible: true,
        filter(query) {
            const normalizedQuery = (query || '').trim().toLowerCase();
            const searchableText = (this.$el.dataset.orgSearch || '').toLowerCase();

            this.visible = normalizedQuery === '' || searchableText.includes(normalizedQuery);

            if (normalizedQuery !== '' && this.visible) {
                this.open = true;
            }
        }
    }"
    @org-search.window="filter($event.detail)"
    @org-expand-all.window="open = true"
    @org-collapse-all.window="open = {{ $isCEO ? 'true' : 'false' }}"
    x-show="visible">

    <div class="relative">
        <button type="button"
            class="w-full flex items-center gap-3 sm:gap-4 px-4 sm:px-5 py-3.5 sm:py-4 rounded-2xl border text-left transition-all duration-200
                {{ $isCEO ? 'bg-titan-navy border-titan-navy shadow-lg shadow-titan-navy/15' : ($isDepartment ? 'bg-titan-navy/[0.04] border-titan-navy/10 hover:border-titan-navy/30' : 'bg-white border-gray-100 shadow-sm hover:border-titan-red/30 hover:shadow-md') }}
                {{ $canExpand ? 'cursor-pointer' : (!$isDepartment ? 'cursor-pointer' : 'cursor-default') }}"
            @if($canExpand) @click="open = !open" @endif
            @if(! $hasChildren && ! $isDepartment) @click="$dispatch('select-member', {{ Js::from($node) }})" @endif
            @if($canExpand) :aria-expanded="open.toString()" @endif>

            @if(! $isDepartment)
                <div class="shrink-0 {{ $isCEO ? 'w-14 h-14 sm:w-16 sm:h-16' : 'w-11 h-11 sm:w-12 sm:h-12' }} rounded-full overflow-hidden border-2 {{ $isCEO ? 'border-titan-red/70' : 'border-gray-200' }} bg-gray-100">
                    @if(! empty($node['image']))
                        <img src="{{ $node['image'] }}" alt="{{ $node['name'] }}" class="w-full h-full object-cover object-top" decoding="async" loading="lazy" />
                    @else
                        <div class="w-full h-full flex items-center justify-center {{ $isCEO ? 'bg-white/10' : 'bg-gray-50' }}">
                            <x-lucide-user class="w-5 h-5 {{ $isCEO ? 'text-white/70' : 'text-gray-300' }}" />
                        </div>
                    @endif
                </div>
            @else
                <div class="shrink-0 w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-titan-navy/10 flex items-center justify-center">
                    <x-lucide-building-2 class="w-5 h-5 text-titan-navy/70" />
                </div>
            @endif

            <div class="flex-1 min-w-0">
                <div class="text-[9px] font-black uppercase tracking-[0.16em] mb-1 {{ $isCEO ? 'text-titan-red' : ($isDepartment ? 'text-titan-navy/50' : 'text-titan-red/70') }}">
                    {{ $isDepartment ? __('Department') : ($node['role'] ?? $node['type'] ?? '') }}
                </div>
                <div class="font-bold leading-snug {{ $isCEO ? 'text-white text-sm sm:text-base' : 'text-titan-navy text-xs sm:text-sm' }}">
                    {{ $node['name'] }}
                </div>
            </div>

            @if($canExpand)
                <div class="shrink-0 flex items-center gap-2">
                    <span class="hidden sm:inline-flex items-center rounded-full px-2.5 py-1 text-[10px] font-bold {{ $isCEO ? 'bg-white/10 text-white/80' : 'bg-titan-navy/5 text-titan-navy/60' }}">
                        {{ trans_choice('{0} No team|{1} :count member|[2,*] :count members', $childCount, ['count' => $childCount]) }}
                    </span>
                    <x-lucide-chevron-down class="w-4 h-4 transition-transform duration-200 {{ $isCEO ? 'text-white/70' : 'text-titan-navy/50' }}" ::class="open ? 'rotate-180' : ''" />
                </div>
            @endif
        </button>

        @if($canExpand)
            <div x-show="open" x-collapse class="ml-5 sm:ml-7 mt-2 pl-4 sm:pl-5 border-l-2 {{ $isCEO ? 'border-titan-red/40' : 'border-titan-navy/15' }} space-y-2.5 sm:space-y-3">
                @foreach($node['children'] as $child)
                    @include('components.about.org-node', ['node' => $child, 'level' => $level + 1, 'small' => true])
                @endforeach
            </div>
        @endif
    </div>
</div>
