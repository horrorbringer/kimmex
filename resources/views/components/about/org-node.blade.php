@props(['node', 'level' => 0])

@php
    $hasChildren = isset($node['children']) && count($node['children']) > 0;
    $isCEO = $level === 0;
@endphp

<div class="flex flex-col items-center w-full">
    <div class="relative">
        <x-about.team-member-card :member="$node" :isCEO="$isCEO" />

        @if($hasChildren)
            <div class="absolute left-1/2 -bottom-8 w-[2px] h-8 bg-titan-red/20 -translate-x-1/2"></div>
        @endif
    </div>

    @if($hasChildren)
        <div class="mt-8 w-full relative">
            @if(count($node['children']) > 1)
                <div class="absolute top-0 left-[12.5%] right-[12.5%] h-[2px] bg-titan-red/20"></div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-{{ min(count($node['children']), 4) }} gap-8 pt-0">
                @foreach($node['children'] as $child)
                    <div class="relative pt-8 flex flex-col items-center">
                        @if(count($node['children']) > 1)
                            <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[2px] h-8 bg-titan-red/20"></div>
                        @endif
                        <x-about.org-node :node="$child" :level="$level + 1" />
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
