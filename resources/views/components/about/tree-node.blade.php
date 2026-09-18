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
        {{-- STYLE 1 / Template 2: Compact Card Without Floating Circle --}}
        <div class="org-card-wrapper pt-0">
            <div class="org-tree-card group relative text-center rounded-lg bg-white !border !border-slate-200 hover:!border-[#0B2B5C] shadow-2xs px-2 py-1.5 sm:py-2 w-[115px] sm:w-[130px] transition-all hover:shadow-xs select-none"
                 style="background: #ffffff !important; color: #0f172a !important;">
                <h4 class="!font-sans !font-bold !text-[8.5px] sm:!text-[9px] !text-[#0B2B5C] group-hover:!text-[#E31E24] leading-tight !m-0 transition-colors"
                    style="font-family: var(--font-sans), sans-serif !important; color: #0B2B5C !important; font-size: 9px !important; line-height: 1.25 !important;">
                    {{ $node['name'] }}
                </h4>
                <div class="my-1 w-5 mx-auto h-px !bg-slate-200 group-hover:!bg-[#E31E24]/30 transition-colors"
                     style="background-color: #E2E8F0 !important;"></div>
                <p class="!font-sans !text-[7px] sm:!text-[7.5px] !font-medium !text-slate-500 leading-tight !m-0"
                   style="font-family: var(--font-sans), sans-serif !important; color: #64748B !important; font-size: 7.5px !important; line-height: 1.2 !important;">
                    {{ $node['role'] }}
                </p>
            </div>
        </div>
    @elseif($cardStyle === 'badge')
        {{-- STYLE 2 / Template 3: Integrated Executive Badge (3/4 Photo, 1/4 Name & Role) --}}
        <div class="org-card-wrapper pt-0">
            <div class="org-tree-card group relative flex flex-col text-center rounded-lg bg-white !border !border-slate-200 hover:!border-[#0B2B5C] shadow-2xs overflow-hidden w-[105px] sm:w-[118px] h-[130px] sm:h-[145px] transition-all hover:shadow-xs select-none"
                 style="background: #ffffff !important; color: #0f172a !important;">
                {{-- 3/4 Image Section --}}
                <div class="w-full h-3/4 shrink-0 bg-slate-100 relative overflow-hidden flex items-center justify-center">
                    @if($image)
                        <img src="{{ $image }}" alt="{{ $name }}" class="w-full h-full object-cover object-top" loading="lazy" />
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-slate-100 to-slate-200 flex items-center justify-center text-slate-400 font-bold text-base tracking-wider">
                            {{ $initials }}
                        </div>
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-black/25 via-transparent to-transparent"></div>
                </div>
                {{-- 1/4 Name & Role Section --}}
                <div class="w-full h-1/4 shrink-0 flex flex-col justify-center items-center px-1.5 py-0.5 text-center">
                    <h4 class="!font-sans !font-bold !text-[8px] sm:!text-[8.5px] !text-[#0B2B5C] group-hover:!text-[#E31E24] leading-tight !m-0 transition-colors"
                        style="font-family: var(--font-sans), sans-serif !important; color: #0B2B5C !important; font-size: 8.5px !important; line-height: 1.2 !important;">{{ $node['name'] }}</h4>
                    <div class="my-0.5 w-4 mx-auto h-px !bg-slate-200" style="background-color: #E2E8F0 !important;"></div>
                    <p class="!font-sans !text-[6.5px] sm:!text-[7px] !font-medium !text-slate-500 leading-tight !m-0"
                       style="font-family: var(--font-sans), sans-serif !important; color: #64748B !important; font-size: 7px !important; line-height: 1.15 !important;">{{ $node['role'] }}</p>
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
            {{-- Compact Circle Avatar - Clean Single Border --}}
            <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-full border-2 border-slate-300 group-hover:border-[#0B2B5C] shadow-xs overflow-hidden flex items-center justify-center bg-gradient-to-b from-slate-50 to-slate-100 text-[#0B2B5C] font-bold text-xs shrink-0 transition-colors duration-200">
                @if($image)
                    <img src="{{ $image }}" alt="{{ $name }}" class="w-full h-full object-cover object-top" loading="lazy" />
                @else
                    <span class="tracking-wider">{{ $initials }}</span>
                @endif
            </div>

            {{-- Clean Typography Below --}}
            <div class="mt-0.5 text-center max-w-[110px] sm:max-w-[130px]">
                <h4 class="!font-sans !font-bold !text-[8.5px] sm:!text-[9px] !text-[#0B2B5C] group-hover:!text-[#E31E24] leading-tight !m-0 transition-colors"
                    style="font-family: var(--font-sans), sans-serif !important; font-size: 9px !important; line-height: 1.25 !important; color: #0B2B5C !important;">{{ $node['name'] }}</h4>
                <p class="mt-0 !font-sans !text-[7px] sm:!text-[7.5px] !font-medium !text-slate-500 leading-tight !m-0"
                   style="font-family: var(--font-sans), sans-serif !important; font-size: 7.5px !important; line-height: 1.2 !important; color: #64748B !important;">{{ $node['role'] }}</p>
            </div>
        </div>
    @endif

    @if($hasChildren)
        @foreach($node['children'] as $child)
            {{-- Crisp vertical connector line between stacked cards --}}
            <div class="w-[2px] h-2 !bg-[#0B2B5C] mx-auto my-0"></div>
            @include('components.about.tree-node', ['node' => $child, 'level' => $level + 1, 'inColumn' => true, 'cardStyle' => $cardStyle])
        @endforeach
    @endif
@else
    {{-- Node in the horizontal tree (CEO, DCEO, DGM) --}}
    <li class="org-tree-item">
        @if($cardStyle === 'floating')
            {{-- STYLE 1 / Template 2: Compact Corporate Card (No Floating Circle Avatar) --}}
            <div class="org-card-wrapper {{ $hasChildren ? 'has-children' : '' }} pt-0">
                <div class="org-tree-card group relative text-center rounded-lg transition-all duration-200 select-none
                    @if($isRoot)
                        !bg-gradient-to-b !from-[#0E3A7A] !to-[#0B2B5C] !text-white shadow-sm !border-t-[2.5px] !border-t-[#E31E24] !border-x !border-b !border-[#0B2B5C] w-[130px] sm:w-[145px] px-2.5 py-2
                    @elseif($isExecutive)
                        !bg-gradient-to-b !from-[#1C69B5] !to-[#185FA5] !text-white shadow-2xs !border !border-[#124A82] w-[120px] sm:w-[135px] px-2 py-1.5 sm:py-2
                    @else
                        !bg-white !text-slate-900 !border !border-slate-200 hover:!border-[#0B2B5C] w-[110px] sm:w-[125px] px-2 py-1.5 sm:py-2 hover:shadow-2xs
                    @endif"
                    style="{{ $isRoot ? 'background: linear-gradient(to bottom, #0E3A7A, #0B2B5C) !important; color: #ffffff !important;' : ($isExecutive ? 'background: linear-gradient(to bottom, #1C69B5, #185FA5) !important; color: #ffffff !important;' : 'background: #ffffff !important; color: #0f172a !important;') }}">

                    {{-- Person Name --}}
                    <h4 class="!font-sans !font-bold tracking-tight leading-tight !m-0
                        @if($isRoot)
                            !text-[10px] sm:!text-[10.5px] !text-white
                        @elseif($isExecutive)
                            !text-[9px] sm:!text-[9.5px] !text-white
                        @else
                            !text-[8.5px] sm:!text-[9px] !text-[#0B2B5C] group-hover:!text-[#E31E24] transition-colors
                        @endif"
                        style="font-family: var(--font-sans), sans-serif !important; color: {{ $isRoot || $isExecutive ? '#ffffff' : '#0B2B5C' }} !important; font-size: {{ $isRoot ? '10.5px' : ($isExecutive ? '9.5px' : '9px') }} !important; line-height: 1.25 !important;">
                        {{ $node['name'] }}
                    </h4>

                    {{-- Dividing Line --}}
                    <div class="my-1
                        @if($isRoot)
                            w-6 mx-auto h-[1.5px] !bg-[#E31E24] rounded-full
                        @elseif($isExecutive)
                            w-5 mx-auto h-px !bg-white/40
                        @else
                            w-5 mx-auto h-px !bg-slate-200 group-hover:!bg-[#E31E24]/30 transition-colors
                        @endif"
                        style="{{ $isRoot ? 'background-color: #E31E24 !important;' : ($isExecutive ? 'background-color: rgba(255, 255, 255, 0.4) !important;' : 'background-color: #E2E8F0 !important;') }}"></div>

                    {{-- Position / Title --}}
                    <p class="!font-sans leading-tight !m-0
                        @if($isRoot)
                            !text-[7.5px] sm:!text-[8px] !font-semibold !text-slate-200 tracking-wider uppercase
                        @elseif($isExecutive)
                            !text-[7px] sm:!text-[7.5px] !font-medium !text-blue-100 tracking-wider uppercase
                        @else
                            !text-[7px] sm:!text-[7.5px] !font-medium !text-slate-500
                        @endif"
                        style="font-family: var(--font-sans), sans-serif !important; color: {{ $isRoot ? '#E2E8F0' : ($isExecutive ? '#DBEAFE' : '#64748B') }} !important; font-size: {{ $isRoot ? '8px' : '7.5px' }} !important; line-height: 1.2 !important;">
                        {{ $node['role'] }}
                    </p>
                </div>
            </div>
        @elseif($cardStyle === 'badge')
            {{-- STYLE 2 / Template 3: Integrated Executive Badge (3/4 Photo, 1/4 Name & Role) --}}
            <div class="org-card-wrapper {{ $hasChildren ? 'has-children' : '' }} pt-0">
                <div class="org-tree-card group relative flex flex-col text-center rounded-lg overflow-hidden transition-all duration-200 select-none shadow-xs
                    @if($isRoot)
                        !bg-[#0B2B5C] !border-t-[3px] !border-t-[#E31E24] !border-x !border-b !border-[#0B2B5C] w-[125px] sm:w-[138px] h-[155px] sm:h-[170px]
                    @elseif($isExecutive)
                        !bg-[#185FA5] !border !border-[#124A82] w-[115px] sm:w-[128px] h-[140px] sm:h-[155px]
                    @else
                        !bg-white !border !border-slate-200 hover:!border-[#0B2B5C] w-[105px] sm:w-[118px] h-[130px] sm:h-[145px]
                    @endif"
                    style="{{ $isRoot ? 'background: #0B2B5C !important; color: #ffffff !important;' : ($isExecutive ? 'background: #185FA5 !important; color: #ffffff !important;' : 'background: #ffffff !important; color: #0f172a !important;') }}">

                    {{-- 3/4 Image Section --}}
                    <div class="w-full h-3/4 shrink-0 bg-slate-800 relative overflow-hidden flex items-center justify-center">
                        @if($image)
                            <img src="{{ $image }}" alt="{{ $name }}" class="w-full h-full object-cover object-top" loading="lazy" />
                        @else
                            <div class="w-full h-full bg-gradient-to-br {{ $isRoot || $isExecutive ? 'from-slate-700 to-slate-900 text-white/50' : 'from-slate-100 to-slate-200 text-slate-400' }} flex items-center justify-center font-bold text-base sm:text-lg tracking-wider">
                                {{ $initials }}
                            </div>
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-black/35 via-transparent to-transparent"></div>
                    </div>

                    {{-- 1/4 Name and Role Section --}}
                    <div class="w-full h-1/4 shrink-0 flex flex-col justify-center items-center px-1.5 py-0.5 text-center">
                        <h4 class="!font-sans !font-bold tracking-tight leading-tight !m-0
                            @if($isRoot)
                                !text-[9.5px] sm:!text-[10px] !text-white
                            @elseif($isExecutive)
                                !text-[8.5px] sm:!text-[9px] !text-white
                            @else
                                !text-[8px] sm:!text-[8.5px] !text-[#0B2B5C] group-hover:!text-[#E31E24] transition-colors
                            @endif"
                            style="font-family: var(--font-sans), sans-serif !important; color: {{ $isRoot || $isExecutive ? '#ffffff' : '#0B2B5C' }} !important; font-size: {{ $isRoot ? '10px' : ($isExecutive ? '9px' : '8.5px') }} !important; line-height: 1.2 !important;">
                            {{ $node['name'] }}
                        </h4>
                        <div class="my-0.5 w-4 mx-auto h-px {{ $isRoot ? '!bg-[#E31E24]' : ($isExecutive ? '!bg-white/40' : '!bg-slate-200') }}"
                             style="{{ $isRoot ? 'background-color: #E31E24 !important;' : ($isExecutive ? 'background-color: rgba(255, 255, 255, 0.4) !important;' : 'background-color: #E2E8F0 !important;') }}"></div>
                        <p class="!font-sans leading-tight !m-0
                            @if($isRoot)
                                !text-[7px] sm:!text-[7.5px] !font-semibold !text-slate-200 tracking-wider uppercase
                            @elseif($isExecutive)
                                !text-[6.5px] sm:!text-[7px] !font-medium !text-blue-100 tracking-wider uppercase
                            @else
                                !text-[6.5px] sm:!text-[7px] !font-medium !text-slate-500
                            @endif"
                            style="font-family: var(--font-sans), sans-serif !important; color: {{ $isRoot ? '#E2E8F0' : ($isExecutive ? '#DBEAFE' : '#64748B') }} !important; font-size: {{ $isRoot ? '7.5px' : '7px' }} !important; line-height: 1.15 !important;">
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
                {{-- Compact Circle Avatar - Clean Single Border --}}
                <div class="{{ $isRoot ? 'w-18 h-18 sm:w-20 sm:h-20 md:w-22 md:h-22 border-2 border-[#E31E24] shadow-sm' : ($isExecutive ? 'w-14 h-14 sm:w-16 sm:h-16 border-2 border-[#185FA5] shadow-xs' : 'w-11 h-11 sm:w-12 sm:h-12 border-2 border-slate-300 group-hover:border-[#0B2B5C] shadow-xs') }} rounded-full overflow-hidden flex items-center justify-center {{ $isRoot ? 'bg-[#0B2B5C] text-white' : ($isExecutive ? 'bg-[#185FA5] text-white' : 'bg-slate-100 text-[#0B2B5C]') }} font-bold {{ $isRoot ? 'text-xl sm:text-2xl' : ($isExecutive ? 'text-base sm:text-lg' : 'text-xs sm:text-sm') }} shrink-0 transition-colors duration-200">
                    @if($image)
                        <img src="{{ $image }}" alt="{{ $name }}" class="w-full h-full object-cover object-top" loading="lazy" />
                    @else
                        <span class="tracking-wider">{{ $initials }}</span>
                    @endif
                </div>

                {{-- Clean Typography Below --}}
                <div class="mt-0.5 text-center max-w-[125px] sm:max-w-[145px]">
                    <h4 class="!font-sans !font-bold tracking-tight leading-tight !m-0
                        {{ $isRoot ? '!text-[10px] sm:!text-[10.5px]' : ($isExecutive ? '!text-[9px] sm:!text-[9.5px]' : '!text-[8.5px] sm:!text-[9px]') }}"
                        style="font-family: var(--font-sans), sans-serif !important; color: #0B2B5C !important; font-size: {{ $isRoot ? '10.5px' : ($isExecutive ? '9.5px' : '9px') }} !important; line-height: 1.25 !important;">
                        {{ $node['name'] }}
                    </h4>

                    @if($isRoot)
                        <p class="mt-0 !font-sans !text-[7.5px] sm:!text-[8px] !font-semibold !text-[#E31E24] leading-tight !m-0 tracking-wider uppercase"
                           style="font-family: var(--font-sans), sans-serif !important; color: #E31E24 !important; font-size: 8px !important; line-height: 1.2 !important;">
                            {{ $node['role'] }}
                        </p>
                    @elseif($isExecutive)
                        <p class="mt-0 !font-sans !text-[7px] sm:!text-[7.5px] !font-medium !text-[#185FA5] leading-tight !m-0 tracking-wider uppercase"
                           style="font-family: var(--font-sans), sans-serif !important; color: #185FA5 !important; font-size: 7.5px !important; line-height: 1.2 !important;">
                            {{ $node['role'] }}
                        </p>
                    @else
                        <p class="mt-0 !font-sans !text-[7px] sm:!text-[7.5px] !font-medium !text-slate-500 leading-tight !m-0"
                           style="font-family: var(--font-sans), sans-serif !important; color: #64748B !important; font-size: 7.5px !important; line-height: 1.2 !important;">
                            {{ $node['role'] }}
                        </p>
                    @endif
                </div>
            </div>
        @endif

        {{-- Subordinate Branches / Children (True Tree Hierarchy) --}}
        @if($hasChildren)
            <ul class="org-tree-children">
                @foreach($node['children'] as $child)
                    @include('components.about.tree-node', ['node' => $child, 'level' => $level + 1, 'inColumn' => false, 'cardStyle' => $cardStyle])
                @endforeach
            </ul>
        @endif
    </li>
@endif
