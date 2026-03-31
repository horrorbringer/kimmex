<x-layouts.app title="Careers" description="Join the Kimmex team and build your future in the construction industry.">

    @php
        $jobsDb = \App\Models\JobPosting::where('isActive', true)
            ->with('department')
            ->orderBy('created_at', 'desc')
            ->get();

        $jobs = $jobsDb->map(function ($j) {
            $deptName = $j->department ? $j->department->getTranslation('name', app()->getLocale()) : ($j->getTranslation('department', app()->getLocale()) ?: __('General'));
            return [
                'id' => $j->id,
                'title' => $j->getTranslation('title', app()->getLocale()),
                'dept' => $deptName,
                'loc' => $j->getTranslation('location', app()->getLocale()),
                'type' => __($j->type ?? 'FULL_TIME'),
                'salary' => $j->getTranslation('salary', app()->getLocale()) ?: __('Negotiable'),
                'experience' => $j->getTranslation('experience', app()->getLocale()) ?: __('2-3 Years'),
                'postedDate' => $j->created_at ? $j->created_at->format('M d, Y') : now()->format('M d, Y'),
                'tags' => [$deptName],
                'summary' => \Illuminate\Support\Str::limit(strip_tags($j->getTranslation('summary', app()->getLocale())), 150)
            ];
        })->toArray();

        $categories = $jobsDb->map(fn($j) => $j->department ? $j->department->getTranslation('name', app()->getLocale()) : ($j->getTranslation('department', app()->getLocale()) ?: __('Engineering')))
            ->unique()->values()->prepend(__('All Departments'))->toArray();

        $locations = $jobsDb->map(fn($j) => $j->getTranslation('location', app()->getLocale()))
            ->unique()->values()->prepend(__('All Locations'))->toArray();

        // Fallback for empty DB
        if (empty($jobs)) {
            $jobs = [
                ['id' => 'gen', 'title' => __('Visionary Talent'), 'dept' => __('General'), 'loc' => __('Phnom Penh'), 'type' => __('Full-time'), 'salary' => __('Competitive'), 'experience' => __('Mixed'), 'postedDate' => now()->format('M d, Y'), 'tags' => [__('Hiring')], 'summary' => __('We are always looking for exceptional engineers and managers.')]
            ];
        }
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
    }
}" class="bg-white min-h-screen text-titan-navy">

        <!-- HERO -->
        <section class="relative z-10 flex items-center overflow-hidden bg-titan-navy" style="min-height: 80vh;">
            <div class="absolute inset-0">
                <!-- Background Image with slight scale for depth -->
                <img src="/images/projects/Thumbnail-5.jpg" alt="Careers at Kimmex"
                    class="w-full h-full object-cover opacity-100 transform scale-105" />

                <!-- Sleek Gradient Overlays -->
                <div class="absolute inset-0 bg-gradient-to-r from-titan-navy/90 via-titan-navy/50 to-titan-navy/10">
                </div>
                <div class="absolute inset-0 bg-gradient-to-t from-titan-navy/80 via-transparent to-transparent"></div>

                <!-- Soft red radial glow to draw focus -->
                <div
                    class="absolute top-[20%] left-[10%] w-[600px] h-[600px] bg-titan-red/10 rounded-full blur-[120px] pointer-events-none mix-blend-screen">
                </div>
            </div>

            <div class="relative z-20 w-full max-w-[1200px] mx-auto px-6 pt-[160px] pb-24">
                <div x-data="{ shown: false }" x-init="setTimeout(() => shown = true, 100)">
                    <!-- Premium Glassmorphism Badge -->
                    <div :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 -translate-y-6'"
                        class="transition-all duration-1000 delay-100 inline-flex items-center gap-4 px-5 py-2.5 bg-white/5 backdrop-blur-xl rounded-full text-white text-[11px] font-black uppercase tracking-[0.25em] mb-10 border border-white/10 shadow-[0_0_30px_rgba(0,0,0,0.3)]">
                        <span class="relative flex h-2.5 w-2.5">
                            <span
                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-titan-red opacity-75"></span>
                            <span
                                class="relative inline-flex rounded-full h-2.5 w-2.5 bg-titan-red shadow-[0_0_10px_rgba(255,42,0,0.8)]"></span>
                        </span>
                        {{ __('We are Hiring') }}
                    </div>

                    <!-- Striking Typography with soft red gradient -->
                    <h1 :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10'"
                        class="transition-all duration-1000 delay-300 font-black text-white mb-8 leading-[0.95] tracking-tight uppercase"
                        style="font-size: clamp(3rem, 9vw, 6.5rem);">
                        {{ __('Build Your') }}<br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-titan-red to-[#ff5533]"
                            style="filter: drop-shadow(0 10px 20px rgba(255,42,0,0.2));">{{ __('Career') }}</span>
                    </h1>

                    <!-- Refined Lead Text with border accent -->
                    <p :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-6'"
                        class="transition-all duration-1000 delay-500 text-lg md:text-xl text-white/70 max-w-2xl font-light leading-relaxed border-l-[3px] border-titan-red/50 pl-6 mb-12">
                        {{ __('Join a team of visionaries shaping the skyline and engineering future of Cambodia. We value excellence, impact, and continuous growth.') }}
                    </p>

                    <!-- Upgraded Buttons -->
                    <div :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-6'"
                        class="transition-all duration-1000 delay-700 flex flex-col sm:flex-row items-stretch sm:items-center gap-5">

                        <button @click="document.getElementById('openings')?.scrollIntoView({ behavior: 'smooth' })"
                            class="relative group overflow-hidden bg-gradient-to-r from-titan-red to-[#cc2200] text-white px-8 py-5 md:px-10 rounded-2xl font-black text-[12px] uppercase tracking-widest shadow-[0_20px_40px_rgba(255,42,0,0.25)] hover:shadow-[0_20px_50px_rgba(255,42,0,0.4)] hover:-translate-y-1 transition-all duration-500 flex items-center justify-between sm:justify-center gap-6 border border-titan-red/50">
                            <div
                                class="absolute inset-0 bg-white/20 translate-y-[100%] group-hover:translate-y-0 transition-transform duration-500 ease-in-out z-0">
                            </div>
                            <span class="relative z-10">{{ __('View Openings') }}</span>
                            <div
                                class="relative z-10 w-8 h-8 rounded-full bg-white/20 flex items-center justify-center group-hover:bg-white group-hover:text-titan-red transition-colors duration-300 shadow-inner">
                                <x-lucide-arrow-down
                                    class="w-4 h-4 translate-y-[-1px] group-hover:translate-y-[2px] transition-transform duration-300" />
                            </div>
                        </button>

                        <button @click="isApplyOpen = true"
                            class="relative group bg-white/5 hover:bg-white/10 backdrop-blur-xl border border-white/10 hover:border-white/30 text-white px-8 py-5 md:px-10 rounded-2xl font-bold text-[12px] uppercase tracking-widest shadow-[0_20px_40px_rgba(0,0,0,0.2)] hover:-translate-y-1 transition-all duration-500 flex items-center justify-between sm:justify-center gap-6">
                            <span class="relative z-10">{{ __('General Application') }}</span>
                            <div
                                class="relative z-10 w-8 h-8 rounded-full bg-white/10 flex items-center justify-center group-hover:scale-110 group-hover:bg-white/20 transition-all duration-300">
                                <x-lucide-send
                                    class="w-4 h-4 group-hover:translate-x-[2px] group-hover:-translate-y-[2px] transition-transform duration-300 text-white/80 group-hover:text-white" />
                            </div>
                        </button>

                    </div>
                </div>
            </div>

            <!-- Stats placeholder removed (Waiting for real data) -->
        </section>

        <!-- WHY JOIN US -->
        <section x-data="{ revealed: false }" x-intersect.once="revealed = true"
            class="pt-20 pb-24 max-w-[1200px] mx-auto px-6">
            <div :class="revealed ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10'"
                class="transition-all duration-700 flex items-center gap-4 mb-12">
                <div class="w-1 h-8 bg-titan-red rounded-full"></div>
                <div>
                    <h2 class="text-2xl font-black text-titan-navy uppercase tracking-tight">
                        {{ __('Why Work With Us?') }}
                    </h2>
                    <p class="text-titan-navy/35 text-xs mt-0.5">{{ __('Build your future with Kimmex Construction.') }}
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div :class="revealed ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10'"
                    class="transition-all duration-700 delay-200 relative group">
                    <div
                        class="bg-gray-50/50 border border-gray-100 rounded-2xl p-8 hover:bg-white hover:border-titan-red/30 hover:shadow-[0_20px_50px_-15px_rgba(0,0,0,0.06)] transition-all duration-500">
                        <div
                            class="w-12 h-12 bg-white border border-gray-100 text-titan-red rounded-xl flex items-center justify-center mb-6 shadow-sm group-hover:bg-titan-red group-hover:text-white group-hover:border-titan-red transition-all duration-300">
                            <x-lucide-award class="w-5 h-5" />
                        </div>
                        <h3
                            class="text-lg font-black text-titan-navy group-hover:text-titan-red transition-colors duration-300 uppercase tracking-tight mb-3">
                            {{ __('Excellence') }}
                        </h3>
                        <p class="text-titan-navy/40 text-sm leading-relaxed">
                            {{ __('We maintain high standards across all our construction and engineering projects.') }}
                        </p>
                    </div>
                </div>
                <div :class="revealed ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10'"
                    class="transition-all duration-700 delay-300 relative group">
                    <div
                        class="bg-gray-50/50 border border-gray-100 rounded-2xl p-8 hover:bg-white hover:border-titan-red/30 hover:shadow-[0_20px_50px_-15px_rgba(0,0,0,0.06)] transition-all duration-500">
                        <div
                            class="w-12 h-12 bg-white border border-gray-100 text-titan-red rounded-xl flex items-center justify-center mb-6 shadow-sm group-hover:bg-titan-red group-hover:text-white group-hover:border-titan-red transition-all duration-300">
                            <x-lucide-target class="w-5 h-5" />
                        </div>
                        <h3
                            class="text-lg font-black text-titan-navy group-hover:text-titan-red transition-colors duration-300 uppercase tracking-tight mb-3">
                            {{ __('Impact') }}
                        </h3>
                        <p class="text-titan-navy/40 text-sm leading-relaxed">
                            {{ __('Our work contributes directly to the growth of infrastructure in Cambodia.') }}
                        </p>
                    </div>
                </div>
                <div :class="revealed ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10'"
                    class="transition-all duration-700 delay-400 relative group">
                    <div
                        class="bg-gray-50/50 border border-gray-100 rounded-2xl p-8 hover:bg-white hover:border-titan-red/30 hover:shadow-[0_20px_50px_-15px_rgba(0,0,0,0.06)] transition-all duration-500">
                        <div
                            class="w-12 h-12 bg-white border border-gray-100 text-titan-red rounded-xl flex items-center justify-center mb-6 shadow-sm group-hover:bg-titan-red group-hover:text-white group-hover:border-titan-red transition-all duration-300">
                            <x-lucide-users class="w-5 h-5" />
                        </div>
                        <h3
                            class="text-lg font-black text-titan-navy group-hover:text-titan-red transition-colors duration-300 uppercase tracking-tight mb-3">
                            {{ __('Growth') }}
                        </h3>
                        <p class="text-titan-navy/40 text-sm leading-relaxed">
                            {{ __('We provide opportunities for professional development within our engineering teams.') }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- HIRING PROCESS -->
        <section x-data="{ revealed: false }" x-intersect.once="revealed = true" class="py-24 bg-white relative">
            <div class="max-w-[1200px] mx-auto px-6">
                <div :class="revealed ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10'"
                    class="transition-all duration-700 flex flex-col items-center text-center mb-16">
                    <!-- Title Group -->
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-[1px] bg-titan-red"></div>
                        <span
                            class="text-[10px] font-black text-titan-red uppercase tracking-[0.3em]">{{ __('The Process') }}</span>
                        <div class="w-10 h-[1px] bg-titan-red"></div>
                    </div>
                    <h2 class="text-3xl font-black text-titan-navy uppercase tracking-tight">
                        {{ __('Our Hiring Journey') }}
                    </h2>
                    <p class="text-titan-navy/35 text-xs max-w-md mt-4">
                        {{ __('We follow a clean, transparent, and efficient process to ensure the best fit for our engineering teams.') }}
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 lg:gap-8 relative">
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
                                class="bg-gray-50/50 border border-gray-100 rounded-2xl p-8 hover:bg-white hover:border-titan-red/30 hover:shadow-[0_20px_50px_-15px_rgba(0,0,0,0.06)] transition-all duration-500 min-h-[180px]">
                                <!-- Number Badge -->
                                <div
                                    class="w-10 h-10 rounded-full bg-white border border-gray-100 flex items-center justify-center text-xs font-black text-titan-red mb-6 shadow-sm group-hover:bg-titan-red group-hover:text-white group-hover:border-titan-red transition-all duration-300">
                                    0{{ $i + 1 }}
                                </div>
                                <h3
                                    class="text-sm font-black text-titan-navy uppercase tracking-widest mb-3 group-hover:text-titan-red transition-colors">
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
            class="scroll-mt-32 pt-24 pb-24 max-w-[1200px] mx-auto px-6">
            <!-- Header Section -->
            <div :class="revealed ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10'"
                class="transition-all duration-700 mb-10">
                <div class="flex items-center gap-4 mb-2">
                    <div class="w-1.5 h-10 bg-titan-red rounded-full"></div>
                    <h2 class="text-3xl font-black text-titan-navy uppercase tracking-tight">
                        {{ __('Current Openings') }}
                    </h2>
                </div>
                <p class="text-titan-navy/35 text-sm ml-5.5">
                    {{ __('Find your place among Cambodia\'s most impactful engineering teams.') }}
                </p>
            </div>

            <!-- Filters Section (One Line Full Width) -->
            <div :class="revealed ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10'"
                class="transition-all duration-700 mb-12 relative z-40">
                <div
                    class="flex flex-col md:flex-row gap-3 w-full bg-gray-50 p-2 rounded-2xl border border-gray-100 shadow-sm">
                    <!-- Search -->
                    <div class="relative flex-grow">
                        <x-lucide-search class="absolute left-4 top-1/2 -translate-y-1/2 text-titan-navy/20 w-4 h-4" />
                        <input type="text"
                            placeholder="{{ __('Search for specific roles (e.g. Engineer, Supervisor)...') }}"
                            x-model="searchQuery"
                            class="w-full pl-11 pr-4 py-3.5 rounded-xl border-none bg-white text-sm font-semibold text-titan-navy focus:ring-2 focus:ring-titan-red/10 transition-all placeholder:text-titan-navy/25 shadow-sm" />
                    </div>

                    <!-- Department Dropdown -->
                    <div class="relative shrink-0 md:w-64" x-data="{ open: false }">
                        <button @click="open = !open"
                            class="w-full flex items-center gap-2 bg-white border-none px-5 py-3.5 rounded-xl text-sm font-bold text-titan-navy justify-between transition-all hover:bg-gray-50 shadow-sm"
                            :class="open ? 'ring-2 ring-titan-red/10' : ''">
                            <div class="flex items-center gap-2">
                                <x-lucide-filter class="text-titan-red w-3.5 h-3.5" />
                                <span x-text="filterDept" class="truncate"></span>
                            </div>
                            <x-lucide-chevron-down
                                class="text-titan-navy/20 w-3.5 h-3.5 transition-transform duration-300"
                                ::class="open ? 'rotate-180' : ''" />
                        </button>
                        <div x-show="open" @click.away="open = false" style="display: none"
                            class="absolute top-full left-0 w-full mt-2 bg-white border border-gray-100 rounded-xl shadow-xl py-2 z-50">
                            @foreach($categories as $cat)
                                <button @click="filterDept = '{{ $cat }}'; open = false"
                                    class="w-full text-left px-5 py-2.5 text-[13px] font-bold hover:bg-gray-50 flex items-center justify-between transition-colors"
                                    :class="filterDept === '{{ $cat }}' ? 'text-titan-red bg-red-50/50' : 'text-titan-navy/60'">
                                    {{ $cat }}
                                    <x-lucide-check x-show="filterDept === '{{ $cat }}'"
                                        class="text-titan-red w-3.5 h-3.5" />
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Location Dropdown -->
                    <div class="relative shrink-0 md:w-60" x-data="{ open: false }">
                        <button @click="open = !open"
                            class="w-full flex items-center gap-2 bg-white border-none px-5 py-3.5 rounded-xl text-sm font-bold text-titan-navy justify-between transition-all hover:bg-gray-50 shadow-sm"
                            :class="open ? 'ring-2 ring-titan-red/10' : ''">
                            <div class="flex items-center gap-2">
                                <x-lucide-map-pin class="text-titan-red w-3.5 h-3.5" />
                                <span x-text="filterLoc" class="truncate"></span>
                            </div>
                            <x-lucide-chevron-down
                                class="text-titan-navy/20 w-3.5 h-3.5 transition-transform duration-300"
                                ::class="open ? 'rotate-180' : ''" />
                        </button>
                        <div x-show="open" @click.away="open = false" style="display: none"
                            class="absolute top-full left-0 w-full mt-2 bg-white border border-gray-100 rounded-xl shadow-xl py-2 z-50">
                            @foreach($locations as $loc)
                                <button @click="filterLoc = '{{ $loc }}'; open = false"
                                    class="w-full text-left px-5 py-2.5 text-[13px] font-bold hover:bg-gray-50 flex items-center justify-between transition-colors"
                                    :class="filterLoc === '{{ $loc }}' ? 'text-titan-red bg-red-50/50' : 'text-titan-navy/60'">
                                    {{ $loc }}
                                    <x-lucide-check x-show="filterLoc === '{{ $loc }}'"
                                        class="text-titan-red w-3.5 h-3.5" />
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Job Cards -->
            <div class="space-y-6">
                <template x-for="(job, index) in filteredJobs" :key="job.id">
                    <div
                        class="bg-white border border-gray-100 rounded-2xl p-8 hover:shadow-[0_20px_60px_-15px_rgba(0,0,0,0.08)] hover:border-gray-200 transition-all duration-400 group relative overflow-hidden">
                        <!-- Hover Accent Bar (Design-Z) -->
                        <div
                            class="absolute inset-y-0 left-0 w-0 group-hover:w-2 bg-titan-red transition-all duration-300">
                        </div>

                        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-8 relative z-10">
                            <!-- Left: Job Info -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center flex-wrap gap-2 mb-4">
                                    <template x-for="tag in job.tags">
                                        <span
                                            class="px-3 py-1 bg-titan-red/5 text-titan-red text-[10px] font-black uppercase tracking-[0.1em] rounded-md border border-titan-red/10"
                                            x-text="tag"></span>
                                    </template>
                                    <span
                                        class="bg-gray-50 px-3 py-1 rounded-md border border-gray-100 text-[10px] font-bold text-titan-navy/30 flex items-center gap-1.5">
                                        <x-lucide-calendar class="w-3 h-3" /> <span x-text="job.postedDate"></span>
                                    </span>
                                </div>
                                <h3 class="text-2xl font-black text-titan-navy group-hover:text-titan-red transition-colors mb-3 tracking-tight"
                                    x-text="job.title"></h3>
                                <p class="text-titan-navy/40 text-[13px] leading-relaxed line-clamp-2 max-w-2xl"
                                    x-text="job.summary"></p>
                            </div>

                            <!-- Middle: Details -->
                            <div
                                class="flex flex-wrap gap-8 text-sm shrink-0 border-t lg:border-t-0 lg:border-l border-gray-100 pt-6 lg:pt-0 lg:pl-8">
                                <div>
                                    <div
                                        class="text-[10px] font-black uppercase tracking-widest text-titan-navy/20 mb-2">
                                        {{ __('Location') }}
                                    </div>
                                    <div class="font-black text-titan-navy flex items-center gap-2 text-xs">
                                        <div class="w-6 h-6 rounded-lg bg-gray-50 flex items-center justify-center">
                                            <x-lucide-map-pin class="w-3 h-3 text-titan-red" />
                                        </div>
                                        <span x-text="job.loc"></span>
                                    </div>
                                </div>
                                <div>
                                    <div
                                        class="text-[10px] font-black uppercase tracking-widest text-titan-navy/20 mb-2">
                                        {{ __('Experience') }}
                                    </div>
                                    <div class="font-black text-titan-navy flex items-center gap-2 text-xs">
                                        <div class="w-6 h-6 rounded-lg bg-gray-50 flex items-center justify-center">
                                            <x-lucide-briefcase class="w-3 h-3 text-titan-red" />
                                        </div>
                                        <span x-text="job.experience"></span>
                                    </div>
                                </div>
                                <div>
                                    <div
                                        class="text-[10px] font-black uppercase tracking-widest text-titan-navy/20 mb-2">
                                        {{ __('Job Type') }}
                                    </div>
                                    <div class="font-black text-titan-navy flex items-center gap-2 text-xs">
                                        <div class="w-6 h-6 rounded-lg bg-gray-50 flex items-center justify-center">
                                            <x-lucide-clock class="w-3 h-3 text-titan-red" />
                                        </div>
                                        <span x-text="job.type"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Right: CTA -->
                            <div class="shrink-0 pt-4 lg:pt-0">
                                <a :href="'/careers/' + job.id"
                                    class="bg-titan-navy text-white px-8 py-4 rounded-xl font-bold text-[11px] uppercase tracking-widest hover:bg-titan-red transition-all shadow-lg hover:-translate-y-0.5 flex items-center gap-3 group/btn">
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
                    class="text-center py-20 border border-dashed border-gray-200 rounded-2xl">
                    <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-5">
                        <x-lucide-search class="text-gray-200 w-7 h-7" />
                    </div>
                    <h3 class="text-lg font-black text-titan-navy mb-2">{{ __('No positions found') }}</h3>
                    <p class="text-titan-navy/35 text-sm mb-6">{{ __('Try adjusting your search or filters.') }}</p>
                    <button
                        @click="filterDept = '{{ __('All Departments') }}'; filterLoc = '{{ __('All Locations') }}'; searchQuery = ''"
                        class="bg-titan-navy text-white px-6 py-3 rounded-xl font-bold text-xs uppercase tracking-widest hover:bg-titan-red transition-colors">
                        {{ __('Clear All Filters') }}
                    </button>
                </div>
            </div>

            <!-- CTA Banner -->
            <div class="mt-16 bg-titan-navy rounded-2xl p-10 md:p-14 text-white relative overflow-hidden">
                <div
                    class="absolute inset-0 opacity-5 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')]">
                </div>
                <div
                    class="absolute top-0 right-0 w-[300px] h-[300px] bg-titan-red/10 rounded-full blur-[100px] pointer-events-none">
                </div>
                <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
                    <div>
                        <h3 class="text-2xl font-black uppercase tracking-tight mb-2">
                            {{ __("Don't see your perfect role?") }}
                        </h3>
                        <p class="text-white/40 text-sm">
                            {{ __('Send us your CV and we\'ll contact you for future opportunities.') }}
                        </p>
                    </div>
                    <button @click="isApplyOpen = true"
                        class="shrink-0 bg-titan-red text-white px-8 py-4 rounded-xl font-bold text-xs uppercase tracking-widest hover:bg-white hover:text-titan-red transition-all shadow-lg flex items-center gap-2 group">
                        {{ __('General Application') }}
                        <x-lucide-send
                            class="w-4 h-4 group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-transform" />
                    </button>
                </div>
            </div>
        </section>

        <!-- APPLICATION MODAL -->
        <div x-show="isApplyOpen" style="display: none"
            class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div @click="isApplyOpen = false" class="absolute inset-0 bg-titan-navy/80 backdrop-blur-sm"></div>

            <div x-show="isApplyOpen" x-transition.scale.95.opacity
                class="relative w-full max-w-2xl bg-white rounded-2xl shadow-2xl overflow-hidden max-h-[90vh] overflow-y-auto">
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-titan-navy to-titan-red"></div>
                <button @click="isApplyOpen = false"
                    class="absolute top-4 right-4 text-gray-400 hover:text-titan-red transition-colors bg-gray-50 rounded-full p-2">
                    <x-lucide-x class="w-5 h-5" />
                </button>

                <div class="p-8 md:p-12">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-1 h-8 bg-titan-red rounded-full"></div>
                        <div>
                            <h3 class="text-xl font-black text-titan-navy uppercase tracking-tight">
                                {{ __('General Application') }}
                            </h3>
                            <p class="text-titan-navy/35 text-xs mt-0.5">
                                {{ __('Ready to join our team? Fill out the form below.') }}
                            </p>
                        </div>
                    </div>

                    @if(session('success'))
                        <div class="bg-green-50 text-green-700 p-4 rounded-xl mb-6 text-sm font-semibold border border-green-100 flex items-center gap-2"
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
                            <label class="block text-xs font-bold text-titan-navy/40 mb-2 ml-1">{{ __('Full Name') }}
                                <span class="text-titan-red">*</span></label>
                            <input type="text" name="full_name" value="{{ old('full_name') }}" required
                                class="w-full bg-gray-50 border border-gray-100 rounded-xl px-4 py-3.5 text-sm font-semibold text-titan-navy focus:ring-2 focus:ring-titan-red/20 focus:border-titan-red/30 transition-all outline-none placeholder:text-gray-300 @error('full_name') border-titan-red @enderror"
                                placeholder="{{ __('John Doe') }}" />
                            @error('full_name') <p
                                class="text-[10px] text-titan-red font-bold uppercase tracking-widest mt-1 ml-1">
                                {{ $message }}
                            </p> @enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-titan-navy/40 mb-2 ml-1">{{ __('Email') }}
                                    <span class="text-titan-red">*</span></label>
                                <input type="email" name="email" value="{{ old('email') }}" required
                                    class="w-full bg-gray-50 border border-gray-100 rounded-xl px-4 py-3.5 text-sm font-semibold text-titan-navy focus:ring-2 focus:ring-titan-red/20 focus:border-titan-red/30 transition-all outline-none placeholder:text-gray-300 @error('email') border-titan-red @enderror"
                                    placeholder="john@example.com" />
                                @error('email') <p
                                    class="text-[10px] text-titan-red font-bold uppercase tracking-widest mt-1 ml-1">
                                    {{ $message }}
                                </p> @enderror
                            </div>
                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-titan-navy/40 mb-2 ml-1">{{ __('Phone') }}
                                    <span class="text-titan-red">*</span></label>
                                <input type="tel" name="phone" value="{{ old('phone') }}" required
                                    class="w-full bg-gray-50 border border-gray-100 rounded-xl px-4 py-3.5 text-sm font-semibold text-titan-navy focus:ring-2 focus:ring-titan-red/20 focus:border-titan-red/30 transition-all outline-none placeholder:text-gray-300 @error('phone') border-titan-red @enderror"
                                    placeholder="+855 12 345 678" />
                                @error('phone') <p
                                    class="text-[10px] text-titan-red font-bold uppercase tracking-widest mt-1 ml-1">
                                    {{ $message }}
                                </p> @enderror
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label
                                class="block text-xs font-bold text-titan-navy/40 mb-2 ml-1">{{ __('Cover Letter / Message') }}</label>
                            <textarea name="message" rows="3"
                                class="w-full bg-gray-50 border border-gray-100 rounded-xl px-4 py-3.5 text-sm font-semibold text-titan-navy focus:ring-2 focus:ring-titan-red/20 focus:border-titan-red/30 transition-all outline-none resize-none placeholder:text-gray-300"
                                placeholder="{{ __('Brief motivation cover letter...') }}">{{ old('message') }}</textarea>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-titan-navy/40 mb-2 ml-1">{{ __('Resume / CV') }}
                                <span class="text-titan-red">*</span></label>
                            <div class="border-2 border-dashed border-gray-200 rounded-xl p-6 text-center hover:bg-gray-50/50 hover:border-gray-300 transition-all cursor-pointer relative @error('resume') border-titan-red @enderror"
                                x-data="{ fileName: '' }">
                                <input type="file" name="resume" required
                                    class="absolute inset-0 opacity-0 cursor-pointer z-10" accept=".pdf,.doc,.docx"
                                    @change="fileName = $event.target.files[0]?.name || ''" />
                                <template x-if="!fileName">
                                    <div>
                                        <x-lucide-upload class="mx-auto mb-2 text-gray-300 w-6 h-6" />
                                        <p class="text-sm font-bold text-titan-navy">
                                            {{ __('Click to Upload or Drag & Drop') }}
                                        </p>
                                        <p class="text-xs text-gray-300 mt-1">{{ __('PDF, DOCX up to 10MB') }}</p>
                                    </div>
                                </template>
                                <template x-if="fileName">
                                    <div>
                                        <x-lucide-file-text class="mx-auto mb-2 text-titan-red w-6 h-6" />
                                        <p class="text-sm font-bold text-titan-navy" x-text="fileName"></p>
                                        <p class="text-xs text-green-500 mt-1 font-bold">{{ __('File Selected') }}</p>
                                    </div>
                                </template>
                            </div>
                            @error('resume') <p
                                class="text-[10px] text-titan-red font-bold uppercase tracking-widest mt-1 ml-1">
                                {{ $message }}
                            </p> @enderror
                        </div>

                        <button type="submit"
                            class="w-full bg-titan-red hover:bg-titan-navy text-white font-bold text-xs uppercase tracking-widest py-4 rounded-xl transition-all shadow-md flex items-center justify-center gap-2 group">
                            {{ __('Submit Application') }}
                            <x-lucide-arrow-right class="w-4 h-4 group-hover:translate-x-1 transition-transform" />
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>