@php
    $level = $level ?? 0;
    $inColumn = $inColumn ?? false;
    $hasChildren = !empty($node['children']);
    $isRoot = $level === 0;
    $isExecutive = $level === 1 || $level === 2;
    $cardStyle = $cardStyle ?? 'avatar_top';
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
    {{-- Node inside a vertical department column (stacks vertically straight down) --}}
    @if($cardStyle === 'floating')
        {{-- STYLE 1: Floating Circular Avatar Clipped to Center Top Edge --}}
        <div class="org-card-wrapper pt-3">
            <div class="org-tree-card group relative text-center rounded-xl bg-white !border-2 !border-slate-200 hover:!border-[#0B2B5C] shadow-xs px-3 pt-5.5 pb-2.5 w-[160px] sm:w-[175px] transition-all hover:shadow-sm select-none"
                 style="background: #ffffff !important; color: #0f172a !important;">
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
    @elseif($cardStyle === 'badge')
        {{-- STYLE 2: Integrated Executive Badge (Photo on top, text below) --}}
        <div class="org-card-wrapper pt-0">
            <div class="org-tree-card group relative text-center rounded-xl bg-white !border-2 !border-slate-200 hover:!border-[#0B2B5C] shadow-xs overflow-hidden w-[160px] sm:w-[175px] transition-all hover:shadow-sm select-none"
                 style="background: #ffffff !important; color: #0f172a !important;">
                <div class="w-full h-22 sm:h-26 bg-slate-100 relative overflow-hidden flex items-center justify-center">
                    @if($image)
                        <img src="{{ $image }}" alt="{{ $name }}" class="w-full h-full object-cover object-top" loading="lazy" />
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-slate-100 to-slate-200 flex items-center justify-center text-slate-400 font-black text-xl tracking-wider">
                            {{ $initials }}
                        </div>
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-black/25 via-transparent to-transparent"></div>
                </div>
                <div class="px-2.5 py-2">
                    <h4 class="font-heading !font-black !text-xs sm:!text-[13px] !text-[#0B2B5C] group-hover:!text-[#E31E24] leading-snug !m-0"
                        style="color: #0B2B5C !important;">{{ $node['name'] }}</h4>
                    <div class="my-1 w-5 mx-auto h-px !bg-slate-200" style="background-color: #E2E8F0 !important;"></div>
                    <p class="italic !text-[10px] sm:!text-[11px] !font-medium !text-slate-500 leading-tight !m-0"
                       style="color: #64748B !important;">{{ $node['role'] }}</p>
                </div>
            </div>
        </div>
    @elseif($cardStyle === 'capsule')
        {{-- STYLE 3: Horizontal Pill / Capsule (Avatar Left, Text Right) --}}
        <div class="org-card-wrapper pt-0">
            <div class="org-tree-card group relative flex items-center gap-2.5 rounded-xl bg-white !border-2 !border-slate-200 hover:!border-[#0B2B5C] shadow-xs px-2.5 py-2 w-[170px] sm:w-[190px] transition-all hover:shadow-sm select-none text-left"
                 style="background: #ffffff !important; color: #0f172a !important;">
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full border border-slate-200 overflow-hidden flex items-center justify-center bg-slate-100 text-[#0B2B5C] font-bold text-xs shrink-0 shadow-2xs ring-1 ring-slate-200/80">
                    @if($image)
                        <img src="{{ $image }}" alt="{{ $name }}" class="w-full h-full object-cover object-top" loading="lazy" />
                    @else
                        <span>{{ $initials }}</span>
                    @endif
                </div>
                <div class="min-w-0 flex-1">
                    <h4 class="font-heading !font-black !text-[11px] sm:!text-xs !text-[#0B2B5C] group-hover:!text-[#E31E24] leading-tight truncate !m-0"
                        style="color: #0B2B5C !important;">{{ $node['name'] }}</h4>
                    <p class="italic !text-[9px] sm:!text-[10px] !font-medium !text-slate-500 leading-tight truncate !m-0 mt-0.5"
                       style="color: #64748B !important;">{{ $node['role'] }}</p>
                </div>
            </div>
        </div>
    @elseif($cardStyle === 'corporate')
        {{-- STYLE 4: Corporate Tagged Minimalist --}}
        <div class="org-card-wrapper pt-0">
            <div class="org-tree-card group relative text-center rounded-xl bg-white !border-2 !border-slate-200 hover:!border-[#0B2B5C] shadow-xs px-3 pt-2 pb-2.5 w-[160px] sm:w-[175px] transition-all hover:shadow-sm select-none"
                 style="background: #ffffff !important; color: #0f172a !important;">
                <div class="inline-block px-2 py-0.5 rounded text-[9px] font-bold tracking-wider uppercase bg-slate-100 text-[#0B2B5C] mb-1.5 border border-slate-200">
                    {{ $node['unitType'] ?? 'MEMBER' }}
                </div>
                <div class="w-10 h-10 rounded-lg mx-auto border border-slate-200 overflow-hidden flex items-center justify-center bg-slate-100 text-[#0B2B5C] font-bold text-xs mb-1.5 shadow-2xs">
                    @if($image)
                        <img src="{{ $image }}" alt="{{ $name }}" class="w-full h-full object-cover object-top" loading="lazy" />
                    @else
                        <span>{{ $initials }}</span>
                    @endif
                </div>
                <h4 class="font-heading !font-black !text-xs sm:!text-[13px] !text-[#0B2B5C] group-hover:!text-[#E31E24] leading-snug !m-0"
                    style="color: #0B2B5C !important;">{{ $node['name'] }}</h4>
                <div class="my-1 w-5 mx-auto h-px !bg-slate-200" style="background-color: #E2E8F0 !important;"></div>
                <p class="italic !text-[10px] sm:!text-[11px] !font-medium !text-slate-500 leading-tight !m-0"
                   style="color: #64748B !important;">{{ $node['role'] }}</p>
            </div>
        </div>
    @else
        {{-- STYLE 5 (Default): Circle Photo Centered Above (Clean Round Avatar) --}}
        <div class="org-card-wrapper pt-0 flex flex-col items-center select-none text-center group">
            {{-- Big Prominent Circle Avatar with Multi-layer Ring & Ambient Shadow --}}
            <div class="w-18 h-18 sm:w-20 sm:h-20 rounded-full border-2 border-slate-300 group-hover:border-[#0B2B5C] shadow-[0_4px_14px_rgba(0,0,0,0.08)] overflow-hidden flex items-center justify-center bg-gradient-to-b from-slate-50 to-slate-100 text-[#0B2B5C] font-black text-base shrink-0 ring-3 ring-slate-200/90 group-hover:ring-[#0B2B5C]/20 ring-offset-2 ring-offset-white transition-all duration-300 group-hover:scale-105">
                @if($image)
                    <img src="{{ $image }}" alt="{{ $name }}" class="w-full h-full object-cover object-top transition-transform duration-500 group-hover:scale-110" loading="lazy" />
                @else
                    <span class="tracking-wider">{{ $initials }}</span>
                @endif
            </div>

            {{-- Clean Decorated Typography Below --}}
            <div class="mt-2 text-center max-w-[145px] sm:max-w-[160px] flex flex-col items-center">
                <h4 class="font-heading !font-black !text-[10.5px] sm:!text-[11.5px] !text-[#0B2B5C] group-hover:!text-[#E31E24] leading-snug !m-0 transition-colors"
                    style="color: #0B2B5C !important;">{{ $node['name'] }}</h4>

                <span class="inline-flex items-center gap-1 mt-1 px-2.5 py-0.5 rounded-full text-[7.5px] sm:text-[8px] font-semibold tracking-wider uppercase bg-slate-50 group-hover:bg-white text-slate-500 border border-slate-200/90 max-w-[140px] sm:max-w-[155px] truncate shadow-2xs transition-colors"
                      style="color: #64748B !important;">
                    <span class="w-1 h-1 rounded-full bg-slate-400 shrink-0"></span>
                    <span class="truncate">{{ $node['role'] }}</span>
                </span>
            </div>
        </div>
    @endif

    @if($hasChildren)
        @foreach($node['children'] as $child)
            {{-- Crisp vertical connector line between stacked cards --}}
            <div class="w-[2px] {{ $cardStyle === 'avatar_top' ? 'h-5.5' : 'h-4' }} !bg-[#0B2B5C] mx-auto my-0"></div>
            @include('components.about.tree-node', ['node' => $child, 'level' => $level + 1, 'inColumn' => true, 'cardStyle' => $cardStyle])
        @endforeach
    @endif
@else
    {{-- Node in the horizontal tree (CEO, DCEO, DGM) --}}
    <li class="org-tree-item">
        @if($cardStyle === 'floating')
            {{-- STYLE 1: Floating Circular Avatar Clipped to Center Top Edge --}}
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
        @elseif($cardStyle === 'badge')
            {{-- STYLE 2: Integrated Executive Badge --}}
            <div class="org-card-wrapper {{ $hasChildren ? 'has-children' : '' }} pt-0">
                <div class="org-tree-card group relative text-center rounded-xl overflow-hidden transition-all duration-200 select-none shadow-md
                    @if($isRoot)
                        !bg-[#0B2B5C] !border-t-4 !border-t-[#E31E24] !border-x !border-b !border-[#0B2B5C] w-[190px] sm:w-[210px]
                    @elseif($isExecutive)
                        !bg-[#185FA5] !border !border-[#124A82] w-[170px] sm:w-[190px]
                    @else
                        !bg-white !border-2 !border-slate-200 w-[160px] sm:w-[175px]
                    @endif"
                    style="{{ $isRoot ? 'background: #0B2B5C !important; color: #ffffff !important;' : ($isExecutive ? 'background: #185FA5 !important; color: #ffffff !important;' : 'background: #ffffff !important; color: #0f172a !important;') }}">

                    <div class="w-full {{ $isRoot ? 'h-28 sm:h-32' : 'h-24 sm:h-28' }} bg-slate-800 relative overflow-hidden flex items-center justify-center">
                        @if($image)
                            <img src="{{ $image }}" alt="{{ $name }}" class="w-full h-full object-cover object-top" loading="lazy" />
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-slate-700 to-slate-900 flex items-center justify-center text-white/50 font-black text-2xl tracking-wider">
                                {{ $initials }}
                            </div>
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent"></div>
                    </div>

                    <div class="px-3 py-2.5">
                        <h4 class="font-heading !font-black tracking-tight leading-snug !m-0
                            {{ $isRoot || $isExecutive ? '!text-white' : '!text-[#0B2B5C]' }}
                            {{ $isRoot ? '!text-xs sm:!text-sm' : '!text-xs sm:!text-[13px]' }}"
                            style="color: {{ $isRoot || $isExecutive ? '#ffffff' : '#0B2B5C' }} !important;">
                            {{ $node['name'] }}
                        </h4>
                        <div class="my-1.5 w-6 mx-auto h-px {{ $isRoot ? '!bg-[#E31E24]' : ($isExecutive ? '!bg-white/40' : '!bg-slate-200') }}"></div>
                        <p class="italic leading-tight !font-medium !m-0
                            {{ $isRoot ? '!text-[11px] !text-slate-200' : ($isExecutive ? '!text-[10px] sm:!text-[11px] !text-blue-100' : '!text-[10px] sm:!text-[11px] !text-slate-500') }}"
                            style="color: {{ $isRoot ? '#E2E8F0' : ($isExecutive ? '#DBEAFE' : '#64748B') }} !important;">
                            {{ $node['role'] }}
                        </p>
                    </div>
                </div>
            </div>
        @elseif($cardStyle === 'capsule')
            {{-- STYLE 3: Horizontal Capsule --}}
            <div class="org-card-wrapper {{ $hasChildren ? 'has-children' : '' }} pt-0">
                <div class="org-tree-card group relative flex items-center gap-3 rounded-2xl transition-all duration-200 select-none text-left shadow-md
                    @if($isRoot)
                        !bg-gradient-to-r !from-[#0E3A7A] !to-[#0B2B5C] !border-2 !border-[#E31E24] w-[200px] sm:w-[220px] px-3.5 py-2.5
                    @elseif($isExecutive)
                        !bg-gradient-to-r !from-[#1C69B5] !to-[#185FA5] !border !border-white/30 w-[185px] sm:w-[205px] px-3 py-2
                    @else
                        !bg-white !border-2 !border-slate-200 w-[170px] sm:w-[190px] px-2.5 py-2
                    @endif"
                    style="{{ $isRoot ? 'background: linear-gradient(to right, #0E3A7A, #0B2B5C) !important; color: #ffffff !important;' : ($isExecutive ? 'background: linear-gradient(to right, #1C69B5, #185FA5) !important; color: #ffffff !important;' : 'background: #ffffff !important; color: #0f172a !important;') }}">

                    <div class="{{ $isRoot ? 'w-11 h-11' : 'w-9 h-9 sm:w-10 sm:h-10' }} rounded-full border-2 border-white/80 overflow-hidden flex items-center justify-center shrink-0 shadow-sm
                        {{ $isRoot ? 'bg-[#0B2B5C] text-white' : ($isExecutive ? 'bg-[#155394] text-white' : 'bg-slate-100 text-[#0B2B5C]') }}">
                        @if($image)
                            <img src="{{ $image }}" alt="{{ $name }}" class="w-full h-full object-cover object-top" loading="lazy" />
                        @else
                            <span class="font-bold text-xs">{{ $initials }}</span>
                        @endif
                    </div>

                    <div class="min-w-0 flex-1">
                        <h4 class="font-heading !font-black leading-tight truncate !m-0
                            {{ $isRoot ? '!text-xs sm:!text-sm !text-white' : ($isExecutive ? '!text-xs sm:!text-[13px] !text-white' : '!text-xs !text-[#0B2B5C]') }}"
                            style="color: {{ $isRoot || $isExecutive ? '#ffffff' : '#0B2B5C' }} !important;">
                            {{ $node['name'] }}
                        </h4>
                        <p class="italic leading-tight truncate !m-0 mt-0.5
                            {{ $isRoot ? '!text-[10px] sm:!text-[11px] !text-slate-200' : ($isExecutive ? '!text-[10px] !text-blue-100' : '!text-[10px] !text-slate-500') }}"
                            style="color: {{ $isRoot ? '#E2E8F0' : ($isExecutive ? '#DBEAFE' : '#64748B') }} !important;">
                            {{ $node['role'] }}
                        </p>
                    </div>
                </div>
            </div>
        @elseif($cardStyle === 'corporate')
            {{-- STYLE 4: Corporate Tagged Minimalist --}}
            <div class="org-card-wrapper {{ $hasChildren ? 'has-children' : '' }} pt-0">
                <div class="org-tree-card group relative text-center rounded-xl transition-all duration-200 select-none shadow-md
                    @if($isRoot)
                        !bg-white !border-2 !border-[#0B2B5C] w-[190px] sm:w-[210px] px-3.5 pt-2.5 pb-3
                    @elseif($isExecutive)
                        !bg-white !border-2 !border-[#185FA5] w-[175px] sm:w-[195px] px-3 pt-2.5 pb-2.5
                    @else
                        !bg-white !border-2 !border-slate-200 w-[160px] sm:w-[175px] px-3 pt-2 pb-2.5
                    @endif"
                    style="background: #ffffff !important; color: #0f172a !important;">

                    <div class="inline-block px-2.5 py-0.5 rounded text-[9px] font-black tracking-wider uppercase mb-2 border
                        {{ $isRoot ? 'bg-[#E31E24] text-white border-[#E31E24]' : ($isExecutive ? 'bg-[#0B2B5C] text-white border-[#0B2B5C]' : 'bg-slate-100 text-[#0B2B5C] border-slate-200') }}">
                        {{ $isRoot ? 'CHAIRMAN / CEO' : ($isExecutive ? 'EXECUTIVE' : ($node['unitType'] ?? 'DIRECTOR')) }}
                    </div>

                    <div class="{{ $isRoot ? 'w-12 h-12' : 'w-10 h-10 sm:w-11 sm:h-11' }} rounded-lg mx-auto border-2 {{ $isRoot ? 'border-[#E31E24]' : 'border-slate-200' }} overflow-hidden flex items-center justify-center bg-slate-100 text-[#0B2B5C] font-bold text-xs mb-2 shadow-2xs">
                        @if($image)
                            <img src="{{ $image }}" alt="{{ $name }}" class="w-full h-full object-cover object-top" loading="lazy" />
                        @else
                            <span class="tracking-wider">{{ $initials }}</span>
                        @endif
                    </div>

                    <h4 class="font-heading !font-black !text-xs sm:!text-[13px] !text-[#0B2B5C] group-hover:!text-[#E31E24] leading-snug !m-0"
                        style="color: #0B2B5C !important;">{{ $node['name'] }}</h4>
                    <div class="my-1.5 w-6 mx-auto h-px !bg-slate-200" style="background-color: #E2E8F0 !important;"></div>
                    <p class="italic !text-[10px] sm:!text-[11px] !font-medium !text-slate-500 leading-tight !m-0"
                       style="color: #64748B !important;">{{ $node['role'] }}</p>
                </div>
            </div>
        @else
            {{-- STYLE 5 (Default): Circle Photo Centered Above (Clean Round Avatar) --}}
            <div class="org-card-wrapper {{ $hasChildren ? 'has-children' : '' }} pt-0 flex flex-col items-center select-none text-center group">
                {{-- Big Prominent Circle Avatar with Multi-layer Executive Ring & Shadow --}}
                <div class="{{ $isRoot ? 'w-28 h-28 sm:w-32 sm:h-32 md:w-36 md:h-36 border-[3.5px] border-[#E31E24] shadow-[0_12px_28px_-6px_rgba(227,30,36,0.32)] ring-4 ring-[#E31E24]/25 ring-offset-4' : ($isExecutive ? 'w-22 h-22 sm:w-26 sm:h-26 border-[2.5px] border-[#185FA5] shadow-[0_8px_22px_-4px_rgba(24,95,165,0.25)] ring-4 ring-[#185FA5]/20 ring-offset-3' : 'w-18 h-18 sm:w-20 sm:h-20 border-2 border-slate-300 group-hover:border-[#0B2B5C] shadow-[0_4px_14px_rgba(0,0,0,0.08)] ring-3 ring-slate-200/90 group-hover:ring-[#0B2B5C]/20 ring-offset-2') }} ring-offset-white rounded-full overflow-hidden flex items-center justify-center {{ $isRoot ? 'bg-[#0B2B5C] text-white' : ($isExecutive ? 'bg-[#185FA5] text-white' : 'bg-slate-100 text-[#0B2B5C]') }} font-black {{ $isRoot ? 'text-3xl sm:text-4xl' : ($isExecutive ? 'text-xl sm:text-2xl' : 'text-base sm:text-lg') }} shrink-0 transition-all duration-300 group-hover:scale-105">
                    @if($image)
                        <img src="{{ $image }}" alt="{{ $name }}" class="w-full h-full object-cover object-top transition-transform duration-500 group-hover:scale-110" loading="lazy" />
                    @else
                        <span class="tracking-wider">{{ $initials }}</span>
                    @endif
                </div>

                {{-- Clean Decorated Typography Below --}}
                <div class="mt-2 text-center max-w-[165px] sm:max-w-[195px] flex flex-col items-center">
                    <h4 class="font-heading !font-black tracking-tight leading-snug !m-0
                        {{ $isRoot ? '!text-xs sm:!text-[13px] !text-[#0B2B5C]' : ($isExecutive ? '!text-[11px] sm:!text-xs !text-[#0B2B5C]' : '!text-[10.5px] sm:!text-[11.5px] !text-[#0B2B5C]') }}"
                        style="color: #0B2B5C !important;">
                        {{ $node['name'] }}
                    </h4>

                    @if($isRoot)
                        <div class="w-6 h-[1.5px] rounded-full mx-auto my-1 bg-gradient-to-r from-transparent via-[#E31E24]/70 to-transparent"></div>
                        <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-[8px] sm:text-[8.5px] font-extrabold tracking-widest uppercase bg-gradient-to-r from-red-50 via-red-100/60 to-red-50 text-[#E31E24] border border-red-300/80 shadow-[0_1px_3px_rgba(227,30,36,0.12)]"
                              style="color: #E31E24 !important;">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#E31E24] shrink-0 shadow-2xs"></span>
                            {{ $node['role'] }}
                        </span>
                    @elseif($isExecutive)
                        <div class="w-5 h-[1.5px] rounded-full mx-auto my-1 bg-gradient-to-r from-transparent via-[#185FA5]/60 to-transparent"></div>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[7.5px] sm:text-[8px] font-extrabold tracking-wider uppercase bg-gradient-to-r from-blue-50 via-blue-100/50 to-blue-50 text-[#185FA5] border border-blue-200/80 shadow-2xs"
                              style="color: #185FA5 !important;">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#185FA5] shrink-0 shadow-2xs"></span>
                            {{ $node['role'] }}
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 mt-1 px-2.5 py-0.5 rounded-full text-[7.5px] sm:text-[8px] font-semibold tracking-wider uppercase bg-slate-50 group-hover:bg-white text-slate-500 border border-slate-200/90 max-w-[155px] truncate shadow-2xs transition-colors"
                              style="color: #64748B !important;">
                            <span class="w-1 h-1 rounded-full bg-slate-400 shrink-0"></span>
                            <span class="truncate">{{ $node['role'] }}</span>
                        </span>
                    @endif
                </div>
            </div>
        @endif

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
                                @include('components.about.tree-node', ['node' => $child, 'level' => $level + 1, 'inColumn' => true, 'cardStyle' => $cardStyle])
                            </div>
                        </li>
                    @else
                        @include('components.about.tree-node', ['node' => $child, 'level' => $level + 1, 'inColumn' => false, 'cardStyle' => $cardStyle])
                    @endif
                @endforeach
            </ul>
        @endif
    </li>
@endif
