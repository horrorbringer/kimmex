

    @php
        $locale = app()->getLocale();
        $article = \Illuminate\Support\Facades\Cache::remember("news_article_data_{$slug}_{$locale}", now()->addHours(12), function() use ($slug, $locale) {
            $articleDb = \App\Models\NewsArticle::where('isActive', true)->where('slug', $slug)->first();
            if ($articleDb) {
                $excerpt = $articleDb->getTranslation('excerpt', $locale)
                    ?: \Illuminate\Support\Str::limit(strip_tags($articleDb->getTranslation('content', $locale)), 180);

                return [
                    'slug' => $articleDb->slug,
                    'category' => $articleDb->category ?: __('Updates'),
                    'image' => ($articleDb->coverImage && \Illuminate\Support\Facades\Storage::disk('public')->exists($articleDb->coverImage)) ? \Illuminate\Support\Facades\Storage::url($articleDb->coverImage) : null,
                    'title' => $articleDb->getTranslation('title', $locale),
                    'date' => $articleDb->publishedAt ? $articleDb->publishedAt->format('M d, Y') : $articleDb->created_at->format('M d, Y'),
                    'author' => $articleDb->getTranslation('authorName', $locale) ?: 'Kimmex Editorial',
                    'readTime' => ($articleDb->getTranslation('readTime', $locale)) ?: (ceil(str_word_count(strip_tags($articleDb->getTranslation('content', $locale))) / 200) . ' min read'),
                    'excerpt' => $excerpt,
                    'content' => $articleDb->getTranslation('content', $locale),
                    'tags' => is_array($articleDb->tags) && count($articleDb->tags) > 0 ? $articleDb->tags : [$articleDb->category ?: 'News'],
                    'gallery' => collect($articleDb->gallery ?? [])->map(fn($img) => \Illuminate\Support\Facades\Storage::url($img))->toArray()
                ];
            }
            return null;
        });

        if (!$article) {
            // Fallback for non-existent slug
            $article = [
                'slug' => 'error',
                'category' => __('Announcement'),
                'image' => '/images/projects/Thumbnail-4.jpg',
                'title' => __('Article Unavailable'),
                'date' => now()->format('M d, Y'),
                'author' => 'System',
                'readTime' => '1 min',
                'excerpt' => __('We are currently updating our news archive. Please try again soon.'),
                'content' => '<p>The content you are looking for might have been archived or moved during our site optimization. Please return to the news index to explore our latest updates.</p>',
                'tags' => ['Announcement'],
                'gallery' => []
            ];
        }

        // Fetch related from DB with caching
        $relatedData = \Illuminate\Support\Facades\Cache::remember("news_related_array_{$slug}_{$locale}", now()->addHours(12), function() use ($slug, $locale) {
            $currentDb = \App\Models\NewsArticle::where('isActive', true)->where('slug', $slug)->first();
            
            $relatedDb = \App\Models\NewsArticle::where('isActive', true)->where('slug', '!=', $slug)->latest()->take(3)->get();
            $related = $relatedDb->map(function (\App\Models\NewsArticle $r) use ($locale) {
                return [
                    'slug' => $r->slug,
                    'title' => $r->getTranslation('title', $locale),
                    'date' => $r->publishedAt ? $r->publishedAt->format('M d, Y') : $r->created_at->format('M d, Y'),
                    'category' => $r->category ?? __('Updates'),
                    'image' => $r->coverImage
                ];
            })->toArray();

            $next = null;
            $prev = null;
            if ($currentDb) {
                $nextDb = \App\Models\NewsArticle::where('isActive', true)->where('id', '>', $currentDb->id)->orderBy('id', 'asc')->first();
                $prevDb = \App\Models\NewsArticle::where('isActive', true)->where('id', '<', $currentDb->id)->orderBy('id', 'desc')->first();
                if ($nextDb) $next = ['slug' => $nextDb->slug, 'title' => $nextDb->getTranslation('title', $locale)];
                if ($prevDb) $prev = ['slug' => $prevDb->slug, 'title' => $prevDb->getTranslation('title', $locale)];
            }

            return [
                'related' => $related,
                'next' => $next,
                'prev' => $prev
            ];
        });
        $relatedArticles = $relatedData['related'] ?? [];
        $nextArticle = $relatedData['next'] ?? null;
        $prevArticle = $relatedData['prev'] ?? null;

        $pageTitle = $article['title'] ?? __('News Details');
        $pageDesc = $article['excerpt'] ?? __('Read the latest news and updates from Kimmex.');
        $pageImage = $article['image'] ?? null;

        $profile = $globalSettings['profile'] ?? [];
        $facebook = $profile['facebook'] ?? null;
        $linkedin = $profile['linkedin'] ?? null;
        $youtube = $profile['youtube'] ?? null;
        $instagram = $profile['instagram'] ?? null;
        $telegram = $profile['telegram'] ?? null;
    @endphp

<x-layouts.app :title="$pageTitle" :description="$pageDesc" :image="$pageImage">

    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "NewsArticle",
        "headline": "{{ $article['title'] ?? '' }}",
        "image": [
            "{{ $article['image'] ? url($article['image']) : url('/logo.png') }}"
        ],
        "datePublished": "{{ isset($article['date']) ? \Carbon\Carbon::parse($article['date'])->toIso8601String() : now()->toIso8601String() }}",
        "author": [{
            "@@type": "Person",
            "name": "{{ $article['author'] ?? 'Kimmex Editorial' }}"
        }]
    }
    </script>

    <div class="bg-white min-h-screen text-titan-navy font-sans antialiased" x-data="{ 
        scrolled: false, 
        progress: 0,
        scrollY: 0,
        headings: [],
        activeHeading: null,
        hElements: [],
        ticking: false
    }" x-init="
        // Initialize headings from article and cache DOM elements
        $nextTick(() => {
            hElements = Array.from(document.querySelectorAll('article h2, article h3'));
            hElements.forEach((h, i) => {
                if(!h.id) h.id = 'heading-' + i;
                headings.push({ id: h.id, text: h.innerText, level: h.tagName });
            });
        });

        // Throttled Scroll Listener using requestAnimationFrame
        window.addEventListener('scroll', () => {
            if (!ticking) {
                window.requestAnimationFrame(() => {
                    scrollY = window.scrollY;
                    scrolled = window.scrollY > 400;
                    const scrollTotal = document.documentElement.scrollHeight - window.innerHeight;
                    progress = scrollTotal > 0 ? (window.scrollY / scrollTotal) * 100 : 0;
                    
                    // Find active heading from cached elements
                    const current = hElements.find(h => {
                        const top = h.getBoundingClientRect().top;
                        return top > 0 && top < 200;
                    });
                    if(current) activeHeading = current.id || current.innerText;
                    
                    ticking = false;
                });
                ticking = true;
            }
        }, { passive: true });
    ">

        <!-- READING PROGRESS & STICKY NAV -->
        <div class="fixed top-0 left-0 w-full z-[100] transition-transform duration-500"
            :class="scrolled ? 'translate-y-0' : '-translate-y-full'">
            <div class="h-1 bg-gray-100 w-full relative">
                <div class="h-full bg-titan-red absolute left-0 top-0 transition-all duration-150"
                    :style="'width: ' + progress + '%'"></div>
            </div>
            <div class="bg-white/95 backdrop-blur-md border-b border-gray-100 h-12 flex items-center px-6">
                <div class="max-w-[1240px] mx-auto w-full flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <span
                            class="text-[9px] font-black text-titan-red uppercase tracking-widest hidden md:block">{{ __('Now Reading:') }}</span>
                        <span
                            class="text-[11px] font-black text-titan-navy truncate max-w-[200px] md:max-w-md uppercase tracking-tight">{{ $article['title'] }}</span>
                    </div>
                    <div class="flex items-center gap-6">
                        <div class="hidden lg:flex items-center gap-3">
                            <span
                                class="text-[9px] font-black text-titan-navy/20 uppercase tracking-widest">{{ __('Share') }}</span>
                            <div class="flex gap-2">
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url('/news/' . $article['slug'])) }}" target="_blank" rel="noopener"
                                    class="w-8 h-8 bg-social-facebook rounded-lg flex items-center justify-center text-white hover:brightness-110 transition-all group/fb">
                                    <x-lucide-facebook class="w-3.5 h-3.5 transition-transform group-hover/fb:scale-110" />
                                </a>
                                <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(url('/news/' . $article['slug'])) }}" target="_blank" rel="noopener"
                                    class="w-8 h-8 bg-social-linkedin rounded-lg flex items-center justify-center text-white hover:brightness-110 transition-all group/li">
                                    <x-lucide-linkedin class="w-3.5 h-3.5 transition-transform group-hover/li:scale-110" />
                                </a>
                                <a href="https://t.me/share/url?url={{ urlencode(url('/news/' . $article['slug'])) }}&text={{ urlencode($article['title']) }}" target="_blank" rel="noopener"
                                    class="w-8 h-8 bg-social-telegram rounded-lg flex items-center justify-center text-white hover:brightness-110 transition-all group/tg">
                                    <x-lucide-send class="w-3.5 h-3.5 transition-transform group-hover/tg:scale-110" />
                                </a>
                            </div>
                        </div>
                        <a href="/news"
                            class="w-8 h-8 bg-titan-navy border border-titan-navy/20 text-white rounded-lg flex items-center justify-center hover:bg-titan-red hover:border-transparent transition-all group/back">
                            <x-lucide-arrow-left class="w-4 h-4 transition-transform group-hover:-translate-x-0.5" />
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- === PREMIUM NEWS HERO === -->
        <header class="relative h-[50vh] min-h-[420px] flex items-center justify-center overflow-hidden bg-titan-navy shadow-2xl">
            {{-- Background Zoom Animation --}}
            <div class="absolute inset-0">
                @if($article['image'])
                    <img src="{{ $article['image'] }}" alt="{{ $article['title'] }}" class="w-full h-full object-cover opacity-100 animate-slow-zoom" />
                @else
                    <div class="w-full h-full bg-[radial-gradient(circle_at_30%_20%,rgba(227,30,36,0.15)_0%,transparent_50%)]"></div>
                @endif
                {{-- Lightened multi-stage gradient --}}
                <div class="absolute inset-0 bg-gradient-to-b from-titan-navy/40 via-transparent to-titan-navy/70"></div>
            </div>

            <div class="relative z-20 text-center max-w-5xl px-6 pt-10" x-data="{ shown: false }" x-init="setTimeout(() => shown = true, 100)">


                <h1 :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'"
                    class="transition-all duration-1000 delay-300 font-black text-white mb-8 leading-[1] tracking-tighter uppercase drop-shadow-2xl"
                    style="font-size: clamp(2rem, 5vw, 3.5rem) !important;">
                    {{ $article['title'] }}
                </h1>

                <div :class="shown ? 'opacity-100' : 'opacity-0'" class="transition-all duration-1000 delay-500 flex items-center justify-center gap-6">
                    <div class="h-[1px] w-12 bg-titan-red"></div>
                    <p class="text-[10px] md:text-xs text-white/90 font-bold uppercase tracking-[0.4em]">
                        {{ $article['date'] }} · {{ $article['readTime'] }} · {{ __('By') }} {{ $article['author'] }}
                    </p>
                    <div class="h-[1px] w-12 bg-titan-red"></div>
                </div>
            </div>

        </header>

        <!-- MAIN CONTENT ARCHITECTURE -->
        <div class="max-w-[1240px] mx-auto px-6 grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 py-20 relative">



            <!-- READABLE ARTICLE AREA -->
            <div class="lg:col-span-10 lg:col-start-2 xl:col-span-8 xl:col-start-3 space-y-16">

                <!-- Lead & Metadata Row -->
                <div class="space-y-10 reveal-up">
                    <p
                        class="text-xl md:text-2xl font-black text-titan-navy leading-tight text-center italic">
                        {{ $article['excerpt'] }}
                    </p>
                    <div class="flex flex-wrap items-center justify-center gap-y-4 gap-x-12 pt-8 border-t border-gray-100">
                        <div class="flex items-center gap-3 group/meta">
                            <div class="w-9 h-9 rounded-full bg-gray-50 flex items-center justify-center group-hover/meta:bg-titan-red/10 transition-all duration-300">
                                <x-lucide-user class="w-4 h-4 text-titan-red" />
                            </div>
                            <span class="text-[10px] font-black text-titan-navy uppercase tracking-[0.2em]">{{ $article['author'] }}</span>
                        </div>
                        <div class="flex items-center gap-3 group/meta">
                            <div class="w-9 h-9 rounded-full bg-gray-50 flex items-center justify-center group-hover/meta:bg-titan-red/10 transition-all duration-300">
                                <x-lucide-calendar class="w-4 h-4 text-titan-red" />
                            </div>
                            <span class="text-[10px] font-black text-titan-navy uppercase tracking-[0.2em]">{{ $article['date'] }}</span>
                        </div>
                        <div class="flex items-center gap-3 group/meta">
                            <div class="w-9 h-9 rounded-full bg-gray-50 flex items-center justify-center group-hover/meta:bg-titan-red/10 transition-all duration-300">
                                <x-lucide-clock class="w-4 h-4 text-titan-red" />
                            </div>
                            <span class="text-[10px] font-black text-titan-navy uppercase tracking-[0.2em]">{{ $article['readTime'] }}</span>
                        </div>
                    </div>
                </div>

                <!-- Core Editorial Content -->
                <article
                    class="prose prose-lg md:prose-xl prose-slate max-w-none prose-p:text-titan-navy/70 prose-p:leading-[1.8] prose-p:font-medium prose-headings:font-black prose-headings:uppercase prose-headings:tracking-tighter prose-headings:text-titan-navy prose-table:border-collapse prose-table:w-full prose-th:border prose-th:border-gray-300 prose-th:bg-gray-50 prose-th:px-4 prose-th:py-3 prose-th:text-left prose-th:font-black prose-th:uppercase prose-th:tracking-wider prose-td:border prose-td:border-gray-300 prose-td:px-4 prose-td:py-3 reveal-up">
                    {!! $article['content'] !!}
                </article>

                <!-- CENTERED SOCIAL SHARING -->
                <div class="reveal-up pt-12 flex flex-col items-center gap-6">
                    <div class="text-[10px] font-black text-titan-navy/20 uppercase tracking-[0.4em]">{{ __('Share this Story') }}</div>
                    <div class="flex items-center gap-4">
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url('/news/' . $article['slug'])) }}" target="_blank" rel="noopener"
                            class="w-12 h-12 bg-social-facebook rounded flex items-center justify-center text-white hover:brightness-110 transition-all transform hover:-translate-y-1 shadow-xl shadow-social-facebook/20 group/fb">
                            <x-lucide-facebook class="w-5 h-5 transition-transform group-hover/fb:scale-110" />
                        </a>
                        <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(url('/news/' . $article['slug'])) }}" target="_blank" rel="noopener"
                            class="w-12 h-12 bg-social-linkedin rounded flex items-center justify-center text-white hover:brightness-110 transition-all transform hover:-translate-y-1 shadow-xl shadow-social-linkedin/20 group/li">
                            <x-lucide-linkedin class="w-5 h-5 transition-transform group-hover/li:scale-110" />
                        </a>
                        <a href="https://t.me/share/url?url={{ urlencode(url('/news/' . $article['slug'])) }}&text={{ urlencode($article['title']) }}" target="_blank" rel="noopener"
                            class="w-12 h-12 bg-social-telegram rounded flex items-center justify-center text-white hover:brightness-110 transition-all transform hover:-translate-y-1 shadow-xl shadow-social-telegram/20 group/tg">
                            <x-lucide-send class="w-5 h-5 transition-transform group-hover/tg:scale-110" />
                        </a>
                        <div x-data="{ 
                            copied: false, 
                            copyLink() {
                                navigator.clipboard.writeText(window.location.href);
                                this.copied = true;
                                setTimeout(() => this.copied = false, 2000);
                            }
                        }" class="relative">
                            <button @click="copyLink()"
                                class="w-12 h-12 bg-white border border-gray-100 rounded flex items-center justify-center text-titan-navy hover:bg-titan-navy hover:text-white transition-all transform hover:-translate-y-1 shadow-xl group/link">
                                <x-lucide-link class="w-5 h-5" x-show="!copied" />
                                <x-lucide-check class="w-5 h-5 text-green-500" x-show="copied" x-cloak />
                            </button>
                        </div>
                    </div>
                </div>

                <!-- AUTHOR BIO BOX -->
                <div class="mt-16 p-8 bg-gray-50 rounded border border-gray-100 flex flex-col items-center gap-8 reveal-up">
                    <div class="w-20 h-20 rounded-full bg-titan-navy flex items-center justify-center shrink-0">
                        <x-lucide-user class="w-8 h-8 text-white/50" />
                    </div>
                    <div class="text-center">
                        <div class="text-[10px] font-black text-titan-red uppercase tracking-[0.3em] mb-1">{{ __('Written By') }}</div>
                        <h4 class="text-xl font-black text-titan-navy uppercase tracking-tight mb-2">{{ $article['author'] }}</h4>
                        <p class="text-sm font-medium text-titan-navy/60 max-w-xl mx-auto">
                            {{ __('An editorial contributor for Kimmex, bringing you the latest updates on engineering, sustainability, and large-scale infrastructure projects.') }}
                        </p>
                    </div>

                    <!-- SOCIAL FOLLOW IN NEWS DETAILS (FILLED) -->
                    <div class="pt-6 border-t border-gray-200/50 w-full flex flex-col items-center gap-4">
                        <div class="text-[9px] font-black text-titan-navy/30 uppercase tracking-[0.3em]">{{ __('Follow Kimmex') }}</div>
                        <div class="flex items-center gap-3">
                            @if($facebook && $facebook !== '#')
                                <a href="{{ $facebook }}" target="_blank"
                                    class="w-10 h-10 rounded bg-social-facebook text-white flex items-center justify-center hover:brightness-110 transition-all duration-300 shadow-lg shadow-social-facebook/20">
                                    <x-lucide-facebook class="w-4 h-4" />
                                </a>
                            @endif
                            @if($linkedin && $linkedin !== '#')
                                <a href="{{ $linkedin }}" target="_blank"
                                    class="w-10 h-10 rounded bg-social-linkedin text-white flex items-center justify-center hover:brightness-110 transition-all duration-300 shadow-lg shadow-social-linkedin/20">
                                    <x-lucide-linkedin class="w-4 h-4" />
                                </a>
                            @endif
                            @if($youtube && $youtube !== '#')
                                <a href="{{ $youtube }}" target="_blank"
                                    class="w-10 h-10 rounded bg-social-youtube text-white flex items-center justify-center hover:brightness-110 transition-all duration-300 shadow-lg shadow-social-youtube/20">
                                    <x-lucide-youtube class="w-4 h-4" />
                                </a>
                            @endif
                            @if($instagram && $instagram !== '#')
                                <a href="{{ $instagram }}" target="_blank"
                                    class="w-10 h-10 rounded bg-social-instagram text-white flex items-center justify-center hover:brightness-110 transition-all duration-300 shadow-lg shadow-social-instagram/20">
                                    <x-lucide-instagram class="w-4 h-4" />
                                </a>
                            @endif
                            @if($telegram && $telegram !== '#')
                                <a href="{{ $telegram }}" target="_blank"
                                    class="w-10 h-10 rounded bg-social-telegram text-white flex items-center justify-center hover:brightness-110 transition-all duration-300 shadow-lg shadow-social-telegram/20">
                                    <x-lucide-send class="w-4 h-4" />
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- GALLERY SECTION: BENTO GRID & LIGHTBOX -->
                @if(!empty($article['gallery']))
                    <div class="reveal-up pt-20" x-data="{ 
                        isOpen: false, 
                        currentIndex: 0,
                        images: {{ \Illuminate\Support\Js::from(array_values($article['gallery'])) }},
                        openLightbox(index) { 
                            this.currentIndex = index; 
                            this.isOpen = true; 
                            document.body.style.overflow = 'hidden';
                        },
                        closeLightbox() {
                            this.isOpen = false;
                            document.body.style.overflow = 'auto';
                        },
                        next() {
                            if (this.images.length > 0) {
                                this.currentIndex = (this.currentIndex + 1) % this.images.length;
                            }
                        },
                        prev() {
                            if (this.images.length > 0) {
                                this.currentIndex = (this.currentIndex - 1 + this.images.length) % this.images.length;
                            }
                        }
                    }">
                        <div class="flex items-center justify-between mb-8">
                            <div class="text-[10px] font-black text-titan-red uppercase tracking-[0.4em]">
                                {{ __('Project Insight Gallery') }}
                            </div>
                            <div class="text-[9px] font-black text-titan-navy/20 uppercase tracking-widest">
                                {{ count($article['gallery']) }} {{ __('Assets') }}
                            </div>
                        </div>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            @foreach($article['gallery'] as $index => $img)
                                @if($index < 5)
                                    <div @click="openLightbox({{ $index }})"
                                        class="group relative overflow-hidden bg-gray-100 cursor-pointer transition-all duration-700
                                        {{ $index === 0 ? 'md:col-span-2 md:row-span-2 aspect-square md:aspect-auto' : 'aspect-square' }}">
                                        
                                        <!-- Image -->
                                        <img src="{{ $img }}"
                                            class="w-full h-full object-cover transition-transform duration-[1.5s] group-hover:scale-110"
                                            loading="lazy" />

                                        <!-- Subtle Index Number -->
                                        <div class="absolute top-4 left-4 opacity-0 group-hover:opacity-100 transition-all duration-500 transform -translate-y-2 group-hover:translate-y-0 z-10">
                                            <span class="text-[10px] font-black text-white bg-titan-navy px-2 py-1">
                                                {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                                            </span>
                                        </div>

                                        @if($index === 4 && count($article['gallery']) > 5)
                                            <!-- +X Overlay -->
                                            <div class="absolute inset-0 bg-titan-navy/80 flex flex-col items-center justify-center transition-all duration-500 z-20">
                                                <span class="text-3xl font-black text-white">+{{ count($article['gallery']) - 5 }}</span>
                                                <span class="text-[9px] font-black text-white/50 uppercase tracking-widest mt-1">{{ __('More Assets') }}</span>
                                            </div>
                                        @else
                                            <!-- Hover Overlay -->
                                            <div class="absolute inset-0 bg-titan-navy/40 opacity-0 group-hover:opacity-100 transition-opacity duration-500 flex items-center justify-center z-20">
                                                <x-lucide-maximize-2 class="w-6 h-6 text-white transform scale-50 group-hover:scale-100 transition-transform duration-500" />
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            @endforeach
                        </div>

                        <!-- REFINED LIGHTBOX MODAL -->
                        <div x-show="isOpen" 
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0"
                             x-transition:enter-end="opacity-100"
                             x-transition:leave="transition ease-in duration-200"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0"
                             class="fixed inset-0 z-[200] flex items-center justify-center bg-titan-navy/95 backdrop-blur-xl p-6"
                             @keydown.escape.window="closeLightbox()"
                             @keydown.right.window="next()"
                             @keydown.left.window="prev()"
                             style="display: none;">
                            
                            <button @click="closeLightbox()" class="absolute top-8 right-8 text-white/50 hover:text-white transition-colors z-[210]">
                                <x-lucide-x class="w-8 h-8" />
                            </button>

                            <button @click.stop="prev()" class="absolute left-4 md:left-8 top-1/2 -translate-y-1/2 w-12 h-12 flex items-center justify-center text-white/50 hover:text-white hover:bg-white/10 rounded-full transition-all z-[210]" x-show="images.length > 1">
                                <x-lucide-chevron-left class="w-8 h-8" />
                            </button>
                            
                            <button @click.stop="next()" class="absolute right-4 md:right-8 top-1/2 -translate-y-1/2 w-12 h-12 flex items-center justify-center text-white/50 hover:text-white hover:bg-white/10 rounded-full transition-all z-[210]" x-show="images.length > 1">
                                <x-lucide-chevron-right class="w-8 h-8" />
                            </button>

                            <div class="absolute bottom-8 left-1/2 -translate-x-1/2 text-white/50 font-black tracking-widest text-xs z-[210]">
                                <span x-text="currentIndex + 1"></span> / <span x-text="images.length"></span>
                            </div>

                            <img :src="images[currentIndex]" class="max-w-full max-h-[85vh] object-contain relative z-[205]" @click.away="closeLightbox()">
                        </div>
                    </div>
                @endif

                <!-- TAGS -->
                <div class="pt-10 border-t border-gray-50 flex items-center flex-wrap gap-3 reveal-up">
                    <span
                        class="text-[9px] font-black text-titan-navy/20 uppercase tracking-widest">{{ __('Tags:') }}</span>
                    @foreach($article['tags'] as $tag)
                        <span
                            class="px-4 py-1.5 bg-gray-50 rounded-full text-[9px] font-black text-titan-navy/40 uppercase tracking-widest hover:text-titan-red hover:bg-titan-red/5 cursor-pointer transition-colors">{{ $tag }}</span>
                    @endforeach
                </div>

                <!-- PREVIOUS / NEXT ARTICLE NAVIGATION -->
                @if($prevArticle || $nextArticle)
                <div class="mt-16 border-t border-b border-gray-100 py-8 grid grid-cols-1 md:grid-cols-2 gap-8 reveal-up">
                    <div>
                        @if($prevArticle)
                            <a href="/news/{{ $prevArticle['slug'] }}" class="group block">
                                <div class="text-[9px] font-black text-titan-navy/30 uppercase tracking-[0.2em] mb-2 group-hover:text-titan-red transition-colors">{{ __('Previous Article') }}</div>
                                <h4 class="text-lg font-black text-titan-navy leading-tight group-hover:text-titan-red transition-colors">{{ $prevArticle['title'] }}</h4>
                            </a>
                        @endif
                    </div>
                    <div class="md:text-right">
                        @if($nextArticle)
                            <a href="/news/{{ $nextArticle['slug'] }}" class="group block">
                                <div class="text-[9px] font-black text-titan-navy/30 uppercase tracking-[0.2em] mb-2 group-hover:text-titan-red transition-colors">{{ __('Next Article') }}</div>
                                <h4 class="text-lg font-black text-titan-navy leading-tight group-hover:text-titan-red transition-colors">{{ $nextArticle['title'] }}</h4>
                            </a>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- REFINED LATEST FEED -->
        <section class="bg-kmd-bg-alt py-24 px-6">
            <div class="max-w-[1240px] mx-auto">
                <div class="flex items-end justify-between mb-16 px-4">
                    <div>
                        <div class="text-[10px] font-black text-titan-red uppercase tracking-[0.5em] mb-4">
                            {{ __('DISCOVER MORE') }}
                        </div>
                        <h2 class="text-3xl md:text-5xl font-black text-titan-navy uppercase tracking-tighter">
                            {{ __('Recent') }} <span class="text-titan-red">{{ __('Highlights') }}</span>
                        </h2>
                    </div>
                    <a href="/news"
                        class="w-12 h-12 bg-titan-navy text-white rounded flex items-center justify-center hover:bg-titan-red transition-all group">
                        <x-lucide-arrow-right class="w-5 h-5 group-hover:translate-x-1 transition-transform" />
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                    @foreach($relatedArticles as $rel)
                        <a href="/news/{{ $rel['slug'] }}"
                            class="group block overflow-hidden transform hover:-translate-y-2 transition-all duration-500 rounded">
                            <div class="aspect-[16/10] bg-titan-navy relative overflow-hidden mb-6 rounded">
                                <img src="{{ ($rel['image'] && \Illuminate\Support\Facades\Storage::disk('public')->exists($rel['image'])) ? \Illuminate\Support\Facades\Storage::url($rel['image']) : '/images/projects/Thumbnail-' . ($loop->index + 1) . '.jpg' }}"
                                    class="w-full h-full object-cover transition-transform duration-[10s] group-hover:scale-110" loading="lazy" />
                            </div>
                            <h4
                                class="text-lg font-black text-titan-navy group-hover:text-titan-red transition-colors leading-tight mb-3">
                                {{ $rel['title'] }}
                            </h4>
                            <div class="text-[9px] font-black text-titan-navy/20 uppercase tracking-widest">
                                {{ $rel['date'] }}
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>

    </div>

    <!-- REFINED STYLES -->
    <style>
        @@keyframes revealUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .reveal-up {
            animation: revealUp 0.8s ease-out forwards;
            opacity: 0;
        }

        @@supports (view-timeline-name: --revealing) {
            .reveal-up {
                animation: revealUp both;
                animation-timeline: view();
                animation-range: entry 5% cover 25%;
            }
        }

        article img {
            margin: 3.5rem 0 !important;
        }

        article iframe {
            margin: 3.5rem 0 !important;
            aspect-ratio: 16 / 9;
            width: 100%;
        }

        /* Table Styles */
        article table {
            width: 100%;
            border-collapse: collapse;
            margin: 2rem 0;
            font-size: 0.95rem;
        }

        article th {
            background: var(--color-kmd-bg-alt);
            border: 1px solid var(--color-kmd-bg-section);
            padding: 12px 16px;
            text-align: left;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--color-kmd-navy);
        }

        article td {
            border: 1px solid var(--color-kmd-bg-section);
            padding: 12px 16px;
            color: var(--color-kmd-navy-subtle);
        }

        article tr:nth-child(even) {
            background: var(--color-kmd-bg-alt);
        }

        article tr:hover {
            background: var(--color-kmd-bg-alt);
        }
    </style>

</x-layouts.app>