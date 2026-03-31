<x-layouts.app title="Projects"
    description="View Kimmex's portfolio of successful construction and engineering projects.">

    @php
        // Fetch All Projects
        $projectsDb = \App\Models\Project::with('projectCategory')
            ->orderBy('created_at', 'desc')
            ->get();

        // Dynamically build filter lists
        $categories = $projectsDb->map(function ($p) {
            $cat = $p->projectCategory;
            if ($cat) {
                return $cat->getTranslation('name', app()->getLocale()) ?: ($cat->getTranslation('name', 'en') ?: $cat->name);
            }
            return $p->category ?: 'General';
        })->unique()->values()->prepend(__('All'))->toArray();

        $locations = $projectsDb->map(fn($p) => $p->getTranslation('location', app()->getLocale()))
            ->unique()->values()->prepend(__('All'))->toArray();

        $statusOptions = collect(\App\Enums\ProjectStatus::cases())->map(fn($s) => $s->getLabel())->prepend(__('All'))->toArray();

        $projects = $projectsDb->map(function ($p) {
            return [
                'id' => $p->slug,
                'title' => $p->getTranslation('title', app()->getLocale()),
                'location' => $p->getTranslation('location', app()->getLocale()),
                'type' => $p->projectCategory
                    ? ($p->projectCategory->getTranslation('name', app()->getLocale()) ?: ($p->projectCategory->getTranslation('name', 'en') ?: $p->projectCategory->name))
                    : ($p->category ?: 'General'),
                'status' => $p->status ? $p->status->getLabel() : __('Unknown'),
                'image' => $p->heroImage ? \Illuminate\Support\Facades\Storage::url($p->heroImage) : '/images/projects/Thumbnail-1.jpg',
                'summary' => strip_tags($p->getTranslation('description', app()->getLocale())),
            ];
        })->toArray();

        // Fallback for empty DB
        if (count($projects) === 0) {
            $projects = [
                ['id' => 'mef', 'title' => __('Ministry of Economy Building'), 'location' => __('Phnom Penh'), 'type' => __('Government'), 'status' => __('Completed'), 'image' => '/images/projects/Thumbnail-1.jpg', 'summary' => __('Kimmex built legacy facility.')]
            ];
        }
    @endphp

    <div x-data="{
        filterType: 'All',
        filterStatus: 'All',
        filterLoc: 'All',
        search: '',
        projects: {{ Js::from($projects) }},
        categories: {{ Js::from($categories) }},
        locations: {{ Js::from($locations) }},
        statusOptions: {{ Js::from($statusOptions) }},
        
        init() {
            const params = new URLSearchParams(window.location.search);
            const status = params.get('status');
            
            if (status === 'completed') {
                this.filterStatus = this.statusOptions.find(opt => opt.toLowerCase() === 'completed') || this.statusOptions[0];
            } else if (status === 'in-progress' || status === 'ongoing') {
                this.filterStatus = this.statusOptions.find(opt => opt.toLowerCase() === 'ongoing' || opt.toLowerCase() === 'in progress') || this.statusOptions[0];
            } else {
                this.filterStatus = this.statusOptions[0]; // All
            }
        },

        get filteredProjects() {
            // Ensure data labels match current translations/values
            const allTypeLabel = this.categories[0];
            const allLocLabel = this.locations[0];
            
            return this.projects.filter(p => {
                const isAllType = (this.filterType === 'All' || this.filterType === allTypeLabel);
                const isAllLoc = (this.filterLoc === 'All' || this.filterLoc === 'Everywhere' || this.filterLoc === allLocLabel);
                
                const matchType = isAllType || p.type === this.filterType;
                const matchLoc = isAllLoc || p.location === this.filterLoc;
                
                // Flexible status matching to handle 'All Status', 'Project Status' or 'All' literal
                const isAllStatus = (this.filterStatus === 'All' || this.filterStatus === 'All Status' || this.filterStatus === 'Project Status' || this.filterStatus === this.statusOptions[0]);
                const matchStatus = isAllStatus || p.status === this.filterStatus;
                
                const query = this.search.toLowerCase();
                const matchSearch = query === '' || 
                                   p.title.toLowerCase().includes(query) || 
                                   p.summary.toLowerCase().includes(query);
                                   
                return matchType && matchLoc && matchStatus && matchSearch;
            });
        }
    }" class="bg-white min-h-screen text-titan-navy relative overflow-hidden">

        <style>
            .projects-hero-container {
                min-height: 600px;
                /* No radius: Square architectural look */
            }

            /* Custom scroll animation */
            @keyframes scrollLine {
                0% {
                    transform: translateY(-100%);
                }

                100% {
                    transform: translateY(100%);
                }
            }

            .animate-scroll-line {
                animation: scrollLine 2s cubic-bezier(0.76, 0, 0.24, 1) infinite;
            }
        </style>

        <!-- === HERO SECTION (Design-Z Elite) === -->
        <section
            class="relative z-10 flex items-center justify-center overflow-hidden bg-[#1a1a2e] min-h-[85vh] projects-hero-container">
            <!-- Cinematic Background -->
            <div class="absolute inset-0 scale-105">
                <img src="/images/projects/Thumbnail-5.jpg" alt="Kimmex Built Legacy"
                    class="w-full h-full object-cover opacity-60 blur-[0.5px]" />
                <div class="absolute inset-0 bg-gradient-to-t from-titan-navy/80 via-titan-navy/30 to-transparent">
                </div>
            </div>

            <!-- Decorative Elements -->
            <div
                class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')]">
            </div>
            <div
                class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[1200px] h-[800px] bg-titan-red/5 rounded-full blur-[150px] pointer-events-none">
            </div>

            <div class="relative z-20 text-center px-6 max-w-7xl pt-24" x-data="{ shown: false }"
                x-init="setTimeout(() => shown = true, 100)">
                <div :class="shown ? 'opacity-100' : 'opacity-0'"
                    class="transition-all duration-1000 delay-100 inline-flex items-center gap-3 px-5 py-2.5 bg-white/5 backdrop-blur-xl rounded-lg border border-white/10 text-white text-[10px] font-black uppercase tracking-[0.3em] mb-12 shadow-2xl">
                    <span class="relative flex h-2 w-2">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-titan-red opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-titan-red"></span>
                    </span>
                    <span>{{ __('Portfolio Showcase') }}</span>
                </div>

                <h1 :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10'"
                    class="transition-all duration-1000 delay-300 text-5xl md:text-8xl font-black text-white mb-8 leading-[0.9] tracking-tighter uppercase">
                    {{ __('BUILT') }} <span class="text-titan-red">{{ __('LEGACY') }}</span>
                </h1>

                <p :class="shown ? 'opacity-100' : 'opacity-0' "
                    class="transition-all duration-1000 delay-500 text-[10px] md:text-xs text-white/40 max-w-2xl mx-auto leading-loose font-bold uppercase tracking-[0.4em] mb-16">
                    {{ __('Architecting the future through engineering precision.') }}
                </p>
            </div>

            <!-- Scroll Indicator -->
            <div class="absolute bottom-10 left-1/2 -translate-x-1/2 flex flex-col items-center gap-4 cursor-pointer group z-20"
                onclick="document.getElementById('portfolio-grid').scrollIntoView({ behavior: 'smooth' })">
                <span
                    class="text-[9px] uppercase tracking-[0.4em] font-black text-white/30 group-hover:text-white transition-colors">{{ __('Scroll') }}</span>
                <div class="w-[1px] h-12 bg-gradient-to-b from-titan-red to-transparent"></div>
            </div>
        </section>
        </section>

        <!-- INTEGRATED FILTER & GRID -->
        <section id="portfolio-grid" class="py-24 px-6 bg-white relative">
            <div class="max-w-[1700px] mx-auto">

                <!-- Clean Portfolio Filter Navigation -->
                <div
                    class="flex flex-col xl:flex-row items-start xl:items-center justify-between gap-6 mb-16 border-b border-gray-200 pb-6">

                    <!-- Category Tabs -->
                    <nav
                        class="flex flex-wrap items-center gap-2 lg:gap-8 w-full xl:w-auto overflow-x-auto no-scrollbar">
                        <template x-for="type in categories" :key="type">
                            <button @click="filterType = type"
                                class="pb-2 text-[11px] font-black uppercase tracking-[0.2em] transition-all duration-300 border-b-2 whitespace-nowrap hover:text-titan-navy"
                                :class="filterType === type ? 'border-titan-red text-titan-navy' : 'border-transparent text-gray-400'">
                                <span x-text="type === 'All' ? 'All Portfolios' : type"></span>
                            </button>
                        </template>
                    </nav>

                    <!-- Status, Location & Search Controls -->
                    <div class="flex flex-wrap items-center gap-4 w-full xl:w-auto">
                        <!-- Status / Location Filter Group -->
                        <div class="flex items-center gap-3 bg-gray-50 border border-gray-100 rounded-xl p-1 shrink-0">
                            <!-- Location Dropdown -->
                            <div class="relative min-w-[130px]">
                                <select x-model="filterLoc"
                                    class="appearance-none w-full bg-transparent pl-4 pr-10 py-2.5 text-[10px] font-black uppercase tracking-widest text-titan-navy transition-all cursor-pointer focus:outline-none focus:ring-0 border-0">
                                    <template x-for="loc in locations" :key="loc">
                                        <option :value="loc" x-text="loc === 'All' ? 'All Locations' : loc"></option>
                                    </template>
                                </select>
                                <x-lucide-chevron-down
                                    class="absolute right-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-titan-navy/40 pointer-events-none" />
                            </div>

                            <!-- Divider -->
                            <div class="w-px h-5 bg-gray-200"></div>

                            <!-- Status Badge Selector -->
                            <div class="relative min-w-[130px]">
                                <select x-model="filterStatus"
                                    class="appearance-none w-full bg-transparent pl-4 pr-10 py-2.5 text-[10px] font-black uppercase tracking-widest text-titan-navy transition-all cursor-pointer focus:outline-none focus:ring-0 border-0">
                                    <template x-for="stat in statusOptions" :key="stat">
                                        <option :value="stat === 'All' ? 'All Status' : stat"
                                            x-text="stat === 'All' ? 'Project Status' : stat"></option>
                                    </template>
                                </select>
                                <x-lucide-chevron-down
                                    class="absolute right-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-titan-navy/40 pointer-events-none" />
                            </div>
                        </div>

                        <!-- Search Quick Input -->
                        <div class="relative shrink-0 w-full sm:w-auto">
                            <x-lucide-search
                                class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-titan-navy/30" />
                            <input type="text" x-model="search" placeholder="Search projects..."
                                class="w-full sm:w-[220px] bg-white border border-gray-100 focus:border-titan-red focus:ring-1 focus:ring-titan-red pl-11 pr-4 py-3 rounded-xl text-[12px] font-medium text-titan-navy transition-all placeholder:text-gray-400" />
                        </div>
                    </div>
                </div>

                <!-- Standard Grid - Clean & Professional UX -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <template x-for="project in filteredProjects" :key="project.id">
                        <div class="animate-fade-in-up h-full">
                            <a :href="'/projects/' + project.id"
                                class="group block relative bg-white rounded-2xl overflow-hidden shadow-sm border border-gray-100 flex flex-col hover:-translate-y-2 hover:shadow-2xl transition-all duration-500 h-full">

                                <!-- Thumbnail Area - Uniform Aspect Ratio -->
                                <div class="relative w-full aspect-[16/10] overflow-hidden bg-gray-100 shrink-0">
                                    <img :src="project.image" :alt="project.title"
                                        class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" />
                                    <div
                                        class="absolute inset-0 bg-titan-navy/0 group-hover:bg-titan-navy/10 transition-colors duration-500">
                                    </div>

                                    <!-- Status Overlay -->
                                    <div class="absolute top-4 left-4 z-20">
                                        <span
                                            class="px-3 py-1.5 backdrop-blur-md rounded border border-white/20 text-white text-[9px] font-black tracking-widest uppercase shadow-lg"
                                            :class="project.status === statusOptions[1] ? 'bg-green-600/90' : 'bg-amber-600/90'">
                                            <span x-text="project.status"></span>
                                        </span>
                                    </div>
                                </div>

                                <!-- Content Block - Clean Typography -->
                                <div class="p-8 flex flex-col flex-1">
                                    <!-- Red Accent Bar -->
                                    <div class="w-8 h-1 bg-titan-red mb-5 group-hover:w-12 transition-all duration-300">
                                    </div>

                                    <h3 class="text-xl font-black text-titan-navy leading-tight mb-2 group-hover:text-titan-red transition-colors uppercase tracking-tight"
                                        x-text="project.title"></h3>
                                    <p class="text-titan-navy/40 text-[10px] font-black uppercase tracking-widest mb-4"
                                        x-text="project.type"></p>

                                    <p class="text-gray-500 text-sm leading-relaxed mb-6 font-medium line-clamp-2"
                                        x-text="project.summary"></p>

                                    <!-- Footer Meta -->
                                    <div
                                        class="mt-auto pt-5 border-t border-gray-100 flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <x-lucide-map-pin class="w-3.5 h-3.5 text-titan-red/70" />
                                            <span
                                                class="text-[10px] font-black uppercase tracking-widest text-titan-navy/60"
                                                x-text="project.location"></span>
                                        </div>
                                        <div
                                            class="text-[10px] font-black uppercase tracking-widest flex items-center gap-2 text-titan-navy group-hover:text-titan-red transition-colors">
                                            {{ __('View Details') }}
                                            <x-lucide-arrow-right
                                                class="w-3 h-3 group-hover:translate-x-1 transition-transform" />
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </template>
                </div>

                <!-- Empty State -->
                <div x-show="filteredProjects.length === 0" style="display: none;"
                    class="text-center py-40 bg-gray-50 rounded-[4rem] border border-dashed border-gray-100">
                    <x-lucide-building class="w-12 h-12 text-titan-navy/10 mx-auto mb-8" />
                    <h3 class="text-2xl font-black text-titan-navy mb-4 uppercase tracking-tighter">
                        {{ __('No Built Legacy Found') }}
                    </h3>
                    <p class="text-titan-navy/40 text-sm max-w-sm mx-auto leading-relaxed">
                        {{ __('Refine your search parameters to explore other successful Kimmex deliveries.') }}
                    </p>
                </div>
            </div>
        </section>
    </div>
    </section>

    </div>

    <style>
        @keyframes superSlowPan {
            0% {
                transform: scale(1.05) translate(0, 0);
            }

            100% {
                transform: scale(1.1) translate(-2%, 2%);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.7s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
    </style>
</x-layouts.app>