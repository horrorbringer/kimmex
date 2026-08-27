<x-layouts.app :title="__('News & Updates')" :description="__('Read the latest news, updates, and announcements from Kimmex.')">

    <div class="min-h-screen bg-slate-50 text-slate-800 pt-28 md:pt-32"
         x-data="{
            activeCategory: {{ Js::from(request('category', 'all')) }},
            searchQuery: '',
            perPage: {{ $perPage }},
            visible: {{ $perPage }},
            allArticles: {{ Js::from($allArticles) }},
            get filtered() {
                let list = this.allArticles;
                if (this.activeCategory && this.activeCategory !== 'all') {
                    const target = String(this.activeCategory).toLowerCase();
                    list = list.filter(a =>
                        String(a.category).toLowerCase() === target ||
                        String(a.categorySlug).toLowerCase() === target
                    );
                }
                if (this.searchQuery && this.searchQuery.trim() !== '') {
                    const query = this.searchQuery.toLowerCase().trim();
                    list = list.filter(a =>
                        String(a.title).toLowerCase().includes(query) ||
                        String(a.category).toLowerCase().includes(query)
                    );
                }
                return list;
            },
            get shown() { return this.filtered.slice(0, this.visible); },
            get hasMore() { return this.visible < this.filtered.length; },
            loadMore() { this.visible += this.perPage; },
            setCategory(cat) { 
                this.activeCategory = cat; 
                this.visible = this.perPage; 
            },
            isCategoryActive(cat) {
                if (cat === 'all') return !this.activeCategory || this.activeCategory === 'all';
                const target = String(this.activeCategory).toLowerCase();
                return String(cat).toLowerCase() === target;
            },
            resetFilters() {
                this.activeCategory = 'all';
                this.searchQuery = '';
                this.visible = this.perPage;
            }
         }">

        <!-- ═══ MINIMAL PAGE TITLE & BREADCRUMB ═══ -->
        <div class="max-w-[1360px] mx-auto px-6 mb-4">
            <nav class="flex items-center gap-2 text-xs mb-2 text-slate-400 font-medium">
                <a href="/" class="hover:text-titan-red transition-colors">{{ __('Home') }}</a>
                <x-lucide-chevron-right class="w-3.5 h-3.5 text-slate-300" />
                <span class="text-titan-navy font-semibold">{{ __('News') }}</span>
            </nav>
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-2">
                <h1 class="font-heading font-black uppercase leading-tight tracking-tight text-titan-navy text-2xl sm:text-3xl md:text-4xl">
                    {{ __('News') }} <span class="text-titan-red">{{ __('& Updates') }}</span>
                </h1>
                <div class="text-xs font-semibold text-slate-500">
                    <span class="font-black text-titan-navy text-base" x-text="filtered.length"></span> {{ __('articles available') }}
                </div>
            </div>
        </div>

        <!-- ═══ ONE-LINE NON-STICKY FILTER & SEARCH TOOLBAR ═══ -->
        <section class="max-w-[1360px] mx-auto px-6 my-6">
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs p-2.5 sm:p-3">
                <div class="flex flex-row items-center justify-between gap-3">
                    <!-- Category Filter Buttons (Horizontal Scrollable) -->
                    <div class="flex items-center gap-2 overflow-x-auto no-scrollbar flex-1 min-w-0 pr-2">
                        <button @click="setCategory('all')"
                            :class="isCategoryActive('all') ? 'bg-titan-navy text-white shadow-xs border-titan-navy' : 'bg-slate-100 text-slate-600 border-slate-200 hover:bg-slate-200 hover:text-slate-900'"
                            class="h-9 px-3.5 rounded-xl border text-xs font-bold transition-all shrink-0 whitespace-nowrap inline-flex items-center gap-1.5 cursor-pointer">
                            <span>{{ __('All') }}</span>
                            <span :class="isCategoryActive('all') ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-600'" class="text-[10px] font-black px-1.5 py-0.5 rounded-full">
                                {{ count($allArticles) }}
                            </span>
                        </button>

                        @foreach($categories as $cat)
                            <button @click="setCategory(@js($cat))"
                                :class="isCategoryActive(@js($cat)) ? 'bg-titan-navy text-white shadow-xs border-titan-navy' : 'bg-slate-100 text-slate-600 border-slate-200 hover:bg-slate-200 hover:text-slate-900'"
                                class="h-9 px-3.5 rounded-xl border text-xs font-bold transition-all shrink-0 whitespace-nowrap inline-flex items-center gap-1.5 cursor-pointer">
                                <span>{{ $cat }}</span>
                                <span :class="isCategoryActive(@js($cat)) ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-600'" class="text-[10px] font-black px-1.5 py-0.5 rounded-full">
                                    {{ $categoryCounts[$cat] ?? 0 }}
                                </span>
                            </button>
                        @endforeach
                    </div>

                    <!-- Compact Search Input -->
                    <div class="relative w-48 sm:w-64 md:w-72 shrink-0">
                        <x-lucide-search class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" />
                        <input type="text"
                               x-model.debounce.250ms="searchQuery"
                               placeholder="{{ __('Search news...') }}"
                               class="w-full h-9 pl-9 pr-8 text-xs font-medium rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-titan-red focus:ring-1 focus:ring-titan-red outline-hidden transition-all text-slate-800 placeholder-slate-400" />
                        <button x-show="searchQuery"
                                @click="searchQuery = ''"
                                class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 p-0.5">
                            <x-lucide-x class="w-3.5 h-3.5" />
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- ═══ FULL WIDTH ONLY-GRID NEWS SECTION ═══ -->
        <main class="max-w-[1360px] mx-auto px-6 pb-16">
            
            <!-- Result Count & Active Filter Indicator -->
            <div class="flex items-center justify-between gap-4 mb-5">
                <div class="text-xs font-semibold text-slate-500">
                    {{ __('Showing') }} <span class="font-bold text-slate-900" x-text="shown.length"></span> {{ __('of') }} <span class="font-bold text-slate-900" x-text="filtered.length"></span> {{ __('stories') }}
                    <span x-show="searchQuery" class="ml-1 text-slate-400">
                        ({{ __('matching') }} "<span class="text-titan-red font-medium" x-text="searchQuery"></span>")
                    </span>
                </div>

                <div x-show="activeCategory !== 'all' || searchQuery" class="flex items-center gap-2">
                    <button @click="resetFilters()" class="text-xs font-bold text-titan-red hover:underline inline-flex items-center gap-1 cursor-pointer">
                        <x-lucide-rotate-ccw class="w-3 h-3" />
                        <span>{{ __('Reset Filters') }}</span>
                    </button>
                </div>
            </div>

            <!-- Responsive 3-Column News Grid (Clean Visual Tile with Smooth Hover Metadata) -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <template x-for="article in shown" :key="article.slug">
                    <a :href="'/news/' + article.slug"
                       class="group relative aspect-[4/3] sm:aspect-[16/11] overflow-hidden rounded-2xl border border-slate-200/80 bg-slate-900 shadow-xs hover:shadow-2xl transition-all duration-500 ease-out block hover:-translate-y-1.5">
                        <!-- Cover Image (Bright, Natural & Crisp) -->
                        <img :src="article.image" :alt="article.title"
                             class="w-full h-full object-fit group-hover:scale-106 transition-transform duration-700 ease-out"
                             loading="lazy" decoding="async" />
                        
                        <!-- Soft Natural Bottom Gradient Overlay -->
                        <div class="absolute inset-0 pointer-events-none transition-opacity duration-500 ease-out"
                             style="background: linear-gradient(180deg, rgba(0,0,0,0) 0%, rgba(0,0,0,0.02) 40%, rgba(0,0,0,0.45) 75%, rgba(0,0,0,0.78) 100%);"></div>

                        <!-- Card Content Overlaid at Bottom -->
                        <div class="absolute inset-0 p-5 flex flex-col justify-end pointer-events-none">
                            <!-- Category Pill & Date Row -->
                            <div class="flex items-center justify-between gap-2 mb-1.5">
                                <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider px-2.5 py-0.5 rounded-md shadow-xs text-white bg-titan-red"
                                      x-text="article.category"></span>
                                <span class="text-[10px] sm:text-[11px] font-semibold uppercase tracking-wider text-white/90"
                                      style="text-shadow: 0 1px 2px rgba(0,0,0,0.75);"
                                      x-text="article.dateUpper || article.date"></span>
                            </div>

                            <!-- Refined Headline Title (Smaller & Clean) -->
                            <h2 class="!text-[11px] sm:!text-xs md:!text-[13px] font-heading font-bold !text-white leading-snug line-clamp-2 group-hover:!text-amber-300 transition-colors duration-300"
                                style="color: #ffffff !important; text-shadow: 0 1px 3px rgba(0,0,0,0.85);"
                                x-text="article.title"></h2>

                            <!-- Author & Meta Stats Row (Smoothly Reveals on Hover) -->
                            <div class="flex items-center gap-2 pt-2 border-t border-white/20 text-xs text-white/90 opacity-0 max-h-0 overflow-hidden group-hover:opacity-100 group-hover:max-h-12 group-hover:mt-2 transition-all duration-500 ease-out">
                                <div class="w-5 h-5 rounded-full bg-white/25 border border-white/40 flex items-center justify-center text-[9px] font-bold text-white uppercase shrink-0"
                                     x-text="(article.authorName || 'K').charAt(0)"></div>
                                <span class="text-xs font-medium text-white/90 truncate max-w-[130px]" x-text="article.authorName || 'Kimmex'"></span>
                                <span class="text-white/40 text-[10px]">•</span>
                                <span class="text-[11px] text-white/75 font-medium" x-text="article.readTime || '3 min read'"></span>
                                
                                <x-lucide-arrow-up-right class="w-4 h-4 text-white/70 group-hover:text-white group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-all duration-300 ml-auto shrink-0" />
                            </div>
                        </div>
                    </a>
                </template>
            </div>

            <!-- Empty State -->
            <div x-show="filtered.length === 0" class="py-20 text-center bg-white border border-dashed border-slate-200 rounded-2xl mt-4">
                <div class="w-14 h-14 mx-auto mb-4 rounded-full bg-slate-100 flex items-center justify-center text-slate-400">
                    <x-lucide-newspaper class="w-7 h-7" />
                </div>
                <h3 class="text-base font-bold text-slate-800 mb-1">{{ __('No matching articles found') }}</h3>
                <p class="text-xs text-slate-500 max-w-sm mx-auto mb-5">{{ __('Try selecting a different category or clearing your search term.') }}</p>
                <button @click="resetFilters()"
                        class="h-9 px-5 rounded-lg bg-titan-navy text-white text-xs font-bold hover:bg-titan-red transition-colors cursor-pointer inline-flex items-center gap-1.5">
                    <x-lucide-rotate-ccw class="w-3 h-3" />
                    <span>{{ __('Show All Articles') }}</span>
                </button>
            </div>

            <!-- Load More Action -->
            <div x-show="hasMore" class="mt-12 text-center">
                <button @click="loadMore()"
                    class="inline-flex items-center gap-2 h-11 px-8 rounded-xl border border-slate-300 bg-white text-slate-800 text-sm font-bold hover:bg-titan-navy hover:text-white hover:border-titan-navy transition-all duration-300 shadow-xs cursor-pointer">
                    <x-lucide-plus class="w-4 h-4" />
                    <span>{{ __('Load More Articles') }}</span>
                </button>
                <p class="text-xs font-medium text-slate-400 mt-2.5">
                    {{ __('Showing') }} <span class="font-bold text-slate-700" x-text="shown.length"></span> {{ __('of') }} <span class="font-bold text-slate-700" x-text="filtered.length"></span> {{ __('articles') }}
                </p>
            </div>

        </main>
    </div>

    <style>
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>

</x-layouts.app>
