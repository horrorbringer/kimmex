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
        filterType: {{ Js::from($selectedCategoryName ?? 'All') }},
        filterStatus: {{ Js::from($status ?? '') }},
        filterYear: {{ Js::from($year ?? '') }},
        filterCategoryId: {{ Js::from($categoryId ?? '') }},
        filterCategorySlug: {{ Js::from($categorySlug ?? '') }},
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
            const categoryParam = params.get('category');
            if (categoryParam) {
                this.filterCategorySlug = categoryParam;
                // Find matching category name for tab highlight
                const match = this.categoryOptions.find(c => c.slug === categoryParam);
                if (match) {
                    this.filterCategoryId = match.id;
                    this.filterType = match.name;
                }
            }
        },

        applyServerFilters() {
            const params = new URLSearchParams();
            if (this.filterYear) params.set('year', this.filterYear);
            if (this.filterStatus) params.set('status', this.filterStatus);
            if (this.filterCategorySlug) params.set('category', this.filterCategorySlug);

            const qs = params.toString();
            window.location.href = '/projects' + (qs ? '?' + qs : '');
        },

        setCategory(type) {
            if (type === 'All') {
                this.filterType = 'All';
                this.filterCategoryId = '';
                this.filterCategorySlug = '';
            } else {
                this.filterType = type;
                const match = this.categoryOptions.find(c => c.name === type);
                this.filterCategoryId = match ? match.id : '';
                this.filterCategorySlug = match ? match.slug : '';
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
            this.filterCategorySlug = '';
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
                const getTime = (item) => item.completion_date ? new Date(item.completion_date).getTime() : 0;

                if (this.sortBy === 'featured') {
                    if ((a.featured ? 1 : 0) !== (b.featured ? 1 : 0)) {
                        return (b.featured ? 1 : 0) - (a.featured ? 1 : 0);
                    }
                    const diff = getTime(b) - getTime(a);
                    return diff !== 0 ? diff : a.title.localeCompare(b.title);
                }

                if (this.sortBy === 'date' || this.sortBy === 'completionDate') {
                    const diff = getTime(b) - getTime(a);
                    return diff !== 0 ? diff : a.title.localeCompare(b.title);
                }

                if (this.sortBy === 'title') {
                    return a.title.localeCompare(b.title);
                }

                if (this.sortBy === 'status') {
                    return a.status.localeCompare(b.status) || a.title.localeCompare(b.title);
                }

                const diff = getTime(b) - getTime(a);
                return diff !== 0 ? diff : a.title.localeCompare(b.title);
            });
        },

        get activeCount() {
            return this.filteredProjects.length;
        }
    }" class="bg-white min-h-screen text-titan-navy relative overflow-hidden">

        <!-- HERO -->
        <section class="relative h-[320px] md:h-[400px] flex items-end overflow-hidden bg-titan-navy">
            <div class="absolute inset-0">
                <img src="/images/webp/projects/Thumbnail-5.webp" alt="Kimmex Projects" class="w-full h-full object-cover opacity-70" loading="eager" decoding="async" fetchpriority="high" />
                <div class="absolute inset-0 bg-gradient-to-t from-titan-navy/75 via-titan-navy/35 to-transparent"></div>
                <div class="absolute inset-0 bg-gradient-to-r from-titan-navy/45 via-transparent to-transparent"></div>
            </div>
            <div class="relative z-10 w-full max-w-[1200px] mx-auto px-6 pb-10 md:pb-12">
                <p class="text-[9px] font-black uppercase tracking-[0.35em] text-titan-red mb-2">{{ __('Our Portfolio') }}</p>
                <h1 class="font-black text-white leading-tight tracking-tight drop-shadow-sm"
                    style="font-size: clamp(1.75rem, 4vw, 2.75rem) !important; color: white !important; font-weight: 900 !important;">
                    {{ __('Our Projects') }}
                </h1>
                <p class="text-white/75 text-sm md:text-base mt-2 max-w-2xl font-normal leading-relaxed">
                    {{ __('From government infrastructure to commercial developments, each project reflects our commitment to quality, safety, and timely delivery.') }}
                </p>
            </div>
        </section>

        <!-- FILTER & GRID -->
        <section id="portfolio-grid" class="py-8 md:py-12 px-6 bg-white">
            <div class="max-w-[1200px] mx-auto">

                <!-- Filter Bar -->
                <div class="sticky top-24 z-30 mb-10 pt-2 lg:top-28">
                    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
                        <div class="flex items-center justify-between gap-4 border-b border-slate-100 px-5 py-4 md:px-6">
                            <div class="flex items-center gap-3">
                                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-titan-red/10 text-titan-red">
                                    <x-lucide-sliders-horizontal class="h-4 w-4" />
                                </span>
                                <div>
                                    <p class="text-sm font-black text-titan-navy">{{ __('Filter Projects') }}</p>
                                    <p class="text-xs text-titan-navy/45"><span x-text="activeCount"></span> {{ __('projects found') }}</p>
                                </div>
                            </div>
                            <button @click="clearFilters()" x-show="hasActiveFilters" style="display: none"
                                class="inline-flex h-9 items-center gap-1.5 rounded-lg border border-titan-red/20 bg-titan-red/5 px-3 text-xs font-bold text-titan-red transition-colors hover:bg-titan-red hover:text-white">
                                <x-lucide-rotate-ccw class="h-3.5 w-3.5" />
                                {{ __('Reset filters') }}
                            </button>
                        </div>

                        <div class="grid gap-3 p-4 sm:grid-cols-2 md:p-5 lg:grid-cols-[minmax(0,2fr)_repeat(3,minmax(0,1fr))]">
                            <label class="relative block sm:col-span-2 lg:col-span-1">
                                <span class="sr-only">{{ __('Search projects') }}</span>
                                <x-lucide-search class="pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-titan-navy/35" />
                                <input type="search" x-model="search" placeholder="{{ __('Search projects by name…') }}"
                                    class="h-12 w-full rounded-xl border border-slate-200 bg-slate-50 pl-11 pr-4 text-sm font-semibold text-titan-navy placeholder:text-titan-navy/35 transition-all focus:border-titan-red/40 focus:bg-white focus:outline-none focus:ring-4 focus:ring-titan-red/10" />
                            </label>

                            <label class="relative block">
                                <span class="sr-only">{{ __('Year') }}</span>
                                <x-lucide-calendar class="pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-titan-red/65" />
                                <select @change="setYear($event.target.value)"
                                    class="h-12 w-full appearance-none rounded-xl border border-slate-200 bg-white px-11 pr-9 text-sm font-bold text-titan-navy transition-all focus:border-titan-red/40 focus:outline-none focus:ring-4 focus:ring-titan-red/10">
                                    <option value="" :selected="!filterYear">{{ __('All Years') }}</option>
                                    <template x-for="y in years" :key="y">
                                        <option :value="y" x-text="y" :selected="filterYear == y"></option>
                                    </template>
                                </select>
                                <x-lucide-chevron-down class="pointer-events-none absolute right-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-titan-navy/35" />
                            </label>

                            <label class="relative block">
                                <span class="sr-only">{{ __('Status') }}</span>
                                <x-lucide-activity class="pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-titan-red/65" />
                                <select @change="setStatus($event.target.value)"
                                    class="h-12 w-full appearance-none rounded-xl border border-slate-200 bg-white px-11 pr-9 text-sm font-bold text-titan-navy transition-all focus:border-titan-red/40 focus:outline-none focus:ring-4 focus:ring-titan-red/10">
                                    <option value="" :selected="!filterStatus">{{ __('All Status') }}</option>
                                    <template x-for="stat in statusOptions" :key="stat.value">
                                        <option :value="stat.value" x-text="stat.label" :selected="filterStatus === stat.value"></option>
                                    </template>
                                </select>
                                <x-lucide-chevron-down class="pointer-events-none absolute right-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-titan-navy/35" />
                            </label>

                            <label class="relative block">
                                <span class="sr-only">{{ __('Location') }}</span>
                                <x-lucide-map-pin class="pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-titan-red/65" />
                                <select x-model="filterLoc"
                                    class="h-12 w-full appearance-none rounded-xl border border-slate-200 bg-white px-11 pr-9 text-sm font-bold text-titan-navy transition-all focus:border-titan-red/40 focus:outline-none focus:ring-4 focus:ring-titan-red/10">
                                    <template x-for="loc in locations" :key="loc">
                                        <option :value="loc" x-text="loc === 'All' ? allLocationsLabel : loc"></option>
                                    </template>
                                </select>
                                <x-lucide-chevron-down class="pointer-events-none absolute right-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-titan-navy/35" />
                            </label>
                        </div>

                        <div class="border-t border-slate-100 px-4 py-4 md:px-5">
                            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                                <div class="flex min-w-0 gap-2 overflow-x-auto pb-1 [scrollbar-width:thin]">
                                    <template x-for="type in categories" :key="type">
                                        <button @click="setCategory(type)"
                                            class="h-9 shrink-0 rounded-full border px-4 text-xs font-bold transition-all duration-200"
                                            :class="filterType === type
                                                ? 'border-titan-red bg-titan-red text-white'
                                                : 'border-slate-200 bg-white text-titan-navy/65 hover:border-titan-red/35 hover:text-titan-red'">
                                            <span x-text="type === 'All' ? allTypesLabel : type"></span>
                                        </button>
                                    </template>
                                </div>
                                <div x-show="hasActiveFilters" x-transition class="flex shrink-0 items-center gap-2 text-xs font-semibold text-titan-navy/55">
                                    <x-lucide-filter class="h-3.5 w-3.5 text-titan-red" />
                                    <span>{{ __('Active filters') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <template x-for="project in filteredProjects" :key="project.id">
                        <a :href="'/projects/' + project.id"
                            class="group flex flex-col h-full bg-white rounded-lg overflow-hidden border border-gray-100 hover:border-titan-red/20 hover:shadow-[0_8px_24px_-8px_rgba(11,43,92,0.14)] transition-all duration-300">

                            <div class="relative w-full aspect-[16/10] overflow-hidden bg-gray-100 shrink-0">
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

                            <div class="p-4 md:p-5 flex flex-col justify-between flex-1">
                                <div>
                                    <div class="text-[9px] font-black uppercase tracking-[0.2em] text-titan-red/70 mb-1.5" x-text="project.type"></div>
                                    <h3 class="projects-title text-base font-bold text-titan-navy leading-snug group-hover:text-titan-red transition-colors tracking-tight line-clamp-2" x-text="project.title"></h3>
                                </div>
                                <div class="flex items-center justify-between pt-3 mt-3.5 border-t border-gray-100">
                                    <div class="flex items-center gap-1.5 text-xs font-semibold text-titan-navy/55 min-w-0 pr-2">
                                        <x-lucide-map-pin class="w-3.5 h-3.5 text-titan-red shrink-0" />
                                        <span class="truncate whitespace-nowrap" x-text="project.location"></span>
                                    </div>
                                    <span class="text-[10px] font-black uppercase tracking-wider text-titan-navy/35 group-hover:text-titan-red transition-colors flex items-center gap-1 shrink-0">
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
