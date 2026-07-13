@props(['node', 'level' => 0, 'small' => false])

@php
    $hasChildren = isset($node['children']) && count($node['children']) > 0;
    $isCEO       = $level === 0;
    $isDirector  = $level === 1;
    $childCount  = $hasChildren ? count($node['children']) : 0;
    $isDepartment = ($node['type'] ?? '') === 'department';
@endphp

{{-- ─── MOBILE: vertical accordion tree ─── --}}
<div class="block md:hidden w-full">
    <div class="relative"
         x-data="{ open: {{ $level < 1 ? 'true' : 'false' }} }">

        {{-- Card --}}
        <div class="flex items-center gap-3 px-3 sm:px-4 py-3 rounded-xl border transition-all duration-300
                    {{ $isCEO ? 'bg-titan-navy border-titan-navy/20 shadow-lg' : ($isDirector ? 'bg-gray-50 border-gray-200 shadow-sm' : 'bg-white border-gray-100 shadow-sm') }}
                    {{ $hasChildren ? 'cursor-pointer select-none active:scale-[0.98]' : (!$isDepartment ? 'cursor-pointer active:scale-[0.98]' : '') }}"
             @if($hasChildren) @click="open = !open" @endif
             @if(!$hasChildren && !$isDepartment) @click="$dispatch('select-member', {{ Js::from($node) }})" @endif>

            {{-- Avatar --}}
            @if(!$isDepartment)
            <div class="shrink-0 {{ $isCEO ? 'w-12 h-12 sm:w-14 sm:h-14' : 'w-10 h-10 sm:w-11 sm:h-11' }} rounded-full overflow-hidden border-2
                        {{ $isCEO ? 'border-titan-red/60' : ($isDirector ? 'border-titan-red/30' : 'border-gray-200') }} bg-gray-100">
                @if(!empty($node['image']))
                    <img src="{{ $node['image'] }}" alt="{{ $node['name'] }}"
                         class="w-full h-full object-cover object-top" decoding="async" loading="lazy" />
                @else
                    <div class="w-full h-full flex items-center justify-center
                                {{ $isCEO ? 'bg-titan-navy/40' : 'bg-gray-50' }}">
                        <x-lucide-user class="w-4 h-4 sm:w-5 sm:h-5 {{ $isCEO ? 'text-white/60' : 'text-gray-300' }}" />
                    </div>
                @endif
            </div>
            @else
            <div class="shrink-0 w-10 h-10 sm:w-11 sm:h-11 rounded-lg bg-titan-navy/10 flex items-center justify-center">
                <x-lucide-building-2 class="w-4 h-4 sm:w-5 sm:h-5 text-titan-navy/60" />
            </div>
            @endif

            {{-- Info --}}
            <div class="flex-1 min-w-0">
                <div class="text-[8px] sm:text-[9px] font-black uppercase tracking-[0.15em] sm:tracking-[0.18em] mb-0.5
                            {{ $isCEO ? 'text-titan-red' : ($isDepartment ? 'text-titan-navy/40' : 'text-titan-red/70') }}">
                    {{ $isDepartment ? __('DEPARTMENT') : ($node['role'] ?? $node['type'] ?? '') }}
                </div>
                <div class="font-bold leading-tight truncate
                            {{ $isCEO ? 'text-white text-xs sm:text-sm' : 'text-titan-navy text-[11px] sm:text-xs' }}">
                    {{ $node['name'] }}
                </div>
            </div>

            {{-- Expand toggle / children count --}}
            @if($hasChildren)
                <div class="shrink-0 flex items-center gap-1.5">
                    <span class="text-[9px] font-bold {{ $isCEO ? 'text-white/40' : 'text-titan-navy/30' }}">{{ $childCount }}</span>
                    <div class="w-6 h-6 sm:w-7 sm:h-7 rounded-full flex items-center justify-center
                                {{ $isCEO ? 'bg-white/10' : 'bg-gray-100' }}">
                        <x-lucide-chevron-down
                            class="w-3.5 h-3.5 transition-transform duration-300
                                   {{ $isCEO ? 'text-white/70' : 'text-titan-navy/50' }}"
                            ::class="open ? 'rotate-180' : ''" />
                    </div>
                </div>
            @endif
        </div>

        {{-- Children accordion --}}
        @if($hasChildren)
            <div x-show="open"
                 x-collapse
                 class="ml-4 sm:ml-5 mt-1.5 pl-3 sm:pl-4 border-l-2 {{ $isCEO ? 'border-titan-red/30' : 'border-gray-200' }} space-y-1.5 pb-1">
                @foreach($node['children'] as $child)
                    <x-about.org-node :node="$child" :level="$level + 1" :small="true" />
                @endforeach
            </div>
        @endif
    </div>
</div>

{{-- ─── TABLET: scrollable card layout (md to lg) ─── --}}
{{-- ─── DESKTOP: horizontal tree ─── --}}
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
            <div class="flex flex-row flex-nowrap justify-center gap-4 lg:gap-6 xl:gap-8 pt-0 min-w-max mx-auto">
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
