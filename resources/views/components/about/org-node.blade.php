@props(['node', 'level' => 0, 'small' => false])

@php
    $hasChildren = isset($node['children']) && count($node['children']) > 0;
    $isCEO       = $level === 0;
    $childCount  = $hasChildren ? count($node['children']) : 0;
    $nodeId      = 'org-' . ($node['id'] ?? $level . '-' . rand(100, 999));
@endphp

{{-- ─── MOBILE: vertical accordion tree ─── --}}
<div class="block md:hidden w-full">
    <div class="relative"
         x-data="{ open: {{ $level < 2 ? 'true' : 'false' }} }">

        {{-- Card --}}
        <div class="flex items-center gap-3 px-3 py-3 rounded-xl border
                    {{ $isCEO ? 'bg-titan-navy border-titan-navy/20 shadow-lg' : 'bg-white border-gray-200 shadow-sm' }}
                    {{ $hasChildren ? 'cursor-pointer select-none' : '' }}"
             @if($hasChildren) @click="open = !open" @endif>

            {{-- Avatar --}}
            <div class="shrink-0 {{ $isCEO ? 'w-14 h-14' : 'w-11 h-11' }} rounded-full overflow-hidden border-2
                        {{ $isCEO ? 'border-titan-red/60' : 'border-gray-200' }} bg-gray-100">
                @if(!empty($node['image']))
                    <img src="{{ $node['image'] }}" alt="{{ $node['name'] }}"
                         class="w-full h-full object-cover object-top" decoding="async" loading="lazy" />
                @else
                    <div class="w-full h-full flex items-center justify-center
                                {{ $isCEO ? 'bg-titan-navy/40' : 'bg-gray-50' }}">
                        <x-lucide-user class="w-5 h-5 {{ $isCEO ? 'text-white/60' : 'text-gray-300' }}" />
                    </div>
                @endif
            </div>

            {{-- Info --}}
            <div class="flex-1 min-w-0">
                <div class="text-[9px] font-black uppercase tracking-[0.18em] mb-0.5
                            {{ $isCEO ? 'text-titan-red' : 'text-titan-red/80' }}">
                    {{ $node['role'] ?? $node['type'] ?? '' }}
                </div>
                <div class="font-black uppercase leading-tight truncate
                            {{ $isCEO ? 'text-white text-sm' : 'text-titan-navy text-[13px]' }}">
                    {{ $node['name'] }}
                </div>
                @if(!empty($node['title']) && $node['title'] !== $node['name'])
                    <div class="text-[10px] mt-0.5 truncate
                                {{ $isCEO ? 'text-white/55' : 'text-titan-navy/45' }}">
                        {{ $node['title'] }}
                    </div>
                @endif
            </div>

            {{-- Expand toggle --}}
            @if($hasChildren)
                <div class="shrink-0 w-7 h-7 rounded-full flex items-center justify-center
                            {{ $isCEO ? 'bg-white/10' : 'bg-gray-100' }}">
                    <x-lucide-chevron-down
                        class="w-4 h-4 transition-transform duration-300
                               {{ $isCEO ? 'text-white/70' : 'text-titan-navy/50' }}"
                        ::class="open ? 'rotate-180' : ''" />
                </div>
            @endif
        </div>

        {{-- Children accordion --}}
        @if($hasChildren)
            <div x-show="open"
                 x-collapse
                 class="ml-5 mt-1 pl-4 border-l-2 border-titan-red/20 space-y-1.5 pb-1">
                @foreach($node['children'] as $child)
                    <x-about.org-node :node="$child" :level="$level + 1" :small="true" />
                @endforeach
            </div>
        @endif
    </div>
</div>

{{-- ─── DESKTOP: horizontal tree (original layout, improved sizing) ─── --}}
<div class="hidden md:flex flex-col items-center w-full">
    <div class="relative">
        {{-- Team member card --}}
        <x-about.team-member-card :member="$node" :isCEO="$isCEO" :small="$small" />

        {{-- Vertical line down to children --}}
        @if($hasChildren)
            <div class="absolute left-1/2 -bottom-8 w-[2px] h-8 bg-titan-red/20 -translate-x-1/2"></div>
        @endif
    </div>

    @if($hasChildren)
        <div class="mt-8 w-full relative">
            <div class="flex flex-row flex-nowrap justify-center gap-6 lg:gap-8 pt-0 min-w-max mx-auto">
                @foreach($node['children'] as $index => $child)
                    <div class="relative pt-8 flex flex-col items-center flex-none">
                        {{-- Horizontal shoulder line --}}
                        @if($childCount > 1)
                            <div class="absolute top-0 h-[2px] bg-titan-red/20
                                {{ $index === 0 ? 'left-1/2 right-0' : ($index === $childCount - 1 ? 'left-0 right-1/2' : 'left-0 right-0') }}">
                            </div>
                        @endif
                        {{-- Vertical connector --}}
                        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[2px] h-8 bg-titan-red/20"></div>

                        <x-about.org-node :node="$child" :level="$level + 1" :small="$small" />
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
