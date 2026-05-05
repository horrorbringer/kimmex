@props(['node', 'level' => 0, 'small' => false])

@php
    $hasChildren = isset($node['children']) && count($node['children']) > 0;
    $isCEO = $level === 0;
    $childCount = $hasChildren ? count($node['children']) : 0;
    
    // Spacing and sizing based on small mode
    $nodeSpacing = $small ? 'mt-4' : 'mt-8';
    $lineHeight = $small ? 'h-4' : 'h-8';
    $lineBottomOffset = $small ? '-bottom-4' : '-bottom-8';
    $verticalLineHeight = $small ? 'h-4' : 'h-8';
    $paddingTop = $small ? 'pt-4' : 'pt-8';
    $gapSize = $small ? 'gap-4' : 'gap-8';
@endphp

<div class="flex flex-col items-center w-full">
    <div class="relative">
        <x-about.team-member-card :member="$node" :isCEO="$isCEO" :small="$small" />

        @if($hasChildren)
            <div class="absolute left-1/2 {{ $lineBottomOffset }} w-[2px] {{ $lineHeight }} bg-titan-red/20 -translate-x-1/2"></div>
        @endif
    </div>

    @if($hasChildren)
        <div class="{{ $nodeSpacing }} w-full relative">
            <div class="grid grid-cols-1 md:grid-cols-{{ min($childCount, 4) }} {{ $gapSize }} pt-0">
                @foreach($node['children'] as $index => $child)
                    <div class="relative {{ $paddingTop }} flex flex-col items-center">
                        {{-- Horizontal 'Shoulder' Line --}}
                        @if($childCount > 1)
                            <div class="absolute top-0 h-[2px] bg-titan-red/20 
                                {{ $index === 0 ? 'left-1/2 right-0' : ($index === $childCount - 1 ? 'left-0 right-1/2' : 'left-0 right-0') }}">
                            </div>
                        @endif

                        {{-- Vertical Connector to this child --}}
                        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[2px] {{ $lineHeight }} bg-titan-red/20"></div>
                        
                        <x-about.org-node :node="$child" :level="$level + 1" :small="$small" />
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
