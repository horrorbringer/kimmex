@php
    $featuredProjects = \App\Models\Project::where('isFeatured', true)->take(5)->get();
    if ($featuredProjects->count() > 0) {
        $slides = $featuredProjects->map(function (\App\Models\Project $p, $index) {
            return [
                'id' => $index + 1,
                'image' => $p->heroImage ? (\Illuminate\Support\Str::startsWith($p->heroImage, '/') ? $p->heroImage : \Illuminate\Support\Facades\Storage::url($p->heroImage)) : '/images/projects/Thumbnail-1.jpg',
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

    <!-- Slides -->
    <template x-for="(slide, index) in slides" :key="index">
        <div x-show="current === index" x-transition:enter="transition transform ease-out duration-700"
            x-transition:enter-start="opacity-0 scale-105" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition absolute transform ease-in duration-500 z-0"
            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-105"
            class="absolute inset-0 w-full h-full">

            <img :src="slide.image" :alt="slide.title" class="object-cover w-full h-full opacity-70" />
            <div class="absolute inset-0 bg-gradient-to-r from-titan-navy/60 via-titan-navy/30 to-transparent"></div>
        </div>
    </template>

    <!-- Content Overlay -->
    <div class="absolute inset-0 flex items-center z-10">
        <div class="max-w-[1400px] w-full mx-auto px-6 grid grid-cols-1 lg:grid-cols-2">

            <template x-for="(slide, index) in slides" :key="'content-'+index">
                <div x-show="current === index" x-transition:enter="transition ease-out duration-700 delay-300"
                    x-transition:enter-start="opacity-0 translate-y-8"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-300 absolute"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-4">

                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-12 h-1 bg-titan-red"></div>
                        <span class="text-titan-red font-bold tracking-[0.2em] uppercase text-sm"
                            x-text="slide.subtitle"></span>
                    </div>

                    <h1 class="font-heading font-black mb-6 text-5xl md:text-7xl leading-[1.1] tracking-tight"
                        x-text="slide.title"></h1>

                    <p class="text-white/80 max-w-lg mb-10 font-light text-lg md:text-xl leading-relaxed"
                        x-text="slide.desc"></p>

                    <div class="flex flex-wrap gap-4">
                        <a :href="slide.link"
                            class="group bg-titan-red text-white px-8 py-4 font-bold text-sm tracking-widest uppercase hover:bg-white hover:text-titan-red transition-all duration-300 flex items-center gap-3 rounded-none">
                            <span>{{ __('View Project') }}</span>
                            <x-lucide-arrow-right
                                class="group-hover:translate-x-1 transition-transform w-[18px] h-[18px]" />
                        </a>
                        <a href="/contact"
                            class="group border-2 border-white text-white px-8 py-4 font-bold text-sm tracking-widest uppercase hover:bg-titan-red hover:border-titan-red hover:text-white transition-all duration-300 flex items-center gap-3 rounded-none">
                            <x-lucide-phone class="w-[18px] h-[18px]" />
                            <span>{{ __('Contact Us') }}</span>
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

    <!-- Decorative Stats -->
    <div class="hidden lg:block absolute bottom-12 right-[20%] z-10">
        <div class="flex gap-12">
            <div>
                <div class="text-3xl font-black text-white">25+</div>
                <div class="text-[10px] text-titan-red uppercase tracking-widest font-bold">{{ __('Years Exp') }}
                </div>
            </div>
            <div>
                <div class="text-3xl font-black text-white">150+</div>
                <div class="text-[10px] text-titan-red uppercase tracking-widest font-bold">{{ __('Projects') }}</div>
            </div>
        </div>
    </div>
</header>