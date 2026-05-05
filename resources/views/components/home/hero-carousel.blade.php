@php
    $featuredProjects = \App\Models\Project::where('isFeatured', true)
        ->where('isActive', true)
        ->take(5)
        ->get();
    if ($featuredProjects->count() > 0) {
        $slides = $featuredProjects->map(function (\App\Models\Project $p, $index) {
            return [
                'id' => $index + 1,
                'image' => ($p->heroImage && (\Illuminate\Support\Str::startsWith($p->heroImage, '/') ? file_exists(public_path($p->heroImage)) : \Illuminate\Support\Facades\Storage::disk('public')->exists($p->heroImage)))
                    ? (\Illuminate\Support\Str::startsWith($p->heroImage, '/') ? $p->heroImage : \Illuminate\Support\Facades\Storage::url($p->heroImage))
                    : null,
                'subtitle' => $p->projectCategory ? $p->projectCategory->getTranslation('name', app()->getLocale() === 'km' ? 'kh' : app()->getLocale()) : ($p->category ?: __('Featured Project')),
                'title' => $p->getTranslation('title', app()->getLocale() === 'km' ? 'kh' : app()->getLocale()) ?: $p->getTranslation('title', 'en'),
                'desc' => \Illuminate\Support\Str::limit(strip_tags($p->getTranslation('description', app()->getLocale() === 'km' ? 'kh' : app()->getLocale()) ?: $p->getTranslation('description', 'en')), 120),
                'link' => '/projects/' . $p->slug
            ];
        })->toArray();
    } else {
        $slides = [
            [
                'id' => 1,
                'image' => '/images/hero/hero-1.jpg',
                'subtitle' => __('Government Infrastructure'),
                'title' => __('Ministry of Economy'),
                'desc' => __('Over 25 years of excellence in building the future of Cambodia. We deliver high-quality infrastructure.'),
                'link' => '/projects'
            ],
            [
                'id' => 2,
                'image' => '/images/hero/hero-2.jpg',
                'subtitle' => __('Water Infrastructure'),
                'title' => __('Khleang Toeuk WTP'),
                'desc' => __('Ensuring clean and accessible water solutions through state-of-the-art treatment facilities and engineering.'),
                'link' => '/projects'
            ],
            [
                'id' => 3,
                'image' => '/images/hero/hero-3.jpg',
                'subtitle' => __('Infrastructure Protection'),
                'title' => __('Mekong Bank Protection'),
                'desc' => __('Securing vulnerable riverbanks and developing resilient infrastructure to protect communities and commerce.'),
                'link' => '/projects'
            ]
        ];
    }
@endphp

<header x-data="{
        current: 0,
        direction: 1,
        slides: {{ Js::from($slides) }},
        timer: null,
        
        nextSlide() {
            this.direction = 1;
            this.current = (this.current === this.slides.length - 1) ? 0 : this.current + 1;
            this.resetTimer();
        },
        prevSlide() {
            this.direction = -1;
            this.current = (this.current === 0) ? this.slides.length - 1 : this.current - 1;
            this.resetTimer();
        },
        goToSlide(index) {
            this.direction = index > this.current ? 1 : -1;
            this.current = index;
            this.resetTimer();
        },
        resetTimer() {
            clearInterval(this.timer);
            this.startTimer();
        },
        startTimer() {
            this.timer = setInterval(() => {
                this.nextSlide();
            }, 6000);
        }
    }" x-init="startTimer()" class="relative h-screen min-h-[700px] overflow-hidden bg-titan-navy text-white">

    <!-- === SLIDES === -->
    <template x-for="(slide, index) in slides" :key="index">
        <div x-show="current === index" x-transition:enter="transition transform ease-out duration-1000"
            x-transition:enter-start="opacity-0 scale-105" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition absolute transform ease-in duration-700 z-0"
            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-105"
            class="absolute inset-0 w-full h-full">

            <template x-if="slide.image">
                <div class="relative w-full h-full overflow-hidden">
                    <img :src="slide.image" :alt="slide.title" class="object-cover w-full h-full opacity-100 animate-slow-zoom" />
                    {{-- Deep multi-stage gradient for maximum text contrast --}}
                    <div class="absolute inset-0 bg-gradient-to-r from-titan-navy/90 via-titan-navy/40 to-transparent"></div>
                    <div class="absolute inset-0 bg-gradient-to-b from-titan-navy/40 via-transparent to-titan-navy/80"></div>
                </div>
            </template>
            <template x-if="!slide.image">
                <div class="w-full h-full bg-titan-navy shadow-inner opacity-100">
                    <div class="absolute inset-0 bg-[radial-gradient(circle_at_30%_20%,rgba(227,30,36,0.15)_0%,transparent_50%)]"></div>
                    <div class="absolute inset-0 bg-gradient-to-r from-titan-navy/90 via-titan-navy/40 to-transparent"></div>
                </div>
            </template>
        </div>
    </template>

    <!-- === CONTENT OVERLAY === -->
    <div class="absolute inset-0 flex items-center z-10 pt-32 lg:pt-40">
        <div class="max-w-[1400px] w-full mx-auto px-6 grid grid-cols-1 lg:grid-cols-2">

            <template x-for="(slide, index) in slides" :key="'content-'+index">
                <div x-show="current === index" 
                    x-transition:enter="transition ease-out duration-1000 delay-300"
                    x-transition:enter-start="opacity-0 translate-y-12"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-500 absolute"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-8">

                    <div class="flex items-center gap-4 mb-10">
                        <div class="transition-all duration-1000 delay-500 inline-flex items-center gap-3 glass-premium px-6 py-2.5 rounded-full">
                            <div class="w-2 h-2 bg-titan-red animate-pulse rounded-full"></div>
                            <span class="{{ app()->getLocale() === 'km' ? 'font-khmer text-white/90 text-sm tracking-normal' : 'text-white/90 font-black tracking-[0.3em] uppercase text-[10px]' }}"
                                x-text="slide.subtitle"></span>
                        </div>
                    </div>

                    <h1 class="font-heading font-black mb-8 text-white uppercase leading-[0.9] tracking-tighter max-w-4xl"
                        style="font-size: clamp(3rem, 7vw, 6.5rem);"
                        x-text="slide.title"></h1>

                    <p class="text-white/70 max-w-lg mb-12 font-medium text-lg md:text-xl leading-relaxed"
                        x-text="slide.desc"></p>

                    <div class="flex flex-wrap gap-5">
                        <a :href="slide.link"
                            class="group relative overflow-hidden bg-titan-red text-white px-10 py-5 font-black transition-all duration-500 flex items-center gap-4 shadow-2xl rounded-2xl {{ app()->getLocale() === 'km' ? 'font-khmer text-base tracking-normal' : 'text-xs tracking-[0.2em] uppercase hover:bg-white hover:text-titan-navy' }}">
                            <span class="relative z-10">{{ __('VIEW PROJECT') }}</span>
                            <x-lucide-arrow-right class="group-hover:translate-x-2 transition-transform w-5 h-5 relative z-10" />
                        </a>
                        <a href="/contact"
                            class="group border-2 border-white/20 backdrop-blur-md text-white px-10 py-5 font-black transition-all duration-500 flex items-center gap-4 rounded-2xl {{ app()->getLocale() === 'km' ? 'font-khmer text-base tracking-normal' : 'text-xs tracking-[0.2em] uppercase hover:bg-white hover:text-titan-navy hover:border-white' }}">
                            <x-lucide-phone class="w-5 h-5 group-hover:rotate-12 transition-transform" />
                            <span>{{ __('CONTACT US') }}</span>
                        </a>
                    </div>
                </div>
            </template>

        </div>
    </div>

    <!-- Navigation Controls -->
    <div class="absolute bottom-12 left-0 right-0 z-20">
        <div class="max-w-[1400px] mx-auto px-6 flex items-end justify-between">
            <!-- Pagination Lines -->
            <div class="flex gap-4">
                <template x-for="(slide, index) in slides" :key="'dot-'+index">
                    <button @click="goToSlide(index)"
                        :class="index === current ? 'w-16 bg-titan-red' : 'w-8 bg-white/30 hover:bg-titan-red'"
                        class="h-1.5 transition-all duration-300"></button>
                </template>
            </div>

            <!-- Arrows -->
            <div class="flex items-center gap-8">
                <!-- Decorative Stats (Integrated) -->
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
                    <button @click="prevSlide"
                        class="w-12 h-12 border border-white/20 rounded-full flex items-center justify-center hover:bg-titan-red hover:border-titan-red transition-all duration-300 text-white">
                        <x-lucide-chevron-left class="w-6 h-6" />
                    </button>
                    <button @click="nextSlide"
                        class="w-12 h-12 border border-white/20 rounded-full flex items-center justify-center hover:bg-titan-red hover:border-titan-red transition-all duration-300 text-white">
                        <x-lucide-chevron-right class="w-6 h-6" />
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Removed separate Decorative Stats to prevent overlap -->
</header>