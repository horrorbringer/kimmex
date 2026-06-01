<x-layouts.app title="Projects"
    description="View Kimmex's portfolio of successful construction and engineering projects.">

    @php
        $locale = app()->getLocale();
        $isKhmer = $locale === 'km';
        $allTypesLabel = $isKhmer ? __('All Types') : __('All Types');
        $allLocationsLabel = $isKhmer ? __('All Locations') : __('All Locations');
        $allStatusLabel = $isKhmer ? __('All Status') : __('All Status');
        $fallbackImage = '/images/projects/Thumbnail-5.jpg';
        $cachedData = \Illuminate\Support\Facades\Cache::remember("projects_index_data_{$locale}", now()->addHours(12), function() use ($fallbackImage, $locale) {
            $projectsDb = \App\Models\Project::where('isActive', true)->with('projectCategory')
                ->orderBy('created_at', 'desc')
                ->get();
            $projectCategories = \App\Models\ProjectCategory::where('isActive', true)->get();
            $categoryLookup = $projectCategories->flatMap(function ($category) {
                return [
                    strtolower($category->slug) => $category,
                    strtolower(\Illuminate\Support\Str::slug($category->getTranslation('name', 'en', false))) => $category,
                ];
            });
            $localizedCategoryName = function ($project) use ($locale, $categoryLookup) {
                if ($project->projectCategory) {
                    return $project->projectCategory->localizedName($locale);
                }

                $legacyCategory = trim((string) $project->category);
                $legacyKey = strtolower(\Illuminate\Support\Str::slug($legacyCategory));
                $matchedCategory = $categoryLookup->get(strtolower($legacyCategory)) ?: $categoryLookup->get($legacyKey);

                return $matchedCategory
                    ? $matchedCategory->localizedName($locale)
                    : ($legacyCategory ? __(\Illuminate\Support\Str::headline($legacyCategory)) : __('General'));
            };

            // Dynamically build filter lists
            $categories = $projectsDb->map($localizedCategoryName)->unique()->values()->prepend(__('All'))->toArray();

            $locations = $projectsDb->map(fn($p) => $p->getTranslation('location', $locale))
                ->unique()->values()->prepend(__('All'))->toArray();

            $statusOptions = collect(\App\Enums\ProjectStatus::cases())->map(fn($s) => $s->getLabel())->prepend(__('All'))->toArray();

            $projects = $projectsDb->map(function ($p) use ($fallbackImage, $locale, $localizedCategoryName) {
                /** @var \App\Models\Project $p */
                return [
                    'id' => $p->slug,
                    'title' => $p->getTranslation('title', $locale),
                    'featured' => (bool) $p->isFeatured,
                    'location' => $p->getTranslation('location', $locale),
                    'type' => $localizedCategoryName($p),
                    'status' => $p->status ? $p->status->getLabel() : __('Unknown'),
                    'image' => ($p->heroImage && (\Illuminate\Support\Str::startsWith($p->heroImage, '/') ? file_exists(public_path($p->heroImage)) : \Illuminate\Support\Facades\Storage::disk('public')->exists($p->heroImage)))
                        ? (\Illuminate\Support\Str::startsWith($p->heroImage, '/') ? $p->heroImage : \Illuminate\Support\Facades\Storage::url($p->heroImage))
                        : $fallbackImage,
                    'summary' => strip_tags($p->getTranslation('description', $locale)),
                ];
            })->toArray();

            return compact('categories', 'locations', 'statusOptions', 'projects');
        });

        $categories = $cachedData['categories'];
        $locations = $cachedData['locations'];
        $statusOptions = $cachedData['statusOptions'];
        $projects = $cachedData['projects'];

        // Fallback for empty DB
        if (count($projects) === 0) {
            $projects = [
                ['id' => 'mef', 'title' => __('Ministry of Economy Building'), 'featured' => true, 'location' => __('Phnom Penh'), 'type' => __('Government'), 'status' => __('Completed'), 'image' => '/images/projects/Thumbnail-1.jpg', 'summary' => __('Kimmex built legacy facility.')]
            ];
        }
    @endphp

    <div x-data="{
        filterType: 'All',
        filterStatus: 'All',
        filterLoc: 'All',
        allTypesLabel: {{ Js::from($allTypesLabel) }},
        allLocationsLabel: {{ Js::from($allLocationsLabel) }},
        allStatusLabel: {{ Js::from($allStatusLabel) }},
        search: '',
        sortBy: 'featured',
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

        clearFilters() {
            this.filterType = 'All';
            this.filterStatus = this.statusOptions[0];
            this.filterLoc = this.locations[0];
            this.search = '';
            this.sortBy = 'featured';
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
            }).sort((a, b) => {
                if (this.sortBy === 'featured') {
                    if ((a.featured ? 1 : 0) !== (b.featured ? 1 : 0)) {
                        return (b.featured ? 1 : 0) - (a.featured ? 1 : 0);
                    }
                    return a.title.localeCompare(b.title);
                }

                if (this.sortBy === 'title') {
                    return a.title.localeCompare(b.title);
                }

                if (this.sortBy === 'status') {
                    return a.status.localeCompare(b.status) || a.title.localeCompare(b.title);
                }

                return a.title.localeCompare(b.title);
            });
        },

        get activeCount() {
            return this.filteredProjects.length;
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

        <!-- === PREMIUM PROJECTS HERO === -->
        <section class="relative h-[60vh] min-h-[500px] flex items-center justify-center overflow-hidden bg-titan-navy">
            {{-- Background Zoom Animation --}}
            <div class="absolute inset-0">
                <img src="/images/projects/Thumbnail-5.jpg" alt="Kimmex Built Legacy" class="w-full h-full object-cover opacity-100 animate-slow-zoom" />
                {{-- Lightened multi-stage gradient --}}
                <div class="absolute inset-0 bg-gradient-to-b from-titan-navy/40 via-transparent to-titan-navy/70"></div>
            </div>

            <!-- Decorative Elements -->
            <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')]"></div>

            <div class="relative z-20 text-center px-6 max-w-7xl pt-24" x-data="{ shown: false }" x-init="setTimeout(() => shown = true, 100)">


                <h1 :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'"
                    class="transition-all duration-1000 delay-300 font-heading font-[900] text-white mb-8 leading-[0.9] tracking-tighter uppercase"
                    style="font-size: clamp(2rem, 5vw, 3.5rem) !important; color: white !important; font-weight: 900 !important;">
                    {{ __('BUILT') }} <span class="text-titan-red">{{ __('LEGACY') }}</span>
                </h1>

                <div :class="shown ? 'opacity-100' : 'opacity-0'" class="transition-all duration-1000 delay-500 flex items-center justify-center gap-6 mb-16">
                    <div class="h-[1px] w-12 bg-white/30"></div>
                    <p class="text-[10px] md:text-xs text-white/90 font-bold uppercase tracking-[0.4em]">
                        {{ __('Architecting the future through engineering precision.') }}
                    </p>
                    <div class="h-[1px] w-12 bg-white/30"></div>
                </div>

                <!-- Scroll Indicator -->
                <div :class="shown ? 'opacity-100' : 'opacity-0'" class="transition-all duration-1000 delay-700 flex flex-col items-center gap-4 cursor-pointer group"
                    onclick="document.getElementById('portfolio-grid').scrollIntoView({ behavior: 'smooth' })">
                    <span class="text-[9px] uppercase tracking-[0.4em] font-bold text-white/40 group-hover:text-titan-red transition-colors">{{ __('Scroll') }}</span>
                    <div class="w-[1px] h-16 bg-gradient-to-b from-titan-red to-transparent"></div>
                </div>
            </div>

        </section>

        <!-- INTEGRATED FILTER & GRID -->
        <section id="portfolio-grid" class="py-20 px-6 bg-white relative">
            <div class="max-w-[1700px] mx-auto">

                <!-- Filter Bar -->
                <div class="sticky top-16 z-30 mb-10 rounded border border-gray-200 bg-white/95 backdrop-blur shadow-sm">
                    <div class="border-b border-gray-100 px-4 py-3 md:px-5">
                        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                            <div>
                                <div class="{{ $isKhmer ? 'text-[11px] font-khmer normal-case tracking-normal leading-tight' : 'text-[10px] font-bold uppercase tracking-[0.22em]' }} text-titan-red">{{ __('Projects') }}</div>
                                <div class="mt-1 {{ $isKhmer ? 'text-sm font-khmer normal-case tracking-normal leading-tight' : 'text-sm font-bold' }} text-titan-navy">
                                    <span x-text="activeCount"></span> {{ __('projects found') }}
                                </div>
                            </div>

                            <div class="flex flex-wrap items-center gap-2">
                                <button
                                    @click="clearFilters()"
                                    class="inline-flex items-center gap-2 rounded border border-gray-200 bg-white px-3 py-2 {{ $isKhmer ? 'text-[11px] font-khmer normal-case tracking-normal leading-tight' : 'text-[10px] font-bold uppercase tracking-[0.18em]' }} text-titan-navy hover:border-titan-red/30 hover:text-titan-red transition-colors">
                                    <x-lucide-rotate-ccw class="w-3.5 h-3.5" />
                                    {{ __('Clear filters') }}
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="px-4 py-4 md:px-5">
                        <nav class="flex gap-2 overflow-x-auto no-scrollbar pb-1">
                            <template x-for="type in categories" :key="type">
                                <button
                                    @click="filterType = type"
                                    class="shrink-0 rounded-full border px-3.5 py-2 min-h-10 {{ $isKhmer ? 'text-[11px] font-khmer normal-case tracking-normal leading-tight' : 'text-[10px] font-bold uppercase tracking-[0.18em]' }} transition-all duration-300"
                                    :class="filterType === type ? 'border-titan-red bg-titan-red text-white shadow-sm' : 'border-gray-200 bg-white text-titan-navy/55 hover:border-titan-red/30 hover:text-titan-navy'">
                                    <span x-text="type === 'All' ? allTypesLabel : type"></span>
                                </button>
                            </template>
                        </nav>

                        <div class="mt-4 grid grid-cols-1 gap-3 md:grid-cols-2 lg:grid-cols-[1.2fr_0.8fr_0.8fr_0.7fr]">
                            <label class="relative block">
                                <span class="sr-only">{{ __('Search projects') }}</span>
                                <x-lucide-search class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-titan-navy/30" />
                                <input type="text" x-model="search" placeholder="Search projects..."
                                    class="w-full rounded border border-gray-200 bg-white py-3 pl-11 pr-4 {{ $isKhmer ? 'text-[12px] font-khmer normal-case tracking-normal leading-tight' : 'text-[12px] font-normal' }} text-titan-navy transition-colors placeholder:text-gray-400 focus:border-titan-red focus:outline-none focus:ring-1 focus:ring-titan-red/20" />
                            </label>

                            <div class="relative">
                                <select x-model="filterLoc"
                                    class="appearance-none w-full rounded border border-gray-200 bg-white px-4 py-3 pr-10 {{ $isKhmer ? 'text-[11px] font-khmer normal-case tracking-normal leading-tight' : 'text-[10px] font-bold uppercase tracking-[0.18em]' }} text-titan-navy transition-colors cursor-pointer focus:outline-none focus:border-titan-red focus:ring-1 focus:ring-titan-red/20">
                                    <template x-for="loc in locations" :key="loc">
                                        <option :value="loc" x-text="loc === 'All' ? allLocationsLabel : loc"></option>
                                    </template>
                                </select>
                                <x-lucide-chevron-down class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-titan-navy/40" />
                            </div>

                            <div class="relative">
                                <select x-model="filterStatus"
                                    class="appearance-none w-full rounded border border-gray-200 bg-white px-4 py-3 pr-10 {{ $isKhmer ? 'text-[11px] font-khmer normal-case tracking-normal leading-tight' : 'text-[10px] font-bold uppercase tracking-[0.18em]' }} text-titan-navy transition-colors cursor-pointer focus:outline-none focus:border-titan-red focus:ring-1 focus:ring-titan-red/20">
                                    <template x-for="stat in statusOptions" :key="stat">
                                        <option :value="stat"
                                            x-text="stat === 'All' ? allStatusLabel : stat"></option>
                                    </template>
                                </select>
                                <x-lucide-chevron-down class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-titan-navy/40" />
                            </div>

                            <div class="relative">
                                <select x-model="sortBy"
                                    class="appearance-none w-full rounded border border-gray-200 bg-white px-4 py-3 pr-10 {{ $isKhmer ? 'text-[11px] font-khmer normal-case tracking-normal leading-tight' : 'text-[10px] font-bold uppercase tracking-[0.18em]' }} text-titan-navy transition-colors cursor-pointer focus:outline-none focus:border-titan-red focus:ring-1 focus:ring-titan-red/20">
                                    <option value="featured">{{ __('Featured') }}</option>
                                    <option value="title">{{ __('A-Z') }}</option>
                                    <option value="status">{{ __('Status') }}</option>
                                </select>
                                <x-lucide-chevron-down class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-titan-navy/40" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Standard Grid - Clean & Professional UX -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <template x-for="project in filteredProjects" :key="project.id">
                        <div class="animate-fade-in-up h-full">
                            <a :href="'/projects/' + project.id"
                                class="group block relative bg-white rounded overflow-hidden shadow-sm border border-gray-100 flex flex-col hover:-translate-y-2 hover:shadow-2xl transition-all duration-500 h-full">

                                <!-- Thumbnail Area - Uniform Aspect Ratio -->
                                <div class="relative w-full aspect-[16/10] overflow-hidden bg-gray-100 shrink-0">
                                    <img :src="project.image" :alt="project.title"
                                        class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" loading="lazy" />
                                    <div
                                        class="absolute inset-0 bg-titan-navy/0 group-hover:bg-titan-navy/10 transition-colors duration-500">
                                    </div>

                                </div>

                                <!-- Content Block - Clean Typography -->
                                <div class="p-8 flex flex-col flex-1">
                                    <!-- Red Accent Bar -->
                                    <div class="w-8 h-1 bg-titan-red mb-5 group-hover:w-12 transition-all duration-300">
                                    </div>

                                    <h3 class="text-xl font-bold text-titan-navy leading-tight mb-2 group-hover:text-titan-red transition-colors uppercase tracking-tight"
                                        x-text="project.title"></h3>
                                    <p class="text-titan-navy/40 text-[10px] font-bold uppercase tracking-widest mb-4"
                                        x-text="project.type"></p>

                                    <p class="text-gray-500 text-sm leading-relaxed mb-6 font-medium line-clamp-2"
                                        x-text="project.summary"></p>

                                    <!-- Footer Meta -->
                                    <div
                                        class="mt-auto pt-5 border-t border-gray-100 flex items-center justify-between">
                                        <div class="flex flex-wrap items-center gap-x-4 gap-y-2">
                                            <div class="flex items-center gap-2">
                                                <x-lucide-map-pin class="w-3.5 h-3.5 text-titan-red/70" />
                                                <span class="text-[10px] font-bold uppercase tracking-widest text-titan-navy/60" x-text="project.location"></span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <x-lucide-activity class="w-3.5 h-3.5 text-titan-red/70" />
                                                <span class="text-[10px] font-bold uppercase tracking-widest text-titan-navy/60" x-text="project.status"></span>
                                            </div>
                                        </div>                                        <div
                                        class="text-[10px] font-bold uppercase tracking-widest flex items-center gap-2 text-titan-navy group-hover:text-titan-red transition-colors">
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
                    class="text-center py-40 bg-gray-50 rounded border border-dashed border-gray-100">
                    <x-lucide-building class="w-12 h-12 text-titan-navy/10 mx-auto mb-8" />
                    <h3 class="text-2xl font-bold text-titan-navy mb-4 uppercase tracking-tighter">
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
