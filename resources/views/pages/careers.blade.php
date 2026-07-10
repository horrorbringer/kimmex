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

        <!-- HERO -->
        <section class="relative h-[480px] md:h-[560px] flex items-center overflow-hidden bg-titan-navy">
            <div class="absolute inset-0">
                <img src="/images/webp/projects/Thumbnail-5.webp" alt="{{ __('Careers') }}" class="w-full h-full object-cover opacity-60" loading="eager" decoding="async" fetchpriority="high" />
                <div class="absolute inset-0 bg-gradient-to-r from-titan-navy/80 via-titan-navy/50 to-transparent"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-titan-navy/70 via-transparent to-transparent"></div>
            </div>

            <div class="relative z-20 w-full max-w-[1200px] mx-auto px-6 pt-24 pb-12" x-data="{ shown: false }" x-init="setTimeout(() => shown = true, 100)">
                <div :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                     class="transition-all duration-700 delay-100 inline-flex items-center gap-2 mb-5">
                    <div class="w-6 h-[2px] bg-titan-red"></div>
                    <span class="text-[10px] font-bold uppercase tracking-[0.3em] text-white/50">{{ __('Careers') }}</span>
                </div>

                <h1 :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                    class="transition-all duration-700 delay-200 font-heading font-black text-white uppercase leading-none mb-4 drop-shadow-lg"
                    style="font-size: clamp(1.6rem, 4vw, 2.8rem) !important; color: white !important; font-weight: 900 !important;">
                    {{ __('Build Your') }} <span class="text-titan-red">{{ __('Legacy') }}</span>
                </h1>

                <p :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                   class="transition-all duration-700 delay-300 text-white/60 text-sm max-w-md mb-8 leading-relaxed">
                    {{ __('Join a team of builders shaping Cambodia\'s infrastructure future.') }}
                </p>

                <div :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                     class="transition-all duration-700 delay-400 flex flex-wrap gap-3">
                    <button @click="document.getElementById('openings')?.scrollIntoView({ behavior: 'smooth' })"
                        class="inline-flex items-center gap-2 h-10 px-5 rounded bg-titan-red text-white text-[10px] font-black uppercase tracking-[0.2em] hover:bg-white hover:text-titan-navy transition-all duration-300">
                        {{ __('Explore Roles') }}
                        <x-lucide-arrow-down class="w-3.5 h-3.5" />
                    </button>
                    <button @click="isApplyOpen = true"
                        class="inline-flex items-center gap-2 h-10 px-5 rounded border border-white/20 text-white text-[10px] font-black uppercase tracking-[0.2em] hover:bg-white/10 transition-all duration-300">
                        {{ __('Direct Apply') }}
                        <x-lucide-send class="w-3.5 h-3.5" />
                    </button>
                </div>

                <div :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                     class="transition-all duration-700 delay-500 hidden md:flex gap-6 mt-10 pt-8 border-t border-white/10">
                    @foreach([['Fast response', 'Reviewed by our team within days.'], ['Clear process', 'Know every step before you apply.'], ['Direct entry', 'Apply to a role or send a general CV.']] as $badge)
                    <div class="flex items-start gap-3">
                        <div class="w-1 h-8 bg-titan-red/60 rounded-full shrink-0 mt-0.5"></div>
                        <div>
                            <div class="text-[9px] font-black uppercase tracking-[0.25em] text-white/40 mb-0.5">{{ __($badge[0]) }}</div>
                            <div class="text-[11px] text-white/70 font-medium">{{ __($badge[1]) }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- WHY JOIN US + HIRING PROCESS — combined light band -->
        <section class="bg-gray-50 border-y border-gray-100">
            {{-- Why join us --}}
            <div class="max-w-[1200px] mx-auto px-6 py-12 md:py-16 border-b border-gray-100">
                <p class="text-[9px] font-black uppercase tracking-[0.35em] text-titan-red mb-6">{{ __('Why Work With Us') }}</p>
                <div class="grid grid-cols-1 md:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-gray-200">
                    @php
                        $values = [
                            ['icon' => 'award',  'title' => __('Excellence'), 'desc' => __('Highest standards across all large-scale construction and engineering projects.')],
                            ['icon' => 'target', 'title' => __('Impact'),     'desc' => __('Work that contributes directly to Cambodia\'s infrastructure growth.')],
                            ['icon' => 'users',  'title' => __('Growth'),     'desc' => __('Unmatched professional development within our global-standard teams.')],
                        ];
                    @endphp
                    @foreach($values as $i => $v)
                    <div class="px-0 md:px-8 py-6 md:py-0 {{ $i === 0 ? 'md:pl-0' : '' }} {{ $i === count($values)-1 ? 'md:pr-0' : '' }}">
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-7 h-7 rounded bg-titan-red/8 flex items-center justify-center shrink-0">
                                <x-dynamic-component :component="'lucide-' . $v['icon']" class="w-3.5 h-3.5 text-titan-red" stroke-width="1.5" />
                            </div>
                            <div class="text-[11px] font-black uppercase tracking-[0.2em] text-titan-navy">{{ $v['title'] }}</div>
                        </div>
                        <p class="text-[11px] text-titan-navy/50 leading-relaxed">{{ $v['desc'] }}</p>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Hiring steps --}}
            <div class="max-w-[1200px] mx-auto px-6 py-10 md:py-12">
                <p class="text-[9px] font-black uppercase tracking-[0.35em] text-titan-red mb-8">{{ __('How We Hire') }}</p>
                @php
                    $steps = [
                        ['num' => '01', 'title' => __('Apply'),     'desc' => __('Submit your CV via our portal.')],
                        ['num' => '02', 'title' => __('Screening'), 'desc' => __('Quick HR conversation.')],
                        ['num' => '03', 'title' => __('Interview'), 'desc' => __('Technical deep-dive.')],
                        ['num' => '04', 'title' => __('Offer'),     'desc' => __('Join the team.')],
                    ];
                @endphp
                <div class="flex flex-col md:flex-row gap-0">
                    @foreach($steps as $i => $step)
                    <div class="flex-1 flex md:flex-col gap-4 md:gap-3 relative pb-6 md:pb-0 md:pr-6
                        {{ $i < count($steps) - 1 ? 'md:border-r border-gray-200' : '' }}
                        {{ $i > 0 ? 'md:pl-6' : '' }}">
                        {{-- mobile left line --}}
                        @if($i < count($steps) - 1)
                        <div class="md:hidden absolute left-[11px] top-10 bottom-0 w-px bg-gray-200"></div>
                        @endif
                        <div class="w-[22px] h-[22px] rounded-full border-2 border-titan-red/40 bg-white flex items-center justify-center shrink-0 relative z-10">
                            <span class="text-[7px] font-black text-titan-red">{{ $step['num'] }}</span>
                        </div>
                        <div>
                            <div class="text-[11px] font-black text-titan-navy uppercase tracking-[0.15em] mb-1">{{ $step['title'] }}</div>
                            <div class="text-[10px] text-titan-navy/40 leading-relaxed">{{ $step['desc'] }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- JOB LISTINGS -->
        <section id="openings" x-data="{ revealed: false }" x-intersect.once="revealed = true"
            class="scroll-mt-24 py-14 md:py-20 max-w-[1200px] mx-auto px-6">

            <!-- Header -->
            <!-- Header -->
            <div :class="revealed ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                class="transition-all duration-600 mb-8">
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-2 mb-4">
                    <div>
                        <p class="text-[9px] font-black uppercase tracking-[0.35em] text-titan-red mb-1">{{ __('Open Positions') }}</p>
                        <h2 class="text-xl md:text-2xl font-black text-titan-navy uppercase tracking-tight leading-none">{{ __('Current Openings') }}</h2>
                    </div>
                    <span class="text-[10px] font-bold text-titan-navy/30 uppercase tracking-[0.2em]">
                        <span x-text="filteredJobs.length"></span> {{ __('roles available') }}
                    </span>
                </div>

                {{-- Search + reset --}}
                <div class="flex gap-2 mb-4">
                    <div class="relative flex-grow">
                        <x-lucide-search class="absolute left-3 top-1/2 -translate-y-1/2 text-titan-navy/25 w-3.5 h-3.5" />
                        <input type="text"
                            placeholder="{{ __('Search by role name…') }}"
                            x-model="searchQuery"
                            class="w-full pl-9 pr-3 h-10 rounded border border-gray-200 bg-white text-[12px] font-semibold text-titan-navy focus:outline-none focus:border-titan-red/40 focus:ring-1 focus:ring-titan-red/10 transition-all placeholder:text-titan-navy/20" />
                    </div>
                    <button @click="clearFilters()"
                        x-show="filterDept !== '{{ __('All Departments') }}' || filterLoc !== '{{ __('All Locations') }}' || searchQuery !== ''"
                        style="display:none"
                        class="h-10 px-3 rounded border border-gray-200 text-[9px] font-black uppercase tracking-[0.15em] text-titan-navy/50 hover:text-titan-red hover:border-titan-red/30 transition-colors flex items-center gap-1.5 whitespace-nowrap">
                        <x-lucide-x class="w-3 h-3" />{{ __('Clear') }}
                    </button>
                </div>

                {{-- Department pill tabs --}}
                <div class="flex flex-wrap gap-1.5 relative z-40">
                    @foreach($categories as $cat)
                    <button @click="filterDept = '{{ addslashes($cat) }}'"
                        class="h-7 px-3 rounded-full text-[9px] font-black uppercase tracking-[0.12em] border transition-all duration-200"
                        :class="filterDept === '{{ addslashes($cat) }}'
                            ? 'bg-titan-navy text-white border-titan-navy'
                            : 'bg-white text-titan-navy/50 border-gray-200 hover:border-titan-navy/30 hover:text-titan-navy'">
                        {{ $cat }}
                    </button>
                    @endforeach

                    {{-- Location compact dropdown --}}
                    <div class="relative ml-auto" x-data="{ open: false }">
                        <button @click="open = !open"
                            class="h-7 px-3 rounded-full border text-[9px] font-black uppercase tracking-[0.12em] flex items-center gap-1.5 transition-all"
                            :class="filterLoc !== '{{ __('All Locations') }}'
                                ? 'bg-titan-red text-white border-titan-red'
                                : 'bg-white text-titan-navy/50 border-gray-200 hover:border-titan-navy/30 hover:text-titan-navy'">
                            <x-lucide-map-pin class="w-3 h-3 shrink-0" />
                            <span x-text="filterLoc === '{{ __('All Locations') }}' ? '{{ __('Location') }}' : filterLoc" class="max-w-[100px] truncate"></span>
                            <x-lucide-chevron-down class="w-2.5 h-2.5 shrink-0 transition-transform" ::class="open ? 'rotate-180' : ''" />
                        </button>
                        <div x-show="open" @click.away="open = false" style="display:none"
                            class="absolute top-full right-0 mt-1 bg-white border border-gray-100 rounded shadow-lg py-1 z-50 min-w-[140px]">
                            @foreach($locations as $loc)
                            <button @click="filterLoc = '{{ addslashes($loc) }}'; open = false"
                                class="w-full text-left px-3 py-2 text-[11px] font-bold hover:bg-gray-50 flex items-center justify-between transition-colors"
                                :class="filterLoc === '{{ addslashes($loc) }}' ? 'text-titan-red' : 'text-titan-navy/60'">
                                <span>{{ $loc }}</span>
                                <x-lucide-check x-show="filterLoc === '{{ addslashes($loc) }}'" class="text-titan-red w-3 h-3 shrink-0" />
                            </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Job Cards -->
            <div class="space-y-2">
                <template x-for="(job, index) in filteredJobs" :key="job.id">
                    <article class="group bg-white border border-gray-100 rounded-lg overflow-hidden transition-all duration-200 hover:border-titan-red/20 hover:shadow-[0_4px_20px_-6px_rgba(11,43,92,0.12)]">
                        <a :href="'/careers/' + job.slug" class="flex flex-col md:flex-row md:items-center gap-0">
                            {{-- Left accent --}}
                            <div class="hidden md:block w-1 self-stretch bg-gray-100 group-hover:bg-titan-red transition-colors duration-200 shrink-0"></div>

                            {{-- Main content --}}
                            <div class="flex-1 px-5 py-4 min-w-0">
                                <div class="flex flex-wrap items-center gap-2 mb-1.5">
                                    <template x-for="tag in job.tags">
                                        <span class="text-[8px] font-black uppercase tracking-[0.15em] text-titan-red bg-titan-red/5 px-2 py-0.5 rounded-full" x-text="tag"></span>
                                    </template>
                                    <span class="text-[9px] font-bold text-titan-navy/25 uppercase tracking-[0.1em]" x-text="job.type"></span>
                                </div>

                                <div class="careers-job-title text-[13px] font-black text-titan-navy group-hover:text-titan-red transition-colors leading-tight mb-2" x-text="job.title"></div>

                                <div class="flex flex-wrap items-center gap-x-4 gap-y-1">
                                    <span class="flex items-center gap-1 text-[10px] text-titan-navy/40 font-medium">
                                        <x-lucide-map-pin class="w-3 h-3 text-titan-navy/20 shrink-0" />
                                        <span x-text="job.loc"></span>
                                    </span>
                                    <span class="flex items-center gap-1 text-[10px] text-titan-navy/40 font-medium">
                                        <x-lucide-briefcase class="w-3 h-3 text-titan-navy/20 shrink-0" />
                                        <span x-text="job.experience"></span>
                                    </span>
                                    <span class="flex items-center gap-1 text-[10px] text-titan-navy/40 font-medium">
                                        <x-lucide-dollar-sign class="w-3 h-3 text-titan-navy/20 shrink-0" />
                                        <span x-text="job.salary"></span>
                                    </span>
                                    <span class="hidden sm:flex items-center gap-1 text-[10px] text-titan-navy/30 font-medium">
                                        <x-lucide-calendar class="w-3 h-3 shrink-0" />
                                        <span x-text="job.postedDate"></span>
                                    </span>
                                </div>
                            </div>

                            {{-- CTA --}}
                            <div class="px-4 py-3 md:py-0 md:pr-5 shrink-0 flex items-center border-t border-gray-50 md:border-t-0">
                                <span class="inline-flex items-center gap-1.5 h-8 px-4 rounded-full bg-gray-50 group-hover:bg-titan-red group-hover:text-white text-titan-navy/50 text-[9px] font-black uppercase tracking-[0.15em] transition-all duration-200">
                                    {{ __('Apply') }}
                                    <x-lucide-arrow-right class="w-3 h-3 group-hover:translate-x-0.5 transition-transform" />
                                </span>
                            </div>
                        </a>
                    </article>
                </template>

                <!-- Empty State -->
                <div x-show="filteredJobs.length === 0" style="display: none"
                    class="text-center py-16 border border-dashed border-gray-200 rounded-lg bg-gray-50/50">
                    <x-lucide-search class="w-8 h-8 text-gray-200 mx-auto mb-3" />
                    <p class="text-sm font-black text-titan-navy/30 uppercase tracking-widest mb-4">{{ __('No roles found') }}</p>
                    <button @click="clearFilters()"
                        class="h-8 px-4 rounded-full bg-titan-navy text-white text-[9px] font-black uppercase tracking-[0.18em] hover:bg-titan-red transition-colors">
                        {{ __('Clear Filters') }}
                    </button>
                </div>
            </div>

            <!-- CTA Banner -->
            <!-- CTA Banner -->
            <div class="mt-8 rounded border border-gray-100 bg-gray-50 p-5 md:p-8 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-48 h-48 bg-titan-red/5 rounded-full blur-3xl pointer-events-none"></div>
                <div class="relative flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                    <div>
                        <h3 class="careers-job-title text-sm font-black text-titan-navy uppercase tracking-tight mb-1">{{ __("Don't see your perfect role?") }}</h3>
                        <p class="text-[11px] text-titan-navy/45 leading-relaxed">{{ __('Send us your CV and we\'ll contact you for future opportunities.') }}</p>
                    </div>
                    <button @click="isApplyOpen = true"
                        class="shrink-0 w-full md:w-auto inline-flex items-center justify-center gap-2 h-9 px-5 rounded border border-titan-navy/15 bg-white text-titan-navy text-[9px] font-black uppercase tracking-[0.2em] hover:border-titan-red hover:text-titan-red transition-all duration-300">
                        {{ __('General Application') }}
                        <x-lucide-send class="w-3.5 h-3.5" />
                    </button>
                </div>
            </div>
        </section>

        <!-- APPLICATION MODAL -->
        <div x-show="isApplyOpen" style="display: none"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center sm:p-4">
            <div @click="isApplyOpen = false" class="absolute inset-0 bg-titan-navy/60 backdrop-blur-sm"></div>

            <div x-show="isApplyOpen"
                x-transition:enter="transition ease-out duration-250"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                class="relative w-full sm:max-w-lg bg-white sm:rounded-xl shadow-2xl overflow-hidden max-h-[92svh] overflow-y-auto scrollbar-clean border border-gray-100">

                {{-- Top header bar --}}
                <div class="bg-titan-navy px-5 py-4 flex items-center justify-between shrink-0">
                    <div>
                        <p class="text-[8px] font-black uppercase tracking-[0.3em] text-white/40 mb-0.5">{{ __('Kimmex') }}</p>
                        <h3 class="careers-job-title text-sm font-black text-white uppercase tracking-tight">{{ __('General Application') }}</h3>
                    </div>
                    <button @click="isApplyOpen = false"
                        class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white/60 hover:text-white transition-all">
                        <x-lucide-x class="w-4 h-4" />
                    </button>
                </div>

                {{-- Form body --}}
                <div class="p-5 sm:p-6">

                    @if(session('success'))
                        <div class="flex items-center gap-2.5 bg-green-50 border border-green-100 text-green-700 rounded-lg p-3 mb-5 text-[11px] font-semibold" x-init="isApplyOpen = true">
                            <x-lucide-check-circle class="w-4 h-4 text-green-500 shrink-0" />
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('careers.apply') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div class="hidden" aria-hidden="true">
                            <input type="text" name="website_url" tabindex="-1" autocomplete="off" />
                        </div>
                        <input type="hidden" name="job_id" value="general-application">

                        {{-- Full name --}}
                        <div>
                            <label class="block text-[10px] font-black text-titan-navy/40 uppercase tracking-[0.15em] mb-1.5">
                                {{ __('Full Name') }} <span class="text-titan-red">*</span>
                            </label>
                            <input type="text" name="full_name" value="{{ old('full_name') }}" required
                                class="w-full h-10 px-3 rounded-lg border border-gray-200 bg-gray-50 text-[12px] font-semibold text-titan-navy placeholder:text-titan-navy/20 focus:outline-none focus:border-titan-red/40 focus:bg-white focus:ring-1 focus:ring-titan-red/10 transition-all @error('full_name') border-titan-red bg-red-50 @enderror"
                                placeholder="{{ __('e.g. CHAN Sopheap') }}" />
                            @error('full_name')<p class="text-[9px] text-titan-red font-bold mt-1">{{ $message }}</p>@enderror
                        </div>

                        {{-- Email + Phone --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[10px] font-black text-titan-navy/40 uppercase tracking-[0.15em] mb-1.5">
                                    {{ __('Email') }} <span class="text-titan-red">*</span>
                                </label>
                                <input type="email" name="email" value="{{ old('email') }}" required
                                    class="w-full h-10 px-3 rounded-lg border border-gray-200 bg-gray-50 text-[12px] font-semibold text-titan-navy placeholder:text-titan-navy/20 focus:outline-none focus:border-titan-red/40 focus:bg-white focus:ring-1 focus:ring-titan-red/10 transition-all @error('email') border-titan-red bg-red-50 @enderror"
                                    placeholder="you@example.com" />
                                @error('email')<p class="text-[9px] text-titan-red font-bold mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-titan-navy/40 uppercase tracking-[0.15em] mb-1.5">
                                    {{ __('Phone') }} <span class="text-titan-red">*</span>
                                </label>
                                <div x-data="{ phoneVal: '{{ old('phone') }}', phoneError: '' }">
                                    <input type="tel" name="phone" required
                                        inputmode="tel"
                                        x-model="phoneVal"
                                        @blur="phoneError = phoneVal && !/^\+?[\d\s\-(). ]{7,25}$/.test(phoneVal.trim()) ? '{{ __('Enter a valid number, e.g. +855 12 345 678') }}' : ''"
                                        @input="if(phoneError) phoneError = ''"
                                        :class="phoneError ? 'border-titan-red bg-red-50' : 'border-gray-200 bg-gray-50 focus:border-titan-red/40 focus:bg-white'"
                                        class="w-full h-10 px-3 rounded-lg border text-[12px] font-semibold text-titan-navy placeholder:text-titan-navy/20 focus:outline-none focus:ring-1 focus:ring-titan-red/10 transition-all @error('phone') border-titan-red bg-red-50 @enderror"
                                        placeholder="+855 12 345 678" />
                                    <p x-show="phoneError" x-text="phoneError" class="text-[9px] text-titan-red font-bold mt-1" style="display:none"></p>
                                    @error('phone')<p class="text-[9px] text-titan-red font-bold mt-1">{{ $message }}</p>@enderror
                                </div>
                            </div>
                        </div>

                        {{-- Message --}}
                        <div>
                            <label class="block text-[10px] font-black text-titan-navy/40 uppercase tracking-[0.15em] mb-1.5">
                                {{ __('Cover Letter') }} <span class="text-titan-navy/20 font-medium normal-case tracking-normal text-[10px]">{{ __('(optional)') }}</span>
                            </label>
                            <textarea name="message" rows="3"
                                class="w-full px-3 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-[12px] font-semibold text-titan-navy placeholder:text-titan-navy/20 focus:outline-none focus:border-titan-red/40 focus:bg-white focus:ring-1 focus:ring-titan-red/10 transition-all resize-none"
                                placeholder="{{ __('Brief introduction or note to the hiring team…') }}">{{ old('message') }}</textarea>
                        </div>

                        {{-- CV Upload --}}
                        <div x-data="{ fileName: '', dragging: false }">
                            <label class="block text-[10px] font-black text-titan-navy/40 uppercase tracking-[0.15em] mb-1.5">
                                {{ __('Resume / CV') }} <span class="text-titan-red">*</span>
                            </label>
                            <div class="relative rounded-lg border-2 border-dashed transition-all duration-200 cursor-pointer overflow-hidden"
                                :class="fileName ? 'border-green-300 bg-green-50' : dragging ? 'border-titan-red/40 bg-titan-red/5' : 'border-gray-200 bg-gray-50 hover:border-gray-300 hover:bg-white'"
                                @dragover.prevent="dragging = true"
                                @dragleave="dragging = false"
                                @drop.prevent="dragging = false; fileName = $event.dataTransfer.files[0]?.name || ''">
                                <input type="file" name="resume" required accept=".pdf,.doc,.docx"
                                    class="absolute inset-0 opacity-0 cursor-pointer z-10"
                                    @change="fileName = $event.target.files[0]?.name || ''" />
                                <div class="flex items-center gap-3 p-3.5">
                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0 transition-colors"
                                        :class="fileName ? 'bg-green-100' : 'bg-white border border-gray-200'">
                                        <template x-if="!fileName">
                                            <x-lucide-upload class="w-4 h-4 text-titan-navy/30" />
                                        </template>
                                        <template x-if="fileName">
                                            <x-lucide-file-check class="w-4 h-4 text-green-600" />
                                        </template>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <template x-if="!fileName">
                                            <div>
                                                <p class="text-[12px] font-bold text-titan-navy">{{ __('Drop your CV here or click to browse') }}</p>
                                                <p class="text-[10px] text-titan-navy/35 mt-0.5">{{ __('PDF, DOC, DOCX — max 10 MB') }}</p>
                                            </div>
                                        </template>
                                        <template x-if="fileName">
                                            <div>
                                                <p class="text-[12px] font-bold text-green-700 truncate" x-text="fileName"></p>
                                                <p class="text-[10px] text-green-500 mt-0.5">{{ __('Ready to submit') }}</p>
                                            </div>
                                        </template>
                                    </div>
                                    <template x-if="fileName">
                                        <button type="button" @click.stop="fileName = ''" class="shrink-0 w-6 h-6 rounded-full bg-white border border-gray-200 flex items-center justify-center hover:border-titan-red hover:text-titan-red text-titan-navy/30 transition-colors z-20 relative">
                                            <x-lucide-x class="w-3 h-3" />
                                        </button>
                                    </template>
                                </div>
                            </div>
                            @error('resume')<p class="text-[9px] text-titan-red font-bold mt-1">{{ $message }}</p>@enderror
                        </div>

                        {{-- Submit row --}}
                        <div class="flex items-center justify-between gap-3 pt-1 border-t border-gray-100">
                            <p class="text-[9px] text-titan-navy/25">* {{ __('required fields') }}</p>
                            <button type="submit"
                                class="inline-flex items-center gap-2 h-9 px-5 rounded-lg bg-titan-red hover:bg-titan-navy text-white font-black text-[9px] uppercase tracking-[0.2em] transition-all duration-200 group">
                                {{ __('Submit') }}
                                <x-lucide-arrow-right class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform" />
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
</x-layouts.app>
