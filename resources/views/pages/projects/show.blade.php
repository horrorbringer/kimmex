@php
    /** @var array $project Passed from ProjectController */
    /** @var string $contentLocale Passed from ProjectController */
    /** @var string $defaultProjectImage Passed from ProjectController */

    $normalizeDesignConcept = function (?string $content) use ($contentLocale): string {
        $content = trim((string) $content);

        if ($contentLocale === 'km') {
            $content = preg_replace('#</h>\s*Mail\s*មុខងារ</h4>#iu', '<h4>មុខងារសំខាន់ៗ</h4>', $content) ?? $content;
            $content = preg_replace('#</h>\s*Main Functions</h4>#iu', '<h4>មុខងារសំខាន់ៗ</h4>', $content) ?? $content;
        }

        return $content;
    };

    // Use the $project passed from ProjectController
    $renderProjectContent = fn (?string $content, string $mode = 'auto') => \App\Support\RichContent::renderProject($content, $mode);

    $hasProjectContent = function (?string $content): bool {
        $content = trim((string) $content);

        if ($content === '') {
            return false;
        }

        $plainText = trim(str_replace("\xc2\xa0", ' ', html_entity_decode(strip_tags($content), ENT_QUOTES | ENT_HTML5, 'UTF-8')));

        return $plainText !== '';
    };

    $overviewSections = [
        [
            'title' => __('Description'),
            'content' => $project['narrative']['description'] ?? '',
            'mode' => 'auto',
            'class' => '',
        ],
        [
            'title' => __('The Background'),
            'content' => $project['narrative']['background'] ?? '',
            'mode' => 'auto',
            'class' => '',
        ],
        [
            'title' => __('Objectives'),
            'content' => $project['narrative']['objectives'] ?? '',
            'mode' => 'list',
            'class' => 'mt-8',
        ],
        [
            'title' => __('Design Concept'),
            'content' => $normalizeDesignConcept($project['narrative']['design_concept'] ?? ''),
            'mode' => 'auto',
            'class' => 'mt-8',
            'rich_class' => 'project-rich-content',
        ],
    ];

    $overviewSections = array_values(array_filter($overviewSections, fn (array $section): bool => $hasProjectContent($section['content'] ?? '')));
    $engineeringNarrative = $project['narrative']['engineering_narrative'] ?? '';
    $hasEngineeringNarrative = $hasProjectContent($engineeringNarrative);

    $storySections = $overviewSections;

    if ($hasProjectContent($project['scope'] ?? '')) {
        $storySections[] = [
            'title' => __('Scope of Work'),
            'content' => $project['scope'],
            'mode' => 'list',
            'scope' => true,
        ];
    }

    if ($hasEngineeringNarrative) {
        $storySections[] = [
            'title' => __('Engineering Challenges & Solutions'),
            'content' => $engineeringNarrative,
            'mode' => 'auto',
            'engineering' => true,
        ];
    }
@endphp

<x-layouts.app :title="$project['title'] . ' | Portfolio'" :description="'Kimmex project showcase: ' . $project['title']" :image="$project['heroImage']" :image-alt="$project['title']" :canonical="route('projects.show', ['slug' => $project['slug']])">
    @push('head')
        <script type="application/ld+json">
            {!! json_encode([
                '@context' => 'https://schema.org',
                '@type' => 'BreadcrumbList',
                'itemListElement' => [
                    ['@type' => 'ListItem', 'position' => 1, 'name' => __('Home'), 'item' => route('home')],
                    ['@type' => 'ListItem', 'position' => 2, 'name' => __('Projects'), 'item' => route('projects.index')],
                    ['@type' => 'ListItem', 'position' => 3, 'name' => $project['title'], 'item' => route('projects.show', ['slug' => $project['slug']])],
                ],
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
        </script>
    @endpush

    <div class="min-h-screen bg-white pt-28 font-sans text-titan-navy antialiased">

        <!-- --- PREMIUM NARRATIVE HERO --- -->
        <header class="relative flex min-h-[340px] w-full items-end overflow-hidden bg-titan-navy md:min-h-[420px]">
            <img src="{{ $project['heroImage'] }}" alt="{{ $project['title'] }}"
                class="absolute inset-0 w-full h-full object-cover opacity-100" loading="eager" decoding="async" fetchpriority="high"
                onerror="this.onerror=null; this.dataset.imageError='false'; this.src='{{ $defaultProjectImage }}';" />
            
            {{-- Deep multi-stage gradient for maximum text contrast --}}
            <div class="absolute inset-0 bg-gradient-to-b from-titan-navy/35 via-titan-navy/5 to-titan-navy/65"></div>
            <div class="absolute inset-0 bg-black/10"></div>

            <div class="relative z-10 mx-auto w-full max-w-[1180px] px-5 pb-10 md:px-6 md:pb-14">
                <a href="/projects" class="mb-7 inline-flex items-center gap-2 text-xs font-bold uppercase tracking-[0.16em] text-white/75 transition hover:text-white">
                    <x-lucide-arrow-left class="h-4 w-4" /> {{ __('All Projects') }}
                </a>
                <h1 class="max-w-4xl font-black text-white drop-shadow-2xl {{ $contentLocale === 'km' ? 'tracking-normal leading-[1.28]' : 'uppercase tracking-tighter leading-[0.95]' }}"
                    style="font-size: {{ $contentLocale === 'km' ? 'clamp(1.75rem, 3.6vw, 3.25rem)' : 'clamp(1.75rem, 5vw, 3.5rem)' }} !important; color: white !important;">
                    {{ $project['title'] }}
                </h1>
                <div class="mt-6 flex flex-wrap items-center gap-x-5 gap-y-2 text-sm font-semibold text-white/85 {{ $contentLocale === 'km' ? 'tracking-normal' : 'uppercase tracking-[0.14em]' }}">
                    <div class="flex items-center gap-2">
                        <x-lucide-map-pin class="h-4 w-4 shrink-0 text-titan-red" />
                        {{ $project['location'] }}
                    </div>
                    <span class="h-1 w-1 rounded-full bg-titan-red"></span>
                    <span>{{ $project['status'] }}</span>
                </div>
            </div>

        </header>

        <!-- --- PROJECT AT A GLANCE & CASE STUDY --- -->
        <section class="px-5 py-12 md:px-6 md:py-20">
            <div class="mx-auto max-w-[1180px]">
                <div class="border-y border-titan-navy/10 py-5">
                    <div class="grid grid-cols-2 divide-x divide-y divide-titan-navy/10 md:grid-cols-4 md:divide-y-0">
                        @foreach([
                            ['label' => __('Client'), 'value' => $project['client'], 'icon' => 'lucide-user'],
                            ['label' => __('Location'), 'value' => $project['location'], 'icon' => 'lucide-map-pin'],
                            ['label' => __('Timeline'), 'value' => $project['year'], 'icon' => 'lucide-calendar'],
                            ['label' => __('Built Area'), 'value' => $project['built_area'] ?: __('Contact for Details'), 'icon' => 'lucide-maximize'],
                        ] as $fact)
                            <div class="min-w-0 px-4 py-3 md:px-6">
                                <p class="text-[10px] font-black uppercase tracking-[0.13em] text-titan-navy/40">{{ $fact['label'] }}</p>
                                <p class="mt-1.5 text-sm font-bold leading-snug text-titan-navy md:text-base">{{ $fact['value'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                @if($storySections !== [])
                    <div class="mx-auto mt-14 max-w-3xl md:mt-20">
                        <div class="mb-12">
                            <p class="text-xs font-black uppercase tracking-[0.2em] text-titan-red">{{ __('Project Story') }}</p>
                            <h2 class="mt-3 text-3xl font-black tracking-tight text-titan-navy md:text-4xl">{{ __('From concept to delivery') }}</h2>
                        </div>
                        @foreach($storySections as $section)
                            <article class="border-t border-titan-navy/10 py-9 first:border-t-0 first:pt-0 md:py-12">
                                <h3 class="mb-5 text-xl font-black text-titan-navy md:text-2xl">{{ $section['title'] }}</h3>
                                @if($section['scope'] ?? false)
                                    <div class="project-scope-content prose max-w-none text-titan-navy/70 project-khmer-content">
                                        <p class="project-scope-intro">{{ __('Key responsibilities delivered by Kimmex.') }}</p>
                                        {!! $renderProjectContent($section['content'], $section['mode']) !!}
                                    </div>
                                @else
                                    <div class="project-rich-content prose prose-base max-w-none text-titan-navy/70 md:prose-lg project-khmer-content">
                                        {!! $renderProjectContent($section['content'], $section['mode']) !!}
                                    </div>
                                @endif
                            </article>
                        @endforeach
                    </div>
                @endif

                <div id="project-share" class="mx-auto mt-4 flex max-w-3xl items-center gap-5 border-t border-titan-navy/10 pt-7 md:mt-8">
                    <p class="shrink-0 text-[10px] font-black uppercase tracking-[0.18em] text-titan-navy/40">{{ __('Share') }}</p>
                    <x-social-share
                        :url="route('projects.show', ['slug' => $project['slug']])"
                        :title="$project['title']"
                        :description="'Kimmex project: ' . $project['title']"
                    />
                </div>
            </div>
        </section>

        <!-- --- GALLERY SECTION --- -->
        @if(count($project['images']) > 0)
            <div x-data="{ lightboxOpen: false, lightboxIndex: 0, images: {{ Js::from($project['images']) }} }">
            <section id="project-gallery" class="bg-slate-50 py-10 md:py-14 px-4 md:px-6 text-titan-navy border-y border-titan-navy/10">
                <div class="max-w-[1400px] mx-auto">
                    <h2 class="text-xl md:text-2xl font-black mb-6 md:mb-8 border-l-4 border-titan-red pl-4 md:pl-6">{{ __('Project Gallery') }}</h2>
                    <div class="grid grid-cols-1 md:grid-cols-6 gap-4 md:gap-6">
                        @foreach($project['images'] as $i => $img)
                            @if($i < 3)
                                @php
                                    $gridClass = "md:col-span-2 aspect-[4/3]";
                                    $count = count($project['images']);
                                    if ($count === 1) {
                                        $gridClass = "md:col-span-6 aspect-video";
                                    } elseif ($count === 2) {
                                        $gridClass = "md:col-span-3 aspect-[4/3]";
                                    } elseif ($count >= 3) {
                                        if ($i === 0)
                                            $gridClass = "md:col-span-4 md:row-span-2 aspect-square md:aspect-auto h-full";
                                        else
                                            $gridClass = "md:col-span-2 aspect-[4/3]";
                                    }
                                @endphp

                                <div @click="lightboxIndex = {{ $i }}; lightboxOpen = true"
                                    class="rounded-lg overflow-hidden group cursor-pointer relative w-full h-full bg-white shadow-[0_16px_44px_rgba(11,43,92,0.10)] {{ $gridClass }}">
                                    <img src="{{ $img }}" alt="Gallery {{ $i + 1 }}"
                                        class="absolute inset-0 w-full h-full object-cover {{ !($i === 2 && $count > 3) ? 'group-hover:scale-110' : '' }} transition-transform duration-700"
                                        loading="lazy" decoding="async" />

                                    @if($i === 2 && $count > 3)
                                        <div
                                            class="absolute inset-0 bg-titan-navy/70 hover:bg-titan-navy/80 transition-colors duration-500 flex flex-col items-center justify-center z-10">
                                            <span class="text-4xl md:text-5xl font-black text-white mb-2">+{{ $count - 3 }}</span>
                                            <span
                                                class="text-xs font-bold text-titan-red uppercase tracking-widest">{{ __('More Gallery') }}</span>
                                        </div>
                                    @else
                                        <div
                                            class="absolute inset-0 bg-black/20 group-hover:bg-transparent transition-colors duration-500">
                                        </div>
                                        <div
                                            class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                            <div class="bg-white/20 backdrop-blur-md p-4 rounded-full">
                                                <x-lucide-maximize class="w-6 h-6 text-white" />
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </section>

            <!-- LIGHTBOX — outside section to avoid overflow/transform issues -->
            <div x-show="lightboxOpen"
                x-effect="document.body.style.overflow = lightboxOpen ? 'hidden' : ''"
                @keydown.escape.window="lightboxOpen = false"
                @keydown.arrow-right.window="if(lightboxOpen) { lightboxIndex = (lightboxIndex + 1) % images.length }"
                @keydown.arrow-left.window="if(lightboxOpen) { lightboxIndex = (lightboxIndex - 1 + images.length) % images.length }"
                class="fixed inset-0 z-[9999] bg-black/95 flex items-center justify-center"
                x-cloak>

                <!-- Close -->
                <button @click="lightboxOpen = false" type="button"
                    class="absolute top-4 right-4 z-50 w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition-colors cursor-pointer">
                    <x-lucide-x class="w-5 h-5 text-white" />
                </button>

                <!-- Prev -->
                <button @click="lightboxIndex = (lightboxIndex - 1 + images.length) % images.length" type="button"
                    class="absolute left-4 top-1/2 -translate-y-1/2 z-50 w-11 h-11 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition-colors cursor-pointer">
                    <x-lucide-chevron-left class="w-5 h-5 text-white" />
                </button>

                <!-- Next -->
                <button @click="lightboxIndex = (lightboxIndex + 1) % images.length" type="button"
                    class="absolute right-4 top-1/2 -translate-y-1/2 z-50 w-11 h-11 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition-colors cursor-pointer">
                    <x-lucide-chevron-right class="w-5 h-5 text-white" />
                </button>

                <!-- Image -->
                <img :src="images[lightboxIndex]"
                    class="max-w-[90vw] max-h-[85vh] object-contain rounded shadow-2xl select-none" decoding="async" />

                <!-- Counter -->
                <div class="absolute bottom-4 left-1/2 -translate-x-1/2 bg-white/10 backdrop-blur-sm px-4 py-2 rounded-full">
                    <span class="text-white/80 text-xs font-bold">
                        <span x-text="lightboxIndex + 1"></span> / <span x-text="images.length"></span>
                    </span>
                </div>
            </div>
            </div>
        @endif

        <!-- --- RELATED PROJECTS --- -->
        @if(count($project['related']) > 0)
            <section class="py-10 md:py-14 px-4 md:px-6 max-w-[1400px] mx-auto">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 mb-6 md:mb-8">
                    <h2 class="text-xl md:text-2xl font-black text-titan-navy">{{ __('Similar Projects') }}</h2>
                    <a href="/projects"
                        class="font-bold text-titan-red hover:underline flex items-center gap-2 text-xs md:text-sm uppercase tracking-widest">
                        {{ __('View All') }} <x-lucide-arrow-right class="w-4 h-4" />
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6">
                    @foreach($project['related'] as $p)
                        <a href="/projects/{{ $p['id'] }}" class="block group">
                            <div class="aspect-[16/10] rounded-lg overflow-hidden mb-3 relative shadow-sm group-hover:shadow-lg transition-all duration-300">
                                <img src="{{ $p['image'] }}" alt="{{ $p['title'] }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy" decoding="async" />
                                <div class="absolute top-3 left-3 bg-titan-navy/90 backdrop-blur-sm text-white text-[9px] font-black uppercase tracking-[0.15em] px-2.5 py-1 rounded">
                                    {{ $p['type'] }}
                                </div>
                            </div>
                            <h3 class="projects-title text-sm font-black text-titan-navy group-hover:text-titan-red transition-colors uppercase tracking-tight leading-tight">
                                {{ $p['title'] }}
                            </h3>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- In the News --}}
        @if($project['newsArticles'] ?? false)
            <section class="py-10 md:py-14 px-4 md:px-6 max-w-[1400px] mx-auto border-t border-gray-200">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 mb-6 md:mb-8">
                    <h2 class="text-xl md:text-2xl font-black text-titan-navy">{{ __('In the News') }}</h2>
                    <a href="/news"
                        class="font-bold text-titan-red hover:underline flex items-center gap-2 text-xs md:text-sm uppercase tracking-widest">
                        {{ __('All News') }} <x-lucide-arrow-right class="w-4 h-4" />
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6">
                    @foreach($project['newsArticles'] as $newsItem)
                        <a href="/news/{{ $newsItem['slug'] }}" class="group rounded border border-gray-200 bg-white overflow-hidden hover:border-titan-red/25 hover:shadow-md transition-all">
                            <div class="aspect-[16/10] overflow-hidden bg-titan-navy/5">
                                @if($newsItem['coverImage'])
                                    <img src="{{ $newsItem['coverImage'] }}" alt="{{ $newsItem['title'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy" decoding="async" />
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <x-lucide-newspaper class="w-10 h-10 text-titan-navy/10" />
                                    </div>
                                @endif
                            </div>
                            <div class="p-4">
                                @if($newsItem['category'])
                                    <div class="text-[9px] font-black uppercase tracking-[0.18em] text-titan-red mb-1">{{ $newsItem['category'] }}</div>
                                @endif
                                <div class="text-sm font-black text-titan-navy group-hover:text-titan-red transition-colors leading-tight line-clamp-2">
                                    {{ $newsItem['title'] }}
                                </div>
                                @if($newsItem['publishedAt'])
                                    <div class="mt-2 text-[10px] text-titan-navy/40 font-medium">{{ $newsItem['publishedAt'] }}</div>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

    <!-- REFINED STYLES -->
    <style>
        @keyframes revealUp {
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

        .project-rich-content :where(ul, ol) {
            margin: 0.75rem 0;
            padding-left: 1.4rem;
        }

        .project-rich-content ul {
            list-style: disc;
        }

        .project-rich-content ol {
            list-style: decimal;
        }

        .project-rich-content li {
            margin: 0.3rem 0;
        }

        .project-rich-content h4 {
            margin-top: 1rem;
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
            font-weight: 800;
            color: rgb(17 24 39);
        }

        .project-rich-content p {
            margin-bottom: 0.75rem;
        }

        .project-scope-content :where(ul, ol) {
            counter-reset: scope-item;
            display: grid;
            grid-template-columns: repeat(1, minmax(0, 1fr));
            gap: 0;
            margin: 1.75rem 0 0;
            padding: 0;
            list-style: none;
        }

        .project-scope-intro {
            max-width: 38rem;
            margin: 0;
            color: rgb(11 43 92 / 0.58);
            font-size: 0.95rem;
            line-height: 1.75;
        }

        .project-scope-content li {
            position: relative;
            display: flex;
            min-height: 5.75rem;
            gap: 1rem;
            align-items: flex-start;
            padding: 1.15rem 1rem 1.15rem 3.75rem;
            border-top: 1px solid rgb(11 43 92 / 0.1);
            background: transparent;
            color: rgb(11 43 92);
            font-weight: 700;
            line-height: 1.7;
            transition: background-color 180ms ease, color 180ms ease;
        }

        .project-scope-content li::before {
            position: absolute;
            top: 1.25rem;
            left: 0;
            counter-increment: scope-item;
            content: counter(scope-item, decimal-leading-zero);
            color: var(--primary-color, #E31E24);
            font-size: 0.75rem;
            font-weight: 900;
            letter-spacing: 0.08em;
        }

        .project-scope-content li:hover {
            background: rgb(11 43 92 / 0.025);
        }

        @media (min-width: 768px) {
            .project-scope-content :where(ul, ol) {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .project-scope-content li:nth-child(-n + 2) {
                border-top: 1px solid rgb(11 43 92 / 0.1);
            }

            .project-scope-content li:nth-child(odd) {
                border-right: 1px solid rgb(11 43 92 / 0.1);
                padding-right: 1.75rem;
            }

            .project-scope-content li:nth-child(even) {
                padding-left: 4.5rem;
            }

            .project-scope-content li:nth-child(even)::before {
                left: 1rem;
            }
        }

        .project-khmer-content {
            word-break: normal !important;
            overflow-wrap: break-word;
            line-break: strict;
        }

        .project-khmer-content :where(p, li) {
            line-height: 2.0;
        }

        @supports (view-timeline-name: --revealing) {
            .reveal-up {
                animation: revealUp both;
                animation-timeline: view();
                animation-range: entry 5% cover 25%;
            }
        }
    </style>

</x-layouts.app>
