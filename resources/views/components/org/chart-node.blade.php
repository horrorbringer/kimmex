@props(['node', 'depth' => 0, 'preview' => false])

<div class="relative group" data-id="{{ $node['id'] }}">
    <!-- Node Content -->
    <div class="node-card">
        <!-- Drag Handle -->
        @if(!$preview)
            <div class="cursor-grab mr-4 text-gray-300 hover:text-titan-navy transition-colors">
                <x-heroicon-o-squares-2x2 class="org-icon-md" />
            </div>
        @endif

        <!-- Collapse/Expand (if children) -->
        @if(!empty($node['children']))
            <div class="mr-4 text-gray-400 hover:text-titan-navy cursor-pointer">
                <x-heroicon-o-chevron-down class="org-icon-sm" />
            </div>
        @else
            <div class="mr-4 w-4"></div>
        @endif

        <!-- Avatar -->
        <div class="avatar-circle">
            @if($node['image'])
                <img src="{{ \Illuminate\Support\Facades\Storage::url($node['image']) }}" 
                     alt="{{ $node['name'] }}" />
            @else
                <span class="avatar-initials">
                    {{ strtoupper(substr($node['name'], 0, 1)) }}{{ strtoupper(substr(strrchr($node['name'], ' ') ?: '', 1, 1)) ?: '' }}
                </span>
            @endif
        </div>

        <!-- Info -->
        <div class="flex-grow">
            <h4 class="node-name">{{ $node['name'] }}</h4>
            <p class="node-role">{{ $node['role'] }}</p>
        </div>

        <!-- Actions -->
        @if(!$preview)
            <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                <button 
                    title="{{ __('Add Child') }}" 
                    wire:click="mountAction('addChild', { id: '{{ $node['id'] }}' })"
                    class="node-action-btn btn-red">
                    <x-heroicon-o-plus class="org-icon-sm" />
                </button>
                <button 
                    title="{{ __('Edit') }}" 
                    wire:click="mountAction('edit', { id: '{{ $node['id'] }}' })"
                    class="node-action-btn">
                    <x-heroicon-o-pencil-square class="org-icon-sm" />
                </button>
                <button 
                    title="{{ __('Delete') }}" 
                    wire:click="mountAction('delete', { id: '{{ $node['id'] }}' })"
                    class="node-action-btn btn-red">
                    <x-heroicon-o-trash class="org-icon-sm" />
                </button>
            </div>
        @endif
    </div>

    <!-- Recursive Children -->
    @if(!empty($node['children']))
        <div class="node-children">
            @foreach($node['children'] as $child)
                <x-org.chart-node :node="$child" :depth="$depth + 1" />
            @endforeach
        </div>
    @endif
</div>
