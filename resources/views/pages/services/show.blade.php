<x-layouts.app :title="'Service Details'" :description="'Detailed information about Kimmex construction services.'">

    @php
        /** @var string $slug */
        $lang = app()->getLocale() === 'km' ? 'kh' : app()->getLocale();
        $fallbackImages = [
            'design-and-build' => '/images/projects/Thumbnail-1.jpg',
            'construction' => '/images/projects/Thumbnail-1.jpg',
            'project-management' => '/images/projects/Thumbnail-3.jpg',
            'consultants' => '/images/projects/Thumbnail-4.jpg',
        ];

        // Try to load from DB first
        $service = \Illuminate\Support\Facades\Cache::remember(
            "service_show_data_{$slug}_{$lang}",
            now()->addHours(12),
            function () use ($slug, $fallbackImages) {
                $serviceDb = \App\Models\Service::where('slug', $slug)->where('isActive', true)->first();
                if ($serviceDb) {
                    return [
                        'id' => $serviceDb->slug,
                        'title' => [
                            'en' => $serviceDb->getTranslation('title', 'en'),
                            'kh' => $serviceDb->getTranslation('title', 'km'),
                        ],
                        'desc' => [
                            'en' => strip_tags($serviceDb->getTranslation('description', 'en')),
                            'kh' => strip_tags($serviceDb->getTranslation('description', 'km')),
                        ],
                        'image' =>
                            $serviceDb->image &&
                            \Illuminate\Support\Facades\Storage::disk('public')->exists($serviceDb->image)
                                ? \Illuminate\Support\Facades\Storage::url($serviceDb->image)
                                : ($fallbackImages[$slug] ?? null),
                        'scopeItems' => is_array($serviceDb->features)
                            ? array_map(
                                fn($f) => ['en' => $f['name'] ?? '', 'kh' => $f['name'] ?? ''],
                                $serviceDb->features,
                            )
                            : [],
                    ];
                }
                return null;
            },
        );

        if (!$service) {
            // Fallback service data
            $fallbackServices = [
                'design-and-build' => [
                    'id' => 'design-and-build',
                    'title' => ['en' => 'Design & Build', 'kh' => 'រចនា និងសាងសង់'],
                    'tagline' => ['en' => 'From Concept to Creation', 'kh' => 'ពីគំនិតដល់ការបង្កើត'],
                    'desc' => [
                        'en' =>
                            'Our flagship service combines architectural creativity with engineering precision. We manage the entire project lifecycle, ensuring seamless transition from blueprints to handover. This integrated approach minimizes risk and accelerates delivery.',
                        'kh' =>
                            'សេវាកម្មដ៏សំខាន់របស់យើងរួមបញ្ចូលភាពច្នៃប្រឌិតស្ថាបត្យកម្ម និងភាពជាក់លាក់នៃវិស្វកម្ម។ យើងគ្រប់គ្រងវដ្តជីវិតគម្រោងទាំងមូល ធានាបាននូវការផ្លាស់ប្តូរយ៉ាងរលូនពីការគូសប្លង់រហូតដល់ការប្រគល់ជូន។ វិធីសាស្រ្តរួមនេះកាត់បន្ថយហានិភ័យ និងជំរុញការចែកចាយឲ្យបានរហ័ស។',
                    ],
                    'idealFor' => [
                        'en' =>
                            'Real estate developers, commercial business owners, and private investors looking for single-point accountability.',
                        'kh' =>
                            'ស័ក្តិសមសម្រាប់អ្នកអភិវឌ្ឍន៍អចលនទ្រព្យ ម្ចាស់អាជីវកម្មពាណិជ្ជកម្ម និងអ្នកវិនិយោគឯកជនដែលកំពុងស្វែងរកការទទួលខុសត្រូវតែមួយ។',
                    ],
                    'image' => $fallbackImages['design-and-build'],
                    'icon' => 'lucide-pen-tool',
                    'scopeItems' => [
                        ['en' => 'Architectural & structural design', 'kh' => 'ការរចនាស្ថាបត្យកម្ម និងរចនាសម្ព័ន្ធ'],
                        ['en' => 'Civil & foundation works', 'kh' => 'ការងារស៊ីវិល និងគ្រឹះ'],
                        ['en' => 'MEP systems installation', 'kh' => 'ការដំឡើងប្រព័ន្ធ MEP'],
                        ['en' => 'Interior finishing & decoration', 'kh' => 'ការបញ្ចប់ខាងក្នុង និងការតុបតែង'],
                        ['en' => 'Exterior cladding & landscaping', 'kh' => 'ការបិទខាងក្រៅ និងការរៀបចំទេសភាព'],
                    ],
                ],
                'construction' => [
                    'id' => 'construction',
                    'title' => ['en' => 'Construction', 'kh' => 'សាងសង់'],
                    'tagline' => ['en' => 'Building the Future', 'kh' => 'កសាងអនាគត'],
                    'desc' => [
                        'en' =>
                            'Premium civil construction services across Cambodia specializing in robust concrete work, high-rise buildings, and commercial spaces.',
                        'kh' =>
                            'សេវាកម្មសំណង់ស៊ីវិលលំដាប់ខ្ពស់ប្រចាំប្រទេសកម្ពុជាដែលមានជំនាញលើការងារបេតុងដ៏រឹងមាំ អគារខ្ពស់ៗ និងអគារពាណិជ្ជកម្ម។',
                    ],
                    'idealFor' => [
                        'en' => 'Large-scale infrastructure and commercial building projects.',
                        'kh' => 'គម្រោងហេដ្ឋារចនាសម្ព័ន្ធខ្នាតធំ និងអគារពាណិជ្ជកម្ម។',
                    ],
                    'image' => $fallbackImages['construction'],
                    'icon' => 'lucide-hammer',
                    'scopeItems' => [
                        ['en' => 'High-Rise Buildings', 'kh' => 'អគារខ្ពស់កប់ពពក'],
                        ['en' => 'Commercial Spaces', 'kh' => 'អគារពាណិជ្ជកម្ម'],
                        ['en' => 'Quality Assurance', 'kh' => 'ការធានាគុណភាព'],
                    ],
                ],
                'project-management' => [
                    'id' => 'project-management',
                    'title' => ['en' => 'Project Management', 'kh' => 'ការគ្រប់គ្រងគម្រោង'],
                    'tagline' => ['en' => 'Delivering On Time', 'kh' => 'ផ្តល់ទាន់ពេលវេលា'],
                    'desc' => [
                        'en' =>
                            'Expert oversight and management of construction projects, ensuring on-time delivery, quality control, cost management, and safety compliance. Our experienced managers keep everything on track.',
                        'kh' =>
                            'ការត្រួតពិនិត្យ និងគ្រប់គ្រងគម្រោងសំណង់ ធានាការផ្តល់ទាន់ពេល ការត្រួតពិនិត្យគុណភាព ការគ្រប់គ្រងថ្លៃដើម និងការអនុលោមតាមសុវត្ថិភាព។',
                    ],
                    'idealFor' => [
                        'en' =>
                            'Large-scale developers and institutions requiring professional oversight across multiple construction phases.',
                        'kh' => 'អ្នកអភិវឌ្ឍន៍ធំ និងស្ថាប័នដែលត្រូវការការត្រួតពិនិត្យជំនាញ។',
                    ],
                    'image' => $fallbackImages['project-management'],
                    'icon' => 'lucide-briefcase',
                    'scopeItems' => [
                        [
                            'en' => 'Project scheduling & timeline management',
                            'kh' => 'កាលវិភាគគម្រោង និងការគ្រប់គ្រងពេលវេលា',
                        ],
                        ['en' => 'Budget tracking & cost control', 'kh' => 'ការតាមដានថវិកា និងការគ្រប់គ្រងចំណាយ'],
                        ['en' => 'Quality assurance & control', 'kh' => 'ការធានា និងត្រួតពិនិត្យគុណភាព'],
                    ],
                ],
                'consultants' => [
                    'id' => 'consultants',
                    'title' => ['en' => 'Consultants', 'kh' => 'ទីប្រឹក្សា'],
                    'tagline' => ['en' => 'Expert Guidance', 'kh' => 'ការណែនាំជំនាញ'],
                    'desc' => [
                        'en' =>
                            'Professional consulting services including project feasibility studies, design consulting, structural analysis, and expert advisory for complex engineering challenges.',
                        'kh' =>
                            'សេវាកម្មប្រឹក្សាវិជ្ជាជីវៈ រួមទាំងការសិក្សាលទ្ធភាពគម្រោង ការប្រឹក្សាការរចនា ការវិភាគរចនាសម្ព័ន្ធ និងការប្រឹក្សាជំនាញ។',
                    ],
                    'idealFor' => [
                        'en' =>
                            'Government agencies, NGOs, and private investors seeking independent technical reviews and feasibility assessments.',
                        'kh' => 'ទីភ្នាក់ងាររដ្ឋាភិបាល អង្គការក្រៅរដ្ឋាភិបាល និងអ្នកវិនិយោគឯកជន។',
                    ],
                    'image' => $fallbackImages['consultants'],
                    'icon' => 'lucide-lightbulb',
                    'scopeItems' => [
                        [
                            'en' => 'Feasibility studies & site assessment',
                            'kh' => 'ការសិក្សាលទ្ធភាព និងការវាយតម្លៃទីតាំង',
                        ],
                        ['en' => 'Design review & optimization', 'kh' => 'ការពិនិត្យការរចនា និងការបង្កើនប្រសិទ្ធភាព'],
                        ['en' => 'Structural & geotechnical analysis', 'kh' => 'ការវិភាគរចនាសម្ព័ន្ធ និងភូមិសាស្ត្រ'],
                    ],
                ],
            ];

            $service = $fallbackServices[$slug] ?? null;
            if (!$service) {
                abort(404);
            }
        }

        $roadmap = [
            [
                'step' => '01',
                'icon' => 'lucide-search',
                'title' => ['en' => 'Consultation', 'kh' => 'ការប្រឹក្សា'],
                'desc' => [
                    'en' => 'Understanding your vision, budget, and feasibility analysis.',
                    'kh' => 'ការយល់ដឹងពីចក្ខុវិស័យ ថវិកា និងការវិភាគសមិទ្ធភាព។',
                ],
            ],
            [
                'step' => '02',
                'icon' => 'lucide-pen-tool',
                'title' => ['en' => 'Design & Strategy', 'kh' => 'រចនា និងយុទ្ធសាស្រ្ត'],
                'desc' => [
                    'en' => 'Creating architectural blueprints and detailed strategy.',
                    'kh' => 'ការបង្កើតប្លង់ស្ថាបត្យកម្ម និងយុទ្ធសាស្រ្តលម្អិត។',
                ],
            ],
            [
                'step' => '03',
                'icon' => 'lucide-hammer',
                'title' => ['en' => 'Construction', 'kh' => 'សាងសង់'],
                'desc' => [
                    'en' => 'Quality-controlled construction execution on-site.',
                    'kh' => 'ការអនុវត្តសាងសង់ប្រកបដោយការគ្រប់គ្រងគុណភាព។',
                ],
            ],
            [
                'step' => '04',
                'icon' => 'lucide-check-circle-2',
                'title' => ['en' => 'Handover', 'kh' => 'ប្រគល់ជូន'],
                'desc' => [
                    'en' => 'Final inspection, documentation, and key handover.',
                    'kh' => 'ការត្រួតពិនិត្យចុងក្រោយ ឯកសារ និងការប្រគល់សោ។',
                ],
            ],
        ];

        $valueProp = [
            [
                'icon' => 'lucide-users',
                'title' => ['en' => 'Single Point of Contact', 'kh' => 'ចំណុចទំនាក់ទំនងតែមួយ'],
                'desc' => [
                    'en' => 'Streamlined communication and accountability.',
                    'kh' => 'ការប្រាស្រ័យទាក់ទង និងការទទួលខុសត្រូវមានប្រសិទ្ធភាព។',
                ],
            ],
            [
                'icon' => 'lucide-clock',
                'title' => ['en' => 'Faster Timeline', 'kh' => 'ពេលវេលាលឿនរហ័ស'],
                'desc' => [
                    'en' => 'Overlapping design and construction phases.',
                    'kh' => 'ការត្រួតគ្នានៃដំណាក់កាលរចនា និងការសាងសង់។',
                ],
            ],
            [
                'icon' => 'lucide-trending-up',
                'title' => ['en' => 'Cost Certainty', 'kh' => 'ភាពប្រាកដប្រជាថ្លៃដើម'],
                'desc' => [
                    'en' => 'Reduced change orders and accurate budgeting.',
                    'kh' => 'កាត់បន្ថយការផ្លាស់ប្តូរ និងរៀបចំថវិកាបានត្រឹមត្រូវ។',
                ],
            ],
            [
                'icon' => 'lucide-shield-check',
                'title' => ['en' => 'Quality Assurance', 'kh' => 'ធានាគុណភាព'],
                'desc' => [
                    'en' => 'Professional teams ensuring design-intent alignment.',
                    'kh' => 'ក្រុមការងារប្រកបដោយវិជ្ជាជីវៈធានាបាននូវការរចនាស្របតាមគោលដៅ។',
                ],
            ],
        ];

        $featuredProjects = [
            [
                'id' => '1',
                'title' => ['en' => 'Vatthanak Capital Expansion', 'kh' => 'ការពង្រីកបរិវេណ វឌ្ឍនៈ កាពីតាល'],
                'category' => ['en' => 'Commercial', 'kh' => 'ពាណិជ្ជកម្ម'],
                'location' => ['en' => 'Phnom Penh', 'kh' => 'ភ្នំពេញ'],
                'image' => '/images/projects/Thumbnail-1.jpg',
            ],
            [
                'id' => '2',
                'title' => ['en' => 'Skyline Residences', 'kh' => 'អគារលំនៅដ្ឋាន Skyline'],
                'category' => ['en' => 'Residential', 'kh' => 'លំនៅដ្ឋាន'],
                'location' => ['en' => 'Siem Reap', 'kh' => 'សៀមរាប'],
                'image' => '/images/projects/Thumbnail-2.jpg',
            ],
        ];
    @endphp

    <div class="bg-white min-h-screen text-titan-navy">

        <!-- === 1. PREMIUM HERO === -->
        <section class="relative overflow-hidden bg-titan-navy">
            <div class="absolute inset-0">
                @if ($service['image'])
                    <img src="{{ $service['image'] }}" alt="{{ $service['title'][$lang] }}"
                        class="w-full h-full object-cover opacity-90 scale-105 animate-slow-zoom" />
                @else
                    <div
                        class="w-full h-full bg-[radial-gradient(circle_at_30%_20%,var(--color-kmd-navy)_0%,var(--color-kmd-navy)_100%)]">
                        <div class="absolute inset-0 opacity-20" style="background-image: url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%23ffffff\" fill-opacity=\"0.1\"%3E%3Cpath d=\"M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2v-4h4v-2h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2v-4h4v-2H6z\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
                    </div>
                @endif
                
                <div class="absolute inset-0 bg-gradient-to-r from-titan-navy/96 via-titan-navy/82 to-titan-navy/30"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-titan-navy via-transparent to-titan-navy/60"></div>
            </div>

            <div class="relative z-10">
                <div class="max-w-[1400px] mx-auto px-6 py-14 pt-24 md:py-18 md:pt-28 lg:py-24 lg:pt-32">
                    <a href="/services"
                        class="inline-flex items-center gap-3 text-white/85 hover:text-white transition-all font-black uppercase tracking-[0.22em] text-[10px] mb-8 group">
                        <x-lucide-arrow-left class="w-3.5 h-3.5 transition-transform group-hover:-translate-x-1" />
                        {{ __('Back to Services') }}
                    </a>

                    <div class="grid grid-cols-1 lg:grid-cols-[1.1fr_0.9fr] gap-10 items-center">
                        <div class="max-w-2xl">
                            <div class="inline-flex items-center gap-3 border border-white/12 bg-white/[0.08] px-4 py-2 rounded mb-6">
                                <x-dynamic-component :component="$service['icon'] ?? 'lucide-building'" class="w-4 h-4 text-titan-red" />
                                <span class="text-[10px] font-black uppercase tracking-[0.24em] text-white/80">
                                    {{ __('Service Details') }}
                                </span>
                            </div>

                            <h1 class="font-black text-white uppercase tracking-normal leading-[0.9] mb-6"
                                style="font-size: clamp(2.35rem, 6vw, 4.5rem) !important;">
                                {{ $service['title'][$lang] }}
                            </h1>

                            <p class="text-white/75 text-base md:text-lg leading-relaxed max-w-xl font-medium">
                                {{ $service['desc'][$lang] }}
                            </p>

                            <div class="mt-8 flex flex-wrap gap-3">
                                <a href="#scope-of-work"
                                    class="h-11 px-5 rounded bg-titan-red text-white inline-flex items-center gap-2 text-[10px] font-black uppercase tracking-[0.16em] transition-all hover:bg-white hover:text-titan-navy">
                                    <x-lucide-list class="w-4 h-4" />
                                    {{ __('View Scope') }}
                                </a>
                                <a href="/contact"
                                    class="h-11 px-5 rounded border border-white/15 bg-white/5 text-white inline-flex items-center gap-2 text-[10px] font-black uppercase tracking-[0.16em] transition-all hover:bg-white hover:text-titan-navy">
                                    <x-lucide-phone class="w-4 h-4" />
                                    {{ __('Contact Us') }}
                                </a>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4">
                            <div class="relative rounded overflow-hidden border border-white/10 shadow-[0_25px_80px_rgba(0,0,0,0.28)] bg-titan-navy min-h-[260px] md:min-h-[340px]">
                                @if ($service['image'])
                                    <img src="{{ $service['image'] }}" alt="{{ $service['title'][$lang] }}"
                                        class="absolute inset-0 w-full h-full object-cover" loading="eager" />
                                    <div class="absolute inset-0 bg-gradient-to-t from-titan-navy/70 via-transparent to-transparent"></div>
                                @else
                                    <div class="absolute inset-0 bg-[radial-gradient(circle_at_30%_20%,rgba(227,30,36,0.15)_0%,transparent_50%)]"></div>
                                @endif
                                <div class="absolute left-5 bottom-5 right-5 flex items-end justify-between gap-4">
                                    <div>
                                        <div class="text-[9px] font-black uppercase tracking-[0.18em] text-white/60 mb-2">
                                            {{ __('Key Scope') }}
                                        </div>
                                        <div class="text-white font-black text-lg uppercase tracking-normal">
                                            {{ $service['title'][$lang] }}
                                        </div>
                                    </div>
                                    <div class="w-11 h-11 rounded bg-white/10 backdrop-blur flex items-center justify-center shrink-0">
                                        <x-lucide-arrow-up-right class="w-5 h-5 text-white" />
                                    </div>
                                </div>
                            </div>

                            @if(!empty($service['scopeItems']))
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                    @foreach(array_slice($service['scopeItems'], 0, 3) as $item)
                                        <div class="rounded border border-white/10 bg-white/[0.06] backdrop-blur px-4 py-3 text-white">
                                            <div class="text-[9px] font-black uppercase tracking-[0.16em] text-titan-red mb-1">
                                                {{ __('Included') }}
                                            </div>
                                            <div class="text-sm font-bold leading-tight text-white/85 line-clamp-2">
                                                {{ $item[$lang] }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- === 2. SERVICE OVERVIEW === -->
        <section class="py-24 px-6 max-w-[1400px] mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-20 items-center">
                <div x-data="{ shown: false }" x-intersect.once="shown = true"
                    :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                    class="transition-all duration-1000">
                    <div class="mb-10">
                        <span
                            class="text-titan-red font-bold uppercase tracking-widest text-xs mb-3 block">{{ __('Overview') }}</span>
                        <h2 class="text-2xl md:text-3xl font-bold text-titan-navy mb-5">
                            {{ $lang === 'kh' ? 'ការកំណត់ឡើងវិញនូវ' : 'Redefining' }} {{ $service['title'][$lang] }}
                        </h2>
                        <div
                            class="text-sm md:text-base text-titan-navy/90 leading-relaxed mb-8 prose prose-slate max-w-none">
                            {{ $service['desc'][$lang] }}
                        </div>
                    </div>

                    @if (!empty($service['idealFor'][$lang] ?? ''))
                        <div class="bg-gray-50 p-8 rounded border-l-4 border-titan-red shadow-sm">
                            <h3 class="text-xl font-bold text-titan-navy mb-3 flex items-center gap-3">
                                <div class="p-2 bg-titan-red/10 rounded-lg">
                                    <x-lucide-users class="w-5 h-5 text-titan-red" />
                                </div>
                                {{ $lang === 'kh' ? 'ស័ក្តិសមសម្រាប់' : 'Ideal For' }}
                            </h3>
                            <div class="text-titan-navy/90 leading-relaxed prose prose-sm prose-slate max-w-none">
                                {{ $service['idealFor'][$lang] }}
                            </div>
                        </div>
                    @endif
                </div>
                <div x-data="{ shown: false }" x-intersect.once="shown = true"
                    :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                    class="transition-all duration-1000 delay-200">
                    <div class="relative group">
                        <div
                            class="absolute -inset-4 bg-titan-red/5 rounded rotate-2 group-hover:rotate-1 transition-transform duration-500">
                        </div>
                        <div class="aspect-[4/3] rounded overflow-hidden shadow-2xl relative z-10 bg-titan-navy">
                            <img src="{{ $service['image'] }}" alt="{{ $service['title'][$lang] }}"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
                                loading="lazy" />
                            <div
                                class="absolute inset-0 bg-titan-navy/10 group-hover:bg-transparent transition-colors duration-500">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- === 3. SCOPE OF WORK === -->
        @if (!empty($service['scopeItems'] ?? []))
            <section id="scope-of-work" class="py-24 bg-[#111827] text-white relative overflow-hidden">
                <div
                    class="absolute top-0 right-0 w-[500px] h-[500px] bg-titan-red/10 rounded-full blur-[120px] pointer-events-none">
                </div>

                <div class="max-w-[1400px] mx-auto px-6 relative z-10">
                    <div x-data="{ shown: false }" x-intersect.once="shown = true"
                        :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                        class="text-center mb-16 transition-all duration-1000">
                        <span
                            class="text-titan-red font-bold uppercase tracking-widest text-xs mb-3 block">{{ $lang === 'kh' ? 'វិសាលភាពការងារ' : 'Scope of Work' }}</span>
                        <h2 class="text-2xl md:text-3xl font-bold mb-5 !text-white">
                            {{ $lang === 'kh' ? 'សេវាកម្មដ៏ទូលំទូលាយ' : 'Comprehensive Coverage' }}
                        </h2>
                    </div>

                    <div class="flex flex-wrap justify-center gap-6">
                        @foreach ($service['scopeItems'] as $i => $item)
                            <div x-data="{ shown: false }" x-intersect.once="shown = true"
                                style="transition-delay: {{ $i * 100 }}ms"
                                :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                                class="w-full sm:w-[calc(50%-1.5rem)] lg:w-[calc(33.333%-1rem)] group flex items-start gap-5 p-8 bg-white/[0.07] border border-white/15 rounded hover:bg-white/[0.11] hover:border-titan-red/40 transition-all duration-300 h-full">
                                <div
                                    class="w-10 h-10 rounded-full bg-titan-red/10 border border-titan-red/20 flex items-center justify-center shrink-0 group-hover:bg-titan-red/20 group-hover:border-titan-red/40 transition-colors duration-300">
                                    <x-lucide-check-circle-2 class="w-5 h-5 text-titan-red" />
                                </div>
                                <span
                                    class="font-bold text-lg leading-tight pt-2 group-hover:text-titan-red transition-colors">{{ $item[$lang] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        <!-- === 4. PROCESS / HOW WE DELIVER === -->
        <section class="py-24 px-6 bg-white">
            <div class="max-w-[1400px] mx-auto">
                <div x-data="{ shown: false }" x-intersect.once="shown = true"
                    :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                    class="text-center max-w-2xl mx-auto mb-16 transition-all duration-1000">
                    <span
                        class="text-titan-red font-bold uppercase tracking-widest text-xs mb-3 block">{{ $lang === 'kh' ? 'ដំណើរការរបស់យើង' : 'Our Process' }}</span>
                    <h2 class="text-2xl md:text-3xl font-bold text-titan-navy mb-4">
                        {{ $lang === 'kh' ? 'មាគ៌ាឆ្ពោះទៅរកភាពជោគជ័យ' : 'The Path to Success' }}
                    </h2>
                    <p class="text-gray-500 text-sm">
                        {{ $lang === 'kh' ? 'វិធីសាស្រ្តដែលមានរចនាសម្ព័ន្ធ និងតម្លាភាពដើម្បីធានាភាពជោគជ័យនៃគម្រោងរបស់អ្នក។' : 'A transparent, structured approach to ensure your project\'s success.' }}
                    </p>
                </div>

                <div class="relative mt-32">
                    <!-- Connecting Line -->
                    <div
                        class="hidden md:block absolute top-[55px] left-[10%] right-[10%] h-[1px] bg-titan-red/20 z-0">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-12 relative z-10">
                        @foreach ($roadmap as $i => $step)
                            <div x-data="{ shown: false }" x-intersect.once="shown = true"
                                style="transition-delay: {{ $i * 100 }}ms"
                                :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                                class="flex flex-col items-center text-center group transition-all duration-1000">

                                <div class="relative mb-16 flex justify-center">
                                    <!-- Large Background Number -->
                                    <div
                                        class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-[80px] font-black text-gray-50 group-hover:text-titan-red/[0.05] transition-colors duration-500 pointer-events-none z-0 tracking-tighter leading-none select-none">
                                        {{ $step['step'] }}
                                    </div>

                                    <!-- Glowing shadow effect on hover -->
                                    <div
                                        class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-32 h-32 bg-titan-red/20 rounded-full blur-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-500 z-0">
                                    </div>

                                    <!-- The Dark Diamond -->
                                    <div
                                        class="w-[110px] h-[110px] bg-titan-navy rounded flex items-center justify-center relative z-10 rotate-45 border-2 border-transparent group-hover:border-titan-red transition-all duration-500 shadow-[0_20px_40px_rgba(0,0,0,0.08)] group-hover:shadow-[0_0_40px_rgba(227,30,36,0.2)]">
                                        <!-- Un-rotate the icon inside -->
                                        <div class="-rotate-45 flex flex-col items-center">
                                            <x-dynamic-component :component="$step['icon']"
                                                class="w-8 h-8 text-white group-hover:text-titan-red transition-colors duration-300 stroke-[1.5]" />
                                        </div>
                                    </div>

                                    <!-- Step Number Badge (Orange box with white border) -->
                                    <div
                                        class="absolute -bottom-2 -right-4 w-11 h-11 bg-titan-red rounded flex items-center justify-center border-[4px] border-white z-20 transition-transform duration-500 group-hover:scale-110 shadow-sm">
                                        <span
                                            class="text-[13px] font-black text-white tracking-tight">{{ $step['step'] }}</span>
                                    </div>
                                </div>

                                <div class="px-2">
                                    <h3
                                        class="text-xl font-bold text-titan-navy mb-3 group-hover:text-titan-red transition-colors duration-300">
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
            <div x-data="{ shown: false }" x-intersect.once="shown = true"
                :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                class="text-center mb-16 transition-all duration-1000">
                <span
                    class="text-titan-red font-bold uppercase tracking-widest text-xs mb-3 block">{{ $lang === 'kh' ? 'ហេតុអ្វីជ្រើសរើសយើង' : 'Why Choose Us' }}</span>
                <h2 class="text-2xl md:text-3xl font-bold text-titan-navy">
                    {{ $lang === 'kh' ? 'គុណតម្លៃដែលផ្តល់ជូន' : 'Value Delivered' }}
                </h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                @foreach ($valueProp as $i => $benefit)
                    <div x-data="{ shown: false }" x-intersect.once="shown = true"
                        style="transition-delay: {{ $i * 100 }}ms"
                        :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                        class="bg-white p-8 rounded shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 border border-gray-100 group h-full">
                        <div
                            class="w-14 h-14 bg-gray-50 border border-gray-100 rounded-full flex items-center justify-center mb-6 group-hover:border-titan-red group-hover:bg-titan-red/5 transition-all duration-500">
                            <x-dynamic-component :component="$benefit['icon']"
                                class="w-6 h-6 text-titan-red" stroke-width="1.5" />
                        </div>
                        <h3
                            class="text-xl font-bold text-titan-navy mb-3 group-hover:text-titan-red transition-colors">
                            {{ $benefit['title'][$lang] }}
                        </h3>
                        <p class="text-titan-navy/90 leading-relaxed">
                            {{ $benefit['desc'][$lang] }}
                        </p>
                    </div>
                @endforeach
            </div>
        </section>

        <!-- === 6. FEATURED PROJECTS === -->
        <section class="py-24 bg-titan-navy text-white px-6">
            <div class="max-w-[1400px] mx-auto">
                <div x-data="{ shown: false }" x-intersect.once="shown = true"
                    :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                    class="flex flex-col md:flex-row justify-between items-end mb-16 border-b border-white/10 pb-8 transition-all duration-1000">
                    <div>
                        <span
                            class="text-titan-red font-bold uppercase tracking-widest text-xs mb-3 block">{{ $lang === 'kh' ? 'ស្នាដៃ' : 'Portfolio' }}</span>
                        <h2 class="text-2xl md:text-3xl font-bold">{{ __('Featured Projects') }}</h2>
                    </div>
                    <a href="/projects"
                        class="mt-8 md:mt-0 px-8 py-3 bg-white/10 hover:bg-white hover:text-titan-navy transition-all font-bold uppercase tracking-widest text-sm flex items-center gap-2 rounded-lg backdrop-blur-sm">
                        {{ $lang === 'kh' ? 'មើលគម្រោងទាំងអស់' : 'View All Projects' }} <x-lucide-arrow-right
                            class="w-4 h-4" />
                    </a>
                </div>

                <div class="flex flex-wrap justify-center gap-10">
                    @foreach ($featuredProjects as $i => $project)
                        <div x-data="{ shown: false }" x-intersect.once="shown = true"
                            style="transition-delay: {{ $i * 100 }}ms"
                            :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                            class="w-full md:w-[calc(50%-1.25rem)] transition-all duration-1000">
                            <a href="/projects/{{ $project['id'] }}"
                                class="group relative aspect-[16/9] overflow-hidden rounded cursor-pointer block shadow-2xl h-full">
                                <img src="{{ $project['image'] }}" alt="{{ $project['title'][$lang] }}"
                                    class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
                                    loading="lazy" />
                                <div
                                    class="absolute inset-0 bg-gradient-to-t from-titan-navy via-titan-navy/40 to-transparent opacity-95 group-hover:opacity-80 transition-opacity">
                                </div>

                                <div class="absolute bottom-0 left-0 p-8 w-full">
                                    <div
                                        class="transform translate-y-4 group-hover:translate-y-0 transition-transform duration-500">
                                        <span
                                            class="inline-block bg-titan-red text-white text-xs font-bold uppercase tracking-widest px-3 py-1 rounded mb-3">{{ $project['category'][$lang] }}</span>
                                        <h3 class="text-2xl md:text-3xl font-bold text-white mb-2">
                                            {{ $project['title'][$lang] }}
                                        </h3>
                                        <div class="flex items-center gap-2 text-white/100 text-sm">
                                            <x-lucide-map-pin class="w-4 h-4 text-titan-red" />
                                            {{ $project['location'][$lang] }}
                                        </div>
                                    </div>
                                </div>

                                <div
                                    class="absolute top-6 right-6 w-12 h-12 bg-white/20 backdrop-blur-md rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transform translate-x-4 group-hover:translate-x-0 transition-all duration-300">
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
            <div
                class="max-w-3xl mx-auto bg-titan-red rounded p-12 md:p-16 shadow-2xl shadow-titan-red/30 relative overflow-hidden">
                <div
                    class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full blur-[50px] translate-x-1/2 -translate-y-1/2 pointer-events-none">
                </div>
                <div
                    class="absolute bottom-0 left-0 w-64 h-64 bg-black/10 rounded-full blur-[50px] -translate-x-1/2 translate-y-1/2 pointer-events-none">
                </div>

                <div x-data="{ shown: false }" x-intersect.once="shown = true"
                    :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                    class="transition-all duration-1000 relative z-10">
                    <h2 class="text-2xl md:text-3xl font-bold text-white mb-4">
                        {{ $lang === 'kh' ? 'រួចរាល់សម្រាប់ការចាប់ផ្តើម?' : 'Ready to start?' }}
                    </h2>
                    <p class="text-white/100 text-base mb-8 font-medium">
                        {{ $lang === 'kh' ? 'ទាក់ទងក្រុមការងារជំនាញរបស់យើងថ្ងៃនេះ សម្រាប់ការពិគ្រោះយោបល់ និងការសិក្សាសមិទ្ធភាពដោយឥតគិតថ្លៃ។' : 'Contact our expert team today for a free consultation and feasibility study.' }}
                    </p>
                    <a href="/contact"
                        class="inline-flex items-center gap-2 bg-white text-titan-red px-10 py-5 font-bold uppercase tracking-widest hover:bg-titan-navy hover:text-white transition-all shadow-xl rounded-lg group">
                        {{ $lang === 'kh' ? 'ស្នើសុំការប្រឹក្សា' : 'Request Quote' }} <x-lucide-arrow-right
                            class="w-4 h-4 group-hover:translate-x-1 transition-transform" />
                    </a>
                </div>
            </div>
        </section>
    </div>

</x-layouts.app>
