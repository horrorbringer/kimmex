@php
    $level = $level ?? 0;
    $inColumn = $inColumn ?? false;
    $hasChildren = !empty($node['children']);
    $isRoot = $level === 0;
    $isExecutive = $level === 1 || $level === 2;
    $image = $node['image'] ?? null;

    $name = trim($node['name'] ?? '');
    $words = preg_split('/\s+/', $name);
    $initials = '';
    if (count($words) >= 2) {
        $initials = mb_substr($words[0], 0, 1) . mb_substr($words[count($words) - 1], 0, 1);
    } elseif (!empty($words[0])) {
        $initials = mb_substr($words[0], 0, 2);
    }
    $initials = strtoupper($initials);
@endphp

@if($inColumn)
    {{-- Node inside a vertical department column (stacks vertically straight down like Canva reference) --}}
    <div class="org-card-wrapper pt-3">
        <div class="org-tree-card group relative text-center rounded-xl bg-white !border-2 !border-slate-200 hover:!border-[#0B2B5C] shadow-xs px-3 pt-5.5 pb-2.5 w-[160px] sm:w-[175px] transition-all hover:shadow-sm select-none"
             style="background: #ffffff !important; color: #0f172a !important;">

            {{-- Floating Circular Avatar Clipped to Center Top Edge --}}
            <div class="absolute -top-4.5 left-1/2 -translate-x-1/2 z-10">
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full border-2 border-white shadow-xs overflow-hidden flex items-center justify-center bg-slate-100 text-[#0B2B5C] font-bold text-xs shrink-0 ring-1 ring-slate-200/90">
                    @if($image)
                        <img src="{{ $image }}" alt="{{ $name }}" class="w-full h-full object-cover object-top" loading="lazy" />
                    @else
                        <span class="tracking-wider">{{ $initials }}</span>
                    @endif
                </div>
            </div>

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
            <div class="w-[2px] h-4 !bg-[#0B2B5C] mx-auto my-0"></div>
            @include('components.about.tree-node', ['node' => $child, 'level' => $level + 1, 'inColumn' => true])
        @endforeach
    @endif
@else
    {{-- Node in the horizontal tree (CEO, DCEO, DGM) --}}
    <li class="org-tree-item">
        <div class="org-card-wrapper {{ $hasChildren ? 'has-children' : '' }} pt-4">
            <div class="org-tree-card group relative text-center rounded-xl transition-all duration-200 select-none
                @if($isRoot)
                    !bg-gradient-to-b !from-[#0E3A7A] !to-[#0B2B5C] !text-white shadow-md !border-t-[3px] !border-t-[#E31E24] !border-x !border-b !border-[#0B2B5C] w-[190px] sm:w-[210px] px-3.5 pt-7 pb-3 sm:pb-3.5
                @elseif($isExecutive)
                    !bg-gradient-to-b !from-[#1C69B5] !to-[#185FA5] !text-white shadow-xs !border !border-[#124A82] w-[170px] sm:w-[190px] px-3 pt-6 pb-2.5 sm:pb-3
                @else
                    !bg-white !text-slate-900 !border-2 !border-slate-200 hover:!border-[#0B2B5C] w-[160px] sm:w-[175px] px-3 pt-5.5 pb-2.5 hover:shadow-sm
                @endif"
                style="{{ $isRoot ? 'background: linear-gradient(to bottom, #0E3A7A, #0B2B5C) !important; color: #ffffff !important;' : ($isExecutive ? 'background: linear-gradient(to bottom, #1C69B5, #185FA5) !important; color: #ffffff !important;' : 'background: #ffffff !important; color: #0f172a !important;') }}">

                {{-- Floating Circular Avatar Clipped to Center Top Edge --}}
                <div class="absolute {{ $isRoot ? '-top-6' : '-top-5' }} left-1/2 -translate-x-1/2 z-10">
                    @if($isRoot)
                        <div class="w-12 h-12 rounded-full border-2 border-[#E31E24] shadow-md overflow-hidden flex items-center justify-center bg-[#0B2B5C] text-white font-black text-sm shrink-0 ring-2 ring-white/20">
                            @if($image)
                                <img src="{{ $image }}" alt="{{ $name }}" class="w-full h-full object-cover object-top" loading="lazy" />
                            @else
                                <span class="tracking-wider">{{ $initials }}</span>
                            @endif
                        </div>
                    @elseif($isExecutive)
                        <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-full border-2 border-white shadow-sm overflow-hidden flex items-center justify-center bg-[#155394] text-white font-bold text-xs shrink-0 ring-1 ring-blue-300/40">
                            @if($image)
                                <img src="{{ $image }}" alt="{{ $name }}" class="w-full h-full object-cover object-top" loading="lazy" />
                            @else
                                <span class="tracking-wider">{{ $initials }}</span>
                            @endif
                        </div>
                    @else
                        <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full border-2 border-white shadow-xs overflow-hidden flex items-center justify-center bg-slate-100 text-[#0B2B5C] font-bold text-xs shrink-0 ring-1 ring-slate-200/90">
                            @if($image)
                                <img src="{{ $image }}" alt="{{ $name }}" class="w-full h-full object-cover object-top" loading="lazy" />
                            @else
                                <span class="tracking-wider">{{ $initials }}</span>
                            @endif
                        </div>
                    @endif
                </div>

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
            <ul class="org-tree-children">
                @foreach($node['children'] as $child)
                    @php
                        $childIsExecutive = in_array(strtoupper($child['unitType'] ?? ''), ['EXECUTIVE', 'MANAGEMENT'])
                            || preg_match('/\b(ceo|dceo|gm|dgm|general manager|president|chief|board)\b/i', $child['role'] ?? '')
                            || ($level < 2 && !empty($child['children']));

                        // Executive leadership positions remain in the central horizontal spine.
                        // Non-executive positions (departments, managers, staff) form vertical columns!
                        $childInColumn = !$childIsExecutive;
                    @endphp

                    @if($childInColumn)
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
