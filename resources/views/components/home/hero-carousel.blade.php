@php
    $fallbackImage = '/images/webp/projects/Thumbnail-5.webp';
    $contentLocale = app()->getLocale() === 'kh' ? 'km' : app()->getLocale();
    $featuredProjects = \Illuminate\Support\Facades\Cache::remember('hero_featured_projects_'.$contentLocale, now()->addHours(6), function() use ($fallbackImage, $contentLocale) {
        return \App\Models\Project::where('isFeatured', true)
            ->where('isActive', true)
            ->take(5)
            ->get()
            ->map(function (\App\Models\Project $p, $index) use ($fallbackImage, $contentLocale) {
                return [
                    'id' => $index + 1,
                    'image' => \App\Support\PublicStorage::urlIfExists($p->heroImage, $fallbackImage),
                    'subtitle' => $p->projectCategory ? $p->projectCategory->getTranslation('name', $contentLocale) : ($p->category ?: __('Featured Project')),
                    'title' => $p->getTranslation('title', $contentLocale) ?: $p->getTranslation('title', 'en'),
                    'desc' => \Illuminate\Support\Str::limit(strip_tags($p->getTranslation('description', $contentLocale) ?: $p->getTranslation('description', 'en')), 120),
                    'link' => '/projects/' . $p->slug
                ];
            })->toArray();
    });
    if (count($featuredProjects) > 0) {
        $slides = $featuredProjects;
    } else {
        $slides = [
            [
                'id' => 1,
                'image' => '/images/webp/hero/hero-1.webp',
                'subtitle' => __('Government Infrastructure'),
                'title' => __('Ministry of Economy'),
                'desc' => __('Over 25 years of excellence in building the future of Cambodia. We deliver high-quality infrastructure.'),
                'link' => '/projects'
            ],
            [
                'id' => 2,
                'image' => '/images/webp/hero/hero-2.webp',
                'subtitle' => __('Water Infrastructure'),
                'title' => __('Khleang Toeuk WTP'),
                'desc' => __('Ensuring clean and accessible water solutions through state-of-the-art treatment facilities and engineering.'),
                'link' => '/projects'
            ],
            [
                'id' => 3,
                'image' => '/images/webp/hero/hero-3.webp',
                'subtitle' => __('Infrastructure Protection'),
                'title' => __('Mekong Bank Protection'),
                'desc' => __('Securing vulnerable riverbanks and developing resilient infrastructure to protect communities and commerce.'),
                'link' => '/projects'
            ]
        ];
    }
@endphp

<section x-data="{
        current: 0,
        prev: null,
        slides: {{ Js::from($slides) }},
        timer: null,
        interval: 6500,
        isAnimating: false,
        isPaused: false,
        prefersReducedMotion: false,

        nextSlide() {
            if (this.isAnimating || this.slides.length <= 1) return;
            this.goTo((this.current + 1) % this.slides.length);
        },
        prevSlide() {
            if (this.isAnimating || this.slides.length <= 1) return;
            this.goTo((this.current - 1 + this.slides.length) % this.slides.length);
        },
        goToSlide(index) {
            if (this.isAnimating || index === this.current) return;
            this.goTo(index);
        },
        goTo(index) {
            this.isAnimating = true;
            this.prev = this.current;
            this.current = index;
            this.preloadNext();
            this.resetTimer();
            const duration = this.prefersReducedMotion ? 0 : 850;
            window.setTimeout(() => {
                this.prev = null;
                this.isAnimating = false;
            }, duration);
        },
        resetTimer() {
            clearInterval(this.timer);
            this.startTimer();
        },
        startTimer() {
            if (this.prefersReducedMotion || this.isPaused || this.slides.length <= 1) return;
            this.timer = setInterval(() => this.nextSlide(), this.interval);
        },
        pause() { this.isPaused = true; clearInterval(this.timer); },
        resume() {
            if (!this.isPaused) return;
            this.isPaused = false;
            this.startTimer();
        },
        preloadImage(src) {
            if (!src) return;
            const img = new Image(); img.decoding = 'async'; img.src = src;
        },
        preloadNext() {
            this.preloadImage(this.slides[(this.current + 1) % this.slides.length]?.image);
        },
        initCarousel() {
            this.prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            this.preloadImage(this.slides[0]?.image);
            this.preloadNext();
            this.startTimer();
        }
    }"
    x-init="initCarousel()"
    @mouseenter="pause()"
    @mouseleave="resume()"
    @focusin="pause()"
    @focusout="resume()"
    @keydown.arrow-left.window="prevSlide()"
    @keydown.arrow-right.window="nextSlide()"
    class="relative h-[100svh] min-h-[520px] max-h-[700px] sm:h-[560px] md:h-[640px] md:max-h-none overflow-hidden bg-titan-navy text-white"
    data-priority-image>

    <!-- === SLIDES (crossfade stack) === -->
    <template x-for="(slide, index) in slides" :key="`slide-${index}`">
        <div class="absolute inset-0"
            :class="{
                'z-10 hero-slide-enter': index === current,
                'z-[9] hero-slide-leave': index === prev,
                'z-0 opacity-0': index !== current && index !== prev
            }">
            <img :src="slide.image"
                :alt="slide.title"
                :class="index === current && !prefersReducedMotion ? 'animate-hero-kenburns' : ''"
                class="hero-slide-image object-cover w-full h-full"
                :loading="index === 0 ? 'eager' : 'lazy'"
                decoding="async"
                :fetchpriority="index === 0 ? 'high' : 'auto'" />
            {{-- Gradient overlays --}}
            <div class="absolute inset-0 bg-gradient-to-r from-titan-navy/80 via-titan-navy/50 to-titan-navy/20 sm:from-titan-navy/75 sm:via-titan-navy/35 sm:to-transparent"></div>
            <div class="absolute inset-0 bg-gradient-to-b from-titan-navy/20 via-transparent to-titan-navy/60"></div>
        </div>
    </template>

    <!-- === CONTENT OVERLAY === -->
    <div class="absolute inset-0 flex flex-col justify-center z-20 pt-28 pb-24 sm:pt-32 sm:pb-20 lg:pt-32 lg:pb-12">
        <div class="max-w-[1200px] w-full mx-auto px-5 sm:px-6 grid grid-cols-1 lg:grid-cols-2">
            <div>
                <template x-for="(slide, index) in slides" :key="`content-${index}`">
                    <div x-show="index === current"
                        :class="prefersReducedMotion ? '' : 'hero-content-enter'"
                        class="w-full">
                        <p class="text-titan-red font-black text-[9px] sm:text-[10px] md:text-xs uppercase tracking-[0.3em] sm:tracking-[0.35em] mb-3 sm:mb-4 flex items-center gap-2 sm:gap-3">
                            <span class="inline-block w-6 sm:w-8 h-px bg-titan-red"></span>
                            <span x-text="slide.subtitle"></span>
                        </p>
                        <h1 class="hero-copy-shadow font-heading font-[900] mb-4 sm:mb-7 !text-white uppercase leading-[1.05] sm:leading-[1.02] tracking-normal"
                            :class="slide.title.length > 48
                                ? 'max-w-[820px] text-[1.25rem] sm:text-[1.5rem] md:text-[2rem] xl:text-[2.3rem]'
                                : 'max-w-[900px] text-[1.5rem] sm:text-[1.75rem] md:text-[2.35rem] xl:text-[2.8rem]'"
                            x-text="slide.title"></h1>

                        <p class="hero-copy-shadow text-[#F8FAFC] max-w-[600px] mb-6 sm:mb-10 font-medium text-sm sm:text-base lg:text-lg leading-relaxed line-clamp-3 sm:line-clamp-none"
                            x-text="slide.desc"></p>

                        <div class="flex flex-col sm:flex-row flex-wrap gap-3 sm:gap-4 md:gap-8">
                            <a :href="slide.link"
                                class="group relative overflow-hidden bg-titan-red text-white px-6 sm:px-10 md:px-12 py-3.5 sm:py-4 md:py-5 font-black transition-all duration-500 flex items-center justify-center gap-3 sm:gap-4 shadow-2xl rounded {{ app()->getLocale() === 'km' ? 'font-khmer text-base sm:text-lg tracking-normal' : 'text-[11px] sm:text-[12px] md:text-[13px] tracking-[0.2em] sm:tracking-[0.25em] uppercase hover:bg-white hover:text-titan-navy' }}">
                                <span class="relative z-10">{{ __('VIEW PROJECT') }}</span>
                                <x-lucide-arrow-right class="group-hover:translate-x-2 transition-transform w-4 h-4 sm:w-5 sm:h-5 relative z-10" />
                            </a>
                            <a href="/contact"
                                class="group border-2 border-white/25 backdrop-blur-sm text-white px-6 sm:px-10 md:px-12 py-3.5 sm:py-4 md:py-5 font-black transition-all duration-500 flex items-center justify-center gap-3 sm:gap-4 rounded {{ app()->getLocale() === 'km' ? 'font-khmer text-base sm:text-lg tracking-normal' : 'text-[11px] sm:text-[12px] md:text-[13px] tracking-[0.2em] sm:tracking-[0.25em] uppercase hover:bg-white hover:text-titan-navy hover:border-white' }}">
                                <x-lucide-phone class="w-4 h-4 sm:w-5 sm:h-5 group-hover:rotate-12 transition-transform" />
                                <span>{{ __('CONTACT US') }}</span>
                            </a>
                        </div>
                    </div>
                </template>
            </div>
            <div></div>
        </div>
    </div>

    <!-- Navigation Controls -->
    <div class="absolute bottom-5 sm:bottom-8 md:bottom-12 left-0 right-0 z-30">
        <div class="max-w-[1200px] mx-auto px-5 sm:px-6 flex items-end justify-between">
            <!-- Pagination -->
            <div class="flex items-center gap-3 sm:gap-5">
                <div class="text-xs sm:text-sm font-black tracking-[0.28em] text-white/90 tabular-nums">
                    <span x-text="String(current + 1).padStart(2, '0')"></span>
                    <span class="mx-1.5 sm:mx-2 text-white/35">/</span>
                    <span class="text-white/55" x-text="String(slides.length).padStart(2, '0')"></span>
                </div>
                <div class="flex gap-2 sm:gap-3" role="tablist" aria-label="{{ __('Featured projects') }}">
                    <template x-for="(slide, index) in slides" :key="'dot-'+index">
                        <button @click="goToSlide(index)"
                            type="button"
                            :aria-label="`{{ __('Show slide') }} ${index + 1}: ${slide.title}`"
                            :aria-selected="index === current"
                            :class="index === current ? 'w-10 sm:w-14 bg-titan-red' : 'w-5 sm:w-7 bg-white/30 hover:bg-white/70'"
                            class="h-1 sm:h-1.5 rounded-full transition-all duration-500 focus:outline-none focus:ring-2 focus:ring-white/80">
                        </button>
                    </template>
                </div>
            </div>

            <!-- Arrows + Stats -->
            <div class="flex items-center gap-4 sm:gap-8">
                <div class="hidden xl:flex gap-8 border-r border-white/10 pr-8 mr-2">
                    <div>
                        <div class="text-2xl font-black text-white">25+</div>
                        <div class="text-[10px] text-titan-red uppercase tracking-widest font-bold">{{ __('Years Exp') }}</div>
                    </div>
                    <div>
                        <div class="text-2xl font-black text-white">150+</div>
                        <div class="text-[10px] text-titan-red uppercase tracking-widest font-bold">{{ __('Projects') }}</div>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button @click="prevSlide()"
                        type="button"
                        aria-label="{{ __('Previous slide') }}"
                        class="w-9 h-9 sm:w-12 sm:h-12 border border-white/20 rounded-full flex items-center justify-center hover:bg-titan-red hover:border-titan-red transition-all duration-300 text-white focus:outline-none focus:ring-2 focus:ring-white/80">
                        <x-lucide-chevron-left class="w-4 h-4 sm:w-6 sm:h-6" />
                    </button>
                    <button @click="nextSlide()"
                        type="button"
                        aria-label="{{ __('Next slide') }}"
                        class="w-9 h-9 sm:w-12 sm:h-12 border border-white/20 rounded-full flex items-center justify-center hover:bg-titan-red hover:border-titan-red transition-all duration-300 text-white focus:outline-none focus:ring-2 focus:ring-white/80">
                        <x-lucide-chevron-right class="w-4 h-4 sm:w-6 sm:h-6" />
                    </button>
                </div>
            </div>
        </div>
    </div>

</section>
