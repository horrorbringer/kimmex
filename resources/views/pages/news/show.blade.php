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
                'content' => $articleDb->getTranslation('content', app()->getLocale()),
                'gallery' => $articleDb->gallery ?? []
            ];
        } else {
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
                'gallery' => []
            ];
        }

        // Fetch related from DB
        $relatedDb = \App\Models\NewsArticle::where('slug', '!=', $slug)->latest()->take(3)->get();
        $relatedArticles = $relatedDb->map(function (\App\Models\NewsArticle $r) {
            return [
                'slug' => $r->slug,
                'title' => $r->getTranslation('title', app()->getLocale()),
                'date' => $r->publishedAt ? $r->publishedAt->format('M d, Y') : $r->created_at->format('M d, Y'),
                'category' => $r->category ?? __('Updates')
            ];
        });
    @endphp

    <div class="bg-white min-h-screen text-titan-navy font-sans antialiased" x-data="{ 
        scrolled: false, 
        progress: 0,
        headings: [],
        activeHeading: null
    }" x-init="
        window.addEventListener('scroll', () => {
            scrolled = window.scrollY > 400;
            const scrollTotal = document.documentElement.scrollHeight - window.innerHeight;
            progress = (window.scrollY / scrollTotal) * 100;
            
            // Find active heading
            const hItems = Array.from(document.querySelectorAll('article h2, article h3'));
            const current = hItems.find(h => h.getBoundingClientRect().top > 0 && h.getBoundingClientRect().top < 200);
            if(current) activeHeading = current.id || current.innerText;
        });

        // Initialize headings from article
        $nextTick(() => {
            const hTags = document.querySelectorAll('article h2, article h3');
            hTags.forEach((h, i) => {
                if(!h.id) h.id = 'heading-' + i;
                headings.push({ id: h.id, text: h.innerText, level: h.tagName });
            });
        });
    ">

        <!-- READING PROGRESS & STICKY NAV -->
        <div class="fixed top-0 left-0 w-full z-[100] transition-transform duration-500"
            :class="scrolled ? 'translate-y-0' : '-translate-y-full'">
            <div class="h-1 bg-gray-100 w-full relative">
                <div class="h-full bg-titan-red absolute left-0 top-0 transition-all duration-150"
                    :style="'width: ' + progress + '%'"></div>
            </div>
            <div class="bg-white/95 backdrop-blur-md border-b border-gray-100 h-14 flex items-center px-6">
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
                                <a href="#"
                                    class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center hover:bg-titan-red hover:text-white transition-all"><x-lucide-facebook
                                        class="w-3.5 h-3.5" /></a>
                                <a href="#"
                                    class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center hover:bg-titan-red hover:text-white transition-all"><x-lucide-linkedin
                                        class="w-3.5 h-3.5" /></a>
                            </div>
                        </div>
                        <a href="/news"
                            class="w-8 h-8 rounded-lg bg-titan-navy text-white flex items-center justify-center hover:bg-titan-red transition-all shadow-xl shadow-titan-navy/20"><x-lucide-arrow-left
                                class="w-4 h-4" /></a>
                    </div>
                </div>
            </div>
        </div>

        <!-- REFINED NARRATIVE HERO -->
        <header class="relative w-full h-[60vh] md:h-[70vh] overflow-hidden bg-titan-navy">
            <img src="{{ $article['image'] }}" alt="{{ $article['title'] }}"
                class="absolute inset-0 w-full h-full object-cover opacity-60 scale-105" />
            <div class="absolute inset-0 bg-gradient-to-b from-titan-navy/30 via-transparent to-titan-navy"></div>

            <div class="absolute inset-0 flex flex-col items-center justify-end pb-24 px-6 text-center">
                <div class="max-w-4xl mx-auto space-y-6" x-data="{ shown: false }"
                    x-init="setTimeout(() => shown = true, 100)">
                    <div :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-6'"
                        class="transition-all duration-700 inline-flex items-center gap-2 px-4 py-1.5 bg-titan-red text-white text-[9px] font-black uppercase tracking-[0.3em] rounded-full shadow-2xl">
                        {{ $article['category'] }}
                    </div>
                    <h1 :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10'"
                        class="transition-all duration-700 delay-300 text-3xl md:text-5xl lg:text-6xl font-black text-white uppercase tracking-tighter leading-[1.1] drop-shadow-2xl">
                        {{ $article['title'] }}
                    </h1>
                </div>
            </div>
        </header>

        <!-- MAIN CONTENT ARCHITECTURE -->
        <div class="max-w-[1240px] mx-auto px-6 grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 py-20 relative">

            <!-- SIDEBAR (TOC & SHARES) -->
            <aside class="hidden lg:block lg:col-span-3">
                <div class="sticky top-32 space-y-12">
                    <!-- TOC -->
                    <div class="space-y-8" x-show="headings.length > 0">
                        <div
                            class="text-[10px] font-black text-titan-navy/10 uppercase tracking-[0.4em] border-b border-gray-100 pb-4">
                            {{ __('On This Page') }}
                        </div>
                        <nav class="flex flex-col gap-4">
                            <template x-for="h in headings" :key="h.id">
                                <a :href="'#' + h.id"
                                    @click.prevent="document.getElementById(h.id).scrollIntoView({ behavior: 'smooth', block: 'center' })"
                                    class="text-[10px] font-black uppercase tracking-[0.1em] transition-all duration-300 hover:translate-x-1"
                                    :class="activeHeading === h.id || activeHeading === h.text ? 'text-titan-red' : 'text-titan-navy/30 hover:text-titan-navy'"
                                    x-text="h.text"></a>
                            </template>
                        </nav>
                    </div>

                    <!-- Vertical Shares -->
                    <div class="space-y-8">
                        <div
                            class="text-[10px] font-black text-titan-navy/10 uppercase tracking-[0.4em] border-b border-gray-100 pb-4">
                            {{ __('Spread Word') }}
                        </div>
                        <div class="flex flex-col gap-3">
                            <a href="#"
                                class="w-11 h-11 rounded-2xl bg-gray-50 flex items-center justify-center hover:bg-titan-red hover:text-white transition-all transform hover:rotate-12 shadow-sm group">
                                <x-lucide-facebook class="w-4 h-4" />
                            </a>
                            <a href="#"
                                class="w-11 h-11 rounded-2xl bg-gray-50 flex items-center justify-center hover:bg-titan-red hover:text-white transition-all transform hover:rotate-12 shadow-sm group">
                                <x-lucide-linkedin class="w-4 h-4" />
                            </a>
                            <button onclick="window.print()"
                                class="w-11 h-11 rounded-2xl bg-gray-50 flex items-center justify-center hover:bg-titan-navy hover:text-white transition-all transform hover:rotate-12 shadow-sm">
                                <x-lucide-printer class="w-4 h-4" />
                            </button>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- READABLE ARTICLE AREA -->
            <div class="lg:col-span-9 xl:col-span-8 xl:col-start-4 space-y-16">

                <!-- Lead & Metadata Row -->
                <div class="space-y-10 reveal-up">
                    <p
                        class="text-xl md:text-2xl font-black text-titan-navy leading-tight border-l-4 border-titan-red pl-8 italic">
                        {{ $article['excerpt'] }}
                    </p>
                    <div
                        class="flex items-center gap-8 text-[9px] font-black text-titan-navy/30 uppercase tracking-[0.3em]">
                        <div class="flex items-center gap-2">
                            <x-lucide-user class="w-3.5 h-3.5 text-titan-red/60" />
                            {{ $article['author'] }}
                        </div>
                        <div class="flex items-center gap-2">
                            <x-lucide-calendar class="w-3.5 h-3.5 text-titan-red/60" />
                            {{ $article['date'] }}
                        </div>
                        <div class="flex items-center gap-2">
                            <x-lucide-clock class="w-3.5 h-3.5 text-titan-red/60" />
                            {{ $article['readTime'] }}
                        </div>
                    </div>
                </div>

                <!-- Core Editorial Content -->
                <article
                    class="prose prose-lg md:prose-xl prose-slate max-w-none prose-p:text-titan-navy/70 prose-p:leading-[1.8] prose-p:font-medium prose-headings:font-black prose-headings:uppercase prose-headings:tracking-tighter prose-headings:text-titan-navy reveal-up">
                    {!! $article['content'] !!}
                </article>

                <!-- GALLERY SECTION (IF MULTIPLE IMAGES) -->
                @if(!empty($article['gallery']))
                    <div class="reveal-up pt-20">
                        <div class="text-[10px] font-black text-titan-red uppercase tracking-[0.4em] mb-6">
                            {{ __('Project Insight Gallery') }}
                        </div>
                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                            @foreach($article['gallery'] as $img)
                                <div
                                    class="aspect-square rounded-2xl overflow-hidden bg-gray-100 group cursor-pointer shadow-sm hover:shadow-xl transition-all duration-500">
                                    <img src="{{ $img }}"
                                        class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                                        loading="lazy" />
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- TAGS -->
                <div class="pt-10 border-t border-gray-50 flex items-center gap-3 reveal-up">
                    <span
                        class="text-[9px] font-black text-titan-navy/20 uppercase tracking-widest">{{ __('Tags:') }}</span>
                    @foreach(explode(',', 'Engineering,Sustainability,Infrastructure') as $tag)
                        <span
                            class="px-4 py-1.5 bg-gray-50 rounded-lg text-[9px] font-black text-titan-navy/40 uppercase tracking-widest hover:text-titan-red cursor-pointer transition-colors">{{ $tag }}</span>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- REFINED LATEST FEED -->
        <section class="bg-[#FCFCFD] py-24 px-6">
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
                        class="w-12 h-12 rounded-xl bg-titan-navy text-white flex items-center justify-center hover:bg-titan-red transition-all group shadow-xl">
                        <x-lucide-arrow-right class="w-5 h-5 group-hover:translate-x-1 transition-transform" />
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                    @foreach($relatedArticles as $rel)
                        <a href="/news/{{ $rel['slug'] }}"
                            class="group block overflow-hidden transform hover:-translate-y-2 transition-all duration-500">
                            <div class="aspect-[16/10] rounded-2xl bg-titan-navy relative overflow-hidden mb-6 shadow-md">
                                <img src="/images/projects/Thumbnail-{{ $loop->index + 1 }}.jpg"
                                    class="w-full h-full object-cover transition-transform duration-[10s] group-hover:scale-110" />
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
            border-radius: 1.5rem;
            box-shadow: 0 30px 60px -12px rgba(0, 0, 0, 0.15);
            margin: 3.5rem 0 !important;
        }

        article iframe {
            border-radius: 1.5rem;
            box-shadow: 0 30px 60px -12px rgba(0, 0, 0, 0.15);
            margin: 3.5rem 0 !important;
            aspect-ratio: 16 / 9;
            width: 100%;
        }
    </style>

</x-layouts.app>