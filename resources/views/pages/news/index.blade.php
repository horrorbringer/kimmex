<x-layouts.app title="News & Updates" description="Read the latest news, updates, and announcements from Kimmex.">

    @php
        $locale = app()->getLocale();
        $newsArticles = \Illuminate\Support\Facades\Cache::remember("news_index_data_{$locale}", now()->addHours(12), function () use ($locale) {
            $newsArticlesDb = \App\Models\NewsArticle::where('isActive', true)
                ->where('publishedAt', '<=', now())
                ->orderBy('publishedAt', 'desc')
                ->get();

            return $newsArticlesDb->map(function ($n) use ($locale) {
                $excerpt = $n->getTranslation('excerpt', $locale)
                    ?: \Illuminate\Support\Str::limit(strip_tags($n->getTranslation('content', $locale)), 180);

                $imageUrl = null;
                $cover = $n->coverImage;
                if ($cover) {
                    if (\Illuminate\Support\Str::startsWith($cover, ['http', '/images'])) {
                        $imageUrl = $cover;
                    } else {
                        $imageUrl = \Illuminate\Support\Facades\Storage::disk('public')->url($cover);
                    }
                }

                return [
                    'slug' => $n->slug,
                    'category' => $n->getTranslation('category', $locale) ?: __('Updates'),
                    'image' => $imageUrl,
                    'title' => $n->getTranslation('title', $locale),
                    'date' => $n->publishedAt ? $n->publishedAt->format('M d, Y') : $n->created_at->format('M d, Y'),
                    'excerpt' => $excerpt,
                ];
            })->toArray();
        });

        // Get unique categories
        $categoriesFromDb = array_column($newsArticles, 'category');
        $categories = array_values(array_unique(array_merge([__('All')], $categoriesFromDb)));
    @endphp

    @php
        $allArticles = collect($newsArticles);
        $featured = $allArticles->first();
        $gridArticles = $allArticles->slice(1);
    @endphp

    <div class="bg-white min-h-screen text-titan-navy" x-data="{
        activeCategory: '{{ __('All') }}',
        articles: {{ Js::from($gridArticles) }},
        get filteredArticles() {
            if (this.activeCategory === '{{ __('All') }}') return this.articles;
            return this.articles.filter(a => a.category === this.activeCategory);
        }
    }">

        <!-- === PREMIUM NEWS HUB HERO === -->
        <section
            class="relative h-[85vh] min-h-[700px] flex items-center justify-center overflow-hidden bg-titan-navy text-center">
            <!-- Background Image -->
            <div class="absolute inset-0">
                <img src="/images/hero/hero-3.jpg" class="w-full h-full object-cover opacity-70 animate-slow-zoom"
                    alt="News Background" />
                <div class="absolute inset-0 bg-gradient-to-b from-titan-navy/60 via-titan-navy/30 to-titan-navy/80">
                </div>
            </div>

            <div class="max-w-4xl mx-auto w-full px-6 relative z-20 pt-20" x-data="{ shown: false }"
                x-init="setTimeout(() => shown = true, 100)">


                <h1 :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'"
                    class="transition-all duration-1000 delay-300 font-heading font-[900] uppercase leading-none tracking-[-0.03em] mb-10 drop-shadow-2xl text-white"
                    style="font-size: clamp(2rem, 5vw, 3.5rem) !important; color: white !important; font-weight: 900 !important;">
                    {{ __('TITAN') }} <span class="text-titan-red">{{ __('NEWSROOM') }}</span>
                </h1>

                <div :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                    class="transition-all duration-1000 delay-500 max-w-3xl mx-auto">
                    <p class="text-white/60 text-lg md:text-xl leading-relaxed font-normal tracking-wide drop-shadow-lg">
                        {{ __('Your central hub for the latest construction announcements,') }}<br
                            class="hidden md:block" />
                        {{ __('project milestones, and industry insights from Kimmex.') }}
                    </p>
                </div>
            </div>
        </section>

        @if($featured)
            <!-- === FEATURED FLOATING CARD === -->
            <section class="relative -mt-16 z-30 px-6 pb-32">
                <div class="max-w-[1400px] mx-auto">
                    <a href="/news/{{ $featured['slug'] }}"
                        class="group block bg-white rounded-[3.5rem] overflow-hidden shadow-[0_60px_120px_-20px_rgba(0,0,0,0.18)] border border-gray-100 transition-all duration-700 hover:-translate-y-3">
                        <div class="flex flex-col lg:flex-row min-h-[550px]">
                            <!-- Content Area -->
                            <div
                                class="lg:w-5/12 p-12 lg:p-24 flex flex-col justify-center order-2 lg:order-1 relative bg-white">
                                <div
                                    class="absolute top-0 left-0 w-1.5 h-full bg-titan-red transform -translate-x-full group-hover:translate-x-0 transition-transform duration-500">
                                </div>

                                <div class="flex items-center gap-4 mb-10">
                                    <span class="bg-titan-navy text-white text-[8px] font-black uppercase tracking-[0.2em] px-3 py-1.5 rounded-md">
                                        {{ $featured["category"] }}
                                    </span>
                                    <span class="text-[9px] font-black text-titan-navy/20 uppercase tracking-[0.2em]">{{ $featured["date"] }}</span>
                                </div>
                                <h2
                                    class="text-4xl lg:text-5xl font-black text-titan-navy mb-10 group-hover:text-titan-red transition-colors duration-300 leading-[1.05] tracking-tight">
                                    {{ $featured['title'] }}
                                </h2>

                                <p class="text-gray-500 text-lg leading-relaxed mb-12 line-clamp-3 font-medium">
                                    {{ $featured['excerpt'] }}
                                </p>

                                <div class="flex items-center gap-5">
                                    <div
                                        class="w-16 h-16 rounded-3xl bg-titan-navy flex items-center justify-center text-white group-hover:bg-titan-red group-hover:rounded-2xl transition-all duration-500 shadow-xl shadow-titan-navy/10 group-hover:shadow-titan-red/30">
                                        <x-lucide-arrow-right
                                            class="w-7 h-7 transition-transform group-hover:translate-x-2" />
                                    </div>
                                    <div class="flex flex-col">
                                        <span
                                            class="text-[10px] font-black uppercase tracking-[0.3em] text-titan-navy/30 mb-1">{{ __('Read More') }}</span>
                                        <span
                                            class="text-xs font-black uppercase tracking-[0.2em] text-titan-navy">{{ __('Full Narrative') }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Image Area -->
                            <div class="lg:w-7/12 relative overflow-hidden order-1 lg:order-2">
                                @if($featured['image'])
                                    <img src="{{ $featured['image'] }}"
                                        class="absolute inset-0 w-full h-full object-cover transition-transform duration-[1.5s] group-hover:scale-105"
                                        alt="{{ $featured['title'] }}" />
                                @else
                                    <div class="absolute inset-0 bg-titan-navy flex items-center justify-center">
                                        <x-lucide-newspaper class="w-32 h-32 text-white/5" />
                                    </div>
                                @endif
                                <div
                                    class="absolute inset-0 bg-gradient-to-r from-white via-transparent to-transparent opacity-0 lg:opacity-100">
                                </div>
                                <div
                                    class="absolute inset-0 bg-titan-navy/10 group-hover:opacity-0 transition-opacity duration-700">
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </section>
        @endif

        <!-- === EDITORIAL TOPICS BAR === -->
        <section class="sticky top-0 z-50 bg-white border-b border-gray-100 px-6">
            <div class="max-w-[1400px] mx-auto h-20 flex items-center justify-between">
                <div class="flex items-center gap-8 overflow-x-auto no-scrollbar">
                    <span
                        class="text-[10px] font-black uppercase tracking-[0.3em] text-titan-navy/30 shrink-0">{{ __('Topics') }}</span>
                    <div class="flex items-center gap-2">
                        @foreach($categories as $cat)
                            <button @click="activeCategory = '{{ $cat }}'"
                                :class="activeCategory === '{{ $cat }}' ? 'bg-titan-navy text-white shadow-lg' : 'text-titan-navy/40 hover:text-titan-navy hover:bg-gray-50'"
                                class="px-5 py-2.5 text-[10px] font-bold uppercase tracking-[0.1em] transition-all duration-300 shrink-0 rounded-full">
                                {{ strtoupper($cat) }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="hidden lg:flex items-center gap-6 border-l border-gray-100 pl-8 ml-8">
                    <span
                        class="text-[10px] font-black uppercase tracking-[0.3em] text-titan-navy/30">{{ __('Year') }}</span>
                    <div class="flex items-center gap-3 bg-gray-50 px-4 py-2 rounded-xl border border-gray-100">
                        <x-lucide-calendar class="w-3.5 h-3.5 text-titan-red" />
                        <span
                            class="text-[10px] font-black uppercase tracking-widest text-titan-navy">{{ __('All') }}</span>
                        <x-lucide-chevron-down class="w-3 h-3 text-titan-navy/20" />
                    </div>
                </div>
            </div>
        </section>

        <!-- === EDITORIAL GRID === -->
        <section class="max-w-[1240px] mx-auto px-6 py-16 lg:py-24">

            <!-- Grid Container -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
                <template x-for="(article, index) in filteredArticles" :key="article.slug">
                    <!-- Article Card -->
                    <a :href="'/news/' + article.slug"
                        class="group flex flex-col bg-white border border-gray-100 hover:border-titan-red/10 transition-all duration-500 overflow-hidden transform hover:-translate-y-2 rounded-2xl shadow-sm hover:shadow-xl">

                        <!-- Thumbnail -->
                        <div class="aspect-video relative overflow-hidden bg-titan-navy rounded-t-2xl">
                            <template x-if="article.image">
                                <img :src="article.image" :alt="article.title"
                                    class="absolute inset-0 w-full h-full object-cover transition-transform duration-[10s] group-hover:scale-105"
                                    loading="lazy" />
                            </template>
                            <template x-if="!article.image">
                                <div
                                    class="absolute inset-0 bg-[radial-gradient(circle_at_30%_20%,rgba(255,107,0,0.15)_0%,transparent_50%)] flex items-center justify-center">
                                    <x-lucide-newspaper class="w-12 h-12 text-white/10" />
                                </div>
                            </template>
                            <div class="absolute top-4 left-4 z-20">
                                <span
                                    class="bg-titan-navy/90 backdrop-blur-sm text-white text-[8px] font-black uppercase tracking-[0.2em] px-2.5 py-1.5 rounded-md"
                                    x-text="article.category"></span>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-8 flex-1 flex flex-col">
                            <div class="flex items-center gap-2 mb-4">
                                <x-lucide-calendar class="w-3 h-3 text-titan-red/60" />
                                <span class="text-[9px] font-black text-titan-navy/20 uppercase tracking-[0.2em]"
                                    x-text="article.date"></span>
                            </div>

                            <h3 class="text-xl font-black text-titan-navy group-hover:text-titan-red transition-colors duration-300 leading-tight mb-4 line-clamp-2"
                                x-text="article.title"></h3>

                            <p class="text-[13px] text-titan-navy/50 leading-relaxed line-clamp-3 mb-6"
                                x-text="article.excerpt"></p>

                            <!-- Footer -->
                            <div class="mt-auto pt-6 border-t border-gray-50 flex items-center justify-between">
                                <span
                                    class="text-[10px] font-black uppercase tracking-[0.3em] text-titan-navy/20 group-hover:text-titan-red transition-colors">{{ __('Read Depth') }}</span>
                                <div
                                    class="w-9 h-9 bg-gray-50 flex items-center justify-center text-titan-navy/20 group-hover:bg-titan-red group-hover:text-white transition-all rounded-xl">
                                    <x-lucide-arrow-right
                                        class="w-4 h-4 group-hover:translate-x-1 transition-transform" />
                                </div>
                            </div>
                        </div>
                    </a>
                </template>
            </div>

            <!-- Empty State -->
            <div x-show="filteredArticles.length === 0"
                class="py-24 text-center bg-white border-2 border-dashed border-gray-100 rounded-2xl">
                <x-lucide-newspaper class="w-12 h-12 text-titan-navy/10 mx-auto mb-4" />
                <p class="text-titan-navy/30 font-black text-xs uppercase tracking-[0.3em]">
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