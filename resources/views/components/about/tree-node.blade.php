@php
    $level = $level ?? 0;
    $inColumn = $inColumn ?? false;
    $hasChildren = !empty($node['children']);
    $isRoot = $level === 0;
    $isExecutive = $level === 1 || $level === 2;
@endphp

@if($inColumn)
    {{-- Node inside a vertical department column (stacks vertically straight down like Canva reference) --}}
    <div class="org-card-wrapper">
        <div class="org-tree-card group relative text-center rounded-xl bg-white !border-2 !border-slate-200 hover:!border-[#0B2B5C] shadow-xs px-3 py-2.5 w-[160px] sm:w-[175px] transition-all hover:shadow-sm select-none"
             style="background: #ffffff !important; color: #0f172a !important;">
            <h4 class="font-heading !font-black !text-xs sm:!text-[13px] !text-[#0B2B5C] group-hover:!text-[#E31E24] leading-snug !m-0 !mb-0"
                style="color: #0B2B5C !important;">
                {{ $node['name'] }}
            </h4>
            <div class="my-1.5 w-6 mx-auto h-px !bg-slate-200 group-hover:!bg-[#E31E24]/40 transition-colors"
                 style="background-color: #E2E8F0 !important;"></div>
            <p class="italic !text-[10px] sm:!text-[11px] !font-medium !text-slate-500 leading-tight !m-0 !mb-0"
               style="color: #64748B !important;">
                {{ $node['role'] }}
            </p>
        </div>
    </div>

    @if($hasChildren)
        @foreach($node['children'] as $child)
            {{-- Crisp vertical connector line between stacked cards --}}
            <div class="w-[2px] h-4.5 !bg-[#0B2B5C] mx-auto my-0"></div>
            @include('components.about.tree-node', ['node' => $child, 'level' => $level + 1, 'inColumn' => true])
        @endforeach
    @endif
@else
    {{-- Node in the horizontal tree (CEO, DCEO, DGM) --}}
    <li class="org-tree-item">
        <div class="org-card-wrapper {{ $hasChildren ? 'has-children' : '' }}">
            <div class="org-tree-card group relative text-center rounded-xl transition-all duration-200 select-none
                @if($isRoot)
                    !bg-gradient-to-b !from-[#0E3A7A] !to-[#0B2B5C] !text-white shadow-md !border-t-[3px] !border-t-[#E31E24] !border-x !border-b !border-[#0B2B5C] w-[190px] sm:w-[210px] px-3.5 py-3
                @elseif($isExecutive)
                    !bg-gradient-to-b !from-[#1C69B5] !to-[#185FA5] !text-white shadow-xs !border !border-[#124A82] w-[170px] sm:w-[190px] px-3 py-2.5
                @else
                    !bg-white !text-slate-900 !border-2 !border-slate-200 hover:!border-[#0B2B5C] w-[160px] sm:w-[175px] px-3 py-2.5 hover:shadow-sm
                @endif"
                style="{{ $isRoot ? 'background: linear-gradient(to bottom, #0E3A7A, #0B2B5C) !important; color: #ffffff !important;' : ($isExecutive ? 'background: linear-gradient(to bottom, #1C69B5, #185FA5) !important; color: #ffffff !important;' : 'background: #ffffff !important; color: #0f172a !important;') }}">

                {{-- Person Name --}}
                <h4 class="font-heading !font-black tracking-tight leading-snug !m-0 !mb-0
                    @if($isRoot)
                        !text-xs sm:!text-sm !text-white
                    @elseif($isExecutive)
                        !text-xs sm:!text-[13px] !text-white
                    @else
                        !text-xs sm:!text-[13px] !text-[#0B2B5C] group-hover:!text-[#E31E24]
                    @endif"
                    style="color: {{ $isRoot || $isExecutive ? '#ffffff' : '#0B2B5C' }} !important;">
                    {{ $node['name'] }}
                </h4>

                {{-- Dividing Line --}}
                <div class="my-1.5
                    @if($isRoot)
                        w-8 mx-auto h-[2px] !bg-[#E31E24] rounded-full
                    @elseif($isExecutive)
                        w-7 mx-auto h-px !bg-white/40
                    @else
                        w-6 mx-auto h-px !bg-slate-200 group-hover:!bg-[#E31E24]/40 transition-colors
                    @endif"
                    style="{{ $isRoot ? 'background-color: #E31E24 !important;' : ($isExecutive ? 'background-color: rgba(255, 255, 255, 0.4) !important;' : 'background-color: #E2E8F0 !important;') }}"></div>

                {{-- Position / Title --}}
                <p class="italic leading-tight !font-medium !m-0 !mb-0
                    @if($isRoot)
                        !text-[11px] !text-slate-200
                    @elseif($isExecutive)
                        !text-[10px] sm:!text-[11px] !text-blue-100
                    @else
                        !text-[10px] sm:!text-[11px] !text-slate-500
                    @endif"
                    style="color: {{ $isRoot ? '#E2E8F0' : ($isExecutive ? '#DBEAFE' : '#64748B') }} !important;">
                    {{ $node['role'] }}
                </p>
            </div>
        </div>

        {{-- Subordinate Branches / Children --}}
        @if($hasChildren)
            @php
                // If this node branches to multiple departments or is at level >= 2,
                // each child becomes a separate vertical column!
                $childrenShouldBeColumns = $level >= 2 || count($node['children']) > 1;
            @endphp

            <ul class="org-tree-children">
                @foreach($node['children'] as $child)
                    @if($childrenShouldBeColumns)
                        <li class="org-tree-item !px-2 sm:!px-3">
                            <div class="flex flex-col items-center">
                                @include('components.about.tree-node', ['node' => $child, 'level' => $level + 1, 'inColumn' => true])
                            </div>
                        </li>
                    @else
                        @include('components.about.tree-node', ['node' => $child, 'level' => $level + 1, 'inColumn' => false])
                    @endif
                @endforeach
            </ul>
        @endif
    </li>
@endif
