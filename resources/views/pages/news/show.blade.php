<x-layouts.app :title="__('News Details')" description="Read the latest news and updates from Kimmex.">

    @php
        $articleDb = \App\Models\NewsArticle::where('slug', $slug)->first();

        if ($articleDb) {
            $excerpt = $articleDb->getTranslation('excerpt', app()->getLocale())
                ?: \Illuminate\Support\Str::limit(strip_tags($articleDb->getTranslation('content', app()->getLocale())), 180);

            $article = [
                'slug' => $articleDb->slug,
                'category' => $articleDb->category ?: __('Updates'),
                'image' => $articleDb->coverImage ? \Illuminate\Support\Facades\Storage::url($articleDb->coverImage) : '/images/projects/Thumbnail-4.jpg',
                'title' => $articleDb->getTranslation('title', app()->getLocale()),
                'date' => $articleDb->publishedAt ? $articleDb->publishedAt->format('M d, Y') : $articleDb->created_at->format('M d, Y'),
                'author' => $articleDb->getTranslation('authorName', app()->getLocale()) ?: 'Kimmex Editorial',
                'readTime' => ($articleDb->getTranslation('readTime', app()->getLocale())) ?: (ceil(str_word_count(strip_tags($articleDb->getTranslation('content', app()->getLocale()))) / 200) . ' min read'),
                'excerpt' => $excerpt,
                'content' => $articleDb->getTranslation('content', app()->getLocale())
            ];
        } else {
            // Fallback for non-existent slug
            $article = [
                'slug' => 'error',
                'category' => __('Error'),
                'image' => '/images/projects/Thumbnail-4.jpg',
                'title' => __('Article Not Found'),
                'date' => now()->format('M d, Y'),
                'author' => 'System',
                'readTime' => '1 min',
                'excerpt' => __('This article could not be located in our database.'),
                'content' => __('Please return to the news index to browse our available articles.')
            ];
        }

        // Fetch related from DB
        $relatedQuery = \App\Models\NewsArticle::orderBy('publishedAt', 'desc')->take(3);
        if ($articleDb) {
            $relatedQuery->where('id', '!=', $articleDb->id);
        }
        $relatedDb = $relatedQuery->get();

        $relatedArticles = $relatedDb->map(function ($r) {
            return [
                'slug' => $r->slug,
                'title' => $r->getTranslation('title', app()->getLocale()),
                'date' => $r->publishedAt ? $r->publishedAt->format('M d, Y') : $r->created_at->format('M d, Y'),
                'category' => __('Updates')
            ];
        })->toArray();
    @endphp

    <div class="bg-white min-h-screen text-titan-navy pb-32">

        <!-- === HERO SECTION (Editorial Style) === -->
        <header class="relative bg-titan-navy overflow-hidden pt-64 pb-32">
            <!-- Background Image with Depth -->
            <div class="absolute inset-0">
                <img src="{{ $article['image'] }}" alt="{{ $article['title'] }}"
                    class="w-full h-full object-cover opacity-20 scale-105" />
                <div class="absolute inset-0 bg-gradient-to-b from-titan-navy/50 via-titan-navy to-titan-navy/95"></div>
            </div>

            <div class="relative z-10 max-w-[1200px] mx-auto px-6">
                <nav
                    class="flex items-center gap-3 text-[10px] font-black uppercase tracking-[0.3em] text-white/40 mb-12">
                    <a href="/" class="hover:text-titan-red transition-colors">{{ __('Home') }}</a>
                    <span class="w-1 h-1 rounded-full bg-white/10"></span>
                    <a href="/news" class="hover:text-titan-red transition-colors">{{ __('News') }}</a>
                    <span class="w-1 h-1 rounded-full bg-titan-red"></span>
                    <span class="text-white/60">{{ __('Article') }}</span>
                </nav>

                <div class="max-w-4xl" x-data="{ shown: false }" x-init="setTimeout(() => shown = true, 100)">
                    <div class="inline-flex items-center gap-3 px-4 py-2 bg-white/5 border border-white/10 rounded-lg mb-8"
                        :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
                        class="transition-all duration-700 delay-100">
                        <span class="w-2 h-2 rounded-full bg-titan-red"></span>
                        <span
                            class="text-[10px] font-black text-white uppercase tracking-[0.3em]">{{ $article['category'] }}</span>
                    </div>

                    <h1 class="text-4xl md:text-5xl lg:text-7xl font-black text-white uppercase tracking-tighter leading-[0.9] mb-12"
                        :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                        class="transition-all duration-700 delay-300">
                        {{ $article['title'] }}
                    </h1>

                    <div class="flex flex-wrap items-center gap-10 text-[11px] font-bold text-white/50 uppercase tracking-widest border-t border-white/10 pt-10"
                        :class="shown ? 'opacity-100' : 'opacity-0'" class="transition-all duration-1000 delay-500">
                        <div class="flex items-center gap-3">
                            <x-lucide-user class="w-4 h-4 text-titan-red" />
                            {{ $article['author'] }}
                        </div>
                        <div class="flex items-center gap-3">
                            <x-lucide-calendar class="w-4 h-4 text-titan-red" />
                            {{ $article['date'] }}
                        </div>
                        <div class="flex items-center gap-3">
                            <x-lucide-clock class="w-4 h-4 text-titan-red" />
                            {{ $article['readTime'] }}
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- === ARTICLE CONTENT === -->
        <main class="relative z-20 -mt-10 lg:-mt-20 max-w-[1200px] mx-auto px-6">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-16">

                <!-- Left: Main Content -->
                <div class="lg:col-span-8">
                    <div
                        class="bg-white rounded-3xl p-8 md:p-16 lg:p-20 shadow-[0_32px_128px_-32px_rgba(0,0,0,0.1)] border border-gray-100">
                        <!-- Dynamic Featured Image -->
                        <div class="aspect-[16/9] rounded-2xl overflow-hidden mb-16 shadow-2xl">
                            <img src="{{ $article['image'] }}" class="w-full h-full object-cover" />
                        </div>

                        <!-- Article Text -->
                        <div
                            class="prose prose-xl prose-slate max-w-none text-titan-navy/80 leading-relaxed font-medium">
                            <p
                                class="mb-10 text-2xl font-black text-titan-navy leading-tight border-l-4 border-titan-red pl-8">
                                {{ $article['excerpt'] ?? __('Leading the way in high-precision construction and infrastructure management.') }}
                            </p>

                            <div class="space-y-8">
                                {!! $article['content'] !!}
                            </div>
                        </div>

                        <!-- Share Section -->
                        <div
                            class="mt-20 pt-10 border-t border-gray-100 flex items-center justify-between flex-wrap gap-8">
                            <div class="flex items-center gap-4">
                                <span
                                    class="text-[10px] font-black uppercase tracking-widest text-titan-navy/30">{{ __('Share Article') }}</span>
                                <div class="flex gap-2">
                                    <a href="#"
                                        class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center hover:bg-titan-red hover:text-white transition-all"><x-lucide-facebook
                                            class="w-4 h-4" /></a>
                                    <a href="#"
                                        class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center hover:bg-titan-red hover:text-white transition-all"><x-lucide-linkedin
                                            class="w-4 h-4" /></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Sidebar -->
                <div class="lg:col-span-4 space-y-12">
                    <!-- Related News -->
                    <div class="bg-gray-50 rounded-3xl p-8 border border-gray-100">
                        <h3
                            class="text-xs font-black uppercase tracking-widest text-titan-navy mb-8 border-b border-titan-red/20 pb-4">
                            {{ __('Latest Updates') }}
                        </h3>
                        <div class="space-y-8">
                            @foreach($relatedArticles as $related)
                                <a href="/news/{{ $related['slug'] }}" class="group block">
                                    <div class="text-[9px] font-black text-titan-red uppercase tracking-widest mb-2">
                                        {{ $related['category'] }}
                                    </div>
                                    <h4
                                        class="text-sm font-black text-titan-navy group-hover:text-titan-red transition-colors leading-snug mb-2">
                                        {{ $related['title'] }}
                                    </h4>
                                    <div class="text-[10px] text-titan-navy/40 font-bold uppercase">{{ $related['date'] }}
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <!-- Newsletter / CTA -->
                    <div class="bg-titan-navy rounded-3xl p-10 text-white relative overflow-hidden group">
                        <div
                            class="absolute top-0 right-0 w-32 h-32 bg-titan-red/10 rounded-full blur-3xl group-hover:bg-titan-red/20 transition-all duration-700">
                        </div>
                        <h3 class="text-2xl font-black uppercase mb-4 relative z-10">{{ __('Newsletter') }}</h3>
                        <p class="text-xs text-white/40 leading-relaxed mb-8 relative z-10">
                            {{ __('Subscribe to receive the latest industry insights and project updates from Kimmex.') }}
                        </p>
                        <form class="space-y-4 relative z-10">
                            <input type="email" placeholder="example@email.com"
                                class="w-full bg-white/5 border border-white/10 rounded-xl px-5 py-4 text-xs font-bold text-white outline-none focus:border-titan-red transition-all" />
                            <button
                                class="w-full bg-titan-red text-white py-4 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-white hover:text-titan-navy transition-all shadow-xl shadow-titan-red/20">{{ __('Subscribe') }}</button>
                        </form>
                    </div>
                </div>

            </div>
        </main>

    </div>

</x-layouts.app>