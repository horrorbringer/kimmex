<x-layouts.app title="Services" description="Explore our comprehensive construction and engineering services provided by Kimmex.">

@php
$lang = app()->getLocale() === 'km' ? 'kh' : app()->getLocale();

$servicesDb = \App\Models\Service::where('isActive', true)->orderBy('orderIndex')->get();
$services = $servicesDb->map(function($service) {
    return [
        "id" => $service->slug,
        "title" => ["en" => $service->title, "kh" => $service->titleKm ?: $service->title],
        "desc" => [
            "en" => strip_tags($service->description),
            "kh" => strip_tags($service->descriptionKm ?: $service->description)
        ],
        "image" => $service->image 
            ? (\Illuminate\Support\Str::startsWith($service->image, '/') ? $service->image : \Illuminate\Support\Facades\Storage::url($service->image)) 
            : "/images/projects/Thumbnail-1.jpg",
        "features" => is_array($service->features) ? $service->features : []
    ];
})->toArray();

// Fallback service categories from project structure if DB is empty
if (empty($services)) {
    $services = [
        [
            "id" => "design-and-build",
            "title" => ["en" => "Design & Build", "kh" => "រចនា និងសាងសង់"],
            "desc" => [
                "en" => "End-to-end construction solutions from architectural design through to project completion, covering detail design, civil work, MEP work, finishing work, and decoration.",
                "kh" => "ដំណោះស្រាយសំណង់ពីការរចនាស្ថាបត្យកម្មរហូតដល់ការបញ្ចប់គម្រោង រួមទាំងការរចនាលម្អិត ការងារស៊ីវិល ការងារ MEP ការងារបញ្ចប់ និងការតុបតែង។"
            ],
            "image" => "/images/projects/Thumbnail-1.jpg",
            "features" => [["name" => "Detail Design"], ["name" => "Civil Work"], ["name" => "MEP Work"], ["name" => "Finishing Work"], ["name" => "Decoration"]]
        ],
        [
            "id" => "construction",
            "title" => ["en" => "Construction", "kh" => "សាងសង់"],
            "desc" => [
                "en" => "Premium civil construction services across Cambodia specializing in robust concrete work, high-rise buildings, and commercial spaces.",
                "kh" => "សេវាកម្មសំណង់ស៊ីវិលលំដាប់ខ្ពស់ប្រចាំប្រទេសកម្ពុជាដែលមានជំនាញលើការងារបេតុងដ៏រឹងមាំ អគារខ្ពស់ៗ និងអគារពាណិជ្ជកម្ម។"
            ],
            "image" => "/images/projects/Thumbnail-1.jpg",
            "features" => [["name" => "High-Rise Buildings"], ["name" => "Commercial Spaces"], ["name" => "Quality Assurance"]]
        ],
        [
            "id" => "project-management",
            "title" => ["en" => "Project Management", "kh" => "ការគ្រប់គ្រងគម្រោង"],
            "desc" => [
                "en" => "Expert oversight and management of construction projects, ensuring on-time delivery, quality control, cost management, and safety compliance.",
                "kh" => "ការត្រួតពិនិត្យ និងគ្រប់គ្រងគម្រោងសំណង់ ធានាការផ្តល់ទាន់ពេល ការត្រួតពិនិត្យគុណភាព ការគ្រប់គ្រងថ្លៃដើម និងការអនុលោមតាមសុវត្ថិភាព។"
            ],
            "image" => "/images/projects/Thumbnail-3.jpg",
            "features" => [["name" => "Scheduling & Planning"], ["name" => "Quality Control"], ["name" => "Cost Management"], ["name" => "Safety Compliance"]]
        ],
        [
            "id" => "consultants",
            "title" => ["en" => "Consultants", "kh" => "ទីប្រឹក្សា"],
            "desc" => [
                "en" => "Professional consulting services including project feasibility studies, design consulting, structural analysis, and expert advisory for complex engineering challenges.",
                "kh" => "សេវាកម្មប្រឹក្សាវិជ្ជាជីវៈ រួមទាំងការសិក្សាលទ្ធភាពគម្រោង ការប្រឹក្សាការរចនា ការវិភាគរចនាសម្ព័ន្ធ និងការប្រឹក្សាជំនាញ។"
            ],
            "image" => "/images/projects/Thumbnail-4.jpg",
            "features" => [["name" => "Project Feasibility"], ["name" => "Design Consulting"], ["name" => "Structural Analysis"]]
        ]
    ];
}

$process = [
    [
        "step" => "01",
        "icon" => "lucide-users",
        "title" => ["en" => "Consultation & Analysis", "kh" => "ការពិគ្រោះយោបល់ និងការវិភាគ"],
        "desc" => ["en" => "Understanding requirements, performing site data deep dives, and feasibility analysis.", "kh" => "ការស្វែងយល់ពីតម្រូវការ និងការវិភាគលទ្ធភាព។"]
    ],
    [
        "step" => "02",
        "icon" => "lucide-layout-dashboard",
        "title" => ["en" => "Planning & Procurement", "kh" => "ការធ្វើផែនការ និងលទ្ធកម្ម"],
        "desc" => ["en" => "Defining project roadmap, budgets, baselines, and vendor selection.", "kh" => "ការកំណត់ផែនទីបង្ហាញផ្លូវ ថវិកា និងការជ្រើសរើសអ្នកផ្គត់ផ្គង់។"]
    ],
    [
        "step" => "03",
        "icon" => "lucide-hard-hat",
        "title" => ["en" => "Execution & Advisory", "kh" => "ការអនុវត្ត និងការប្រឹក្សា"],
        "desc" => ["en" => "On-site management, daily coordination, and ongoing strategic guidance.", "kh" => "ការគ្រប់គ្រងការដ្ឋាន និងការសម្របសម្រួលប្រចាំថ្ងៃ។"]
    ],
    [
        "step" => "04",
        "icon" => "lucide-settings",
        "title" => ["en" => "Systems Integration", "kh" => "ការធ្វើសមាហរណកម្មប្រព័ន្ធ"],
        "desc" => ["en" => "Implementing smart building tech, MEP systems, and advanced automation.", "kh" => "ការអនុវត្តបច្ចេកវិទ្យាអាគារឆ្លាតវៃ និងប្រព័ន្ធ MEP។"]
    ],
    [
        "step" => "05",
        "icon" => "lucide-check-circle-2",
        "title" => ["en" => "Close-out & Reporting", "kh" => "ការបញ្ចប់ និងការរាយការណ៍"],
        "desc" => ["en" => "Final accounting, documentation, and delivering actionable recommendations.", "kh" => "ការរៀបចំឯកសារចុងក្រោយ និងរបាយការណ៍។"]
    ]
];

$sectors = [
    ["title" => ["en" => "Government Offices", "kh" => "ការិយាល័យរដ្ឋាភិបាល"], "image" => "/images/projects/Thumbnail-1.jpg", "icon" => "lucide-landmark"],
    ["title" => ["en" => "Education", "kh" => "អប់រំ"], "image" => "/images/projects/Thumbnail-2.jpg", "icon" => "lucide-graduation-cap"],
    ["title" => ["en" => "Commercial", "kh" => "ពាណិជ្ជកម្ម"], "image" => "/images/projects/Thumbnail-3.jpg", "icon" => "lucide-building"],
    ["title" => ["en" => "Infrastructure", "kh" => "ហេដ្ឋារចនាសម្ព័ន្ធ"], "image" => "/images/projects/Thumbnail-6.jpg", "icon" => "lucide-route"]
];
@endphp

<div class="bg-white min-h-screen text-titan-navy">
    <style>
        .custom-hero-container {
            height: 95vh;
            min-height: 800px;
        }
        @media (min-width: 1024px) {
            .custom-hero-container {
                /* Removed radius for sharp corners */
            }
        }
    </style>
    <!-- === HERO SECTION (Design-Z) === -->
    <section class="relative z-10 flex items-center justify-center overflow-hidden bg-titan-navy shadow-2xl custom-hero-container">
        {{-- Background Parallel Zoom Animation --}}
        <div class="absolute inset-0 scale-105 animate-super-slow-pan bg-titan-navy">
            <img src="/images/projects/Thumbnail-1.jpg" alt="Kimmex Expertise" class="w-full h-full object-cover opacity-100 transition-opacity duration-1000" />
            <div class="absolute inset-0 bg-gradient-to-t from-titan-navy/80 via-titan-navy/20 to-transparent"></div>
        </div>

        <!-- Decorative Floating Elements -->
        <div class="absolute top-[20%] -left-32 w-[600px] h-[600px] border border-white/5 rounded-full hidden lg:block pointer-events-none"></div>
        <div class="absolute bottom-[20%] -right-40 w-[600px] h-[600px] border border-accent-orange/10 rounded-full hidden lg:block pointer-events-none"></div>
        
        <!-- Center Glow -->
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-accent-orange/5 rounded-full pointer-events-none" style="filter: blur(120px);"></div>

        <!-- Hero Content -->
        <div class="relative z-20 text-center max-w-6xl px-6 pt-16" x-data="{ shown: false }" x-init="setTimeout(() => shown = true, 100)">
            <div :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 -translate-y-8'" class="transition-all duration-1000 delay-100 inline-flex items-center gap-2 px-6 py-3 bg-white/5 backdrop-blur-md rounded-full text-white text-[11px] font-bold uppercase tracking-[0.2em] mb-8 border border-white/20 shadow-2xl">
                <x-lucide-settings class="w-4 h-4 text-accent-orange animate-spin-slow" />
                <span>{{ strtoupper(__('Services')) }}</span>
            </div>

            <h1 :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'" class="transition-all duration-1000 delay-300 text-5xl md:text-7xl lg:text-[6rem] font-black text-white mb-10 leading-[0.9] tracking-tight uppercase">
                <span class="text-white">{{ $lang === 'kh' ? 'ជំនាញ' : 'OUR' }}</span> <span class="text-accent-orange">{{ $lang === 'kh' ? 'របស់យើង' : 'EXPERTISE' }}</span>
            </h1>

            <p :class="shown ? 'opacity-100' : 'opacity-0'" class="transition-all duration-1000 delay-500 text-sm md:text-base text-white/60 max-w-3xl mx-auto leading-relaxed font-bold uppercase tracking-[0.3em] opacity-80">
                {{ __('Precision. Innovation. Excellence.') }}
            </p>
        </div>

        <!-- Scroll Indicator -->
        <div class="absolute bottom-12 left-1/2 -translate-x-1/2 flex flex-col items-center gap-4 cursor-pointer group z-20" @click="document.getElementById('services-list').scrollIntoView({ behavior: 'smooth' })">
            <span class="text-[10px] uppercase tracking-[0.4em] font-bold text-white/50 group-hover:text-accent-orange transition-colors">{{ strtoupper(__('Explore Services')) }}</span>
            <div class="w-6 h-10 border border-white/20 rounded-full flex justify-center pt-2 backdrop-blur-sm bg-transparent group-hover:border-accent-orange transition-colors">
                <div class="w-1.5 h-1.5 bg-accent-orange rounded-full animate-bounce"></div>
            </div>
        </div>
    </section>

    <!-- === SERVICE CATEGORIES (Design-Z Staggered) === -->
    <div class="w-full" style="background-color: #f8f9fa;">
        <section id="services-list" class="pt-8 pb-16 px-6 max-w-[1500px] mx-auto overflow-hidden">
        <div x-data="{ shown: false }" x-intersect.once="shown = true" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'" class="text-center mb-8 transition-all duration-1000">
            <span class="text-titan-red font-bold uppercase tracking-[0.4em] text-xs mb-4 block">{{ __('What We Do') }}</span>
            <h2 class="text-4xl md:text-6xl font-black text-titan-navy mb-8 uppercase tracking-tighter">{{ __('Capabilities & Expertise') }}</h2>
            <div class="w-24 h-1.5 bg-titan-red mx-auto mb-8"></div>
            <p class="text-titan-navy/50 text-xl max-w-3xl mx-auto leading-relaxed">
                {{ __('We bring decades of experience to every project, ensuring quality and efficiency at every stage.') }}
            </p>
        </div>

        <div class="space-y-24 md:space-y-32">
            @foreach($services as $i => $service)
                <div x-data="{ shown: false }" x-intersect.once="shown = true" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-16'" 
                     class="group flex flex-col lg:flex-row gap-0 overflow-hidden transition-all duration-700 bg-white h-full"
                     style="box-shadow: 0 20px 60px rgba(0,0,0,0.05); border-radius: 2rem; {{ $i % 2 === 1 ? 'flex-direction: row-reverse;' : '' }}">
                    
                    <!-- Image Side (Staggered) -->
                    <div class="lg:w-[45%] relative h-[350px] lg:h-auto overflow-hidden">
                        <img src="{{ $service['image'] }}" alt="{{ $service['title'][$lang] }}" class="w-full h-full object-cover group-hover:scale-105 transition-all duration-1000 ease-out" />
                        <div class="absolute inset-0 bg-black/10 transition-colors duration-700"></div>
                        
                        <!-- Overlay Title for Mobile -->
                        <div class="absolute bottom-0 left-0 right-0 p-8 lg:hidden bg-gradient-to-t from-titan-navy text-white">
                            <h3 class="text-3xl font-black uppercase tracking-tighter">{{ $service['title'][$lang] }}</h3>
                        </div>
                    </div>

                    <!-- Content Side -->
                    <div class="lg:w-[55%] p-10 md:p-20 flex flex-col items-center text-center justify-center relative bg-white">
                        <!-- Ghost Number -->
                        <div class="absolute top-10 right-10 hidden md:block select-none pointer-events-none">
                            <span class="text-[12rem] font-black text-titan-navy transition-colors duration-700 leading-none" style="opacity: 0.05;">0{{ $i + 1 }}</span>
                        </div>

                        <div class="relative z-10">
                            <div class="w-20 h-2 bg-titan-red mb-10 mx-auto"></div>
                            
                            <h3 class="text-4xl md:text-6xl font-black text-titan-navy mb-8 uppercase tracking-tighter leading-[0.9]">
                                {{ $service['title'][$lang] }}
                            </h3>
                            
                            <p class="text-titan-navy/60 text-xl leading-relaxed mb-12 max-w-2xl mx-auto">
                                {{ $service['desc'][$lang] }}
                            </p>

                            @php
                                $featuresArray = is_array($service['features']) ? $service['features'] : [];
                            @endphp
                            
                            @if(count($featuresArray) > 0)
                            <div class="flex flex-wrap justify-center gap-y-5 gap-x-12 mb-12">
                                @foreach($featuresArray as $feature)
                                    <div class="flex items-center gap-4 text-titan-navy/80 font-bold text-xs uppercase tracking-[0.2em] group/feat bg-gray-50 px-5 py-3 rounded-full border border-gray-100">
                                        <div class="w-2.5 h-2.5 bg-titan-red rounded-full"></div>
                                        <span>{{ __($feature['name'] ?? (is_array($feature) ? ($feature[$lang] ?? '') : $feature)) }}</span>
                                    </div>
                                @endforeach
                            </div>
                            @endif

                            <div class="pt-8 border-t border-gray-100 flex items-center justify-between">
                                <a href="/services/{{ $service['id'] }}" class="inline-flex items-center gap-4 text-titan-red font-black uppercase tracking-[0.4em] text-xs transition-all group/link">
                                    {{ __('Learn More') }}
                                    <x-lucide-arrow-right class="w-5 h-5 transition-transform group-hover/link:translate-x-1" />
                                </a>
                                
                                <div class="hidden sm:flex items-center gap-2 text-titan-navy/20 font-black text-5xl">
                                    0{{ $i + 1 }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        </section>
    </div>

    <!-- === OUR METHODOLOGY (Design-Z Process) === -->
    <section class="py-32 bg-titan-navy text-white relative overflow-hidden">
        <!-- Background Decor -->
        <div class="absolute top-0 right-0 w-[800px] h-[800px] bg-titan-red/5 rounded-full blur-[150px] translate-x-1/2 -translate-y-1/2 pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-blue-500/5 rounded-full blur-[120px] -translate-x-1/2 translate-y-1/2 pointer-events-none"></div>

        <div class="max-w-[1400px] mx-auto px-6 relative z-10">
            <div x-data="{ shown: false }" x-intersect.once="shown = true" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'" class="text-center mb-24 transition-all duration-1000">
                <span class="text-titan-red font-bold uppercase tracking-[0.2em] text-sm mb-4 block">{{ __('HOW IT WORKS') }}</span>
                <h2 class="text-5xl md:text-6xl font-bold mb-6 text-white tracking-tight">{{ __('Our Methodology') }}</h2>
                <p class="text-white/60 text-lg max-w-2xl mx-auto leading-relaxed">
                    {{ __('A systematic approach ensuring transparency, safety, and excellence from the first meeting to final handover.') }}
                </p>
            </div>

            <style>
                .hide-scrollbar::-webkit-scrollbar { display: none; }
                .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
            </style>
            
            <div class="relative mt-10 md:mt-16">
                <!-- Connecting Line (Desktop) -->
                <div class="hidden md:block absolute top-[80px] left-[10%] right-[10%] h-[1px] bg-white/10 z-0">
                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-titan-red/80 to-transparent w-1/4 h-full animate-line-flow"></div>
                </div>

                <div class="flex flex-wrap justify-center gap-8 md:gap-12 relative z-10 hide-scrollbar snap-x snap-mandatory pb-10 pt-10 px-6 md:px-0">
                    @foreach($process as $i => $s)
                        <div x-data="{ shown: false }" x-intersect.once="shown = true" style="transition-delay: {{ $i * 150 }}ms" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'" 
                             class="flex-none w-full sm:w-[320px] md:w-[calc(20%-2rem)] snap-center flex flex-col items-center text-center group transition-all duration-1000">
                            
                            <div class="relative mb-12 flex justify-center items-center" style="width: 160px; height: 160px;">
                                <!-- Ghost Number -->
                                <div class="absolute font-black z-0 leading-none select-none tracking-tighter mb-4 pointer-events-none transition-colors duration-700" style="font-size: 120px; color: rgba(255,255,255,0.02); top: 50%; left: 50%; transform: translate(-50%, -50%);">
                                    0{{ $i + 1 }}
                                </div>

                                <!-- The Rotating Diamond -->
                                <div class="relative z-10 flex items-center justify-center transition-all duration-700 pointer-events-none"
                                     style="width: 100px; height: 100px; background-color: #12141C; border: 1px solid rgba(255,255,255,0.05); border-radius: 20px; transform: rotate(45deg); box-shadow: inset 0 0 15px rgba(0,0,0,0.5), 0 20px 40px rgba(0,0,0,0.5);">
                                    <div style="transform: rotate(-45deg);">
                                        <x-dynamic-component :component="$s['icon']" class="w-8 h-8 transition-colors duration-500 {{ $i == 2 ? 'text-[#FF6B00]' : 'text-white' }} group-hover:text-[#FF6B00]" />
                                    </div>
                                </div>

                                <!-- Floating Mini Step Indicator -->
                                <div class="absolute z-20 flex items-center justify-center font-bold text-white transition-transform duration-500 group-hover:scale-110"
                                     style="width: 30px; height: 30px; background-color: #FF6B00; border-radius: 8px; bottom: 15px; right: 15px; font-size: 12px; box-shadow: 0 5px 15px rgba(255, 107, 0, 0.4);">
                                    0{{ $i + 1 }}
                                </div>
                            </div>

                            <div class="px-2 relative z-10 mt-2">
                                <h3 class="font-black mb-3 uppercase tracking-wider transition-colors duration-300 {{ $i == 2 ? 'text-[#FF6B00]' : 'text-white' }} group-hover:text-[#FF6B00]" style="font-size: 14px;">
                                    {{ $s['title'][$lang] }}
                                </h3>
                                <p class="text-white/40 leading-relaxed max-w-[200px] mx-auto transition-colors duration-300 group-hover:text-white/60" style="font-size: 11px;">
                                    {{ $s['desc'][$lang] }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <!-- === THE KIMMEX ADVANTAGE (Design-Z Premium) === -->
    <section class="py-24 md:py-32 bg-[#F8F9FA] relative overflow-hidden">
        <!-- Decorative abstract lines -->
        <div class="absolute top-0 right-0 w-full h-full opacity-5 pointer-events-none" style="background-image: repeating-linear-gradient(45deg, #0F172A 0, #0F172A 1px, transparent 1px, transparent 40px);"></div>
        
        <div class="max-w-[1400px] mx-auto px-6 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 lg:gap-24 items-center">
                <!-- Left Content -->
                <div x-data="{ shown: false }" x-intersect.once="shown = true" :class="shown ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-12'" class="transition-all duration-1000">
                    <span class="text-titan-red font-bold uppercase tracking-[0.3em] text-xs mb-6 block">{{ __('The Kimmex Advantage') }}</span>
                    <h2 class="text-4xl md:text-5xl font-black text-titan-navy mb-8 uppercase tracking-tighter leading-tight">{{ __('Why Partner With Us?') }}</h2>
                    <p class="text-titan-navy/60 text-lg leading-relaxed mb-12">
                        {{ __('We deliver more than just buildings; we deliver peace of mind. Our integrated approach ensures your project is handled with the utmost care and professionalism.') }}
                    </p>

                    <div class="space-y-4">
                        @foreach([
                            ['icon' => 'lucide-shield-check', 'title' => ['en' => 'Uncompromising Safety', 'kh' => 'សុវត្ថិភាពជាចម្បង'], 'desc' => ['en' => 'Zero-tolerance policy ensuring the safety of all stakeholders.', 'kh' => 'គោលការណ៍តឹងរ៉ឹងបំផុតដើម្បីធានាសុវត្ថិភាពសម្រាប់ភាគីពាក់ព័ន្ធទាំងអស់។']],
                            ['icon' => 'lucide-clock', 'title' => ['en' => 'On-Time Delivery', 'kh' => 'ការប្រគល់ជូនទាន់ពេលវេលា'], 'desc' => ['en' => 'Rigorous scheduling and project management to meet deadlines.', 'kh' => 'ការរៀបចំកាលវិភាគ និងគ្រប់គ្រងគម្រោងយ៉ាងម៉ត់ចត់ដើម្បីឆ្លើយតបពេលវេលាកំណត់។']],
                            ['icon' => 'lucide-zap', 'title' => ['en' => 'Innovative Solutions', 'kh' => 'ដំណោះស្រាយច្នៃប្រឌិត'], 'desc' => ['en' => 'Using modern technologies to solve complex engineering challenges.', 'kh' => 'ប្រើប្រាស់បច្ចេកវិទ្យាទំនើបដើម្បីដោះស្រាយបញ្ហាវិស្វកម្មស្មុគស្មាញ។']],
                        ] as $item)
                        <div class="group flex items-start gap-6 p-6 rounded-2xl hover:bg-white hover:shadow-[0_20px_40px_rgba(0,0,0,0.04)] transition-all duration-500 relative overflow-hidden bg-transparent border border-transparent hover:border-gray-100">
                            <!-- Animated left border accent -->
                            <div class="absolute left-0 top-0 bottom-0 w-1 bg-titan-red scale-y-0 group-hover:scale-y-100 transition-transform duration-500 origin-top"></div>
                            
                            <div class="w-14 h-14 rounded-xl flex items-center justify-center text-titan-navy bg-white shadow-sm border border-gray-100 group-hover:bg-titan-red group-hover:text-white group-hover:shadow-md transition-all duration-500 shrink-0">
                                <x-dynamic-component :component="$item['icon']" class="w-6 h-6" />
                            </div>
                            <div>
                                <h3 class="text-xl font-black text-titan-navy mb-2 uppercase tracking-wide group-hover:text-titan-red transition-colors">{{ $item['title'][$lang] }}</h3>
                                <p class="text-titan-navy/60 leading-relaxed text-sm">{{ $item['desc'][$lang] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Right Staggered Image Grid -->
                <div x-data="{ shown: false }" x-intersect.once="shown = true" :class="shown ? 'opacity-100 translate-x-0' : 'opacity-0 translate-x-12'" class="transition-all duration-1000 delay-200 relative lg:ml-10 mt-10 lg:mt-0">
                    <!-- Architectural Accent Background -->
                    <div class="absolute -inset-10 bg-gradient-to-br from-titan-navy/5 to-transparent rounded-[3rem] -z-10 rotate-[-4deg]"></div>

                    <div class="grid grid-cols-2 gap-4 md:gap-6 relative z-10 w-full h-full">
                        <!-- Left Image -->
                        <div class="relative h-[300px] md:h-[400px] rounded-[2rem] shadow-2xl overflow-hidden translate-y-12 md:translate-y-20 group">
                            <div class="absolute inset-0 bg-titan-navy/10 group-hover:bg-transparent transition-colors duration-500 z-10"></div>
                            <img src="/images/projects/Thumbnail-3.jpg" alt="Excellence" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-1000" />
                        </div>
                        <!-- Right Image -->
                        <div class="relative h-[350px] md:h-[480px] rounded-[2rem] shadow-2xl overflow-hidden group">
                            <div class="absolute inset-0 bg-titan-red/10 group-hover:bg-transparent transition-colors duration-500 z-10"></div>
                            <img src="/images/projects/Thumbnail-5.jpg" alt="Innovation" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-1000" />
                        </div>
                    </div>
                    
                    <!-- Center Floating Glass Badge -->
                    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-white/70 backdrop-blur-2xl text-titan-navy p-6 rounded-full shadow-[0_20px_50px_rgba(0,0,0,0.15)] border border-white z-20 flex flex-col items-center justify-center animate-bounce-slow" style="width: 130px; height: 130px;">
                        <x-lucide-award class="w-10 h-10 text-titan-red mb-2" />
                        <span class="text-[9px] font-black uppercase tracking-[0.2em] text-center leading-tight">{{ __('ISO') }}<br/>{{ __('Certified') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- === SECTORS WE SERVE (Design-Z Style) === -->
    <section class="py-32 bg-white relative overflow-hidden">
        <div class="max-w-[1400px] mx-auto px-6">
            <div x-data="{ shown: false }" x-intersect.once="shown = true" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'" class="flex flex-col md:flex-row md:items-end justify-between mb-20 transition-all duration-1000">
                <div class="max-w-2xl">
                    <span class="text-titan-red font-bold uppercase tracking-[0.4em] text-xs mb-4 block">{{ __('Industries & Sectors') }}</span>
                    <h2 class="text-4xl md:text-6xl font-black text-titan-navy uppercase tracking-tighter leading-none">{{ __('Where We Operate') }}</h2>
                </div>
                <div class="mt-8 md:mt-0">
                    <div class="w-32 h-1.5 bg-titan-red"></div>
                </div>
            </div>

            <div class="flex flex-wrap justify-center gap-8">
                @foreach($sectors as $i => $sector)
                    <div x-data="{ shown: false }" x-intersect.once="shown = true" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'" 
                         style="transition-delay: {{ $i * 100 }}ms"
                         class="group relative h-[500px] w-full md:w-[calc(50%-1rem)] lg:w-[calc(25%-1.5rem)] overflow-hidden rounded-[2rem] bg-[#0F172A] cursor-pointer transition-all duration-700 shadow-2xl">
                        
                        <img src="{{ $sector['image'] }}" class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-all duration-1000 opacity-80" alt="{{ $sector['title'][$lang] }}" />
                        
                        <!-- Gradient Overlay always present to ensure text contrast -->
                        <div class="absolute inset-0 bg-gradient-to-t from-[#0F172A] via-[#0F172A]/40 to-transparent transition-opacity duration-300"></div>
                        
                        <!-- Content -->
                        <div class="absolute inset-0 p-8 flex flex-col justify-end relative z-10">
                            <!-- Icon Badge (Design-Z signature) -->
                            <div class="absolute top-10 right-10 w-16 h-16 bg-white/10 backdrop-blur-md rounded-2xl border border-white/20 flex items-center justify-center text-white opacity-100 md:opacity-0 md:group-hover:opacity-100 -translate-y-4 md:group-hover:translate-y-0 transition-all duration-500">
                                <x-dynamic-component :component="$sector['icon']" class="w-8 h-8" />
                            </div>

                            <div class="transform translate-y-0 transition-transform duration-500">
                                <h3 class="text-3xl font-black text-white mb-4 uppercase tracking-tighter leading-tight">
                                    {{ $sector['title'][$lang] }}
                                </h3>
                                <div class="w-12 h-1.5 bg-[#FF2A00] group-hover:w-24 transition-all duration-500"></div>
                                <p class="text-white/80 mt-6 text-sm leading-relaxed transition-opacity duration-500 delay-100">
                                    {{ __('Delivering tailor-made engineering and construction solutions for the :sector sector.', ['sector' => strtolower($sector['title'][$lang])]) }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- === CTA SECTION (Design-Z Premium) === -->
    <section class="py-20 md:py-32 bg-gray-50 relative overflow-hidden">
        <div class="max-w-[1400px] mx-auto px-6 relative z-10">
            <div x-data="{ shown: false }" x-intersect.once="shown = true" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'" class="bg-titan-navy border border-white/5 rounded-[3rem] shadow-[0_30px_60px_-15px_rgba(15,23,42,0.5)] overflow-hidden relative transition-all duration-1000">
                
                <div class="grid grid-cols-1 lg:grid-cols-2">
                    <!-- Left Content -->
                    <div class="p-10 md:p-20 flex flex-col items-center text-center justify-center relative z-10">
                        <span class="text-titan-red font-bold uppercase tracking-[0.3em] text-xs mb-6 block">{{ __('Work with us') }}</span>
                        <h2 class="text-4xl md:text-6xl font-bold text-white mb-8 tracking-tight leading-tight mx-auto">
                            {{ __('Ready to Start Your') }} <br/>
                            <span class="text-titan-red">{{ __('Next Visionary Project?') }}</span>
                        </h2>
                        <p class="text-white/60 text-lg mb-12 leading-relaxed max-w-xl font-normal mx-auto">
                            {{ __('Join forces with Kimmex for engineering excellence and construction that defines the future.') }}
                        </p>

                        <div class="flex flex-col sm:flex-row gap-5">
                            <a href="/contact" class="flex items-center justify-center gap-3 px-8 py-4 rounded-full font-bold text-sm shadow-xl hover:scale-105 transition-all w-max" style="background-color: #FF2A00; color: white;">
                                <span>{{ __('Get a Free Quote') }}</span>
                                <x-lucide-arrow-right class="w-4 h-4" />
                            </a>
                            <a href="/projects" class="flex items-center justify-center gap-3 px-8 py-4 rounded-full font-bold text-sm border hover:bg-white/5 hover:scale-105 transition-all w-max" style="border-color: rgba(255,255,255,0.2); color: white;">
                                <span>{{ __('View Our Portfolio') }}</span>
                            </a>
                        </div>
                    </div>

                    <!-- Right Image Area -->
                    <div class="relative min-h-[350px] lg:min-h-full hidden md:block">
                        <div class="absolute inset-0 bg-gradient-to-r from-titan-navy via-titan-navy/60 to-transparent z-10 w-2/3 lg:w-1/2"></div>
                        <img src="/images/projects/Thumbnail-1.jpg" alt="Work with us" class="w-full h-full object-cover opacity-80" />
                        
                        <!-- Floating Decorative Badge -->
                        <div class="absolute top-12 right-12 bg-white/10 backdrop-blur-xl border border-white/20 text-white p-5 rounded-3xl z-20 shadow-2xl flex items-center gap-5 animate-bounce-slow shrink-0" style="animation-duration: 4s;">
                            <div class="w-12 h-12 bg-titan-red rounded-full flex items-center justify-center shadow-[0_0_20px_rgba(255,42,0,0.5)]">
                                <x-lucide-phone class="w-5 h-5 text-white" />
                            </div>
                            <div>
                                <p class="text-[10px] text-white/50 uppercase tracking-[0.2em] font-bold mb-1">{{ __('Contact Us') }}</p>
                                <p class="font-black text-lg tracking-tight">info@kimmex.com</p>
                            </div>
                        </div>

                        <!-- Accent Glow -->
                        <div class="absolute bottom-0 right-0 w-80 h-80 bg-titan-red/20 blur-[120px] rounded-full z-0 pointer-events-none"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

</x-layouts.app>
