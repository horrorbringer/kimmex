<x-layouts.app title="Services" description="Explore our comprehensive construction and engineering services provided by Kimmex.">

@php
$lang = app()->getLocale() === 'km' ? 'kh' : app()->getLocale();

$services = \Illuminate\Support\Facades\Cache::remember('services_index_data', now()->addHours(12), function() {
    $servicesDb = \App\Models\Service::where('isActive', true)->orderBy('orderIndex')->get();
    return $servicesDb->map(function($service) {
        return [
            "id" => $service->slug,
            "title" => ["en" => $service->getTranslation('title', 'en'), "kh" => $service->getTranslation('title', 'km')],
            "desc" => [
                "en" => strip_tags($service->getTranslation('description', 'en')),
                "kh" => strip_tags($service->getTranslation('description', 'km'))
            ],
            "image" => \App\Support\PublicStorage::urlIfExists($service->image, "/images/webp/projects/Thumbnail-1.webp"),
            "features" => is_array($service->features) ? $service->features : []
        ];
    })->toArray();
});

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
            "image" => "/images/webp/projects/Thumbnail-1.webp",
            "features" => [["name" => "Detail Design"], ["name" => "Civil Work"], ["name" => "MEP Work"], ["name" => "Finishing Work"], ["name" => "Decoration"]]
        ],
        [
            "id" => "construction",
            "title" => ["en" => "Construction", "kh" => "សាងសង់"],
            "desc" => [
                "en" => "Premium civil construction services across Cambodia specializing in robust concrete work, high-rise buildings, and commercial spaces.",
                "kh" => "សេវាកម្មសំណង់ស៊ីវិលលំដាប់ខ្ពស់ប្រចាំប្រទេសកម្ពុជាដែលមានជំនាញលើការងារបេតុងដ៏រឹងមាំ អគារខ្ពស់ៗ និងអគារពាណិជ្ជកម្ម។"
            ],
            "image" => "/images/webp/projects/Thumbnail-1.webp",
            "features" => [["name" => "High-Rise Buildings"], ["name" => "Commercial Spaces"], ["name" => "Quality Assurance"]]
        ],
        [
            "id" => "project-management",
            "title" => ["en" => "Project Management", "kh" => "ការគ្រប់គ្រងគម្រោង"],
            "desc" => [
                "en" => "Expert oversight and management of construction projects, ensuring on-time delivery, quality control, cost management, and safety compliance.",
                "kh" => "ការត្រួតពិនិត្យ និងគ្រប់គ្រងគម្រោងសំណង់ ធានាការផ្តល់ទាន់ពេល ការត្រួតពិនិត្យគុណភាព ការគ្រប់គ្រងថ្លៃដើម និងការអនុលោមតាមសុវត្ថិភាព។"
            ],
            "image" => "/images/webp/projects/Thumbnail-3.webp",
            "features" => [["name" => "Scheduling & Planning"], ["name" => "Quality Control"], ["name" => "Cost Management"], ["name" => "Safety Compliance"]]
        ],
        [
            "id" => "consultants",
            "title" => ["en" => "Consultants", "kh" => "ទីប្រឹក្សា"],
            "desc" => [
                "en" => "Professional consulting services including project feasibility studies, design consulting, structural analysis, and expert advisory for complex engineering challenges.",
                "kh" => "សេវាកម្មប្រឹក្សាវិជ្ជាជីវៈ រួមទាំងការសិក្សាលទ្ធភាពគម្រោង ការប្រឹក្សាការរចនា ការវិភាគរចនាសម្ព័ន្ធ និងការប្រឹក្សាជំនាញ។"
            ],
            "image" => "/images/webp/projects/Thumbnail-4.webp",
            "features" => [["name" => "Project Feasibility"], ["name" => "Design Consulting"], ["name" => "Structural Analysis"]]
        ]
    ];
}

$process = \Illuminate\Support\Facades\Cache::remember('services_process_array_'.app()->getLocale(), now()->addHours(12), function() {
    $processDb = \App\Models\MethodologyStep::where('isActive', true)->orderBy('orderIndex')->get();
    return $processDb->map(function($step, $index) {
        return [
            "step" => str_pad($index + 1, 2, '0', STR_PAD_LEFT),
            "icon" => $step->icon ?: 'lucide-check-circle',
            "title" => ["en" => $step->getTranslation('title', 'en'), "kh" => $step->getTranslation('title', 'km')],
            "desc" => [
                "en" => trim(strip_tags($step->getTranslation('description', 'en'))),
                "kh" => trim(strip_tags($step->getTranslation('description', 'km'))),
            ]
        ];
    })->toArray();
});

$defaultProcess = [
    [
        "step" => "01",
        "icon" => "lucide-users",
        "title" => ["en" => "Consultation & Analysis", "kh" => "ការពិគ្រោះយោបល់ និងការវិភាគ"],
        "desc" => ["en" => "We clarify project goals, review site conditions, and confirm practical requirements before work begins.", "kh" => "យើងកំណត់គោលដៅគម្រោង ពិនិត្យលក្ខខណ្ឌទីតាំង និងបញ្ជាក់តម្រូវការជាក់ស្តែងមុនចាប់ផ្តើមការងារ។"]
    ],
    [
        "step" => "02",
        "icon" => "lucide-ruler",
        "title" => ["en" => "Planning & Design", "kh" => "ការរៀបចំផែនការ និងរចនា"],
        "desc" => ["en" => "Our team prepares the scope, design direction, timeline, budget baseline, and approval path.", "kh" => "ក្រុមការងាររៀបចំវិសាលភាព ទិសដៅរចនា កាលវិភាគ ថវិកាមូលដ្ឋាន និងដំណើរការអនុម័ត។"]
    ],
    [
        "step" => "03",
        "icon" => "lucide-hard-hat",
        "title" => ["en" => "Construction Execution", "kh" => "ការអនុវត្តសំណង់"],
        "desc" => ["en" => "We coordinate teams, materials, site safety, and daily progress so the project moves according to plan.", "kh" => "យើងសម្របសម្រួលក្រុមការងារ សម្ភារៈ សុវត្ថិភាពទីតាំង និងវឌ្ឍនភាពប្រចាំថ្ងៃឲ្យគម្រោងដំណើរការតាមផែនការ។"]
    ],
    [
        "step" => "04",
        "icon" => "lucide-shield-check",
        "title" => ["en" => "Quality Control", "kh" => "ការត្រួតពិនិត្យគុណភាព"],
        "desc" => ["en" => "Each stage is checked against technical standards, drawings, and client expectations before moving forward.", "kh" => "រាល់ដំណាក់កាលត្រូវបានត្រួតពិនិត្យតាមស្តង់ដារបច្ចេកទេស គំនូសប្លង់ និងការរំពឹងទុករបស់អតិថិជនមុនបន្តការងារ។"]
    ],
    [
        "step" => "05",
        "icon" => "lucide-check-circle-2",
        "title" => ["en" => "Handover & Support", "kh" => "ការប្រគល់ការងារ និងគាំទ្រ"],
        "desc" => ["en" => "We complete documentation, final inspection, handover, and follow-up support for a clean project close.", "kh" => "យើងបញ្ចប់ឯកសារ ការត្រួតពិនិត្យចុងក្រោយ ការប្រគល់ការងារ និងការគាំទ្របន្តសម្រាប់បិទគម្រោងឲ្យបានរលូន។"]
    ],
];

if (count($process) < 4) {
    $process = array_slice(array_values(array_replace($defaultProcess, $process)), 0, 5);
}

$sectors = [
    ["title" => ["en" => "Government Offices", "kh" => "ការិយាល័យរដ្ឋាភិបាល"], "image" => "/images/webp/projects/Thumbnail-1.webp", "icon" => "lucide-landmark"],
    ["title" => ["en" => "Education", "kh" => "អប់រំ"], "image" => "/images/webp/projects/Thumbnail-2.webp", "icon" => "lucide-graduation-cap"],
    ["title" => ["en" => "Commercial", "kh" => "ពាណិជ្ជកម្ម"], "image" => "/images/webp/projects/Thumbnail-3.webp", "icon" => "lucide-building"],
    ["title" => ["en" => "Infrastructure", "kh" => "ហេដ្ឋារចនាសម្ព័ន្ធ"], "image" => "/images/webp/projects/Thumbnail-6.webp", "icon" => "lucide-route"]
];
@endphp

<div class="bg-white min-h-screen text-titan-navy">

    <!-- HERO -->
    <section class="relative h-[320px] md:h-[380px] flex items-end overflow-hidden bg-titan-navy">
        <div class="absolute inset-0">
            <img src="/images/webp/projects/Thumbnail-1.webp" alt="Kimmex Services"
                class="w-full h-full object-cover opacity-50" loading="eager" decoding="async" fetchpriority="high" />
            <div class="absolute inset-0 bg-gradient-to-t from-titan-navy/90 via-titan-navy/40 to-transparent"></div>
        </div>
        <div class="relative z-10 w-full max-w-[1200px] mx-auto px-6 pb-10 md:pb-12">
            <p class="text-[9px] font-black uppercase tracking-[0.35em] text-titan-red mb-2">{{ __('Kimmex') }}</p>
            <h1 class="font-black text-white uppercase leading-none"
                style="font-size: clamp(1.6rem, 4vw, 2.6rem) !important; color: white !important; font-weight: 900 !important;">
                {{ __('Our Services') }}
            </h1>
            <p class="text-white/50 text-sm mt-2 max-w-lg">{{ __('Precision engineering and construction solutions across Cambodia.') }}</p>
        </div>
    </section>

    <!-- SERVICES -->
    <section id="services-list" class="py-12 md:py-16 max-w-[1200px] mx-auto px-6">
        <div class="flex items-center gap-3 mb-8 md:mb-10">
            <div class="w-5 h-[2px] bg-titan-red"></div>
            <div>
                <p class="text-[9px] font-black uppercase tracking-[0.3em] text-titan-red">{{ __('What We Do') }}</p>
                <h2 class="services-title text-2xl md:text-3xl font-black text-titan-navy uppercase tracking-tight">{{ __('Capabilities & Expertise') }}</h2>
            </div>
        </div>

        <div class="space-y-4">
            @foreach($services as $i => $service)
            <div class="group grid grid-cols-1 md:grid-cols-12 border border-gray-100 rounded-lg overflow-hidden bg-white hover:border-titan-red/20 hover:shadow-[0_4px_24px_-6px_rgba(11,43,92,0.12)] transition-all duration-300">

                {{-- Image --}}
                <div class="md:col-span-4 relative h-48 md:h-auto overflow-hidden {{ $i % 2 === 1 ? 'md:order-last' : '' }}">
                    <img src="{{ $service['image'] }}" alt="{{ $service['title'][$lang] }}"
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" loading="lazy" decoding="async" />
                    <div class="absolute inset-0 bg-gradient-to-t from-titan-navy/40 to-transparent md:bg-none"></div>
                    <span class="absolute bottom-3 left-4 md:hidden text-white text-[9px] font-black uppercase tracking-[0.2em]">{{ sprintf('%02d', $i + 1) }}</span>
                </div>

                {{-- Content --}}
                <div class="md:col-span-8 p-5 md:p-8 flex flex-col justify-center">
                    <div class="flex items-center gap-2.5 mb-3">
                        <span class="text-[9px] font-black text-titan-red/50 tracking-[0.2em]">{{ sprintf('%02d', $i + 1) }}</span>
                        <div class="w-4 h-[1px] bg-titan-red/30"></div>
                        <h3 class="services-title text-lg md:text-xl font-black text-titan-navy uppercase tracking-tight group-hover:text-titan-red transition-colors">{{ $service['title'][$lang] }}</h3>
                    </div>

                    <p class="text-base text-titan-navy/55 leading-relaxed mb-5 max-w-2xl">{{ $service['desc'][$lang] }}</p>

                    @php $featuresArray = is_array($service['features']) ? $service['features'] : []; @endphp
                    @if(count($featuresArray) > 0)
                    <div class="flex flex-wrap gap-1.5 mb-4">
                        @foreach($featuresArray as $feature)
                        <span class="inline-flex items-center gap-1 h-6 px-2.5 rounded-full bg-gray-50 border border-gray-100 text-xs font-bold uppercase tracking-[0.1em] text-titan-navy/60">
                            <span class="w-1 h-1 rounded-full bg-titan-red shrink-0"></span>
                            {{ __($feature['name'] ?? (is_array($feature) ? ($feature[$lang] ?? '') : $feature)) }}
                        </span>
                        @endforeach
                    </div>
                    @endif

                    <a href="/services/{{ $service['id'] }}"
                        class="inline-flex items-center gap-1.5 text-[11px] font-black uppercase tracking-[0.18em] text-titan-navy/50 hover:text-titan-red transition-colors group/link">
                        {{ __('Learn More') }}
                        <x-lucide-arrow-right class="w-3 h-3 group-hover/link:translate-x-1 transition-transform" />
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </section>

    <!-- METHODOLOGY -->
    <section class="py-12 md:py-16 bg-gray-50 border-y border-gray-100">
        <div class="max-w-[1200px] mx-auto px-6">
            <div class="flex items-center gap-3 mb-8">
                <div class="w-5 h-[2px] bg-titan-red"></div>
                <div>
                    <p class="text-[9px] font-black uppercase tracking-[0.3em] text-titan-red">{{ __('How It Works') }}</p>
                    <h2 class="services-title text-2xl md:text-3xl font-black text-titan-navy uppercase tracking-tight">{{ __('Our Methodology') }}</h2>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                @foreach($process as $i => $s)
                <div class="group bg-white border border-gray-100 rounded-lg p-4 hover:border-titan-red/25 hover:shadow-[0_4px_20px_-6px_rgba(11,43,92,0.12)] transition-all duration-300 relative overflow-hidden">
                    <div class="absolute top-3 right-3 text-5xl font-black leading-none text-titan-navy/[0.04]">{{ sprintf('%02d', $i + 1) }}</div>
                    <div class="w-8 h-8 rounded bg-gray-50 border border-gray-100 group-hover:bg-titan-red group-hover:border-titan-red flex items-center justify-center mb-3 transition-all duration-300">
                        <x-dynamic-component :component="$s['icon']" class="w-3.5 h-3.5 text-titan-red group-hover:text-white transition-colors duration-300" stroke-width="1.7" />
                    </div>
                    <div class="text-[9px] font-black uppercase tracking-[0.2em] text-titan-red/50 mb-1">{{ __('Step') }} {{ sprintf('%02d', $i + 1) }}</div>
                    <h3 class="services-title text-sm font-black text-titan-navy uppercase tracking-tight leading-tight mb-2 group-hover:text-titan-red transition-colors">
                        {{ is_array($s['title']) ? ($s['title'][$lang] ?? $s['title']['en'] ?? '') : $s['title'] }}
                    </h3>
                    <p class="text-xs text-titan-navy/55 leading-relaxed">
                        {{ is_array($s['desc']) ? ($s['desc'][$lang] ?? $s['desc']['en'] ?? '') : $s['desc'] }}
                    </p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- ADVANTAGE -->
    <section class="py-12 md:py-16 max-w-[1200px] mx-auto px-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 items-center">
            <div>
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-5 h-[2px] bg-titan-red"></div>
                    <div>
                        <p class="text-[9px] font-black uppercase tracking-[0.3em] text-titan-red">{{ __('The Kimmex Advantage') }}</p>
                        <h2 class="services-title text-2xl md:text-3xl font-black text-titan-navy uppercase tracking-tight">{{ __('Why Partner With Us?') }}</h2>
                    </div>
                </div>
                <p class="text-base text-titan-navy/55 leading-relaxed mb-6">
                    {{ __('We deliver more than buildings — we deliver peace of mind through an integrated approach handled with care and professionalism.') }}
                </p>
                <div class="space-y-3">
                    @foreach([
                        ['icon' => 'lucide-shield-check', 'title' => ['en' => 'Uncompromising Safety', 'kh' => 'សុវត្ថិភាពជាចម្បង'], 'desc' => ['en' => 'Zero-tolerance policy ensuring the safety of all stakeholders.', 'kh' => 'គោលការណ៍តឹងរ៉ឹងបំផុតដើម្បីធានាសុវត្ថិភាព។']],
                        ['icon' => 'lucide-clock',         'title' => ['en' => 'On-Time Delivery',     'kh' => 'ការប្រគល់ជូនទាន់ពេល'],   'desc' => ['en' => 'Rigorous scheduling and project management to meet every deadline.', 'kh' => 'ការរៀបចំកាលវិភាគម៉ត់ចត់ដើម្បីបំពេញពេលវេលា។']],
                        ['icon' => 'lucide-zap',           'title' => ['en' => 'Innovative Solutions', 'kh' => 'ដំណោះស្រាយច្នៃប្រឌិត'], 'desc' => ['en' => 'Modern technologies solving complex engineering challenges.', 'kh' => 'បច្ចេកវិទ្យាទំនើបដោះស្រាយបញ្ហាស្មុគស្មាញ។']],
                    ] as $item)
                    <div class="group flex items-start gap-3 p-4 rounded-lg border border-gray-100 hover:border-titan-red/20 hover:bg-gray-50/50 transition-all duration-200">
                        <div class="w-8 h-8 rounded bg-gray-50 border border-gray-100 flex items-center justify-center shrink-0 group-hover:bg-titan-red/5 group-hover:border-titan-red/20 transition-all">
                            <x-dynamic-component :component="$item['icon']" class="w-3.5 h-3.5 text-titan-red" stroke-width="1.5" />
                        </div>
                        <div>
                            <div class="services-title text-base font-black text-titan-navy uppercase tracking-tight mb-1 group-hover:text-titan-red transition-colors">{{ $item['title'][$lang] }}</div>
                            <p class="text-sm text-titan-navy/50 leading-relaxed">{{ $item['desc'][$lang] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3 h-[320px] md:h-[380px]">
                <div class="rounded-lg overflow-hidden relative translate-y-6">
                    <img src="/images/webp/projects/Thumbnail-3.webp" class="w-full h-full object-cover" loading="lazy" decoding="async" />
                </div>
                <div class="rounded-lg overflow-hidden relative">
                    <img src="/images/webp/projects/Thumbnail-5.webp" class="w-full h-full object-cover" loading="lazy" decoding="async" />
                </div>
            </div>
        </div>
    </section>

    <!-- SECTORS -->
    <section class="py-12 md:py-16 bg-gray-50 border-y border-gray-100">
        <div class="max-w-[1200px] mx-auto px-6">
            <div class="flex items-center gap-3 mb-8">
                <div class="w-5 h-[2px] bg-titan-red"></div>
                <div>
                    <p class="text-[9px] font-black uppercase tracking-[0.3em] text-titan-red">{{ __('Industries & Sectors') }}</p>
                    <h2 class="services-title text-2xl md:text-3xl font-black text-titan-navy uppercase tracking-tight">{{ __('Where We Operate') }}</h2>
                </div>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                @foreach($sectors as $i => $sector)
                <div class="group relative h-44 md:h-52 rounded-lg overflow-hidden cursor-pointer">
                    <img src="{{ $sector['image'] }}" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="{{ $sector['title'][$lang] }}" loading="lazy" decoding="async" />
                    <div class="absolute inset-0 bg-gradient-to-t from-titan-navy/80 via-titan-navy/30 to-transparent"></div>
                    <div class="absolute inset-0 p-4 flex flex-col justify-between">
                        <div class="w-7 h-7 rounded bg-white/10 backdrop-blur-sm border border-white/20 flex items-center justify-center">
                            <x-dynamic-component :component="$sector['icon']" class="w-3.5 h-3.5 text-white" />
                        </div>
                        <div>
                            <h3 class="services-title text-base font-black text-white uppercase tracking-tight leading-tight">{{ $sector['title'][$lang] }}</h3>
                            <div class="w-6 h-[2px] bg-titan-red mt-1.5 group-hover:w-10 transition-all duration-300"></div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="py-12 md:py-16 max-w-[1200px] mx-auto px-6">
        <div class="bg-titan-navy rounded-lg overflow-hidden grid grid-cols-1 md:grid-cols-2">
            <div class="p-8 md:p-10 flex flex-col justify-center">
                <p class="text-[9px] font-black uppercase tracking-[0.3em] text-titan-red mb-2">{{ __('Work With Us') }}</p>
                <h2 class="services-title text-2xl md:text-3xl font-black text-white uppercase tracking-tight leading-tight mb-3">
                    {{ __('Ready to Start Your Next Project?') }}
                </h2>
                <p class="text-white/40 text-base leading-relaxed mb-6">
                    {{ __('Join forces with Kimmex for engineering excellence and construction that defines the future.') }}
                </p>
                <div class="flex flex-wrap gap-2">
                    <a href="/contact"
                        class="inline-flex items-center gap-2 h-9 px-5 rounded bg-titan-red text-white text-[9px] font-black uppercase tracking-[0.2em] hover:bg-white hover:text-titan-navy transition-all">
                        {{ __('Get a Free Quote') }}<x-lucide-arrow-right class="w-3.5 h-3.5" />
                    </a>
                    <a href="/projects"
                        class="inline-flex items-center gap-2 h-9 px-5 rounded border border-white/20 text-white text-[9px] font-black uppercase tracking-[0.2em] hover:bg-white/10 transition-all">
                        {{ __('View Portfolio') }}
                    </a>
                </div>
            </div>
            <div class="hidden md:block relative min-h-[220px]">
                <img src="/images/webp/projects/Thumbnail-1.webp" class="absolute inset-0 w-full h-full object-cover opacity-40" loading="lazy" decoding="async" />
                <div class="absolute inset-0 bg-gradient-to-r from-titan-navy via-titan-navy/60 to-transparent"></div>
            </div>
        </div>
    </section>

</div>

</x-layouts.app>
