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
                <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
                    <a href="/projects" class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-[0.16em] text-white/80 transition hover:text-white hover:translate-x-[-2px]">
                        <x-lucide-arrow-left class="h-4 w-4" /> {{ __('All Projects') }}
                    </a>
                    @if(!empty($project['type']))
                        <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-titan-red text-white text-[10px] font-black uppercase tracking-widest shadow-sm">
                            {{ $project['type'] }}
                        </span>
                    @endif
                </div>

                <h1 class="max-w-3xl font-heading font-black !text-white drop-shadow-md !text-xl sm:!text-2xl md:!text-3xl lg:!text-4xl {{ $contentLocale === 'km' ? 'font-khmer leading-snug tracking-normal' : 'leading-tight tracking-tight' }}">
                    {{ $project['title'] }}
                </h1>

                <div class="mt-6 flex flex-wrap items-center gap-x-5 gap-y-2 !text-sm font-semibold text-white/90 {{ $contentLocale === 'km' ? 'tracking-normal' : ' tracking-[0.14em]' }}">
                    @if(!empty($project['status']))
                        <span class="inline-flex items-center gap-1.5">
                            <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                            <span>{{ $project['status'] }}</span>
                        </span>
                    @endif
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

                <div id="project-share" class="mx-auto mt-4 flex max-w-3xl items-center gap-2.5 sm:gap-3.5 border-t border-titan-navy/10 pt-6 md:mt-8 flex-nowrap overflow-x-auto">
                    <p class="shrink-0 !text-[10px] font-black uppercase tracking-[0.18em] text-titan-navy/40">{{ __('Share') }}</p>
                    <x-social-share
                        :url="route('projects.show', ['slug' => $project['slug']])"
                        :title="$project['title']"
                        :description="'Kimmex project: ' . $project['title']"
                    />
                </div>
            </div>
        </section>

        <!-- --- GALLERY SECTION WITH TELEPORTED LIGHTBOX --- -->
        @if(count($project['images']) > 0)
            @php
                $galleryList = array_map(function($img, $idx) use ($project) {
                    $url = is_array($img) ? ($img['url'] ?? '') : (string) $img;
                    $caption = is_array($img) ? ($img['caption'] ?? '') : '';
                    return [
                        'url' => $url,
                        'caption' => $caption ?: ($project['title'] . ' — ' . __('Photo') . ' ' . ($idx + 1)),
                    ];
                }, $project['images'], array_keys($project['images']));
            @endphp

            <div x-data="{
                displayMode: 'bento',
                activeSlide: 0,
                lightboxOpen: false,
                currentLightboxIndex: 0,
                images: {{ Js::from($galleryList) }},
                total: {{ count($project['images']) }},
                openLightbox(index) {
                    this.currentLightboxIndex = index;
                    this.lightboxOpen = true;
                    document.body.style.overflow = 'hidden';
                },
                closeLightbox() {
                    this.lightboxOpen = false;
                    document.body.style.overflow = '';
                },
                prev() {
                    this.currentLightboxIndex = (this.currentLightboxIndex - 1 + this.images.length) % this.images.length;
                },
                next() {
                    this.currentLightboxIndex = (this.currentLightboxIndex + 1) % this.images.length;
                }
            }"
            @keydown.escape.window="closeLightbox()"
            @keydown.arrow-left.window="lightboxOpen && prev()"
            @keydown.arrow-right.window="lightboxOpen && next()"
            >
                <section id="project-gallery" class="bg-slate-950 py-8 md:py-12 text-white border-y border-white/10" style="background-color: #090e1a;">
                    <div class="max-w-7xl mx-auto px-5 sm:px-6">
                        
                        <!-- Header & Display Switcher Controls -->
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6 md:mb-8 pb-5 border-b border-white/10">
                            <div class="flex items-center gap-3">
                                <div class="w-1.5 h-6 rounded-full bg-titan-red shrink-0"></div>
                                <div class="flex flex-wrap items-baseline gap-x-2.5 gap-y-1">
                                    <span class="text-[10px] sm:text-xs font-black uppercase tracking-[0.2em] text-titan-red">
                                        {{ __('Visual Showcase') }}
                                    </span>
                                    <span class="text-white/30 hidden sm:inline">•</span>
                                    <h2 class="font-heading font-black !text-white text-lg sm:text-xl md:text-2xl tracking-tight leading-tight inline-flex items-baseline gap-1.5 {{ $contentLocale === 'km' ? 'font-khmer leading-snug' : '' }}" style="color: #ffffff !important;">
                                        {{ __('Project Gallery') }}
                                        <span class="text-xs sm:text-sm font-semibold !text-white/40 font-sans">({{ count($project['images']) }})</span>
                                    </h2>
                                </div>
                            </div>

                            <!-- Display Mode Tabs Switcher -->
                            <div class="inline-flex items-center gap-1 bg-white/[0.06] p-1 rounded-xl border border-white/10 shrink-0 self-start md:self-auto overflow-x-auto max-w-full" role="tablist">
                                <button type="button" @click="displayMode = 'bento'" role="tab" :aria-selected="displayMode === 'bento'"
                                    :class="displayMode === 'bento' ? 'bg-titan-red text-white shadow-xs' : 'text-white/70 hover:text-white hover:bg-white/5'"
                                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold transition-all duration-200 cursor-pointer shrink-0">
                                    <x-lucide-layout-grid class="w-3.5 h-3.5" />
                                    <span>{{ __('Grid') }}</span>
                                </button>
                                <button type="button" @click="displayMode = 'carousel'" role="tab" :aria-selected="displayMode === 'carousel'"
                                    :class="displayMode === 'carousel' ? 'bg-titan-red text-white shadow-xs' : 'text-white/70 hover:text-white hover:bg-white/5'"
                                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold transition-all duration-200 cursor-pointer shrink-0">
                                    <x-lucide-film class="w-3.5 h-3.5" />
                                    <span>{{ __('Carousel') }}</span>
                                </button>
                                <button type="button" @click="displayMode = 'masonry'" role="tab" :aria-selected="displayMode === 'masonry'"
                                    :class="displayMode === 'masonry' ? 'bg-titan-red text-white shadow-xs' : 'text-white/70 hover:text-white hover:bg-white/5'"
                                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold transition-all duration-200 cursor-pointer shrink-0">
                                    <x-lucide-columns class="w-3.5 h-3.5" />
                                    <span>{{ __('Masonry') }}</span>
                                </button>
                                
                                {{-- Fullscreen Action with Divider --}}
                                <div class="border-l border-white/15 pl-1 ml-0.5 shrink-0">
                                    <button type="button" @click="openLightbox(0)"
                                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-white/90 hover:text-white bg-white/10 hover:bg-white/20 transition-all duration-200 cursor-pointer border border-white/15 shrink-0">
                                        <x-lucide-maximize-2 class="w-3.5 h-3.5 text-titan-red" />
                                        <span>{{ __('Fullscreen') }}</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- DISPLAY MODE 1: Architectural Bento Grid -->
                        <div x-show="displayMode === 'bento'" x-transition:enter="transition ease-out duration-350" x-transition:enter-start="opacity-0 translate-y-3 scale-[0.99]" x-transition:enter-end="opacity-100 translate-y-0 scale-100">
                            <div class="grid grid-cols-1 md:grid-cols-6 gap-4 md:gap-6">
                                @foreach($project['images'] as $i => $img)
                                    @php
                                        $imgUrl = is_array($img) ? $img['url'] : $img;
                                        $imgCaption = is_array($img) ? ($img['caption'] ?? '') : '';
                                        $count = count($project['images']);
                                        $gridClass = "md:col-span-2 aspect-[4/3]";
                                        if ($count === 1) {
                                            $gridClass = "md:col-span-6 aspect-video";
                                        } elseif ($count === 2) {
                                            $gridClass = "md:col-span-3 aspect-[4/3]";
                                        } elseif ($count >= 3) {
                                            if ($i === 0)
                                                $gridClass = "md:col-span-4 md:row-span-2 aspect-square md:aspect-auto h-full min-h-[320px]";
                                            else
                                                $gridClass = "md:col-span-2 aspect-[4/3]";
                                        }
                                    @endphp

                                    @if($i < 3)
                                        <button type="button" @click="openLightbox({{ $i }})"
                                            aria-label="{{ __('Open gallery image :number', ['number' => $i + 1]) }}"
                                            class="project-gallery-card rounded-2xl overflow-hidden group cursor-pointer relative block w-full bg-slate-800 text-left border border-white/10 shadow-xl hover:border-titan-red/40 {{ $gridClass }}">
                                            <img src="{{ $imgUrl }}" alt="Gallery {{ $i + 1 }}"
                                                class="project-gallery-image absolute inset-0 w-full h-full object-cover"
                                                loading="lazy" decoding="async" />

                                            @if($i === 2 && $count > 3)
                                                <div class="absolute inset-0 flex flex-col items-center justify-center bg-slate-950/80 backdrop-blur-xs z-10 transition-all duration-400 group-hover:bg-slate-950/90">
                                                    <div class="w-16 h-16 rounded-full bg-titan-red/20 border border-titan-red/40 flex items-center justify-center mb-3 group-hover:scale-110 group-hover:bg-titan-red/30 transition-transform duration-400 ease-out">
                                                        <x-lucide-images class="w-8 h-8 text-titan-red" />
                                                    </div>
                                                    <span class="text-4xl md:text-5xl font-black text-white tracking-tight group-hover:scale-105 transition-transform duration-400 ease-out">+{{ $count - 3 }}</span>
                                                    <span class="mt-1 text-xs font-bold text-slate-300 uppercase tracking-widest">{{ __('More Photos') }}</span>
                                                </div>
                                            @else
                                                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/85 via-slate-950/20 to-transparent opacity-40 group-hover:opacity-75 transition-opacity duration-400 ease-out"></div>
                                                <div class="absolute bottom-4 left-4 right-4 flex items-center justify-between z-10">
                                                    <span class="text-xs font-semibold text-white/95 truncate max-w-[75%] bg-black/50 backdrop-blur-md px-3.5 py-1.5 rounded-full border border-white/15 shadow-md transform group-hover:translate-y-[-2px] transition-transform duration-400 ease-out">
                                                        {{ $imgCaption ?: __('Photo') . ' ' . ($i + 1) }}
                                                    </span>
                                                    <div class="w-9 h-9 rounded-full bg-white/20 backdrop-blur-md flex items-center justify-center opacity-0 group-hover:opacity-100 transform translate-y-2 group-hover:translate-y-0 scale-90 group-hover:scale-100 transition-all duration-400 ease-out shadow-lg hover:bg-titan-red text-white">
                                                        <x-lucide-zoom-in class="w-4 h-4 text-white" />
                                                    </div>
                                                </div>
                                            @endif
                                        </button>
                                    @endif
                                @endforeach
                            </div>
                        </div>

                        <!-- DISPLAY MODE 2: Showcase Carousel & Filmstrip Slider -->
                        <div x-show="displayMode === 'carousel'" x-cloak x-transition:enter="transition ease-out duration-350" x-transition:enter-start="opacity-0 translate-y-3 scale-[0.99]" x-transition:enter-end="opacity-100 translate-y-0 scale-100">
                            <div class="space-y-6">
                                <!-- Main Showcase Stage -->
                                <div class="relative aspect-video max-h-[560px] w-full rounded-2xl overflow-hidden bg-slate-950 border border-white/10 shadow-2xl group">
                                    @foreach($project['images'] as $i => $img)
                                        @php
                                            $imgUrl = is_array($img) ? $img['url'] : $img;
                                            $imgCaption = is_array($img) ? ($img['caption'] ?? '') : '';
                                        @endphp
                                        <div x-show="activeSlide === {{ $i }}" x-transition:enter="transition ease-out duration-400" x-transition:enter-start="opacity-0 scale-105" x-transition:enter-end="opacity-100 scale-100" class="absolute inset-0">
                                            <img src="{{ $imgUrl }}" alt="{{ $project['title'] }} {{ $i + 1 }}" class="w-full h-full object-cover" loading="lazy" decoding="async" />
                                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/20 to-transparent"></div>
                                            <div class="absolute bottom-6 left-6 right-20 flex flex-col items-start gap-2 z-10">
                                                <span class="px-3 py-1 rounded-full bg-titan-red text-white text-[10px] font-black uppercase tracking-widest shadow-md">
                                                    {{ $i + 1 }} / {{ count($project['images']) }}
                                                </span>
                                                <p class="text-base md:text-lg font-bold text-white max-w-2xl drop-shadow-md">
                                                    {{ $imgCaption ?: $project['title'] }}
                                                </p>
                                            </div>
                                            <button type="button" @click="openLightbox({{ $i }})" class="absolute top-6 right-6 z-20 w-11 h-11 rounded-full bg-white/20 backdrop-blur-md hover:bg-titan-red transition-all duration-300 ease-out hover:scale-110 active:scale-95 flex items-center justify-center text-white cursor-pointer shadow-lg border border-white/20">
                                                <x-lucide-maximize-2 class="w-5 h-5" />
                                            </button>
                                        </div>
                                    @endforeach

                                    <!-- Carousel Nav Arrows -->
                                    <button type="button" @click="activeSlide = (activeSlide - 1 + total) % total" class="absolute left-4 top-1/2 -translate-y-1/2 z-20 w-12 h-12 rounded-full bg-slate-900/70 backdrop-blur-md border border-white/10 hover:bg-titan-red hover:border-titan-red/40 text-white transition-all duration-300 ease-out hover:scale-110 active:scale-95 flex items-center justify-center cursor-pointer shadow-xl">
                                        <x-lucide-chevron-left class="w-6 h-6" />
                                    </button>
                                    <button type="button" @click="activeSlide = (activeSlide + 1) % total" class="absolute right-4 top-1/2 -translate-y-1/2 z-20 w-12 h-12 rounded-full bg-slate-900/70 backdrop-blur-md border border-white/10 hover:bg-titan-red hover:border-titan-red/40 text-white transition-all duration-300 ease-out hover:scale-110 active:scale-95 flex items-center justify-center cursor-pointer shadow-xl">
                                        <x-lucide-chevron-right class="w-6 h-6" />
                                    </button>
                                </div>

                                <!-- Filmstrip Thumbnail Track -->
                                <div class="flex items-center gap-3 overflow-x-auto pb-2 scrollbar-thin">
                                    @foreach($project['images'] as $i => $img)
                                        @php
                                            $imgUrl = is_array($img) ? $img['url'] : $img;
                                        @endphp
                                        <button type="button" @click="activeSlide = {{ $i }}"
                                            :class="activeSlide === {{ $i }} ? 'ring-2 ring-titan-red scale-105 opacity-100 shadow-lg' : 'opacity-60 hover:opacity-100 hover:scale-102'"
                                            class="shrink-0 aspect-[4/3] w-24 md:w-32 rounded-lg overflow-hidden bg-slate-800 border border-white/10 transition-all duration-300 ease-out cursor-pointer relative active:scale-95">
                                            <img src="{{ $imgUrl }}" alt="Thumb {{ $i + 1 }}" class="w-full h-full object-cover" loading="lazy" decoding="async" />
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- DISPLAY MODE 3: Masonry Mosaic Grid -->
                        <div x-show="displayMode === 'masonry'" x-cloak x-transition:enter="transition ease-out duration-350" x-transition:enter-start="opacity-0 translate-y-3 scale-[0.99]" x-transition:enter-end="opacity-100 translate-y-0 scale-100">
                            <div class="columns-1 sm:columns-2 lg:columns-3 gap-5 space-y-5">
                                @foreach($project['images'] as $i => $img)
                                    @php
                                        $imgUrl = is_array($img) ? $img['url'] : $img;
                                        $imgCaption = is_array($img) ? ($img['caption'] ?? '') : '';
                                    @endphp
                                    <button type="button" @click="openLightbox({{ $i }})"
                                        class="project-gallery-card break-inside-avoid group rounded-2xl overflow-hidden bg-slate-800 border border-white/10 text-left cursor-pointer relative block w-full shadow-xl hover:border-titan-red/40">
                                        <div class="relative overflow-hidden aspect-[4/3]">
                                            <img src="{{ $imgUrl }}" alt="Photo {{ $i + 1 }}" class="project-gallery-image absolute inset-0 w-full h-full object-cover" loading="lazy" decoding="async" />
                                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/85 via-slate-950/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-400 ease-out flex items-end p-4">
                                                <span class="text-xs font-semibold text-white flex items-center gap-2 transform translate-y-2 group-hover:translate-y-0 transition-transform duration-400 ease-out">
                                                    <x-lucide-zoom-in class="w-4 h-4 text-titan-red shrink-0" />
                                                    <span>{{ __('Expand Image Fullscreen') }}</span>
                                                </span>
                                            </div>
                                            <span class="absolute top-3 right-3 bg-slate-950/85 backdrop-blur-md text-white text-[10px] font-bold px-2.5 py-1 rounded-full border border-white/15 shadow-md">
                                                {{ $i + 1 }} / {{ count($project['images']) }}
                                            </span>
                                        </div>
                                        @if($imgCaption)
                                            <div class="p-3.5 bg-slate-900/90 border-t border-white/10">
                                                <p class="text-xs font-medium text-slate-300 line-clamp-2">{{ $imgCaption }}</p>
                                            </div>
                                        @endif
                                    </button>
                                @endforeach
                            </div>
                        </div>

                    </div>
                </section>

                <!-- Fullscreen Lightbox Modal -->
                <div
                    x-cloak
                    x-show="lightboxOpen"
                    x-transition:enter="transition ease-out duration-250"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 z-[99999] flex flex-col justify-between bg-slate-950/98 backdrop-blur-xl text-white p-4 md:p-6 select-none"
                    @click.self="closeLightbox()"
                    style="display: none;"
                >
                    <!-- Top Toolbar -->
                    <div class="flex items-center justify-between z-20 w-full max-w-7xl mx-auto">
                        <div class="flex items-center gap-3">
                            <span class="px-3.5 py-1 rounded-full bg-titan-red text-white text-xs font-black uppercase tracking-wider shadow-md">
                                <span x-text="currentLightboxIndex + 1"></span> / <span x-text="images.length"></span>
                            </span>
                            <span class="text-sm font-bold text-slate-300 truncate max-w-md hidden sm:inline drop-shadow">
                                {{ $project['title'] }}
                            </span>
                        </div>
                        
                        <div class="flex items-center gap-2">
                            <a :href="images[currentLightboxIndex]?.url" target="_blank" class="w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition cursor-pointer" title="{{ __('Open full resolution') }}">
                                <x-lucide-external-link class="w-4 h-4" />
                            </a>
                            <button type="button" @click="closeLightbox()" class="w-10 h-10 rounded-full bg-white/10 hover:bg-titan-red text-white flex items-center justify-center transition cursor-pointer" title="{{ __('Close') }}">
                                <x-lucide-x class="w-5 h-5" />
                            </button>
                        </div>
                    </div>

                    <!-- Main Image Stage with Navigation -->
                    <div class="relative flex-1 flex items-center justify-center my-2 overflow-hidden w-full" @click.self="closeLightbox()">
                        <!-- Prev Button -->
                        <button type="button" @click="prev()" x-show="images.length > 1" class="absolute left-2 md:left-6 z-20 w-12 h-12 md:w-14 md:h-14 rounded-full bg-slate-900/80 hover:bg-titan-red text-white border border-white/10 flex items-center justify-center transition shadow-2xl cursor-pointer">
                            <x-lucide-chevron-left class="w-6 h-6 md:w-7 md:h-7" />
                        </button>

                        <!-- Image with 100% aspect-ratio contain (NEVER STRETCHES) -->
                        <div class="flex items-center justify-center max-h-[74vh] max-w-[90vw]">
                            <img
                                :src="images[currentLightboxIndex]?.url"
                                :alt="images[currentLightboxIndex]?.caption"
                                class="max-h-[74vh] max-w-[90vw] w-auto h-auto object-contain rounded-2xl shadow-2xl transition-all duration-200"
                                draggable="false"
                            />
                        </div>

                        <!-- Next Button -->
                        <button type="button" @click="next()" x-show="images.length > 1" class="absolute right-2 md:right-6 z-20 w-12 h-12 md:w-14 md:h-14 rounded-full bg-slate-900/80 hover:bg-titan-red text-white border border-white/10 flex items-center justify-center transition shadow-2xl cursor-pointer">
                            <x-lucide-chevron-right class="w-6 h-6 md:w-7 md:h-7" />
                        </button>
                    </div>

                    <!-- Bottom Bar: Caption & Thumbnails -->
                    <div class="flex flex-col items-center gap-3 z-20 w-full max-w-4xl mx-auto">
                        <!-- Caption -->
                        <div x-show="images[currentLightboxIndex]?.caption" class="px-5 py-2 rounded-full bg-slate-900/90 border border-white/15 text-xs md:text-sm font-semibold text-slate-200 text-center max-w-2xl truncate shadow-xl">
                            <span x-text="images[currentLightboxIndex]?.caption"></span>
                        </div>

                        <!-- Thumbnails Track -->
                        <div class="flex items-center gap-2 overflow-x-auto max-w-full pb-1 scrollbar-none" x-show="images.length > 1">
                            <template x-for="(item, idx) in images" :key="idx">
                                <button type="button" @click="currentLightboxIndex = idx"
                                    :class="currentLightboxIndex === idx ? 'ring-2 ring-titan-red scale-105 opacity-100 shadow-lg' : 'opacity-45 hover:opacity-90'"
                                    class="shrink-0 w-14 h-10 md:w-16 md:h-12 rounded-lg overflow-hidden border border-white/10 transition-all duration-200 cursor-pointer bg-slate-800">
                                    <img :src="item.url" :alt="'Thumb ' + (idx + 1)" class="w-full h-full object-cover" loading="lazy" decoding="async" />
                                </button>
                            </template>
                        </div>
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
                            <h3 class="projects-title text-sm font-bold text-titan-navy group-hover:text-titan-red transition-colors tracking-tight leading-snug">
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

        .project-gallery-card {
            transform: translate3d(0, 0, 0);
            transition: transform 0.5s cubic-bezier(0.16, 1, 0.3, 1), 
                        box-shadow 0.5s cubic-bezier(0.16, 1, 0.3, 1), 
                        border-color 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            will-change: transform, box-shadow;
            backface-visibility: hidden;
        }

        .project-gallery-card:hover {
            transform: translate3d(0, -6px, 0);
            box-shadow: 0 24px 48px -12px rgba(0, 0, 0, 0.75), 0 0 0 1px rgba(227, 30, 36, 0.35);
        }

        .project-gallery-card:focus-visible {
            outline: 3px solid var(--primary-color, #E31E24);
            outline-offset: 4px;
        }

        .project-gallery-image {
            transform: scale(1) translate3d(0, 0, 0);
            transition: transform 0.75s cubic-bezier(0.16, 1, 0.3, 1), 
                        filter 0.5s cubic-bezier(0.16, 1, 0.3, 1);
            will-change: transform;
            backface-visibility: hidden;
        }

        .project-gallery-card:hover .project-gallery-image {
            transform: scale(1.06) translate3d(0, 0, 0);
        }

        .project-gallery-overlay {
            transition: background-color 0.4s ease, opacity 0.4s ease;
        }

        .project-gallery-card:hover .project-gallery-overlay {
            background-color: rgb(0 0 0 / 0.08);
        }

        .project-gallery-more {
            transition: background-color 0.4s ease, transform 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .project-gallery-card:hover .project-gallery-more {
            background-color: rgb(11 43 92 / 0.88);
        }

        @media (prefers-reduced-motion: reduce) {
            .project-gallery-card,
            .project-gallery-image,
            .project-gallery-overlay,
            .project-gallery-more {
                transition: none !important;
            }

            .project-gallery-card:hover {
                transform: none !important;
            }

            .project-gallery-card:hover .project-gallery-image {
                transform: none !important;
            }
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
