@php
    $level = $level ?? 0;
    $hasChildren = count($node['children'] ?? []) > 0;
    $childCount = count($node['children'] ?? []);
@endphp

<div class="draggable-item" data-id="{{ $node['id'] }}">
    <div class="node-card">

        {{-- Drag handle --}}
        <div class="sortable-handle node-drag-handle" @click.stop>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="9" cy="6" r="1.5" fill="currentColor" />
                <circle cx="9" cy="12" r="1.5" fill="currentColor" />
                <circle cx="9" cy="18" r="1.5" fill="currentColor" />
                <circle cx="15" cy="6" r="1.5" fill="currentColor" />
                <circle cx="15" cy="12" r="1.5" fill="currentColor" />
                <circle cx="15" cy="18" r="1.5" fill="currentColor" />
            </svg>
        </div>

        {{-- Expand toggle --}}
        <div style="width: 24px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; cursor: pointer;"
             @click="expanded['{{ $node['id'] }}'] = !expanded['{{ $node['id'] }}']">
            @if($hasChildren)
                <svg :class="expanded['{{ $node['id'] }}'] ? 'org-chevron-open' : 'org-chevron-closed'"
                     class="org-chevron"
                     width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="m6 9 6 6 6-6"/>
                </svg>
            @else
                <span style="width: 6px; height: 6px; border-radius: 50%; background: var(--org-border);"></span>
            @endif
        </div>

        {{-- Avatar --}}
        <div class="node-avatar">
            @if($node['image'])
                <img src="{{ $node['image'] }}"
                     onerror="this.parentElement.innerHTML='<span class=\'avatar-initials\'>{{ mb_substr($node['name'], 0, 1) }}</span>'"
                     decoding="async" loading="lazy" />
            @else
                <span class="avatar-initials">{{ mb_substr($node['name'], 0, 2) }}</span>
            @endif
        </div>

        {{-- Content --}}
        <div style="flex: 1; min-width: 0;">
            <p class="node-name">{{ $node['name'] }}</p>
            <p class="node-role">{{ $node['title'] }}</p>
        </div>

        {{-- Type badge --}}
        <span class="node-type-pill">{{ $node['type'] }}</span>

        {{-- Child count --}}
        @if($hasChildren)
            <span class="node-child-count" @click="expanded['{{ $node['id'] }}'] = !expanded['{{ $node['id'] }}']">
                {{ $childCount }}
            </span>
        @endif

        {{-- Actions --}}
        <div class="node-actions" @click.stop>
            <button class="node-action-btn" title="{{ __('Add Child') }}"
                x-on:click="$wire.mountAction('addChild', { id: '{{ $node['id'] }}' })">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M12 5v14M5 12h14"/>
                </svg>
            </button>
            <button class="node-action-btn" title="{{ __('Edit') }}"
                x-on:click="$wire.mountAction('edit', { id: '{{ $node['id'] }}' })">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                    <path d="M18.5 2.5a2.1 2.1 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                </svg>
            </button>
            <button class="node-action-btn btn-danger" title="{{ __('Delete') }}"
                x-on:click="$wire.mountAction('delete', { id: '{{ $node['id'] }}' })">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Children --}}
    <div x-show="expanded['{{ $node['id'] }}']"
         x-collapse
         class="children-container"
         data-parent-id="{{ $node['id'] }}">
        @foreach($node['children'] ?? [] as $child)
            @include('filament.pages.org-chart-node', ['node' => $child, 'level' => $level + 1])
        @endforeach
    </div>
</div>
