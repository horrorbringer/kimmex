@php
// Dummy project data for display
$project = [
    'title' => __('Ministry of Economy & Finance Building Expansion'),
    'type' => __('Government Office Building'),
    'location' => __('Phnom Penh, Cambodia'),
    'status' => __('Completed'),
    'date' => __('Oct 2026'),
    'client' => __('Ministry of Economy and Finance'),
    'built_area' => __('50,000 SQM'),
    'contract_value' => __('$120.5M'),
    'year' => __('2023 - 2026'),
    'heroImage' => '/images/projects/Thumbnail-1.jpg',
    
    // Structured Narrative sections
    'narrative' => [
        'background' => __('The Ministry of Economy & Finance expansion project was initiated to centralize administrative operations and provide a modern, sustainable environment for governmental functions. Following the rapid growth of the department, a new structural landmark was required to house over 1,500 staff members.'),
        'objectives' => __('To deliver a state-of-the-art office complex with Grade A specifications, ensuring maximum energy efficiency and seamless integration of high-security governmental systems.'),
        'design_concept' => __('The architectural design focuses on a "Solid Foundation" theme, utilizing heavy reinforced concrete with a glass facade that symbolizes transparency and strength. The building layout optimizes natural light through a central atrium.')
    ],
    
    // Scope of Services
    'scope' => [
        __('General Contracting'),
        __('Structural Engineering'),
        __('MEP Systems Integration'),
        __('Interior Fit-out'),
        __('Project Management')
    ],
    
    // Challenges & Solutions
    'challenges' => [
        [
            'challenge' => __('High-density urban site constraints.'),
            'solution' => __('Implemented a just-in-time logistics system for material delivery to minimize traffic disruption.')
        ],
        [
            'challenge' => __('Strict government security protocols.'),
            'solution' => __('Developed a specialized vetting and access control system for all site personnel.')
        ]
    ],
    
    'images' => [
        '/images/projects/Thumbnail-2.jpg',
        '/images/projects/Thumbnail-3.jpg',
        '/images/projects/Thumbnail-4.jpg'
    ],
    'related' => [
        [
            'id' => 'national-bank',
            'title' => __('National Bank HQ'),
            'type' => __('Government Office Building'),
            'image' => '/images/projects/Thumbnail-5.jpg'
        ],
        [
            'id' => 'tax-dept',
            'title' => __('Tax Department Tower'),
            'type' => __('Government Office Building'),
            'image' => '/images/projects/Thumbnail-6.jpg'
        ]
    ]
];
@endphp

<x-layouts.app :title="$project['title'] . ' | Portfolio'" :description="'View detailed information about this Kimmex built legacy.'">

<div class="bg-gray-50 min-h-screen text-titan-navy selection:bg-titan-red selection:text-white pb-32">

    <!-- === EDITORIAL SPLIT HERO === -->
    <section class="relative w-full lg:h-[90vh] flex flex-col lg:flex-row bg-titan-navy border-b-[20px] border-titan-red">
        
        <!-- Left Content Block -->
        <div class="w-full lg:w-5/12 pt-40 pb-24 lg:pb-16 px-8 lg:px-16 xl:px-24 flex flex-col justify-between relative z-10 bg-titan-navy relative">
            
            <div x-data="{ reveal: false }" x-init="setTimeout(() => reveal = true, 300)">
                <!-- Breadcrumbs -->
                <nav class="flex items-center gap-3 text-[9px] font-black uppercase tracking-[0.4em] text-white/40 mb-16 transition-all duration-700" :class="reveal ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'">
                    <a href="/" class="hover:text-titan-red transition-colors">{{ __('Home') }}</a>
                    <span class="w-3 h-px bg-white/20"></span>
                    <a href="/projects" class="hover:text-titan-red transition-colors">{{ __('Portfolio') }}</a>
                    <span class="w-3 h-px bg-titan-red"></span>
                    <span class="text-white">{{ __('Project Detail') }}</span>
                </nav>

                <!-- Project Tag -->
                <div class="inline-flex items-center gap-3 px-4 py-1.5 border border-white/10 text-titan-red text-[9px] font-black uppercase tracking-[0.4em] mb-8 transition-all duration-700 delay-100" :class="reveal ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'">
                    <span class="w-1.5 h-1.5 rounded-full bg-titan-red animate-pulse"></span>
                    {{ $project['type'] }}
                </div>

                <!-- Title -->
                <h1 class="text-4xl md:text-6xl xl:text-[5rem] font-black text-white leading-[0.9] uppercase tracking-tighter mix-blend-difference mb-8 transition-all duration-1000 delay-300" :class="reveal ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'">
                    {!! str_replace(' & ', '<br><span class="text-titan-red">&</span><br>', $project['title']) !!}
                </h1>
                
                <p class="text-titan-navy/40 text-sm xl:text-base font-bold uppercase tracking-widest transition-all duration-1000 delay-500" :class="reveal ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'">{{ $project['location'] }}</p>
            </div>

            <!-- Scroll Indicator -->
            <div class="hidden lg:flex items-center gap-6 mt-20" x-data="{ reveal: false }" x-init="setTimeout(() => reveal = true, 800)" :class="reveal ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'" class="transition-all duration-1000">
                <div class="w-12 h-px bg-titan-red"></div>
                <span class="text-[9px] font-black text-white/40 uppercase tracking-[0.4em]">Explore Legacy</span>
            </div>
            
            <!-- Large Watermark -->
            <div class="absolute -bottom-10 -left-10 text-[20vw] font-black text-white/[0.02] uppercase tracking-tighter select-none pointer-events-none z-[-1] overflow-hidden leading-none">
                KMX
            </div>
        </div>

        <!-- Right Image Block -->
        <div class="w-full lg:w-7/12 h-[50vh] lg:h-full relative overflow-hidden group">
            <div class="absolute inset-0 bg-titan-navy/10 z-10 transition-colors duration-1000 group-hover:bg-transparent pointer-events-none"></div>
            <img src="{{ $project['heroImage'] }}" alt="{{ $project['title'] }}" class="w-full h-full object-cover scale-105 group-hover:scale-100 transition-transform duration-[10s] select-none" />
            
            <!-- Overlay Tech Detail -->
            <div class="absolute bottom-12 right-12 z-20 text-right backdrop-blur-md bg-titan-navy/40 p-6 border border-white/10 hidden md:block" x-data="{ reveal: false }" x-init="setTimeout(() => reveal = true, 1000)" :class="reveal ? 'opacity-100 translate-x-0' : 'opacity-0 translate-x-12'" class="transition-all duration-1000">
                <div class="text-[9px] font-black text-white/60 uppercase tracking-[0.5em] mb-2 border-b border-white/20 pb-2 inline-block">Archived Spec</div>
                <div class="text-white text-xl font-black uppercase tracking-tight">{{ $project['built_area'] }}</div>
            </div>
        </div>
    </section>

    <!-- === METRIC BAND === -->
    <div class="bg-white border-b border-gray-200 py-12 px-8 lg:px-16 shadow-xl relative z-20 -mt-10 lg:-mt-16 mx-4 lg:mx-16 flex justify-center">
        <div class="w-full flex justify-between gap-12 xl:gap-24 overflow-x-auto pb-4 snap-x">
            @php
                $metrics = [
                    ['label' => 'Project Owner', 'value' => $project['client']],
                    ['label' => 'Contract Value', 'value' => $project['contract_value']],
                    ['label' => 'Status', 'value' => $project['status']],
                    ['label' => 'Timeline', 'value' => $project['year']],
                    ['label' => 'Final Handover', 'value' => $project['date']],
                ];
            @endphp
            @foreach($metrics as $index => $metric)
                <div class="flex-shrink-0 snap-center min-w-[200px]">
                    <div class="text-[9px] font-black uppercase tracking-[0.4em] text-titan-navy/30 mb-2">{{ __($metric['label']) }}</div>
                    <div class="text-sm lg:text-base font-black uppercase tracking-tight text-titan-navy border-l-[3px] border-titan-red pl-3 leading-tight">{{ $metric['value'] }}</div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- === EDITORIAL NARRATIVE === -->
    <section class="py-32 px-8 xl:px-0 max-w-[1400px] mx-auto">
        <div class="flex flex-col lg:flex-row gap-20 xl:gap-32">
            
            <!-- Context Sidebar -->
            <div class="lg:w-3/12 hidden lg:block">
                <div class="sticky top-40">
                    <h3 class="text-4xl xl:text-5xl font-black text-titan-navy uppercase tracking-tighter mb-8 leading-[0.9]">
                        Project<br><span class="text-titan-red">Context</span>
                    </h3>
                    <p class="text-[10px] font-black text-titan-navy/50 uppercase tracking-[0.3em] leading-[2] mb-12 border-l-2 border-titan-red pl-4">
                        A definitive case study on structural engineering and public infrastructure integration by Kimmex.
                    </p>
                    
                    <!-- Resources -->
                    <div class="space-y-4">
                        <div class="text-[9px] font-black text-titan-navy/30 uppercase tracking-[0.4em] mb-6">Available Briefs</div>
                        <a href="#" class="flex items-center gap-4 group/doc pb-4 border-b border-gray-200">
                            <div class="w-10 h-10 border border-gray-200 flex items-center justify-center bg-gray-50 text-titan-navy/30 group-hover/doc:text-titan-red group-hover/doc:bg-white transition-all shadow-sm">
                                <x-lucide-file-text class="w-4 h-4" />
                            </div>
                            <div>
                                <span class="block text-[10px] font-black text-titan-navy uppercase tracking-widest group-hover/doc:text-titan-red transition-colors mb-0.5">Technical Summary</span>
                                <span class="block text-[8px] font-black text-titan-navy/40 uppercase tracking-[0.4em]">2.4 MB PDF</span>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Deep Narrative -->
            <div class="lg:w-9/12 w-full space-y-32">
                @php
                    $narrativeSections = [
                        'background' => 'Historical Background',
                        'objectives' => 'Strategic Objectives',
                        'design_concept' => 'Architectural Concept'
                    ];
                @endphp
                
                @foreach($narrativeSections as $key => $label)
                <div class="relative group">
                    <!-- Massive Background Number -->
                    <div class="absolute -top-12 -left-8 md:-left-16 text-[100px] md:text-[140px] font-black text-gray-200/50 uppercase tracking-tighter select-none pointer-events-none z-0 transition-transform group-hover:-translate-x-4 duration-700">
                        0{{ $loop->iteration }}
                    </div>
                    
                    <div class="relative z-10 pl-4 md:pl-16 border-l w-full max-w-4xl {{ $loop->index % 2 == 0 ? 'border-titan-red' : 'border-titan-navy/20' }}">
                        <div class="flex items-center gap-4 mb-6">
                            <h2 class="text-3xl md:text-4xl font-black text-titan-navy uppercase tracking-tighter">{{ __($label) }}</h2>
                        </div>
                        <p class="text-lg md:text-xl text-titan-navy/70 leading-[1.8] font-medium">
                            {{ $project['narrative'][$key] }}
                        </p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- === FULL WIDTH BREAK IMAGE === -->
    <div class="w-full h-[60vh] lg:h-[80vh] relative overflow-hidden group mb-32">
        <img src="{{ $project['images'][0] }}" class="w-full h-full object-cover scale-100 group-hover:scale-105 transition-transform duration-[15s]" alt="Project Mid Highlight">
        <div class="absolute inset-0 bg-titan-navy/40 mix-blend-multiply transition-colors duration-1000 group-hover:bg-titan-navy/20"></div>
        <div class="absolute bottom-16 left-8 xl:left-24 max-w-3xl z-10">
            <h3 class="text-3xl md:text-5xl lg:text-6xl font-black text-white uppercase tracking-tighter mb-8 leading-[0.9] drop-shadow-2xl">
                Delivering scale without <br><span class="text-titan-red">compromising precision.</span>
            </h3>
            <div class="flex items-center gap-6">
                <div class="w-16 h-1 bg-titan-red"></div>
                <div class="text-[10px] uppercase font-black tracking-[0.4em] text-white">Kimmex Standards</div>
            </div>
        </div>
    </div>

    <!-- === HIGH-END DATA PANELS === -->
    <section class="pb-32 px-8 xl:px-0 max-w-[1400px] mx-auto">
        <div class="flex flex-col lg:flex-row gap-8">
            
            <!-- Left: Scope -->
            <div class="lg:w-1/2 p-12 lg:p-20 bg-white border border-gray-100 shadow-2xl relative overflow-hidden group">
                 <div class="absolute -right-20 -bottom-20 opacity-[0.02] transform -rotate-12 group-hover:rotate-0 transition-transform duration-1000">
                      <img src="/images/logo/logo-kimmex.png" class="w-[400px]" alt="">
                 </div>
                 
                <div class="inline-block px-4 py-1.5 bg-titan-navy text-white text-[9px] font-black uppercase tracking-[0.4em] mb-12">Execution</div>
                <h3 class="text-4xl md:text-5xl font-black text-titan-navy uppercase tracking-tighter mb-16 relative z-10">Scope of <br><span class="text-titan-red">Services</span></h3>
                
                <div class="space-y-4 relative z-10">
                    @foreach($project['scope'] as $index => $service)
                    <div class="group/item flex items-center justify-between p-6 bg-gray-50 border border-transparent hover:bg-white hover:border-titan-red/20 hover:shadow-lg transition-all duration-300">
                        <div class="flex items-center gap-6">
                            <span class="text-[10px] font-black text-titan-red/30 group-hover/item:text-titan-red transition-colors">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                            <span class="text-sm font-black text-titan-navy uppercase tracking-widest group-hover/item:translate-x-2 transition-transform">{{ $service }}</span>
                        </div>
                        <x-lucide-check class="w-4 h-4 text-titan-navy/10 group-hover/item:text-titan-red transition-colors" />
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Right: Resilience Grid -->
            <div class="lg:w-1/2 p-12 lg:p-20 bg-titan-navy text-white relative overflow-hidden group border-b-8 border-titan-red shadow-2xl">
                <!-- Abstract Blueprint Pattern -->
                <div class="absolute inset-0 opacity-[0.05] pointer-events-none mix-blend-overlay" style="background-image: linear-gradient(#ffffff 1px, transparent 1px), linear-gradient(90deg, #ffffff 1px, transparent 1px); background-size: 40px 40px;"></div>
                
                <div class="inline-block px-4 py-1.5 bg-titan-red text-white text-[9px] font-black uppercase tracking-[0.4em] mb-12 relative z-10">Resilience</div>
                <h3 class="text-4xl md:text-5xl font-black text-white uppercase tracking-tighter mb-16 relative z-10">Technical <br><span class="text-titan-red">Challenges</span></h3>
                
                <div class="space-y-12 relative z-10">
                    @foreach($project['challenges'] as $cs)
                    <div class="relative group/chal">
                        <div class="border-l-2 border-white/10 pl-8 group-hover/chal:border-titan-red transition-colors duration-500 pb-8 border-b border-white/5 last:border-b-0 last:pb-0">
                            <div class="mb-6">
                                <div class="text-[8px] font-black text-titan-red uppercase tracking-[0.4em] mb-3">The Obstacle</div>
                                <p class="text-lg md:text-xl font-black text-white uppercase leading-tight tracking-tight">{{ $cs['challenge'] }}</p>
                            </div>
                            <div>
                                <div class="text-[8px] font-black text-white/30 uppercase tracking-[0.4em] mb-2 flex items-center gap-2">
                                    <x-lucide-arrow-right class="w-3 h-3 text-titan-red" />
                                    Kimmex Solution
                                </div>
                                <p class="text-white/60 text-sm md:text-base font-medium leading-[1.8]">{{ $cs['solution'] }}</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <!-- === OFFSET VISUALS GALLERY === -->
    <section class="pb-40 px-8 xl:px-0 max-w-[1400px] mx-auto">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-16 gap-8">
            <h3 class="text-5xl md:text-6xl font-black text-titan-navy uppercase tracking-tighter leading-[0.9]">Site <br><span class="text-titan-red">Documentation</span></h3>
            <div class="text-[10px] font-black text-titan-navy/40 uppercase tracking-[0.4em] pb-2 border-b-2 border-titan-red">
                {{ count($project['images']) - 1 }} Media Files
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-12">
            @foreach(array_slice($project['images'], 1) as $index => $img) <!-- skip first image used in break -->
                <div class="group relative overflow-hidden bg-gray-100 shadow-xl {{ $index % 2 == 1 ? 'md:mt-32' : '' }} aspect-[4/5]">
                    <img src="{{ $img }}" alt="Gallery Image {{ $index + 2 }}" class="w-full h-full object-cover transition-transform duration-[2s] group-hover:scale-105" />
                    <div class="absolute inset-0 bg-titan-navy/60 opacity-0 group-hover:opacity-100 transition-opacity duration-700 flex flex-col items-center justify-center p-8 backdrop-blur-[2px]">
                         <div class="w-16 h-16 rounded-full border border-white/30 flex items-center justify-center mb-6 scale-50 group-hover:scale-100 transition-transform duration-500 delay-100">
                             <x-lucide-maximize-2 class="w-6 h-6 text-white" />
                         </div>
                         <div class="text-[9px] font-black text-titan-red uppercase tracking-[0.5em] mb-2 translate-y-4 group-hover:translate-y-0 opacity-0 group-hover:opacity-100 transition-all duration-500 delay-200">Enlarge</div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- === NEXT LEGACY / RELATED === -->
    @if(isset($project['related']) && count($project['related']) > 0)
    <section class="max-w-[1400px] mx-auto px-8 xl:px-0 cursor-pointer">
        <a href="/projects/{{ $project['related'][0]['id'] }}" class="block w-full bg-titan-navy hover:bg-black transition-colors duration-1000 text-white relative overflow-hidden group shadow-2xl">
            <div class="absolute right-0 top-0 bottom-0 w-1/2 z-0 hidden md:block">
                 <img src="{{ $project['related'][0]['image'] }}" class="w-full h-full object-cover opacity-30 group-hover:opacity-70 scale-100 group-hover:scale-110 transition-all duration-[3s]" alt="">
                 <div class="absolute inset-0 bg-gradient-to-r from-titan-navy to-transparent"></div>
            </div>
            
            <div class="relative z-10 py-32 md:py-40 px-12 md:px-24 w-full md:w-3/4 lg:w-2/3">
                <div class="flex items-center gap-4 mb-8">
                     <div class="w-8 h-px bg-titan-red"></div>
                     <span class="text-[9px] font-black text-titan-red uppercase tracking-[0.5em]">{{ __('Next Legacy') }}</span>
                </div>
                <h3 class="text-4xl md:text-6xl lg:text-7xl font-black text-white uppercase tracking-tighter mb-12 leading-[0.9] group-hover:translate-x-4 transition-transform duration-700">
                    {{ $project['related'][0]['title'] }}
                </h3>
                
                <div class="inline-flex items-center gap-4 text-[10px] font-black uppercase tracking-[0.3em] text-white/50 group-hover:text-white transition-colors">
                    <span>{{ __('Discover Project') }}</span>
                    <x-lucide-arrow-right class="w-4 h-4 text-titan-red group-hover:translate-x-4 transition-transform duration-500" />
                </div>
            </div>
        </a>
    </section>
    @endif

</div>

</x-layouts.app>
