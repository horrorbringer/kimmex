<x-layouts.app title="About Us"
    description="Learn about Kimmex's history, mission, vision, and core values in construction.">

    @php
        $brandProfile = \App\Models\SystemSetting::get('brand_identity', []);
        $locale = app()->getLocale();
        $localeKey = $locale === 'kh' ? 'km' : $locale;
        $brand = $brandProfile[$localeKey] ?? ($brandProfile['en'] ?? []);
        $ceoName = $brandProfile['ceo_name'] ?? 'Okhna. TOUCH KIM';
        $aboutHeroImage = $brandProfile['about_hero_image'] ?? null;
        $aboutHeroImageUrl = '/images/webp/hero/hero-1.webp';

        if (filled($aboutHeroImage)) {
            $aboutHeroImageUrl = \App\Support\PublicStorage::urlIfExists($aboutHeroImage, $aboutHeroImageUrl);
        }

        $resolveAboutImage = function (?string $image, string $fallback): string {
            if (! filled($image)) {
                return $fallback;
            }

            return \App\Support\PublicStorage::urlIfExists($image, $fallback);
        };

        $aboutSectionImageDefaults = [
            '/images/webp/projects/Thumbnail-1.webp',
            '/images/webp/projects/Thumbnail-3.webp',
            '/images/webp/projects/Thumbnail-2.webp',
            '/images/webp/projects/Thumbnail-4.webp',
        ];

        $aboutSectionImages = array_map(
            fn (string $fallback, int $index): string => $resolveAboutImage($brandProfile['about_section_images'][$index] ?? null, $fallback),
            $aboutSectionImageDefaults,
            array_keys($aboutSectionImageDefaults)
        );

        $aboutData = [
            'story' => $brand['company_story'] ?? __('Since our humble beginnings, KIM MEX Construction has grown into a premier partner...'),
            'values' => array_map(function ($v) {
                $icon = $v['icon'] ?? 'lucide-shield';
                // If the icon was accidentally translated or contains invalid characters, fallback to a default
                if (!preg_match('/^[a-zA-Z0-9\-]+$/', $icon)) {
                    $icon = 'lucide-shield';
                }

                $image = $v['image'] ?? null;
                $image = \App\Support\PublicStorage::urlIfExists($image);

                return [
                    'title' => $v['title'] ?? '',
                    'content' => $v['description'] ?? '',
                    'icon' => $icon,
                    'image' => $image,
                ];
            }, $brand['values_list'] ?? [])
        ];

        // Fallback values if branding values list is empty
        if (empty($aboutData['values'])) {
            $aboutData['values'] = [
                ['title' => __('Safety First'), 'content' => __('We maintain a strict zero-incident policy on all construction sites.'), 'icon' => 'lucide-heart', 'image' => null],
                ['title' => __('Quality Excellence'), 'content' => __('Utilizing premium materials and rigorous QA workflows.'), 'icon' => 'lucide-award', 'image' => null],
                ['title' => __('Integrity'), 'content' => __('Honest and transparent communication with all our clients.'), 'icon' => 'lucide-shield', 'image' => null],
                ['title' => __('Innovation'), 'content' => __('Leveraging the latest in 3D modeling and MEP system architecture.'), 'icon' => 'lucide-lightbulb', 'image' => null]
            ];
        }

        $milestones = \Illuminate\Support\Facades\Cache::remember('about_milestones_data_'.app()->getLocale(), now()->addHours(12), function() {
            $milestonesDb = \App\Models\Milestone::where('isActive', true)->orderBy('sortOrder')->get();

            return $milestonesDb->values()->map(function (\App\Models\Milestone $m, int $index) {
                $detail = $m->getTranslation('detailed_description', app()->getLocale());
                $hasDetail = filled(trim(strip_tags((string) $detail)));
                $fallbackImage = '/images/webp/projects/Thumbnail-'.(($index % 6) + 1).'.webp';

                return [
                    'year' => $m->year,
                    'title' => $m->getTranslation('title', app()->getLocale()),
                    'desc' => $m->getTranslation('description', app()->getLocale()),
                    'detail' => $hasDetail ? $detail : '',
                    'has_detail' => $hasDetail,
                    'image' => \App\Support\PublicStorage::urlIfExists($m->image, $fallbackImage),
                ];
            })->toArray();
        });

        // Fallback to hardcoded if DB is empty
        if (empty($milestones)) {
            $milestones = [
                [
                    'year' => '1999',
                    'title' => __('Company Founded'),
                    'desc' => __('Started as a small dedicated engineering firm.'),
                    'detail' => '',
                    'has_detail' => false,
                    'image' => '/images/webp/projects/Thumbnail-1.webp'
                ],
                [
                    'year' => '2010',
                    'title' => __('First Mega Project'),
                    'desc' => __('Secured our first major government infrastructure contract.'),
                    'detail' => '',
                    'has_detail' => false,
                    'image' => '/images/webp/projects/Thumbnail-2.webp'
                ],
                [
                    'year' => '2026',
                    'title' => __('Industry Leaders'),
                    'desc' => __('Recognized as the top infrastructure firm in the Kingdom of Cambodia.'),
                    'detail' => '',
                    'has_detail' => false,
                    'image' => '/images/webp/projects/Thumbnail-3.webp'
                ]
            ];
        }

        $orgChart = \Illuminate\Support\Facades\Cache::remember('about_orgchart_'.app()->getLocale(), now()->addHours(12), function() {
            $unitsByParent = \App\Models\OrgUnit::where('isActive', true)
                ->with(['employee', 'department'])
                ->orderBy('orderIndex')
                ->get()
                ->groupBy(fn (\App\Models\OrgUnit $unit): string => (string) ($unit->parentId ?? '__root__'));

            $buildNode = function ($unit) use (&$buildNode, $unitsByParent) {
                // Determine name based on Employee or local Title
                $name = $unit->employee?->name ?? $unit->getTranslation('title', app()->getLocale());

                // Determine role based on Employee or local Department
                $role = $unit->employee?->role ?? $unit->getTranslation('title', app()->getLocale());

                // Specific Type Mapping for Styling
                $rawType = strtoupper($unit->type);
                $type = match ($rawType) {
                    'STAFF' => 'staff',
                    'DEPARTMENT' => 'department',
                    'OFFICE' => 'office',
                    default => 'staff',
                };

                // Override "ceo" or "director" types based on role content 
                // to maintain hierarchy visual styles from CSS components
                $lowRole = strtolower($role);
                if (str_contains($lowRole, 'ceo') || str_contains($lowRole, 'chief')) {
                    $type = 'ceo';
                } elseif (str_contains($lowRole, 'director') || str_contains($lowRole, 'manager')) {
                    $type = 'director';
                }

                $employeeImage = $unit->employee?->image;
                $employeeImage = \App\Support\PublicStorage::urlIfExists($employeeImage);

                return [
                    'name' => $name,
                    'role' => $role,
                    'type' => $type,
                    'image' => $employeeImage,
                    'phone' => $unit->employee?->phone,
                    'bio' => $unit->employee?->bio,
                    'children' => $unitsByParent->get((string) $unit->id, collect())
                        ->map(fn($child) => $buildNode($child))
                        ->toArray()
                ];
            };

            $roots = $unitsByParent->get('__root__', collect());

            if ($roots->isEmpty()) {
                // Fallback to placeholder if nothing in DB
                return [
                    'name' => 'Sok Visal',
                    'role' => __('CEO (Not Configured)'),
                    'type' => 'ceo',
                    'image' => null,
                    'bio' => __('To show your team here, please: 1. Add an Employee record. 2. Create an Org Unit mapping for that employee in the admin panel.'),
                    'children' => []
                ];
            }

            // If there's only one root (standard), render it directly
            if ($roots->count() === 1) {
                return $buildNode($roots->first());
            }

            // If there are multiple roots (e.g., Board of Directors), 
            // wrap them in a virtual company node to maintain tree structure
            $profile = \App\Models\SystemSetting::get('organization_profile', []);
            $locale = app()->getLocale();
            $localeKey = $locale === 'kh' ? 'km' : $locale; // Normalize to 'km' if using 'kh'

            $companyName = $profile[$localeKey]['company_name'] ?? 'Kimmex Group';

            return [
                'name' => $companyName,
                'role' => __('Organization Structure'),
                'type' => 'office',
                'children' => $roots->map(fn($root) => $buildNode($root))->toArray()
            ];
        });
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
                class="relative bg-white rounded overflow-hidden max-w-4xl w-full shadow-[0_30px_60px_-15px_rgba(0,0,0,0.5)] flex flex-col md:flex-row max-h-[90vh] md:min-h-[500px] overflow-y-auto md:overflow-visible z-10">

                <button @click="selectedMember = null"
                    class="absolute top-4 right-4 md:top-6 md:right-6 z-30 w-10 h-10 bg-white/90 backdrop-blur-sm shadow-xl text-titan-navy hover:bg-titan-red hover:text-white rounded-full transition-all duration-300 flex items-center justify-center group">
                    <x-lucide-x class="w-5 h-5 transition-transform group-hover:rotate-90" />
                </button>

                <!-- Left Image -->
                <div
                    class="w-full md:w-1/2 relative h-[300px] sm:h-[400px] md:h-auto shrink-0 overflow-hidden bg-gray-100 flex items-center justify-center">
                    <template x-if="selectedMember?.image">
                        <img :src="selectedMember.image" class="object-cover object-top w-full h-full" decoding="async" loading="lazy" />
                    </template>
                    <template x-if="!selectedMember?.image">
                        <x-lucide-users class="w-24 h-24 text-gray-300" />
                    </template>
                    <div class="absolute inset-0 bg-gradient-to-t from-titan-navy/60 via-transparent to-transparent">
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


        <!-- === PREMIUM ABOUT HERO === -->
        <section class="relative h-[60vh] md:h-[75vh] min-h-[500px] md:min-h-[600px] flex items-center justify-center overflow-hidden bg-titan-navy">
            {{-- Background Zoom Animation --}}
            <div class="absolute inset-0">
                <img src="{{ $aboutHeroImageUrl }}" alt="Construction Excellence" class="w-full h-full object-cover opacity-100 animate-slow-zoom" loading="eager" decoding="async" fetchpriority="high" />
                {{-- Lightened multi-stage gradient --}}
                <div class="absolute inset-0 bg-gradient-to-b from-titan-navy/40 via-transparent to-titan-navy/70"></div>
            </div>

            <div class="relative z-20 text-center max-w-5xl px-4 md:px-6" x-data="{ shown: false }" x-init="setTimeout(() => shown = true, 100)">

                <h1 :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'" 
                    class="transition-all duration-1000 delay-300 font-heading font-[900] text-white mb-6 md:mb-8 leading-[0.9] tracking-tighter uppercase"
                    style="font-size: clamp(1.75rem, 5vw, 3.5rem) !important; color: white !important; font-weight: 900 !important;">
                    {{ __('BUILDING') }}<br />
                    <span class="text-titan-red">{{ __('CAMBODIA FUTURE') }}</span>
                </h1>

                <div :class="shown ? 'opacity-100' : 'opacity-0'" class="transition-all duration-1000 delay-500 flex items-center justify-center gap-4 md:gap-6">
                    <div class="h-[1px] w-8 md:w-12 bg-white/30 hidden sm:block"></div>
                    <p class="text-xs md:text-base text-white/90 font-bold uppercase tracking-[0.3em] md:tracking-[0.4em]">
                        {{ __('Precision. Integrity. Excellence.') }}
                    </p>
                    <div class="h-[1px] w-8 md:w-12 bg-white/30 hidden sm:block"></div>
                </div>
            </div>

        </section>

        <!-- STATS BAR -->
        <section class="bg-titan-navy py-12 md:py-16 border-t border-white/10">
            <div class="max-w-[1400px] mx-auto px-4 md:px-6">
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
                            <div class="text-4xl md:text-5xl font-heading font-black text-white mb-2">
                                <span x-text="count">0</span>{{ $stat['suffix'] }}
                            </div>
                            <div class="text-sm uppercase tracking-widest text-white/60 font-bold">{{ $stat['label'] }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- WHO WE ARE SECTION (Synced with Image) -->
        <section class="py-16 sm:py-20 md:py-32 px-4 sm:px-6 bg-white overflow-hidden">
            <div class="max-w-[1400px] mx-auto grid lg:grid-cols-2 gap-10 sm:gap-12 md:gap-20 items-center">

                <!-- Left: Aesthetic Image Grid with Badge -->
                <div class="relative w-full flex justify-center lg:block overflow-hidden" x-data="{ shown: false }" x-intersect.once="shown = true">
                    <div :class="shown ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-12'"
                        class="grid grid-cols-2 gap-2.5 sm:gap-6 transition-all duration-1000 relative w-full max-w-[420px] sm:max-w-[560px] lg:max-w-full mx-auto">
                            <div class="space-y-3 sm:space-y-6">
                                <div x-data="{ loaded: false }" class="relative aspect-[5/4] md:aspect-[4/5] rounded overflow-hidden bg-slate-100 shadow-lg md:shadow-2xl">
                                <div x-cloak x-show="!loaded" class="absolute inset-0 animate-pulse bg-gradient-to-br from-slate-100 via-slate-200 to-slate-100"></div>
                                <img src="{{ $aboutSectionImages[0] }}"
                                    x-on:load="loaded = true" x-on:error="loaded = true"
                                    :class="loaded ? 'opacity-100' : 'opacity-0'"
                                    class="relative z-10 object-cover w-full h-full hover:scale-105 transition-all duration-700" loading="lazy" decoding="async" />
                            </div>
                            <div x-data="{ loaded: false }" class="relative aspect-[5/4] md:aspect-square rounded overflow-hidden bg-slate-100 shadow-lg md:shadow-2xl">
                                <div x-cloak x-show="!loaded" class="absolute inset-0 animate-pulse bg-gradient-to-br from-slate-100 via-slate-200 to-slate-100"></div>
                                <img src="{{ $aboutSectionImages[1] }}"
                                    x-on:load="loaded = true" x-on:error="loaded = true"
                                    :class="loaded ? 'opacity-100' : 'opacity-0'"
                                    class="relative z-10 object-cover w-full h-full hover:scale-105 transition-all duration-700" loading="lazy" decoding="async" />
                            </div>
                        </div>
                        <div class="space-y-3 sm:space-y-6 pt-4 md:pt-12">
                            <div x-data="{ loaded: false }" class="relative aspect-[5/4] md:aspect-square rounded overflow-hidden bg-slate-100 shadow-lg md:shadow-2xl">
                                <div x-cloak x-show="!loaded" class="absolute inset-0 animate-pulse bg-gradient-to-br from-slate-100 via-slate-200 to-slate-100"></div>
                                <img src="{{ $aboutSectionImages[2] }}"
                                    x-on:load="loaded = true" x-on:error="loaded = true"
                                    :class="loaded ? 'opacity-100' : 'opacity-0'"
                                    class="relative z-10 object-cover w-full h-full hover:scale-105 transition-all duration-700" loading="lazy" decoding="async" />
                            </div>
                            <div x-data="{ loaded: false }" class="relative aspect-[5/4] md:aspect-[4/5] rounded overflow-hidden bg-slate-100 shadow-lg md:shadow-2xl">
                                <div x-cloak x-show="!loaded" class="absolute inset-0 animate-pulse bg-gradient-to-br from-slate-100 via-slate-200 to-slate-100"></div>
                                <img src="{{ $aboutSectionImages[3] }}"
                                    x-on:load="loaded = true" x-on:error="loaded = true"
                                    :class="loaded ? 'opacity-100' : 'opacity-0'"
                                    class="relative z-10 object-cover w-full h-full hover:scale-105 transition-all duration-700" loading="lazy" decoding="async" />

                                <!-- Floating 25+ Years Badge -->
                                <div
                                    class="absolute bottom-3 right-3 md:-bottom-6 md:-right-6 bg-titan-red text-white p-3 sm:p-4 md:p-8 rounded shadow-[0_20px_40px_rgba(227,30,36,0.3)] z-20 flex flex-col items-center justify-center w-[72px] sm:w-[96px] md:w-auto md:min-w-[140px] transform hover:scale-105 transition-transform">
                                    <span class="text-xl sm:text-2xl md:text-4xl font-black leading-none">25+</span>
                                    <span
                                        class="text-[8px] md:text-[10px] font-black uppercase tracking-[0.16em] md:tracking-[0.2em] mt-1">{{ __('Years') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Text Content -->
                <div x-data="{ shown: false }" x-intersect.once="shown = true"
                    :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                    class="text-center lg:text-left transition-all duration-1000 delay-200">
                    <span class="text-titan-red font-bold uppercase tracking-widest text-sm mb-4 block">
                        {{ __('WHO WE ARE') }}
                    </span>
                    <h2 class="text-3xl sm:text-4xl md:text-5xl font-bold text-gray-900 leading-tight mb-5 sm:mb-6">
                        @php
                            $profile = \App\Models\SystemSetting::get('organization_profile', []);
                            $locale = app()->getLocale();
                            $localeKey = $locale === 'kh' ? 'km' : $locale;
                            $tagline = $profile[$localeKey]['tagline'] ?? "Cambodia's Premier Construction Partner";
                        @endphp
                        {{ $tagline }}
                    </h2>
                    
                    <p class="text-center lg:text-left text-gray-500 text-base sm:text-lg leading-relaxed mb-8 sm:mb-12 whitespace-pre-line">
                        {{ $brand['company_story'] ?? __("With over 25 years of experience, we have established ourselves as Cambodia's most trusted construction partner, delivering projects that stand the test of time and elevate communities.") }}
                    </p>

                    <!-- Mission/Vision/Goal Interactive List -->
                    <div class="space-y-4 text-left sm:space-y-6" x-data="{ active: 'vision' }">
                        @php
                            $mvg_items = [
                                [
                                    'id' => 'vision',
                                    'icon' => 'eye',
                                    'title' => __('Our Vision'),
                                    'desc' => $brand['vision'] ?? __('To be the most trusted and innovative construction partner in Cambodia.')
                                ],
                                [
                                    'id' => 'mission',
                                    'icon' => 'flag',
                                    'title' => __('Our Mission'),
                                    'desc' => $brand['mission'] ?? __('To bridge the gap between concept and reality through exceptional engineering and safety.')
                                ],
                                [
                                    'id' => 'goal',
                                    'icon' => 'target',
                                    'title' => __('Our Strategy'),
                                    'desc' => $brand['goal'] ?? __('To maintain long-term leadership in the Cambodian market through talent development and CMS investment.')
                                ],
                            ];
                        @endphp

                        @foreach($mvg_items as $item)
                        <div class="group cursor-pointer border-b border-gray-100 last:border-b-0 pb-4 sm:pb-6"
                             @click="active = (active === '{{ $item['id'] }}' ? null : '{{ $item['id'] }}')">
                            <div class="flex gap-3 sm:gap-6 items-start">
                                <!-- Icon Box -->
                                <div class="w-12 h-12 sm:w-14 sm:h-14 rounded flex items-center justify-center shrink-0 transition-colors duration-500"
                                     :class="active === '{{ $item['id'] }}' ? 'bg-titan-red text-white' : 'bg-titan-red/10 text-titan-red'">
                                    @if($item['icon'] === 'eye')
                                        <x-lucide-eye class="w-6 h-6" stroke-width="2" />
                                    @elseif($item['icon'] === 'flag')
                                        <x-lucide-flag class="w-6 h-6" stroke-width="2" />
                                    @elseif($item['icon'] === 'target')
                                        <x-lucide-target class="w-6 h-6" stroke-width="2" />
                                    @endif
                                </div>

                                <!-- Text Content -->
                                <div class="min-w-0 flex-grow">
                                    <div class="flex items-center justify-between gap-3 mb-2">
                                        <h3 class="min-w-0 text-lg sm:text-xl font-bold text-gray-900 transition-colors duration-500"
                                            :class="active === '{{ $item['id'] }}' ? 'text-titan-red' : ''">
                                            {{ $item['title'] }}
                                        </h3>
                                        <div class="w-8 h-8 shrink-0 rounded-full border flex items-center justify-center transition-all duration-500"
                                             :class="active === '{{ $item['id'] }}' ? 'border-titan-red text-titan-red rotate-180' : 'border-gray-200 text-gray-400'">
                                            <x-lucide-chevron-down class="w-4 h-4" />
                                        </div>
                                    </div>
                                    
                                    <div x-show="active === '{{ $item['id'] }}'" x-collapse>
                                        <p class="text-gray-500 text-sm sm:text-base leading-relaxed whitespace-pre-line pb-4">
                                            {{ $item['desc'] }}
                                        </p>
                                    </div>
                                    
                                    <p x-show="active !== '{{ $item['id'] }}'" class="w-full text-gray-400 text-sm italic truncate">
                                        {{ \Illuminate\Support\Str::limit($item['desc'], 80) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <!-- CEO MESSAGE -->
        <section class="py-20 px-6 bg-white border-t border-gray-100">
            <div class="max-w-[1000px] mx-auto">
                <div x-data="{ shown: false }" x-intersect.once="shown = true"
                    :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                    class="transition-all duration-1000 bg-gray-50 rounded p-8 md:p-12 lg:p-16 border border-gray-100 shadow-sm relative overflow-hidden">
                    <!-- Decorative blurred background element -->
                    <div
                        class="absolute -top-24 -right-24 w-64 h-64 bg-titan-navy/5 rounded-full blur-3xl pointer-events-none">
                    </div>

                    <div class="flex flex-col md:flex-row gap-10 md:gap-16 items-center">

                        <!-- Left: Image -->
                        <div class="w-[200px] md:w-[280px] shrink-0 relative">
                            <div
                                class="aspect-[3/4] w-full rounded overflow-hidden shadow-lg border-4 border-white">
                                <img src="/images/team-leadership-professional/touch_kim.jpg" alt="Okhna. TOUCH KIM"
                                    class="object-cover object-top w-full h-full bg-titan-navy/5" loading="lazy" decoding="async" />
                            </div>
                            <div
                                class="absolute -bottom-4 -right-4 w-16 h-16 bg-titan-red text-white flex items-center justify-center rounded shadow-lg border-4 border-gray-50 rotate-3 hover:rotate-0 transition-transform cursor-default">
                                <x-lucide-quote class="w-6 h-6" stroke-width="2" />
                            </div>
                        </div>

                        <!-- Right: Message -->
                        <div class="relative z-10 flex-grow">
                            <span class="text-titan-red font-bold uppercase tracking-widest text-xs mb-3 block">{{
    __('Message From CEO') }}</span>

                            <div class="prose prose-titan max-w-none text-titan-navy mb-8">
                                {!! $brand['ceo_message'] ?? __('Construction is not just about concrete and steel. It is about building trust, fostering communities, and leaving a legacy that stands the test of time.') !!}
                            </div>
                            <div class="border-t border-gray-100 pt-6">
                                <h4 class="text-xl font-heading font-black text-titan-navy uppercase tracking-tighter mb-1">{{ $ceoName }}</h4>
                                <div class="text-xs font-bold text-titan-navy/50 uppercase tracking-widest">{{ __('Founder & Chief Executive Officer') }}</div>
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
                    <h2 class="text-3xl md:text-4xl font-heading font-black text-titan-navy">{{ __('Our Core Values') }}
                    </h2>
                </div>

                <div class="space-y-8">
                    @foreach($aboutData['values'] as $i => $value)
                        @continue(empty($value['image']))

                        <div x-data="{ shown: false }" x-intersect.once="shown = true"
                            x-bind:class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                            class="transition-all duration-700" style="transition-delay: {{ $i * 100 }}ms">
                            <img src="{{ $value['image'] }}" alt=""
                                class="block w-full max-w-none h-auto rounded" decoding="async" loading="lazy">
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- MILESTONES (Our Journey) -->
        <section class="py-32 px-6 bg-white overflow-hidden relative border-t border-gray-100">
            <div class="max-w-[1400px] mx-auto">
                <div class="text-center mb-24">
                    <span
                        class="text-titan-red font-black uppercase tracking-[0.3em] text-xs mb-4 block">{{ __('OUR JOURNEY') }}</span>
                    <h2 class="text-3xl md:text-5xl font-heading font-black text-titan-navy uppercase tracking-tight">
                        {{ __('Company Milestones') }}
                    </h2>
                </div>

                <div class="space-y-24 relative">
                    <!-- Vertical Timeline Line -->
                    <div class="absolute left-[30px] md:left-1/2 top-0 bottom-0 w-[2px] bg-gradient-to-b from-gray-100 via-titan-red/30 to-gray-100 hidden md:block -translate-x-1/2 z-0"></div>

                    @foreach($milestones as $idx => $milestone)
                        @php
                            $hasMilestoneDetail = (bool) ($milestone['has_detail'] ?? false);
                            $milestoneCursorClass = $hasMilestoneDetail ? 'cursor-pointer' : 'cursor-default';
                            $milestoneAriaDisabled = $hasMilestoneDetail ? 'false' : 'true';
                            $exploreDetailsClass = $hasMilestoneDetail ? '' : 'hidden';
                        @endphp
                        <div x-data="{ shown: false, open: false, hasDetail: @js($hasMilestoneDetail) }" x-intersect.once="shown = true"
                            :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'"
                            class="relative flex flex-col {{ $idx % 2 === 0 ? 'md:flex-row' : 'md:flex-row-reverse' }} items-center gap-12 md:gap-24 transition-all duration-1000">

                            <div
                                class="w-full md:w-5/12 flex flex-col {{ $idx % 2 === 0 ? 'md:items-end md:text-right' : 'md:items-start md:text-left' }} pl-16 md:pl-0 z-10 relative">
                                <div
                                    class="inline-block bg-titan-red text-white text-sm font-bold px-5 py-2 rounded-full mb-4 shadow-md tracking-wider">
                                    {{ $milestone['year'] }}
                                </div>
                                <h3
                                    class="text-2xl md:text-3xl font-heading font-black text-titan-navy mb-4 tracking-tight">
                                    {{ $milestone['title'] }}
                                </h3>
                                
                                <div class="w-full text-titan-navy/60 leading-relaxed 
                                    [&>p]:mb-4 
                                    [&>ul]:space-y-2 [&>ul]:mt-4 [&>ul]:inline-block [&>ul]:w-full [&>ul]:list-none 
                                    [&>ol]:space-y-2 [&>ol]:mt-4 [&>ol]:inline-block [&>ol]:w-full [&>ol]:list-none 
                                    {{ $idx % 2 === 0 ? '[&>ul]:md:text-right [&>ol]:md:text-right' : '[&>ul]:md:text-left [&>ol]:md:text-left' }}
                                    [&_li]:flex [&_li]:items-start [&_li]:gap-2 [&_li]:text-[15px] [&_li]:font-bold [&_li]:text-titan-navy/80 [&_li]:hover:text-titan-red [&_li]:transition-colors
                                    {{ $idx % 2 === 0 ? '[&_li]:md:flex-row-reverse [&_li]:md:text-right' : '[&_li]:md:flex-row [&_li]:md:text-left' }}
                                    ">
                                    {!! str_replace(['<li>', '</li>'], ['<li><div class="w-2 h-2 bg-titan-red rounded-full mt-1.5 shrink-0 block"></div><span>', '</span></li>'], $milestone['desc']) !!}
                                </div>
                                
                                <div x-show="open && hasDetail" x-collapse>
                                    <div
                                        class="mt-6 p-6 bg-gray-50/80 rounded border border-gray-100 text-titan-navy/60 italic leading-relaxed text-sm max-w-xl {{ $idx % 2 === 0 ? 'md:ml-auto' : 'md:mr-auto' }}">
                                        {!! $milestone['detail'] !!}
                                    </div>
                                </div>
                            </div>

                            <!-- Timeline Dot -->
                            <div class="hidden md:block absolute left-1/2 top-[50px] w-[18px] h-[18px] bg-white border-4 border-titan-red rounded-full -translate-x-1/2 shadow-lg z-20 transition-all duration-300"
                                :class="open ? 'scale-[1.3] bg-titan-red' : ''">
                            </div>

                            <!-- Image Side -->
                            <div class="w-full md:w-5/12 pl-0 z-10">
                                <a class="block aspect-video rounded overflow-hidden shadow-lg border border-gray-100 relative group {{ $milestoneCursorClass }}"
                                    @click.prevent="if (hasDetail) open = !open"
                                    aria-disabled="{{ $milestoneAriaDisabled }}">
                                    <img src="{{ $milestone['image'] }}"
                                        class="object-cover w-full h-full transition-transform duration-700 group-hover:scale-105"
                                        :class="open ? 'scale-105' : ''" loading="lazy" decoding="async" />
                                    <div class="absolute inset-0 bg-titan-navy/0 group-hover:bg-titan-navy/10 transition-colors duration-300"></div>
                                    <div
                                        class="{{ $exploreDetailsClass }} absolute bottom-4 left-4 right-4 translate-y-4 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition-all duration-300 pointer-events-none">
                                        <span class="bg-white/95 backdrop-blur-sm text-titan-navy px-3 py-1.5 text-xs font-bold rounded-full shadow-sm">
                                            {{ __('Explore Details') }}
                                        </span>
                                    </div>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- ORG CHART SECTON -->
        @php
            $orgProfile = \App\Models\SystemSetting::get('organization_profile', []);
            $orgChartType = $orgProfile['org_chart_type'] ?? 'dynamic';
            $orgChartImage = $orgProfile['org_chart_image'] ?? null;
            $orgChartPdf = $orgProfile['org_chart_pdf'] ?? null;

            if ($orgChartImage && !\Illuminate\Support\Str::startsWith($orgChartImage, ['http://', 'https://', '/'])) {
                $orgChartImage = \App\Support\PublicStorage::urlIfExists($orgChartImage);
            }
            if ($orgChartPdf && !\Illuminate\Support\Str::startsWith($orgChartPdf, ['http://', 'https://', '/'])) {
                $orgChartPdf = \App\Support\PublicStorage::urlIfExists($orgChartPdf);
            }
        @endphp

        <section id="leadership" class="py-32 px-6 bg-gray-50 overflow-hidden relative border-b border-gray-100">
            <div class="max-w-[1700px] mx-auto relative z-10 pb-12">
                @if($orgChartType === 'dynamic')
                <div class="text-center mb-24">
                    <span
                        class="text-titan-red font-black uppercase tracking-[0.3em] text-xs mb-4 block">{{ __('GOVERNANCE') }}</span>
                    <h2 class="text-3xl md:text-5xl font-heading font-black text-titan-navy uppercase tracking-tight">
                        {{ __('KIM MEX ORGANIZATION STRUCTURE') }}
                    </h2>
                </div>
                @endif

                @if($orgChartType === 'image' && $orgChartImage)
                    {{-- IMAGE MODE --}}
                    <div class="flex justify-center" x-data="{ shown: false }" x-intersect.once="shown = true">
                        <div :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                             class="transition-all duration-1000 w-full max-w-6xl mx-auto">
                            <img src="{{ $orgChartImage }}"
                                 alt="{{ __('Organization Structure') }}"
                                 class="w-full h-auto rounded-lg shadow-xl border border-gray-200" loading="lazy" decoding="async" />
                        </div>
                    </div>

                @elseif($orgChartType === 'pdf' && $orgChartPdf)
                    {{-- PDF MODE --}}
                    <div class="flex flex-col items-center gap-6" x-data="{ shown: false }" x-intersect.once="shown = true">
                        <div :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                             class="transition-all duration-1000 w-full max-w-5xl mx-auto">
                            <div class="rounded-lg shadow-xl border border-gray-200 overflow-hidden bg-white">
                                <iframe src="{{ $orgChartPdf }}"
                                        class="w-full border-0"
                                        style="height: 80vh; min-height: 600px;"
                                        title="{{ __('Organization Structure') }}">
                                </iframe>
                            </div>
                            <div class="text-center mt-6">
                                <a href="{{ $orgChartPdf }}" target="_blank" download
                                   class="inline-flex items-center gap-2 bg-titan-navy text-white px-6 py-3 rounded-lg font-bold text-sm uppercase tracking-wider hover:bg-titan-red transition-colors duration-300 shadow-md">
                                    <x-lucide-download class="w-4 h-4" />
                                    {{ __('Download Organization Chart') }}
                                </a>
                            </div>
                        </div>
                    </div>

                @else
                    {{-- DYNAMIC INTERACTIVE MODE (default) --}}
                    {{-- Mobile: full-width vertical accordion; Desktop: horizontal tree --}}
                    <div class="w-full md:min-w-[800px] md:flex md:justify-center md:overflow-x-auto px-2 md:px-0">
                        <x-about.org-node :node="$orgChart" :level="0" :small="true" />
                    </div>
                @endif
            </div>
        </section>


        <!-- QUALITY & SAFETY -->
        <section id="safety" class="py-16 md:py-24 px-4 md:px-6 bg-slate-50 border-y border-titan-navy/10">
            <div class="max-w-[1400px] mx-auto">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 md:gap-16 items-center">
                    <div x-data="{ shown: false }" x-intersect.once="shown = true"
                        :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                        class="transition-all duration-1000">
                        <span class="text-titan-red font-bold uppercase tracking-widest text-xs md:text-sm mb-3 md:mb-4 block">{{ __('Our
                            Standards') }}</span>
                        <h2 class="text-2xl md:text-4xl font-heading font-black text-titan-navy mb-4 md:mb-6 leading-tight tracking-normal">
                            {{ __('Quality & Safety') }} <span class="text-titan-red uppercase">{{ __('First') }}</span>
                        </h2>
                        <p class="text-titan-navy/65 text-base md:text-lg leading-relaxed mb-8 md:mb-10">
                            {{ __('We adhere to the highest international standards in construction quality and
                            workplace safety. Every project undergoes rigorous QA/QC protocols to ensure excellence from
                            foundation to finishing.') }}
                        </p>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 md:gap-6">
                            @php
                                $qualityItems = [
                                    [
                                        'icon' => 'shield',
                                        'title' => 'ISO 9001:2015',
                                        'desc' => __('Quality Management
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            Certified')
                                    ],
                                    ['icon' => 'award', 'title' => __('Zero Accidents'), 'desc' => __('Safety record policy')],
                                    [
                                        'icon' => 'check-circle-2',
                                        'title' => __('100% Compliance'),
                                        'desc' => __('Building code
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            adherence')
                                    ],
                                    ['icon' => 'clock', 'title' => __('On-Time Delivery'), 'desc' => __('98% completion rate')],
                                ];
                            @endphp
                            @foreach($qualityItems as $item)
                                <div class="flex items-start gap-3 md:gap-4 p-4 bg-white rounded-lg border border-titan-navy/10 shadow-sm">
                                    <div
                                        class="w-10 h-10 md:w-12 md:h-12 bg-titan-red/10 rounded-lg flex items-center justify-center text-titan-red shrink-0">
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
                                        <div class="text-titan-navy font-bold leading-tight">{{ $item['title'] }}</div>
                                        <div class="text-titan-navy/50 text-sm leading-snug mt-1">{{ $item['desc'] }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div x-data="{ shown: false }" x-intersect.once="shown = true"
                        :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                        class="transition-all duration-1000 delay-200 relative">
                        <img src="/images/webp/projects/Thumbnail-6.webp" alt="Safety Inspection"
                            class="rounded-lg shadow-[0_20px_60px_rgba(11,43,92,0.16)] w-full aspect-[4/3] md:aspect-auto object-cover" loading="lazy" decoding="async" />
                        <!-- Floating ISO Card -->
                        <div class="absolute -bottom-6 -left-6 bg-white p-6 rounded shadow-xl hidden md:block">
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
        <section class="py-24 bg-titan-navy border-t border-white/10">
            <div class="max-w-4xl mx-auto px-6 text-center">
                <h2 class="text-4xl md:text-5xl font-heading font-black mb-8 leading-tight tracking-normal" style="color: white !important;">
                    {{ __('READY TO BUILD YOUR') }} <br />
                    <span class="text-titan-red">{{ __('NEXT LANDMARK?') }}</span>
                </h2>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-6">
                    <a href="/contact"
                        class="bg-white text-titan-navy px-8 py-4 font-bold uppercase tracking-widest text-sm border-2 border-white hover:!bg-titan-red hover:!border-titan-red hover:!text-white hover:-translate-y-0.5 hover:shadow-xl transition-all rounded-lg">
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
