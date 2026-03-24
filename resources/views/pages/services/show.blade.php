<x-layouts.app :title="'Service Details'" :description="'Detailed information about Kimmex construction services.'">

@php
$lang = app()->getLocale() === 'km' ? 'kh' : app()->getLocale();

// Try to load from DB first
$serviceDb = \App\Models\Service::where('slug', $slug)->where('isActive', true)->first();

if ($serviceDb) {
    $service = [
        "id" => $serviceDb->slug,
        "title" => ["en" => $serviceDb->title, "kh" => $serviceDb->titleKm ?: $serviceDb->title],
        "desc" => [
            "en" => strip_tags($serviceDb->description),
            "kh" => strip_tags($serviceDb->descriptionKm ?: $serviceDb->description)
        ],
        "image" => $serviceDb->image 
            ? (\Illuminate\Support\Str::startsWith($serviceDb->image, '/') ? $serviceDb->image : \Illuminate\Support\Facades\Storage::url($serviceDb->image)) 
            : "/images/projects/Thumbnail-1.jpg",
        "features" => is_array($serviceDb->features) ? $serviceDb->features : [],
    ];
} else {
    // Fallback service data
    $fallbackServices = [
        "design-and-build" => [
            "id" => "design-and-build",
            "title" => ["en" => "Design & Build", "kh" => "រចនា និងសាងសង់"],
            "tagline" => ["en" => "From Concept to Creation", "kh" => "ពីគំនិតដល់ការបង្កើត"],
            "desc" => [
                "en" => "Our flagship service combines architectural creativity with engineering precision. We manage the entire project lifecycle, ensuring seamless transition from blueprints to handover. This integrated approach minimizes risk and accelerates delivery.",
                "kh" => "សេវាកម្មដ៏សំខាន់របស់យើងរួមបញ្ចូលភាពច្នៃប្រឌិតស្ថាបត្យកម្ម និងភាពជាក់លាក់នៃវិស្វកម្ម។ យើងគ្រប់គ្រងវដ្តជីវិតគម្រោងទាំងមូល ធានាបាននូវការផ្លាស់ប្តូរយ៉ាងរលូនពីការគូសប្លង់រហូតដល់ការប្រគល់ជូន។ វិធីសាស្រ្តរួមនេះកាត់បន្ថយហានិភ័យ និងជំរុញការចែកចាយឲ្យបានរហ័ស។"
            ],
            "idealFor" => ["en" => "Real estate developers, commercial business owners, and private investors looking for single-point accountability.", "kh" => "ស័ក្តិសមសម្រាប់អ្នកអភិវឌ្ឍន៍អចលនទ្រព្យ ម្ចាស់អាជីវកម្មពាណិជ្ជកម្ម និងអ្នកវិនិយោគឯកជនដែលកំពុងស្វែងរកការទទួលខុសត្រូវតែមួយ។"],
            "image" => "/images/projects/Thumbnail-1.jpg",
            "icon" => "lucide-pen-tool",
            "scopeItems" => [
                ["en" => "Architectural & structural design", "kh" => "ការរចនាស្ថាបត្យកម្ម និងរចនាសម្ព័ន្ធ"],
                ["en" => "Civil & foundation works", "kh" => "ការងារស៊ីវិល និងគ្រឹះ"],
                ["en" => "MEP systems installation", "kh" => "ការដំឡើងប្រព័ន្ធ MEP"],
                ["en" => "Interior finishing & decoration", "kh" => "ការបញ្ចប់ខាងក្នុង និងការតុបតែង"],
                ["en" => "Exterior cladding & landscaping", "kh" => "ការបិទខាងក្រៅ និងការរៀបចំទេសភាព"],
            ]
        ],
        "building-renovation" => [
            "id" => "building-renovation",
            "title" => ["en" => "Building Renovation", "kh" => "ការជួសជុលអគារ"],
            "tagline" => ["en" => "Transforming Spaces", "kh" => "ផ្លាស់ប្តូរទីកន្លែង"],
            "desc" => [
                "en" => "Comprehensive renovation services for interior and exterior improvements, structural upgrades, and modernization of existing buildings. We breathe new life into aging structures while maintaining their core integrity.",
                "kh" => "សេវាកម្មជួសជុលសម្រាប់ការកែលម្អខាងក្នុង និងខាងក្រៅ ការធ្វើឱ្យប្រសើរឡើងនូវរចនាសម្ព័ន្ធ និងការធ្វើទំនើបកម្មអគារដែលមានស្រាប់។"
            ],
            "idealFor" => ["en" => "Property owners seeking to modernize, upgrade capacity, or repurpose existing buildings.", "kh" => "ម្ចាស់អចលនទ្រព្យដែលចង់ធ្វើទំនើបកម្ម ធ្វើឱ្យប្រសើរឡើង ឬប្រើប្រាស់អគារដែលមានស្រាប់។"],
            "image" => "/images/projects/Thumbnail-2.jpg",
            "icon" => "lucide-hammer",
            "scopeItems" => [
                ["en" => "Interior redesign & remodeling", "kh" => "ការរចនាឡើងវិញ និងកែលម្អខាងក្នុង"],
                ["en" => "Structural reinforcement", "kh" => "ការពង្រឹងរចនាសម្ព័ន្ធ"],
                ["en" => "Facade upgrades", "kh" => "ការធ្វើឱ្យប្រសើរឡើងនូវផ្ទៃខាងក្រៅ"],
            ]
        ],
        "project-management" => [
            "id" => "project-management",
            "title" => ["en" => "Project Management", "kh" => "ការគ្រប់គ្រងគម្រោង"],
            "tagline" => ["en" => "Delivering On Time", "kh" => "ផ្តល់ទាន់ពេលវេលា"],
            "desc" => [
                "en" => "Expert oversight and management of construction projects, ensuring on-time delivery, quality control, cost management, and safety compliance. Our experienced managers keep everything on track.",
                "kh" => "ការត្រួតពិនិត្យ និងគ្រប់គ្រងគម្រោងសំណង់ ធានាការផ្តល់ទាន់ពេល ការត្រួតពិនិត្យគុណភាព ការគ្រប់គ្រងថ្លៃដើម និងការអនុលោមតាមសុវត្ថិភាព។"
            ],
            "idealFor" => ["en" => "Large-scale developers and institutions requiring professional oversight across multiple construction phases.", "kh" => "អ្នកអភិវឌ្ឍន៍ធំ និងស្ថាប័នដែលត្រូវការការត្រួតពិនិត្យជំនាញ។"],
            "image" => "/images/projects/Thumbnail-3.jpg",
            "icon" => "lucide-briefcase",
            "scopeItems" => [
                ["en" => "Project scheduling & timeline management", "kh" => "កាលវិភាគគម្រោង និងការគ្រប់គ្រងពេលវេលា"],
                ["en" => "Budget tracking & cost control", "kh" => "ការតាមដានថវិកា និងការគ្រប់គ្រងចំណាយ"],
                ["en" => "Quality assurance & control", "kh" => "ការធានា និងត្រួតពិនិត្យគុណភាព"],
            ]
        ],
        "consultants" => [
            "id" => "consultants",
            "title" => ["en" => "Consultants", "kh" => "ទីប្រឹក្សា"],
            "tagline" => ["en" => "Expert Guidance", "kh" => "ការណែនាំជំនាញ"],
            "desc" => [
                "en" => "Professional consulting services including project feasibility studies, design consulting, structural analysis, and expert advisory for complex engineering challenges.",
                "kh" => "សេវាកម្មប្រឹក្សាវិជ្ជាជីវៈ រួមទាំងការសិក្សាលទ្ធភាពគម្រោង ការប្រឹក្សាការរចនា ការវិភាគរចនាសម្ព័ន្ធ និងការប្រឹក្សាជំនាញ។"
            ],
            "idealFor" => ["en" => "Government agencies, NGOs, and private investors seeking independent technical reviews and feasibility assessments.", "kh" => "ទីភ្នាក់ងាររដ្ឋាភិបាល អង្គការក្រៅរដ្ឋាភិបាល និងអ្នកវិនិយោគឯកជន។"],
            "image" => "/images/projects/Thumbnail-4.jpg",
            "icon" => "lucide-lightbulb",
            "scopeItems" => [
                ["en" => "Feasibility studies & site assessment", "kh" => "ការសិក្សាលទ្ធភាព និងការវាយតម្លៃទីតាំង"],
                ["en" => "Design review & optimization", "kh" => "ការពិនិត្យការរចនា និងការបង្កើនប្រសិទ្ធភាព"],
                ["en" => "Structural & geotechnical analysis", "kh" => "ការវិភាគរចនាសម្ព័ន្ធ និងភូមិសាស្ត្រ"],
            ]
        ]
    ];

    $service = $fallbackServices[$slug] ?? null;
    if (!$service) abort(404);
}

$roadmap = [
    ['step' => '01', 'icon' => 'lucide-search', 'title' => ['en' => 'Consultation', 'kh' => 'ការប្រឹក្សា'], 'desc' => ['en' => 'Understanding your vision, budget, and feasibility analysis.', 'kh' => 'ការយល់ដឹងពីចក្ខុវិស័យ ថវិកា និងការវិភាគសមិទ្ធភាព។']],
    ['step' => '02', 'icon' => 'lucide-pen-tool', 'title' => ['en' => 'Design & Strategy', 'kh' => 'រចនា និងយុទ្ធសាស្រ្ត'], 'desc' => ['en' => 'Creating architectural blueprints and detailed strategy.', 'kh' => 'ការបង្កើតប្លង់ស្ថាបត្យកម្ម និងយុទ្ធសាស្រ្តលម្អិត។']],
    ['step' => '03', 'icon' => 'lucide-hammer', 'title' => ['en' => 'Construction', 'kh' => 'សាងសង់'], 'desc' => ['en' => 'Quality-controlled construction execution on-site.', 'kh' => 'ការអនុវត្តសាងសង់ប្រកបដោយការគ្រប់គ្រងគុណភាព។']],
    ['step' => '04', 'icon' => 'lucide-check-circle-2', 'title' => ['en' => 'Handover', 'kh' => 'ប្រគល់ជូន'], 'desc' => ['en' => 'Final inspection, documentation, and key handover.', 'kh' => 'ការត្រួតពិនិត្យចុងក្រោយ ឯកសារ និងការប្រគល់សោ។']],
];

$valueProp = [
    ['icon' => 'lucide-users', 'title' => ['en' => 'Single Point of Contact', 'kh' => 'ចំណុចទំនាក់ទំនងតែមួយ'], 'desc' => ['en' => 'Streamlined communication and accountability.', 'kh' => 'ការប្រាស្រ័យទាក់ទង និងការទទួលខុសត្រូវមានប្រសិទ្ធភាព។']],
    ['icon' => 'lucide-clock', 'title' => ['en' => 'Faster Timeline', 'kh' => 'ពេលវេលាលឿនរហ័ស'], 'desc' => ['en' => 'Overlapping design and construction phases.', 'kh' => 'ការត្រួតគ្នានៃដំណាក់កាលរចនា និងការសាងសង់។']],
    ['icon' => 'lucide-trending-up', 'title' => ['en' => 'Cost Certainty', 'kh' => 'ភាពប្រាកដប្រជាថ្លៃដើម'], 'desc' => ['en' => 'Reduced change orders and accurate budgeting.', 'kh' => 'កាត់បន្ថយការផ្លាស់ប្តូរ និងរៀបចំថវិកាបានត្រឹមត្រូវ។']],
    ['icon' => 'lucide-shield-check', 'title' => ['en' => 'Quality Assurance', 'kh' => 'ធានាគុណភាព'], 'desc' => ['en' => 'Professional teams ensuring design-intent alignment.', 'kh' => 'ក្រុមការងារប្រកបដោយវិជ្ជាជីវៈធានាបាននូវការរចនាស្របតាមគោលដៅ។']],
];

$featuredProjects = [
    ['id' => '1', 'title' => ['en' => 'Vatthanak Capital Expansion', 'kh' => 'ការពង្រីកបរិវេណ វឌ្ឍនៈ កាពីតាល'], 'category' => ['en' => 'Commercial', 'kh' => 'ពាណិជ្ជកម្ម'], 'location' => ['en' => 'Phnom Penh', 'kh' => 'ភ្នំពេញ'], 'image' => '/images/projects/Thumbnail-1.jpg'],
    ['id' => '2', 'title' => ['en' => 'Skyline Residences', 'kh' => 'អគារលំនៅដ្ឋាន Skyline'], 'category' => ['en' => 'Residential', 'kh' => 'លំនៅដ្ឋាន'], 'location' => ['en' => 'Siem Reap', 'kh' => 'សៀមរាប'], 'image' => '/images/projects/Thumbnail-2.jpg'],
];
@endphp

<div class="bg-white min-h-screen text-titan-navy">

    <!-- === 1. PARALLAX HERO === -->
    <section class="relative h-[80vh] flex items-center justify-center overflow-hidden bg-titan-navy">
        <div class="absolute inset-0">
            <!-- Simplified parallax effect for blade without framer-motion -->
            <img src="{{ $service['image'] }}" alt="{{ $service['title'][$lang] }}" class="w-full h-[120%] object-cover opacity-50 mix-blend-overlay -translate-y-[10%]" />
            <div class="absolute inset-0 bg-gradient-to-b from-titan-navy/80 via-titan-navy/40 to-titan-navy"></div>
        </div>

        <div class="relative z-10 text-center max-w-5xl px-6 pt-20 mt-10" x-data="{ shown: false }" x-init="setTimeout(() => shown = true, 100)" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'" class="transition-all duration-1000">
            <a href="/services" class="inline-flex items-center gap-2 text-white/60 hover:text-titan-red transition-all font-bold uppercase tracking-widest text-xs mb-8 group">
                <div class="w-8 h-8 rounded-full border border-white/20 flex items-center justify-center group-hover:border-titan-red group-hover:bg-titan-red group-hover:text-white transition-all">
                    <x-lucide-arrow-left class="w-3 h-3" />
                </div>
                {{ __('Back') }}
            </a>

            <div class="mx-auto w-24 h-24 bg-white/5 rounded-3xl flex items-center justify-center mb-8 backdrop-blur-md border border-white/10 shadow-2xl">
                <x-dynamic-component :component="$service['icon'] ?? 'lucide-building'" class="w-12 h-12 text-white drop-shadow-lg" />
            </div>

            <h1 class="text-5xl md:text-7xl lg:text-8xl font-black text-white mb-6 uppercase tracking-tighter">
                {{ $service['title'][$lang] }}
            </h1>

            <p class="text-xl md:text-2xl text-white/80 max-w-3xl mx-auto font-light leading-relaxed">
                {{ $service['tagline'][$lang] ?? $service['title'][$lang] }}
            </p>
        </div>
    </section>

    <!-- === 2. SERVICE OVERVIEW === -->
    <section class="py-24 px-6 max-w-[1400px] mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-20 items-center">
            <div x-data="{ shown: false }" x-intersect.once="shown = true" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'" class="transition-all duration-1000">
                <div class="mb-12">
                    <span class="text-titan-red font-bold uppercase tracking-widest text-sm mb-4 block">{{ __('Overview') }}</span>
                    <h2 class="text-4xl md:text-5xl font-black text-titan-navy mb-6">
                        {{ $lang === 'kh' ? 'ការកំណត់ឡើងវិញនូវ' : 'Redefining' }} {{ $service['title'][$lang] }}
                    </h2>
                    <div class="text-lg md:text-xl text-titan-navy/60 leading-relaxed mb-10 prose prose-lg prose-slate max-w-none">
                        {{ $service['desc'][$lang] }}
                    </div>
                </div>

                @if(!empty($service['idealFor'][$lang] ?? ''))
                <div class="bg-gray-50 p-8 rounded-2xl border-l-4 border-titan-red shadow-sm">
                    <h3 class="text-xl font-bold text-titan-navy mb-3 flex items-center gap-3">
                        <div class="p-2 bg-titan-red/10 rounded-lg">
                            <x-lucide-users class="w-5 h-5 text-titan-red" />
                        </div>
                        {{ $lang === 'kh' ? 'ស័ក្តិសមសម្រាប់' : 'Ideal For' }}
                    </h3>
                    <div class="text-titan-navy/70 leading-relaxed prose prose-sm prose-slate max-w-none">
                        {{ $service['idealFor'][$lang] }}
                    </div>
                </div>
                @endif
            </div>

            <div x-data="{ shown: false }" x-intersect.once="shown = true" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'" class="transition-all duration-1000 delay-200">
                <div class="relative group">
                    <div class="absolute -inset-4 bg-titan-red/5 rounded-[2rem] rotate-2 group-hover:rotate-1 transition-transform duration-500"></div>
                    <div class="aspect-[4/3] rounded-2xl overflow-hidden shadow-2xl relative z-10 bg-titan-navy">
                        <img src="{{ $service['image'] }}" alt="{{ $service['title'][$lang] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" />
                        <div class="absolute inset-0 bg-titan-navy/10 group-hover:bg-transparent transition-colors duration-500"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- === 3. SCOPE OF WORK === -->
    @if(!empty($service['scopeItems'] ?? []))
    <section class="py-24 bg-titan-navy text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-titan-red/5 rounded-full blur-[120px] pointer-events-none"></div>

        <div class="max-w-[1400px] mx-auto px-6 relative z-10">
            <div x-data="{ shown: false }" x-intersect.once="shown = true" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'" class="text-center mb-16 transition-all duration-1000">
                <span class="text-titan-red font-bold uppercase tracking-widest text-sm mb-4 block">{{ $lang === 'kh' ? 'វិសាលភាពការងារ' : 'Scope of Work' }}</span>
                <h2 class="text-4xl md:text-5xl font-black mb-6">{{ $lang === 'kh' ? 'សេវាកម្មដ៏ទូលំទូលាយ' : 'Comprehensive Coverage' }}</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($service['scopeItems'] as $i => $item)
                    <div x-data="{ shown: false }" x-intersect.once="shown = true" style="transition-delay: {{ $i * 100 }}ms" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'" 
                         class="group flex items-start gap-5 p-8 bg-white/5 border border-white/10 rounded-2xl hover:bg-white/10 hover:border-titan-red/30 transition-all duration-300 h-full">
                        <div class="w-10 h-10 rounded-full bg-titan-red/20 flex items-center justify-center shrink-0 group-hover:bg-titan-red group-hover:text-white transition-colors duration-300">
                            <x-lucide-check-circle-2 class="w-5 h-5 text-titan-red group-hover:text-white" />
                        </div>
                        <span class="font-bold text-lg leading-tight pt-2 group-hover:text-titan-red transition-colors">{{ $item[$lang] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- === 4. PROCESS / HOW WE DELIVER === -->
    <section class="py-24 px-6 bg-white">
        <div class="max-w-[1400px] mx-auto">
            <div x-data="{ shown: false }" x-intersect.once="shown = true" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'" class="text-center max-w-3xl mx-auto mb-20 transition-all duration-1000">
                <span class="text-accent-orange font-bold uppercase tracking-widest text-sm mb-4 block">{{ $lang === 'kh' ? 'ដំណើរការរបស់យើង' : 'Our Process' }}</span>
                <h2 class="text-4xl md:text-5xl font-black text-titan-navy mb-6">{{ $lang === 'kh' ? 'មាគ៌ាឆ្ពោះទៅរកភាពជោគជ័យ' : 'The Path to Success' }}</h2>
                <p class="text-gray-500 text-lg">{{ $lang === 'kh' ? 'វិធីសាស្រ្តដែលមានរចនាសម្ព័ន្ធ និងតម្លាភាពដើម្បីធានាភាពជោគជ័យនៃគម្រោងរបស់អ្នក។' : 'A transparent, structured approach to ensure your project\'s success.' }}</p>
            </div>

            <div class="relative mt-32">
                <!-- Connecting Line -->
                <div class="hidden md:block absolute top-[55px] left-[10%] right-[10%] h-[1px] bg-accent-orange/20 z-0"></div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-12 relative z-10">
                    @foreach($roadmap as $i => $step)
                        <div x-data="{ shown: false }" x-intersect.once="shown = true" style="transition-delay: {{ $i * 100 }}ms" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'" 
                             class="flex flex-col items-center text-center group transition-all duration-1000">
                            
                            <div class="relative mb-16 flex justify-center">
                                <!-- Large Background Number -->
                                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-[180px] font-black text-gray-50 group-hover:text-accent-orange/[0.05] transition-colors duration-500 pointer-events-none z-0 tracking-tighter leading-none select-none">
                                    {{ $step['step'] }}
                                </div>

                                <!-- Glowing shadow effect on hover -->
                                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-32 h-32 bg-accent-orange/20 rounded-full blur-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-500 z-0"></div>

                                <!-- The Dark Diamond -->
                                <div class="w-[110px] h-[110px] bg-[#0f1423] rounded-3xl flex items-center justify-center relative z-10 rotate-45 border-2 border-transparent group-hover:border-accent-orange transition-all duration-500 shadow-[0_20px_40px_rgba(0,0,0,0.08)] group-hover:shadow-[0_0_40px_rgba(255,107,0,0.2)]">
                                    <!-- Un-rotate the icon inside -->
                                    <div class="-rotate-45 flex flex-col items-center">
                                        <x-dynamic-component :component="$step['icon']" class="w-8 h-8 text-white group-hover:text-accent-orange transition-colors duration-300 stroke-[1.5]" />
                                    </div>
                                </div>

                                <!-- Step Number Badge (Orange box with white border) -->
                                <div class="absolute -bottom-2 -right-4 w-11 h-11 bg-accent-orange rounded-xl flex items-center justify-center border-[4px] border-white z-20 transition-transform duration-500 group-hover:scale-110 shadow-sm">
                                    <span class="text-[13px] font-black text-white tracking-tight">{{ $step['step'] }}</span>
                                </div>
                            </div>

                            <div class="px-2">
                                <h3 class="text-xl font-bold text-titan-navy mb-3 group-hover:text-accent-orange transition-colors duration-300">
                                    {{ $step['title'][$lang] }}
                                </h3>
                                <p class="text-sm text-gray-500 leading-relaxed max-w-[240px] mx-auto">
                                    {{ $step['desc'][$lang] }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <!-- === 5. KEY BENEFITS === -->
    <section class="py-24 px-6 max-w-[1400px] mx-auto">
        <div x-data="{ shown: false }" x-intersect.once="shown = true" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'" class="text-center mb-16 transition-all duration-1000">
            <span class="text-titan-red font-bold uppercase tracking-widest text-sm mb-4 block">{{ $lang === 'kh' ? 'ហេតុអ្វីជ្រើសរើសយើង' : 'Why Choose Us' }}</span>
            <h2 class="text-4xl md:text-5xl font-black text-titan-navy">{{ $lang === 'kh' ? 'គុណតម្លៃដែលផ្តល់ជូន' : 'Value Delivered' }}</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach($valueProp as $i => $benefit)
                <div x-data="{ shown: false }" x-intersect.once="shown = true" style="transition-delay: {{ $i * 100 }}ms" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'" 
                     class="bg-white p-8 rounded-2xl shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 border border-gray-100 group h-full">
                    <div class="w-16 h-16 bg-titan-navy/5 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-titan-red group-hover:text-white transition-all duration-300">
                        <x-dynamic-component :component="$benefit['icon']" class="w-7 h-7 text-titan-navy group-hover:text-white transition-colors" />
                    </div>
                    <h3 class="text-xl font-bold text-titan-navy mb-3 group-hover:text-titan-red transition-colors">{{ $benefit['title'][$lang] }}</h3>
                    <p class="text-titan-navy/60 leading-relaxed">
                        {{ $benefit['desc'][$lang] }}
                    </p>
                </div>
            @endforeach
        </div>
    </section>

    <!-- === 6. FEATURED PROJECTS === -->
    <section class="py-24 bg-titan-navy text-white px-6">
        <div class="max-w-[1400px] mx-auto">
            <div x-data="{ shown: false }" x-intersect.once="shown = true" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'" class="flex flex-col md:flex-row justify-between items-end mb-16 border-b border-white/10 pb-8 transition-all duration-1000">
                <div>
                    <span class="text-titan-red font-bold uppercase tracking-widest text-sm mb-4 block">{{ $lang === 'kh' ? 'ស្នាដៃ' : 'Portfolio' }}</span>
                    <h2 class="text-4xl md:text-5xl font-black">{{ __('Featured Projects') }}</h2>
                </div>
                <a href="/projects" class="mt-8 md:mt-0 px-8 py-3 bg-white/10 hover:bg-white hover:text-titan-navy transition-all font-bold uppercase tracking-widest text-sm flex items-center gap-2 rounded-lg backdrop-blur-sm">
                    {{ $lang === 'kh' ? 'មើលគម្រោងទាំងអស់' : 'View All Projects' }} <x-lucide-arrow-right class="w-4 h-4" />
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                @foreach($featuredProjects as $i => $project)
                    <div x-data="{ shown: false }" x-intersect.once="shown = true" style="transition-delay: {{ $i * 100 }}ms" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'" class="transition-all duration-1000">
                        <a href="/projects/{{ $project['id'] }}" class="group relative aspect-[16/9] overflow-hidden rounded-2xl cursor-pointer block shadow-2xl h-full">
                            <img src="{{ $project['image'] }}" alt="{{ $project['title'][$lang] }}" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" />
                            <div class="absolute inset-0 bg-gradient-to-t from-titan-navy via-titan-navy/40 to-transparent opacity-80 group-hover:opacity-60 transition-opacity"></div>

                            <div class="absolute bottom-0 left-0 p-8 w-full">
                                <div class="transform translate-y-4 group-hover:translate-y-0 transition-transform duration-500">
                                    <span class="inline-block bg-titan-red text-white text-xs font-bold uppercase tracking-widest px-3 py-1 rounded mb-3">{{ $project['category'][$lang] }}</span>
                                    <h3 class="text-2xl md:text-3xl font-bold text-white mb-2">{{ $project['title'][$lang] }}</h3>
                                    <div class="flex items-center gap-2 text-white/80 text-sm">
                                        <x-lucide-map-pin class="w-4 h-4 text-titan-red" /> {{ $project['location'][$lang] }}
                                    </div>
                                </div>
                            </div>

                            <div class="absolute top-6 right-6 w-12 h-12 bg-white/20 backdrop-blur-md rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transform translate-x-4 group-hover:translate-x-0 transition-all duration-300">
                                <x-lucide-arrow-right class="w-5 h-5 text-white" />
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- === FOOTER CTA === -->
    <section class="py-24 bg-white text-center px-6">
        <div class="max-w-3xl mx-auto bg-titan-red rounded-3xl p-12 md:p-16 shadow-2xl shadow-titan-red/30 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full blur-[50px] translate-x-1/2 -translate-y-1/2 pointer-events-none"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 bg-black/10 rounded-full blur-[50px] -translate-x-1/2 translate-y-1/2 pointer-events-none"></div>

            <div x-data="{ shown: false }" x-intersect.once="shown = true" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'" class="transition-all duration-1000 relative z-10">
                <h2 class="text-3xl md:text-5xl font-black text-white mb-6">{{ $lang === 'kh' ? 'រួចរាល់សម្រាប់ការចាប់ផ្តើម?' : 'Ready to start?' }}</h2>
                <p class="text-white/90 text-xl mb-10 font-medium">
                    {{ $lang === 'kh' ? 'ទាក់ទងក្រុមការងារជំនាញរបស់យើងថ្ងៃនេះ សម្រាប់ការពិគ្រោះយោបល់ និងការសិក្សាសមិទ្ធភាពដោយឥតគិតថ្លៃ។' : 'Contact our expert team today for a free consultation and feasibility study.' }}
                </p>
                <a href="/contact" class="inline-flex items-center gap-2 bg-white text-titan-red px-10 py-5 font-bold uppercase tracking-widest hover:bg-titan-navy hover:text-white transition-all shadow-xl rounded-lg group">
                    {{ $lang === 'kh' ? 'ស្នើសុំការប្រឹក្សា' : 'Request Quote' }} <x-lucide-arrow-right class="w-4 h-4 group-hover:translate-x-1 transition-transform" />
                </a>
            </div>
        </div>
    </section>
</div>

</x-layouts.app>
