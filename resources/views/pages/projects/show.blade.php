@php
    // Use the $slug passed from the router to fetch the project
    $projectDb = \App\Models\Project::where('slug', $slug)->first();

    if ($projectDb) {
        $project = [
            'title' => $projectDb->getTranslation('title', app()->getLocale()),
            'type' => $projectDb->projectCategory ? $projectDb->projectCategory->getTranslation('name', app()->getLocale()) : ($projectDb->category ?: __('Infrastructure')),
            'location' => $projectDb->getTranslation('location', app()->getLocale()),
            'status' => $projectDb->status?->getLabel() ?: __('Completed'),
            'date' => $projectDb->completionDate?->format('F Y') ?: __('Oct 2026'),
            'client' => $projectDb->client ?: __('Ministry of Economy and Finance'),
            'built_area' => $projectDb->scale ?: __('50,000 SQM'),
            'contract_value' => __('Contact for Details'),
            'year' => $projectDb->timeline ?: __('2023 - 2026'),
            'heroImage' => $projectDb->heroImage
                ? (\Illuminate\Support\Str::startsWith($projectDb->heroImage, '/') ? $projectDb->heroImage : \Illuminate\Support\Facades\Storage::url($projectDb->heroImage))
                : '/images/projects/Thumbnail-1.jpg',

            'narrative' => [
                'background' => $projectDb->getTranslation('background', app()->getLocale()) ?: $projectDb->getTranslation('description', app()->getLocale()),
                'objectives' => $projectDb->getTranslation('objectives', app()->getLocale()) ?: __('No specific objectives listed.'),
                'design_concept' => $projectDb->getTranslation('designConcept', app()->getLocale()) ?: __('No architectural concept specified.')
            ],

            'scope' => is_array($projectDb->scopeContributions) ? $projectDb->scopeContributions : [
                __('General Contracting'),
                __('Structural Engineering'),
                __('MEP Systems Integration')
            ],

            'challenges' => [
                [
                    'challenge' => __('High-density urban site constraints.'),
                    'solution' => __('Implemented a just-in-time logistics system for material delivery.')
                ]
            ],

            'images' => $projectDb->images->map(fn($img) => \Illuminate\Support\Str::startsWith($img->path, '/') ? $img->path : \Illuminate\Support\Facades\Storage::url($img->path))->toArray(),
            'related' => \App\Models\Project::where('id', '!=', $projectDb->id)->where('status', $projectDb->status)->take(3)->get()->map(fn($p) => [
                'id' => $p->slug,
                'title' => $p->getTranslation('title', app()->getLocale()),
                'type' => $p->category ?: __('Infrastructure'),
                'image' => $p->heroImage ? (\Illuminate\Support\Str::startsWith($p->heroImage, '/') ? $p->heroImage : \Illuminate\Support\Facades\Storage::url($p->heroImage)) : '/images/projects/Thumbnail-5.jpg'
            ])->toArray()
        ];

        // Fallback for missing images
        if (empty($project['images'])) {
            $project['images'] = ['/images/projects/Thumbnail-2.jpg', '/images/projects/Thumbnail-3.jpg', '/images/projects/Thumbnail-4.jpg'];
        }
    } else {
        // Keep internal fallback for development if DB is empty
        $project = [
            'title' => __('Ministry of Economy & Finance Building Expansion'),
            'type' => __('Government Office Building'),
            'location' => __('Phnom Penh, Cambodia'),
            'status' => __('Completed'),
            'date' => __('Oct 2026'),
            'client' => __('MEF'),
            'built_area' => __('50,000 SQM'),
            'contract_value' => __('$120.5M'),
            'year' => __('2023 - 2026'),
            'heroImage' => '/images/projects/Thumbnail-1.jpg',
            'narrative' => [
                'background' => __('A definitive case study on administrative centralization and public infrastructure integration for the Royal Government of Cambodia.'),
                'objectives' => __('To deliver a state-of-the-art office complex with Grade A specifications, ensuring maximum energy efficiency and seamless integration of governmental systems.'),
                'design_concept' => __('The architectural design focuses on a "Solid Foundation" theme, utilizing heavy reinforced concrete with a glass facade that symbolizes transparency and strength.')
            ],
            'scope' => [__('General Contracting'), __('Structural Engineering'), __('MEP Systems Integration'), __('Interior Fit-out')],
            'challenges' => [['challenge' => __('Strict government security protocols.'), 'solution' => __('Developed a specialized vetting and access control system.')]],
            'images' => ['/images/projects/Thumbnail-2.jpg', '/images/projects/Thumbnail-3.jpg', '/images/projects/Thumbnail-4.jpg'],
            'related' => [
                ['id' => '1', 'title' => __('National Bank HQ'), 'type' => __('Government'), 'image' => '/images/projects/Thumbnail-5.jpg'],
                ['id' => '2', 'title' => __('Khleang Toeuk WTP'), 'type' => __('Infrastructure'), 'image' => '/images/projects/Thumbnail-2.jpg'],
                ['id' => '3', 'title' => __('Mekong River Bank'), 'type' => __('Infrastructure'), 'image' => '/images/projects/Thumbnail-3.jpg']
            ]
        ];
    }
@endphp

<x-layouts.app :title="$project['title'] . ' | Portfolio'" :description="'Kimmex project showcase: ' . $project['title']">

    <div class="bg-gray-50 min-h-screen text-titan-navy selection:bg-titan-red selection:text-white pb-32">

        <!-- === HERO SECTION (Refined Architectural Style) === -->
        <section class="relative w-full lg:min-h-[80vh] flex flex-col lg:flex-row bg-titan-navy border-b border-white/5 overflow-hidden">

            <!-- Left Content Block -->
            <div
                class="w-full lg:w-1/2 pt-40 pb-24 lg:pt-48 lg:pb-24 px-8 lg:px-16 xl:px-24 flex flex-col justify-center relative z-10 bg-titan-navy">

                <div x-data="{ reveal: false }" x-init="setTimeout(() => reveal = true, 100)" class="relative z-10">
                    <!-- Breadcrumbs -->
                    <nav class="flex items-center gap-3 text-[10px] font-bold uppercase tracking-[0.3em] text-white/30 mb-12 transition-all duration-700"
                        :class="reveal ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'">
                        <a href="/" class="hover:text-titan-red transition-colors">{{ __('Home') }}</a>
                        <span class="w-1 h-1 rounded-full bg-white/10"></span>
                        <a href="/projects" class="hover:text-titan-red transition-colors">{{ __('Portfolio') }}</a>
                        <span class="w-1 h-1 rounded-full bg-titan-red"></span>
                        <span class="text-white/60">{{ __('Project Detail') }}</span>
                    </nav>

                    <!-- Project Tag -->
                    <div class="inline-flex items-center gap-3 mb-10 transition-all duration-700 delay-100"
                        :class="reveal ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'">
                        <span class="w-8 h-px bg-titan-red"></span>
                        <span class="text-titan-red text-[11px] font-black uppercase tracking-[0.4em]">
                            {{ $project['type'] }}
                        </span>
                    </div>

                    <!-- Title -->
                    <h1 class="text-2xl md:text-3xl lg:text-4xl font-black text-white leading-[1.1] uppercase tracking-tight mb-8 transition-all duration-1000 delay-300"
                        :class="reveal ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'">
                        {!! str_replace(' & ', ' <span class="text-titan-red">&</span> ', $project['title']) !!}
                    </h1>

                    <div class="flex items-center gap-4 transition-all duration-1000 delay-500"
                        :class="reveal ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'">
                        <x-lucide-map-pin class="w-4 h-4 text-titan-red" />
                        <p class="text-white/70 text-sm font-bold uppercase tracking-widest font-heading">{{ $project['location'] }}
                        </p>
                    </div>
                </div>

                <!-- Subtle Decorative Background Text removed for clarity -->
            </div>

            <!-- Right Image Block -->
            <div class="w-full lg:w-1/2 h-[50vh] lg:h-full relative overflow-hidden group bg-titan-navy">
                <div
                    class="absolute inset-0 bg-gradient-to-r from-titan-navy via-transparent to-transparent z-10 pointer-events-none lg:block hidden">
                </div>
                <div class="absolute inset-0 bg-gradient-to-t from-titan-navy/60 via-transparent to-transparent z-10 pointer-events-none"></div>

                <img src="{{ $project['heroImage'] }}" alt="{{ $project['title'] }}"
                    class="w-full h-full object-cover scale-105 group-hover:scale-110 transition-transform duration-[20s] ease-out brightness-90" />

                <!-- Refined Scale Badge -->
                <div class="absolute bottom-8 right-8 lg:bottom-12 lg:right-12 z-20 backdrop-blur-3xl bg-white/5 py-6 px-8 border border-white/10 hidden md:block group/scale transition-all duration-500 hover:bg-white/10 rounded-xl"
                    x-data="{ reveal: false }" x-init="setTimeout(() => reveal = true, 1000)"
                    :class="reveal ? 'opacity-100 translate-x-0' : 'opacity-0 translate-x-12'">
                    <div
                        class="text-[10px] font-black text-titan-red uppercase tracking-[0.4em] mb-3 border-b border-titan-red/20 pb-3 flex items-center justify-between">
                        <span>Project Scale</span>
                        <x-lucide-maximize-2 class="w-3 h-3 group-hover/scale:scale-125 transition-transform" />
                    </div>
                    <div class="text-white text-xl lg:text-2xl font-black uppercase tracking-tight group-hover:text-titan-red transition-colors duration-500">{{ $project['built_area'] }}
                    </div>
                </div>
            </div>
        </section>

        <!-- === METRIC BAND === -->
        <div
            class="bg-white/90 backdrop-blur-2xl border border-white/20 py-16 px-8 lg:px-20 shadow-[0_32px_128px_-16px_rgba(0,0,0,0.1)] relative z-20 -mt-10 lg:-mt-16 mx-4 lg:mx-20 rounded-3xl flex justify-center group/band transition-all duration-700 hover:shadow-[0_48px_160px_-16px_rgba(0,0,0,0.15)]">
            <div class="w-full grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-12 lg:gap-8 gap-y-16">
                @php
                    $metrics = [
                        ['icon' => 'lucide-users', 'label' => 'Project Owner', 'value' => $project['client']],
                        ['icon' => 'lucide-wallet', 'label' => 'Contract Value', 'value' => $project['contract_value']],
                        ['icon' => 'lucide-activity', 'label' => 'Current Status', 'value' => $project['status']],
                        ['icon' => 'lucide-clock', 'label' => 'Total Timeline', 'value' => $project['year']],
                        ['icon' => 'lucide-calendar-check', 'label' => 'Handover', 'value' => $project['date']],
                    ];
                @endphp
                @foreach($metrics as $metric)
                    <div class="group-item relative">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="p-2 rounded-lg bg-titan-red/5 text-titan-red/50 group-hover/band:bg-titan-red/10 group-hover/band:text-titan-red transition-all duration-500">
                                <x-dynamic-component :component="$metric['icon']" class="w-4 h-4" />
                            </div>
                            <div
                                class="text-[10px] font-black uppercase tracking-[0.4em] text-titan-navy/30 group-hover/band:text-titan-navy/50 transition-colors">
                                {{ __($metric['label']) }}</div>
                        </div>
                        <div
                            class="text-xs md:text-sm font-black uppercase tracking-tight text-titan-navy border-l-2 border-titan-red pl-4 leading-tight group-hover/band:translate-x-2 transition-transform duration-500">
                            {{ $metric['value'] }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- === DEEP NARRATIVE SECTION === -->
        <section class="py-40 px-8 xl:px-0 max-w-[1400px] mx-auto overflow-hidden">
            <div class="flex flex-col lg:flex-row gap-20 xl:gap-32">

                <!-- Sidebar Labels -->
                <div class="lg:w-3/12">
                    <div class="sticky top-40">
                        <div class="flex items-center gap-4 mb-8">
                            <span class="w-8 h-[2px] bg-titan-red"></span>
                            <span class="text-[11px] font-black text-titan-navy/40 uppercase tracking-[0.4em]">{{ __('Insight') }}</span>
                        </div>
                        <h3 class="text-3xl md:text-4xl font-black text-titan-navy uppercase tracking-tighter mb-10 leading-[0.8]">
                            Project<br><span class="text-titan-red">Story</span>
                        </h3>
                        <div class="w-full h-px bg-gray-100 mb-12"></div>
                        <p class="text-xs font-bold text-titan-navy/50 leading-loose max-w-[240px]">
                            {{ __('A deep dive into the engineering excellence and architectural vision delivered for this landmark structure.') }}
                        </p>
                    </div>
                </div>

                <!-- Content Area -->
                <div class="lg:w-9/12 w-full space-y-40">
                    @foreach($project['narrative'] as $key => $content)
                        <div class="relative group" x-data="{ shown: false }" x-intersect.once="shown = true"
                            :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'"
                            class="transition-all duration-1000">
                            <!-- Background Number - Fainter & Repositioned -->
                            <div
                                class="absolute -top-12 right-0 text-[100px] font-black text-titan-navy/[0.02] select-none pointer-events-none transition-all duration-1000 transform group-hover:text-titan-red/[0.03] group-hover:translate-x-4">
                                0{{ $loop->iteration }}
                            </div>
                            
                            <div class="relative z-10 group-hover:translate-x-4 transition-transform duration-700">
                                <div class="flex items-center gap-4 mb-8">
                                    <div class="w-2 h-2 rounded-full bg-titan-red scale-0 group-hover:scale-100 transition-transform duration-500"></div>
                                    <h2 class="text-xl md:text-2xl font-black text-titan-navy uppercase tracking-tighter">
                                        {{ __(str_replace('_', ' ', $key)) }}
                                    </h2>
                                </div>
                                <p class="text-base md:text-lg text-titan-navy/70 leading-loose font-normal selection:bg-titan-red selection:text-white max-w-4xl">
                                    {{ $content }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- === FULL WIDTH MID-PAGE BREAK === -->
        <div class="w-full h-[60vh] lg:h-[80vh] relative overflow-hidden group mb-32 bg-titan-navy">
            <img src="{{ $project['images'] ? $project['images'][0] : $project['heroImage'] }}"
                class="w-full h-full object-cover scale-100 group-hover:scale-105 transition-transform duration-[20s]"
                alt="Project Highlight" />
            <div class="absolute inset-0 bg-titan-navy/50 mix-blend-multiply"></div>
            <div class="absolute inset-0 flex flex-col items-center justify-center text-center p-8">
                <div class="max-w-4xl transform group-hover:scale-105 transition-transform duration-1000">
                    <h3
                        class="text-2xl md:text-4xl lg:text-5xl font-black text-white uppercase tracking-tighter mb-10 leading-none drop-shadow-2xl">
                        PRECISION AT <br><span class="text-titan-red italic">ANY SCALE.</span>
                    </h3>
                    <div class="flex items-center justify-center gap-6">
                        <div class="w-20 h-px bg-titan-red/50"></div>
                        <span class="text-[10px] font-black uppercase tracking-[0.6em] text-white/60">The Kimmex
                            Standard Deliverable</span>
                        <div class="w-20 h-px bg-titan-red/50"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- === TECHNICAL DATASHEETS === -->
        <section class="max-w-[1400px] mx-auto px-8 xl:px-0 mb-40">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">

                <!-- Scope Section -->
                <div
                    class="bg-white p-12 lg:p-20 shadow-2xl border border-gray-50 flex flex-col group hover:border-titan-red/10 transition-colors">
                    <header class="mb-16">
                        <div class="text-[9px] font-black text-titan-red uppercase tracking-[0.5em] mb-4">Implementation
                            Scope</div>
                        <h3 class="text-xl md:text-2xl font-black text-titan-navy uppercase tracking-tighter leading-none">
                            Engineering <br>Services</h3>
                    </header>

                    <div class="space-y-3 flex-1">
                        @foreach($project['scope'] as $index => $item)
                            <div
                                class="flex items-center justify-between p-6 bg-gray-50 border-l border-titan-navy/10 hover:bg-titan-navy hover:text-white hover:border-titan-red transition-all duration-300 group/list">
                                <span class="text-xs font-black uppercase tracking-widest">{{ $item }}</span>
                                <span
                                    class="text-[10px] font-black text-titan-red group-hover/list:text-white">0{{ $index + 1 }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Resolution Section -->
                <div class="bg-[#1a1a2e] p-12 lg:p-20 shadow-2xl relative overflow-hidden group">
                    <div
                        class="absolute top-0 right-0 w-64 h-64 bg-titan-red/5 rounded-full blur-[100px] pointer-events-none">
                    </div>

                    <header class="mb-16 relative z-10">
                        <div class="text-[9px] font-black text-titan-red uppercase tracking-[0.5em] mb-4">Strategic
                            Resolution</div>
                        <h3 class="text-xl md:text-2xl font-black text-white uppercase tracking-tighter leading-none">Technical
                            <br>Resilience</h3>
                    </header>

                    <div class="space-y-12 relative z-10">
                        @foreach($project['challenges'] as $node)
                            <div class="flex items-start gap-8 group/chal">
                                <div class="pt-2">
                                    <div
                                        class="w-1.5 h-1.5 bg-titan-red rounded-full group-hover/chal:scale-[2] transition-transform">
                                    </div>
                                </div>
                                <div>
                                    <h4
                                        class="text-base font-bold text-white uppercase tracking-tight mb-2 group-hover/chal:text-titan-red transition-colors">
                                        {{ $node['challenge'] }}</h4>
                                    <p
                                        class="text-white/40 text-sm leading-loose group-hover/chal:text-white/70 transition-colors">
                                        {{ $node['solution'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <!-- === PROJECTS GALLERY === -->
        <section class="max-w-[1400px] mx-auto px-8 xl:px-0 mb-40">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6 mb-16 border-b border-gray-100 pb-12">
                <div>
                    <div class="flex items-center gap-3 mb-4 text-titan-red font-black text-[11px] uppercase tracking-[0.4em]">
                        <span class="w-6 h-[2px] bg-titan-red"></span>
                        {{ __('Visual Capture') }}
                    </div>
                    <h3 class="text-2xl md:text-3xl font-black text-titan-navy uppercase tracking-tighter leading-none">{{ __('Projects') }}
                        <br><span class="text-titan-red">{{ __('Gallery') }}</span></h3>
                </div>
                <div class="bg-gray-50 px-6 py-4 rounded-xl border border-gray-100">
                    <span class="text-[12px] font-black text-titan-navy/40 uppercase tracking-[0.2em]">
                        <span class="text-titan-red">{{ count($project['images']) }}</span> {{ __('Media Files Captured') }}
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                @foreach($project['images'] as $index => $img)
                    <div
                        class="relative aspect-[4/3] bg-gray-100 overflow-hidden group rounded-2xl shadow-sm hover:shadow-xl transition-all duration-500">
                        <img src="{{ $img }}"
                            class="w-full h-full object-cover transition-transform duration-[12s] scale-100 group-hover:scale-110" />
                        <div class="absolute inset-0 ring-1 ring-inset ring-black/5 rounded-2xl pointer-events-none"></div>
                        <div
                            class="absolute inset-0 bg-titan-navy/0 group-hover:bg-titan-navy/10 transition-colors duration-500 pointer-events-none">
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <!-- === SIMILAR PROJECTS === -->
        @if(count($project['related']) > 0)
            <section class="max-w-[1400px] mx-auto px-8 xl:px-0 mb-40">
                <div class="flex items-center gap-4 mb-16">
                    <span class="w-12 h-1 bg-titan-red"></span>
                    <h2 class="text-3xl md:text-5xl font-black text-titan-navy uppercase tracking-tight">{{ __('Similar') }} <span class="text-titan-red">{{ __('Projects') }}</span></h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($project['related'] as $rel)
                    <a href="/projects/{{ $rel['id'] }}" class="group block cursor-pointer">
                        <div class="relative h-[450px] overflow-hidden mb-8 rounded-2xl shadow-sm shadow-black/5 hover:shadow-2xl hover:shadow-black/10 transition-all duration-700">
                            <img src="{{ $rel['image'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-[15s]" alt="{{ $rel['title'] }}" />
                            <div class="absolute inset-0 bg-gradient-to-t from-titan-navy via-titan-navy/20 to-transparent opacity-80 group-hover:opacity-90 transition-opacity duration-500"></div>
                            <div class="absolute bottom-8 left-8 right-8 text-white z-10 transition-transform duration-500 overflow-hidden">
                                <div class="text-[10px] font-bold text-titan-red uppercase tracking-[0.2em] mb-2 transform opacity-100 transition-all duration-300">{{ $rel['type'] }}</div>
                                <h3 class="text-2xl font-black uppercase tracking-tight leading-snug mb-4 group-hover:text-titan-red transition-colors">{{ $rel['title'] }}</h3>
                                <div class="flex items-center gap-3 text-[11px] font-black uppercase tracking-[0.2em] text-white/50 group-hover:text-white transition-colors mt-6">
                                    <span>{{ __('View Project') }}</span>
                                    <x-lucide-arrow-right class="w-4 h-4 text-titan-red group-hover:translate-x-2 transition-transform duration-500" />
                                </div>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
            </section>
        @endif

    </div>

</x-layouts.app>