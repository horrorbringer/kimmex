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
        $gridArticles  = $allArticles->slice(1)->values();  // all except featured → go in grid
        $totalArticles = count($newsArticles);

        // Categories with counts
        $categoryCounts = collect($newsArticles)->groupBy('category')->map->count();
        $categories     = array_values(array_unique(array_column($newsArticles, 'category')));

        // Sidebar: pick articles NOT already shown (skip featured + first 2 grid)
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
            return \App\Models\JobPosting::where('isActive', true)->with('department')->orderByDesc('created_at')->take(3)->get()
                ->map(fn($j) => [
                    'slug'     => $j->slug,
                    'title'    => $j->getTranslation('title', $locale),
                    'dept'     => $j->department ? $j->department->getTranslation('name', $locale) : __('General'),
                    'location' => $j->getTranslation('location', $locale),
                    'type'     => __(str_replace('_', ' ', \Illuminate\Support\Str::title(strtolower($j->type ?? 'FULL_TIME')))),
                ])->toArray();
        });
    @endphp

    <div class="min-h-screen text-titan-navy"
         style="background-color: {{ \App\Models\SystemSetting::get('theme_settings', [])['news_page_bg_color'] ?? '#F7F8FA' }}"
         x-data="{
            activeCategory: 'all',
            perPage: {{ $perPage }},
            visible: {{ $perPage }},
            allArticles: {{ Js::from($gridArticles) }},
            get categories() {
                const cats = [...new Set(this.allArticles.map(a => a.category))];
                return cats;
            },
            get filtered() {
                if (this.activeCategory === 'all') return this.allArticles;
                return this.allArticles.filter(a => a.category === this.activeCategory);
            },
            get shown() {
                return this.filtered.slice(0, this.visible);
            },
            get hasMore() {
                return this.visible < this.filtered.length;
            },
            loadMore() {
                this.visible += this.perPage;
            },
            setCategory(cat) {
                this.activeCategory = cat;
                this.visible = this.perPage;
            }
         }">

        {{-- ══════════════════════════════════════
             PAGE HEADER
        ══════════════════════════════════════ --}}
        <section class="bg-titan-navy-lighter border-b border-white/10 pt-28 pb-8">
            <div class="max-w-[1240px] mx-auto px-4 md:px-6">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-1 h-4 bg-titan-red rounded-full"></div>
                    <span class="text-[9px] font-black uppercase tracking-[0.3em] text-white/40">{{ __('Kimmex Newsroom') }}</span>
                </div>
                <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
                    <div>
                        <h1 class="font-black uppercase leading-[0.93] text-white"
                            style="font-size: clamp(1.75rem, 4vw, 3rem);">
                            {{ __('News') }} <span class="text-titan-red">{{ __('& Updates') }}</span>
                        </h1>
                        <p class="text-white/40 text-sm mt-2 max-w-lg leading-relaxed">
                            {{ __('Company announcements, project milestones, and industry updates from Kimmex.') }}
                        </p>
                    </div>
                    <div class="flex items-center gap-5 shrink-0">
                        <div class="text-center">
                            <div class="text-xl font-black text-white">{{ $totalArticles }}</div>
                            <div class="text-[8px] font-black uppercase tracking-[0.2em] text-white/30">{{ __('Stories') }}</div>
                        </div>
                        <div class="w-px h-7 bg-white/20"></div>
                        <a href="/documents" class="text-center hover:opacity-60 transition-opacity">
                            <div class="text-xl font-black text-white">{{ count($sidebarDocs) }}</div>
                            <div class="text-[8px] font-black uppercase tracking-[0.2em] text-white/30">{{ __('Documents') }}</div>
                        </a>
                        <div class="w-px h-7 bg-white/20"></div>
                        <a href="/careers" class="text-center hover:opacity-60 transition-opacity">
                            <div class="text-xl font-black text-white">{{ count($sidebarJobs) }}</div>
                            <div class="text-[8px] font-black uppercase tracking-[0.2em] text-white/30">{{ __('Careers') }}</div>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        {{-- ══════════════════════════════════════
             FEATURED ARTICLE — full-width hero card
        ══════════════════════════════════════ --}}
        @if($featured)
        <section class="max-w-[1240px] mx-auto px-4 md:px-6 pt-8 md:pt-10">
            <a href="/news/{{ $featured['slug'] }}"
               class="group relative block rounded-2xl overflow-hidden bg-titan-navy shadow-xl hover:shadow-2xl transition-shadow duration-500"
               style="min-height: 420px;">

                {{-- Cover image --}}
                <div class="absolute inset-0">
                    <img src="{{ $featured['image'] }}" alt="{{ $featured['title'] }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-[1.5s]"
                         loading="eager" decoding="async" fetchpriority="high" />
                    <div class="absolute inset-0 bg-gradient-to-r from-titan-navy/90 via-titan-navy/60 to-titan-navy/20"></div>
                    <div class="absolute inset-0 bg-gradient-to-t from-titan-navy/80 via-transparent to-transparent"></div>
                </div>

                {{-- Content --}}
                <div class="relative z-10 flex flex-col justify-end h-full p-6 md:p-10 lg:p-14" style="min-height: 420px;">
                    <div class="max-w-2xl">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="bg-titan-red text-white text-[8px] font-black uppercase tracking-[0.2em] px-3 py-1.5 rounded">
                                {{ $featured['category'] }}
                            </span>
                            <span class="text-[9px] font-bold text-titan-red/90 uppercase tracking-[0.16em]">
                                {{ __('Featured') }} · {{ $featured['date'] }}
                            </span>
                        </div>
                        <h2 class="font-black !text-white leading-tight mb-4 group-hover:!text-titan-red transition-colors"
                            style="font-size: clamp(1.5rem, 3.5vw, 2.5rem);">
                            {{ $featured['title'] }}
                        </h2>
                        <p class="text-white/85 text-sm md:text-base leading-relaxed line-clamp-2 mb-6 hidden sm:block font-normal">
                            {{ $featured['excerpt'] }}
                        </p>
                        <div class="inline-flex items-center gap-2 bg-titan-red hover:bg-red-700 border border-titan-red text-white px-4 py-2.5 rounded-lg text-[10px] font-black uppercase tracking-[0.2em] transition-colors">
                            {{ __('Read Full Story') }}
                            <x-lucide-arrow-right class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" />
                        </div>
                    </div>
                </div>
            </a>
        </section>
        @endif

        {{-- ══════════════════════════════════════
             GRID + SIDEBAR
        ══════════════════════════════════════ --}}
        <section class="max-w-[1240px] mx-auto px-4 md:px-6 py-8 md:py-12">
            <div class="grid grid-cols-1 lg:grid-cols-[1fr_296px] gap-8 items-start">

                {{-- LEFT --}}
                <div>
                    {{-- Sticky filter bar --}}
                    <div class="sticky top-20 z-30 backdrop-blur-sm border-b border-gray-200 -mx-4 md:mx-0 px-4 md:px-0 py-3 mb-6 bg-white/90">
                        <div class="flex items-center gap-2 overflow-x-auto no-scrollbar">
                            {{-- All button --}}
                            <button @click="setCategory('all')"
                                :class="activeCategory === 'all'
                                    ? 'bg-titan-navy text-white border-titan-navy'
                                    : 'bg-white text-titan-navy/50 border-gray-200 hover:border-titan-red/30 hover:text-titan-navy'"
                                class="h-8 px-3 rounded border text-[9px] font-black uppercase tracking-[0.16em] transition-all shrink-0 whitespace-nowrap flex items-center gap-1.5">
                                {{ __('All') }}
                                <span class="opacity-50 font-bold">({{ count($gridArticles) }})</span>
                            </button>

                            {{-- Per-category buttons --}}
                            @foreach($categories as $cat)
                                <button @click="setCategory(@js($cat))"
                                    :class="activeCategory === @js($cat)
                                        ? 'bg-titan-navy text-white border-titan-navy'
                                        : 'bg-white text-titan-navy/50 border-gray-200 hover:border-titan-red/30 hover:text-titan-navy'"
                                    class="h-8 px-3 rounded border text-[9px] font-black uppercase tracking-[0.16em] transition-all shrink-0 whitespace-nowrap flex items-center gap-1.5">
                                    {{ $cat }}
                                    <span class="opacity-50 font-bold">({{ $categoryCounts[$cat] ?? 0 }})</span>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Grid --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                        <template x-for="article in shown" :key="article.slug">
                            <a :href="'/news/' + article.slug"
                               class="group bg-white border border-gray-200 rounded-xl overflow-hidden hover:shadow-lg hover:border-titan-red/20 transition-all duration-300 flex flex-col">
                                <div class="relative aspect-[16/9] overflow-hidden bg-gray-100 shrink-0">
                                    <template x-if="article.image">
                                        <img :src="article.image" :alt="article.title"
                                             class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                                             loading="lazy" decoding="async" />
                                    </template>
                                    <template x-if="!article.image">
                                        <div class="absolute inset-0 flex items-center justify-center bg-gray-50">
                                            <x-lucide-newspaper class="w-8 h-8 text-gray-200" />
                                        </div>
                                    </template>
                                    <div class="absolute top-3 left-3">
                                        <span class="bg-titan-navy/80 backdrop-blur-sm text-white text-[8px] font-black uppercase tracking-[0.16em] px-2 py-1 rounded"
                                              x-text="article.category"></span>
                                    </div>
                                </div>
                                <div class="p-4 flex flex-col flex-1">
                                    <div class="text-[9px] font-bold text-titan-navy/25 uppercase tracking-[0.16em] mb-2" x-text="article.date"></div>
                                    <h3 class="text-sm font-black text-titan-navy leading-snug line-clamp-2 group-hover:text-titan-red transition-colors mb-2 flex-1"
                                        x-text="article.title"></h3>
                                    <p class="text-[11px] text-titan-navy/45 leading-relaxed line-clamp-2 font-normal mb-3"
                                       x-text="article.excerpt"></p>
                                    <div class="pt-3 border-t border-gray-100 flex items-center justify-between">
                                        <span class="text-[9px] font-black uppercase tracking-[0.18em] text-titan-red">{{ __('Read More') }}</span>
                                        <x-lucide-arrow-right class="w-3.5 h-3.5 text-titan-red group-hover:translate-x-1 transition-transform" />
                                    </div>
                                </div>
                            </a>
                        </template>
                    </div>

                    {{-- Empty state --}}
                    <div x-show="filtered.length === 0"
                         class="py-16 text-center bg-white border border-dashed border-gray-200 rounded-xl mt-2">
                        <x-lucide-newspaper class="w-10 h-10 text-titan-navy/10 mx-auto mb-3" />
                        <p class="text-[10px] font-black uppercase tracking-[0.26em] text-titan-navy/30">
                            {{ __('No articles in this category') }}
                        </p>
                    </div>

                    {{-- Load More --}}
                    <div x-show="hasMore" class="mt-8 flex flex-col items-center gap-2">
                        <button @click="loadMore()"
                            class="h-11 px-8 rounded-lg border border-gray-200 bg-white text-titan-navy text-[10px] font-black uppercase tracking-[0.2em] hover:bg-titan-navy hover:text-white hover:border-titan-navy transition-all duration-300 flex items-center gap-2">
                            <x-lucide-plus class="w-3.5 h-3.5" />
                            {{ __('Load More Stories') }}
                        </button>
                        <p class="text-[9px] text-titan-navy/30 font-bold uppercase tracking-[0.16em]">
                            <span x-text="shown.length"></span> / <span x-text="filtered.length"></span> {{ __('stories') }}
                        </p>
                    </div>
                </div>

                {{-- RIGHT: sidebar --}}
                <aside class="space-y-4 lg:sticky lg:top-24">

                    {{-- Latest Headlines — deduplicated from grid, not featured --}}
                    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                        <div class="px-4 pt-4 pb-3 border-b border-gray-100">
                            <div class="text-[10px] font-black uppercase tracking-[0.24em] text-titan-red">{{ __('Latest Stories') }}</div>
                        </div>
                        <div class="divide-y divide-gray-100">
                            @forelse($sidebarHeadlines as $h)
                                <a href="/news/{{ $h['slug'] }}" class="group flex items-center hover:bg-gray-50 transition-colors">
                                    <div class="w-16 h-14 shrink-0 overflow-hidden bg-gray-100">
                                        <img src="{{ $h['image'] }}" alt="{{ $h['title'] }}"
                                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                             loading="lazy" decoding="async" />
                                    </div>
                                    <div class="flex-1 min-w-0 px-3 py-2">
                                        <div class="text-[8px] font-black uppercase tracking-[0.14em] text-titan-red leading-none mb-1">{{ $h['category'] }}</div>
                                        <div class="text-[11px] font-bold text-titan-navy line-clamp-1 group-hover:text-titan-red transition-colors">{{ $h['title'] }}</div>
                                        <div class="text-[9px] text-titan-navy/30 mt-0.5">{{ $h['date'] }}</div>
                                    </div>
                                    <div class="pr-3 shrink-0">
                                        <x-lucide-chevron-right class="w-3.5 h-3.5 text-gray-300 group-hover:text-titan-red transition-colors" />
                                    </div>
                                </a>
                            @empty
                                <div class="px-4 py-5 text-xs text-titan-navy/35">{{ __('No stories yet.') }}</div>
                            @endforelse
                        </div>
                    </div>

                    {{-- Documents --}}
                    @if(!empty($sidebarDocs))
                    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                        <div class="px-4 pt-4 pb-3 border-b border-gray-100 flex items-center justify-between">
                            <div class="text-[10px] font-black uppercase tracking-[0.24em] text-titan-red">{{ __('Documents') }}</div>
                            <a href="/documents" class="text-[9px] font-black uppercase tracking-[0.14em] text-titan-navy/30 hover:text-titan-red transition-colors">{{ __('All') }} →</a>
                        </div>
                        <div class="divide-y divide-gray-100">
                            @foreach($sidebarDocs as $doc)
                                <a href="/documents/{{ $doc['slug'] }}" class="group flex items-center hover:bg-gray-50 transition-colors">
                                    <div class="w-16 h-14 shrink-0 flex flex-col items-center justify-center bg-gray-50 border-r border-gray-100 gap-0.5">
                                        <x-lucide-file-text class="w-4 h-4 text-gray-300 group-hover:text-titan-red transition-colors" />
                                        <span class="text-[7px] font-black uppercase tracking-wider text-gray-300 group-hover:text-titan-red transition-colors">{{ strtoupper($doc['fileType'] ?? 'PDF') }}</span>
                                    </div>
                                    <div class="flex-1 min-w-0 px-3 py-2">
                                        <div class="text-[8px] font-black uppercase tracking-[0.14em] text-titan-red leading-none mb-1">{{ $doc['category'] }}</div>
                                        <div class="text-[11px] font-bold text-titan-navy line-clamp-1 group-hover:text-titan-red transition-colors">{{ $doc['title'] }}</div>
                                        @if($doc['fileSize'])<div class="text-[9px] text-titan-navy/30 mt-0.5">{{ $doc['fileSize'] }}</div>@endif
                                    </div>
                                    <div class="pr-3 shrink-0">
                                        <x-lucide-download class="w-3.5 h-3.5 text-gray-300 group-hover:text-titan-red transition-colors" />
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Careers --}}
                    @if(!empty($sidebarJobs))
                    <div class="bg-titan-navy rounded-xl overflow-hidden">
                        <div class="px-4 pt-4 pb-3 border-b border-white/10 flex items-center justify-between">
                            <div class="text-[10px] font-black uppercase tracking-[0.24em] text-titan-red">{{ __('Careers') }}</div>
                            <a href="/careers" class="text-[9px] font-black uppercase tracking-[0.14em] text-white/25 hover:text-titan-red transition-colors">{{ __('All') }} →</a>
                        </div>
                        <div class="divide-y divide-white/[0.06]">
                            @foreach($sidebarJobs as $job)
                                <a href="/careers/{{ $job['slug'] }}" class="group flex items-center hover:bg-white/5 transition-colors">
                                    <div class="w-16 h-14 shrink-0 flex items-center justify-center border-r border-white/[0.06]">
                                        <x-lucide-briefcase class="w-4 h-4 text-titan-red/60 group-hover:text-titan-red transition-colors" />
                                    </div>
                                    <div class="flex-1 min-w-0 px-3 py-2">
                                        <div class="text-[8px] font-black uppercase tracking-[0.14em] text-titan-red leading-none mb-1">{{ $job['dept'] }}</div>
                                        <div class="text-[11px] font-bold text-white line-clamp-1 group-hover:text-titan-red transition-colors">{{ $job['title'] }}</div>
                                        <div class="text-[9px] text-white/25 mt-0.5">{{ $job['location'] }} · {{ $job['type'] }}</div>
                                    </div>
                                    <div class="pr-3 shrink-0">
                                        <x-lucide-chevron-right class="w-3.5 h-3.5 text-white/20 group-hover:text-titan-red transition-colors" />
                                    </div>
                                </a>
                            @endforeach
                            <div class="px-4 py-3">
                                <a href="/careers/gen"
                                   class="flex items-center justify-center gap-2 w-full h-9 rounded-lg bg-titan-red hover:bg-red-700 text-white text-[9px] font-black uppercase tracking-[0.18em] transition-colors">
                                    <x-lucide-send class="w-3 h-3" />
                                    {{ __('General Application') }}
                                </a>
                            </div>
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
