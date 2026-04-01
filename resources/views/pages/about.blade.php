<x-layouts.app title="About Us"
    description="Learn about Kimmex's history, mission, vision, and core values in construction.">

    @php
        $brandProfile = \App\Models\SystemSetting::get('brand_identity', []);
        $locale = app()->getLocale();
        $localeKey = $locale === 'kh' ? 'km' : $locale;
        $brand = $brandProfile[$localeKey] ?? ($brandProfile['en'] ?? []);
        $ceoName = $brandProfile['ceo_name'] ?? 'Okhna. TOUCH KIM';

        $aboutData = [
            'story' => $brand['company_story'] ?? __('Since our humble beginnings, KIM MEX Construction has grown into a premier partner...'),
            'values' => array_map(function ($v) {
                return [
                    'title' => $v['title'] ?? '',
                    'content' => $v['description'] ?? '',
                    'icon' => $v['icon'] ?? 'lucide-shield'
                ];
            }, $brand['values_list'] ?? [])
        ];

        // Fallback values if branding values list is empty
        if (empty($aboutData['values'])) {
            $aboutData['values'] = [
                ['title' => __('Safety First'), 'content' => __('We maintain a strict zero-incident policy on all construction sites.'), 'icon' => 'lucide-heart'],
                ['title' => __('Quality Excellence'), 'content' => __('Utilizing premium materials and rigorous QA workflows.'), 'icon' => 'lucide-award'],
                ['title' => __('Integrity'), 'content' => __('Honest and transparent communication with all our clients.'), 'icon' => 'lucide-shield'],
                ['title' => __('Innovation'), 'content' => __('Leveraging the latest in 3D modeling and MEP system architecture.'), 'icon' => 'lucide-lightbulb']
            ];
        }

        $milestones = \App\Models\Milestone::orderBy('sortOrder')->get()->map(function (\App\Models\Milestone $m) {
            return [
                'year' => $m->year,
                'title' => $m->getTranslation('title', app()->getLocale()),
                'desc' => $m->getTranslation('description', app()->getLocale()),
                'image' => $m->image ? \Illuminate\Support\Facades\Storage::url($m->image) : '/images/projects/Thumbnail-1.jpg',
            ];
        })->toArray();

        // Fallback to hardcoded if DB is empty
        if (empty($milestones)) {
            $milestones = [
                [
                    'year' => '1999',
                    'title' => __('Company Founded'),
                    'desc' => __('Started as a small dedicated engineering firm.'),
                    'image' => '/images/projects/Thumbnail-1.jpg'
                ],
                [
                    'year' => '2010',
                    'title' => __('First Mega Project'),
                    'desc' => __('Secured our first major government infrastructure contract.'),
                    'image' => '/images/projects/Thumbnail-2.jpg'
                ],
                [
                    'year' => '2026',
                    'title' => __('Industry Leaders'),
                    'desc' => __('Recognized as the top infrastructure firm in the Kingdom of Cambodia.'),
                    'image' => '/images/projects/Thumbnail-3.jpg'
                ]
            ];
        }

        $orgChart = (function () {
            $buildNode = function ($unit) use (&$buildNode) {
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

                return [
                    'name' => $name,
                    'role' => $role,
                    'type' => $type,
                    'image' => $unit->employee?->image ? \Illuminate\Support\Facades\Storage::url($unit->employee->image) : null,
                    'phone' => $unit->employee?->phone,
                    'bio' => $unit->employee?->bio,
                    'children' => \App\Models\OrgUnit::where('parentId', $unit->id)
                        ->orderBy('orderIndex')
                        ->with(['employee', 'department'])
                        ->get()
                        ->map(fn($child) => $buildNode($child))
                        ->toArray()
                ];
            };

            $roots = \App\Models\OrgUnit::whereNull('parentId')
                ->orderBy('orderIndex')
                ->with(['employee', 'department'])
                ->get();

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
        })();
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
                    class="text-4xl md:text-5xl lg:text-6xl font-heading font-black text-white mb-6 tracking-tight leading-tight uppercase">
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
        <section class="py-32 px-6 bg-white overflow-hidden">
            <div class="max-w-[1400px] mx-auto grid lg:grid-cols-2 gap-20 items-center">

                <!-- Left: Aesthetic Image Grid with Badge -->
                <div class="relative" x-data="{ shown: false }" x-intersect.once="shown = true">
                    <div :class="shown ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-12'"
                        class="grid grid-cols-2 gap-6 transition-all duration-1000 relative">
                        <div class="space-y-6">
                            <div class="aspect-[4/5] rounded-[2.5rem] overflow-hidden shadow-2xl">
                                <img src="/images/projects/Thumbnail-1.jpg"
                                    class="object-cover w-full h-full hover:scale-105 transition-transform duration-700" />
                            </div>
                            <div class="aspect-square rounded-[2.5rem] overflow-hidden shadow-2xl">
                                <img src="/images/projects/Thumbnail-3.jpg"
                                    class="object-cover w-full h-full hover:scale-105 transition-transform duration-700" />
                            </div>
                        </div>
                        <div class="space-y-6 pt-12">
                            <div class="aspect-square rounded-[2.5rem] overflow-hidden shadow-2xl">
                                <img src="/images/projects/Thumbnail-2.jpg"
                                    class="object-cover w-full h-full hover:scale-105 transition-transform duration-700" />
                            </div>
                            <div class="aspect-[4/5] rounded-[2.5rem] overflow-hidden shadow-2xl relative">
                                <img src="/images/projects/Thumbnail-4.jpg"
                                    class="object-cover w-full h-full hover:scale-105 transition-transform duration-700" />

                                <!-- Floating 25+ Years Badge -->
                                <div
                                    class="absolute -bottom-6 -right-6 bg-accent-orange text-white p-8 rounded-3xl shadow-[0_20px_40px_rgba(255,107,0,0.3)] z-20 flex flex-col items-center justify-center min-w-[140px] transform hover:scale-105 transition-transform">
                                    <span class="text-4xl font-black leading-none">25+</span>
                                    <span
                                        class="text-[10px] font-black uppercase tracking-[0.2em] mt-1">{{ __('Years') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Content & MVG -->
                <div x-data="{ shown: false }" x-intersect.once="shown = true"
                    :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                    class="transition-all duration-1000 delay-200">
                    <span class="text-accent-orange font-bold uppercase tracking-widest text-sm mb-4 block">
                        {{ __('WHO WE ARE') }}
                    </span>
                    <h2 class="text-4xl md:text-5xl font-bold text-gray-900 leading-tight mb-6">
                        @php
                            $profile = \App\Models\SystemSetting::get('organization_profile', []);
                            $locale = app()->getLocale();
                            $localeKey = $locale === 'kh' ? 'km' : $locale;
                            $tagline = $profile[$localeKey]['tagline'] ?? "Cambodia's Premier Construction Partner";
                        @endphp
                        {{ $tagline }}
                    </h2>
                    
                    <p class="text-gray-500 text-lg leading-relaxed mb-12 whitespace-pre-line">
                        {{ $brand['company_story'] ?? __("With over 25 years of experience, we have established ourselves as Cambodia's most trusted construction partner, delivering projects that stand the test of time and elevate communities.") }}
                    </p>

                    <!-- Mission/Vision/Goal Interactive List -->
                    <div class="space-y-6" x-data="{ active: 'vision' }">
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
                        <div class="group cursor-pointer border-b border-gray-100 last:border-b-0 pb-6"
                             @click="active = (active === '{{ $item['id'] }}' ? null : '{{ $item['id'] }}')">
                            <div class="flex gap-6 items-start">
                                <!-- Icon Box -->
                                <div class="w-14 h-14 rounded-[1rem] bg-[#FFF0E6] text-accent-orange flex items-center justify-center shrink-0 transition-colors duration-500"
                                     :class="active === '{{ $item['id'] }}' ? 'bg-accent-orange text-white' : ''">
                                    @if($item['icon'] === 'eye')
                                        <x-lucide-eye class="w-6 h-6" stroke-width="2" />
                                    @elseif($item['icon'] === 'flag')
                                        <x-lucide-flag class="w-6 h-6" stroke-width="2" />
                                    @elseif($item['icon'] === 'target')
                                        <x-lucide-target class="w-6 h-6" stroke-width="2" />
                                    @endif
                                </div>

                                <!-- Text Content -->
                                <div class="flex-grow">
                                    <div class="flex items-center justify-between mb-2">
                                        <h3 class="text-xl font-bold text-gray-900 transition-colors duration-500"
                                            :class="active === '{{ $item['id'] }}' ? 'text-accent-orange' : ''">
                                            {{ $item['title'] }}
                                        </h3>
                                        <div class="w-8 h-8 rounded-full border flex items-center justify-center transition-all duration-500"
                                             :class="active === '{{ $item['id'] }}' ? 'border-accent-orange text-accent-orange rotate-180' : 'border-gray-200 text-gray-400'">
                                            <x-lucide-chevron-down class="w-4 h-4" />
                                        </div>
                                    </div>
                                    
                                    <div x-show="active === '{{ $item['id'] }}'" x-collapse>
                                        <p class="text-gray-500 text-base leading-relaxed whitespace-pre-line pb-4">
                                            {{ $item['desc'] }}
                                        </p>
                                    </div>
                                    
                                    <p x-show="active !== '{{ $item['id'] }}'" class="text-gray-400 text-sm italic truncate max-w-md">
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

                            <div class="prose prose-titan max-w-none text-titan-navy mb-8">
                                {!! $brand['ceo_message'] ?? __('Construction is not just about concrete and steel. It is about building trust, fostering communities, and leaving a legacy that stands the test of time.') !!}
                            </div>

                            <div>
                                <div class="text-titan-navy font-black text-lg uppercase mb-1">{{ $ceoName }}</div>
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
                    <h2 class="text-3xl md:text-4xl font-heading font-black text-titan-navy">{{ __('Our Core Values') }}
                    </h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($aboutData['values'] as $i => $value)
                        <div x-data="{ shown: false }" x-intersect.once="shown = true"
                            :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                            class="transition-all duration-700" style="transition-delay: {{ $i * 100 }}ms">
                            <div
                                class="bg-gray-50/50 p-10 rounded-[2.5rem] border-2 border-transparent hover:border-gray-100 hover:bg-white hover:shadow-2xl hover:shadow-gray-200/30 group transition-all duration-500 h-full">
                                <div
                                    class="w-16 h-16 bg-accent-orange/5 rounded-2xl flex items-center justify-center text-accent-orange mb-8 group-hover:bg-accent-orange group-hover:text-white transition-all duration-500 shadow-sm group-hover:shadow-accent-orange/30">
                                    <x-dynamic-component :component="$value['icon']" class="w-7 h-7" />
                                </div>
                                <h3
                                    class="text-xl font-heading font-black text-titan-navy mb-4 group-hover:text-accent-orange transition-colors duration-500">
                                    {{ $value['title'] }}
                                </h3>
                                <p class="text-sm text-titan-navy/50 leading-relaxed font-medium">
                                    {{ $value['content'] }}
                                </p>
                            </div>
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
                        class="text-accent-orange font-black uppercase tracking-[0.3em] text-xs mb-4 block">{{ __('OUR JOURNEY') }}</span>
                    <h2 class="text-3xl md:text-5xl font-heading font-black text-titan-navy uppercase tracking-tight">
                        {{ __('Company Milestones') }}
                    </h2>
                </div>

                <div class="space-y-32 relative">
                    <!-- Vertical Timeline Line -->
                    <div class="absolute left-1/2 top-0 bottom-0 w-[1px] bg-gray-200 hidden md:block"></div>

                    @foreach($milestones as $idx => $milestone)
                        <div x-data="{ shown: false, open: false }" x-intersect.once="shown = true"
                            :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'"
                            class="relative flex flex-col {{ $idx % 2 === 0 ? 'md:flex-row' : 'md:flex-row-reverse' }} items-center gap-12 md:gap-24 transition-all duration-1000">

                            <!-- Content Side -->
                            <div
                                class="w-full md:w-1/2 flex flex-col {{ $idx % 2 === 0 ? 'md:items-end md:text-right' : 'md:items-start md:text-left' }}">
                                <div
                                    class="inline-flex items-center justify-center px-6 py-2 bg-accent-orange text-white rounded-2xl text-xs font-black mb-6 shadow-lg shadow-accent-orange/20">
                                    {{ $milestone['year'] }}
                                </div>
                                <h3
                                    class="text-2xl md:text-3xl font-heading font-black text-titan-navy mb-6 tracking-tight">
                                    {{ $milestone['title'] }}
                                </h3>
                                <p class="text-titan-navy/60 text-lg leading-relaxed max-w-xl">{{ $milestone['desc'] }}</p>

                                <!-- Interactive Detail Toggle -->
                                <button @click="open = !open"
                                    class="mt-8 flex items-center gap-3 group/btn text-accent-orange font-black uppercase text-[10px] tracking-widest hover:text-titan-navy transition-all duration-300">
                                    <span x-text="open ? '{{ __('Show Less') }}' : '{{ __('Explore Details') }}'"></span>
                                    <div
                                        class="w-8 h-8 rounded-full border border-accent-orange/20 flex items-center justify-center group-hover/btn:border-titan-navy transition-colors">
                                        <x-lucide-chevron-down class="w-3.5 h-3.5 transition-transform duration-500"
                                            ::class="open ? 'rotate-180' : ''" />
                                    </div>
                                </button>

                                <div x-show="open" x-collapse>
                                    <div
                                        class="mt-8 p-8 bg-gray-50 rounded-3xl border border-gray-100 text-titan-navy/50 italic leading-relaxed text-sm max-w-xl {{ $idx % 2 === 0 ? 'md:ml-auto' : 'md:mr-auto' }}">
                                        {{ __('This journey began as a vision of excellence. Through dedication and hard work, we expanded our footprint, technical expertise, and community impact, setting new standards in the Cambodian construction landscape.') }}
                                    </div>
                                </div>
                            </div>

                            <!-- Timeline Dot -->
                            <div class="hidden md:flex items-center justify-center absolute left-1/2 -translate-x-1/2 w-8 h-8 rounded-full bg-white border-4 border-accent-orange z-10 shadow-xl transition-transform duration-500"
                                :class="open ? 'scale-125 bg-accent-orange' : ''">
                                <div class="w-2 h-2 rounded-full bg-accent-orange transition-colors"
                                    :class="open ? 'bg-white' : ''"></div>
                            </div>

                            <!-- Image Side -->
                            <div class="w-full md:w-1/2">
                                <div class="relative aspect-[16/10] rounded-[2rem] overflow-hidden shadow-2xl group cursor-pointer"
                                    @click="open = !open">
                                    <img src="{{ $milestone['image'] }}"
                                        class="object-cover w-full h-full transition-transform duration-1000 group-hover:scale-110"
                                        :class="open ? 'scale-110' : ''" />
                                    <div class="absolute inset-0 bg-accent-orange/10 mix-blend-overlay transition-opacity duration-500"
                                        :class="open ? 'opacity-100' : 'opacity-0'"></div>
                                    <div
                                        class="absolute inset-0 bg-gradient-to-t from-titan-navy/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity">
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- ORG CHART SECTON -->
        <section id="leadership" class="py-32 px-6 bg-gray-50 overflow-hidden relative border-b border-gray-100">
            <div class="max-w-[1700px] mx-auto relative z-10 overflow-x-auto pb-12">
                <div class="text-center mb-24">
                    <span
                        class="text-accent-orange font-black uppercase tracking-[0.3em] text-xs mb-4 block">{{ __('GOVERNANCE') }}</span>
                    <h2 class="text-3xl md:text-5xl font-heading font-black text-titan-navy uppercase tracking-tight">
                        {{ __('KIM MEX ORGANIZATION STRUCTURE') }}
                    </h2>
                </div>

                <div class="min-w-[800px] flex justify-center">
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
                        <h2 class="text-3xl md:text-4xl font-heading font-black text-white mb-6 leading-tight">
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