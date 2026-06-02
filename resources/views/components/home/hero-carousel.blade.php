@php
    $fallbackImage = '/images/projects/Thumbnail-5.jpg';
    $contentLocale = app()->getLocale() === 'kh' ? 'km' : app()->getLocale();
    $featuredProjects = \App\Models\Project::where('isFeatured', true)
        ->where('isActive', true)
        ->take(5)
        ->get();
    if ($featuredProjects->count() > 0) {
        $slides = $featuredProjects->map(function (\App\Models\Project $p, $index) use ($fallbackImage, $contentLocale) {
            return [
                'id' => $index + 1,
                'image' => ($p->heroImage && (\Illuminate\Support\Str::startsWith($p->heroImage, '/') ? file_exists(public_path($p->heroImage)) : \App\Support\PublicStorage::exists($p->heroImage)))
                    ? (\Illuminate\Support\Str::startsWith($p->heroImage, '/') ? $p->heroImage : \App\Support\PublicStorage::url($p->heroImage))
                    : $fallbackImage,
                'subtitle' => $p->projectCategory ? $p->projectCategory->getTranslation('name', $contentLocale) : ($p->category ?: __('Featured Project')),
                'title' => $p->getTranslation('title', $contentLocale) ?: $p->getTranslation('title', 'en'),
                'desc' => \Illuminate\Support\Str::limit(strip_tags($p->getTranslation('description', $contentLocale) ?: $p->getTranslation('description', 'en')), 120),
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
        <div x-show="current === index" x-transition:enter="transition-all transform ease-out duration-[1200ms]"
            x-transition:enter-start="opacity-0 scale-[1.04]" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition-all absolute transform ease-in duration-[900ms] z-0"
            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-[1.02]"
            class="absolute inset-0 w-full h-full">

            <div class="relative w-full h-full overflow-hidden">
                <img :src="slide.image" :alt="slide.title" class="object-cover w-full h-full opacity-100 animate-slow-zoom" />
                {{-- Stronger left-side scrim keeps hero copy readable over bright project photos. --}}
                <div class="absolute inset-0 bg-gradient-to-r from-titan-navy/70 via-titan-navy/32 to-transparent"></div>
                <div class="absolute inset-0 bg-gradient-to-b from-titan-navy/10 via-transparent to-titan-navy/42"></div>
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_18%_50%,rgba(11,43,92,0.26)_0%,rgba(11,43,92,0.10)_38%,transparent_65%)]"></div>
            </div>
        </div>
    </template>

    <!-- === CONTENT OVERLAY === -->
    <div class="absolute inset-0 flex flex-col justify-center z-10 pt-32 lg:pt-10">
        <div class="max-w-[1400px] w-full mx-auto px-6 grid grid-cols-1 lg:grid-cols-2">

            <template x-for="(slide, index) in slides" :key="'content-'+index">
                <div x-show="current === index" 
                    class="w-full"
                    x-transition:enter="transition-all transform ease-out duration-[1200ms] delay-300"
                    x-transition:enter-start="opacity-0 translate-y-6"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition-all transform ease-in duration-700 absolute"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 translate-y-4">



                    <h1 class="hero-copy-shadow font-heading font-[900] mb-7 !text-white uppercase leading-[1.02] tracking-normal"
                        :class="slide.title.length > 48
                            ? 'max-w-[820px] text-[2rem] md:text-[2.65rem] xl:text-[3.05rem]'
                            : 'max-w-[900px] text-[2.35rem] md:text-[3.15rem] xl:text-[3.75rem]'"
                        x-text="slide.title"></h1>

                    <p class="hero-copy-shadow text-[#F8FAFC] max-w-[650px] mb-10 font-medium text-base lg:text-lg leading-relaxed"
                        x-text="slide.desc"></p>

                    <div class="flex flex-wrap gap-8">
                        <a :href="slide.link"
                            class="group relative overflow-hidden bg-titan-red text-white px-12 py-6 font-black transition-all duration-500 flex items-center gap-6 shadow-2xl rounded {{ app()->getLocale() === 'km' ? 'font-khmer text-lg tracking-normal' : 'text-[13px] tracking-[0.25em] uppercase hover:bg-white hover:text-titan-navy' }}">
                            <span class="relative z-10">{{ __('VIEW PROJECT') }}</span>
                            <x-lucide-arrow-right class="group-hover:translate-x-2 transition-transform w-6 h-6 relative z-10" />
                        </a>
                        <a href="/contact"
                            class="group border-2 border-white/20 backdrop-blur-md text-white px-12 py-6 font-black transition-all duration-500 flex items-center gap-6 rounded {{ app()->getLocale() === 'km' ? 'font-khmer text-lg tracking-normal' : 'text-[13px] tracking-[0.25em] uppercase hover:bg-white hover:text-titan-navy hover:border-white' }}">
                            <x-lucide-phone class="w-6 h-6 group-hover:rotate-12 transition-transform" />
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
