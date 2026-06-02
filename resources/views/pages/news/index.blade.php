<x-layouts.app title="News & Updates" description="Read the latest news, updates, and announcements from Kimmex.">

    @php
        $locale = app()->getLocale();
        $fallbackImage = '/images/webp/hero/hero-3.webp';

        $newsArticles = \Illuminate\Support\Facades\Cache::remember("news_index_data_{$locale}", now()->addHours(12), function () use ($locale, $fallbackImage) {
            $newsArticlesDb = \App\Models\NewsArticle::where('isActive', true)
                ->where('publishedAt', '<=', now())
                ->orderByDesc('isFeatured')
                ->orderByDesc('publishedAt')
                ->get();

            $fb = $fallbackImage; // local alias inside closure to be explicit

            return $newsArticlesDb->map(function ($n) use ($locale, $fb) {
                $excerpt = $n->getTranslation('excerpt', $locale)
                    ?: \Illuminate\Support\Str::limit(strip_tags($n->getTranslation('content', $locale)), 180);

                $imageUrl = null;
                $cover = $n->coverImage;
                if ($cover) {
                    if (\Illuminate\Support\Str::startsWith($cover, ['http', '/images'])) {
                        $imageUrl = $cover;
                    } else {
                        $imageUrl = \App\Support\PublicStorage::url($cover);
                    }
                }

                return [
                    'slug'       => $n->slug,
                    'category'   => $n->getTranslation('category', $locale) ?: __('Updates'),
                    'image'      => $imageUrl ?: $fb,
                    'title'      => $n->getTranslation('title', $locale),
                    'date'       => $n->publishedAt ? $n->publishedAt->format('M d, Y') : $n->created_at->format('M d, Y'),
                    'excerpt'    => $excerpt,
                    'isFeatured' => (bool) $n->isFeatured,
                ];
            })->toArray();
        });

        $allArticles = collect($newsArticles);
        $featured = $allArticles->first();
        $gridArticles = $allArticles->slice(1)->values();
        $headlineArticles = $gridArticles->take(3)->values();
        $categories = array_values(array_unique(array_merge([__('All Topics')], array_column($newsArticles, 'category'))));

        $sidebarDocs = \Illuminate\Support\Facades\Cache::remember("news_sidebar_documents_{$locale}", now()->addHours(12), function () use ($locale) {
            return \App\Models\Document::with('documentCategory')
                ->publiclyVisible()
                ->latest()
                ->take(3)
                ->get()
                ->map(function ($d) use ($locale) {
                    return [
                        'slug' => $d->slug,
                        'title' => $d->getTranslation('title', $locale),
                        'category' => $d->documentCategory ? $d->documentCategory->getTranslation('name', $locale) : ($d->category ?: __('Documents')),
                        'fileType' => $d->fileType ?: 'PDF',
                        'fileSize' => $d->fileSize,
                    ];
                })->toArray();
        });

        $sidebarJobs = \Illuminate\Support\Facades\Cache::remember("news_sidebar_jobs_{$locale}", now()->addHours(12), function () use ($locale) {
            return \App\Models\JobPosting::where('isActive', true)
                ->with('department')
                ->orderByDesc('created_at')
                ->take(3)
                ->get()
                ->map(function ($j) use ($locale) {
                    $deptName = $j->department ? $j->department->getTranslation('name', $locale) : __('General');
                    return [
                        'slug' => $j->slug,
                        'title' => $j->getTranslation('title', $locale),
                        'dept' => $deptName,
                        'location' => $j->getTranslation('location', $locale),
                        'type' => __(str_replace('_', ' ', \Illuminate\Support\Str::title(strtolower($j->type ?? 'FULL_TIME')))),
                    ];
                })->toArray();
        });
    @endphp

    <div
        class="bg-[#F7F8FA] min-h-screen text-titan-navy pt-28"
        x-data="{
            activeCategory: @js(__('All Topics')),
            allLabel: @js(__('All Topics')),
            articles: {{ Js::from($gridArticles) }},
            get filteredArticles() {
                if (this.activeCategory === this.allLabel) return this.articles;
                return this.articles.filter(a => a.category === this.activeCategory);
            }
        }"
    >
        <!-- HERO -->
        <section class="relative overflow-hidden bg-titan-navy min-h-[320px] md:min-h-[420px] flex items-end">
            <div class="absolute inset-0">
                <img src="/images/webp/hero/hero-3.webp" class="w-full h-full object-cover opacity-55 animate-slow-zoom" alt="News Background" loading="eager" decoding="async" fetchpriority="high" />
                <div class="absolute inset-0 bg-gradient-to-r from-titan-navy/86 via-titan-navy/64 to-titan-navy/25"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-titan-navy/90 via-transparent to-titan-navy/30"></div>
            </div>

            <div class="relative z-10 w-full">
                <div class="max-w-[1240px] mx-auto px-4 md:px-6 pt-10 pb-12 md:pt-16 md:pb-24">
                    <div class="max-w-3xl">
                        <div class="inline-flex items-center gap-2 md:gap-3 border border-white/15 bg-white/10 px-3 md:px-4 py-2 rounded mb-5 md:mb-7">
                            <x-lucide-newspaper class="w-4 h-4 text-titan-red" />
                            <span class="text-[9px] md:text-[10px] font-bold uppercase tracking-[0.2em] md:tracking-[0.24em] text-white/85">
                                {{ __('Kimmex Newsroom') }}
                            </span>
                        </div>

                        <h1 class="font-black uppercase leading-[0.98] md:leading-[0.92] tracking-normal !text-white mb-4 md:mb-6"
                            style="font-size: clamp(1.85rem, 10vw, 4.5rem) !important;">
                            {{ __('News') }} <span class="text-titan-red">{{ __('& Updates') }}</span>
                        </h1>

                        <p class="text-white/78 text-sm md:text-lg leading-relaxed max-w-2xl font-normal">
                            {{ __('A newsroom-style view of company announcements, project milestones, documents, and career updates from Kimmex.') }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- TOP STRIP -->
        <section class="max-w-[1240px] mx-auto px-4 md:px-6 -mt-7 md:-mt-10 relative z-20">
            <div class="grid grid-cols-3 gap-2 md:gap-4">
                <div class="rounded border border-gray-200 bg-white p-3 md:p-5 shadow-sm">
                    <div class="text-[10px] font-bold uppercase tracking-[0.18em] text-titan-red mb-2">{{ __('Stories') }}</div>
                    <div class="text-xl md:text-2xl font-black text-titan-navy">{{ count($newsArticles) }}</div>
                </div>
                <a href="/documents" class="rounded border border-gray-200 bg-white p-3 md:p-5 shadow-sm hover:border-titan-red/25 hover:shadow-md transition-all">
                    <div class="text-[10px] font-bold uppercase tracking-[0.18em] text-titan-red mb-2">{{ __('Documents') }}</div>
                    <div class="text-xl md:text-2xl font-black text-titan-navy">{{ count($sidebarDocs) }}</div>
                    <div class="hidden md:block mt-2 text-sm text-titan-navy/45 font-normal">{{ __('Reference files and technical resources') }}</div>
                </a>
                <a href="/careers" class="rounded border border-gray-200 bg-white p-3 md:p-5 shadow-sm hover:border-titan-red/25 hover:shadow-md transition-all">
                    <div class="text-[10px] font-bold uppercase tracking-[0.18em] text-titan-red mb-2">{{ __('Careers') }}</div>
                    <div class="text-xl md:text-2xl font-black text-titan-navy">{{ count($sidebarJobs) }}</div>
                    <div class="hidden md:block mt-2 text-sm text-titan-navy/45 font-normal">{{ __('Open roles and team opportunities') }}</div>
                </a>
            </div>
        </section>

        <!-- FEATURED + SIDEBAR -->
        <section class="max-w-[1240px] mx-auto px-4 md:px-6 py-8 md:py-12">
            <div class="grid grid-cols-1 lg:grid-cols-[1.18fr_0.82fr] gap-6 items-start">
                @if($featured)
                    <a href="/news/{{ $featured['slug'] }}"
                        class="group bg-white border border-gray-200 rounded overflow-hidden shadow-sm hover:shadow-xl hover:border-titan-red/15 transition-all duration-500">
                        <div class="grid grid-cols-1 xl:grid-cols-[1.08fr_0.92fr] md:min-h-[520px]">
                            <div class="relative overflow-hidden bg-titan-navy min-h-[220px] sm:min-h-[280px] xl:min-h-full">
                                @if($featured['image'])
                                    <img src="{{ $featured['image'] }}" alt="{{ $featured['title'] }}"
                                        class="absolute inset-0 w-full h-full object-cover transition-transform duration-[1.5s] group-hover:scale-105" decoding="async" loading="lazy" />
                                @else
                                    <div class="absolute inset-0 bg-[radial-gradient(circle_at_30%_20%,rgba(227,30,36,0.14)_0%,transparent_50%)] flex items-center justify-center">
                                        <x-lucide-newspaper class="w-20 h-20 text-white/10" />
                                    </div>
                                @endif
                                <div class="absolute inset-0 bg-gradient-to-t from-titan-navy/70 via-transparent to-transparent"></div>
                                <div class="absolute top-4 left-4">
                                    <span class="bg-titan-navy/90 text-white text-[8px] font-black uppercase tracking-[0.2em] px-2.5 py-1.5 rounded-md">
                                        {{ $featured['category'] }}
                                    </span>
                                </div>
                            </div>

                            <div class="p-5 md:p-10 lg:p-12 flex flex-col justify-center">
                                <div class="text-[10px] font-bold uppercase tracking-[0.24em] text-titan-red mb-3">
                                    {{ __('Featured Story') }}
                                </div>
                                <h2 class="text-xl sm:text-2xl md:text-4xl font-bold text-titan-navy leading-tight md:leading-[1.05] tracking-normal group-hover:text-titan-red transition-colors">
                                    {{ $featured['title'] }}
                                </h2>
                                <div class="mt-5 text-[10px] font-bold uppercase tracking-[0.18em] text-titan-navy/25">
                                    {{ $featured['date'] }}
                                </div>
                                <p class="mt-4 md:mt-6 text-sm md:text-base text-titan-navy/60 leading-relaxed font-normal line-clamp-3 md:line-clamp-4">
                                    {{ $featured['excerpt'] }}
                                </p>
                                <div class="mt-6 md:mt-8 flex items-center gap-3">
                                    <span class="text-[10px] font-bold uppercase tracking-[0.22em] text-titan-red">
                                        {{ __('Read Story') }}
                                    </span>
                                    <x-lucide-arrow-right class="w-4 h-4 text-titan-red transition-transform group-hover:translate-x-1" />
                                </div>
                            </div>
                        </div>
                    </a>
                @endif

                <div class="space-y-3 md:space-y-4">
                    <div class="rounded border border-gray-200 bg-white p-5">
                        <div class="flex items-end justify-between gap-4 mb-4">
                            <div>
                                <div class="text-[10px] font-bold uppercase tracking-[0.24em] text-titan-red mb-1">
                                    {{ __('Latest Headlines') }}
                                </div>
                                <div class="text-sm font-bold uppercase tracking-normal text-titan-navy">
                                    {{ __('More news') }}
                                </div>
                            </div>
                            <a href="#story-grid" class="text-[10px] font-bold uppercase tracking-[0.18em] text-titan-red">
                                {{ __('All stories') }}
                            </a>
                        </div>

                        <div class="space-y-3">
                            @forelse($headlineArticles as $headline)
                                <a href="/news/{{ $headline['slug'] }}" class="group flex items-start gap-3 p-3 rounded hover:bg-gray-50 transition-colors">
                                    <div class="w-10 h-10 rounded bg-titan-navy/5 flex items-center justify-center shrink-0">
                                        <x-lucide-newspaper class="w-4 h-4 text-titan-navy/30 group-hover:text-titan-red transition-colors" />
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="text-[9px] font-bold uppercase tracking-[0.16em] text-titan-red mb-1">
                                            {{ $headline['category'] }}
                                        </div>
                                        <div class="text-sm font-bold text-titan-navy leading-tight line-clamp-2 group-hover:text-titan-red transition-colors">
                                            {{ $headline['title'] }}
                                        </div>
                                        <div class="mt-1 text-[10px] text-titan-navy/30 font-normal">
                                            {{ $headline['date'] }}
                                        </div>
                                    </div>
                                </a>
                            @empty
                                <div class="text-sm text-titan-navy/40 font-normal">{{ __('No recent stories.') }}</div>
                            @endforelse
                        </div>
                    </div>

                    <div class="rounded border border-gray-200 bg-white p-5">
                        <div class="text-[10px] font-bold uppercase tracking-[0.24em] text-titan-red mb-4">
                            {{ __('Latest Documents') }}
                        </div>
                        <div class="space-y-3">
                            @forelse($sidebarDocs as $doc)
                                <a href="/documents/{{ $doc['slug'] }}" class="group flex items-start gap-3 p-3 rounded hover:bg-gray-50 transition-colors">
                                    <div class="w-10 h-10 rounded bg-titan-navy/5 flex items-center justify-center shrink-0">
                                        <x-lucide-file-text class="w-4 h-4 text-titan-navy/30 group-hover:text-titan-red transition-colors" />
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="text-[9px] font-bold uppercase tracking-[0.16em] text-titan-red mb-1">
                                            {{ $doc['category'] }}
                                        </div>
                                        <div class="text-sm font-bold text-titan-navy leading-tight line-clamp-2 group-hover:text-titan-red transition-colors">
                                            {{ $doc['title'] }}
                                        </div>
                                        <div class="mt-1 text-[10px] text-titan-navy/30 font-normal">
                                            {{ strtoupper($doc['fileType']) }}{{ $doc['fileSize'] ? ' · ' . $doc['fileSize'] : '' }}
                                        </div>
                                    </div>
                                </a>
                            @empty
                                <div class="text-sm text-titan-navy/40 font-normal">{{ __('No documents found.') }}</div>
                            @endforelse
                        </div>
                    </div>

                    <div class="rounded border border-gray-200 bg-white p-5">
                        <div class="text-[10px] font-bold uppercase tracking-[0.24em] text-titan-red mb-4">
                            {{ __('Open Roles') }}
                        </div>
                        <div class="space-y-3">
                            @forelse($sidebarJobs as $job)
                                <a href="/careers/{{ $job['slug'] }}" class="group flex items-start gap-3 p-3 rounded hover:bg-gray-50 transition-colors">
                                    <div class="w-10 h-10 rounded bg-titan-red/5 flex items-center justify-center shrink-0">
                                        <x-lucide-briefcase class="w-4 h-4 text-titan-red" />
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="text-[9px] font-bold uppercase tracking-[0.16em] text-titan-red mb-1">
                                            {{ $job['dept'] }}
                                        </div>
                                        <div class="text-sm font-bold text-titan-navy leading-tight line-clamp-2 group-hover:text-titan-red transition-colors">
                                            {{ $job['title'] }}
                                        </div>
                                        <div class="mt-1 text-[10px] text-titan-navy/30 font-normal">
                                            {{ $job['location'] }} · {{ $job['type'] }}
                                        </div>
                                    </div>
                                </a>
                            @empty
                                <div class="text-sm text-titan-navy/40 font-normal">{{ __('No open roles right now.') }}</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- FILTERS -->
        <section class="sticky top-20 z-30 bg-white/96 backdrop-blur border-y border-gray-200 px-4 md:px-6">
            <div class="max-w-[1240px] mx-auto py-3 md:py-4 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3 md:gap-4">
                <div class="flex items-center gap-2 overflow-x-auto no-scrollbar">
                    @foreach($categories as $cat)
                        <button
                            @click="activeCategory = @js($cat)"
                            :class="activeCategory === @js($cat) ? 'bg-titan-navy text-white border-titan-navy' : 'bg-white text-titan-navy/55 border-gray-200 hover:text-titan-navy hover:border-titan-red/30'"
                            class="h-9 md:h-10 px-3 md:px-4 rounded border text-[9px] md:text-[10px] font-bold uppercase tracking-[0.14em] md:tracking-[0.16em] transition-all duration-200 shrink-0">
                            {{ $cat }}
                        </button>
                    @endforeach
                </div>
                <div class="hidden sm:block text-[10px] font-bold uppercase tracking-[0.18em] text-titan-navy/30">
                    {{ __('Latest coverage from Kimmex') }}
                </div>
            </div>
        </section>

        <!-- STORY GRID -->
        <section id="story-grid" class="max-w-[1240px] mx-auto px-4 md:px-6 py-8 md:py-14">
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 md:gap-5">
                <template x-for="article in filteredArticles" :key="article.slug">
                    <a :href="'/news/' + article.slug"
                        class="group bg-white border border-gray-200 rounded overflow-hidden hover:shadow-xl hover:border-titan-red/15 transition-all duration-300">
                        <div class="relative aspect-[16/9] md:aspect-[16/10] overflow-hidden bg-titan-navy">
                            <template x-if="article.image">
                                <img :src="article.image" :alt="article.title"
                                    class="absolute inset-0 w-full h-full object-cover transition-transform duration-[1.2s] group-hover:scale-105" loading="lazy" decoding="async" />
                            </template>
                            <div class="absolute top-4 left-4">
                                <span class="bg-titan-navy/90 backdrop-blur text-white text-[8px] font-bold uppercase tracking-[0.2em] px-2.5 py-1.5 rounded-md" x-text="article.category"></span>
                            </div>
                        </div>

                        <div class="p-4 md:p-5 flex flex-col">
                            <div class="text-[9px] font-bold uppercase tracking-[0.18em] text-titan-navy/25 mb-3" x-text="article.date"></div>
                            <h3 class="text-lg font-bold text-titan-navy leading-tight mb-3 line-clamp-2 group-hover:text-titan-red transition-colors" x-text="article.title"></h3>
                            <p class="text-sm text-titan-navy/55 leading-relaxed line-clamp-3 mb-4" x-text="article.excerpt"></p>
                            <div class="mt-auto flex items-center justify-between pt-4 border-t border-gray-100">
                                <span class="text-[10px] font-bold uppercase tracking-[0.18em] text-titan-red">
                                    {{ __('Read Story') }}
                                </span>
                                <x-lucide-arrow-right class="w-4 h-4 text-titan-red group-hover:translate-x-1 transition-transform" />
                            </div>
                        </div>
                    </a>
                </template>
            </div>

            <div x-show="filteredArticles.length === 0" class="py-20 text-center bg-white border border-dashed border-gray-300 rounded mt-8">
                <x-lucide-newspaper class="w-12 h-12 text-titan-navy/10 mx-auto mb-4" />
                <p class="text-titan-navy/35 font-bold text-xs uppercase tracking-[0.3em]">
                    {{ __('No articles found in this category') }}
                </p>
            </div>
        </section>
    </div>

    <style>
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>

</x-layouts.app>
