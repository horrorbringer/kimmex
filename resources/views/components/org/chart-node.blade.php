@props(['node', 'depth' => 0, 'preview' => false])

<div class="org-node" data-id="{{ $node['id'] }}">
    <div class="node-card">
        @if(!$preview)
            <div class="node-drag-handle cursor-grab" title="{{ __('Drag to reorder') }}">
                <x-heroicon-o-squares-2x2 class="org-icon-md" />
            </div>
        @endif

        @if(!empty($node['children']))
            <div class="node-toggle-placeholder" title="{{ __('Has child units') }}">
                <x-heroicon-o-chevron-down class="org-icon-sm" />
            </div>
        @else
            <div class="node-toggle-placeholder"></div>
        @endif

        <div class="avatar-circle">
            @if($node['image'])
                <img src="{{ \App\Support\PublicStorage::url($node['image']) }}" 
                     alt="{{ $node['name'] }}" decoding="async" loading="lazy" />
            @else
                <span class="avatar-initials">
                    {{ strtoupper(substr($node['name'], 0, 1)) }}{{ strtoupper(substr(strrchr($node['name'], ' ') ?: '', 1, 1)) ?: '' }}
                </span>
            @endif
        </div>

        <div class="node-content">
            <div class="node-title-row">
                <h4 class="node-name">{{ $node['name'] }}</h4>
                <span class="node-type-pill">{{ $node['type'] }}</span>
            </div>
            <p class="node-role">{{ $node['role'] }}</p>
        </div>

        @if(!$preview)
            <div class="node-actions">
                <button 
                    title="{{ __('Add Child') }}" 
                    wire:click="mountAction('addChild', { id: '{{ $node['id'] }}' })"
                    class="node-action-btn">
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
                    class="node-action-btn btn-danger">
                    <x-heroicon-o-trash class="org-icon-sm" />
                </button>
            </div>
        @endif
    </div>

    @if(!empty($node['children']))
        <div class="node-children children-container">
            @foreach($node['children'] as $child)
                <x-org.chart-node :node="$child" :depth="$depth + 1" />
            @endforeach
        </div>
    @endif
</div>
