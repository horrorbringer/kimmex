<x-layouts.app :title="__('News & Updates')" :description="__('Read the latest news, updates, and announcements from Kimmex.')">

    @php
        $locale        = app()->getLocale();
        $fallbackImage = '/images/webp/hero/hero-3.webp';
        $perPage       = 9;

        $newsArticles = \Illuminate\Support\Facades\Cache::remember("news_index_data_{$locale}", now()->addHours(12), function () use ($locale, $fallbackImage) {
            return \App\Models\NewsArticle::where('isActive', true)
                ->where('publishedAt', '<=', now())
                ->orderByDesc('isFeatured')
                ->orderByDesc('publishedAt')
                ->get()
                ->map(function ($n) use ($locale, $fallbackImage) {
                    $excerpt = $n->getTranslation('excerpt', $locale)
                        ?: \Illuminate\Support\Str::limit(strip_tags($n->getTranslation('content', $locale)), 160);
                    return [
                        'slug'       => $n->slug,
                        'category'   => $n->getTranslation('category', $locale) ?: __('Updates'),
                        'image'      => \App\Support\PublicStorage::urlIfExists($n->coverImage, $fallbackImage),
                        'title'      => $n->getTranslation('title', $locale),
                        'date'       => $n->publishedAt ? $n->publishedAt->format('M d, Y') : $n->created_at->format('M d, Y'),
                        'excerpt'    => $excerpt,
                        'isFeatured' => (bool) $n->isFeatured,
                    ];
                })->toArray();
        });

        $allArticles   = collect($newsArticles);
        $featured      = $allArticles->first();
        $gridArticles  = $allArticles->slice(1)->values();
        $totalArticles = count($newsArticles);

        $categoryCounts = collect($newsArticles)->groupBy('category')->map->count();
        $categories     = array_values(array_unique(array_column($newsArticles, 'category')));

        $sidebarHeadlines = $gridArticles->slice(0, 4)->values();

        $sidebarDocs = \Illuminate\Support\Facades\Cache::remember("news_sidebar_documents_{$locale}", now()->addHours(12), function () use ($locale) {
            return \App\Models\Document::with('documentCategory')->publiclyVisible()->latest()->take(3)->get()
                ->map(fn($d) => [
                    'slug'     => $d->slug,
                    'title'    => $d->getTranslation('title', $locale),
                    'category' => $d->documentCategory ? $d->documentCategory->getTranslation('name', $locale) : ($d->category ?: __('Documents')),
                    'fileType' => $d->fileType ?: 'PDF',
                    'fileSize' => $d->fileSize,
                ])->toArray();
        });

        $sidebarJobs = \Illuminate\Support\Facades\Cache::remember("news_sidebar_jobs_{$locale}", now()->addHours(12), function () use ($locale) {
            return \App\Models\JobPosting::where('status', \App\Enums\JobPostingStatus::OPEN)->with('department')->orderByDesc('created_at')->take(3)->get()
                ->map(fn($j) => [
                    'slug'     => $j->slug,
                    'title'    => $j->getTranslation('title', $locale),
                    'dept'     => $j->department ? $j->department->getTranslation('name', $locale) : __('General'),
                    'location' => $j->getTranslation('location', $locale),
                    'type'     => __(str_replace('_', ' ', \Illuminate\Support\Str::title(strtolower($j->type ?? 'FULL_TIME')))),
                ])->toArray();
        });
    @endphp


    <div class="min-h-screen bg-gray-50"
         x-data="{
            activeCategory: 'all',
            perPage: {{ $perPage }},
            visible: {{ $perPage }},
            allArticles: {{ Js::from($gridArticles) }},
            get filtered() {
                if (this.activeCategory === 'all') return this.allArticles;
                return this.allArticles.filter(a => a.category === this.activeCategory);
            },
            get shown() { return this.filtered.slice(0, this.visible); },
            get hasMore() { return this.visible < this.filtered.length; },
            loadMore() { this.visible += this.perPage; },
            setCategory(cat) { this.activeCategory = cat; this.visible = this.perPage; }
         }">

        <!-- ═══ HERO ═══ -->
        <section class="relative h-[380px] md:h-[440px] flex items-end overflow-hidden" style="background: #0B2B5C;">
            <div class="absolute inset-0">
                <img src="/images/webp/hero/hero-3.webp" alt="{{ __('News & Updates') }}" class="w-full h-full object-cover opacity-40" loading="eager" decoding="async" fetchpriority="high" />
                <div class="absolute inset-0 bg-gradient-to-t from-[#071A33]/95 via-[#0B2B5C]/50 to-transparent"></div>
                <div class="absolute inset-0 bg-gradient-to-r from-[#071A33]/50 via-transparent to-transparent"></div>
            </div>
            <div class="relative z-10 w-full max-w-[1280px] mx-auto px-6 pb-12 md:pb-16">
                <nav class="flex items-center gap-2 text-xs mb-5" style="color: rgba(255,255,255,0.5);">
                    <a href="/" class="hover:text-white transition-colors">{{ __('Home') }}</a>
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                    <span style="color: rgba(255,255,255,0.9);">{{ __('News') }}</span>
                </nav>
                <h1 class="font-heading font-[900] uppercase leading-[1] tracking-tight mb-4"
                    style="font-size: clamp(2rem, 5vw, 3.2rem); color: #FFFFFF;">
                    {{ __('News') }} <span style="color: var(--primary-color, #E31E24);">{{ __('& Updates') }}</span>
                </h1>
                <p style="color: rgba(255,255,255,0.6); font-size: 1rem;" class="max-w-lg leading-relaxed">
                    {{ __('Company announcements, project milestones, and industry insights.') }}
                </p>
                <div class="flex items-center gap-5 mt-6">
                    <div class="text-center">
                        <div class="text-xl font-black" style="color: #FFFFFF;">{{ $totalArticles }}</div>
                        <div class="text-[9px] font-bold uppercase tracking-wider" style="color: rgba(255,255,255,0.35);">{{ __('Articles') }}</div>
                    </div>
                    <div class="w-px h-8" style="background: rgba(255,255,255,0.15);"></div>
                    <div class="text-center">
                        <div class="text-xl font-black" style="color: #FFFFFF;">{{ count($categories) }}</div>
                        <div class="text-[9px] font-bold uppercase tracking-wider" style="color: rgba(255,255,255,0.35);">{{ __('Categories') }}</div>
                    </div>
                </div>
            </div>
        </section>


        <!-- ═══ FEATURED ARTICLE ═══ -->
        @if($featured)
        <section class="max-w-[1280px] mx-auto px-6 -mt-8 relative z-20">
            <a href="/news/{{ $featured['slug'] }}"
               class="group relative block rounded-2xl overflow-hidden shadow-[0_20px_60px_-15px_rgba(0,0,0,0.2)] hover:shadow-[0_30px_80px_-15px_rgba(0,0,0,0.25)] transition-all duration-500"
               style="min-height: 340px; background: #0B2B5C;">
                <div class="absolute inset-0">
                    <img src="{{ $featured['image'] }}" alt="{{ $featured['title'] }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-[1.2s]"
                         loading="eager" decoding="async" />
                    <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/50 to-black/20"></div>
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                </div>
                <div class="relative z-10 flex flex-col justify-end h-full p-7 md:p-12" style="min-height: 340px;">
                    <div class="max-w-2xl">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="text-[10px] font-bold uppercase tracking-wider px-3 py-1 rounded-md" style="background: var(--primary-color, #E31E24); color: #FFFFFF;">
                                {{ $featured['category'] }}
                            </span>
                            <span class="text-xs font-medium" style="color: rgba(255,255,255,0.5);">{{ $featured['date'] }}</span>
                        </div>
                        <h2 class="text-2xl md:text-3xl font-heading font-black leading-tight mb-4" style="color: #FFFFFF;">
                            {{ $featured['title'] }}
                        </h2>
                        <p class="text-sm md:text-base leading-relaxed line-clamp-2 mb-6 hidden sm:block" style="color: rgba(255,255,255,0.7);">
                            {{ $featured['excerpt'] }}
                        </p>
                        <div class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-wider group-hover:gap-3 transition-all" style="color: var(--primary-color, #E31E24);">
                            {{ __('Read Full Story') }}
                            <x-lucide-arrow-right class="w-4 h-4" />
                        </div>
                    </div>
                </div>
            </a>
        </section>
        @endif


        <!-- ═══ MAIN CONTENT: GRID + SIDEBAR ═══ -->
        <section class="max-w-[1280px] mx-auto px-6 py-12 md:py-16">
            <div class="grid grid-cols-1 lg:grid-cols-[1fr_300px] gap-8 lg:gap-10 items-start">

                <!-- LEFT: Articles -->
                <div>
                    <!-- Category Filters -->
                    <div class="flex items-center gap-2 overflow-x-auto no-scrollbar pb-1 mb-8">
                        <button @click="setCategory('all')"
                            :class="activeCategory === 'all' ? 'bg-gray-900 text-white border-gray-900' : 'bg-white text-gray-500 border-gray-200 hover:border-gray-300'"
                            class="h-9 px-4 rounded-lg border text-xs font-bold transition-all shrink-0 whitespace-nowrap">
                            {{ __('All') }} <span class="opacity-50 ml-1">({{ count($gridArticles) }})</span>
                        </button>
                        @foreach($categories as $cat)
                            <button @click="setCategory(@js($cat))"
                                :class="activeCategory === @js($cat) ? 'bg-gray-900 text-white border-gray-900' : 'bg-white text-gray-500 border-gray-200 hover:border-gray-300'"
                                class="h-9 px-4 rounded-lg border text-xs font-bold transition-all shrink-0 whitespace-nowrap">
                                {{ $cat }} <span class="opacity-50 ml-1">({{ $categoryCounts[$cat] ?? 0 }})</span>
                            </button>
                        @endforeach
                    </div>

                    <!-- Article Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <template x-for="article in shown" :key="article.slug">
                            <a :href="'/news/' + article.slug"
                               class="group bg-white rounded-xl border border-gray-100 overflow-hidden hover:shadow-lg hover:border-gray-200 transition-all duration-300 flex flex-col">
                                <div class="relative aspect-[16/10] overflow-hidden bg-gray-100 shrink-0">
                                    <img :src="article.image" :alt="article.title"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
                                         loading="lazy" decoding="async" />
                                    <div class="absolute top-3 left-3">
                                        <span class="text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-md backdrop-blur-sm"
                                              style="background: rgba(0,0,0,0.6); color: #FFFFFF;"
                                              x-text="article.category"></span>
                                    </div>
                                </div>
                                <div class="p-5 flex flex-col flex-1">
                                    <div class="text-[11px] font-medium text-gray-400 mb-2" x-text="article.date"></div>
                                    <h3 class="text-base font-bold text-gray-900 leading-snug line-clamp-2 group-hover:text-titan-red transition-colors mb-3 flex-1"
                                        x-text="article.title"></h3>
                                    <p class="text-xs text-gray-400 leading-relaxed line-clamp-2 mb-4"
                                       x-text="article.excerpt"></p>
                                    <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider group-hover:gap-3 transition-all"
                                         style="color: var(--primary-color, #E31E24);">
                                        {{ __('Read More') }}
                                        <x-lucide-arrow-right class="w-3.5 h-3.5" />
                                    </div>
                                </div>
                            </a>
                        </template>
                    </div>

                    <!-- Empty State -->
                    <div x-show="filtered.length === 0" class="py-16 text-center bg-white border border-dashed border-gray-200 rounded-xl mt-4">
                        <x-lucide-newspaper class="w-10 h-10 text-gray-200 mx-auto mb-3" />
                        <p class="text-sm text-gray-400">{{ __('No articles in this category.') }}</p>
                    </div>

                    <!-- Load More -->
                    <div x-show="hasMore" class="mt-10 text-center">
                        <button @click="loadMore()"
                            class="inline-flex items-center gap-2 h-11 px-8 rounded-xl border border-gray-200 bg-white text-gray-700 text-sm font-bold hover:bg-gray-900 hover:text-white hover:border-gray-900 transition-all duration-300">
                            <x-lucide-plus class="w-4 h-4" />
                            {{ __('Load More') }}
                        </button>
                        <p class="text-xs text-gray-400 mt-2">
                            <span x-text="shown.length"></span> / <span x-text="filtered.length"></span> {{ __('articles') }}
                        </p>
                    </div>
                </div>


                <!-- RIGHT: Sidebar -->
                <aside class="space-y-6 lg:sticky lg:top-28">

                    <!-- Latest Headlines -->
                    <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
                        <div class="px-5 py-4 border-b border-gray-100">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-900">{{ __('Latest Stories') }}</h3>
                        </div>
                        <div class="divide-y divide-gray-50">
                            @forelse($sidebarHeadlines as $h)
                                <a href="/news/{{ $h['slug'] }}" class="group flex items-center gap-3 p-4 hover:bg-gray-50 transition-colors">
                                    <div class="w-14 h-14 rounded-lg overflow-hidden bg-gray-100 shrink-0">
                                        <img src="{{ $h['image'] }}" alt="{{ $h['title'] }}"
                                             class="w-full h-full object-cover" loading="lazy" decoding="async" />
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-bold text-gray-900 line-clamp-2 group-hover:text-titan-red transition-colors leading-snug">{{ $h['title'] }}</p>
                                        <p class="text-[10px] text-gray-400 mt-1">{{ $h['date'] }}</p>
                                    </div>
                                </a>
                            @empty
                                <div class="p-5 text-xs text-gray-400">{{ __('No stories yet.') }}</div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Documents -->
                    @if(!empty($sidebarDocs))
                    <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
                        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-900">{{ __('Documents') }}</h3>
                            <a href="/documents" class="text-[10px] font-bold text-gray-400 hover:text-titan-red transition-colors">{{ __('View All') }}</a>
                        </div>
                        <div class="divide-y divide-gray-50">
                            @foreach($sidebarDocs as $doc)
                                <a href="/documents/{{ $doc['slug'] }}" class="group flex items-center gap-3 p-4 hover:bg-gray-50 transition-colors">
                                    <div class="w-10 h-10 rounded-lg bg-gray-50 border border-gray-100 flex items-center justify-center shrink-0">
                                        <x-lucide-file-text class="w-4 h-4 text-gray-300 group-hover:text-titan-red transition-colors" />
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-bold text-gray-900 line-clamp-1 group-hover:text-titan-red transition-colors">{{ $doc['title'] }}</p>
                                        <p class="text-[10px] text-gray-400 mt-0.5">{{ $doc['category'] }} @if($doc['fileSize'])· {{ $doc['fileSize'] }}@endif</p>
                                    </div>
                                    <x-lucide-download class="w-3.5 h-3.5 text-gray-300 group-hover:text-titan-red transition-colors shrink-0" />
                                </a>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Careers -->
                    @if(!empty($sidebarJobs))
                    <div class="rounded-xl overflow-hidden" style="background: #071A33;">
                        <div class="px-5 py-4" style="border-bottom: 1px solid rgba(255,255,255,0.08);">
                            <div class="flex items-center justify-between">
                                <h3 class="text-xs font-bold uppercase tracking-wider" style="color: #FFFFFF;">{{ __('We\'re Hiring') }}</h3>
                                <a href="/careers" class="text-[10px] font-bold transition-colors" style="color: rgba(255,255,255,0.4);">{{ __('All Jobs') }}</a>
                            </div>
                        </div>
                        <div style="border-color: rgba(255,255,255,0.05);" class="divide-y">
                            @foreach($sidebarJobs as $job)
                                <a href="/careers/{{ $job['slug'] }}" class="group flex items-center gap-3 p-4 transition-colors" style="--tw-divide-opacity: 0.05;">
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0" style="background: rgba(255,255,255,0.05);">
                                        <x-lucide-briefcase class="w-4 h-4" style="color: var(--primary-color, #E31E24);" />
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-bold line-clamp-1 transition-colors" style="color: rgba(255,255,255,0.85);">{{ $job['title'] }}</p>
                                        <p class="text-[10px] mt-0.5" style="color: rgba(255,255,255,0.35);">{{ $job['dept'] }} · {{ $job['type'] }}</p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                        <div class="p-4">
                            <a href="/careers"
                               class="flex items-center justify-center gap-2 w-full h-10 rounded-lg text-xs font-bold uppercase tracking-wider transition-colors"
                               style="background: var(--primary-color, #E31E24); color: #FFFFFF;">
                                <x-lucide-send class="w-3.5 h-3.5" />
                                {{ __('View All Positions') }}
                            </a>
                        </div>
                    </div>
                    @endif
                </aside>
            </div>
        </section>
    </div>

    <style>
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>

</x-layouts.app>
