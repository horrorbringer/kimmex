@php
    $level      = $level ?? 0;
    $isDeep     = $level >= 2;
    $hasChildren = count($node['children'] ?? []) > 0;
@endphp

<div class="draggable-item" data-id="{{ $node['id'] }}">
    <div class="node-card" @click="expanded['{{ $node['id'] }}'] = !expanded['{{ $node['id'] }}']">

        {{-- Drag handle — class MUST match SortableJS handle option --}}
        <div class="sortable-handle node-drag-handle">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <circle cx="9"  cy="9"  r="1" fill="currentColor" />
                <circle cx="9"  cy="15" r="1" fill="currentColor" />
                <circle cx="15" cy="9"  r="1" fill="currentColor" />
                <circle cx="15" cy="15" r="1" fill="currentColor" />
            </svg>
        </div>

        {{-- Expand toggle --}}
        <div style="width:20px" class="flex items-center justify-center flex-none">
            @if($hasChildren)
                <svg :class="expanded['{{ $node['id'] }}'] ? '' : '-rotate-90'"
                     class="transition-transform duration-200"
                     width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#5A7BA5" stroke-width="3">
                    <path d="m6 9 6 6 6-6"/>
                </svg>
            @endif
        </div>

        {{-- Avatar --}}
        <div class="node-avatar">
            @if($node['image'])
                <img src="{{ $node['image'] }}"
                     onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($node['name']) }}&background=f8fafc&color=cbd5e1'"
                     decoding="async" loading="lazy" />
            @else
                @if($isDeep)
                    <svg viewBox="0 0 24 24" fill="none" stroke="#5A7BA5" stroke-width="1.5">
                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                        <polyline points="3.29 7 12 12 20.71 7"/>
                        <line x1="12" y1="22" x2="12" y2="12"/>
                    </svg>
                @else
                    <svg viewBox="0 0 24 24" fill="none" stroke="#5A7BA5" stroke-width="1.5">
                        <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                @endif
            @endif
        </div>

        {{-- Name --}}
        <div class="flex-1 text-left min-w-0">
            <p class="node-name">{{ $node['name'] }}</p>
        </div>

        <div class="node-role-list">{{ $node['role'] }}</div>

        {{-- Actions --}}
        <div class="card-actions" @click.stop>
            <button class="action-btn primary" title="{{ __('Add Child') }}"
                x-on:click="$wire.mountAction('addChild', { id: '{{ $node['id'] }}' })">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5">
                    <path d="M12 5v14M5 12h14"/>
                </svg>
            </button>
            <button class="action-btn" title="{{ __('Edit') }}"
                x-on:click="$wire.mountAction('edit', { id: '{{ $node['id'] }}' })">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                </svg>
            </button>
            <button class="action-btn delete" title="{{ __('Delete') }}"
                x-on:click="$wire.mountAction('delete', { id: '{{ $node['id'] }}' })">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Children container — class MUST match SortableJS selector --}}
    <div x-show="expanded['{{ $node['id'] }}']"
         x-collapse
         class="children-container"
         data-parent-id="{{ $node['id'] }}">
        @foreach($node['children'] ?? [] as $child)
            @include('filament.pages.org-chart-node', ['node' => $child, 'level' => $level + 1])
        @endforeach
    </div>
</div>
