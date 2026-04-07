<x-layouts.app title="News & Updates" description="Read the latest news, updates, and announcements from Kimmex.">

    @php
        $newsArticlesDb = \App\Models\NewsArticle::where('publishedAt', '<=', now())
            ->orderBy('publishedAt', 'desc')
            ->get();

        $newsArticles = $newsArticlesDb->map(function ($n) {
            $excerpt = $n->getTranslation('excerpt', app()->getLocale())
                ?: \Illuminate\Support\Str::limit(strip_tags($n->getTranslation('content', app()->getLocale())), 180);

            return [
                'slug' => $n->slug,
                'category' => $n->category ?: __('Updates'),
                'image' => $n->coverImage ? \Illuminate\Support\Facades\Storage::url($n->coverImage) : '/images/heroes/documents-bg.png',
                'title' => $n->getTranslation('title', app()->getLocale()),
                'date' => $n->publishedAt ? $n->publishedAt->format('M d, Y') : $n->created_at->format('M d, Y'),
                'excerpt' => $excerpt,
            ];
        })->toArray();

        // Get unique categories
        $categoriesFromDb = $newsArticlesDb->map(fn($n) => $n->category ?: __('Updates'))->unique()->toArray();
        $categories = array_values(array_unique(array_merge([__('All')], $categoriesFromDb)));
    @endphp

    <div class="bg-[#FCFCFD] min-h-screen text-titan-navy" x-data="{
        activeCategory: '{{ __('All') }}',
        articles: {{ Js::from($newsArticles) }},
        get filteredArticles() {
            if (this.activeCategory === '{{ __('All') }}') return this.articles;
            return this.articles.filter(a => a.category === this.activeCategory);
        }
    }">

        <!-- === CINEMATIC HERO === -->
        <section class="relative bg-titan-navy pt-[140px] pb-24 px-6 overflow-hidden">
            <!-- Background Image -->
            <div class="absolute inset-0">
                <img src="/images/projects/Thumbnail-6.jpg" class="w-full h-full object-cover opacity-60" alt="" />
                <div class="absolute inset-0 bg-gradient-to-r from-titan-navy via-titan-navy/70 to-transparent"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-titan-navy/60 via-transparent to-transparent"></div>
            </div>

            <!-- Accent Glow -->
            <div
                class="absolute top-0 right-0 -mr-32 -mt-32 w-96 h-96 bg-titan-red/10 blur-[120px] rounded-full pointer-events-none">
            </div>

            <div class="max-w-[1240px] mx-auto relative z-10">
                <!-- Badge -->
                <div
                    class="inline-flex items-center gap-2 bg-white/5 border border-white/10 backdrop-blur-md rounded-full px-5 py-2.5 mb-10 shadow-xl">
                    <x-lucide-newspaper class="w-3.5 h-3.5 text-titan-red" />
                    <span
                        class="text-[10px] font-black uppercase tracking-[0.3em] text-white/90">{{ __('Kimmex Narrative') }}</span>
                </div>

                <div class="max-w-3xl">
                    <h1 class="font-black text-white uppercase leading-[0.9] tracking-tighter mb-8"
                        style="font-size: clamp(3rem, 8vw, 6rem);">
                        {{ __('NEWS') }}<span class="text-titan-red">.</span><br />
                        <span
                            class="text-transparent bg-clip-text bg-gradient-to-r from-gray-300 to-white">{{ __('HUB') }}</span>
                    </h1>
                    <p class="text-white/40 text-lg leading-relaxed max-w-lg font-medium">
                        {{ __('Stay up to date with the latest engineering breakthroughs and project updates from Kimmex.') }}
                    </p>
                </div>
            </div>
        </section>

        <!-- === MINIMAL CATEGORY BAR === -->
        <section class="sticky top-0 z-50 bg-white/95 backdrop-blur-xl border-b border-gray-100 px-6 py-2 shadow-sm">
            <div class="max-w-[1240px] mx-auto flex items-center gap-2 overflow-x-auto no-scrollbar">
                @foreach($categories as $cat)
                    <button @click="activeCategory = '{{ $cat }}'"
                        :class="activeCategory === '{{ $cat }}' ? 'bg-titan-navy text-white shadow-md' : 'text-titan-navy/30 hover:text-titan-navy hover:bg-gray-50'"
                        class="px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-[0.2em] transition-all duration-300 shrink-0">
                        {{ $cat }}
                    </button>
                @endforeach
            </div>
        </section>

        <!-- === EDITORIAL GRID === -->
        <section class="max-w-[1240px] mx-auto px-6 py-16 lg:py-24">

            <!-- Grid Container -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
                <template x-for="(article, index) in filteredArticles" :key="article.slug">
                    <!-- Article Card -->
                    <a :href="'/news/' + article.slug"
                        class="group flex flex-col bg-white rounded-3xl border border-gray-100 hover:border-titan-red/10 hover:shadow-2xl transition-all duration-500 overflow-hidden transform hover:-translate-y-2">

                        <!-- Thumbnail -->
                        <div class="aspect-video relative overflow-hidden bg-titan-navy/5">
                            <img :src="article.image" :alt="article.title"
                                class="absolute inset-0 w-full h-full object-cover transition-transform duration-[10s] group-hover:scale-105" />
                            <div class="absolute top-4 left-4 z-20">
                                <span
                                    class="bg-titan-navy/90 backdrop-blur-sm text-white text-[8px] font-black uppercase tracking-[0.2em] px-2.5 py-1.5 rounded-lg shadow-xl"
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
                                    class="w-9 h-9 rounded-xl bg-gray-50 flex items-center justify-center text-titan-navy/20 group-hover:bg-titan-red group-hover:text-white transition-all">
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
                class="py-24 text-center bg-white rounded-[3rem] border-2 border-dashed border-gray-100">
                <x-lucide-newspaper class="w-12 h-12 text-titan-navy/10 mx-auto mb-4" />
                <p class="text-titan-navy/30 font-black text-xs uppercase tracking-[0.3em]">
                    {{ __('No articles found in this category') }}</p>
            </div>

        </section>

        <!-- === CORPORATE CTA === -->
        <section class="bg-titan-navy py-20 px-6 mt-16">
            <div class="max-w-[1240px] mx-auto flex flex-col md:flex-row items-center justify-between gap-12">
                <div>
                    <div class="text-[10px] font-black text-titan-red uppercase tracking-[0.4em] mb-4">
                        {{ __('Media Inquiries') }}</div>
                    <h3 class="text-3xl font-black text-white uppercase tracking-tight mb-3">{{ __('Press & PR Desk') }}
                    </h3>
                    <p class="text-white/40 text-base max-w-md font-medium">
                        {{ __('Are you a journalist or industry analyst? Get in touch with our communications team.') }}
                    </p>
                </div>
                <a href="/contact"
                    class="bg-white text-titan-navy px-10 py-5 rounded-2xl font-black uppercase tracking-widest hover:bg-titan-red hover:text-white transition-all shadow-2xl">
                    {{ __('Contact Media Team') }}
                </a>
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