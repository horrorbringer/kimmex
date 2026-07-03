@php
    use App\Models\Document;
    use App\Models\JobPosting;
    use App\Models\NewsArticle;
    use Illuminate\Support\Facades\Cache;
    use Illuminate\Support\Str;

    /** @var string $slug */
    $locale = app()->getLocale();
    $fallbackImage = '/images/webp/hero/hero-3.webp';
    $resolveNewsImage = function (?string $path, string $fallback) {
        if (! filled($path)) {
            return $fallback;
        }

        return \App\Support\PublicStorage::urlIfExists($path, $fallback);
    };

    $article = Cache::remember("news_article_data_{$slug}_{$locale}", now()->addHours(12), function () use ($slug, $locale, $fallbackImage, $resolveNewsImage) {
        $articleDb = NewsArticle::where('isActive', true)
            ->where('publishedAt', '<=', now())
            ->where('slug', $slug)
            ->first();

        if (!$articleDb) {
            return null;
        }

        $excerpt = $articleDb->getTranslation('excerpt', $locale)
            ?: \Illuminate\Support\Str::limit(strip_tags($articleDb->getTranslation('content', $locale)), 180);

        return [
            'slug' => $articleDb->slug,
            'category' => $articleDb->getTranslation('category', $locale) ?: __('Updates'),
            'image' => $resolveNewsImage($articleDb->coverImage, $fallbackImage),
            'title' => $articleDb->getTranslation('title', $locale),
            'metaTitle' => $articleDb->getTranslation('metaTitle', $locale),
            'metaDescription' => $articleDb->getTranslation('metaDescription', $locale),
            'date' => $articleDb->publishedAt ? $articleDb->publishedAt->format('M d, Y') : $articleDb->created_at->format('M d, Y'),
            'publishedAt' => ($articleDb->publishedAt ?: $articleDb->created_at)->toIso8601String(),
            'updatedAt' => $articleDb->updated_at->toIso8601String(),
            'author' => $articleDb->getTranslation('authorName', $locale) ?: 'Kimmex Editorial',
            'readTime' => $articleDb->getTranslation('readTime', $locale) ?: (ceil(str_word_count(strip_tags($articleDb->getTranslation('content', $locale))) / 200) . ' min read'),
            'excerpt' => $excerpt,
            'content' => $articleDb->getTranslation('content', $locale),
            'tags' => is_array($articleDb->tags) && count($articleDb->tags) > 0 ? $articleDb->tags : [$articleDb->category ?: 'News'],
            'gallery' => collect($articleDb->gallery ?? [])->map(fn($img) => $resolveNewsImage($img, ''))->filter()->values()->toArray(),
            'videoUrl' => $articleDb->videoUrl,
        ];
    });

    if (!$article) {
        abort(404);
    }

    $relatedData = Cache::remember("news_related_array_{$slug}_{$locale}", now()->addHours(12), function () use ($slug, $locale, $fallbackImage, $resolveNewsImage, $article) {
        $relatedDb = NewsArticle::where('isActive', true)
            ->where('publishedAt', '<=', now())
            ->where('slug', '!=', $slug)
            ->orderByDesc('publishedAt')
            ->take(3)
            ->get();

        $related = $relatedDb->map(function (NewsArticle $r) use ($locale, $fallbackImage, $resolveNewsImage) {
            return [
                'slug' => $r->slug,
                'title' => $r->getTranslation('title', $locale),
                'date' => $r->publishedAt ? $r->publishedAt->format('M d, Y') : $r->created_at->format('M d, Y'),
                'category' => $r->getTranslation('category', $locale) ?: __('Updates'),
                'image' => $resolveNewsImage($r->coverImage, $fallbackImage),
            ];
        })->toArray();

        $next = null;
        $prev = null;
        if (! empty($article['publishedAt'])) {
            $currentPublishedAt = \Illuminate\Support\Carbon::parse($article['publishedAt']);
            $nextDb = NewsArticle::where('isActive', true)
                ->where('publishedAt', '<=', now())
                ->where('publishedAt', '<', $currentPublishedAt)
                ->orderByDesc('publishedAt')
                ->first();
            $prevDb = NewsArticle::where('isActive', true)
                ->where('publishedAt', '<=', now())
                ->where('publishedAt', '>', $currentPublishedAt)
                ->orderBy('publishedAt')
                ->first();
            if ($nextDb) {
                $next = ['slug' => $nextDb->slug, 'title' => $nextDb->getTranslation('title', $locale)];
            }
            if ($prevDb) {
                $prev = ['slug' => $prevDb->slug, 'title' => $prevDb->getTranslation('title', $locale)];
            }
        }

        return compact('related', 'next', 'prev');
    });

    $relatedArticles = $relatedData['related'] ?? [];
    $nextArticle = $relatedData['next'] ?? null;
    $prevArticle = $relatedData['prev'] ?? null;

    $pageTitle = ($article['metaTitle'] ?? null) ?: ($article['title'] ?? __('News Details'));
    $pageDesc = ($article['metaDescription'] ?? null) ?: ($article['excerpt'] ?? __('Read the latest news and updates from Kimmex.'));
    $pageImage = $article['image'] ?? null;
    $structuredImage = ($pageImage && Str::startsWith($pageImage, ['http://', 'https://']))
        ? $pageImage
        : url($pageImage ?: '/logo.png');
    $canonicalUrl = route('news.show', ['slug' => $article['slug']]);
    $articleSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'NewsArticle',
        'mainEntityOfPage' => [
            '@type' => 'WebPage',
            '@id' => $canonicalUrl,
        ],
        'headline' => $article['title'] ?? '',
        'description' => $pageDesc,
        'image' => [$structuredImage],
        'datePublished' => $article['publishedAt'] ?? now()->toIso8601String(),
        'dateModified' => $article['updatedAt'] ?? ($article['publishedAt'] ?? now()->toIso8601String()),
        'author' => [
            '@type' => 'Person',
            'name' => $article['author'] ?? 'Kimmex Editorial',
        ],
        'publisher' => [
            '@type' => 'Organization',
            'name' => 'Kimmex',
            'logo' => [
                '@type' => 'ImageObject',
                'url' => url('/logo.png'),
            ],
        ],
        'articleSection' => $article['category'] ?? __('Updates'),
        'keywords' => implode(', ', $article['tags'] ?? []),
        'url' => $canonicalUrl,
    ];
    $breadcrumbSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => [
            [
                '@type' => 'ListItem',
                'position' => 1,
                'name' => __('Home'),
                'item' => route('home'),
            ],
            [
                '@type' => 'ListItem',
                'position' => 2,
                'name' => __('News'),
                'item' => route('news.index'),
            ],
            [
                '@type' => 'ListItem',
                'position' => 3,
                'name' => $article['title'] ?? '',
                'item' => $canonicalUrl,
            ],
        ],
    ];
    $schema = [$articleSchema, $breadcrumbSchema];

    $renderNewsContent = function (?string $content) {
        $content = trim((string) $content);

        if ($content === '') {
            return '';
        }

        // RichEditor stores HTML. If the content already contains tags, render it as-is.
        // Plain text fallback is wrapped in a paragraph for consistency.
        if (preg_match('/<\s*[a-z][\s\S]*>/i', $content)) {
            return $content;
        }

        return '<p>' . e($content) . '</p>';
    };

    $getVideoEmbedUrl = function (?string $url) {
        if (!$url) return null;

        // YouTube pattern
        $ytPattern = '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|shorts\/|watch\?v=)|youtu\.be\/)([^"&?\/ ]{11})/i';
        if (preg_match($ytPattern, $url, $matches)) {
            return "https://www.youtube.com/embed/" . $matches[1];
        }

        // Vimeo pattern
        $vimeoPattern = '/vimeo\.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|video\/|)(\d+)(?:$|\/|\?)/i';
        if (preg_match($vimeoPattern, $url, $matches)) {
            return "https://player.vimeo.com/video/" . $matches[3];
        }

        return null;
    };

    $sidebarDocs = Cache::remember("news_sidebar_documents_{$locale}", now()->addHours(12), function () use ($locale) {
        return Document::with('documentCategory')
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

    $sidebarJobs = Cache::remember("news_sidebar_jobs_{$locale}", now()->addHours(12), function () use ($locale) {
        return JobPosting::where('isActive', true)
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

<x-layouts.app :title="$pageTitle" :description="$pageDesc" :image="$pageImage" :canonical="$canonicalUrl" og-type="article">
    @push('head')
        <meta property="article:published_time" content="{{ $article['publishedAt'] }}">
        <meta property="article:modified_time" content="{{ $article['updatedAt'] }}">
        <meta property="article:author" content="{{ $article['author'] }}">
    @endpush

    <script type="application/ld+json">
        {!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>

    <div class="bg-[#F7F8FA] min-h-screen text-titan-navy font-sans antialiased pt-28">
        <!-- TOP BAR -->
        <div class="sticky top-20 z-[80] bg-white/95 backdrop-blur border-b border-gray-200">
            <div class="h-1 bg-gray-100 w-full relative">
                <div class="h-full bg-titan-red absolute left-0 top-0" style="width: 100%"></div>
            </div>
            <div class="max-w-[1240px] mx-auto px-6 h-10 md:h-11 flex items-center justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <a href="/news" class="w-7 h-7 rounded border border-gray-200 bg-white text-titan-navy flex items-center justify-center hover:border-titan-red/30 hover:text-titan-red transition-colors shrink-0">
                        <x-lucide-arrow-left class="w-4 h-4" />
                    </a>
                    <div class="min-w-0">
                        <div class="text-[8px] font-black uppercase tracking-[0.24em] text-titan-red leading-none">{{ __('Now Reading') }}</div>
                        <div class="text-[10px] font-black uppercase tracking-tight text-titan-navy truncate max-w-[180px] md:max-w-[360px] leading-tight">{{ $article['title'] }}</div>
                    </div>
                </div>

                <div class="hidden md:flex items-center gap-2">
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url('/news/' . $article['slug'])) }}" target="_blank" rel="noopener" class="w-7 h-7 rounded bg-social-facebook text-white flex items-center justify-center hover:brightness-110 transition-all">
                        <x-lucide-facebook class="w-3.5 h-3.5" />
                    </a>
                    <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(url('/news/' . $article['slug'])) }}" target="_blank" rel="noopener" class="w-7 h-7 rounded bg-social-linkedin text-white flex items-center justify-center hover:brightness-110 transition-all">
                        <x-lucide-linkedin class="w-3.5 h-3.5" />
                    </a>
                    <a href="https://t.me/share/url?url={{ urlencode(url('/news/' . $article['slug'])) }}&text={{ urlencode($article['title']) }}" target="_blank" rel="noopener" class="w-7 h-7 rounded bg-social-telegram text-white flex items-center justify-center hover:brightness-110 transition-all">
                        <x-lucide-send class="w-3.5 h-3.5" />
                    </a>
                </div>
            </div>
        </div>

        <!-- ARTICLE HEADER -->
        <header class="border-b border-gray-200 bg-white">
            <div class="max-w-[1240px] mx-auto px-6 py-8 md:py-12">
                <div class="grid grid-cols-1 lg:grid-cols-[0.94fr_1.06fr] gap-8 lg:gap-12 items-center">
                    <div>
                        <div class="flex flex-wrap items-center gap-2 mb-4">
                            <span class="rounded bg-titan-red text-white px-3 py-1.5 text-[10px] font-black uppercase tracking-[0.16em]">{{ $article['category'] }}</span>
                            <span class="rounded border border-gray-200 bg-gray-50 px-3 py-1.5 text-[10px] font-black uppercase tracking-[0.16em] text-titan-navy/55">{{ $article['date'] }}</span>
                            <span class="rounded border border-gray-200 bg-gray-50 px-3 py-1.5 text-[10px] font-black uppercase tracking-[0.16em] text-titan-navy/55">{{ $article['readTime'] }}</span>
                        </div>

                        <h1 class="font-bold uppercase leading-[1.04] tracking-normal text-titan-navy max-w-3xl"
                            style="font-size: clamp(2rem, 3.35vw, 3.75rem) !important;">
                            {{ $article['title'] }}
                        </h1>

                        <p class="mt-5 max-w-2xl text-base md:text-lg leading-relaxed text-titan-navy/60 font-medium">
                            {{ $article['excerpt'] }}
                        </p>

                        <div class="mt-7 flex flex-wrap items-center gap-3">
                            <div class="inline-flex items-center gap-2 rounded border border-gray-200 bg-white px-4 py-2">
                                <div class="w-8 h-8 rounded bg-titan-navy/5 flex items-center justify-center">
                                    <x-lucide-user class="w-4 h-4 text-titan-red" />
                                </div>
                                <div>
                                    <div class="text-[9px] font-black uppercase tracking-[0.16em] text-titan-red">{{ __('Written by') }}</div>
                                    <div class="text-sm font-black text-titan-navy">{{ $article['author'] }}</div>
                                </div>
                            </div>
                            <a href="#article-body" class="h-11 px-5 rounded bg-titan-navy text-white inline-flex items-center gap-2 text-[10px] font-black uppercase tracking-[0.16em] hover:bg-titan-red transition-colors">
                                <x-lucide-book-open class="w-4 h-4" />
                                {{ __('Read Article') }}
                            </a>
                        </div>
                    </div>

                    <div class="relative">
                        <div class="relative rounded overflow-hidden border border-gray-200 bg-titan-navy shadow-[0_20px_60px_rgba(0,0,0,0.12)] aspect-[16/11]">
                            @if($article['image'])
                                <img src="{{ $article['image'] }}" alt="{{ $article['title'] }}" class="w-full h-full object-cover" decoding="async" loading="lazy" />
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-[radial-gradient(circle_at_30%_20%,rgba(227,30,36,0.16)_0%,transparent_50%)]">
                                    <x-lucide-newspaper class="w-20 h-20 text-white/10" />
                                </div>
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-titan-navy/65 via-transparent to-transparent"></div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 mt-3">
                            <div class="rounded border border-gray-200 bg-white p-4 shadow-sm">
                                <div class="text-[9px] font-black uppercase tracking-[0.18em] text-titan-navy/35">{{ __('Category') }}</div>
                                <div class="mt-1 text-sm font-black text-titan-navy line-clamp-1">{{ $article['category'] }}</div>
                            </div>
                            <div class="rounded border border-gray-200 bg-white p-4 shadow-sm">
                                <div class="text-[9px] font-black uppercase tracking-[0.18em] text-titan-navy/35">{{ __('Tags') }}</div>
                                <div class="mt-1 text-sm font-black text-titan-navy line-clamp-1">{{ implode(' · ', array_slice($article['tags'], 0, 2)) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- CONTENT -->
        <main id="article-body" class="max-w-[1240px] mx-auto px-6 py-12 md:py-14">
            <div class="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-8 lg:gap-12 items-start">
                <article class="rounded border border-gray-200 bg-white p-6 md:p-10">
                    <div class="text-[10px] font-black uppercase tracking-[0.24em] text-titan-red mb-5">
                        {{ __('Story') }}
                    </div>

                    <div class="news-content prose prose-lg md:prose-xl prose-slate max-w-none prose-p:text-titan-navy/70 prose-p:leading-[1.85] prose-p:font-medium prose-headings:font-black prose-headings:tracking-normal prose-headings:text-titan-navy prose-a:text-titan-red prose-strong:text-titan-navy">
                        {!! $renderNewsContent($article['content'] ?? '') !!}
                    </div>

                    @if(!empty($article['videoUrl']) && $getVideoEmbedUrl($article['videoUrl']))
                        <section class="mt-12 pt-10 border-t border-gray-200">
                            <div class="text-[10px] font-black uppercase tracking-[0.24em] text-titan-red mb-2">
                                {{ __('Video') }}
                            </div>
                            <h2 class="text-xl md:text-2xl font-black uppercase tracking-normal text-titan-navy mb-6">
                                {{ __('Featured Media') }}
                            </h2>
                            <div class="aspect-[16/9] w-full overflow-hidden rounded shadow-md border border-gray-200 bg-black">
                                <iframe src="{{ $getVideoEmbedUrl($article['videoUrl']) }}" 
                                        class="w-full h-full border-0" 
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                        allowfullscreen></iframe>
                            </div>
                        </section>
                    @endif

                    @if(!empty($article['gallery']))
                        <section class="mt-12 pt-10 border-t border-gray-200">
                            <div class="flex items-end justify-between gap-4 mb-6">
                                <div>
                                    <div class="text-[10px] font-black uppercase tracking-[0.24em] text-titan-red mb-2">
                                        {{ __('Gallery') }}
                                    </div>
                                    <h2 class="text-xl md:text-2xl font-black uppercase tracking-normal text-titan-navy">
                                        {{ __('Project visuals') }}
                                    </h2>
                                </div>
                                <div class="text-[10px] font-black uppercase tracking-[0.16em] text-titan-navy/30">
                                    {{ count($article['gallery']) }} {{ __('Assets') }}
                                </div>
                            </div>

                            @php
                                $galleryCount = count($article['gallery']);
                            @endphp

                            @if($galleryCount === 1)
                                <div class="aspect-[16/9] overflow-hidden rounded shadow-sm border border-gray-200">
                                    <img src="{{ $article['gallery'][0] }}" alt="Gallery 1" class="w-full h-full object-cover hover:scale-105 transition-transform duration-500" decoding="async" loading="lazy" />
                                </div>
                            @elseif($galleryCount === 2)
                                <div class="grid grid-cols-2 gap-2">
                                    <div class="aspect-[4/3] overflow-hidden rounded shadow-sm border border-gray-200">
                                        <img src="{{ $article['gallery'][0] }}" alt="Gallery 1" class="w-full h-full object-cover hover:scale-105 transition-transform duration-500" decoding="async" loading="lazy" />
                                    </div>
                                    <div class="aspect-[4/3] overflow-hidden rounded shadow-sm border border-gray-200">
                                        <img src="{{ $article['gallery'][1] }}" alt="Gallery 2" class="w-full h-full object-cover hover:scale-105 transition-transform duration-500" decoding="async" loading="lazy" />
                                    </div>
                                </div>
                            @else
                                <!-- Premium Grid Layout -->
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                                    <!-- Main Big Image (Left) -->
                                    <div class="md:col-span-2 aspect-[4/3] md:aspect-auto md:h-[400px] overflow-hidden rounded shadow-sm border border-gray-200">
                                        <img src="{{ $article['gallery'][0] }}" alt="Gallery 1" class="w-full h-full object-cover hover:scale-105 transition-transform duration-500" decoding="async" loading="lazy" />
                                    </div>
                                    <!-- Stacked Right Images -->
                                    <div class="grid grid-rows-2 gap-2 h-auto md:h-[400px]">
                                        @for($i = 1; $i <= 2; $i++)
                                            @if(isset($article['gallery'][$i]))
                                                <div class="aspect-[16/10] md:aspect-auto overflow-hidden rounded shadow-sm border border-gray-200">
                                                    <img src="{{ $article['gallery'][$i] }}" alt="Gallery {{ $i + 1 }}" class="w-full h-full object-cover hover:scale-105 transition-transform duration-500" decoding="async" loading="lazy" />
                                                </div>
                                            @endif
                                        @endfor
                                    </div>
                                </div>

                                <!-- Bottom row: Up to 5 thumbnails -->
                                @if($galleryCount > 3)
                                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-2 mt-2">
                                        @for($i = 3; $i < min($galleryCount, 8); $i++)
                                            @php
                                                $isLastVisible = ($i === 7 && $galleryCount > 8);
                                            @endphp
                                            <div class="relative aspect-[4/3] overflow-hidden rounded shadow-sm border border-gray-200">
                                                <img src="{{ $article['gallery'][$i] }}" alt="Gallery {{ $i + 1 }}" class="w-full h-full object-cover hover:scale-105 transition-transform duration-500" decoding="async" loading="lazy" />
                                                @if($isLastVisible)
                                                    <div class="absolute inset-0 bg-black/60 flex items-center justify-center text-white font-black text-lg tracking-wider">
                                                        +{{ $galleryCount - 7 }} {{ __('photos') }}
                                                    </div>
                                                @endif
                                            </div>
                                        @endfor
                                    </div>
                                @endif
                            @endif
                        </section>
                    @endif

                    <div class="mt-12 pt-8 border-t border-gray-200 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div class="flex items-center gap-2 text-[10px] font-black uppercase tracking-[0.18em] text-titan-navy/30">
                            <x-lucide-share-2 class="w-4 h-4 text-titan-red" />
                            {{ __('Share this story') }}
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url('/news/' . $article['slug'])) }}" target="_blank" rel="noopener" class="w-10 h-10 rounded bg-social-facebook text-white flex items-center justify-center hover:brightness-110 transition-all">
                                <x-lucide-facebook class="w-4 h-4" />
                            </a>
                            <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(url('/news/' . $article['slug'])) }}" target="_blank" rel="noopener" class="w-10 h-10 rounded bg-social-linkedin text-white flex items-center justify-center hover:brightness-110 transition-all">
                                <x-lucide-linkedin class="w-4 h-4" />
                            </a>
                            <a href="https://t.me/share/url?url={{ urlencode(url('/news/' . $article['slug'])) }}&text={{ urlencode($article['title']) }}" target="_blank" rel="noopener" class="w-10 h-10 rounded bg-social-telegram text-white flex items-center justify-center hover:brightness-110 transition-all">
                                <x-lucide-send class="w-4 h-4" />
                            </a>
                            <button x-data="{ 
                                    copied: false,
                                    copyLink() {
                                        const url = window.location.href;
                                        if (navigator.clipboard && navigator.clipboard.writeText) {
                                            navigator.clipboard.writeText(url).catch(() => {});
                                        } else {
                                            const el = document.createElement('textarea');
                                            el.value = url;
                                            document.body.appendChild(el);
                                            el.select();
                                            document.execCommand('copy');
                                            document.body.removeChild(el);
                                        }
                                        this.copied = true;
                                        setTimeout(() => this.copied = false, 1600);
                                    }
                                }"
                                @click="copyLink()"
                                class="h-10 px-4 rounded border border-gray-200 bg-white text-titan-navy hover:text-titan-red hover:border-titan-red/30 inline-flex items-center gap-2 text-[10px] font-black uppercase tracking-[0.14em] transition-all">
                                <x-lucide-link class="w-4 h-4" />
                                <span x-text="copied ? '{{ __('Copied') }}' : '{{ __('Copy') }}'"></span>
                            </button>
                        </div>
                    </div>
                </article>

                <aside class="space-y-4 lg:sticky lg:top-24">
                    <div class="rounded border border-gray-200 bg-white p-5">
                        <div class="text-[10px] font-normal uppercase tracking-[0.24em] text-titan-red mb-4">
                            {{ __('Related Stories') }}
                        </div>
                        <div class="space-y-3">
                            @forelse($relatedArticles as $rel)
                                <a href="/news/{{ $rel['slug'] }}" class="group flex items-start gap-3 p-3 rounded hover:bg-gray-50 transition-colors">
                                    <div class="w-10 h-10 rounded overflow-hidden bg-titan-navy/5 flex items-center justify-center shrink-0">
                                        <img src="{{ $rel['image'] }}" alt="{{ $rel['title'] }}" class="w-full h-full object-cover" loading="lazy" decoding="async" />
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="text-[9px] font-normal uppercase tracking-[0.16em] text-titan-red mb-1">
                                            {{ $rel['category'] }}
                                        </div>
                                        <div class="text-sm font-normal text-titan-navy leading-tight line-clamp-2 group-hover:text-titan-red transition-colors">
                                            {{ $rel['title'] }}
                                        </div>
                                        <div class="mt-1 text-[10px] text-titan-navy/30 font-normal">
                                            {{ $rel['date'] }}
                                        </div>
                                    </div>
                                </a>
                            @empty
                                <div class="text-sm text-titan-navy/40 font-normal">{{ __('No related stories yet.') }}</div>
                            @endforelse
                        </div>
                    </div>

                    <div class="rounded border border-gray-200 bg-white p-5">
                        <div class="text-[10px] font-normal uppercase tracking-[0.24em] text-titan-red mb-4">
                            {{ __('Documents') }}
                        </div>
                        <div class="space-y-3">
                            @forelse($sidebarDocs as $doc)
                                <a href="/documents/{{ $doc['slug'] }}" class="group flex items-start gap-3 p-3 rounded hover:bg-gray-50 transition-colors">
                                    <div class="w-10 h-10 rounded bg-titan-navy/5 flex items-center justify-center shrink-0">
                                        <x-lucide-file-text class="w-4 h-4 text-titan-navy/30 group-hover:text-titan-red transition-colors" />
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="text-[9px] font-normal uppercase tracking-[0.16em] text-titan-red mb-1">
                                            {{ $doc['category'] }}
                                        </div>
                                        <div class="text-sm font-normal text-titan-navy leading-tight line-clamp-2 group-hover:text-titan-red transition-colors">
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

                    <div class="rounded border border-gray-200 bg-titan-navy p-5 text-white">
                        <div class="text-[10px] font-normal uppercase tracking-[0.24em] text-titan-red mb-3">
                            {{ __('Careers') }}
                        </div>
                        <div class="space-y-3">
                            @forelse($sidebarJobs as $job)
                                <a href="/careers/{{ $job['slug'] }}" class="group flex items-start gap-3 p-3 rounded hover:bg-white/5 transition-colors">
                                    <div class="w-10 h-10 rounded bg-white/10 flex items-center justify-center shrink-0">
                                        <x-lucide-briefcase class="w-4 h-4 text-titan-red" />
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="text-[9px] font-normal uppercase tracking-[0.16em] text-titan-red mb-1">
                                            {{ $job['dept'] }}
                                        </div>
                                        <div class="text-sm font-normal text-white leading-tight line-clamp-2 group-hover:text-titan-red transition-colors">
                                            {{ $job['title'] }}
                                        </div>
                                        <div class="mt-1 text-[10px] text-white/45 font-normal">
                                            {{ $job['location'] }} · {{ $job['type'] }}
                                        </div>
                                    </div>
                                </a>
                            @empty
                                <div class="text-sm text-white/55 font-normal">{{ __('No open roles right now.') }}</div>
                            @endforelse
                        </div>
                    </div>
                </aside>
            </div>
        </main>

        <section class="max-w-[1240px] mx-auto px-6 pb-16">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @if($prevArticle)
                    <a href="/news/{{ $prevArticle['slug'] }}" class="rounded border border-gray-200 bg-white p-5 hover:border-titan-red/25 hover:shadow-md transition-all">
                        <div class="text-[10px] font-black uppercase tracking-[0.24em] text-titan-red mb-2">{{ __('Previous Story') }}</div>
                        <div class="text-base font-black text-titan-navy leading-tight">{{ $prevArticle['title'] }}</div>
                    </a>
                @endif
                @if($nextArticle)
                    <a href="/news/{{ $nextArticle['slug'] }}" class="rounded border border-gray-200 bg-white p-5 hover:border-titan-red/25 hover:shadow-md transition-all md:text-right">
                        <div class="text-[10px] font-black uppercase tracking-[0.24em] text-titan-red mb-2">{{ __('Next Story') }}</div>
                        <div class="text-base font-black text-titan-navy leading-tight">{{ $nextArticle['title'] }}</div>
                    </a>
                @endif
            </div>
        </section>
    </div>

    <style>
        .news-content h2,
        .news-content h3,
        .news-content h4 {
            margin-top: 2rem;
            margin-bottom: 0.85rem;
        }

        .news-content img {
            border: 1px solid #E5E7EB;
            border-radius: 0.25rem;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.05);
            overflow: hidden;
        }

        .news-content iframe,
        .news-content video {
            border: 1px solid #E5E7EB;
            border-radius: 0.25rem;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.05);
            overflow: hidden;
        }

        .news-content p + table,
        .news-content h2 + table,
        .news-content h3 + table,
        .news-content h4 + table {
            margin-top: 1rem;
        }

        .news-content table {
            width: 100%;
            table-layout: auto;
            border-collapse: collapse;
            border: 1px solid #E5E7EB;
            border-radius: 0.5rem;
            background: #FFFFFF;
            margin: 1.25rem 0 1.5rem;
        }

        .news-content thead {
            background: #F9FAFB;
        }

        .news-content th,
        .news-content td {
            border: 1px solid #E5E7EB;
            padding: 0.7rem 0.85rem;
            text-align: left;
            vertical-align: top;
            white-space: normal;
            word-break: break-word;
        }

        .news-content th {
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            font-size: 0.7rem;
            color: #0B2B5C;
        }

        .news-content td {
            font-size: 0.92rem;
            color: #334155;
        }

        .news-content tr:nth-child(even) td {
            background: #FCFCFD;
        }

        .news-content table p {
            margin: 0;
        }
    </style>

</x-layouts.app>
