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
@endphp

<x-layouts.app :title="$project['title'] . ' | Portfolio'" :description="'Kimmex project showcase: ' . $project['title']">
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

    <div class="bg-white min-h-screen text-titan-navy font-sans antialiased pt-28" x-data="{ 
        scrolled: false, 
        progress: 0,
        scrollY: 0,
        ticking: false
    }" x-init="
        window.addEventListener('scroll', () => {
            if (!ticking) {
                window.requestAnimationFrame(() => {
                    scrollY = window.scrollY;
                    scrolled = window.scrollY > 400;
                    const scrollTotal = document.documentElement.scrollHeight - window.innerHeight;
                    progress = scrollTotal > 0 ? (window.scrollY / scrollTotal) * 100 : 0;
                    ticking = false;
                });
                ticking = true;
            }
        }, { passive: true });
    ">

        <!-- READING PROGRESS & STICKY NAV -->
        <div class="sticky top-20 z-[80] bg-white/95 backdrop-blur border-b border-gray-200">
            <div class="h-1 bg-gray-100 w-full relative">
                <div class="h-full bg-titan-red absolute left-0 top-0 transition-all duration-150"
                    :style="'width: ' + progress + '%'"></div>
            </div>
            <div class="max-w-[1400px] mx-auto px-6 h-10 md:h-11 flex items-center gap-3">
                    <a href="/projects" class="w-7 h-7 rounded border border-gray-200 bg-white text-titan-navy flex items-center justify-center hover:border-titan-red/30 hover:text-titan-red transition-colors shrink-0">
                        <x-lucide-arrow-left class="w-4 h-4" />
                    </a>
                    <div class="min-w-0">
                        <div class="text-[8px] font-black uppercase tracking-[0.24em] text-titan-red leading-none">{{ __('Project:') }}</div>
                        <div class="text-[10px] font-black uppercase tracking-tight text-titan-navy truncate max-w-[180px] md:max-w-[360px] leading-tight">{{ $project['title'] }}</div>
                    </div>
            </div>
        </div>

        <!-- --- PREMIUM NARRATIVE HERO --- -->
        <header class="relative w-full h-[50vh] md:h-[55vh] min-h-[360px] md:min-h-[420px] overflow-hidden bg-titan-navy flex items-center justify-center">
            <img src="{{ $project['heroImage'] }}" alt="{{ $project['title'] }}"
                class="absolute inset-0 w-full h-full object-cover opacity-100" loading="eager" decoding="async" fetchpriority="high"
                onerror="this.onerror=null; this.dataset.imageError='false'; this.src='{{ $defaultProjectImage }}';" />
            
            {{-- Deep multi-stage gradient for maximum text contrast --}}
            <div class="absolute inset-0 bg-gradient-to-b from-titan-navy/35 via-titan-navy/5 to-titan-navy/65"></div>
            <div class="absolute inset-0 bg-black/10"></div>

            <div class="relative z-10 text-center max-w-6xl px-4 md:px-6" x-data="{ shown: false }"
                x-init="setTimeout(() => shown = true, 100)">

                
                <h1 :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10'"
                    class="transition-all duration-1000 delay-300 font-black text-white mb-6 md:mb-8 mx-auto max-w-5xl drop-shadow-2xl {{ $contentLocale === 'km' ? 'tracking-normal leading-[1.28]' : 'uppercase tracking-tighter leading-[0.9]' }}"
                    style="font-size: {{ $contentLocale === 'km' ? 'clamp(1.75rem, 3.6vw, 3.25rem)' : 'clamp(1.75rem, 5vw, 3.5rem)' }} !important; color: white !important;">
                    {{ $project['title'] }}
                </h1>
                
                <div :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-6'"
                    class="transition-all duration-700 delay-500 flex flex-wrap items-center justify-center gap-3 md:gap-6 text-white font-bold {{ $contentLocale === 'km' ? 'tracking-normal text-xs md:text-base' : 'uppercase tracking-[0.3em] md:tracking-[0.4em] text-[10px] md:text-sm' }}">
                    <div class="h-[1px] w-8 md:w-12 bg-titan-red hidden sm:block"></div>
                    <div class="flex items-center justify-center gap-2 md:gap-3 max-w-3xl leading-relaxed">
                        <x-lucide-map-pin class="w-3.5 h-3.5 md:w-4 md:h-4 text-titan-red shrink-0" />
                        {{ $project['location'] }}
                    </div>
                    <x-page-view-count light />
                    <div class="h-[1px] w-8 md:w-12 bg-titan-red hidden sm:block"></div>
                </div>
            </div>

        </header>

        {{-- Social Share Buttons --}}
        <div style="max-width: 1400px; margin: 0 auto; padding: 1.5rem 1.5rem 0;">
            <x-social-share
                :url="route('projects.show', ['slug' => $project['slug']])"
                :title="$project['title']"
                :description="'Kimmex project: ' . $project['title']"
            />
        </div>

        <!-- --- MAIN CONTENT SPLIT --- -->
        <section class="py-10 md:py-16 px-4 md:px-6 bg-gradient-to-b from-white via-slate-50/60 to-white">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 max-w-[1400px] mx-auto">

                <!-- LEFT: CONTENT -->
                <div class="lg:col-span-8">
                    <!-- Description -->
                    @if(!empty($overviewSections))
                    <div class="mb-12 md:mb-16 reveal-up">
                        <h2 class="text-xl md:text-2xl font-black text-titan-navy mb-6 md:mb-8 flex items-center gap-3">
                            <x-lucide-help-circle class="w-5 h-5 md:w-6 md:h-6 text-titan-red" /> {{ __('Project Overview') }}
                        </h2>
                        <div class="space-y-6 md:space-y-8 text-base md:text-lg text-titan-navy/70 leading-relaxed project-khmer-content">
                            @foreach($overviewSections as $section)
                                <div class="{{ $section['class'] ?? '' }}">
                                    <h3 class="text-titan-navy font-bold text-sm uppercase tracking-widest mb-2">
                                        {{ $section['title'] }}
                                    </h3>
                                    <div class="{{ $section['rich_class'] ?? '' }} prose prose-base xl:prose-lg max-w-none text-titan-navy/70 project-khmer-content">
                                        {!! $renderProjectContent($section['content'] ?? '', $section['mode'] ?? 'auto') !!}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Scope -->
                    @if(!empty($project['scope']))
                        <div class="mb-12 md:mb-16 bg-gray-50 p-6 md:p-10 rounded border border-titan-navy/10 reveal-up">
                            <h2 class="text-xl md:text-2xl font-black text-titan-navy mb-6 md:mb-8 flex items-center gap-3">
                                <x-lucide-activity class="w-5 h-5 md:w-6 md:h-6 text-titan-red" /> {{ __('Scope of Work') }}
                            </h2>
                            <div class="prose prose-base xl:prose-lg max-w-none text-titan-navy/70 
                                [&_ul]:grid [&_ul]:grid-cols-1 [&_ul]:md:grid-cols-2 [&_ul]:gap-4 [&_ul]:list-none [&_ul]:p-0
                                [&_li]:flex [&_li]:items-center [&_li]:gap-3 [&_li]:p-4 [&_li]:bg-white [&_li]:rounded-lg [&_li]:shadow-sm [&_li]:border [&_li]:border-transparent [&_li]:hover:border-titan-red/20 [&_li]:transition-all [&_li]:font-bold [&_li]:text-titan-navy
                                [&_li:before]:content-[''] [&_li:before]:w-5 [&_li:before]:h-5 [&_li:before]:bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyNCIgaGVpZ2h0PSIyNCIgdmlld0JveD0iMCAwIDI0IDI0IiBmaWxsPSJub25lIiBzdHJva2U9IiNGRjZCMDAiIHN0cm9rZS13aWR0aD0iMiIgc3Ryb2tlLWxpbmVjYXA9InJvdW5kIiBzdHJva2UtbGluZWpvaW49InJvdW5kIj48cGF0aCBkPSJNMjIgMTEuMDhWMTJBMTAgMTAgMCAxIDEgMTcgNC40NyIvPjxwYXRoIGQ9Ik0yMiA0IDEyIDE0LjAxIDkgMTEiLz48L3N2Zz4=')] [&_li:before]:bg-contain [&_li:before]:bg-no-repeat
                            ">
                                @if(is_array($project['scope']))
                                    <ul>
                                        @foreach($project['scope'] as $item)
                                            <li>{{ $item }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <div class="project-rich-content">
                                        {!! $renderProjectContent($project['scope'] ?? '', 'list') !!}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Narrative -->
                    @if($hasEngineeringNarrative)
                        <div class="reveal-up">
                            <h2 class="text-xl md:text-2xl font-black text-titan-navy mb-6 md:mb-8 flex items-center gap-3">
                                <x-lucide-alert-triangle class="w-5 h-5 md:w-6 md:h-6 text-titan-red" />
                                {{ __('Engineering Challenges & Solutions') }}
                            </h2>
                            <div class="project-rich-content prose prose-base xl:prose-lg max-w-none text-titan-navy/70 project-khmer-content">
                                {!! $renderProjectContent($engineeringNarrative) !!}
                            </div>
                        </div>
                    @endif
                </div>

                <!-- RIGHT: KEY FACTS SIDEBAR -->
                <div class="lg:col-span-4">
                    <div class="bg-white/95 p-6 md:p-8 rounded-lg shadow-[0_18px_50px_rgba(11,43,92,0.08)] border border-titan-navy/10 lg:sticky lg:top-24 overflow-hidden relative">
                        <div class="absolute inset-x-0 top-0 h-1 bg-titan-red"></div>
                        <h3 class="text-lg md:text-xl font-black text-titan-navy mb-6 md:mb-8 pb-4 border-b border-titan-navy/10">
                            {{ __('Project Data') }}
                        </h3>

                        <div class="space-y-6">
                            <div class="group">
                                <span
                                    class="block text-xs font-bold text-titan-navy/70 uppercase tracking-widest mb-1 group-hover:text-titan-red transition-colors">{{ __('Client') }}</span>
                                <div class="flex items-center gap-3 font-bold text-titan-navy text-base md:text-lg">
                                    <x-lucide-user
                                        class="w-5 h-5 text-gray-300 group-hover:text-titan-red transition-colors" />
                                    {{ $project['client'] }}
                                </div>
                            </div>

                            <div class="group">
                                <span
                                    class="block text-xs font-bold text-titan-navy/70 uppercase tracking-widest mb-1 group-hover:text-titan-red transition-colors">{{ __('Location') }}</span>
                                <div class="flex items-center gap-3 font-bold text-titan-navy text-base md:text-lg">
                                    <x-lucide-map-pin
                                        class="w-5 h-5 text-gray-300 group-hover:text-titan-red transition-colors" />
                                    {{ $project['location'] }}
                                </div>
                            </div>

                            @if($project['built_area'])
                                <div class="group">
                                    <span
                                        class="block text-xs font-bold text-titan-navy/70 uppercase tracking-widest mb-1 group-hover:text-titan-red transition-colors">{{ __('Built Area') }}</span>
                                    <div class="flex items-center gap-3 font-bold text-titan-navy text-base md:text-lg">
                                        <x-lucide-maximize
                                            class="w-5 h-5 text-gray-300 group-hover:text-titan-red transition-colors" />
                                        {{ $project['built_area'] }}
                                    </div>
                                </div>
                            @endif

                            <div class="group">
                                <span
                                    class="block text-xs font-bold text-titan-navy/70 uppercase tracking-widest mb-1 group-hover:text-titan-red transition-colors">{{ __('Year & Status') }}</span>
                                <div class="flex items-center gap-3 font-bold text-titan-navy text-base md:text-lg">
                                    <x-lucide-calendar
                                        class="w-5 h-5 text-gray-300 group-hover:text-titan-red transition-colors" />
                                    {{ $project['year'] }} <span class="text-xs font-black text-titan-navy/40 ml-2 uppercase tracking-widest">{{ $project['status'] }}</span>                            </div>
                        </div>

                        <!-- CENTERED SOCIAL SHARING -->
                        <div class="reveal-up pt-8 md:pt-12 border-t border-gray-100 mt-8 md:mt-12 flex flex-col items-center gap-4 md:gap-6">
                            <div class="text-[10px] font-black text-titan-navy/20 uppercase tracking-[0.4em]">{{ __('Share this Project') }}</div>
                            <div class="flex items-center gap-3 md:gap-4">
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" rel="noopener"
                                    class="w-10 h-10 md:w-12 md:h-12 bg-social-facebook rounded flex items-center justify-center text-white hover:brightness-110 transition-all transform hover:-translate-y-1 shadow-lg group/fb">
                                    <x-social-icon network="facebook" class="w-4 h-4 md:w-5 md:h-5 transition-transform group-hover/fb:scale-110" />
                                </a>
                                <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(url()->current()) }}" target="_blank" rel="noopener"
                                    class="w-10 h-10 md:w-12 md:h-12 bg-social-linkedin rounded flex items-center justify-center text-white hover:brightness-110 transition-all transform hover:-translate-y-1 shadow-lg group/li">
                                    <x-social-icon network="linkedin" class="w-4 h-4 md:w-5 md:h-5 transition-transform group-hover/li:scale-110" />
                                </a>
                                <a href="https://t.me/share/url?url={{ urlencode(url()->current()) }}&text={{ urlencode($project['title']) }}" target="_blank" rel="noopener"
                                    class="w-10 h-10 md:w-12 md:h-12 bg-social-telegram rounded flex items-center justify-center text-white hover:brightness-110 transition-all transform hover:-translate-y-1 shadow-lg group/tg">
                                    <x-social-icon network="telegram" class="w-4 h-4 md:w-5 md:h-5 transition-transform group-hover/tg:scale-110" />
                                </a>
                                <div x-data="{ 
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
                                        setTimeout(() => this.copied = false, 2000);
                                    }
                                }" class="relative">
                                    <button @click="copyLink()"
                                        class="w-10 h-10 md:w-12 md:h-12 flex items-center justify-center rounded transition-all duration-300 transform hover:-translate-y-1 active:scale-95 shadow-lg group/link"
                                        :class="copied ? 'bg-titan-red text-white border-titan-red' : 'bg-white text-titan-navy border border-gray-100 hover:border-titan-red/30 hover:text-titan-red'">
                                        <x-lucide-link class="w-4 h-4 md:w-5 md:h-5" x-show="!copied" />
                                        <x-lucide-check class="w-4 h-4 md:w-5 md:h-5" x-show="copied" x-cloak />
                                    </button>

                                    <!-- Tooltip -->
                                    <div x-show="copied" 
                                         x-transition:enter="transition ease-out duration-300"
                                         x-transition:enter-start="opacity-0 translate-y-2"
                                         x-transition:enter-end="opacity-100 translate-y-0"
                                         x-transition:leave="transition ease-in duration-200"
                                         x-transition:leave-start="opacity-100 translate-y-0"
                                         x-transition:leave-end="opacity-0 translate-y-2"
                                         class="absolute -top-10 left-1/2 -translate-x-1/2 px-3 py-1.5 bg-titan-navy text-white text-[9px] font-black uppercase tracking-widest rounded whitespace-nowrap shadow-xl z-50"
                                         style="display: none;">
                                        {{ __('Copied!') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>

        <!-- --- GALLERY SECTION --- -->
        @if(count($project['images']) > 0)
            <div x-data="{ lightboxOpen: false, lightboxIndex: 0, images: {{ Js::from($project['images']) }} }">
            <section class="bg-slate-50 py-10 md:py-14 px-4 md:px-6 text-titan-navy border-y border-titan-navy/10">
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
