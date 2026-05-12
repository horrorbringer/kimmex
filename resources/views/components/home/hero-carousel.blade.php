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
        <div x-show="current === index" x-transition:enter="transition-all transform ease-out duration-[1200ms]"
            x-transition:enter-start="opacity-0 translate-x-12" x-transition:enter-end="opacity-100 translate-x-0"
            x-transition:leave="transition-all absolute transform ease-in duration-[1000ms] z-0"
            x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 -translate-x-12"
            class="absolute inset-0 w-full h-full">

            <template x-if="slide.image">
                <div class="relative w-full h-full overflow-hidden">
                    <img :src="slide.image" :alt="slide.title" class="object-cover w-full h-full opacity-100 animate-slow-zoom" />
                    {{-- Brighter multi-stage gradient for vibrant imagery --}}
                    <div class="absolute inset-0 bg-gradient-to-r from-titan-navy/60 via-titan-navy/20 to-transparent"></div>
                    <div class="absolute inset-0 bg-gradient-to-b from-titan-navy/20 via-transparent to-titan-navy/60"></div>
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
    <div class="absolute inset-0 flex flex-col justify-center z-10 pt-32 lg:pt-10">
        <div class="max-w-[1400px] w-full mx-auto px-6 grid grid-cols-1 lg:grid-cols-2">

            <template x-for="(slide, index) in slides" :key="'content-'+index">
                <div x-show="current === index" 
                    class="w-full"
                    x-transition:enter="transition-all transform ease-out duration-[1200ms] delay-300"
                    x-transition:enter-start="opacity-0 translate-x-24"
                    x-transition:enter-end="opacity-100 translate-x-0"
                    x-transition:leave="transition-all transform ease-in duration-700 absolute"
                    x-transition:leave-start="opacity-100 translate-x-0"
                    x-transition:leave-end="opacity-0 -translate-x-24">



                    <h1 class="font-heading font-[900] mb-8 text-white uppercase leading-[0.9] tracking-tighter max-w-[900px] drop-shadow-2xl"
                        style="color: white !important; font-weight: 900 !important; font-size: clamp(2rem, 5vw, 3.5rem) !important;"
                        x-text="slide.title"></h1>

                    <p class="text-white/90 max-w-[650px] mb-12 font-normal text-lg lg:text-xl leading-relaxed drop-shadow-lg opacity-80"
                        x-text="slide.desc"></p>

                    <div class="flex flex-wrap gap-8">
                        <a :href="slide.link"
                            class="group relative overflow-hidden bg-titan-red text-white px-12 py-6 font-black transition-all duration-500 flex items-center gap-6 shadow-2xl rounded-2xl {{ app()->getLocale() === 'km' ? 'font-khmer text-lg tracking-normal' : 'text-[13px] tracking-[0.25em] uppercase hover:bg-white hover:text-titan-navy' }}">
                            <span class="relative z-10">{{ __('VIEW PROJECT') }}</span>
                            <x-lucide-arrow-right class="group-hover:translate-x-2 transition-transform w-6 h-6 relative z-10" />
                        </a>
                        <a href="/contact"
                            class="group border-2 border-white/20 backdrop-blur-md text-white px-12 py-6 font-black transition-all duration-500 flex items-center gap-6 rounded-2xl {{ app()->getLocale() === 'km' ? 'font-khmer text-lg tracking-normal' : 'text-[13px] tracking-[0.25em] uppercase hover:bg-white hover:text-titan-navy hover:border-white' }}">
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