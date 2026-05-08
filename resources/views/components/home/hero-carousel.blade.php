@php
    $featuredProjects = \App\Models\Project::where('isFeatured', true)->where('isActive', true)->take(5)->get();
    if ($featuredProjects->count() > 0) {
        $slides = $featuredProjects
            ->map(function (\App\Models\Project $p, $index) {
                return [
                    'id' => $index + 1,
                    'image' =>
                        $p->heroImage &&
                        (\Illuminate\Support\Str::startsWith($p->heroImage, '/')
                            ? file_exists(public_path($p->heroImage))
                            : \Illuminate\Support\Facades\Storage::disk('public')->exists($p->heroImage))
                            ? (\Illuminate\Support\Str::startsWith($p->heroImage, '/')
                                ? $p->heroImage
                                : \Illuminate\Support\Facades\Storage::url($p->heroImage))
                            : null,
                    'subtitle' => $p->projectCategory
                        ? $p->projectCategory->getTranslation(
                            'name',
                            app()->getLocale() === 'km' ? 'kh' : app()->getLocale(),
                        )
                        : ($p->category ?:
                        __('Featured Project')),
                    'title' =>
                        $p->getTranslation('title', app()->getLocale() === 'km' ? 'kh' : app()->getLocale()) ?:
                        $p->getTranslation('title', 'en'),
                    'desc' => \Illuminate\Support\Str::limit(
                        strip_tags(
                            $p->getTranslation(
                                'description',
                                app()->getLocale() === 'km' ? 'kh' : app()->getLocale(),
                            ) ?:
                            $p->getTranslation('description', 'en'),
                        ),
                        140,
                    ),
                    'link' => '/projects/' . $p->slug,
                ];
            })
            ->toArray();
    } else {
        $slides = [
            [
                'id' => 1,
                'image' => '/images/hero/hero-1.jpg',
                'subtitle' => __('Government Infrastructure'),
                'title' => __('Ministry of Economy'),
                'desc' => __(
                    'Over 25 years of excellence in building the future of Cambodia. We deliver high-quality infrastructure.',
                ),
                'link' => '/projects',
            ],
            [
                'id' => 2,
                'image' => '/images/hero/hero-2.jpg',
                'subtitle' => __('Water Infrastructure'),
                'title' => __('Khleang Toeuk WTP'),
                'desc' => __(
                    'Ensuring clean and accessible water solutions through state-of-the-art treatment facilities and engineering.',
                ),
                'link' => '/projects',
            ],
            [
                'id' => 3,
                'image' => '/images/hero/hero-3.jpg',
                'subtitle' => __('Infrastructure Protection'),
                'title' => __('Mekong Bank Protection'),
                'desc' => __(
                    'Securing vulnerable riverbanks and developing resilient infrastructure to protect communities and commerce.',
                ),
                'link' => '/projects',
            ],
        ];
    }
    // Pre-process slides to include word arrays for staggered title animation
    $processedSlides = collect($slides)->map(function($slide) {
        $slide['titleWords'] = explode(' ', $slide['title']);
        return $slide;
    })->toArray();
@endphp

<header x-data="{
        current: 0,
        slides: {{ Js::from($processedSlides) }},
        progress: 0,
        timer: null,
        progressTimer: null,
        
        nextSlide() {
            this.current = (this.current === this.slides.length - 1) ? 0 : this.current + 1;
            this.resetTimers();
        },
        prevSlide() {
            this.current = (this.current === 0) ? this.slides.length - 1 : this.current - 1;
            this.resetTimers();
        },
        goToSlide(index) {
            this.current = index;
            this.resetTimers();
        },
        resetTimers() {
            clearInterval(this.timer);
            clearInterval(this.progressTimer);
            this.progress = 0;
            this.startTimers();
        },
        startTimers() {
            this.timer = setInterval(() => {
                this.nextSlide();
            }, 8000);
            
            this.progressTimer = setInterval(() => {
                if(this.progress < 100) {
                    this.progress += 0.5;
                }
            }, 40); // 8000ms / 200 steps for smoother filling
        }
    }" x-init="startTimers()" class="relative min-h-screen lg:h-[100vh] overflow-hidden bg-titan-navy text-white font-sans antialiased">

    <!-- === BACKGROUND LAYERS === -->
    <div class="absolute inset-0 z-0">
        <template x-for="(slide, index) in slides" :key="'bg-'+index">
            <div x-show="current === index" 
                x-transition:enter="transition-all duration-[2000ms] ease-[cubic-bezier(0.2,0,0,1)]"
                x-transition:enter-start="opacity-0 scale-110"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition-all duration-[2000ms] ease-[cubic-bezier(0.2,0,0,1)] absolute inset-0"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-[1.05]"
                class="absolute inset-0">
                
                <img :src="slide.image" :alt="slide.title" class="w-full h-full object-cover" />
                
                {{-- Cinematic Overlays --}}
                <div class="absolute inset-0 bg-titan-navy/40"></div>
                <div class="absolute inset-0 bg-gradient-to-b from-titan-navy/60 via-transparent to-titan-navy/90"></div>
            </div>
        </template>
    </div>

    <!-- === CENTER CONTENT === -->
    <div class="relative z-20 min-h-screen flex items-center pt-24 pb-48">
        <div class="max-w-[1600px] mx-auto px-6 lg:px-12 w-full">
            <template x-for="(slide, index) in slides" :key="'text-'+index">
                <div x-show="current === index" class="max-w-5xl space-y-12">
                    
                    <!-- Subtitle with Red Line -->
                    <div x-show="current === index"
                        x-transition:enter="transition-all duration-1000 delay-300 ease-out"
                        x-transition:enter-start="opacity-0 -translate-x-20"
                        x-transition:enter-end="opacity-100 translate-x-0"
                    <!-- Subtitle with Red Line -->
                    <div x-show="current === index"
                        x-transition:enter="transition-all duration-1000 delay-300 ease-out"
                        x-transition:enter-start="opacity-0 -translate-x-20"
                        x-transition:enter-end="opacity-100 translate-x-0"
                        class="flex items-center gap-6 lg:gap-10">
                        <div class="w-16 lg:w-24 h-[4px] bg-titan-red"></div>
                        <span class="text-titan-red font-logo font-bold text-xs lg:text-sm uppercase tracking-[0.6em]" x-text="slide.subtitle"></span>
                    </div>

                    <!-- Editorial Title (Word-by-Word Reveal) -->
                    <div class="flex flex-wrap gap-x-6 lg:gap-x-12 gap-y-4">
                        <template x-for="(word, wIndex) in slide.titleWords" :key="'word-'+wIndex">
                            <div class="overflow-hidden">
                                <span x-show="current === index"
                                    x-transition:enter="transition-all duration-[1200ms] ease-[cubic-bezier(0.2,0,0,1)]"
                                    :style="'transition-delay: ' + (500 + (wIndex * 150)) + 'ms'"
                                    x-transition:enter-start="opacity-0 translate-y-full blur-xl"
                                    x-transition:enter-end="opacity-100 translate-y-0 blur-0"
                                    class="inline-block font-logo font-black leading-[0.8] tracking-[-0.05em] uppercase"
                                    style="font-size: clamp(3.5rem, 14vw, 11rem);"
                                    x-text="word"></span>
                            </div>
                        </template>
                    </div>

                    <!-- Description -->
                    <p x-show="current === index"
                        x-transition:enter="transition-all duration-1000 delay-[1200ms] ease-out"
                        x-transition:enter-start="opacity-0 translate-y-12"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        class="text-white/60 max-w-4xl text-lg lg:text-[1.4rem] leading-relaxed font-inter font-medium lg:font-normal"
                        x-text="slide.desc"></p>

                    <!-- Buttons -->
                    <div x-show="current === index"
                        x-transition:enter="transition-all duration-1000 delay-[1400ms] ease-out"
                        x-transition:enter-start="opacity-0 translate-y-12"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        class="flex flex-wrap gap-6 pt-12">
                        <a :href="slide.link"
                            class="group relative bg-titan-red text-white px-16 py-8 font-logo font-bold text-xs tracking-[0.3em] uppercase transition-all duration-500 rounded-none overflow-hidden">
                            <span class="relative z-10 flex items-center gap-6">
                                {{ __('VIEW PROJECT') }}
                                <x-lucide-arrow-right class="w-7 h-7 group-hover:translate-x-2 transition-transform" />
                            </span>
                            <div class="absolute inset-0 bg-white translate-y-full group-hover:translate-y-0 transition-transform duration-500 z-0"></div>
                        </a>
                        <a href="/contact"
                            class="group border-2 border-white/40 text-white px-16 py-8 font-logo font-bold text-xs tracking-[0.3em] uppercase hover:bg-white hover:text-titan-navy transition-all duration-500 rounded-none">
                            <span>{{ __('CONTACT US') }}</span>
                        </a>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- === BOTTOM CONTROL BAR === -->
    <div class="absolute bottom-0 left-0 right-0 z-40">
        <div class="max-w-[1600px] mx-auto px-6 lg:px-12 pb-24">
            <div class="flex flex-col lg:flex-row items-end lg:items-center justify-between gap-16">
                
                <!-- Progress Indicators -->
                <div class="flex items-center gap-6 w-full lg:w-[600px]">
                    <template x-for="(slide, index) in slides" :key="'progress-'+index">
                        <button @click="goToSlide(index)"
                            class="flex-grow h-[4px] transition-all duration-500 relative bg-white/10 overflow-hidden">
                            <div x-show="index === current" 
                                class="absolute inset-y-0 left-0 bg-titan-red transition-all duration-[40ms] ease-linear"
                                :style="'width: ' + progress + '%'"></div>
                            <div x-show="index < current" class="absolute inset-0 bg-titan-red/70"></div>
                        </button>
                    </template>
                </div>

                <!-- Stats & Navigation Combined -->
                <div class="flex items-center gap-20">
                    <!-- Stats Group -->
                    <div class="flex items-center gap-24 border-r border-white/10 pr-24 hidden lg:flex">
                        <div class="text-right group cursor-default">
                            <div class="text-7xl font-logo font-black text-white leading-none group-hover:text-titan-red transition-colors duration-500">25+</div>
                            <div class="text-[11px] font-logo font-bold text-white/40 uppercase tracking-[0.5em] mt-5">{{ __('YEARS TRUST') }}</div>
                        </div>
                        <div class="text-right group cursor-default">
                            <div class="text-7xl font-logo font-black text-white leading-none group-hover:text-titan-red transition-colors duration-500">150+</div>
                            <div class="text-[11px] font-logo font-bold text-white/40 uppercase tracking-[0.5em] mt-5">{{ __('PROJECTS') }}</div>
                        </div>
                    </div>

                    <!-- Navigation Arrows -->
                    <div class="flex gap-8">
                        <button @click="prevSlide"
                            class="w-24 h-24 rounded-full border border-white/20 flex items-center justify-center text-white hover:bg-white hover:text-titan-navy hover:scale-105 transition-all duration-500">
                            <x-lucide-arrow-left class="w-10 h-10" />
                        </button>
                        <button @click="nextSlide"
                            class="w-24 h-24 rounded-full border border-white/20 flex items-center justify-center text-white hover:bg-white hover:text-titan-navy hover:scale-105 transition-all duration-500">
                            <x-lucide-arrow-right class="w-10 h-10" />
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Scroll Indicator -->
    <div
        class="absolute bottom-0 left-1/2 -translate-x-1/2 w-px h-24 bg-gradient-to-b from-white/20 to-transparent z-10 overflow-hidden">
        <div class="w-full h-1/2 bg-titan-red animate-scroll-down"></div>
    </div>
</header>
