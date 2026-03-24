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
    'video' => 'https://www.w3schools.com/html/mov_bbb.mp4',
    
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

<div class="bg-white min-h-screen text-titan-navy">
    
    <!-- === PREMIUM CINEMATIC HERO SECTION === -->
    <section class="relative h-screen w-full flex items-center overflow-hidden bg-titan-navy">
        <!-- Dynamic Cinematic Background -->
        <div class="absolute inset-0 scale-105">
            <img src="{{ $project['heroImage'] }}" alt="{{ $project['title'] }}" class="w-full h-full object-cover opacity-40 blur-[1px]" />
            <div class="absolute inset-0 bg-gradient-to-r from-titan-navy via-titan-navy/60 to-transparent"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-titan-navy via-transparent to-transparent"></div>
            <!-- Structural Grid Overlay -->
            <div class="absolute inset-0 opacity-[0.03] bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')]"></div>
            <div class="absolute inset-0 border-[40px] border-titan-navy/20 pointer-events-none"></div>
        </div>
        
        <!-- Breadcrumbs (Floating Left) -->
        <div class="absolute top-12 left-12 z-30 hidden lg:block">
            <nav class="flex items-center gap-6 text-[9px] font-black uppercase tracking-[0.5em] text-white/30">
                <a href="/" class="hover:text-titan-red transition-all">{{ __('Home') }}</a>
                <span class="w-8 h-px bg-white/10"></span>
                <a href="/projects" class="hover:text-titan-red transition-all">{{ __('Portfolios') }}</a>
                <span class="w-8 h-px bg-titan-red"></span>
                <span class="text-white">{{ __('Project Detail') }}</span>
            </nav>
        </div>

        <!-- Hero Content: Split Layout -->
        <div class="relative z-20 w-full max-w-[1700px] mx-auto px-12 lg:px-24 grid grid-cols-1 lg:grid-cols-12 gap-20 items-end pb-24" x-data="{ shown: false }" x-init="setTimeout(() => shown = true, 100)">
            
            <div class="lg:col-span-8 space-y-12">
                <div :class="shown ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-12'" class="transition-all duration-1000 delay-100 inline-flex items-center gap-4 text-titan-red text-[10px] font-black uppercase tracking-[0.5em]">
                    <span class="w-12 h-px bg-titan-red"></span>
                    <span>{{ $project['type'] }}</span>
                </div>

                <h1 :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-20'" class="transition-all duration-1000 delay-300 text-6xl md:text-[120px] font-black text-white leading-[0.85] uppercase tracking-tighter mix-blend-lighten max-w-4xl">
                    {{ $project['title'] }}
                </h1>
            </div>

            <!-- Floating Hero Specs -->
            <div class="lg:col-span-4" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-20'" class="transition-all duration-1000 delay-500">
                <div class="bg-white/5 backdrop-blur-3xl border border-white/10 p-12 space-y-10 group hover:border-titan-red/50 transition-colors duration-700">
                    <div class="flex items-center justify-between">
                         <div class="text-[9px] font-black uppercase tracking-[0.4em] text-white/40">{{ __('Location') }}</div>
                         <div class="font-black text-white uppercase text-sm tracking-widest text-right">{{ $project['location'] }}</div>
                    </div>
                    <div class="w-full h-px bg-white/5"></div>
                    <div class="flex items-center justify-between">
                         <div class="text-[9px] font-black uppercase tracking-[0.4em] text-white/40">{{ __('Project Status') }}</div>
                         <div class="px-3 py-1 bg-green-600/20 border border-green-600/30 text-green-500 font-black text-[9px] uppercase tracking-widest">{{ $project['status'] }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scroll Indicator (Architectural Line) -->
        <div class="absolute bottom-12 right-12 flex items-center gap-8 cursor-pointer group" onclick="window.scrollTo({top: window.innerHeight, behavior: 'smooth'})">
             <span class="text-[9px] font-black uppercase tracking-[0.5em] text-white/20 group-hover:text-white transition-colors">{{ __('Scroll to legacy') }}</span>
             <div class="w-24 h-px bg-gradient-to-r from-titan-red to-transparent"></div>
        </div>
    </section>

    <!-- === PROJECT STORYTELLING AREA === -->
    <section class="py-24 px-6 bg-white relative">
        <div class="max-w-[1500px] mx-auto">
            
            <!-- Stealth Professional: Key Facts Grid -->
            <div class="mb-32">
                <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-0 border border-gray-100">
                    @php
                        $facts = [
                            ['label' => 'Project Owner', 'value' => $project['client'], 'icon' => 'user-check'],
                            ['label' => 'Precise Location', 'value' => $project['location'], 'icon' => 'navigation'],
                            ['label' => 'Sector Category', 'value' => $project['type'], 'icon' => 'factory'],
                            ['label' => 'Structural Area', 'value' => $project['built_area'], 'icon' => 'ruler'],
                            ['label' => 'Project Valuation', 'value' => $project['contract_value'], 'icon' => 'bar-chart-3'],
                            ['label' => 'Current Lifecycle', 'value' => $project['status'] . ' / ' . $project['year'], 'icon' => 'timer'],
                        ];
                    @endphp
                    @foreach($facts as $fact)
                    <div class="p-10 border-r border-b lg:border-b-0 border-gray-100 group hover:bg-titan-navy transition-all duration-500">
                        <x-dynamic-component :component="'lucide-' . $fact['icon']" class="w-4 h-4 text-titan-red mb-6 opacity-40 group-hover:opacity-100 transition-opacity" />
                        <div class="text-[8px] font-black tracking-[0.3em] text-titan-navy/30 group-hover:text-white/30 uppercase mb-3">{{ __($fact['label']) }}</div>
                        <div class="font-black uppercase tracking-tight text-[13px] text-titan-navy group-hover:text-white leading-tight">{{ $fact['value'] }}</div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-20">
                
                <!-- Main Narrative Content -->
                <div class="lg:col-span-8 space-y-24">
                <!-- Main Narrative Content -->
                <div class="lg:col-span-8 space-y-32">
                    <!-- Project Storytelling Grid -->
                    <div class="grid grid-cols-1 gap-24 relative">
                         <!-- Vertical Architectural Line -->
                         <div class="absolute left-0 top-0 bottom-0 w-px bg-gray-100 hidden lg:block"></div>

                        @foreach(['background' => 'Historical Background', 'objectives' => 'Strategic Objectives', 'design_concept' => 'Architectural Concept'] as $key => $label)
                        <div class="relative pl-0 lg:pl-16 space-y-10 group">
                            <!-- Bullet Accent -->
                            <div class="absolute -left-1.5 top-0 w-3 h-3 bg-titan-red hidden lg:block group-hover:scale-150 transition-transform duration-500"></div>

                            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 border-b border-gray-100 pb-10">
                                <h3 class="text-3xl font-black text-titan-navy uppercase tracking-tighter">{{ __($label) }}</h3>
                                <span class="text-[10px] font-black text-titan-red/40 uppercase tracking-[0.5em]">{{ __('Kimmex Report 0'.($loop->index+1)) }}</span>
                            </div>
                            <div class="prose prose-2xl text-titan-navy/50 font-medium leading-[1.8] max-w-none hover:text-titan-navy transition-colors duration-500">
                                <p>{{ $project['narrative'][$key] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Professional Scope -->
                    <div class="relative p-12 lg:p-16 border border-gray-100 bg-white group overflow-hidden">
                        <!-- Background Stealth Logo -->
                        <div class="absolute -bottom-10 -right-10 opacity-[0.02] rotate-12 group-hover:rotate-0 transition-transform duration-1000">
                             <img src="/images/logo/logo-kimmex.png" class="w-64" alt="">
                        </div>

                        <div class="relative z-10">
                            <div class="inline-block px-4 py-1.5 bg-titan-navy text-white text-[9px] font-black uppercase tracking-[0.3em] mb-10">
                                {{ __('Kimmex Execution') }}
                            </div>
                            <h3 class="text-4xl font-black text-titan-navy uppercase tracking-tighter mb-12">{{ __('Scope of Services') }}</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-12">
                                @foreach($project['scope'] as $service)
                                <div class="flex items-center justify-between pb-4 border-b border-gray-50 group/item">
                                    <span class="text-xs font-black text-titan-navy/60 group-hover/item:text-titan-red transition-colors uppercase tracking-tight">{{ $service }}</span>
                                    <x-lucide-arrow-up-right class="w-3.5 h-3.5 text-titan-red opacity-0 group-hover/item:opacity-100 -translate-x-2 group-hover/item:translate-x-0 transition-all" />
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Intellectual Asset: Challenges & Solutions -->
                    <div class="space-y-16">
                        <div class="flex items-center gap-5">
                            <span class="text-[10px] font-black text-titan-red uppercase tracking-[0.5em]">{{ __('Strategy') }}</span>
                            <div class="h-px flex-1 bg-gray-100"></div>
                            <h2 class="text-2xl font-black text-titan-navy uppercase tracking-tighter">{{ __('Challenges & Solutions') }}</h2>
                        </div>
                        <div class="grid grid-cols-1 gap-12">
                            @foreach($project['challenges'] as $cs)
                            <div class="group">
                                <div class="flex flex-col md:flex-row gap-10">
                                    <div class="md:w-1/3 flex gap-4">
                                        <div class="w-1.5 h-1.5 rounded-full bg-titan-red mt-1.5 shrink-0"></div>
                                        <div>
                                            <div class="text-[9px] font-black uppercase tracking-widest text-titan-navy/30 mb-2">{{ __('The Obstacle') }}</div>
                                            <p class="font-black text-titan-navy uppercase tracking-tight leading-tight">{{ $cs['challenge'] }}</p>
                                        </div>
                                    </div>
                                    <div class="md:w-2/3 p-8 bg-gray-50 border-l-2 border-titan-red group-hover:bg-titan-navy group-hover:text-white transition-all duration-500">
                                        <div class="text-[9px] font-black uppercase tracking-widest text-titan-red mb-3 group-hover:text-white/40">{{ __('Kimmex Solution') }}</div>
                                        <p class="text-titan-navy/60 group-hover:text-white/80 text-base font-medium leading-relaxed">{{ $cs['solution'] }}</p>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Cinematic Video -->
                    @if(isset($project['video']))
                    <div class="space-y-10">
                         <div class="flex items-center gap-4">
                            <div class="w-16 h-1 bg-titan-red"></div>
                            <h2 class="text-3xl font-black text-titan-navy uppercase tracking-tighter">{{ __('Visualization') }}</h2>
                        </div>
                        <div class="relative w-full aspect-video overflow-hidden bg-titan-navy border border-gray-100 shadow-xl group">
                            <video src="{{ $project['video'] }}" class="w-full h-full object-cover opacity-90 group-hover:opacity-100 transition-opacity duration-700" autoplay muted loop playsinline></video>
                        </div>
                    </div>
                    @endif

                    <!-- Uniform Gallery -->
                    <div class="space-y-10 relative">
                         <!-- Structural Accent Corners -->
                         <div class="absolute -top-4 -left-4 w-12 h-12 border-t-2 border-l-2 border-titan-red/20 pointer-events-none"></div>
                         <div class="absolute -bottom-4 -right-4 w-12 h-12 border-b-2 border-r-2 border-titan-red/20 pointer-events-none"></div>

                         <div class="flex items-center gap-4">
                            <div class="w-16 h-1 bg-titan-red"></div>
                            <h2 class="text-3xl font-black text-titan-navy uppercase tracking-tighter">{{ __('Visual Documentation') }}</h2>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach($project['images'] as $index => $img)
                                <div class="group relative aspect-[4/3] overflow-hidden bg-gray-50 border border-gray-100 shadow-sm hover:shadow-2xl transition-all duration-700">
                                    <img src="{{ $img }}" alt="Gallery Image {{ $index + 1 }}" class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110" />
                                    <div class="absolute inset-0 bg-titan-navy/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center backdrop-blur-[2px]">
                                         <div class="w-14 h-14 bg-white rounded-full flex items-center justify-center scale-75 group-hover:scale-100 transition-transform duration-500 shadow-2xl">
                                             <x-lucide-maximize-2 class="w-6 h-6 text-titan-red" />
                                         </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- High-End Specs Sidebar -->
                <div class="lg:col-span-4 lg:pl-12">
                    <div class="sticky top-32 space-y-12">
                        <!-- Floating Data Card -->
                        <div class="relative bg-white border border-gray-100 p-12 overflow-hidden group shadow-[0_40px_100px_-20px_rgba(0,0,0,0.05)]">
                            <div class="absolute top-0 left-0 w-1 h-12 bg-titan-red"></div>
                            
                            <p class="text-[9px] font-black uppercase tracking-[0.5em] text-titan-navy/30 mb-12">{{ __('Executive Data') }}</p>
                            
                            <ul class="space-y-10">
                                <li class="flex items-end justify-between border-b border-gray-50 pb-6 group/spec">
                                    <div class="text-[9px] uppercase font-black tracking-[0.3em] text-titan-navy/40 group-hover/spec:text-titan-red transition-colors">{{ __('Total Built Area') }}</div>
                                    <div class="font-black text-titan-navy uppercase text-lg tracking-tight">{{ $project['built_area'] }}</div>
                                </li>
                                <li class="flex items-end justify-between border-b border-gray-50 pb-6 group/spec">
                                    <div class="text-[9px] uppercase font-black tracking-[0.3em] text-titan-navy/40 group-hover/spec:text-titan-red transition-colors">{{ __('Contract Phase') }}</div>
                                    <div class="font-black text-titan-navy uppercase text-lg tracking-tight">{{ $project['status'] }}</div>
                                </li>
                                <li class="flex items-end justify-between border-b border-gray-50 pb-6 group/spec">
                                    <div class="text-[9px] uppercase font-black tracking-[0.3em] text-titan-navy/40 group-hover/spec:text-titan-red transition-colors">{{ __('Active Since') }}</div>
                                    <div class="font-black text-titan-navy uppercase text-lg tracking-tight">{{ $project['year'] }}</div>
                                </li>
                            </ul>

                            <a href="/contact" class="mt-16 w-full bg-titan-navy py-5 text-white text-[10px] font-black uppercase tracking-[0.5em] flex items-center justify-center gap-4 hover:bg-titan-red transition-all duration-500 shadow-2xl">
                                {{ __('Inquire Details') }}
                                <x-lucide-arrow-right class="w-4 h-4" />
                            </a>
                        </div>

                        <!-- Professional Downloads -->
                        <div class="p-12 border border-gray-100 space-y-10 relative overflow-hidden group">
                             <div class="absolute -right-8 -bottom-8 opacity-[0.05]">
                                 <x-lucide-file-text class="w-32 h-32 text-titan-navy" />
                             </div>
                             <h4 class="text-xs font-black text-titan-navy uppercase tracking-[0.4em] relative z-10">{{ __('Whitepapers') }}</h4>
                             <div class="space-y-6 relative z-10">
                                <a href="#" class="flex items-center justify-between group/link border-b border-gray-50 pb-4">
                                    <span class="text-[10px] font-black text-titan-navy/50 group-hover/link:text-titan-red transition-colors uppercase tracking-widest">{{ __('Technical Summary.PDF') }}</span>
                                    <x-lucide-download class="w-3.5 h-3.5 text-titan-navy/20 group-hover/link:text-titan-red transition-transform group-hover/link:translate-y-1" />
                                </a>
                                <a href="#" class="flex items-center justify-between group/link border-b border-gray-50 pb-4">
                                    <span class="text-[10px] font-black text-titan-navy/50 group-hover/link:text-titan-red transition-colors uppercase tracking-widest">{{ __('Site Specifications.PDF') }}</span>
                                    <x-lucide-download class="w-3.5 h-3.5 text-titan-navy/20 group-hover/link:text-titan-red transition-transform group-hover/link:translate-y-1" />
                                </a>
                             </div>
                        </div>
                    </div>
                </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- === RELATED LEGACIES (Dynamic Grid) === -->
    @if(isset($project['related']) && count($project['related']) > 0)
    <section class="py-24 bg-gray-50/50 border-t border-gray-100 relative overflow-hidden">
        <div class="absolute inset-0 opacity-5 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] pointer-events-none"></div>
        <div class="max-w-[1500px] mx-auto px-6 relative z-10">
            
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-8 mb-16">
                <div>
                     <span class="text-[10px] font-black uppercase tracking-[0.4em] text-titan-red mb-4 block">{{ __('Portfolio') }}</span>
                     <h3 class="text-4xl md:text-5xl font-black text-titan-navy uppercase tracking-tighter">{{ __('Related Legacies') }}</h3>
                </div>
                <a href="/projects" class="inline-flex items-center gap-3 px-8 py-4 bg-white border border-gray-100 rounded-2xl text-[11px] font-black uppercase tracking-[0.2em] text-titan-navy hover:border-titan-red/30 hover:shadow-xl transition-all group">
                    {{ __('View All Projects') }}
                    <x-lucide-arrow-right class="w-4 h-4 group-hover:translate-x-2 transition-transform text-titan-red" />
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                @foreach($project['related'] as $related)
                <a href="/projects/{{ $related['id'] }}" class="group block relative bg-white border border-gray-100 hover:shadow-[0_40px_80px_-15px_rgba(0,0,0,0.1)] transition-all duration-700">
                    <div class="aspect-[16/10] overflow-hidden relative">
                        <img src="{{ $related['image'] }}" alt="{{ $related['title'] }}" class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110" />
                        <div class="absolute inset-0 bg-titan-navy/20 group-hover:bg-titan-navy/5 transition-colors duration-700"></div>
                        
                        <!-- Premium Float Badge -->
                        <div class="absolute top-6 left-6 px-4 py-2 bg-white/10 backdrop-blur-md border border-white/20 text-white text-[9px] font-black uppercase tracking-widest opacity-0 group-hover:opacity-100 transition-all duration-500 translate-y-2 group-hover:translate-y-0">
                            {{ __('Explore Legacy') }}
                        </div>

                        <div class="absolute bottom-0 right-0 p-8">
                            <div class="w-14 h-14 bg-titan-red text-white flex items-center justify-center translate-y-4 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition-all duration-700 shadow-2xl">
                                <x-lucide-arrow-right class="w-6 h-6" />
                            </div>
                        </div>
                    </div>
                    <div class="p-10">
                        <p class="text-[9px] uppercase tracking-[0.4em] font-black text-titan-red/60 mb-3">{{ $related['type'] }}</p>
                        <h4 class="text-2xl font-black text-titan-navy uppercase tracking-tight group-hover:text-titan-red transition-colors duration-500">{{ $related['title'] }}</h4>
                    </div>
                </a>
                @endforeach
            </div>
            
        </div>
    </section>
    @endif

</div>

</x-layouts.app>
