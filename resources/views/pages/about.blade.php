<x-layouts.app title="About Us"
    description="Learn about Kimmex's history, mission, vision, and core values in construction.">

    @php
    $aboutData = [
    'story' => __('Since our humble beginnings, KIM MEX Construction has grown into a premier partner for Cambodia\'s
    most critical infrastructure projects. We pride ourselves on engineering excellence, unwavering safety standards,
    and delivering landmark results on time.'),
    'values' => [
    ['title' => __('Safety First'), 'content' => __('We maintain a strict zero-incident policy on all construction
    sites.')],
    ['title' => __('Quality Excellence'), 'content' => __('Utilizing premium materials and rigorous QA workflows.')],
    ['title' => __('Integrity'), 'content' => __('Honest and transparent communication with all our clients.')],
    ['title' => __('Innovation'), 'content' => __('Leveraging the latest in 3D modeling and MEP system architecture.')]
    ]
    ];

    $milestones = [
    ['year' => '2000', 'title' => __('Company Founded'), 'desc' => __('Started as a small dedicated engineering firm.'),
    'image' => '/images/projects/Thumbnail-1.jpg'],
    ['year' => '2010', 'title' => __('First Mega Project'), 'desc' => __('Secured our first major government
    infrastructure contract.'), 'image' => '/images/projects/Thumbnail-2.jpg'],
    ['year' => '2026', 'title' => __('Industry Leaders'), 'desc' => __('Recognized as the top infrastructure firm in the
    Kingdom of Cambodia.'), 'image' => '/images/projects/Thumbnail-3.jpg']
    ];

    $orgChart = [
    'name' => 'Sok Visal',
    'role' => __('Chief Executive Officer'),
    'type' => 'ceo',
    'image' => null,
    'bio' => __('With over 30 years of experience, Sok leads the strategic vision of Kim Mex.'),
    'children' => [
    [
    'name' => 'Chamroeun S.',
    'role' => __('Managing Director'),
    'type' => 'director',
    'image' => null,
    'bio' => __('Focuses on operational excellence and high-level project management.'),
    'children' => [
    ['name' => __('Engineering Dept'), 'role' => __('Department'), 'type' => 'department', 'children' => []],
    ['name' => __('Finance Dept'), 'role' => __('Department'), 'type' => 'department', 'children' => []],
    ['name' => __('HR Dept'), 'role' => __('Department'), 'type' => 'department', 'children' => []]
    ]
    ]
    ]
    ];
    @endphp

    <div x-data="{ selectedMember: null }" class="bg-white min-h-screen text-titan-navy border-t border-gray-100">

        <!-- Modal -->
        <div x-show="selectedMember" style="display: none"
            class="fixed inset-0 z-[200] flex items-center justify-center p-4 sm:p-6">

            <!-- Backdrop -->
            <div x-show="selectedMember" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="selectedMember = null"
                class="absolute inset-0 bg-titan-navy/95 backdrop-blur-md"></div>

            <!-- Content -->
            <div x-show="selectedMember" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-8 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                x-transition:leave-end="opacity-0 translate-y-8 scale-95"
                class="relative bg-white rounded-[2rem] overflow-hidden max-w-4xl w-full shadow-[0_30px_60px_-15px_rgba(0,0,0,0.5)] flex flex-col md:flex-row max-h-[90vh] md:min-h-[500px] overflow-y-auto md:overflow-visible z-10">

                <button @click="selectedMember = null"
                    class="absolute top-4 right-4 md:top-6 md:right-6 z-30 w-10 h-10 bg-white/90 backdrop-blur-sm shadow-xl text-titan-navy hover:bg-titan-red hover:text-white rounded-full transition-all duration-300 flex items-center justify-center group">
                    <x-lucide-x class="w-5 h-5 transition-transform group-hover:rotate-90" />
                </button>

                <!-- Left Image -->
                <div
                    class="w-full md:w-1/2 relative h-[300px] sm:h-[400px] md:h-auto shrink-0 overflow-hidden bg-gray-100 flex items-center justify-center">
                    <template x-if="selectedMember?.image">
                        <img :src="selectedMember.image" class="object-cover object-top w-full h-full" />
                    </template>
                    <template x-if="!selectedMember?.image">
                        <x-lucide-users class="w-24 h-24 text-gray-300" />
                    </template>
                    <div class="absolute inset-0 bg-gradient-to-t from-titan-navy/60 via-transparent to-transparent">
                    </div>
                    <div class="absolute bottom-6 left-6 right-6">
                        <div
                            class="inline-flex items-center gap-2 bg-white/20 backdrop-blur-md border border-white/30 px-3 py-1.5 rounded-full text-white text-[10px] font-black uppercase tracking-[0.2em]">
                            <x-lucide-shield class="text-titan-red animate-pulse w-3 h-3" />
                            {{ __('Verified Leadership') }}
                        </div>
                    </div>
                </div>

                <!-- Right Content -->
                <div class="w-full md:w-1/2 p-8 md:p-14 flex flex-col relative bg-white">
                    <div
                        class="absolute top-10 right-10 text-[80px] md:text-[120px] font-black text-gray-50 -z-10 select-none leading-none opacity-50 md:opacity-100">
                        KM
                    </div>
                    <div class="mb-8 md:mb-12 relative">
                        <span class="text-titan-red font-black uppercase tracking-[0.3em] text-[10px] block mb-3"
                            x-text="selectedMember?.role"></span>
                        <h3 class="text-3xl md:text-5xl font-heading font-black text-titan-navy uppercase leading-[1.1] tracking-tighter"
                            x-text="selectedMember?.name"></h3>
                        <div class="w-16 md:w-20 h-1.5 bg-titan-red mt-6 rounded-full"></div>
                    </div>
                    <div class="flex-grow">
                        <h4 class="text-[10px] font-black uppercase tracking-[0.2em] text-titan-navy/30 mb-4 italic">{{
                            __('Executive Biography') }}</h4>
                        <div class="space-y-4 md:space-y-6 text-titan-navy/80 leading-relaxed font-medium">
                            <p class="text-base md:text-lg leading-relaxed"
                                x-text="selectedMember?.bio || 'An integral part of KIM MEX Construction bringing specialized expertise.'">
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- HERO SECTION -->
        <section class="relative min-h-[75vh] flex items-center justify-center overflow-hidden bg-titan-navy">
            <div class="absolute inset-0">
                <img src="/images/hero/hero-1.jpg" alt="Construction" class="object-cover w-full h-full" />
                <div class="absolute inset-0 bg-titan-navy/30"></div>
            </div>
            <div class="relative z-10 text-center px-6 max-w-4xl mx-auto pt-20">
                <h1
                    class="text-5xl md:text-7xl lg:text-8xl font-heading font-black text-white mb-6 tracking-tight leading-[0.95]">
                    {{ __('BUILDING') }}<br /><span class="text-titan-red">{{ __('CAMBODIA FUTURE') }}</span>
                </h1>
            </div>
        </section>

        <!-- STATS BAR -->
        <section class="bg-titan-navy py-16 border-t border-white/10">
            <div class="max-w-[1400px] mx-auto px-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                    @php
                    $stats = [
                    ['value' => 25, 'suffix' => '+', 'label' => __('Years Experience')],
                    ['value' => 150, 'suffix' => '+', 'label' => __('Projects Completed')],
                    ['value' => 500, 'suffix' => '+', 'label' => __('Team Members')],
                    ['value' => 98, 'suffix' => '%', 'label' => __('Client Satisfaction')],
                    ];
                    @endphp
                    @foreach($stats as $stat)
                    <div x-data="{ count: 0, target: {{ $stat['value'] }}, shown: false }"
                        x-intersect.once="shown = true; let steps = 60; let step = target / steps; let c = 0; let timer = setInterval(() => { c += step; if (c >= target) { count = target; clearInterval(timer); } else { count = Math.floor(c); } }, 2000 / steps);"
                        class="text-center">
                        <div class="text-5xl md:text-6xl font-heading font-black text-white mb-2">
                            <span x-text="count">0</span>{{ $stat['suffix'] }}
                        </div>
                        <div class="text-sm uppercase tracking-widest text-white/60 font-bold">{{ $stat['label'] }}
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- ABOUT STORY -->
        <section class="py-24 px-6">
            <div class="max-w-[1400px] mx-auto grid lg:grid-cols-2 gap-16 items-center">
                <div x-data="{ shown: false }" x-intersect.once="shown = true"
                    :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                    class="grid grid-cols-2 gap-4 transition-all duration-1000">
                    <div class="space-y-4">
                        <div class="relative h-48 rounded-2xl overflow-hidden shadow-lg"><img
                                src="/images/projects/Thumbnail-1.jpg" class="object-cover w-full h-full" /></div>
                        <div class="relative h-64 rounded-2xl overflow-hidden shadow-lg"><img
                                src="/images/projects/Thumbnail-2.jpg" class="object-cover w-full h-full" /></div>
                    </div>
                    <div class="space-y-4 pt-8">
                        <div class="relative h-64 rounded-2xl overflow-hidden shadow-lg"><img
                                src="/images/projects/Thumbnail-3.jpg" class="object-cover w-full h-full" /></div>
                        <div class="relative h-48 rounded-2xl overflow-hidden shadow-lg"><img
                                src="/images/projects/Thumbnail-4.jpg" class="object-cover w-full h-full" /></div>
                    </div>
                </div>

                <div x-data="{ shown: false }" x-intersect.once="shown = true"
                    :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                    class="transition-all duration-1000 delay-200">
                    <span class="text-titan-red font-bold uppercase tracking-widest text-sm mb-4 block">{{ __('Who We
                        Are') }}</span>
                    <h2 class="text-4xl md:text-5xl font-heading font-black text-titan-navy mb-6">{{ __('Cambodia
                        Premier Partner') }}</h2>
                    <p class="text-titan-navy/60 text-lg leading-relaxed mb-8">{{ $aboutData['story'] }}</p>
                    <div class="space-y-4">
                        @foreach($aboutData['values'] as $val)
                        <div class="p-6 bg-gray-50 rounded-2xl">
                            <h3 class="font-bold text-titan-navy mb-1">{{ $val['title'] }}</h3>
                            <p class="text-sm text-titan-navy/60">{{ $val['content'] }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <!-- WHO WE ARE: MISSION / VISION / GOAL -->
        <section class="py-16 px-6 bg-white">
            <div class="max-w-[1400px] mx-auto">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    @php
                    $mvg = [
                    [
                    'icon' => 'flag',
                    'title' => __('Our Mission'),
                    'desc' => __('To bridge the gap between concept and reality through exceptional engineering and
                    safety.'),
                    'points' => [
                    __('Prioritizing safety in every structural phase.'),
                    __('Implementing sustainable building practices.'),
                    __('Delivering unmatched precision and quality.'),
                    ]
                    ],
                    [
                    'icon' => 'eye',
                    'title' => __('Our Vision'),
                    'desc' => __('To be the most trusted and innovative construction partner in Cambodia.'),
                    'points' => [
                    __('Global recognition for engineering excellence.'),
                    __('Pioneering smart construction technologies.'),
                    __('Shaping the future of urban living.'),
                    ]
                    ],
                    [
                    'icon' => 'target',
                    'title' => __('Our Goal'),
                    'desc' => __('To complete every project on time and within budget with zero-accident safety.'),
                    'points' => [
                    __('Achieving 100% on-time project completion.'),
                    __('Maintaining a strict zero-accident safety record.'),
                    __('Expanding our footprint into renewable infrastructure.'),
                    ]
                    ],
                    ];
                    @endphp
                    @foreach($mvg as $item)
                    <div x-data="{ open: false }"
                        class="p-6 rounded-2xl bg-gray-50 hover:bg-gray-100 transition-all duration-300 cursor-pointer border border-transparent hover:border-titan-red/20"
                        @click="open = !open">
                        <div class="flex gap-5">
                            <div
                                class="w-14 h-14 rounded-xl flex items-center justify-center shrink-0 bg-titan-red/10 text-titan-red">
                                @if($item['icon'] === 'flag')
                                <x-lucide-flag class="w-6 h-6" />
                                @elseif($item['icon'] === 'eye')
                                <x-lucide-eye class="w-6 h-6" />
                                @elseif($item['icon'] === 'target')
                                <x-lucide-target class="w-6 h-6" />
                                @endif
                            </div>
                            <div class="flex-grow">
                                <div class="flex items-center justify-between mb-1">
                                    <h3 class="text-lg font-heading font-bold text-titan-navy">{{ $item['title'] }}</h3>
                                    <x-lucide-chevron-right
                                        class="w-4 h-4 text-titan-red/30 transition-transform duration-300"
                                        ::class="open ? 'rotate-90 text-titan-red' : ''" />
                                </div>
                                <p class="text-titan-navy/50 text-sm leading-relaxed">{{ $item['desc'] }}</p>
                                <div x-show="open" x-collapse class="overflow-hidden">
                                    <div class="pt-4 mt-4 border-t border-gray-200">
                                        <ul class="grid grid-cols-1 gap-2">
                                            @foreach($item['points'] as $point)
                                            <li class="flex items-start gap-2 text-xs font-bold text-titan-navy/60">
                                                <div class="w-1.5 h-1.5 bg-titan-red rounded-full mt-1.5 shrink-0">
                                                </div>
                                                {{ $point }}
                                            </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- CEO MESSAGE -->
        <section class="py-20 px-6 bg-white border-t border-gray-100">
            <div class="max-w-[1000px] mx-auto">
                <div x-data="{ shown: false }" x-intersect.once="shown = true"
                    :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                    class="transition-all duration-1000 bg-gray-50 rounded-[2.5rem] p-8 md:p-12 lg:p-16 border border-gray-100 shadow-sm relative overflow-hidden">
                    <!-- Decorative blurred background element -->
                    <div
                        class="absolute -top-24 -right-24 w-64 h-64 bg-titan-navy/5 rounded-full blur-3xl pointer-events-none">
                    </div>

                    <div class="flex flex-col md:flex-row gap-10 md:gap-16 items-center">

                        <!-- Left: Image -->
                        <div class="w-[200px] md:w-[280px] shrink-0 relative">
                            <div
                                class="aspect-[3/4] w-full rounded-2xl overflow-hidden shadow-lg border-4 border-white">
                                <img src="/images/team-leadership-professional/touch_kim.jpg" alt="Okhna. TOUCH KIM"
                                    class="object-cover object-top w-full h-full bg-titan-navy/5" />
                            </div>
                            <div
                                class="absolute -bottom-4 -right-4 w-16 h-16 bg-titan-red text-white flex items-center justify-center rounded-2xl shadow-lg border-4 border-gray-50 rotate-3 hover:rotate-0 transition-transform cursor-default">
                                <x-lucide-quote class="w-6 h-6" stroke-width="2" />
                            </div>
                        </div>

                        <!-- Right: Message -->
                        <div class="relative z-10 flex-grow">
                            <span class="text-titan-red font-bold uppercase tracking-widest text-xs mb-3 block">{{
                                __('Message From CEO') }}</span>

                            <blockquote
                                class="text-xl md:text-2xl text-titan-navy font-heading font-black leading-relaxed mb-6">
                                &ldquo;{{ __('Construction is not just about concrete and steel. It is about building
                                trust, fostering communities, and leaving a legacy that stands the test of time.')
                                }}&rdquo;
                            </blockquote>
                            <p class="text-titan-navy/60 text-sm md:text-base leading-relaxed mb-8">
                                {{ __('At KIM MEX, we pour our heart into every foundation we lay. Our commitment to
                                excellence ensures that every project we undertake shapes a better, more resilient
                                future for the Kingdom of Cambodia.') }}
                            </p>

                            <div>
                                <div class="text-titan-navy font-black text-lg uppercase mb-1">Okhna. TOUCH KIM</div>
                                <div class="text-titan-red text-[10px] font-bold uppercase tracking-[0.2em]">{{
                                    __('Founder & Chief Executive Officer') }}</div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </section>

        <!-- CORE VALUES -->
        <section class="py-24 px-6 bg-white">
            <div class="max-w-[1400px] mx-auto">
                <div x-data="{ shown: false }" x-intersect.once="shown = true"
                    :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                    class="text-center mb-16 transition-all duration-1000">
                    <span class="text-titan-red font-bold uppercase tracking-widest text-sm mb-4 block">{{ __('What
                        Drives Us') }}</span>
                    <h2 class="text-4xl md:text-5xl font-heading font-black text-titan-navy">{{ __('Our Core Values') }}
                    </h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @php
                    $coreValues = [
                    ['icon' => 'shield', 'title' => __('Integrity'), 'desc' => __('We uphold the highest ethical
                    standards in every project and relationship.')],
                    ['icon' => 'award', 'title' => __('Excellence'), 'desc' => __('We strive for perfection in every
                    beam, brick, and blueprint we deliver.')],
                    ['icon' => 'handshake', 'title' => __('Partnership'), 'desc' => __('We build lasting relationships
                    with clients, partners, and communities.')],
                    ['icon' => 'lightbulb', 'title' => __('Innovation'), 'desc' => __('We embrace new technologies and
                    methods to deliver better solutions.')],
                    ['icon' => 'heart', 'title' => __('Safety First'), 'desc' => __('We prioritize the wellbeing of our
                    team and everyone on our sites.')],
                    ['icon' => 'trending-up', 'title' => __('Growth'), 'desc' => __('We continuously improve and invest
                    in our people and capabilities.')],
                    ];
                    @endphp
                    @foreach($coreValues as $i => $value)
                    <div x-data="{ shown: false }" x-intersect.once="shown = true"
                        :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                        class="transition-all duration-700" style="transition-delay: {{ $i * 100 }}ms">
                        <div
                            class="bg-gray-50 p-8 rounded-2xl hover:bg-titan-navy group transition-all duration-500 h-full">
                            <div
                                class="w-16 h-16 bg-titan-red/10 rounded-2xl flex items-center justify-center text-titan-red mb-6 group-hover:bg-titan-red group-hover:text-white transition-all duration-300">
                                @if($value['icon'] === 'shield')
                                <x-lucide-shield class="w-7 h-7" />
                                @elseif($value['icon'] === 'award')
                                <x-lucide-award class="w-7 h-7" />
                                @elseif($value['icon'] === 'handshake')
                                <x-lucide-handshake class="w-7 h-7" />
                                @elseif($value['icon'] === 'lightbulb')
                                <x-lucide-lightbulb class="w-7 h-7" />
                                @elseif($value['icon'] === 'heart')
                                <x-lucide-heart class="w-7 h-7" />
                                @elseif($value['icon'] === 'trending-up')
                                <x-lucide-trending-up class="w-7 h-7" />
                                @endif
                            </div>
                            <h3
                                class="text-xl font-heading font-bold text-titan-navy mb-3 group-hover:text-white transition-colors">
                                {{ $value['title'] }}</h3>
                            <p class="text-titan-navy/50 leading-relaxed group-hover:text-white/70 transition-colors">{{
                                $value['desc'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- MILESTONES -->
        <section class="py-24 px-6 bg-gray-900 text-white overflow-hidden relative">
            <div class="absolute top-0 left-0 w-full h-24 bg-gradient-to-b from-white to-transparent opacity-10"></div>
            <div class="max-w-[1400px] mx-auto">
                <div class="text-center mb-20">
                    <span class="text-titan-red font-bold uppercase tracking-widest text-sm mb-4 block">{{ __('Our
                        Journey') }}</span>
                    <h2 class="text-4xl md:text-6xl font-heading font-black uppercase tracking-tighter">{{ __('Project
                        Milestones') }}</h2>
                </div>

                <div class="space-y-12 relative">
                    <div class="absolute left-1/2 top-0 bottom-0 w-[1px] bg-white/10 hidden md:block"></div>

                    @foreach($milestones as $idx => $milestone)
                    <div x-data="{ shown: false }" x-intersect.once="shown = true"
                        :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                        class="flex flex-col {{ $idx % 2 === 0 ? 'md:flex-row' : 'md:flex-row-reverse' }} items-center gap-8 md:gap-20 transition-all duration-1000 delay-[{{ $idx * 100 }}ms]">
                        <div class="w-full md:w-1/2">
                            <div
                                class="relative h-64 md:h-80 rounded-2xl overflow-hidden shadow-2xl border-4 border-white/10 {{ $idx % 2 === 0 ? 'md:ml-auto' : 'md:mr-auto' }}">
                                <img src="{{ $milestone['image'] }}" class="object-cover w-full h-full" />
                                <div class="absolute inset-0 bg-gradient-to-t from-gray-900/60 to-transparent"></div>
                                <div class="absolute bottom-6 left-6">
                                    <span class="text-5xl font-black text-white/40">{{ $milestone['year'] }}</span>
                                </div>
                            </div>
                        </div>

                        <div
                            class="hidden md:flex items-center justify-center absolute left-1/2 -translate-x-1/2 w-4 h-4 rounded-full bg-titan-red border-4 border-gray-900 z-10 shadow-[0_0_20px_rgba(200,16,46,0.5)]">
                        </div>

                        <div
                            class="w-full md:w-1/2 text-center {{ $idx % 2 === 0 ? 'md:text-left' : 'md:text-right' }}">
                            <h3
                                class="text-2xl md:text-3xl font-heading font-black text-titan-red mb-4 uppercase tracking-tight">
                                {{ $milestone['title'] }}</h3>
                            <p class="text-white/60 text-lg leading-relaxed mb-6">{{ $milestone['desc'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- ORG CHART -->
        <section id="leadership" class="py-32 px-6 bg-gray-50 overflow-hidden relative">
            <div class="max-w-[1700px] mx-auto relative z-10">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-heading font-black text-[#002B5B] uppercase">{{ __('KIM MEX Organization
                        Structure') }}</h2>
                </div>

                <div class="flex flex-col items-center space-y-16">
                    <x-about.org-node :node="$orgChart" :level="0" />
                </div>
            </div>
        </section>

        <!-- QUALITY & SAFETY -->
        <section id="safety" class="py-24 px-6 bg-titan-navy">
            <div class="max-w-[1400px] mx-auto">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                    <div x-data="{ shown: false }" x-intersect.once="shown = true"
                        :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                        class="transition-all duration-1000">
                        <span class="text-titan-red font-bold uppercase tracking-widest text-sm mb-4 block">{{ __('Our
                            Standards') }}</span>
                        <h2 class="text-4xl md:text-5xl font-heading font-black text-white mb-6 leading-tight">
                            {{ __('Quality & Safety') }} <span class="text-titan-red uppercase">{{ __('First') }}</span>
                        </h2>
                        <p class="text-white/60 text-lg leading-relaxed mb-10">
                            {{ __('We adhere to the highest international standards in construction quality and
                            workplace safety. Every project undergoes rigorous QA/QC protocols to ensure excellence from
                            foundation to finishing.') }}
                        </p>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            @php
                            $qualityItems = [
                            ['icon' => 'shield', 'title' => 'ISO 9001:2015', 'desc' => __('Quality Management
                            Certified')],
                            ['icon' => 'award', 'title' => __('Zero Accidents'), 'desc' => __('Safety record policy')],
                            ['icon' => 'check-circle-2', 'title' => __('100% Compliance'), 'desc' => __('Building code
                            adherence')],
                            ['icon' => 'clock', 'title' => __('On-Time Delivery'), 'desc' => __('98% completion rate')],
                            ];
                            @endphp
                            @foreach($qualityItems as $item)
                            <div class="flex items-start gap-4 p-4 bg-white/5 rounded-xl border border-white/10">
                                <div
                                    class="w-12 h-12 bg-titan-red/20 rounded-lg flex items-center justify-center text-titan-red shrink-0">
                                    @if($item['icon'] === 'shield')
                                    <x-lucide-shield class="w-5 h-5" />
                                    @elseif($item['icon'] === 'award')
                                    <x-lucide-award class="w-5 h-5" />
                                    @elseif($item['icon'] === 'check-circle-2')
                                    <x-lucide-check-circle-2 class="w-5 h-5" />
                                    @elseif($item['icon'] === 'clock')
                                    <x-lucide-clock class="w-5 h-5" />
                                    @endif
                                </div>
                                <div>
                                    <div class="text-white font-bold">{{ $item['title'] }}</div>
                                    <div class="text-white/40 text-sm">{{ $item['desc'] }}</div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div x-data="{ shown: false }" x-intersect.once="shown = true"
                        :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                        class="transition-all duration-1000 delay-200 relative">
                        <img src="/images/projects/Thumbnail-6.jpg" alt="Safety Inspection"
                            class="rounded-2xl shadow-2xl w-full h-auto" />
                        <!-- Floating ISO Card -->
                        <div class="absolute -bottom-6 -left-6 bg-white p-6 rounded-xl shadow-xl hidden md:block">
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-14 bg-green-100 rounded-full flex items-center justify-center">
                                    <x-lucide-check-circle-2 class="text-green-600 w-7 h-7" />
                                </div>
                                <div>
                                    <div class="text-2xl font-black text-titan-navy">ISO</div>
                                    <div class="text-sm text-titan-navy/50">{{ __('9001:2015 Certified') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA SECTION -->
        <section class="py-20 px-6 bg-titan-red">
            <div class="max-w-[1200px] mx-auto text-center" x-data="{ shown: false }" x-intersect.once="shown = true"
                :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                class="transition-all duration-1000">
                <h2 class="text-3xl md:text-5xl font-heading font-black text-white mb-6">{{ __('Ready to Build
                    Together?') }}</h2>
                <p class="text-white/80 text-lg mb-8 max-w-2xl mx-auto">
                    {{ __('Partner with Cambodia\'s leading construction firm. Let\'s create infrastructure that lasts
                    for generations.') }}
                </p>
                <div class="flex flex-wrap justify-center gap-4">
                    <a href="/contact"
                        class="bg-white text-titan-navy px-8 py-4 font-bold uppercase tracking-widest text-sm hover:bg-titan-navy hover:text-white transition-all rounded-lg">
                        {{ __('Contact Us') }}
                    </a>
                    <a href="/projects"
                        class="border-2 border-white text-white px-8 py-4 font-bold uppercase tracking-widest text-sm hover:bg-white hover:text-titan-navy transition-all rounded-lg">
                        {{ __('View Projects') }}
                    </a>
                </div>
            </div>
        </section>

    </div>
</x-layouts.app>