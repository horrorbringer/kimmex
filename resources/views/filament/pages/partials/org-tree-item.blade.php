@props(['node', 'depth' => 0])

@php
    $hasChildren = !empty($node['children']);
    $nodeName = $node['name'] ?? __('Unnamed');
    $nodeTitle = $node['title'] ?: ($node['role'] ?: __('Position'));
    $nodeType = $node['type'] ?? 'STAFF';
    $isActive = (bool)($node['isActive'] ?? true);
@endphp

<div class="org-tree-row"
     x-data="{ expanded: true }"
     x-show="search === '' || ('{{ strtolower($nodeName . ' ' . $nodeTitle . ' ' . $nodeType) }}'.includes(search.toLowerCase()))"
     style="margin-left: {{ $depth > 0 ? ($depth * 1.125) : 0 }}rem; position: relative;">

    {{-- Left connector line if indented --}}
    @if($depth > 0)
        <div style="position: absolute; left: -0.75rem; top: 1.25rem; width: 0.65rem; height: 1px; background-color: #cbd5e1;"></div>
    @endif

    <div class="org-item-card {{ $isActive ? '' : 'is-inactive' }} {{ $depth === 0 ? 'is-root' : '' }}">
        {{-- Expand/collapse button or spacer --}}
        @if($hasChildren)
            <button type="button"
                    @click="expanded = !expanded"
                    class="org-expand-btn"
                    title="{{ __('Toggle Subordinates') }}">
                <x-heroicon-o-chevron-down style="width: 13px; height: 13px; transition: transform 0.2s;" ::style="expanded ? 'width: 13px; height: 13px;' : 'transform: rotate(-90deg); width: 13px; height: 13px;'" />
            </button>
        @else
            <div style="width: 1.35rem; height: 1.35rem; flex-shrink: 0; display: grid; place-items: center;">
                <span style="width: 5px; height: 5px; border-radius: 999px; background-color: #cbd5e1;"></span>
            </div>
        @endif

        {{-- Compact Avatar / Initials --}}
        <div class="org-avatar-box">
            @if(!empty($node['image']))
                <img src="{{ $node['image'] }}" alt="" style="width: 100%; height: 100%; object-fit: cover; border-radius: 999px;" />
            @else
                <span>{{ strtoupper(substr($nodeName, 0, 2)) }}</span>
            @endif
        </div>

        {{-- Node Details --}}
        <div class="org-item-details">
            <div style="display: flex; align-items: center; gap: 0.375rem; flex-wrap: wrap;">
                <span class="org-person-name">{{ $nodeName }}</span>
                <span class="org-badge-type org-badge-{{ strtolower($nodeType) }}">{{ $nodeType }}</span>
                @if(!$isActive)
                    <span class="org-badge-hidden">{{ __('Hidden') }}</span>
                @endif
                @if($hasChildren)
                    <span class="org-badge-children">{{ count($node['children']) }}</span>
                @endif
            </div>
            <p class="org-position-title">{{ $nodeTitle }}</p>
        </div>

        {{-- Compact Direct Icon Actions --}}
        <div class="org-item-actions">
            <button type="button"
                    wire:click="mountAction('addChild', { id: '{{ $node['id'] }}' })"
                    class="org-icon-btn is-add"
                    title="{{ __('Add Subordinate under :name', ['name' => $nodeName]) }}">
                <x-heroicon-o-plus style="width: 13px; height: 13px;" />
            </button>

            <button type="button"
                    wire:click="mountAction('edit', { id: '{{ $node['id'] }}' })"
                    class="org-icon-btn"
                    title="{{ __('Edit Position') }}">
                <x-heroicon-o-pencil-square style="width: 13px; height: 13px;" />
            </button>

            <button type="button"
                    wire:click="mountAction('toggleActive', { id: '{{ $node['id'] }}' })"
                    class="org-icon-btn"
                    title="{{ $isActive ? __('Hide from Website') : __('Show on Website') }}">
                @if($isActive)
                    <x-heroicon-o-eye style="width: 13px; height: 13px; color: #16a34a;" />
                @else
                    <x-heroicon-o-eye-slash style="width: 13px; height: 13px; color: #dc2626;" />
                @endif
            </button>

            <button type="button"
                    wire:click="mountAction('delete', { id: '{{ $node['id'] }}' })"
                    class="org-icon-btn is-delete"
                    title="{{ __('Delete Position') }}">
                <x-heroicon-o-trash style="width: 13px; height: 13px; color: #ef4444;" />
            </button>
        </div>
    </div>

    {{-- Subordinate Children --}}
    @if($hasChildren)
        <div x-show="expanded" style="position: relative; margin-top: 0.35rem; display: flex; flex-direction: column; gap: 0.35rem;">
            {{-- Vertical connecting line --}}
            <div style="position: absolute; left: calc({{ $depth * 1.125 }}rem + 0.65rem); top: -0.35rem; bottom: 1rem; width: 1px; background-color: #cbd5e1;"></div>

            @foreach($node['children'] as $child)
                @include('filament.pages.partials.org-tree-item', ['node' => $child, 'depth' => $depth + 1])
            @endforeach
        </div>
    @endif
</div>
