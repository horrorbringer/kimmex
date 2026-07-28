<x-layouts.app title="{{ __('Careers') }}" description="{{ __('Join the Kimmex team and build your future in the construction industry.') }}">

    @push('head')
    <script type="application/ld+json">
    {!! json_encode(['@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => [
        ['@type' => 'ListItem', 'position' => 1, 'name' => __('Home'), 'item' => url('/')],
        ['@type' => 'ListItem', 'position' => 2, 'name' => __('Careers'), 'item' => url('/careers')],
    ]], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
    @endpush

    @php
        $jobs = \Illuminate\Support\Facades\Cache::remember('careers_jobs_data_'.app()->getLocale(), now()->addHours(12), function() {
            $jobsDb = \App\Models\JobPosting::where('status', \App\Enums\JobPostingStatus::OPEN)
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

        $categories = array_values(array_unique(array_merge([__('All')], array_column($jobs, 'dept'))));
        $locations = array_values(array_unique(array_merge([__('All Locations')], array_column($jobs, 'loc'))));

        if (empty($jobs)) {
            $jobs = [
                ['id' => 'gen', 'slug' => 'gen', 'title' => __('Visionary Talent'), 'dept' => __('General'), 'loc' => __('Phnom Penh'), 'type' => __('Full-time'), 'salary' => __('Competitive'), 'experience' => __('Mixed'), 'postedDate' => now()->format('M d, Y'), 'tags' => [__('Hiring')], 'summary' => __('We are always looking for exceptional engineers and managers.')]
            ];
        }
    @endphp


    <div x-data="{
        filterDept: '{{ __('All') }}',
        filterLoc: '{{ __('All Locations') }}',
        searchQuery: '',
        isApplyOpen: false,
        jobs: @js($jobs),
        get filteredJobs() {
            return this.jobs.filter(job => {
                if (this.filterDept !== '{{ __('All') }}' && job.dept !== this.filterDept) return false;
                if (this.filterLoc !== '{{ __('All Locations') }}' && job.loc !== this.filterLoc) return false;
                if (this.searchQuery && !job.title.toLowerCase().includes(this.searchQuery.toLowerCase())) return false;
                return true;
            });
        },
        clearFilters() {
            this.filterDept = '{{ __('All') }}';
            this.filterLoc = '{{ __('All Locations') }}';
            this.searchQuery = '';
        }
    }" class="bg-white min-h-screen flex flex-col">

        <!-- ═══ HERO ═══ -->
        <section class="order-1 relative h-[420px] md:h-[480px] flex items-end overflow-hidden" style="background: #0B2B5C;">
            <div class="absolute inset-0">
                <img src="/images/webp/projects/Thumbnail-5.webp" alt="{{ __('Careers') }}" class="w-full h-full object-cover opacity-40" loading="eager" decoding="async" fetchpriority="high" />
                <div class="absolute inset-0 bg-gradient-to-t from-[#071A33]/95 via-[#0B2B5C]/50 to-transparent"></div>
                <div class="absolute inset-0 bg-gradient-to-r from-[#071A33]/60 via-transparent to-transparent"></div>
            </div>
            <div class="relative z-10 w-full max-w-[1280px] mx-auto px-6 pb-12 md:pb-16">
                <nav class="flex items-center gap-2 text-xs mb-5" style="color: rgba(255,255,255,0.5);">
                    <a href="/" class="hover:text-white transition-colors">{{ __('Home') }}</a>
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                    <span style="color: rgba(255,255,255,0.9);">{{ __('Careers') }}</span>
                </nav>
                <h1 class="font-heading font-[900] uppercase leading-[1] tracking-tight mb-4"
                    style="font-size: clamp(2rem, 5vw, 3.2rem); color: #FFFFFF;">
                    {{ __('Build Your') }} <span style="color: var(--primary-color, #E31E24);">{{ __('Future') }}</span>
                </h1>
                <p class="max-w-lg leading-relaxed mb-8" style="color: rgba(255,255,255,0.6); font-size: 1rem;">
                    {{ __('Join a team of builders shaping Cambodia\'s infrastructure. We\'re always looking for exceptional talent.') }}
                </p>
                <div class="flex flex-wrap gap-3">
                    <button @click="document.getElementById('openings')?.scrollIntoView({ behavior: 'smooth' })"
                        class="inline-flex items-center gap-2 px-6 py-3 rounded-xl text-sm font-bold transition-all duration-300 shadow-lg hover:shadow-xl hover:-translate-y-0.5"
                        style="background: var(--primary-color, #E31E24); color: #FFFFFF;">
                        {{ __('View Open Roles') }}
                        <x-lucide-arrow-down class="w-4 h-4" />
                    </button>
                    <button @click="isApplyOpen = true"
                        class="inline-flex items-center gap-2 px-6 py-3 rounded-xl text-sm font-bold transition-all duration-300"
                        style="border: 2px solid rgba(255,255,255,0.2); color: #FFFFFF;">
                        <x-lucide-send class="w-4 h-4" />
                        {{ __('General Application') }}
                    </button>
                </div>
            </div>
        </section>


        <!-- ═══ WHY JOIN US ═══ -->
        <section class="order-3 py-16 md:py-20 bg-white border-b border-gray-100">
            <div class="max-w-[1280px] mx-auto px-6">
                <div class="flex items-center gap-3 mb-10">
                    <div class="w-10 h-[2px]" style="background: var(--primary-color, #E31E24);"></div>
                    <span class="font-bold uppercase tracking-[0.2em] text-xs" style="color: var(--primary-color, #E31E24);">{{ __('Why Kimmex') }}</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @php
                        $perks = [
                            ['icon' => 'lucide-trophy', 'title' => __('Industry Leader'), 'desc' => __('Work on Cambodia\'s most significant infrastructure projects with 25+ years of proven track record.')],
                            ['icon' => 'lucide-trending-up', 'title' => __('Career Growth'), 'desc' => __('Clear advancement paths, mentorship programs, and continuous professional development opportunities.')],
                            ['icon' => 'lucide-shield-check', 'title' => __('Safety & Wellbeing'), 'desc' => __('Zero-incident policy, comprehensive insurance, and work-life balance that respects your time.')],
                        ];
                    @endphp
                    @foreach($perks as $i => $perk)
                        <div class="p-6 md:p-8 rounded-xl border border-gray-100 hover:border-gray-200 hover:shadow-lg transition-all duration-300 group">
                            <div class="w-11 h-11 rounded-xl flex items-center justify-center mb-5 transition-all duration-300 group-hover:shadow-md"
                                 style="background: color-mix(in srgb, var(--primary-color, #E31E24) 10%, transparent);">
                                <x-dynamic-component :component="$perk['icon']" class="w-5 h-5" style="color: var(--primary-color, #E31E24);" stroke-width="1.8" />
                            </div>
                            <h3 class="text-base font-bold text-gray-900 mb-2">{{ $perk['title'] }}</h3>
                            <p class="text-sm text-gray-500 leading-relaxed">{{ $perk['desc'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>


        <!-- ═══ HIRING PROCESS ═══ -->
        <section class="order-4 py-16 md:py-20 bg-gray-50 border-b border-gray-100">
            <div class="max-w-[1280px] mx-auto px-6">
                <div class="text-center mb-12">
                    <div class="flex items-center justify-center gap-3 mb-4">
                        <div class="w-8 h-[2px]" style="background: var(--primary-color, #E31E24);"></div>
                        <span class="font-bold uppercase tracking-[0.2em] text-xs" style="color: var(--primary-color, #E31E24);">{{ __('Process') }}</span>
                        <div class="w-8 h-[2px]" style="background: var(--primary-color, #E31E24);"></div>
                    </div>
                    <h2 class="text-2xl md:text-3xl font-heading font-black text-gray-900 tracking-tight">{{ __('What happens after you apply') }}</h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @php
                        $steps = [
                            ['num' => '01', 'icon' => 'lucide-send', 'title' => __('Apply'), 'desc' => __('Submit your CV via our portal or email.')],
                            ['num' => '02', 'icon' => 'lucide-phone', 'title' => __('Screening'), 'desc' => __('Quick HR call to understand your background.')],
                            ['num' => '03', 'icon' => 'lucide-users', 'title' => __('Interview'), 'desc' => __('Technical discussion with the team lead.')],
                            ['num' => '04', 'icon' => 'lucide-check-circle-2', 'title' => __('Offer'), 'desc' => __('Welcome aboard — join the Kimmex family.')],
                        ];
                    @endphp
                    @foreach($steps as $i => $step)
                        <div class="relative text-center group">
                            <div class="w-16 h-16 mx-auto rounded-full border-2 border-gray-200 bg-white flex items-center justify-center mb-5 group-hover:border-transparent group-hover:shadow-lg transition-all duration-300"
                                 style="--hover-bg: var(--primary-color, #E31E24);">
                                <x-dynamic-component :component="$step['icon']" class="w-6 h-6 text-gray-400 group-hover:text-titan-red transition-colors" stroke-width="1.5" />
                            </div>
                            <span class="text-[10px] font-bold text-gray-300 uppercase tracking-wider">{{ __('Step') }} {{ $step['num'] }}</span>
                            <h3 class="text-sm font-bold text-gray-900 mt-1 mb-2">{{ $step['title'] }}</h3>
                            <p class="text-xs text-gray-400 leading-relaxed max-w-[180px] mx-auto">{{ $step['desc'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>


        <!-- ═══ JOB LISTINGS ═══ -->
        <section id="openings" class="order-2 scroll-mt-24 py-16 md:py-24 bg-gray-50">
            <div class="max-w-[1280px] mx-auto px-6">

                <!-- Header -->
                <div class="mb-10">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-[2px]" style="background: var(--primary-color, #E31E24);"></div>
                        <span class="font-bold uppercase tracking-[0.2em] text-xs" style="color: var(--primary-color, #E31E24);">{{ __('Open Positions') }}</span>
                    </div>
                    <div class="flex items-end gap-6">
                        <h2 class="text-3xl md:text-4xl font-heading font-black text-gray-900 tracking-tight">{{ __('Join Our Team') }}</h2>
                        <div class="flex items-center gap-2 pb-1">
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background: color-mix(in srgb, var(--primary-color, #E31E24) 10%, transparent);">
                                <x-lucide-briefcase class="w-4 h-4" style="color: var(--primary-color, #E31E24);" />
                            </div>
                            <div>
                                <span class="text-xl font-black text-gray-900" x-text="filteredJobs.length"></span>
                                <span class="text-[9px] font-bold uppercase tracking-wider text-gray-400 ml-1">{{ __('Open Roles') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <ol class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-8" aria-label="{{ __('Apply in 3 simple steps') }}">
                    @foreach([
                        ['number' => '1', 'title' => __('Choose a role'), 'description' => __('Pick a job that fits your skills.')],
                        ['number' => '2', 'title' => __('Read the job details'), 'description' => __('Check the role before you apply.')],
                        ['number' => '3', 'title' => __('Send your CV'), 'description' => __('Complete the short form with your CV.')],
                    ] as $step)
                        <li class="flex items-center gap-3 rounded-xl border border-gray-100 bg-white px-4 py-3">
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-sm font-black text-white" style="background: var(--primary-color, #E31E24);">{{ $step['number'] }}</span>
                            <span>
                                <span class="block text-sm font-bold text-gray-900">{{ $step['title'] }}</span>
                                <span class="block text-xs text-gray-500 mt-0.5">{{ $step['description'] }}</span>
                            </span>
                        </li>
                    @endforeach
                </ol>

                <!-- Compact filters -->
                <div class="mb-6 grid grid-cols-1 items-center gap-2 rounded-xl border border-gray-200 bg-white p-3 shadow-sm md:grid-cols-[minmax(0,1fr)_11rem_11rem_auto]">
                    <label class="relative min-w-0 flex-1">
                        <span class="sr-only">{{ __('Search by job title...') }}</span>
                        <x-lucide-search class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
                        <input type="search" x-model="searchQuery"
                            placeholder="{{ __('Search jobs') }}"
                            class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50 pl-9 pr-3 text-sm text-gray-900 transition placeholder:text-gray-400 focus:border-titan-red focus:bg-white focus:outline-none focus:ring-1 focus:ring-titan-red/20" />
                    </label>

                    <label class="relative min-w-0">
                        <span class="sr-only">{{ __('Department') }}</span>
                        <x-lucide-briefcase class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
                        <select x-model="filterDept" class="h-10 w-full appearance-none rounded-lg border border-gray-200 bg-gray-50 pl-9 pr-8 text-sm font-medium text-gray-600 transition focus:border-titan-red focus:bg-white focus:outline-none focus:ring-1 focus:ring-titan-red/20">
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                        </select>
                        <x-lucide-chevron-down class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
                    </label>

                    <label class="relative min-w-0">
                        <span class="sr-only">{{ __('Location') }}</span>
                        <x-lucide-map-pin class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
                        <select x-model="filterLoc" class="h-10 w-full appearance-none rounded-lg border border-gray-200 bg-gray-50 pl-9 pr-8 text-sm font-medium text-gray-600 transition focus:border-titan-red focus:bg-white focus:outline-none focus:ring-1 focus:ring-titan-red/20">
                            @foreach($locations as $loc)
                                <option value="{{ $loc }}">{{ $loc }}</option>
                            @endforeach
                        </select>
                        <x-lucide-chevron-down class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
                    </label>

                    <button @click="clearFilters()"
                        x-show="filterDept !== '{{ __('All') }}' || filterLoc !== '{{ __('All Locations') }}' || searchQuery !== ''"
                        x-cloak
                        class="inline-flex h-10 shrink-0 items-center justify-center gap-1.5 rounded-lg px-3 text-xs font-bold text-gray-500 transition hover:bg-gray-100 hover:text-titan-navy">
                        <x-lucide-rotate-ccw class="h-3.5 w-3.5" /> {{ __('Reset') }}
                    </button>
                </div>

                <!-- Job Cards Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <template x-for="(job, index) in filteredJobs" :key="job.id">
                        <a :href="'/careers/' + job.slug"
                           class="group relative flex flex-col overflow-hidden rounded-2xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-titan-red/30 hover:shadow-[0_18px_42px_-18px_rgba(7,26,51,0.28)] focus:outline-none focus-visible:ring-2 focus-visible:ring-titan-red focus-visible:ring-offset-2">
                            <span aria-hidden="true" class="absolute inset-y-0 left-0 w-1 origin-bottom scale-y-0 bg-titan-red transition-transform duration-300 group-hover:scale-y-100"></span>

                            <div class="flex items-start gap-4">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-titan-navy text-white transition-colors duration-300 group-hover:bg-titan-red">
                                    <x-lucide-briefcase class="h-4 w-4" />
                                </span>
                                <div class="min-w-0 flex-1">
                                    <div class="mb-2 flex items-center justify-between gap-3">
                                        <span class="truncate text-[10px] font-black uppercase tracking-[0.14em] text-titan-red" x-text="job.dept"></span>
                                        <span class="inline-flex shrink-0 items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-[10px] font-black uppercase tracking-[0.1em] text-emerald-700">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>{{ __('Open') }}
                                        </span>
                                    </div>
                                    <h3 class="line-clamp-2 text-lg font-black leading-snug text-titan-navy transition-colors duration-300 group-hover:text-titan-red" x-text="job.title"></h3>
                                </div>
                            </div>

                            <p class="mt-3 text-sm leading-relaxed text-gray-500 line-clamp-2" x-show="job.summary" x-text="job.summary"></p>

                            <div class="mt-4 grid grid-cols-2 overflow-hidden rounded-xl border border-gray-100 bg-gray-50 text-xs">
                                <div class="flex min-w-0 items-center gap-2 border-r border-gray-100 px-3 py-2.5 text-gray-600">
                                    <x-lucide-map-pin class="h-4 w-4 shrink-0 text-titan-red" />
                                    <span class="truncate" x-text="job.loc"></span>
                                </div>
                                <div class="flex min-w-0 items-center gap-2 px-3 py-2.5 text-gray-600">
                                    <x-lucide-clock class="h-4 w-4 shrink-0 text-titan-red" />
                                    <span class="truncate" x-text="job.type"></span>
                                </div>
                            </div>

                            <div class="mt-4">
                                <span class="flex h-10 items-center justify-between rounded-xl bg-titan-navy px-4 text-sm font-bold text-white transition-colors duration-300 group-hover:bg-titan-red">
                                    {{ __('View Job Details') }}
                                    <x-lucide-arrow-right class="h-4 w-4 transition-transform duration-300 group-hover:translate-x-1" />
                                </span>
                            </div>
                        </a>
                    </template>
                </div>

                <!-- Empty State -->
                <div x-show="filteredJobs.length === 0" style="display: none"
                    class="text-center py-20 bg-white border border-dashed border-gray-200 rounded-2xl mt-4">
                    <x-lucide-search class="w-12 h-12 text-gray-200 mx-auto mb-4" />
                    <p class="text-base text-gray-400 mb-2">{{ __('No positions match your search.') }}</p>
                    <p class="text-sm text-gray-300 mb-6">{{ __('Try adjusting your filters or search term.') }}</p>
                    <button @click="clearFilters()"
                        class="h-10 px-6 rounded-xl text-sm font-bold text-white transition-colors"
                        style="background: var(--primary-color, #E31E24);">
                        {{ __('Clear All Filters') }}
                    </button>
                </div>

                <!-- General Application CTA -->
                <div class="mt-12 relative overflow-hidden rounded-2xl" style="background: linear-gradient(135deg, #071A33, #0B2B5C);">
                    <div class="absolute top-0 right-0 w-[300px] h-[300px] opacity-[0.04] pointer-events-none"
                         style="background: radial-gradient(circle, var(--primary-color, #E31E24), transparent 70%);"></div>
                    <div class="relative z-10 p-8 md:p-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                        <div>
                            <h3 class="text-xl md:text-2xl font-heading font-black mb-2" style="color: #FFFFFF;">{{ __("Don't see your perfect role?") }}</h3>
                            <p class="text-sm leading-relaxed" style="color: rgba(255,255,255,0.55);">{{ __('Send us your CV and we\'ll reach out when a position matches your skills.') }}</p>
                        </div>
                        <button @click="isApplyOpen = true"
                            class="shrink-0 inline-flex items-center gap-2 px-7 py-3.5 rounded-xl text-sm font-bold transition-all duration-300 shadow-lg hover:shadow-xl hover:-translate-y-0.5"
                            style="background: var(--primary-color, #E31E24); color: #FFFFFF;">
                            <x-lucide-send class="w-4 h-4" />
                            {{ __('General Application') }}
                        </button>
                    </div>
                </div>
            </div>
        </section>


        <!-- ═══ APPLICATION MODAL ═══ -->
        <div x-show="isApplyOpen" style="display: none"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[200] flex items-end sm:items-center justify-center sm:p-4">
            <div @click="isApplyOpen = false" class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>

            <div x-show="isApplyOpen"
                x-transition:enter="transition ease-out duration-250" x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                class="relative w-full sm:max-w-lg bg-white sm:rounded-2xl shadow-2xl overflow-hidden max-h-[92svh] overflow-y-auto"
                role="dialog" aria-modal="true" aria-labelledby="general-application-title">

                <!-- Header -->
                <div class="px-6 py-5 flex items-center justify-between" style="background: #071A33;">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-wider mb-0.5" style="color: rgba(255,255,255,0.4);">{{ __('Kimmex Careers') }}</p>
                        <h3 id="general-application-title" class="text-base font-bold" style="color: #FFFFFF;">{{ __('General Application') }}</h3>
                    </div>
                    <button @click="isApplyOpen = false" class="w-9 h-9 rounded-full flex items-center justify-center transition-colors" style="background: rgba(255,255,255,0.1); color: rgba(255,255,255,0.6);">
                        <x-lucide-x class="w-4 h-4" />
                    </button>
                </div>

                <!-- Form -->
                <div class="p-6">
                    <p class="mb-5 rounded-xl bg-gray-50 px-4 py-3 text-sm text-gray-600">
                        {{ __('You only need your name, contact details, and CV.') }}
                    </p>
                    @if(session('success'))
                        <div class="flex items-center gap-2 bg-green-50 border border-green-100 text-green-700 rounded-lg p-3 mb-5 text-sm font-medium" x-init="isApplyOpen = true">
                            <x-lucide-check-circle class="w-4 h-4 text-green-500 shrink-0" />
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('careers.apply') }}" method="POST" enctype="multipart/form-data" class="space-y-4" x-data="{ submitting: false }" x-on:submit="submitting = true">
                        @csrf
                        <div class="hidden" aria-hidden="true"><input type="text" name="website_url" tabindex="-1" autocomplete="off" /></div>
                        <input type="hidden" name="job_id" value="general-application">

                        <!-- Full Name -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1.5">{{ __('Full Name') }} <span class="text-red-500">*</span></label>
                            <input type="text" name="full_name" value="{{ old('full_name') }}" required
                                class="w-full h-11 px-4 rounded-xl border border-gray-200 text-sm text-gray-900 placeholder:text-gray-300 focus:outline-none focus:border-gray-400 transition @error('full_name') border-red-300 bg-red-50 @enderror"
                                placeholder="{{ __('e.g. CHAN Sopheap') }}" />
                            @error('full_name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                        </div>

                        <!-- Email + Phone -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1.5">{{ __('Email') }} <span class="text-red-500">*</span></label>
                                <input type="email" name="email" value="{{ old('email') }}" required
                                    class="w-full h-11 px-4 rounded-xl border border-gray-200 text-sm text-gray-900 placeholder:text-gray-300 focus:outline-none focus:border-gray-400 transition @error('email') border-red-300 bg-red-50 @enderror"
                                    placeholder="you@example.com" />
                                @error('email')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1.5">{{ __('Phone') }} <span class="text-red-500">*</span></label>
                                <input type="tel" name="phone" value="{{ old('phone') }}" required inputmode="tel"
                                    class="w-full h-11 px-4 rounded-xl border border-gray-200 text-sm text-gray-900 placeholder:text-gray-300 focus:outline-none focus:border-gray-400 transition @error('phone') border-red-300 bg-red-50 @enderror"
                                    placeholder="+855 12 345 678" />
                                @error('phone')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <!-- Cover Letter -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1.5">{{ __('Cover Letter') }} <span class="text-gray-300 font-normal">({{ __('optional') }})</span></label>
                            <textarea name="message" rows="3"
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm text-gray-900 placeholder:text-gray-300 focus:outline-none focus:border-gray-400 transition resize-none"
                                placeholder="{{ __('Brief introduction to the hiring team...') }}">{{ old('message') }}</textarea>
                        </div>

                        <!-- CV Upload -->
                        <div x-data="{ fileName: '' }">
                            <label class="block text-xs font-bold text-gray-700 mb-1.5">{{ __('Resume / CV') }} <span class="text-red-500">*</span></label>
                            <div class="relative rounded-xl border-2 border-dashed border-gray-200 hover:border-gray-300 transition-colors cursor-pointer overflow-hidden"
                                :class="fileName ? 'border-green-300 bg-green-50' : ''">
                                <input type="file" name="resume" required accept=".pdf,.doc,.docx"
                                    class="absolute inset-0 opacity-0 cursor-pointer z-10"
                                    @change="fileName = $event.target.files[0]?.name || ''" />
                                <div class="flex items-center gap-3 p-4">
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0"
                                        :class="fileName ? 'bg-green-100' : 'bg-gray-50 border border-gray-200'">
                                        <template x-if="!fileName"><x-lucide-upload class="w-4 h-4 text-gray-400" /></template>
                                        <template x-if="fileName"><x-lucide-file-check class="w-4 h-4 text-green-600" /></template>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <template x-if="!fileName">
                                            <div>
                                                <p class="text-sm font-medium text-gray-700">{{ __('Drop your CV here or click to browse') }}</p>
                                                <p class="text-xs text-gray-400 mt-0.5">PDF, DOC, DOCX — max 10 MB</p>
                                            </div>
                                        </template>
                                        <template x-if="fileName">
                                            <div>
                                                <p class="text-sm font-medium text-green-700 truncate" x-text="fileName"></p>
                                                <p class="text-xs text-green-500 mt-0.5">{{ __('Ready to submit') }}</p>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                            @error('resume')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                        </div>

                        <!-- Submit -->
                        <div class="flex items-center justify-between pt-2">
                            <p class="text-xs text-gray-400">* {{ __('required') }}</p>
                            <button type="submit"
                                x-bind:disabled="submitting"
                                class="inline-flex items-center gap-2 h-11 px-6 rounded-xl text-sm font-bold transition-all duration-300 group disabled:opacity-60 disabled:cursor-not-allowed"
                                style="background: var(--primary-color, #E31E24); color: #FFFFFF;">
                                <span x-show="!submitting">{{ __('Submit Application') }}</span>
                                <span x-show="submitting" class="inline-flex items-center gap-2">
                                    <svg class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                    {{ __('Submitting...') }}
                                </span>
                                <x-lucide-arrow-right x-show="!submitting" class="w-4 h-4 group-hover:translate-x-0.5 transition-transform" />
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-layouts.app>
