<x-layouts.app title="{{ __('Careers') }}" description="{{ __('Join the Kimmex team and build your future in the construction industry.') }}">

    @php
        $jobs = \Illuminate\Support\Facades\Cache::remember('careers_jobs_data_'.app()->getLocale(), now()->addHours(12), function() {
            $jobsDb = \App\Models\JobPosting::where('isActive', true)
                ->with('department')
                ->orderBy('created_at', 'desc')
                ->get();

            return $jobsDb->map(function (\App\Models\JobPosting $j) {
                $deptName = $j->department ? $j->department->getTranslation('name', app()->getLocale()) : __('General');
                return [
                    'id' => $j->id,
                    'slug' => $j->slug,
                    'title' => $j->getTranslation('title', app()->getLocale()),
                    'dept' => $deptName,
                    'loc' => $j->getTranslation('location', app()->getLocale()),
                    'type' => __(str_replace('_', ' ', \Illuminate\Support\Str::title(strtolower($j->type ?? 'FULL_TIME')))),
                    'salary' => $j->getTranslation('salary', app()->getLocale()) ?: __('Negotiable'),
                    'experience' => $j->getTranslation('experience', app()->getLocale()) ?: __('2-3 Years'),
                    'postedDate' => $j->created_at ? $j->created_at->format('M d, Y') : now()->format('M d, Y'),
                    'tags' => [$deptName],
                    'summary' => \Illuminate\Support\Str::limit(strip_tags($j->getTranslation('summary', app()->getLocale())), 150)
                ];
            })->toArray();
        });

        $categories = array_values(array_unique(array_merge([__('All Departments')], array_column($jobs, 'dept'))));
        $locations = array_values(array_unique(array_merge([__('All Locations')], array_column($jobs, 'loc'))));

        // Fallback for empty DB
        if (empty($jobs)) {
            $jobs = [
                ['id' => 'gen', 'slug' => 'gen', 'title' => __('Visionary Talent'), 'dept' => __('General'), 'loc' => __('Phnom Penh'), 'type' => __('Full-time'), 'salary' => __('Competitive'), 'experience' => __('Mixed'), 'postedDate' => now()->format('M d, Y'), 'tags' => [__('Hiring')], 'summary' => __('We are always looking for exceptional engineers and managers.')]
            ];
        }
    @endphp

    @php
        $isKhmer = app()->getLocale() === 'km';
        $heroCtaBase = 'inline-flex w-full sm:w-auto items-center justify-center gap-3 px-5 sm:px-8 py-3.5 sm:py-4 rounded-xl sm:rounded-2xl font-bold shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all duration-500';
        $heroCtaPrimary = $heroCtaBase . ' bg-titan-red text-white';
        $heroCtaSecondary = $heroCtaBase . ' text-white border border-white/15 bg-white/10 backdrop-blur-md hover:bg-white/15';
        $heroCtaText = $isKhmer ? 'font-khmer text-sm normal-case tracking-normal leading-tight' : 'text-[11px] uppercase tracking-[0.28em]';
        $heroSubtitleClass = $isKhmer
            ? 'font-khmer text-lg md:text-xl text-white font-bold leading-relaxed tracking-normal normal-case drop-shadow-xl'
            : 'text-xl md:text-2xl text-white font-bold leading-tight uppercase tracking-[0.15em] drop-shadow-xl';
        $heroBadgeLabelClass = $isKhmer
            ? 'font-khmer text-xs font-bold tracking-normal normal-case text-white/75 mb-2'
            : 'text-[10px] uppercase tracking-[0.3em] text-white/50 font-bold mb-2';
        $heroScrollLabelClass = $isKhmer
            ? 'font-khmer text-[10px] font-bold tracking-normal normal-case text-white [writing-mode:vertical-lr]'
            : 'text-[9px] font-bold uppercase tracking-[0.5em] text-white [writing-mode:vertical-lr]';
    @endphp

    <div x-data="{ 
    filterDept: '{{ __('All Departments') }}', 
    filterLoc: '{{ __('All Locations') }}', 
    searchQuery: '',
    isApplyOpen: false,
    jobs: @js($jobs),
    get filteredJobs() {
        return this.jobs.filter(job => {
            if (this.filterDept !== '{{ __('All Departments') }}' && job.dept !== this.filterDept) return false;
            if (this.filterLoc !== '{{ __('All Locations') }}' && job.loc !== this.filterLoc) return false;
            if (this.searchQuery && !job.title.toLowerCase().includes(this.searchQuery.toLowerCase())) return false;
            return true;
        });
    },
    clearFilters() {
        this.filterDept = '{{ __('All Departments') }}';
        this.filterLoc = '{{ __('All Locations') }}';
        this.searchQuery = '';
    }
}" class="bg-white min-h-screen text-titan-navy">

        <!-- === PREMIUM CAREERS HERO === -->
        <section class="relative min-h-[680px] md:min-h-[800px] md:h-screen flex items-center overflow-hidden bg-titan-navy">
            {{-- Background Image with Brighter Overlay --}}
            <div class="absolute inset-0">
                <img src="/images/projects/Thumbnail-5.jpg" alt="{{ __('Careers Excellence') }}" class="w-full h-full object-cover opacity-100 animate-slow-zoom" loading="eager" decoding="async" fetchpriority="high" />
                <div class="absolute inset-0 bg-gradient-to-r from-titan-navy/60 via-titan-navy/30 to-transparent"></div>
                <div class="absolute inset-0 bg-titan-navy/20"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-titan-navy/60 via-transparent to-transparent"></div>
            </div>

            <div class="relative z-20 w-full max-w-[1400px] mx-auto px-4 sm:px-6 pt-28 md:pt-32 pb-16 md:pb-40" x-data="{ shown: false }" x-init="setTimeout(() => shown = true, 100)">

                <h1 :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'"
                    class="transition-all duration-1000 delay-300 font-heading font-[900] text-white mb-6 md:mb-10 leading-none tracking-normal uppercase drop-shadow-2xl"
                    style="font-size: clamp(1.75rem, 5vw, 3.5rem) !important; color: white !important; font-weight: 900 !important;">
                    <span class="block">{{ __('BUILD YOUR') }}</span>
                    <span class="block text-titan-red mt-2">{{ __('LEGACY') }}</span>
                </h1>

                <div :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'" 
                     class="transition-all duration-1000 delay-500 border-l-4 border-titan-red pl-4 sm:pl-6 md:pl-10 mb-8 md:mb-16 max-w-2xl">
                    <p class="{{ $heroSubtitleClass }}" style="color: white !important;">
                        {{ __('Join a team of visionaries') }}<br/>
                        {{ __('shaping the future.') }}
                    </p>
                </div>

                <!-- Action Buttons -->
                <div :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'"
                    class="transition-all duration-1000 delay-700 flex flex-col sm:flex-row sm:flex-wrap gap-3 sm:gap-6">
                    <button @click="document.getElementById('openings')?.scrollIntoView({ behavior: 'smooth' })"
                        class="{{ $heroCtaPrimary }}">
                        <span class="{{ $heroCtaText }}">{{ __('Explore Roles') }}</span>
                        <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-white/20 flex items-center justify-center shrink-0">
                            <x-lucide-arrow-down class="w-4 h-4" />
                        </div>
                    </button>

                    <button @click="isApplyOpen = true"
                        class="{{ $heroCtaSecondary }}">
                        <span class="{{ $heroCtaText }}">{{ __('Direct Apply') }}</span>
                        <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-white/10 flex items-center justify-center shrink-0">
                            <x-lucide-send class="w-4 h-4" />
                        </div>
                    </button>
                </div>

                <div :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'"
                    class="transition-all duration-1000 delay-900 mt-8 md:mt-12 grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4 max-w-4xl">
                    <div class="rounded-xl sm:rounded-2xl border border-white/10 bg-white/10 backdrop-blur-md px-4 sm:px-5 py-3.5 sm:py-4 text-white shadow-lg">
                        <div class="{{ $heroBadgeLabelClass }}">{{ __('Fast response') }}</div>
                        <div class="font-bold text-sm">{{ __('Applications are reviewed by our team.') }}</div>
                    </div>
                    <div class="rounded-xl sm:rounded-2xl border border-white/10 bg-white/10 backdrop-blur-md px-4 sm:px-5 py-3.5 sm:py-4 text-white shadow-lg">
                        <div class="{{ $heroBadgeLabelClass }}">{{ __('Clear process') }}</div>
                        <div class="font-bold text-sm">{{ __('Know the next step before you apply.') }}</div>
                    </div>
                    <div class="rounded-xl sm:rounded-2xl border border-white/10 bg-white/10 backdrop-blur-md px-4 sm:px-5 py-3.5 sm:py-4 text-white shadow-lg">
                        <div class="{{ $heroBadgeLabelClass }}">{{ __('Direct entry') }}</div>
                        <div class="font-bold text-sm">{{ __('Apply to a role or send a general application.') }}</div>
                    </div>
                </div>
            </div>

            <!-- Scroll Indicator -->
            <div class="absolute bottom-10 right-10 flex flex-col items-center gap-4 opacity-40 hover:opacity-100 transition-opacity duration-500 hidden lg:flex">
                <span class="{{ $heroScrollLabelClass }}">{{ __('Scroll') }}</span>
                <div class="w-[1px] h-20 bg-gradient-to-b from-white to-transparent"></div>
            </div>
        </section>

        <!-- WHY JOIN US -->
        <section x-data="{ revealed: false }" x-intersect.once="revealed = true"
            class="pt-16 pb-16 md:pt-32 md:pb-32 max-w-[1400px] mx-auto px-4 sm:px-6">
                <div :class="revealed ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10'"
                    class="transition-all duration-700 flex flex-col items-start gap-4 mb-10 md:mb-20">
                    <div class="w-20 h-1.5 bg-titan-red rounded-full"></div>
                    <div>
                    <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-titan-navy uppercase tracking-normal">
                        {{ __('Why Work With Us?') }}
                    </h2>
                    <p class="text-titan-navy/40 text-sm font-bold uppercase tracking-widest mt-2">
                        {{ __('Engineering Excellence, Together.') }}
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 md:gap-10">
                @php
                    $values = [
                        ['icon' => 'award', 'title' => __('Excellence'), 'desc' => __('We maintain the highest standards across all our large-scale construction and engineering projects.')],
                        ['icon' => 'target', 'title' => __('Impact'), 'desc' => __('Our work contributes directly to the sustainable growth of infrastructure across Cambodia.')],
                        ['icon' => 'users', 'title' => __('Growth'), 'desc' => __('We provide unmatched opportunities for professional development within our global-standard teams.')]
                    ];
                @endphp

                @foreach($values as $i => $v)
                <div x-data="{ hover: false }" @mouseenter="hover = true" @mouseleave="hover = false"
                    x-bind:class="revealed ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10'"
                    class="transition-all duration-1000 relative"
                    style="transition-delay: {{ 200 + ($i * 150) }}ms">
                    <div class="bg-white border-2 border-gray-100 rounded p-6 md:p-12 transition-all duration-500 h-full relative overflow-hidden"
                        x-bind:class="hover ? 'border-titan-red shadow-[0_24px_50px_-18px_rgba(15,23,42,0.14)] -translate-y-1' : ''">

                        <div class="w-12 h-12 md:w-16 md:h-16 rounded-full flex items-center justify-center text-titan-red mb-6 md:mb-10 transition-all duration-500 border border-gray-100"
                            x-bind:class="hover ? 'border-titan-red bg-titan-red/5' : 'bg-gray-50'">
                            <x-dynamic-component :component="'lucide-' . $v['icon']" class="w-7 h-7" stroke-width="1.5" />
                        </div>

                        <h3 class="text-xl md:text-2xl font-bold text-titan-navy uppercase tracking-normal mb-3 md:mb-6 transition-colors duration-500"
                            x-bind:class="hover ? 'text-titan-red' : ''">
                            {{ $v['title'] }}
                        </h3>
                        <p class="text-titan-navy/50 text-base leading-relaxed font-medium">
                            {{ $v['desc'] }}
                        </p>

                        <!-- Bottom Accent Line -->
                        <div class="absolute bottom-0 left-0 right-0 h-1 bg-titan-red transition-all duration-500 transform origin-left"
                            x-bind:class="hover ? 'scale-x-100' : 'scale-x-0'"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </section>

        <!-- HIRING PROCESS -->
        <section x-data="{ revealed: false }" x-intersect.once="revealed = true" class="py-16 md:py-24 bg-white relative">
            <div class="max-w-[1200px] mx-auto px-4 sm:px-6">
                <div :class="revealed ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10'"
                    class="transition-all duration-700 flex flex-col items-center text-center mb-10 md:mb-16">
                    <!-- Title Group -->
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-[1px] bg-titan-red"></div>
                        <span
                            class="text-[10px] font-bold text-titan-red uppercase tracking-[0.3em]">{{ __('The Process') }}</span>
                        <div class="w-10 h-[1px] bg-titan-red"></div>
                    </div>
                    <h2 class="text-2xl md:text-3xl font-bold text-titan-navy uppercase tracking-normal">
                        {{ __('Our Hiring Journey') }}
                    </h2>
                    <p class="text-titan-navy/35 text-xs max-w-md mt-4">
                        {{ __('We follow a clean, transparent, and efficient process to ensure the best fit for our engineering teams.') }}
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 md:gap-6 lg:gap-8 relative">
                    <!-- Connector Line (Desktop) -->
                    <div class="hidden md:block absolute top-[40px] left-[10%] right-[10%] h-[1px] bg-gray-100 z-0">
                    </div>

                    @php
                        $steps = [
                            ['title' => __('Apply'), 'desc' => __('Submit your documents via our recruitment portal.')],
                            ['title' => __('Screening'), 'desc' => __('Conversation with HR to discuss your fit and goals.')],
                            ['title' => __('Interview'), 'desc' => __('Deep-dive technical assessment with our experts.')],
                            ['title' => __('Finalize'), 'desc' => __('An offer that reflects your value and joining the team.')]
                        ];
                    @endphp

                    @foreach($steps as $i => $step)
                        <div :class="revealed ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10'"
                            :style="'transition-delay: ' + (150 + ($i * 100)) + 'ms'"
                            class="transition-all duration-700 relative z-10 group">
                            <div
                                class="bg-gray-50/50 border border-gray-100 rounded p-5 md:p-8 hover:bg-white hover:border-titan-red/30 hover:shadow-[0_20px_45px_-18px_rgba(15,23,42,0.12)] transition-all duration-500 md:min-h-[180px]">
                                <!-- Number Badge -->
                                <div
                                    class="w-10 h-10 rounded-full bg-white border-2 border-gray-100 flex items-center justify-center text-xs font-bold text-titan-red mb-6 shadow-sm group-hover:border-titan-red group-hover:shadow-[0_0_0_4px_rgba(15,23,42,0.06)] transition-all duration-300">
                                    0{{ $i + 1 }}
                                </div>
                                <h3
                                    class="text-sm font-bold text-titan-navy uppercase tracking-widest mb-3 group-hover:text-titan-red transition-colors">
                                    {{ $step['title'] }}
                                </h3>
                                <p class="text-titan-navy/40 text-[11px] leading-relaxed line-clamp-2">{{ $step['desc'] }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- JOB LISTINGS -->
        <section id="openings" x-data="{ revealed: false }" x-intersect.once="revealed = true"
            class="scroll-mt-28 md:scroll-mt-32 pt-16 pb-16 md:pt-24 md:pb-24 max-w-[1200px] mx-auto px-4 sm:px-6">
            <!-- Header Section -->
                <div :class="revealed ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10'"
                    class="transition-all duration-700 mb-10">
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-2">
                    <div class="flex items-start sm:items-center gap-3 sm:gap-4 min-w-0">
                        <div class="w-1.5 h-9 sm:h-10 bg-titan-red rounded-full shrink-0"></div>
                        <h2 class="text-2xl md:text-3xl font-bold text-titan-navy uppercase tracking-normal leading-tight">
                            {{ __('Current Openings') }}
                        </h2>
                    </div>
                    <div class="flex flex-wrap items-center gap-3">
                        <span class="text-xs uppercase tracking-[0.25em] text-titan-navy/35 font-bold">
                            <span x-text="filteredJobs.length"></span> {{ __('roles available') }}
                        </span>
                        <button @click="clearFilters()"
                            class="hidden md:inline-flex items-center gap-2 rounded-full border border-gray-200 bg-white px-4 py-2 text-[10px] font-bold uppercase tracking-[0.18em] text-titan-navy hover:border-titan-red/30 hover:text-titan-red transition-colors shadow-sm">
                            <x-lucide-rotate-ccw class="w-3.5 h-3.5" />
                            {{ __('Clear filters') }}
                        </button>
                    </div>
                </div>
                <p class="text-titan-navy/35 text-sm sm:ml-5">
                    {{ __('Find your place among Cambodia\'s most impactful engineering teams.') }}
                </p>
            </div>

            <!-- Filters Section (One Line Full Width) -->
            <div :class="revealed ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10'"
                class="transition-all duration-700 mb-12 relative z-40">
                <div class="rounded-xl md:rounded-3xl border border-gray-100 bg-gray-50/90 p-4 md:p-5 shadow-sm">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                        <div class="min-w-0">
                            <div class="text-[10px] font-bold uppercase tracking-[0.25em] text-titan-red">{{ __('Refine results') }}</div>
                            <div class="text-sm text-titan-navy/55 mt-1">{{ __('Search by title, then narrow by department or location.') }}</div>
                        </div>
                        <button @click="clearFilters()"
                            class="md:hidden inline-flex self-start items-center gap-2 rounded-full border border-gray-200 bg-white px-3 py-2 text-[10px] font-bold uppercase tracking-[0.18em] text-titan-navy hover:border-titan-red/30 hover:text-titan-red transition-colors shadow-sm">
                            <x-lucide-rotate-ccw class="w-3.5 h-3.5" />
                            {{ __('Reset') }}
                        </button>
                    </div>
                    <div class="flex flex-col md:flex-row gap-3 w-full">
                    <!-- Search -->
                    <div class="relative flex-grow">
                        <x-lucide-search class="absolute left-4 top-1/2 -translate-y-1/2 text-titan-navy/20 w-4 h-4" />
                        <input type="text"
                            placeholder="{{ __('Search roles, e.g. Engineer or Supervisor') }}"
                            x-model="searchQuery"
                            class="w-full pl-11 pr-4 py-3.5 rounded border-none bg-white text-sm font-semibold text-titan-navy focus:ring-2 focus:ring-titan-red/10 transition-all placeholder:text-titan-navy/25 shadow-sm" />
                    </div>

                    <!-- Department Dropdown -->
                    <div class="relative shrink-0 md:w-64" x-data="{ open: false }">
                        <button @click="open = !open"
                            class="w-full flex items-center gap-2 bg-white border-none px-5 py-3.5 rounded text-sm font-bold text-titan-navy justify-between transition-all hover:bg-gray-50 shadow-sm"
                            :class="open ? 'ring-2 ring-titan-red/10' : ''">
                            <div class="flex items-center gap-2 min-w-0">
                                <x-lucide-filter class="text-titan-red w-3.5 h-3.5 shrink-0" />
                                <span x-text="filterDept" class="truncate"></span>
                            </div>
                            <x-lucide-chevron-down
                                class="text-titan-navy/20 w-3.5 h-3.5 shrink-0 transition-transform duration-300"
                                ::class="open ? 'rotate-180' : ''" />
                        </button>
                        <div x-show="open" @click.away="open = false" style="display: none"
                            class="absolute top-full left-0 w-full mt-2 bg-white border border-gray-100 rounded shadow-xl py-2 z-50 overflow-hidden">
                            @foreach($categories as $cat)
                                <button @click="filterDept = '{{ addslashes($cat) }}'; open = false"
                                    class="w-full text-left px-5 py-2.5 text-[13px] font-bold hover:bg-gray-50 flex items-center justify-between transition-colors"
                                    :class="filterDept === '{{ addslashes($cat) }}' ? 'text-titan-red bg-red-50/50' : 'text-titan-navy/60'">
                                    <span class="truncate pr-2">{{ $cat }}</span>
                                    <x-lucide-check x-show="filterDept === '{{ addslashes($cat) }}'"
                                        class="text-titan-red w-3.5 h-3.5 shrink-0" />
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Location Dropdown -->
                    <div class="relative shrink-0 md:w-60" x-data="{ open: false }">
                        <button @click="open = !open"
                            class="w-full flex items-center gap-2 bg-white border-none px-5 py-3.5 rounded text-sm font-bold text-titan-navy justify-between transition-all hover:bg-gray-50 shadow-sm"
                            :class="open ? 'ring-2 ring-titan-red/10' : ''">
                            <div class="flex items-center gap-2 min-w-0">
                                <x-lucide-map-pin class="text-titan-red w-3.5 h-3.5 shrink-0" />
                                <span x-text="filterLoc" class="truncate"></span>
                            </div>
                            <x-lucide-chevron-down
                                class="text-titan-navy/20 w-3.5 h-3.5 shrink-0 transition-transform duration-300"
                                ::class="open ? 'rotate-180' : ''" />
                        </button>
                        <div x-show="open" @click.away="open = false" style="display: none"
                            class="absolute top-full left-0 w-full mt-2 bg-white border border-gray-100 rounded shadow-xl py-2 z-50 overflow-hidden">
                            @foreach($locations as $loc)
                                <button @click="filterLoc = '{{ addslashes($loc) }}'; open = false"
                                    class="w-full text-left px-5 py-2.5 text-[13px] font-bold hover:bg-gray-50 flex items-center justify-between transition-colors"
                                    :class="filterLoc === '{{ addslashes($loc) }}' ? 'text-titan-red bg-red-50/50' : 'text-titan-navy/60'">
                                    <span class="truncate pr-2">{{ $loc }}</span>
                                    <x-lucide-check x-show="filterLoc === '{{ addslashes($loc) }}'"
                                        class="text-titan-red w-3.5 h-3.5 shrink-0" />
                                </button>
                            @endforeach
                        </div>
                    </div>
                    </div>
                </div>
            </div>

            <!-- Job Cards -->
            <div class="space-y-6">
                <template x-for="(job, index) in filteredJobs" :key="job.id">
                    <div
                        class="bg-white border border-gray-100 rounded p-4 sm:p-6 md:p-8 hover:shadow-[0_24px_50px_-18px_rgba(15,23,42,0.12)] hover:border-gray-200 transition-all duration-400 group relative overflow-hidden">
                        <!-- Hover Accent Bar (Design-Z) -->
                        <div
                            class="absolute inset-y-0 left-0 w-0 group-hover:w-2 bg-titan-red transition-all duration-300">
                        </div>

                        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-5 md:gap-8 relative z-10">
                            <!-- Left: Job Info -->
                            <div class="flex-1 min-w-0">
                        <div class="flex items-center flex-wrap gap-2 mb-4">
                            <template x-for="tag in job.tags">
                                <span
                                            class="max-w-full break-words px-3 py-1 bg-titan-red/5 text-titan-red text-[10px] font-bold uppercase tracking-[0.1em] rounded-md border border-titan-red/10"
                                            x-text="tag"></span>
                            </template>
                            <span
                                class="bg-gray-50 px-3 py-1 rounded-md border border-gray-100 text-[10px] font-bold text-titan-navy/30 flex items-center gap-1.5">
                                <x-lucide-calendar class="w-3 h-3" /> <span x-text="job.postedDate"></span>
                            </span>
                            <span class="bg-titan-navy/5 px-3 py-1 rounded-md border border-titan-navy/10 text-[10px] font-bold text-titan-navy/60 flex items-center gap-1.5">
                                <x-lucide-dollar-sign class="w-3 h-3 text-titan-red" />
                                <span x-text="job.salary"></span>
                            </span>
                        </div>
                                <h3 class="text-xl md:text-2xl font-bold text-titan-navy group-hover:text-titan-red transition-colors mb-3 tracking-normal leading-tight"
                                    x-text="job.title"></h3>
                                <p class="text-titan-navy/40 text-[13px] leading-relaxed line-clamp-2 max-w-2xl"
                                    x-text="job.summary"></p>
                            </div>

                            <!-- Middle: Details -->
                            <div
                                class="grid grid-cols-1 sm:grid-cols-3 lg:flex lg:flex-wrap gap-4 sm:gap-6 lg:gap-8 text-sm shrink-0 border-t lg:border-t-0 lg:border-l border-gray-100 pt-5 lg:pt-0 lg:pl-8">
                                <div>
                                    <div
                                        class="text-[10px] font-bold uppercase tracking-widest text-titan-navy/20 mb-2">
                                        {{ __('Location') }}
                                    </div>
                                    <div class="font-semibold text-titan-navy flex items-center gap-2 text-xs">
                                        <div class="w-6 h-6 rounded-lg bg-gray-50 flex items-center justify-center">
                                            <x-lucide-map-pin class="w-3 h-3 text-titan-red" />
                                        </div>
                                        <span x-text="job.loc"></span>
                                    </div>
                                </div>
                                <div>
                                    <div
                                        class="text-[10px] font-bold uppercase tracking-widest text-titan-navy/20 mb-2">
                                        {{ __('Experience') }}
                                    </div>
                                    <div class="font-semibold text-titan-navy flex items-center gap-2 text-xs">
                                        <div class="w-6 h-6 rounded-lg bg-gray-50 flex items-center justify-center">
                                            <x-lucide-briefcase class="w-3 h-3 text-titan-red" />
                                        </div>
                                        <span x-text="job.experience"></span>
                                    </div>
                                </div>
                                <div>
                                    <div
                                        class="text-[10px] font-bold uppercase tracking-widest text-titan-navy/20 mb-2">
                                        {{ __('Job Type') }}
                                    </div>
                                    <div class="font-semibold text-titan-navy flex items-center gap-2 text-xs">
                                        <div class="w-6 h-6 rounded-lg bg-gray-50 flex items-center justify-center">
                                            <x-lucide-clock class="w-3 h-3 text-titan-red" />
                                        </div>
                                        <span x-text="job.type"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Right: CTA -->
                            <div class="shrink-0 pt-2 lg:pt-0">
                                <a :href="'/careers/' + job.slug"
                                    class="w-full sm:w-auto border-2 border-titan-navy text-titan-navy px-6 md:px-8 py-3.5 md:py-4 rounded font-bold text-[11px] uppercase tracking-widest hover:border-titan-red hover:text-titan-red transition-all duration-300 hover:-translate-y-0.5 flex items-center justify-center gap-3 group/btn bg-white">
                                    {{ __('Apply Now') }}
                                    <x-lucide-arrow-right
                                        class="w-4 h-4 group-hover/btn:translate-x-1 transition-transform" />
                                </a>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Empty State -->
                <div x-show="filteredJobs.length === 0" style="display: none"
                    class="text-center py-20 border border-dashed border-gray-200 rounded">
                    <div class="w-16 h-16 bg-gray-50 rounded flex items-center justify-center mx-auto mb-5">
                        <x-lucide-search class="text-gray-200 w-7 h-7" />
                    </div>
                    <h3 class="text-lg font-bold text-titan-navy mb-2">{{ __('No positions found') }}</h3>
                    <p class="text-titan-navy/35 text-sm mb-6">{{ __('Try adjusting your search or filters.') }}</p>
                    <button
                        @click="clearFilters()"
                        class="bg-titan-navy text-white px-6 py-3 rounded font-bold text-xs uppercase tracking-widest hover:bg-titan-red transition-colors">
                        {{ __('Clear All Filters') }}
                    </button>
                </div>
            </div>

            <!-- CTA Banner -->
            <div class="mt-10 md:mt-16 rounded-xl md:rounded-2xl border border-gray-100 bg-gray-50 p-5 sm:p-8 md:p-14 text-titan-navy relative overflow-hidden shadow-sm">
                <div class="absolute inset-0 opacity-60 bg-[linear-gradient(135deg,rgba(11,43,92,0.03),transparent_55%)]">
                </div>
                <div class="absolute top-0 right-0 w-[300px] h-[300px] bg-titan-red/5 rounded-full blur-[100px] pointer-events-none">
                </div>
                <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-6 md:gap-8">
                    <div>
                        <h3 class="text-xl md:text-2xl font-bold uppercase tracking-normal mb-2">
                            {{ __("Don't see your perfect role?") }}
                        </h3>
                        <p class="text-titan-navy/45 text-sm">
                            {{ __('Send us your CV and we\'ll contact you for future opportunities.') }}
                        </p>
                    </div>
                    <button @click="isApplyOpen = true"
                        class="w-full md:w-auto shrink-0 border-2 border-titan-navy/15 bg-white text-titan-navy px-6 md:px-8 py-4 rounded-full font-bold text-xs uppercase tracking-widest hover:border-titan-red hover:text-titan-red transition-all duration-300 shadow-sm hover:shadow-md flex items-center justify-center gap-3 group">
                        <span>{{ __('General Application') }}</span>
                        <x-lucide-send
                            class="w-4 h-4 group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-transform" />
                    </button>
                </div>
            </div>
        </section>

        <!-- APPLICATION MODAL -->
        <div x-show="isApplyOpen" style="display: none"
            class="fixed inset-0 z-[100] flex items-center justify-center p-3 sm:p-4">
            <div @click="isApplyOpen = false" class="absolute inset-0 bg-titan-navy/55 backdrop-blur-sm"></div>

            <div x-show="isApplyOpen" x-transition.scale.95.opacity
                class="relative w-full max-w-4xl bg-white rounded-xl md:rounded-2xl shadow-[0_24px_50px_-24px_rgba(15,23,42,0.22)] overflow-hidden max-h-[calc(100svh-1.5rem)] sm:max-h-[90vh] overflow-y-auto border border-gray-100 scrollbar-clean">
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-titan-navy to-titan-red"></div>
                <button @click="isApplyOpen = false"
                    class="absolute top-4 right-4 text-gray-400 hover:text-titan-red transition-colors bg-gray-50 rounded-full p-2 z-20">
                    <x-lucide-x class="w-5 h-5" />
                </button>

                <div class="grid grid-cols-1 lg:grid-cols-[0.9fr_1.1fr]">
                    <aside class="bg-gray-50 text-titan-navy p-5 sm:p-8 md:p-10 lg:p-12 relative overflow-hidden border-b lg:border-b-0 lg:border-r border-gray-100">
                        <div class="absolute -top-24 -right-16 w-64 h-64 rounded-full bg-titan-red/5 blur-3xl"></div>
                        <div class="relative z-10">
                            <div class="inline-flex items-center gap-2 rounded-full border border-titan-red/10 bg-titan-red/5 px-3 py-1 text-[10px] font-bold uppercase tracking-[0.25em] text-titan-red mb-6">
                                {{ __('General Application') }}
                            </div>
                            <h3 class="text-2xl md:text-4xl font-bold uppercase tracking-normal leading-tight">
                                {{ __('Ready to join our team?') }}
                            </h3>
                            <p class="mt-4 text-titan-navy/55 leading-relaxed">
                                {{ __('Send your details once and we will review them for current and future opportunities.') }}
                            </p>

                            <div class="mt-6 md:mt-8 space-y-3">
                                <div class="flex items-start gap-3 rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
                                    <div class="mt-0.5 w-8 h-8 rounded-full bg-titan-red/5 flex items-center justify-center shrink-0">
                                        <x-lucide-clock class="w-4 h-4 text-titan-red" />
                                    </div>
                                    <div>
                                        <div class="font-bold">{{ __('Fast review') }}</div>
                                        <div class="text-sm text-titan-navy/45">{{ __('Applications are checked by our recruitment team.') }}</div>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3 rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
                                    <div class="mt-0.5 w-8 h-8 rounded-full bg-titan-red/5 flex items-center justify-center shrink-0">
                                        <x-lucide-file-up class="w-4 h-4 text-titan-red" />
                                    </div>
                                    <div>
                                        <div class="font-bold">{{ __('Simple upload') }}</div>
                                        <div class="text-sm text-titan-navy/45">{{ __('Attach your CV in PDF, DOC, or DOCX format.') }}</div>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3 rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
                                    <div class="mt-0.5 w-8 h-8 rounded-full bg-titan-red/5 flex items-center justify-center shrink-0">
                                        <x-lucide-send class="w-4 h-4 text-titan-red" />
                                    </div>
                                    <div>
                                        <div class="font-bold">{{ __('Direct route') }}</div>
                                        <div class="text-sm text-titan-navy/45">{{ __('Use this form for general applications and open roles.') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </aside>

                    <div class="p-5 sm:p-6 md:p-10 lg:p-12 bg-white">
                        <div class="flex items-center gap-4 mb-8">
                            <div class="w-1 h-8 bg-titan-red rounded-full"></div>
                            <div>
                                <h3 class="text-xl font-bold text-titan-navy uppercase tracking-tight">
                                    {{ __('General Application') }}
                                </h3>
                                <p class="text-titan-navy/35 text-xs mt-0.5">
                                    {{ __('Ready to join our team? Fill out the form below.') }}
                                </p>
                            </div>
                        </div>

                        @if(session('success'))
                            <div class="bg-green-50 text-green-700 p-4 rounded mb-6 text-sm font-semibold border border-green-100 flex items-center gap-2"
                                x-init="isApplyOpen = true">
                                <x-lucide-check-circle class="w-4 h-4 text-green-500 shrink-0" />
                                {{ session('success') }}
                            </div>
                        @endif

                        <form action="{{ route('careers.apply') }}" method="POST" enctype="multipart/form-data"
                            class="space-y-5">
                            @csrf

                            <!-- Honeypot Field (Hidden from humans) -->
                            <div class="hidden" aria-hidden="true">
                                <input type="text" name="website_url" tabindex="-1" autocomplete="off" />
                            </div>

                            <input type="hidden" name="job_id" value="general-application">

                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-titan-navy/35 mb-2 ml-1">{{ __('Full Name') }}
                                    <span class="text-titan-red">*</span></label>
                                <input type="text" name="full_name" value="{{ old('full_name') }}" required
                                    class="form-field w-full px-3.5 py-2.5 text-[13px] font-semibold @error('full_name') border-titan-red @enderror"
                                    placeholder="{{ __('Your full name') }}" />
                                @error('full_name') <p
                                    class="text-[10px] text-titan-red font-bold uppercase tracking-widest mt-1 ml-1">
                                    {{ $message }}
                                </p> @enderror
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div class="space-y-2">
                                    <label class="block text-xs font-bold text-titan-navy/35 mb-2 ml-1">{{ __('Email') }}
                                        <span class="text-titan-red">*</span></label>
                                    <input type="email" name="email" value="{{ old('email') }}" required
                                        class="form-field w-full px-3.5 py-2.5 text-[13px] font-semibold @error('email') border-titan-red @enderror"
                                        placeholder="{{ __('Your email address') }}" />
                                    @error('email') <p
                                        class="text-[10px] text-titan-red font-bold uppercase tracking-widest mt-1 ml-1">
                                        {{ $message }}
                                    </p> @enderror
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-xs font-bold text-titan-navy/35 mb-2 ml-1">{{ __('Phone') }}
                                        <span class="text-titan-red">*</span></label>
                                    <input type="tel" name="phone" value="{{ old('phone') }}" required
                                        class="form-field w-full px-3.5 py-2.5 text-[13px] font-semibold @error('phone') border-titan-red @enderror"
                                        placeholder="{{ __('Your phone number') }}" />
                                    @error('phone') <p
                                        class="text-[10px] text-titan-red font-bold uppercase tracking-widest mt-1 ml-1">
                                        {{ $message }}
                                    </p> @enderror
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label
                                    class="block text-xs font-bold text-titan-navy/35 mb-2 ml-1">{{ __('Cover Letter / Message') }}</label>
                                <textarea name="message" rows="3"
                                    class="form-field w-full px-3.5 py-2.5 text-[13px] font-semibold rounded-2xl resize-none"
                                    placeholder="{{ __('Write a short cover letter or note...') }}">{{ old('message') }}</textarea>
                            </div>

                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-titan-navy/35 mb-2 ml-1">{{ __('Resume / CV') }}
                                    <span class="text-titan-red">*</span></label>
                                <div class="border-2 border-dashed border-gray-200 rounded-2xl p-5 text-center bg-white hover:bg-gray-50/70 hover:border-gray-300 transition-all cursor-pointer relative @error('resume') border-titan-red @enderror"
                                    x-data="{ fileName: '' }">
                                    <input type="file" name="resume" required
                                        class="absolute inset-0 opacity-0 cursor-pointer z-10" accept=".pdf,.doc,.docx"
                                        @change="fileName = $event.target.files[0]?.name || ''" />
                                    <template x-if="!fileName">
                                        <div class="space-y-2">
                                            <div class="mx-auto w-10 h-10 rounded-full bg-titan-red/5 flex items-center justify-center">
                                                <x-lucide-upload class="text-titan-red w-4 h-4" />
                                            </div>
                                            <p class="text-[13px] font-bold text-titan-navy">
                                                {{ __('Click to Upload or Drag & Drop') }}
                                            </p>
                                            <p class="text-[11px] text-titan-navy/35">{{ __('PDF, DOCX up to 10MB') }}</p>
                                        </div>
                                    </template>
                                    <template x-if="fileName">
                                        <div class="space-y-2">
                                            <div class="mx-auto w-10 h-10 rounded-full bg-green-50 flex items-center justify-center">
                                                <x-lucide-file-text class="text-green-600 w-4 h-4" />
                                            </div>
                                            <p class="text-[13px] font-bold text-titan-navy break-all" x-text="fileName"></p>
                                            <p class="text-[11px] text-green-600 font-bold">{{ __('File Selected') }}</p>
                                        </div>
                                    </template>
                                </div>
                                @error('resume') <p
                                    class="text-[10px] text-titan-red font-bold uppercase tracking-widest mt-1 ml-1">
                                    {{ $message }}
                                </p> @enderror
                            </div>

                            <div class="flex flex-col sm:flex-row sm:items-center gap-3 pt-2">
                                <p class="text-[10px] text-titan-navy/30 sm:flex-1">
                                    {{ __('All fields marked with * are required') }}
                                </p>
                                <button type="submit"
                                    class="w-full sm:w-auto bg-titan-red hover:bg-titan-navy text-white font-bold text-[11px] uppercase tracking-widest py-3 px-6 rounded-full transition-all shadow-sm hover:shadow-md flex items-center justify-center gap-2 group">
                                    {{ __('Submit Application') }}
                                    <x-lucide-arrow-right class="w-4 h-4 group-hover:translate-x-1 transition-transform" />
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
</x-layouts.app>
