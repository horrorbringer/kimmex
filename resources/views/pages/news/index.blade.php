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
                'image' => $n->coverImage ? \Illuminate\Support\Facades\Storage::url($n->coverImage) : '/images/projects/Thumbnail-4.jpg',
                'title' => $n->getTranslation('title', app()->getLocale()),
                'date' => $n->publishedAt ? $n->publishedAt->format('M d, Y') : $n->created_at->format('M d, Y'),
                'excerpt' => $excerpt,
                'isSustainability' => in_array(strtolower($n->category ?? ''), ['sustainability', 'csr', 'environment', 'green', 'esg'])
            ];
        })->toArray();

        // Get unique categories including Sustainability and CSR as default options
        $categoriesFromDb = $newsArticlesDb->map(fn($n) => $n->category ?: __('Updates'))->unique()->toArray();
        $defaultCategories = [__('Sustainability'), __('CSR'), __('Updates')];
        $categories = array_values(array_unique(array_merge($defaultCategories, $categoriesFromDb)));
        array_unshift($categories, __('All'));

        // Fallback
        if (empty($newsArticles)) {
            $newsArticles = [
                ['slug' => 'award', 'category' => __('Updates'), 'image' => '/images/projects/Thumbnail-4.jpg', 'title' => __('Kimmex Insights and Announcements'), 'date' => now()->format('M d, Y'), 'excerpt' => __('Discover our journey in construction excellence.'), 'isSustainability' => false],
                ['slug' => 'green-initiative', 'category' => __('Sustainability'), 'image' => '/images/projects/Thumbnail-6.jpg', 'title' => __('Green Building Initiative Launched'), 'date' => now()->subDays(15)->format('M d, Y'), 'excerpt' => __('Kimmex launches new sustainable construction practices across all projects.'), 'isSustainability' => true]
            ];
            $categories = [__('All'), __('Sustainability'), __('CSR'), __('Updates')];
        }
    @endphp

    <div class="bg-white min-h-screen text-titan-navy" x-data="{
        activeCategory: '{{ __('All') }}',
        articles: {{ Js::from($newsArticles) }},
        categories: {{ Js::from($categories) }},
        get filteredArticles() {
            if (this.activeCategory === '{{ __('All') }}') return this.articles;
            return this.articles.filter(a => a.category === this.activeCategory);
        }
    }">

        <!-- HERO -->
        <section class="relative z-10 flex items-center justify-center overflow-hidden bg-titan-navy"
            style="min-height: 480px;">
            <div class="absolute inset-0">
                <img src="/images/projects/Thumbnail-6.jpg" alt="News" class="w-full h-full object-cover opacity-45" />
                <div class="absolute inset-0 bg-gradient-to-b from-titan-navy/50 via-titan-navy/30 to-titan-navy/80">
                </div>
            </div>

            <div class="relative z-20 text-center max-w-4xl px-6 pt-64" x-data="{ shown: false }"
                x-init="setTimeout(() => shown = true, 100)">
                <div :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 -translate-y-6'"
                    class="transition-all duration-700 delay-100 inline-flex items-center gap-2 px-5 py-2.5 bg-white/5 backdrop-blur-sm rounded-full text-white/80 text-[11px] font-bold uppercase tracking-widest mb-8 border border-white/10">
                    <x-lucide-newspaper class="w-3.5 h-3.5 text-titan-red" />
                    {{ __('Insights') }}
                </div>

                <h1 :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10'"
                    class="transition-all duration-700 delay-300 font-black text-white mb-6 leading-none tracking-tighter uppercase"
                    style="font-size: clamp(2.5rem, 7vw, 5.5rem);">
                    {{ __('News & Updates') }}
                </h1>

                <p :class="shown ? 'opacity-100' : 'opacity-0'"
                    class="transition-all duration-700 delay-500 text-base text-white/40 max-w-xl mx-auto leading-relaxed">
                    {{ __('Stay up to date with the latest developments from Kimmex Construction.') }}
                </p>
            </div>
        </section>

        <!-- CATEGORY FILTER -->
        <section class="max-w-[1200px] mx-auto px-6 relative z-40 -mt-12">
            <div class="flex flex-wrap items-center gap-2 bg-white rounded-xl shadow-lg border border-gray-100 p-2">
                <template x-for="cat in categories" :key="cat">
                    <button @click="activeCategory = cat"
                        :class="activeCategory === cat ? 'bg-titan-navy text-white shadow-md' : 'text-titan-navy/50 hover:text-titan-navy hover:bg-gray-100'"
                        class="px-4 py-2.5 rounded-lg text-[11px] font-bold uppercase tracking-wider transition-all duration-300 whitespace-nowrap"
                        x-text="cat === '{{ __('All') }}' ? '{{ __('All News') }}' : cat">
                    </button>
                </template>
            </div>
        </section>

        <!-- FEATURED ARTICLE (First article as hero card) -->
        <section class="max-w-[1200px] mx-auto px-6 relative z-40 mt-8">
            <a href="/news/{{ $newsArticles[0]['slug'] }}"
                class="group block bg-white rounded-2xl shadow-[0_20px_60px_-15px_rgba(0,0,0,0.1)] border border-gray-100 overflow-hidden">
                <div class="grid grid-cols-1 lg:grid-cols-2">
                    <!-- Image -->
                    <div class="relative h-64 lg:h-auto overflow-hidden">
                        <img src="{{ $newsArticles[0]['image'] }}" alt="{{ $newsArticles[0]['title'] }}"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" />
                        <div
                            class="absolute top-5 left-5 px-4 py-2 bg-titan-red text-white text-[10px] font-black uppercase tracking-widest rounded-lg shadow-lg">
                            {{ $newsArticles[0]['category'] }}
                        </div>
                    </div>
                    <!-- Content -->
                    <div class="p-8 lg:p-14 flex flex-col justify-center">
                        <div class="flex items-center gap-3 mb-5">
                            <span
                                class="bg-titan-red/10 text-titan-red text-[10px] font-black uppercase tracking-widest px-3 py-1.5 rounded-lg">{{ __('Featured') }}</span>
                            <span class="text-[11px] font-bold text-titan-navy/30 flex items-center gap-1.5">
                                <x-lucide-calendar class="w-3 h-3" /> {{ $newsArticles[0]['date'] }}
                            </span>
                        </div>
                        <h2
                            class="text-2xl lg:text-3xl font-black text-titan-navy leading-tight mb-4 group-hover:text-titan-red transition-colors duration-300 uppercase tracking-tight">
                            {{ $newsArticles[0]['title'] }}
                        </h2>
                        <p class="text-titan-navy/50 text-[15px] leading-relaxed mb-8">
                            {{ $newsArticles[0]['excerpt'] }}
                        </p>
                        <div
                            class="flex items-center gap-3 text-titan-red font-bold text-xs uppercase tracking-widest group-hover:gap-4 transition-all duration-300">
                            {{ __('Read Full Story') }}
                            <x-lucide-arrow-right class="w-4 h-4" />
                        </div>
                    </div>
                </div>
            </a>
        </section>

        <!-- NEWS GRID -->
        <section class="py-24 max-w-[1200px] mx-auto px-6">
            <!-- Section Header -->
            <div class="flex items-center justify-between mb-12">
                <div class="flex items-center gap-4">
                    <div class="w-1 h-8 bg-titan-red rounded-full"></div>
                    <div>
                        <h2 class="text-xl font-black text-titan-navy uppercase tracking-tight">{{ __('All Articles') }}
                        </h2>
                        <p class="text-titan-navy/30 text-xs mt-0.5">{{ count($newsArticles) - 1 }} {{ __('stories') }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach(array_slice($newsArticles, 1) as $i => $article)
                    <a href="/news/{{ $article['slug'] }}"
                        class="group block bg-white rounded-2xl overflow-hidden border border-gray-100 hover:shadow-[0_20px_50px_-15px_rgba(0,0,0,0.08)] transition-all duration-500 hover:-translate-y-1">
                        <!-- Image -->
                        <div class="aspect-[16/10] relative overflow-hidden">
                            <div
                                class="absolute top-4 left-4 z-20 px-3 py-1.5 bg-white/90 backdrop-blur-sm text-titan-navy text-[10px] font-black uppercase tracking-widest rounded-lg">
                                {{ $article['category'] }}
                            </div>
                            <img src="{{ $article['image'] }}" alt="{{ $article['title'] }}"
                                class="object-cover w-full h-full group-hover:scale-105 transition-transform duration-700" />
                        </div>

                        <!-- Content -->
                        <div class="p-7">
                            <div class="text-[10px] font-bold text-titan-navy/30 mb-3 flex items-center gap-1.5">
                                <x-lucide-calendar class="w-3 h-3 text-titan-red" /> {{ $article['date'] }}
                            </div>

                            <h3
                                class="text-lg font-black text-titan-navy leading-snug mb-3 group-hover:text-titan-red transition-colors duration-300">
                                {{ $article['title'] }}
                            </h3>

                            <p class="text-titan-navy/40 text-sm leading-relaxed mb-6 line-clamp-2">
                                {{ $article['excerpt'] }}
                            </p>

                            <div class="flex items-center justify-between pt-5 border-t border-gray-50">
                                <span
                                    class="text-[11px] font-bold uppercase tracking-widest text-titan-navy/30 group-hover:text-titan-red transition-colors">{{ __('Read More') }}</span>
                                <div
                                    class="w-8 h-8 rounded-full bg-gray-50 text-titan-navy/40 flex items-center justify-center group-hover:bg-titan-red group-hover:text-white transition-all duration-300">
                                    <x-lucide-arrow-right class="w-3.5 h-3.5" />
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>

        <!-- CTA BANNER -->
        <section class="bg-titan-navy py-16 relative overflow-hidden">
            <div
                class="absolute inset-0 opacity-5 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')]">
            </div>
            <div
                class="max-w-[1200px] mx-auto px-6 relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
                <div>
                    <h3 class="text-2xl font-black text-white uppercase tracking-tight mb-2">{{ __('Stay Connected') }}
                    </h3>
                    <p class="text-white/40 text-sm">
                        {{ __('Follow us for the latest updates and project announcements.') }}
                    </p>
                </div>
                <div class="flex gap-3">
                    <a href="#"
                        class="w-12 h-12 rounded-xl bg-white/10 text-white flex items-center justify-center hover:bg-titan-red transition-all duration-300 border border-white/5"><x-lucide-facebook
                            class="w-5 h-5" /></a>
                    <a href="#"
                        class="w-12 h-12 rounded-xl bg-white/10 text-white flex items-center justify-center hover:bg-titan-red transition-all duration-300 border border-white/5"><x-lucide-linkedin
                            class="w-5 h-5" /></a>
                    <a href="#"
                        class="w-12 h-12 rounded-xl bg-white/10 text-white flex items-center justify-center hover:bg-titan-red transition-all duration-300 border border-white/5"><x-lucide-youtube
                            class="w-5 h-5" /></a>
                    <a href="#"
                        class="w-12 h-12 rounded-xl bg-white/10 text-white flex items-center justify-center hover:bg-titan-red transition-all duration-300 border border-white/5"><x-lucide-instagram
                            class="w-5 h-5" /></a>
                </div>
            </div>
        </section>
    </div>

</x-layouts.app>