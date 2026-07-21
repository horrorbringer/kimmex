<x-layouts.app :title="__('Projects')"
    description="View Kimmex's portfolio of successful construction and engineering projects.">

    @php
        $locale = app()->getLocale();
        $isKhmer = $locale === 'km';
        $allTypesLabel = __('All Types');
        $allLocationsLabel = __('All Locations');
        $allStatusLabel = __('All Status');
        $allYearsLabel = __('All Years');
        $fallbackImage = '/images/webp/projects/Thumbnail-5.webp';
    @endphp

    <div x-data="{
        filterType: '{{ old('category_id', $categoryId ?? '') }}' ? '' : 'All',
        filterStatus: {{ Js::from($status ?? '') }},
        filterYear: {{ Js::from($year ?? '') }},
        filterCategoryId: {{ Js::from($categoryId ?? '') }},
        filterLoc: 'All',
        allTypesLabel: {{ Js::from($allTypesLabel) }},
        allLocationsLabel: {{ Js::from($allLocationsLabel) }},
        allStatusLabel: {{ Js::from($allStatusLabel) }},
        allYearsLabel: {{ Js::from($allYearsLabel) }},
        search: '',
        sortBy: 'featured',
        projects: {{ Js::from($projects) }},
        categories: {{ Js::from(array_merge(['All'], $categories)) }},
        locations: {{ Js::from(array_merge([__('All')], $locations)) }},
        statusOptions: {{ Js::from($statusOptions) }},
        categoryOptions: {{ Js::from($categoryOptions) }},
        years: {{ Js::from($years) }},

        init() {
            const params = new URLSearchParams(window.location.search);

            // Sync status from URL
            const statusParam = params.get('status');
            if (statusParam) {
                this.filterStatus = statusParam;
            }

            // Sync year from URL
            const yearParam = params.get('year');
            if (yearParam) {
                this.filterYear = yearParam;
            }

            // Sync category from URL
            const categoryParam = params.get('category_id');
            if (categoryParam) {
                this.filterCategoryId = categoryParam;
                // Find matching category name for tab highlight
                const match = this.categoryOptions.find(c => c.id === categoryParam);
                if (match) {
                    this.filterType = match.name;
                }
            }
        },

        applyServerFilters() {
            const params = new URLSearchParams();
            if (this.filterYear) params.set('year', this.filterYear);
            if (this.filterStatus) params.set('status', this.filterStatus);
            if (this.filterCategoryId) params.set('category_id', this.filterCategoryId);

            const qs = params.toString();
            window.location.href = '/projects' + (qs ? '?' + qs : '');
        },

        setCategory(type) {
            if (type === 'All') {
                this.filterType = 'All';
                this.filterCategoryId = '';
            } else {
                this.filterType = type;
                const match = this.categoryOptions.find(c => c.name === type);
                this.filterCategoryId = match ? match.id : '';
            }
            this.applyServerFilters();
        },

        setStatus(value) {
            this.filterStatus = value;
            this.applyServerFilters();
        },

        setYear(value) {
            this.filterYear = value;
            this.applyServerFilters();
        },

        clearFilters() {
            this.filterType = 'All';
            this.filterStatus = '';
            this.filterYear = '';
            this.filterCategoryId = '';
            this.filterLoc = 'All';
            this.search = '';
            this.sortBy = 'featured';
            window.location.href = '/projects';
        },

        get hasActiveFilters() {
            return this.filterStatus !== '' || this.filterYear !== '' || this.filterCategoryId !== '' || this.filterLoc !== 'All' || this.search !== '';
        },

        get filteredProjects() {
            const allLocLabel = this.locations[0];

            return this.projects.filter(p => {
                const isAllLoc = (this.filterLoc === 'All' || this.filterLoc === allLocLabel);
                const matchLoc = isAllLoc || p.location === this.filterLoc;

                const query = this.search.toLowerCase();
                const matchSearch = query === '' ||
                                   p.title.toLowerCase().includes(query) ||
                                   p.summary.toLowerCase().includes(query);

                return matchLoc && matchSearch;
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
        <section id="portfolio-grid" class="py-14 md:py-20 px-6 bg-white">
            <div class="max-w-[1200px] mx-auto">

                <!-- Section Header -->
                <div x-data="{ shown: false }" x-intersect.once="shown = true"
                    :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                    class="transition-all duration-1000 mb-10">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-[2px]" style="background: var(--primary-color, #E31E24);"></div>
                        <span class="font-bold uppercase tracking-[0.2em] text-xs" style="color: var(--primary-color, #E31E24);">{{ __('Our Portfolio') }}</span>
                    </div>
                    <h2 class="text-3xl md:text-4xl font-heading font-black text-gray-900 tracking-tight mb-4">{{ __('Projects Delivered') }}</h2>
                    <p class="text-gray-500 text-sm md:text-base max-w-2xl leading-relaxed">
                        {{ __('From government infrastructure to commercial developments, each project reflects our commitment to quality, safety, and timely delivery.') }}
                    </p>
                </div>

                <!-- Filter Bar -->
                <div class="sticky top-16 z-30 mb-8 bg-white border-b border-gray-100 pb-5">

                    {{-- Search row --}}
                    <div class="flex flex-col sm:flex-row gap-3 mb-5">
                        <div class="relative flex-grow">
                            <x-lucide-search class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-titan-navy/25" />
                            <input type="text" x-model="search" placeholder="{{ __('Search projects by name…') }}"
                                class="w-full h-11 pl-11 pr-4 rounded-lg border border-gray-200 bg-white text-sm font-semibold text-titan-navy placeholder:text-titan-navy/30 focus:outline-none focus:border-titan-red/40 focus:ring-2 focus:ring-titan-red/10 transition-all shadow-sm" />
                        </div>
                        <div class="flex gap-2 flex-wrap">
                            {{-- Year Filter --}}
                            <div class="relative">
                                <select @change="setYear($event.target.value)"
                                    class="appearance-none h-11 px-4 pr-9 rounded-lg border border-gray-200 bg-white text-xs font-bold text-titan-navy cursor-pointer focus:outline-none focus:border-titan-red/40 focus:ring-2 focus:ring-titan-red/10 transition-all shadow-sm">
                                    <option value="" :selected="!filterYear">{{ __('All Years') }}</option>
                                    <template x-for="y in years" :key="y">
                                        <option :value="y" x-text="y" :selected="filterYear == y"></option>
                                    </template>
                                </select>
                                <x-lucide-calendar class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-titan-red/50" />
                            </div>
                            {{-- Location Filter --}}
                            <div class="relative">
                                <select x-model="filterLoc"
                                    class="appearance-none h-11 px-4 pr-9 rounded-lg border border-gray-200 bg-white text-xs font-bold text-titan-navy cursor-pointer focus:outline-none focus:border-titan-red/40 focus:ring-2 focus:ring-titan-red/10 transition-all shadow-sm">
                                    <template x-for="loc in locations" :key="loc">
                                        <option :value="loc" x-text="loc === 'All' ? allLocationsLabel : loc"></option>
                                    </template>
                                </select>
                                <x-lucide-map-pin class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-titan-red/50" />
                            </div>
                            {{-- Status Filter --}}
                            <div class="relative">
                                <select @change="setStatus($event.target.value)"
                                    class="appearance-none h-11 px-4 pr-9 rounded-lg border border-gray-200 bg-white text-xs font-bold text-titan-navy cursor-pointer focus:outline-none focus:border-titan-red/40 focus:ring-2 focus:ring-titan-red/10 transition-all shadow-sm">
                                    <option value="" :selected="!filterStatus">{{ __('All Status') }}</option>
                                    <template x-for="stat in statusOptions" :key="stat.value">
                                        <option :value="stat.value" x-text="stat.label" :selected="filterStatus === stat.value"></option>
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
                                <button @click="setCategory(type)"
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
                                x-show="hasActiveFilters"
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
                                <template x-if="project.year">
                                    <div class="absolute top-3 right-3">
                                        <span class="inline-flex h-5 px-2 rounded bg-white/90 backdrop-blur-sm border border-gray-200/50 text-[8px] font-black uppercase tracking-[0.15em] text-titan-navy/70 items-center" x-text="project.year"></span>
                                    </div>
                                </template>
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
