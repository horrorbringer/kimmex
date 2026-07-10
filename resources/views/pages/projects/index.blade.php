<x-layouts.app :title="__('Projects')"
    description="View Kimmex's portfolio of successful construction and engineering projects.">

    @php
        $locale = app()->getLocale();
        $isKhmer = $locale === 'km';
        $allTypesLabel = $isKhmer ? __('All Types') : __('All Types');
        $allLocationsLabel = $isKhmer ? __('All Locations') : __('All Locations');
        $allStatusLabel = $isKhmer ? __('All Status') : __('All Status');
        $fallbackImage = '/images/webp/projects/Thumbnail-5.webp';
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
                    'image' => \App\Support\PublicStorage::urlIfExists($p->heroImage, $fallbackImage),
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
                ['id' => 'mef', 'title' => __('Ministry of Economy Building'), 'featured' => true, 'location' => __('Phnom Penh'), 'type' => __('Government'), 'status' => __('Completed'), 'image' => '/images/webp/projects/Thumbnail-1.webp', 'summary' => __('Kimmex built legacy facility.')]
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

        <!-- HERO -->
        <section class="relative h-[320px] md:h-[380px] flex items-end overflow-hidden bg-titan-navy">
            <div class="absolute inset-0">
                <img src="/images/webp/projects/Thumbnail-5.webp" alt="Kimmex Projects" class="w-full h-full object-cover opacity-50" loading="eager" decoding="async" fetchpriority="high" />
                <div class="absolute inset-0 bg-gradient-to-t from-titan-navy/90 via-titan-navy/40 to-transparent"></div>
            </div>
            <div class="relative z-10 w-full max-w-[1200px] mx-auto px-6 pb-10 md:pb-12">
                <p class="text-[9px] font-black uppercase tracking-[0.35em] text-titan-red mb-2">{{ __('Kimmex') }}</p>
                <h1 class="font-black text-white uppercase leading-none"
                    style="font-size: clamp(1.6rem, 4vw, 2.6rem) !important; color: white !important; font-weight: 900 !important;">
                    {{ __('Our Projects') }}
                </h1>
                <p class="text-white/50 text-sm mt-2">{{ __('Architecting the future through engineering precision.') }}</p>
            </div>
        </section>

        <!-- FILTER & GRID -->
        <section id="portfolio-grid" class="py-10 md:py-14 px-6 bg-white">
            <div class="max-w-[1200px] mx-auto">

                <!-- Filter Bar -->
                <div class="sticky top-16 z-30 mb-8 bg-white border-b border-gray-100 pb-5">

                    {{-- Search row --}}
                    <div class="flex flex-col sm:flex-row gap-3 mb-5">
                        <div class="relative flex-grow">
                            <x-lucide-search class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-titan-navy/25" />
                            <input type="text" x-model="search" placeholder="{{ __('Search projects by name…') }}"
                                class="w-full h-11 pl-11 pr-4 rounded-lg border border-gray-200 bg-white text-sm font-semibold text-titan-navy placeholder:text-titan-navy/30 focus:outline-none focus:border-titan-red/40 focus:ring-2 focus:ring-titan-red/10 transition-all shadow-sm" />
                        </div>
                        <div class="flex gap-2">
                            <div class="relative">
                                <select x-model="filterLoc"
                                    class="appearance-none h-11 px-4 pr-9 rounded-lg border border-gray-200 bg-white text-xs font-bold text-titan-navy cursor-pointer focus:outline-none focus:border-titan-red/40 focus:ring-2 focus:ring-titan-red/10 transition-all shadow-sm">
                                    <template x-for="loc in locations" :key="loc">
                                        <option :value="loc" x-text="loc === 'All' ? allLocationsLabel : loc"></option>
                                    </template>
                                </select>
                                <x-lucide-map-pin class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-titan-red/50" />
                            </div>
                            <div class="relative">
                                <select x-model="filterStatus"
                                    class="appearance-none h-11 px-4 pr-9 rounded-lg border border-gray-200 bg-white text-xs font-bold text-titan-navy cursor-pointer focus:outline-none focus:border-titan-red/40 focus:ring-2 focus:ring-titan-red/10 transition-all shadow-sm">
                                    <template x-for="stat in statusOptions" :key="stat">
                                        <option :value="stat" x-text="stat === 'All' ? allStatusLabel : stat"></option>
                                    </template>
                                </select>
                                <x-lucide-activity class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-titan-red/50" />
                            </div>
                        </div>
                    </div>

                    {{-- Category tabs + count --}}
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div class="flex flex-wrap gap-2 overflow-x-auto">
                            <template x-for="type in categories" :key="type">
                                <button @click="filterType = type"
                                    class="h-8 px-4 rounded-full text-xs font-bold border transition-all duration-200 whitespace-nowrap"
                                    :class="filterType === type
                                        ? 'bg-titan-red text-white border-titan-red shadow-sm'
                                        : 'bg-gray-50 text-titan-navy/60 border-gray-200 hover:border-titan-red/30 hover:text-titan-red'">
                                    <span x-text="type === 'All' ? allTypesLabel : type"></span>
                                </button>
                            </template>
                        </div>
                        <div class="flex items-center gap-3 shrink-0">
                            <span class="text-xs font-bold text-titan-navy/35">
                                <span x-text="activeCount"></span> {{ __('projects') }}
                            </span>
                            <button @click="clearFilters()"
                                x-show="filterType !== 'All' || filterStatus !== statusOptions[0] || filterLoc !== locations[0] || search !== ''"
                                style="display:none"
                                class="inline-flex items-center gap-1.5 h-8 px-3 rounded-full border border-gray-200 text-xs font-bold text-titan-navy/50 hover:text-titan-red hover:border-titan-red/30 transition-colors">
                                <x-lucide-x class="w-3 h-3" />{{ __('Reset') }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <template x-for="project in filteredProjects" :key="project.id">
                        <a :href="'/projects/' + project.id"
                            class="group block bg-white rounded-lg overflow-hidden border border-gray-100 hover:border-titan-red/20 hover:shadow-[0_8px_24px_-8px_rgba(11,43,92,0.14)] transition-all duration-300">

                            <div class="relative w-full aspect-[16/10] overflow-hidden bg-gray-100">
                                <img :src="project.image" :alt="project.title"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy" decoding="async" />
                                <div class="absolute top-3 left-3">
                                    <span class="inline-flex h-5 px-2 rounded bg-white/90 backdrop-blur-sm border border-gray-200/50 text-[8px] font-black uppercase tracking-[0.15em] text-titan-navy/70 items-center" x-text="project.status"></span>
                                </div>
                            </div>

                            <div class="p-4 md:p-5">
                                <div class="text-[9px] font-black uppercase tracking-[0.2em] text-titan-red/60 mb-1.5" x-text="project.type"></div>
                                <h3 class="projects-title text-base font-black text-titan-navy leading-tight mb-2 group-hover:text-titan-red transition-colors uppercase tracking-tight" x-text="project.title"></h3>
                                <p class="text-xs text-titan-navy/45 leading-relaxed line-clamp-2 mb-3" x-text="project.summary"></p>
                                <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                                    <div class="flex items-center gap-1.5 text-[10px] font-semibold text-titan-navy/40">
                                        <x-lucide-map-pin class="w-3 h-3 text-titan-red/50" />
                                        <span x-text="project.location"></span>
                                    </div>
                                    <span class="text-[9px] font-black uppercase tracking-[0.15em] text-titan-navy/30 group-hover:text-titan-red transition-colors flex items-center gap-1">
                                        {{ __('View') }}
                                        <x-lucide-arrow-right class="w-3 h-3 group-hover:translate-x-0.5 transition-transform" />
                                    </span>
                                </div>
                            </div>
                        </a>
                    </template>
                </div>

                <!-- Empty State -->
                <div x-show="filteredProjects.length === 0" style="display: none;"
                    class="text-center py-20 bg-gray-50/50 rounded-lg border border-dashed border-gray-200 mt-4">
                    <x-lucide-building class="w-8 h-8 text-titan-navy/15 mx-auto mb-4" />
                    <p class="text-sm font-black text-titan-navy/30 uppercase tracking-widest mb-3">{{ __('No projects found') }}</p>
                    <p class="text-xs text-titan-navy/30 mb-5">{{ __('Try adjusting your filters.') }}</p>
                    <button @click="clearFilters()"
                        class="h-8 px-4 rounded-full bg-titan-navy text-white text-[9px] font-black uppercase tracking-[0.18em] hover:bg-titan-red transition-colors">
                        {{ __('Clear Filters') }}
                    </button>
                </div>
            </div>
        </section>
    </div>

</x-layouts.app>
