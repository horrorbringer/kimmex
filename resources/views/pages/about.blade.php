<x-layouts.app title="About Us"
    description="Learn about Kimmex's history, mission, vision, and core values in construction.">

    @push('head')
    <script type="application/ld+json">
    {!! json_encode(['@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => [
        ['@type' => 'ListItem', 'position' => 1, 'name' => __('Home'), 'item' => url('/')],
        ['@type' => 'ListItem', 'position' => 2, 'name' => __('About Us'), 'item' => url('/about')],
    ]], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
    <script type="application/ld+json">
    {!! json_encode(['@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => [
        ['@type' => 'Question', 'name' => 'What is Kimmex Construction?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'Kimmex Construction & Investment Co., Ltd is Cambodia\'s premier construction company with over 25 years of experience. Founded in 1999, Kimmex has completed 150+ projects including government buildings, commercial complexes, and infrastructure across Cambodia.']],
        ['@type' => 'Question', 'name' => 'Where is Kimmex located?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'Kimmex is headquartered in Phnom Penh, Cambodia. The company operates nationwide across all provinces of Cambodia.']],
        ['@type' => 'Question', 'name' => 'How many employees does Kimmex have?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'Kimmex has over 500 team members including engineers, architects, project managers, and skilled construction workers.']],
        ['@type' => 'Question', 'name' => 'What are Kimmex\'s core values?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'Kimmex\'s core values are Safety First (zero-incident policy), Quality Excellence (rigorous QA/QC procedures), Integrity (transparent communication), and Innovation (modern 3D modeling and MEP systems).']],
    ]], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
    @endpush

    @php
        $locale = app()->getLocale();
        $localeKey = $locale === 'kh' ? 'km' : $locale;
    @endphp


    <div id="page-top" x-data="{ selectedMember: null }" @select-member.window="selectedMember = $event.detail" @keydown.escape.window="selectedMember = null" class="bg-white text-titan-navy">

        <!-- Team Member Modal -->
        <div x-show="selectedMember" style="display: none"
            class="fixed inset-0 z-[200] flex items-center justify-center p-4 sm:p-6">
            <div x-show="selectedMember" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="selectedMember = null"
                class="absolute inset-0 bg-titan-navy/95 backdrop-blur-md"></div>
            <div x-show="selectedMember" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-8 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                x-transition:leave-end="opacity-0 translate-y-8 scale-95"
                class="relative bg-white rounded-xl overflow-hidden max-w-4xl w-full shadow-[0_30px_60px_-15px_rgba(0,0,0,0.5)] flex flex-col md:flex-row max-h-[90vh] md:min-h-[500px] overflow-y-auto md:overflow-visible z-10">
                <button @click="selectedMember = null"
                    class="absolute top-4 right-4 md:top-6 md:right-6 z-30 w-10 h-10 bg-white/90 backdrop-blur-sm shadow-xl text-titan-navy hover:bg-titan-red hover:text-white rounded-full transition-all duration-300 flex items-center justify-center group">
                    <x-lucide-x class="w-5 h-5 transition-transform group-hover:rotate-90" />
                </button>
                <div class="w-full md:w-1/2 relative h-[300px] sm:h-[400px] md:h-auto shrink-0 overflow-hidden bg-gray-100 flex items-center justify-center">
                    <template x-if="selectedMember?.image">
                        <img :src="selectedMember.image" class="object-cover object-top w-full h-full" decoding="async" loading="lazy" />
                    </template>
                    <template x-if="!selectedMember?.image">
                        <x-lucide-users class="w-24 h-24 text-gray-300" />
                    </template>
                    <div class="absolute inset-0 bg-gradient-to-t from-titan-navy/60 via-transparent to-transparent"></div>
                </div>
                <div class="w-full md:w-1/2 p-8 md:p-14 flex flex-col relative bg-white">
                    <div class="absolute top-10 right-10 text-[80px] md:text-[120px] font-black text-gray-50 -z-10 select-none leading-none opacity-50">KM</div>
                    <div class="mb-8 md:mb-12 relative">
                        <span class="text-titan-red font-black uppercase tracking-[0.3em] text-[10px] block mb-3" x-text="selectedMember?.role"></span>
                        <h3 class="text-2xl md:text-3xl font-heading font-black text-titan-navy uppercase leading-[1.1] tracking-tighter" x-text="selectedMember?.name"></h3>
                        <div class="w-16 md:w-20 h-1.5 bg-titan-red mt-6 rounded-full"></div>
                    </div>
                    <div class="flex-grow">
                        <h4 class="text-[10px] font-black uppercase tracking-[0.2em] text-titan-navy/30 mb-4 italic">{{ __('Executive Biography') }}</h4>
                        <p class="text-base md:text-lg leading-relaxed text-titan-navy/80"
                            x-text="selectedMember?.bio || 'An integral part of KIM MEX Construction bringing specialized expertise.'"></p>
                    </div>
                </div>
            </div>
        </div>


        <!-- === HERO SECTION === -->
        <section class="relative h-[380px] sm:h-[420px] md:h-[500px] flex items-end overflow-hidden bg-titan-navy">
            <div class="absolute inset-0">
                <img src="{{ $aboutHeroImageUrl }}" alt="{{ __('About Kimmex') }}" class="w-full h-full object-cover" loading="eager" decoding="async" fetchpriority="high" />
                <div class="absolute inset-0 bg-gradient-to-t from-titan-navy/90 via-titan-navy/40 to-titan-navy/20"></div>
                <div class="absolute inset-0 bg-gradient-to-r from-titan-navy/60 via-transparent to-transparent"></div>
            </div>

            <div class="relative z-20 w-full max-w-[1200px] mx-auto px-5 sm:px-6 pb-10 sm:pb-14 md:pb-20">
                {{-- Breadcrumb --}}
                <nav class="flex items-center gap-2 text-[11px] sm:text-xs text-white/60 mb-4 sm:mb-6" aria-label="Breadcrumb">
                    <a href="/" class="hover:text-white transition-colors">{{ __('Home') }}</a>
                    <x-lucide-chevron-right class="w-3 h-3 text-white/30" />
                    <span class="text-white font-semibold">{{ __('About Us') }}</span>
                </nav>

                <h1 class="font-heading font-[900] !text-white leading-[1.05] tracking-tight uppercase mb-4 sm:mb-5 text-[1.5rem] sm:text-[clamp(2rem,5vw,3.5rem)]" style="color: #FFFFFF !important;">
                    {{ __('BUILDING') }}
                    <span class="text-titan-red" style="color: var(--primary-color, #E31E24) !important;">{{ __('CAMBODIA\'S FUTURE') }}</span>
                </h1>

                <p class="text-white/70 text-sm sm:text-base md:text-lg max-w-xl leading-relaxed">
                    {{ __('Over 25 years of precision engineering, trusted partnerships, and landmark projects across the Kingdom.') }}
                </p>
            </div>
        </section>


        <!-- === STATS BAR === -->
        <section class="relative z-30 -mt-1">
            <div class="max-w-[1200px] mx-auto px-6">
                <div class="bg-white rounded-xl shadow-[0_20px_60px_-15px_rgba(0,0,0,0.12)] border border-gray-100 grid grid-cols-2 md:grid-cols-4 divide-x divide-gray-100">
                    @php
                        $stats = [
                            ['value' => 25, 'suffix' => '+', 'label' => __('Years Experience'), 'icon' => 'calendar'],
                            ['value' => 150, 'suffix' => '+', 'label' => __('Projects Completed'), 'icon' => 'building-2'],
                            ['value' => 500, 'suffix' => '+', 'label' => __('Team Members'), 'icon' => 'users'],
                            ['value' => 98, 'suffix' => '%', 'label' => __('Client Satisfaction'), 'icon' => 'heart'],
                        ];
                    @endphp
                    @foreach($stats as $stat)
                        <div x-data="{ count: 0, target: {{ $stat['value'] }}, shown: false }"
                            x-intersect.once="shown = true; let steps = 50; let step = target / steps; let c = 0; let timer = setInterval(() => { c += step; if (c >= target) { count = target; clearInterval(timer); } else { count = Math.floor(c); } }, 1500 / steps);"
                            class="flex flex-col items-center justify-center py-5 sm:py-8 md:py-10 px-2 sm:px-4 text-center group hover:bg-gray-50/50 transition-colors first:rounded-l-xl last:rounded-r-xl">
                            <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg bg-titan-red/10 flex items-center justify-center mb-2 sm:mb-3 group-hover:bg-titan-red/20 transition-colors">
                                @if($stat['icon'] === 'calendar')
                                    <x-lucide-calendar class="w-4 h-4 sm:w-5 sm:h-5 text-titan-red" />
                                @elseif($stat['icon'] === 'building-2')
                                    <x-lucide-building-2 class="w-4 h-4 sm:w-5 sm:h-5 text-titan-red" />
                                @elseif($stat['icon'] === 'users')
                                    <x-lucide-users class="w-4 h-4 sm:w-5 sm:h-5 text-titan-red" />
                                @elseif($stat['icon'] === 'heart')
                                    <x-lucide-heart class="w-4 h-4 sm:w-5 sm:h-5 text-titan-red" />
                                @endif
                            </div>
                            <div class="text-2xl sm:text-3xl md:text-4xl font-black text-titan-navy mb-1 tabular-nums">
                                <span x-text="count">0</span><span class="text-titan-red">{{ $stat['suffix'] }}</span>
                            </div>
                            <div class="text-[9px] sm:text-xs uppercase tracking-wider text-titan-navy/50 font-bold leading-tight">{{ $stat['label'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>


        <!-- === WHO WE ARE === -->
        <section id="profile" class="py-14 sm:py-20 md:py-28 px-5 sm:px-6 bg-white overflow-hidden">
            <div class="max-w-[1200px] mx-auto grid lg:grid-cols-2 gap-12 lg:gap-20 items-center">

                <!-- Left: Image Grid -->
                <div class="relative" x-data="{ shown: false }" x-intersect.once="shown = true">
                    <div :class="shown ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-12'"
                        class="grid grid-cols-2 gap-3 md:gap-4 transition-all duration-1000 max-w-[520px] mx-auto lg:mx-0">
                        <div class="space-y-3 md:space-y-4">
                            <div class="aspect-[4/5] rounded-xl overflow-hidden shadow-lg">
                                <img src="{{ $aboutSectionImages[0] }}" alt="" class="object-cover w-full h-full hover:scale-105 transition-transform duration-700" loading="lazy" decoding="async" />
                            </div>
                            <div class="aspect-square rounded-xl overflow-hidden shadow-lg">
                                <img src="{{ $aboutSectionImages[1] }}" alt="" class="object-cover w-full h-full hover:scale-105 transition-transform duration-700" loading="lazy" decoding="async" />
                            </div>
                        </div>
                        <div class="space-y-3 md:space-y-4 pt-8 md:pt-12">
                            <div class="aspect-square rounded-xl overflow-hidden shadow-lg">
                                <img src="{{ $aboutSectionImages[2] }}" alt="" class="object-cover w-full h-full hover:scale-105 transition-transform duration-700" loading="lazy" decoding="async" />
                            </div>
                            <div class="aspect-[4/5] rounded-xl overflow-hidden shadow-lg relative">
                                <img src="{{ $aboutSectionImages[3] }}" alt="" class="object-cover w-full h-full hover:scale-105 transition-transform duration-700" loading="lazy" decoding="async" />
                                <!-- Experience Badge -->
                                <div class="absolute bottom-4 right-4 bg-titan-red text-white p-4 md:p-5 rounded-xl shadow-[0_10px_30px_rgba(227,30,36,0.35)] flex flex-col items-center justify-center">
                                    <span class="text-2xl md:text-3xl font-black leading-none">25+</span>
                                    <span class="text-[9px] font-bold uppercase tracking-wider mt-1">{{ __('Years') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Text Content -->
                <div>

                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-[2px] bg-titan-red"></div>
                        <span class="text-titan-red font-bold uppercase tracking-[0.2em] text-xs">{{ __('WHO WE ARE') }}</span>
                    </div>

                    <h2 class="text-3xl md:text-4xl font-heading font-black text-titan-navy leading-tight mb-6 tracking-tight">
                        {{ $tagline }}
                    </h2>

                    <p class="text-gray-500 text-base md:text-lg leading-relaxed mb-10 whitespace-pre-line">{{ $brand['company_story'] ?? __("With over 25 years of experience, we have established ourselves as Cambodia's most trusted construction partner, delivering projects that stand the test of time and elevate communities.") }}</p>


                    <!-- Vision / Mission / Strategy Accordion -->
                    <div class="space-y-4" x-data="{ active: null }">
                        @php
                            $mvg_items = [
                                ['id' => 'vision', 'icon' => 'eye', 'title' => __('Our Vision'), 'desc' => $brand['vision'] ?? __('To be the most trusted and innovative construction partner in Cambodia.')],
                                ['id' => 'mission', 'icon' => 'flag', 'title' => __('Our Mission'), 'desc' => $brand['mission'] ?? __('To bridge the gap between concept and reality through exceptional engineering and safety.')],
                                ['id' => 'goal', 'icon' => 'target', 'title' => __('Our Strategy'), 'desc' => $brand['goal'] ?? __('To maintain long-term leadership in the Cambodian market through talent development and CMS investment.')],
                            ];
                        @endphp

                        @foreach($mvg_items as $item)
                        <div class="relative z-0 border border-gray-200 rounded-xl overflow-visible transition-colors duration-200 hover:border-gray-300"
                             :class="active === '{{ $item['id'] }}' ? 'z-20 bg-white shadow-lg shadow-titan-navy/10' : 'bg-gray-50'"
                             @mouseenter="active = '{{ $item['id'] }}'"
                             @mouseleave="active = null"
                             @focusin="active = '{{ $item['id'] }}'"
                             @keydown.escape="active = null"
                             @click="active = (active === '{{ $item['id'] }}' ? null : '{{ $item['id'] }}')"
                             role="button"
                             tabindex="0"
                             :aria-expanded="active === '{{ $item['id'] }}'">
                            <div class="flex items-center gap-4 p-4 md:p-5 cursor-pointer">
                                <div class="w-11 h-11 rounded-lg flex items-center justify-center shrink-0 transition-all duration-300"
                                     :class="active === '{{ $item['id'] }}' ? 'bg-titan-red text-white shadow-md shadow-titan-red/20' : 'bg-titan-red/10 text-titan-red'">
                                    @if($item['icon'] === 'eye')
                                        <x-lucide-eye class="w-5 h-5" />
                                    @elseif($item['icon'] === 'flag')
                                        <x-lucide-flag class="w-5 h-5" />
                                    @elseif($item['icon'] === 'target')
                                        <x-lucide-target class="w-5 h-5" />
                                    @endif
                                </div>
                                <div class="flex-grow min-w-0">
                                    <h3 class="text-base font-bold text-titan-navy transition-colors"
                                        :class="active === '{{ $item['id'] }}' ? 'text-titan-red' : ''">
                                        {{ $item['title'] }}
                                    </h3>
                                    <p x-show="active !== '{{ $item['id'] }}'" class="text-sm text-gray-400 truncate mt-0.5">
                                        {{ \Illuminate\Support\Str::limit($item['desc'], 60) }}
                                    </p>
                                </div>
                                <div class="w-7 h-7 rounded-full border flex items-center justify-center transition-all duration-300 shrink-0"
                                     :class="active === '{{ $item['id'] }}' ? 'border-titan-red text-titan-red rotate-180' : 'border-gray-200 text-gray-400'">
                                    <x-lucide-chevron-down class="w-4 h-4" />
                                </div>
                            </div>
                            <div x-show="active === '{{ $item['id'] }}'" x-collapse
                                 x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="opacity-0 translate-y-2"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 translate-y-0"
                                 x-transition:leave-end="opacity-0 translate-y-2"
                                 class="relative z-30 bg-white md:absolute md:top-full md:left-0 md:right-0 md:rounded-b-xl md:border md:border-t-0 md:border-gray-200 md:shadow-[0_16px_30px_-20px_rgba(15,23,42,0.45)]">
                                <div class="px-5 pb-5 pt-1 pl-5 sm:pl-[4.5rem] md:pt-3">
                                    <p class="text-gray-500 text-sm md:text-base leading-relaxed whitespace-pre-line">{{ $item['desc'] }}</p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>


        <!-- === CEO MESSAGE === -->
        <section class="relative py-14 sm:py-20 md:py-28 overflow-hidden" style="background: linear-gradient(135deg, #071A33 0%, #0B2B5C 100%);">
            <div class="max-w-[1200px] mx-auto px-5 sm:px-6 relative z-10">
                <div x-data="{ shown: false }" x-intersect.once="shown = true"
                    :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                    class="transition-all duration-1000">

                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-0 items-center">

                        <!-- Left: Photo & Name -->
                        <div class="lg:col-span-4 flex flex-col items-center lg:items-start">
                            <div class="relative w-[180px] sm:w-[240px] md:w-[280px]">
                                {{-- Photo --}}
                                <div class="aspect-[3/4] rounded-2xl overflow-hidden shadow-[0_30px_80px_-20px_rgba(0,0,0,0.5)] ring-1 ring-white/10">
                                    <img src="/images/team-leadership-professional/touch_kim.jpg" alt="{{ $ceoName }}"
                                        class="object-cover object-top w-full h-full transition-all duration-500 hover:scale-[1.02] hover:shadow-xl" loading="lazy" decoding="async" />
                                </div>
                                {{-- Decorative quote badge --}}
                                <div class="absolute -bottom-2 -right-2 sm:-bottom-3 sm:-right-3 w-10 h-10 sm:w-12 sm:h-12 rounded-xl flex items-center justify-center shadow-lg"
                                     style="background: var(--primary-color, #E31E24); color: #fff;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21c3 0 7-1 7-8V5c0-1.25-.756-2.017-2-2H4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2 1 0 1 0 1 1v1c0 1-1 2-2 2s-1 .008-1 1.031V20c0 1 0 1 1 1z"/><path d="M15 21c3 0 7-1 7-8V5c0-1.25-.756-2.017-2-2h-4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2h.75c0 2.25.25 4-2.75 4v3c0 1 0 1 1 1z"/></svg>
                                </div>
                            </div>

                            {{-- Name & title --}}
                            <div class="mt-8 text-center lg:text-left">
                                <h4 class="text-lg font-heading font-black uppercase tracking-tight" style="color: #FFFFFF;">{{ $ceoName }}</h4>
                                <p class="text-xs font-bold uppercase tracking-wider mt-1.5" style="color: rgba(255,255,255,0.5);">{{ __('Founder & Chief Executive Officer') }}</p>
                            </div>
                        </div>

                        <!-- Right: Message -->
                        <div class="lg:col-span-8 lg:pl-12 xl:pl-16">
                            {{-- Label --}}
                            <div class="flex items-center gap-3 mb-8">
                                <div class="w-10 h-[2px]" style="background: var(--primary-color, #E31E24);"></div>
                                <span class="font-bold uppercase tracking-[0.2em] text-xs" style="color: var(--primary-color, #E31E24);">{{ __('Message from the CEO') }}</span>
                            </div>

                            {{-- Large opening quote --}}
                            <div class="mb-4 sm:mb-6" style="color: rgba(255,255,255,0.1);">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 sm:w-12 sm:h-12" viewBox="0 0 24 24" fill="currentColor"><path d="M3 21c3 0 7-1 7-8V5c0-1.25-.756-2.017-2-2H4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2 1 0 1 0 1 1v1c0 1-1 2-2 2s-1 .008-1 1.031V20c0 1 0 1 1 1z"/><path d="M15 21c3 0 7-1 7-8V5c0-1.25-.756-2.017-2-2h-4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2h.75c0 2.25.25 4-2.75 4v3c0 1 0 1 1 1z"/></svg>
                            </div>

                            {{-- Message body --}}
                            <div class="ceo-message-content space-y-4 sm:space-y-5 mb-8 sm:mb-10"
                                 style="color: rgba(255,255,255,0.75); font-size: 0.9rem; line-height: 1.8;">
                                {!! $brand['ceo_message'] ?? '<p>' . __('Construction is not just about concrete and steel. It is about building trust, fostering communities, and leaving a legacy that stands the test of time. At KIMMEX, we are committed to delivering projects that elevate the nation while maintaining the highest standards of safety and quality.') . '</p>' !!}
                            </div>

                            {{-- Signature line --}}
                            <div class="flex items-center gap-5 pt-8" style="border-top: 1px solid rgba(255,255,255,0.08);">
                                <div class="w-12 h-[2px]" style="background: rgba(255,255,255,0.2);"></div>
                                <div>
                                    <p class="font-heading font-bold text-sm" style="color: rgba(255,255,255,0.8);">{{ $ceoName }}</p>
                                    <p class="text-xs italic" style="color: rgba(255,255,255,0.35);">{{ __('Leading with vision since 1999') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Background decorative elements --}}
            <div class="absolute top-0 right-0 w-[500px] h-[500px] opacity-[0.03] pointer-events-none"
                 style="background: radial-gradient(circle, var(--primary-color, #E31E24), transparent 70%);"></div>
            <div class="absolute bottom-0 left-0 w-[400px] h-[400px] opacity-[0.02] pointer-events-none"
                 style="background: radial-gradient(circle, #fff, transparent 70%);"></div>
        </section>

        <style>
            .ceo-message-content p:first-child {
                font-size: 1rem;
                font-weight: 500;
                color: rgba(255,255,255,0.9);
                line-height: 1.75;
            }
            @media (min-width: 640px) {
                .ceo-message-content p:first-child {
                    font-size: 1.2rem;
                }
                .ceo-message-content {
                    font-size: 1.05rem !important;
                    line-height: 1.9 !important;
                }
            }
        </style>


        <!-- === CORE VALUES === -->
        <section class="py-20 md:py-28 px-6 bg-white">
            <div class="max-w-[1200px] mx-auto">
                <div x-data="{ shown: false }" x-intersect.once="shown = true"
                    :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                    class="text-center mb-14 md:mb-20 transition-all duration-1000">
                    <div class="flex items-center justify-center gap-3 mb-5">
                        <div class="w-8 h-[2px] bg-titan-red"></div>
                        <span class="text-titan-red font-bold uppercase tracking-[0.2em] text-xs">{{ __('WHAT DRIVES US') }}</span>
                        <div class="w-8 h-[2px] bg-titan-red"></div>
                    </div>
                    <h2 class="text-3xl md:text-4xl font-heading font-black text-titan-navy tracking-tight">{{ __('Our Core Values') }}</h2>
                </div>

                @php
                    $hasValueImages = collect($aboutData['values'])->contains(fn($v) => !empty($v['image']));
                @endphp

                @if($hasValueImages)
                    {{-- Image-based values display --}}
                    <div class="space-y-6">
                        @foreach($aboutData['values'] as $i => $value)
                            @continue(empty($value['image']))
                            <div x-data="{ shown: false }" x-intersect.once="shown = true"
                                :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                                class="transition-all duration-700 w-full overflow-hidden rounded-xl"
                                style="transition-delay: {{ $i * 100 }}ms">
                                <img src="{{ $value['image'] }}" alt="{{ $value['title'] }}"
                                    class="w-full object-cover transition-all duration-500 hover:scale-[1.02] hover:shadow-xl" decoding="async" loading="lazy" />
                            </div>
                        @endforeach
                    </div>
                @else
                    {{-- Icon-based values grid --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        @foreach($aboutData['values'] as $i => $value)
                            <div x-data="{ shown: false }" x-intersect.once="shown = true"
                                :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                                class="transition-all duration-700 p-6 md:p-8 rounded-xl border border-gray-100 hover:border-titan-red/20 hover:shadow-lg group text-center bg-white"
                                style="transition-delay: {{ $i * 100 }}ms">
                                <div class="w-14 h-14 mx-auto rounded-xl bg-titan-red/10 flex items-center justify-center mb-5 group-hover:bg-titan-red group-hover:text-white text-titan-red transition-all duration-300 group-hover:shadow-md group-hover:shadow-titan-red/20">
                                    @php $iconName = str_replace('lucide-', '', $value['icon']); @endphp
                                    <x-dynamic-component :component="'lucide-' . $iconName" class="w-6 h-6" />
                                </div>
                                <h3 class="font-bold text-titan-navy text-base mb-3">{{ $value['title'] }}</h3>
                                <p class="text-gray-500 text-sm leading-relaxed">{{ $value['content'] }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>


        <style>
            .milestone-list ol {
                list-style: none;
                padding: 0;
                counter-reset: milestone-counter;
            }
            .milestone-list ol li {
                counter-increment: milestone-counter;
                position: relative;
                padding-left: 2rem;
            }
            .milestone-list ol li::before {
                content: counter(milestone-counter) ".";
                position: absolute;
                left: 0;
                color: #E31E24;
                font-weight: 700;
            }
            .md\:text-right .milestone-list ol li {
                padding-left: 0;
                padding-right: 2rem;
            }
            .md\:text-right .milestone-list ol li::before {
                left: auto;
                right: 0;
            }
            .milestone-animate {
                will-change: transform, opacity;
            }
            @media (prefers-reduced-motion: reduce) {
                .milestone-animate,
                .milestone-timeline-progress {
                    transition: none !important;
                    transform: none !important;
                }
            }
        </style>

        <!-- === MILESTONES === -->
        <section id="milestones" x-data="{ timelineVisible: false }" x-intersect.once="timelineVisible = true"
            class="py-20 md:py-28 px-6 bg-gray-50 border-y border-gray-100 overflow-hidden">
            <div class="max-w-[1200px] mx-auto">
                <div x-data="{ shown: false }" x-intersect.once="shown = true"
                    :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-6'"
                    class="text-center mb-16 md:mb-24 transition-all duration-700 ease-out">
                    <div class="flex items-center justify-center gap-3 mb-5">
                        <div class="w-8 h-px bg-titan-red"></div>
                        <span class="text-titan-red font-bold uppercase tracking-[0.2em] text-xs">{{ __('OUR JOURNEY') }}</span>
                        <div class="w-8 h-px bg-titan-red"></div>
                    </div>
                    <h2 class="text-3xl md:text-4xl font-heading font-black text-titan-navy tracking-tight">{{ __('Company Milestones') }}</h2>
                </div>

                <div class="milestone-timeline space-y-12 md:space-y-0 relative">
                    <div class="hidden md:block absolute left-1/2 top-0 bottom-0 w-px bg-gradient-to-b from-transparent via-titan-red/20 to-transparent -translate-x-1/2"></div>
                    <div class="milestone-timeline-progress hidden md:block absolute left-1/2 top-8 bottom-8 w-[2px] bg-gradient-to-b from-titan-red via-titan-red/70 to-titan-red/10 -translate-x-1/2 origin-top transition-transform duration-[1600ms] ease-out"
                        :class="timelineVisible ? 'scale-y-100' : 'scale-y-0'"></div>

                    @foreach($milestones as $idx => $milestone)
                        @php
                            $isEven = $idx % 2 === 0;
                            $hasMilestoneDetail = (bool) ($milestone['has_detail'] ?? false);
                            $isFeaturedMilestone = (bool) ($milestone['is_featured'] ?? false);
                        @endphp
                        <div x-data="{ shown: false, open: false, hasDetail: @js($hasMilestoneDetail) }" x-intersect.once="shown = true"
                            :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10'"
                            class="milestone-animate relative md:grid md:grid-cols-2 md:gap-16 md:py-12 transition-all duration-700 ease-out group/milestone"
                            style="transition-delay: {{ min($idx * 90, 360) }}ms">

                            <div class="hidden md:flex absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 z-10">
                                <div class="w-4 h-4 bg-white border-[3px] border-titan-red rounded-full shadow-sm transition-all duration-300"
                                    :class="open ? 'scale-150 bg-titan-red' : 'group-hover/milestone:scale-125'"></div>
                            </div>

                            <!-- Content Side -->
                            <div class="{{ $isEven ? 'md:text-right md:pr-8' : 'md:col-start-2 md:pl-8' }}">
                                <div class="inline-flex items-center gap-2 bg-titan-red/10 text-titan-red text-sm font-bold px-4 py-1.5 rounded-full mb-4">
                                    <x-lucide-calendar class="w-3.5 h-3.5" />
                                    {{ $milestone['year'] }}
                                </div>
                                @if($isFeaturedMilestone)
                                    <span class="ml-2 inline-flex items-center gap-1.5 rounded-full bg-amber-100 px-3 py-1.5 text-[10px] font-black uppercase tracking-[0.14em] text-amber-800">
                                        <x-lucide-star class="h-3 w-3 fill-current" />
                                        {{ __('Key milestone') }}
                                    </span>
                                @endif
                                <h3 class="text-xl md:text-2xl font-heading font-black text-titan-navy mb-3 tracking-tight">
                                    {{ $milestone['title'] }}
                                </h3>
                                <div class="text-gray-500 leading-relaxed text-sm md:text-base
                                    [&>p]:mb-3 [&>ul]:space-y-1.5 [&>ol]:space-y-1.5
                                    [&_li]:text-sm milestone-list">
                                    {!! $milestone['desc'] !!}
                                </div>

                                @if($hasMilestoneDetail)
                                    <button @click="open = !open" class="mt-4 inline-flex items-center gap-2 text-titan-red text-xs font-bold uppercase tracking-wider hover:gap-3 transition-all">
                                        <span x-text="open ? '{{ __('Close') }}' : '{{ __('Read More') }}'"></span>
                                        <x-lucide-chevron-down class="w-3.5 h-3.5 transition-transform" :class="open ? 'rotate-180' : ''" />
                                    </button>
                                    <div x-show="open" x-collapse>
                                        <div class="mt-4 p-5 bg-white rounded-lg border border-gray-100 text-gray-500 text-sm leading-relaxed">
                                            {!! $milestone['detail'] !!}
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Image Side -->
                            <div class="{{ $isEven ? 'md:col-start-2 md:pl-8' : 'md:row-start-1 md:col-start-1 md:pr-8' }} mt-6 md:mt-0">
                                <div class="aspect-[16/10] rounded-xl overflow-hidden shadow-md border border-gray-100 group/img cursor-{{ $hasMilestoneDetail ? 'pointer' : 'default' }}"
                                    @if($hasMilestoneDetail) @click="open = !open" @endif>
                                    <img src="{{ $milestone['image'] }}" alt="{{ $milestone['title'] }}"
                                        class="w-full h-full object-cover transition-transform duration-700 group-hover/img:scale-105" loading="lazy" decoding="async" />
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-14 text-center md:mt-20">
                    <a href="#page-top" class="inline-flex items-center gap-2 rounded-full border border-titan-navy/10 bg-white px-5 py-3 text-xs font-bold uppercase tracking-[0.14em] text-titan-navy shadow-sm transition-all hover:-translate-y-0.5 hover:border-titan-red/30 hover:text-titan-red focus:outline-none focus-visible:ring-4 focus-visible:ring-titan-red/20">
                        <x-lucide-arrow-up class="h-4 w-4" />
                        {{ __('Back to top') }}
                    </a>
                </div>
            </div>
        </section>


        <!-- === ORG CHART === -->
        <section id="leadership" class="py-14 sm:py-20 md:py-28 px-4 sm:px-6 bg-white overflow-hidden">
            <div class="max-w-[1700px] mx-auto">
                @if($orgChartType === 'dynamic')
                <div class="text-center mb-10 sm:mb-16 md:mb-24">
                    <div class="flex items-center justify-center gap-2 sm:gap-3 mb-4 sm:mb-5">
                        <div class="w-6 sm:w-8 h-[2px] bg-titan-red"></div>
                        <span class="text-titan-red font-bold uppercase tracking-[0.15em] sm:tracking-[0.2em] text-[10px] sm:text-xs">{{ __('GOVERNANCE') }}</span>
                        <div class="w-6 sm:w-8 h-[2px] bg-titan-red"></div>
                    </div>
                    <h2 class="text-2xl sm:text-3xl md:text-4xl font-heading font-black text-titan-navy tracking-tight">
                        {{ __('Organization Structure') }}
                    </h2>
                </div>
                @endif

                @if($orgChartType === 'image' && $orgChartImage)
                    <div class="flex justify-center" x-data="{ shown: false }" x-intersect.once="shown = true">
                        <div :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                             class="transition-all duration-1000 w-full max-w-6xl mx-auto">
                            <img src="{{ $orgChartImage }}" alt="{{ __('Organization Structure') }}"
                                 class="w-full h-auto rounded-lg sm:rounded-xl shadow-lg sm:shadow-xl border border-gray-200 transition-all duration-500 hover:scale-[1.02] hover:shadow-2xl" loading="lazy" decoding="async" />
                        </div>
                    </div>
                @elseif($orgChartType === 'pdf' && $orgChartPdf)
                    <div class="flex flex-col items-center gap-4 sm:gap-6" x-data="{ shown: false }" x-intersect.once="shown = true">
                        <div :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                             class="transition-all duration-1000 w-full max-w-5xl mx-auto">
                            <div class="rounded-lg sm:rounded-xl shadow-lg sm:shadow-xl border border-gray-200 overflow-hidden bg-white">
                                <iframe src="{{ $orgChartPdf }}" class="w-full border-0 h-[60vh] sm:h-[70vh] md:h-[80vh] min-h-[400px] sm:min-h-[600px]" title="{{ __('Organization Structure') }}"></iframe>
                            </div>
                            <div class="text-center mt-4 sm:mt-6">
                                <a href="{{ $orgChartPdf }}" target="_blank" download
                                   class="inline-flex items-center gap-2 bg-titan-navy text-white px-5 sm:px-6 py-2.5 sm:py-3 rounded-lg font-bold text-xs sm:text-sm uppercase tracking-wider hover:bg-titan-red transition-colors duration-300 shadow-md">
                                    <x-lucide-download class="w-4 h-4" />
                                    {{ __('Download Organization Chart') }}
                                </a>
                            </div>
                        </div>
                    </div>
                @else
                    <div x-data="{ query: '' }" class="max-w-4xl mx-auto">
                        <div class="mb-6 sm:mb-8 flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between rounded-2xl border border-gray-100 bg-gray-50 p-3 sm:p-4">
                            <label class="relative flex-1 block">
                                <span class="sr-only">{{ __('Search organization') }}</span>
                                <x-lucide-search class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-titan-navy/40" />
                                <input type="search" x-model="query"
                                    @input="window.dispatchEvent(new CustomEvent('org-search', { detail: query }))"
                                    placeholder="{{ __('Search by name, role, or department') }}"
                                    class="w-full rounded-xl border border-gray-200 bg-white py-2.5 pl-10 pr-4 text-sm text-titan-navy placeholder:text-titan-navy/35 focus:border-titan-red focus:outline-none focus:ring-2 focus:ring-titan-red/15" />
                            </label>
                            <div class="flex gap-2">
                                <button type="button" @click="window.dispatchEvent(new CustomEvent('org-expand-all'))"
                                    class="flex-1 sm:flex-none rounded-xl bg-white border border-gray-200 px-3 py-2.5 text-xs font-bold text-titan-navy hover:border-titan-navy/30 transition-colors">
                                    {{ __('Expand all') }}
                                </button>
                                <button type="button" @click="query = ''; window.dispatchEvent(new CustomEvent('org-search', { detail: '' })); window.dispatchEvent(new CustomEvent('org-collapse-all'))"
                                    class="flex-1 sm:flex-none rounded-xl border border-gray-200 px-3 py-2.5 text-xs font-bold text-titan-navy/60 hover:border-titan-navy/30 transition-colors">
                                    {{ __('Collapse') }}
                                </button>
                            </div>
                        </div>

                        <p class="mb-5 text-center text-xs sm:text-sm text-titan-navy/55">
                            {{ __('Browse leadership teams or search for a team member. Select a person to view their profile.') }}
                        </p>

                        <div class="max-w-xl mx-auto mb-8 sm:mb-10">
                            @include('components.about.org-node', ['node' => $orgChart, 'level' => 0, 'small' => true, 'showChildren' => false])
                        </div>

                        @if(! empty($orgChart['children']))
                            <div class="mb-4 flex items-center gap-3">
                                <div class="h-px flex-1 bg-gray-200"></div>
                                <h3 class="text-[10px] sm:text-xs font-black uppercase tracking-[0.2em] text-titan-navy/45">
                                    {{ __('Leadership & Departments') }}
                                </h3>
                                <div class="h-px flex-1 bg-gray-200"></div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5 items-start">
                                @foreach($orgChart['children'] as $child)
                                    <div class="rounded-2xl bg-gray-50/60 p-2 sm:p-3 border border-gray-100">
                                        @include('components.about.org-node', ['node' => $child, 'level' => 1, 'small' => true])
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </section>


        <!-- === QUALITY & SAFETY === -->
        <section id="safety" class="py-20 md:py-28 px-6 bg-gray-50 border-y border-gray-100">
            <div class="max-w-[1200px] mx-auto">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 md:gap-20 items-center">
                    <!-- Left: Content -->
                    <div x-data="{ shown: false }" x-intersect.once="shown = true"
                        :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                        class="transition-all duration-1000">

                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-[2px] bg-titan-red"></div>
                            <span class="text-titan-red font-bold uppercase tracking-[0.2em] text-xs">{{ __('OUR STANDARDS') }}</span>
                        </div>

                        <h2 class="text-3xl md:text-4xl font-heading font-black text-titan-navy mb-6 leading-tight tracking-tight">
                            {{ __('Quality & Safety') }} <span class="text-titan-red">{{ __('First') }}</span>
                        </h2>

                        <p class="text-gray-500 text-base md:text-lg leading-relaxed mb-10">
                            {{ __('We adhere to the highest international standards in construction quality and workplace safety. Every project undergoes rigorous QA/QC protocols to ensure excellence from foundation to finishing.') }}
                        </p>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @php
                                $qualityItems = [
                                    ['icon' => 'shield', 'title' => __('Quality Assurance'), 'desc' => __('Rigorous QA/QC procedures')],
                                    ['icon' => 'award', 'title' => __('Zero Accidents'), 'desc' => __('Safety record policy')],
                                    ['icon' => 'check-circle-2', 'title' => __('100% Compliance'), 'desc' => __('Building code adherence')],
                                    ['icon' => 'clock', 'title' => __('On-Time Delivery'), 'desc' => __('98% completion rate')],
                                ];
                            @endphp
                            @foreach($qualityItems as $qi)
                                <div class="flex items-start gap-4 p-4 bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md hover:border-gray-200 transition-all duration-300">
                                    <div class="w-10 h-10 bg-titan-red/10 rounded-lg flex items-center justify-center text-titan-red shrink-0">
                                        @if($qi['icon'] === 'shield')
                                            <x-lucide-shield class="w-5 h-5" />
                                        @elseif($qi['icon'] === 'award')
                                            <x-lucide-award class="w-5 h-5" />
                                        @elseif($qi['icon'] === 'check-circle-2')
                                            <x-lucide-check-circle-2 class="w-5 h-5" />
                                        @elseif($qi['icon'] === 'clock')
                                            <x-lucide-clock class="w-5 h-5" />
                                        @endif
                                    </div>
                                    <div>
                                        <div class="text-titan-navy font-bold text-sm leading-tight">{{ $qi['title'] }}</div>
                                        <div class="text-gray-400 text-xs mt-1">{{ $qi['desc'] }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Right: Image -->
                    <div x-data="{ shown: false }" x-intersect.once="shown = true"
                        :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                        class="transition-all duration-1000 delay-200 relative">
                        <div class="rounded-2xl overflow-hidden shadow-[0_20px_60px_-15px_rgba(0,0,0,0.12)]">
                            <img src="/images/webp/projects/Thumbnail-6.webp" alt="{{ __('Safety Inspection') }}"
                                class="w-full aspect-[4/3] object-cover transition-all duration-500 hover:scale-[1.02] hover:shadow-xl" loading="lazy" decoding="async" />
                        </div>
                    </div>
                </div>
            </div>
        </section>


        <!-- === CTA SECTION === -->
        <section class="relative py-20 md:py-28 overflow-hidden" style="background-color: #071A33;">
            {{-- Background image with overlay --}}
            <div class="absolute inset-0">
                <img src="{{ $aboutHeroImageUrl }}" alt="" class="w-full h-full object-cover opacity-30" loading="lazy" decoding="async" />
                <div class="absolute inset-0" style="background: linear-gradient(135deg, rgba(7,26,51,0.92), rgba(11,43,92,0.88));"></div>
            </div>

            {{-- Decorative elements --}}
            <div class="absolute top-0 left-0 w-full h-px" style="background: linear-gradient(90deg, transparent, var(--footer-accent, #ED1C24) 50%, transparent);"></div>

            <div class="relative z-10 max-w-[1200px] mx-auto px-5 sm:px-6">
                <div class="grid grid-cols-1 lg:grid-cols-5 gap-10 lg:gap-16 items-center">
                    {{-- Left content --}}
                    <div class="lg:col-span-3">
                        <h2 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-heading font-black leading-[1.1] tracking-tight mb-4 sm:mb-6" style="color: #FFFFFF !important;">
                            {{ __('Let\'s Build Something') }}<br>
                            <span style="color: var(--primary-color, #E31E24) !important;">{{ __('Extraordinary Together') }}</span>
                        </h2>
                        <p class="text-sm sm:text-base md:text-lg leading-relaxed max-w-lg" style="color: rgba(255,255,255,0.65);">
                            {{ __('Whether it\'s a government infrastructure project or a commercial development, our team is ready to deliver excellence.') }}
                        </p>
                    </div>

                    {{-- Right actions --}}
                    <div class="lg:col-span-2 flex flex-col gap-3 sm:gap-4">
                        <a href="/contact"
                            class="group flex items-center justify-between px-5 sm:px-8 py-4 sm:py-5 rounded-xl font-bold uppercase tracking-wider text-xs sm:text-sm transition-all duration-300 shadow-lg hover:shadow-xl"
                            style="background-color: var(--primary-color, #E31E24); color: #FFFFFF;">
                            <div class="flex items-center gap-3">
                                <x-lucide-phone class="w-4 h-4 sm:w-5 sm:h-5" />
                                <span>{{ __('Contact Us') }}</span>
                            </div>
                            <x-lucide-arrow-right class="w-4 h-4 sm:w-5 sm:h-5 group-hover:translate-x-1 transition-transform" />
                        </a>
                        <a href="/projects"
                            class="group flex items-center justify-between px-5 sm:px-8 py-4 sm:py-5 rounded-xl font-bold uppercase tracking-wider text-xs sm:text-sm transition-all duration-300"
                            style="border: 2px solid rgba(255,255,255,0.2); color: #FFFFFF;">
                            <div class="flex items-center gap-3">
                                <x-lucide-folder-open class="w-4 h-4 sm:w-5 sm:h-5" />
                                <span>{{ __('View Our Projects') }}</span>
                            </div>
                            <x-lucide-arrow-right class="w-4 h-4 sm:w-5 sm:h-5 opacity-0 group-hover:opacity-100 group-hover:translate-x-1 transition-all" />
                        </a>
                        <a href="/services"
                            class="group flex items-center justify-between px-5 sm:px-8 py-4 sm:py-5 rounded-xl font-bold uppercase tracking-wider text-xs sm:text-sm transition-all duration-300"
                            style="border: 2px solid rgba(255,255,255,0.1); color: rgba(255,255,255,0.7);">
                            <div class="flex items-center gap-3">
                                <x-lucide-settings class="w-4 h-4 sm:w-5 sm:h-5" />
                                <span>{{ __('Explore Services') }}</span>
                            </div>
                            <x-lucide-arrow-right class="w-4 h-4 sm:w-5 sm:h-5 opacity-0 group-hover:opacity-100 group-hover:translate-x-1 transition-all" />
                        </a>
                    </div>
                </div>
            </div>
        </section>

    </div>
</x-layouts.app>
