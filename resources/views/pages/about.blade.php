<x-layouts.app title="About Us"
    description="Learn about Kimmex's history, mission, vision, and core values in construction.">

    @push('head')
    <script type="application/ld+json">
    {!! json_encode(['@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => [
        ['@type' => 'ListItem', 'position' => 1, 'name' => __('Home'), 'item' => url('/')],
        ['@type' => 'ListItem', 'position' => 2, 'name' => __('About Us'), 'item' => url('/about')],
    ]], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
    @endpush

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

        if (empty($milestones)) {
            $milestones = [
                ['year' => '1999', 'title' => __('Company Founded'), 'desc' => __('Started as a small dedicated engineering firm.'), 'detail' => '', 'has_detail' => false, 'image' => '/images/webp/projects/Thumbnail-1.webp'],
                ['year' => '2010', 'title' => __('First Mega Project'), 'desc' => __('Secured our first major government infrastructure contract.'), 'detail' => '', 'has_detail' => false, 'image' => '/images/webp/projects/Thumbnail-2.webp'],
                ['year' => '2026', 'title' => __('Industry Leaders'), 'desc' => __('Recognized as the top infrastructure firm in the Kingdom of Cambodia.'), 'detail' => '', 'has_detail' => false, 'image' => '/images/webp/projects/Thumbnail-3.webp']
            ];
        }


        $orgChart = \Illuminate\Support\Facades\Cache::remember('about_orgchart_'.app()->getLocale(), now()->addHours(12), function() {
            $unitsByParent = \App\Models\OrgUnit::where('isActive', true)
                ->with(['employee', 'department'])
                ->orderBy('orderIndex')
                ->get()
                ->groupBy(fn (\App\Models\OrgUnit $unit): string => (string) ($unit->parentId ?? '__root__'));

            $buildNode = function ($unit) use (&$buildNode, $unitsByParent) {
                $name = $unit->employee?->name ?? $unit->getTranslation('title', app()->getLocale());
                $role = $unit->employee?->role ?? $unit->getTranslation('title', app()->getLocale());
                $rawType = strtoupper($unit->type);
                $type = match ($rawType) {
                    'STAFF' => 'staff',
                    'DEPARTMENT' => 'department',
                    'OFFICE' => 'office',
                    default => 'staff',
                };
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
                return [
                    'name' => 'Sok Visal', 'role' => __('CEO (Not Configured)'), 'type' => 'ceo',
                    'image' => null, 'bio' => __('To show your team here, please add Employee and Org Unit records in the admin panel.'), 'children' => []
                ];
            }
            if ($roots->count() === 1) {
                return $buildNode($roots->first());
            }
            $profile = \App\Models\SystemSetting::get('organization_profile', []);
            $companyName = $profile[$localeKey]['company_name'] ?? 'Kimmex Group';
            return [
                'name' => $companyName, 'role' => __('Organization Structure'), 'type' => 'office',
                'children' => $roots->map(fn($root) => $buildNode($root))->toArray()
            ];
        });
    @endphp


    <div x-data="{ selectedMember: null }" class="bg-white text-titan-navy">

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
        <section class="relative h-[420px] md:h-[500px] flex items-end overflow-hidden bg-titan-navy">
            <div class="absolute inset-0">
                <img src="{{ $aboutHeroImageUrl }}" alt="{{ __('About Kimmex') }}" class="w-full h-full object-cover" loading="eager" decoding="async" fetchpriority="high" />
                <div class="absolute inset-0 bg-gradient-to-t from-titan-navy/90 via-titan-navy/40 to-titan-navy/20"></div>
                <div class="absolute inset-0 bg-gradient-to-r from-titan-navy/60 via-transparent to-transparent"></div>
            </div>

            <div class="relative z-20 w-full max-w-[1200px] mx-auto px-6 pb-14 md:pb-20">
                {{-- Breadcrumb --}}
                <nav class="flex items-center gap-2 text-xs text-white/60 mb-6" aria-label="Breadcrumb">
                    <a href="/" class="hover:text-white transition-colors">{{ __('Home') }}</a>
                    <x-lucide-chevron-right class="w-3 h-3 text-white/30" />
                    <span class="text-white font-semibold">{{ __('About Us') }}</span>
                </nav>

                <h1 class="font-heading font-[900] text-white leading-[1] tracking-tight uppercase mb-5"
                    style="font-size: clamp(2rem, 5vw, 3.5rem);">
                    {{ __('BUILDING') }}
                    <span class="text-titan-red">{{ __('CAMBODIA\'S FUTURE') }}</span>
                </h1>

                <p class="text-white/70 text-base md:text-lg max-w-xl leading-relaxed">
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
                            class="flex flex-col items-center justify-center py-8 md:py-10 px-4 text-center group hover:bg-gray-50/50 transition-colors first:rounded-l-xl last:rounded-r-xl">
                            <div class="w-10 h-10 rounded-lg bg-titan-red/10 flex items-center justify-center mb-3 group-hover:bg-titan-red/20 transition-colors">
                                @if($stat['icon'] === 'calendar')
                                    <x-lucide-calendar class="w-5 h-5 text-titan-red" />
                                @elseif($stat['icon'] === 'building-2')
                                    <x-lucide-building-2 class="w-5 h-5 text-titan-red" />
                                @elseif($stat['icon'] === 'users')
                                    <x-lucide-users class="w-5 h-5 text-titan-red" />
                                @elseif($stat['icon'] === 'heart')
                                    <x-lucide-heart class="w-5 h-5 text-titan-red" />
                                @endif
                            </div>
                            <div class="text-3xl md:text-4xl font-black text-titan-navy mb-1 tabular-nums">
                                <span x-text="count">0</span><span class="text-titan-red">{{ $stat['suffix'] }}</span>
                            </div>
                            <div class="text-xs uppercase tracking-wider text-titan-navy/50 font-bold">{{ $stat['label'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>


        <!-- === WHO WE ARE === -->
        <section id="profile" class="py-20 md:py-28 px-6 bg-white overflow-hidden">
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
                <div x-data="{ shown: false }" x-intersect.once="shown = true"
                    :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                    class="transition-all duration-1000 delay-200">

                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-[2px] bg-titan-red"></div>
                        <span class="text-titan-red font-bold uppercase tracking-[0.2em] text-xs">{{ __('WHO WE ARE') }}</span>
                    </div>

                    @php
                        $profile = \App\Models\SystemSetting::get('organization_profile', []);
                        $tagline = $profile[$localeKey]['tagline'] ?? "Cambodia's Premier Construction Partner";
                    @endphp
                    <h2 class="text-3xl md:text-4xl font-heading font-black text-titan-navy leading-tight mb-6 tracking-tight">
                        {{ $tagline }}
                    </h2>

                    <p class="text-gray-500 text-base md:text-lg leading-relaxed mb-10 whitespace-pre-line">{{ $brand['company_story'] ?? __("With over 25 years of experience, we have established ourselves as Cambodia's most trusted construction partner, delivering projects that stand the test of time and elevate communities.") }}</p>


                    <!-- Vision / Mission / Strategy Accordion -->
                    <div class="space-y-4" x-data="{ active: 'vision' }">
                        @php
                            $mvg_items = [
                                ['id' => 'vision', 'icon' => 'eye', 'title' => __('Our Vision'), 'desc' => $brand['vision'] ?? __('To be the most trusted and innovative construction partner in Cambodia.')],
                                ['id' => 'mission', 'icon' => 'flag', 'title' => __('Our Mission'), 'desc' => $brand['mission'] ?? __('To bridge the gap between concept and reality through exceptional engineering and safety.')],
                                ['id' => 'goal', 'icon' => 'target', 'title' => __('Our Strategy'), 'desc' => $brand['goal'] ?? __('To maintain long-term leadership in the Cambodian market through talent development and CMS investment.')],
                            ];
                        @endphp

                        @foreach($mvg_items as $item)
                        <div class="border border-gray-100 rounded-xl overflow-hidden transition-all duration-300 hover:border-gray-200"
                             :class="active === '{{ $item['id'] }}' ? 'shadow-sm bg-white' : 'bg-gray-50/50'"
                             @click="active = (active === '{{ $item['id'] }}' ? null : '{{ $item['id'] }}')">
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
                            <div x-show="active === '{{ $item['id'] }}'" x-collapse>
                                <div class="px-5 pb-5 pl-[4.5rem]">
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
        <section class="relative py-20 md:py-28 overflow-hidden" style="background: linear-gradient(135deg, #071A33 0%, #0B2B5C 100%);">
            <div class="max-w-[1200px] mx-auto px-6 relative z-10">
                <div x-data="{ shown: false }" x-intersect.once="shown = true"
                    :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                    class="transition-all duration-1000">

                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-0 items-center">

                        <!-- Left: Photo & Name -->
                        <div class="lg:col-span-4 flex flex-col items-center lg:items-start">
                            <div class="relative w-[240px] md:w-[280px]">
                                {{-- Photo --}}
                                <div class="aspect-[3/4] rounded-2xl overflow-hidden shadow-[0_30px_80px_-20px_rgba(0,0,0,0.5)] ring-1 ring-white/10">
                                    <img src="/images/team-leadership-professional/touch_kim.jpg" alt="{{ $ceoName }}"
                                        class="object-cover object-top w-full h-full" loading="lazy" decoding="async" />
                                </div>
                                {{-- Decorative quote badge --}}
                                <div class="absolute -bottom-3 -right-3 w-12 h-12 rounded-xl flex items-center justify-center shadow-lg"
                                     style="background: var(--primary-color, #E31E24); color: #fff;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21c3 0 7-1 7-8V5c0-1.25-.756-2.017-2-2H4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2 1 0 1 0 1 1v1c0 1-1 2-2 2s-1 .008-1 1.031V20c0 1 0 1 1 1z"/><path d="M15 21c3 0 7-1 7-8V5c0-1.25-.756-2.017-2-2h-4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2h.75c0 2.25.25 4-2.75 4v3c0 1 0 1 1 1z"/></svg>
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
                            <div class="mb-6" style="color: rgba(255,255,255,0.1);">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="currentColor"><path d="M3 21c3 0 7-1 7-8V5c0-1.25-.756-2.017-2-2H4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2 1 0 1 0 1 1v1c0 1-1 2-2 2s-1 .008-1 1.031V20c0 1 0 1 1 1z"/><path d="M15 21c3 0 7-1 7-8V5c0-1.25-.756-2.017-2-2h-4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2h.75c0 2.25.25 4-2.75 4v3c0 1 0 1 1 1z"/></svg>
                            </div>

                            {{-- Message body --}}
                            <div class="ceo-message-content space-y-5 mb-10"
                                 style="color: rgba(255,255,255,0.75); font-size: 1.05rem; line-height: 1.9;">
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
                font-size: 1.2rem;
                font-weight: 500;
                color: rgba(255,255,255,0.9);
                line-height: 1.75;
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
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($aboutData['values'] as $i => $value)
                            @continue(empty($value['image']))
                            <div x-data="{ shown: false }" x-intersect.once="shown = true"
                                :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                                class="transition-all duration-700 rounded-xl overflow-hidden shadow-sm border border-gray-100 hover:shadow-lg hover:border-gray-200 group"
                                style="transition-delay: {{ $i * 100 }}ms">
                                <div class="aspect-[16/9] overflow-hidden">
                                    <img src="{{ $value['image'] }}" alt="{{ $value['title'] }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" decoding="async" loading="lazy" />
                                </div>
                                @if(!empty($value['title']))
                                <div class="p-5 md:p-6">
                                    <h3 class="font-bold text-titan-navy text-lg mb-2">{{ $value['title'] }}</h3>
                                    @if(!empty($value['content']))
                                        <p class="text-gray-500 text-sm leading-relaxed">{{ $value['content'] }}</p>
                                    @endif
                                </div>
                                @endif
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


        <!-- === MILESTONES === -->
        <section class="py-20 md:py-28 px-6 bg-gray-50 border-y border-gray-100 overflow-hidden">
            <div class="max-w-[1200px] mx-auto">
                <div class="text-center mb-16 md:mb-24">
                    <div class="flex items-center justify-center gap-3 mb-5">
                        <div class="w-8 h-[2px] bg-titan-red"></div>
                        <span class="text-titan-red font-bold uppercase tracking-[0.2em] text-xs">{{ __('OUR JOURNEY') }}</span>
                        <div class="w-8 h-[2px] bg-titan-red"></div>
                    </div>
                    <h2 class="text-3xl md:text-4xl font-heading font-black text-titan-navy tracking-tight">{{ __('Company Milestones') }}</h2>
                </div>

                <div class="space-y-16 md:space-y-0 relative">
                    <!-- Timeline Line (desktop) -->
                    <div class="hidden md:block absolute left-1/2 top-0 bottom-0 w-px bg-gradient-to-b from-transparent via-titan-red/20 to-transparent -translate-x-1/2"></div>

                    @foreach($milestones as $idx => $milestone)
                        @php
                            $isEven = $idx % 2 === 0;
                            $hasMilestoneDetail = (bool) ($milestone['has_detail'] ?? false);
                        @endphp
                        <div x-data="{ shown: false, open: false, hasDetail: @js($hasMilestoneDetail) }" x-intersect.once="shown = true"
                            :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10'"
                            class="relative md:grid md:grid-cols-2 md:gap-16 md:py-12 transition-all duration-700 group/milestone">

                            <!-- Timeline Dot (desktop) -->
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
                                <h3 class="text-xl md:text-2xl font-heading font-black text-titan-navy mb-3 tracking-tight">
                                    {{ $milestone['title'] }}
                                </h3>
                                <div class="text-gray-500 leading-relaxed text-sm md:text-base
                                    [&>p]:mb-3 [&>ul]:space-y-1.5 [&>ol]:space-y-1.5
                                    [&_li]:flex [&_li]:items-start [&_li]:gap-2 [&_li]:text-sm">
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
            </div>
        </section>


        <!-- === ORG CHART === -->
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

        <section id="leadership" class="py-20 md:py-28 px-6 bg-white overflow-hidden">
            <div class="max-w-[1700px] mx-auto">
                @if($orgChartType === 'dynamic')
                <div class="text-center mb-16 md:mb-24">
                    <div class="flex items-center justify-center gap-3 mb-5">
                        <div class="w-8 h-[2px] bg-titan-red"></div>
                        <span class="text-titan-red font-bold uppercase tracking-[0.2em] text-xs">{{ __('GOVERNANCE') }}</span>
                        <div class="w-8 h-[2px] bg-titan-red"></div>
                    </div>
                    <h2 class="text-3xl md:text-4xl font-heading font-black text-titan-navy tracking-tight">
                        {{ __('Organization Structure') }}
                    </h2>
                </div>
                @endif

                @if($orgChartType === 'image' && $orgChartImage)
                    <div class="flex justify-center" x-data="{ shown: false }" x-intersect.once="shown = true">
                        <div :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                             class="transition-all duration-1000 w-full max-w-6xl mx-auto">
                            <img src="{{ $orgChartImage }}" alt="{{ __('Organization Structure') }}"
                                 class="w-full h-auto rounded-xl shadow-xl border border-gray-200" loading="lazy" decoding="async" />
                        </div>
                    </div>
                @elseif($orgChartType === 'pdf' && $orgChartPdf)
                    <div class="flex flex-col items-center gap-6" x-data="{ shown: false }" x-intersect.once="shown = true">
                        <div :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                             class="transition-all duration-1000 w-full max-w-5xl mx-auto">
                            <div class="rounded-xl shadow-xl border border-gray-200 overflow-hidden bg-white">
                                <iframe src="{{ $orgChartPdf }}" class="w-full border-0" style="height: 80vh; min-height: 600px;" title="{{ __('Organization Structure') }}"></iframe>
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
                    <div class="w-full md:min-w-[800px] md:flex md:justify-center md:overflow-x-auto px-2 md:px-0">
                        <x-about.org-node :node="$orgChart" :level="0" :small="true" />
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
                                    ['icon' => 'shield', 'title' => 'ISO 9001:2015', 'desc' => __('Quality Management Certified')],
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
                                class="w-full aspect-[4/3] object-cover" loading="lazy" decoding="async" />
                        </div>
                        <!-- Floating ISO badge -->
                        <div class="absolute -bottom-5 -left-5 bg-white p-5 rounded-xl shadow-xl border border-gray-100 hidden md:flex items-center gap-4">
                            <div class="w-12 h-12 bg-green-50 rounded-full flex items-center justify-center">
                                <x-lucide-check-circle-2 class="text-green-600 w-6 h-6" />
                            </div>
                            <div>
                                <div class="text-xl font-black text-titan-navy">ISO</div>
                                <div class="text-xs text-gray-400 font-medium">{{ __('9001:2015 Certified') }}</div>
                            </div>
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

            <div class="relative z-10 max-w-[1200px] mx-auto px-6">
                <div class="grid grid-cols-1 lg:grid-cols-5 gap-10 lg:gap-16 items-center">
                    {{-- Left content --}}
                    <div class="lg:col-span-3">
                        <h2 class="text-3xl md:text-4xl lg:text-5xl font-heading font-black leading-[1.1] tracking-tight mb-6" style="color: #FFFFFF !important;">
                            {{ __('Let\'s Build Something') }}<br>
                            <span style="color: var(--primary-color, #E31E24) !important;">{{ __('Extraordinary Together') }}</span>
                        </h2>
                        <p class="text-base md:text-lg leading-relaxed max-w-lg" style="color: rgba(255,255,255,0.65);">
                            {{ __('Whether it\'s a government infrastructure project or a commercial development, our team is ready to deliver excellence.') }}
                        </p>
                    </div>

                    {{-- Right actions --}}
                    <div class="lg:col-span-2 flex flex-col gap-4">
                        <a href="/contact"
                            class="group flex items-center justify-between px-8 py-5 rounded-xl font-bold uppercase tracking-wider text-sm transition-all duration-300 shadow-lg hover:shadow-xl"
                            style="background-color: var(--primary-color, #E31E24); color: #FFFFFF;">
                            <div class="flex items-center gap-3">
                                <x-lucide-phone class="w-5 h-5" />
                                <span>{{ __('Contact Us') }}</span>
                            </div>
                            <x-lucide-arrow-right class="w-5 h-5 group-hover:translate-x-1 transition-transform" />
                        </a>
                        <a href="/projects"
                            class="group flex items-center justify-between px-8 py-5 rounded-xl font-bold uppercase tracking-wider text-sm transition-all duration-300"
                            style="border: 2px solid rgba(255,255,255,0.2); color: #FFFFFF;">
                            <div class="flex items-center gap-3">
                                <x-lucide-folder-open class="w-5 h-5" />
                                <span>{{ __('View Our Projects') }}</span>
                            </div>
                            <x-lucide-arrow-right class="w-5 h-5 opacity-0 group-hover:opacity-100 group-hover:translate-x-1 transition-all" />
                        </a>
                        <a href="/services"
                            class="group flex items-center justify-between px-8 py-5 rounded-xl font-bold uppercase tracking-wider text-sm transition-all duration-300"
                            style="border: 2px solid rgba(255,255,255,0.1); color: rgba(255,255,255,0.7);">
                            <div class="flex items-center gap-3">
                                <x-lucide-settings class="w-5 h-5" />
                                <span>{{ __('Explore Services') }}</span>
                            </div>
                            <x-lucide-arrow-right class="w-5 h-5 opacity-0 group-hover:opacity-100 group-hover:translate-x-1 transition-all" />
                        </a>
                    </div>
                </div>
            </div>
        </section>

    </div>
</x-layouts.app>
