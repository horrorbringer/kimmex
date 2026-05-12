@php
    $locale = app()->getLocale();
    // Use the $slug passed from the router to fetch the project
    $project = \Illuminate\Support\Facades\Cache::remember("project_show_data_{$slug}_{$locale}", now()->addHours(12), function() use ($slug, $locale) {
        $projectDb = \App\Models\Project::where('isActive', true)->where('slug', $slug)->first();
        if (!$projectDb) return null;

        return [
            'id' => $projectDb->slug,
            'title' => $projectDb->getTranslation('title', app()->getLocale()),
            'type' => $projectDb->projectCategory ? $projectDb->projectCategory->getTranslation('name', app()->getLocale()) : ($projectDb->category ?: __('Infrastructure')),
            'location' => $projectDb->getTranslation('location', app()->getLocale()),
            'status' => $projectDb->status?->getLabel() ?: __('Completed'),
            'date' => $projectDb->completionDate?->format('F Y') ?: __('Oct 2026'),
            'client' => $projectDb->client ?: __('Ministry of Economy and Finance'),
            'built_area' => $projectDb->scale ?: __('50,000 SQM'),
            'contract_value' => __('Contact for Details'),
            'year' => $projectDb->timeline ?: __('2023 - 2026'),
            'heroImage' => ($projectDb->heroImage && (\Illuminate\Support\Str::startsWith($projectDb->heroImage, '/') ? file_exists(public_path($projectDb->heroImage)) : \Illuminate\Support\Facades\Storage::disk('public')->exists($projectDb->heroImage)))
                ? (\Illuminate\Support\Str::startsWith($projectDb->heroImage, '/') ? $projectDb->heroImage : \Illuminate\Support\Facades\Storage::url($projectDb->heroImage))
                : null,

            'narrative' => [
                'background' => $projectDb->getTranslation('background', app()->getLocale()) ?: $projectDb->getTranslation('description', app()->getLocale()),
                'objectives' => $projectDb->getTranslation('objectives', app()->getLocale()) ?: '',
                'design_concept' => $projectDb->getTranslation('designConcept', app()->getLocale()) ?: '',
                'engineering_narrative' => $projectDb->getTranslation('engineeringNarrative', app()->getLocale()) ?: ''
            ],

            'scope' => $projectDb->getTranslation('scopeContributions', app()->getLocale()),

            'images' => $projectDb->images->map(fn($img) => \Illuminate\Support\Str::startsWith($img->url, '/') ? $img->url : \Illuminate\Support\Facades\Storage::url($img->url))->toArray(),
            'related' => \App\Models\Project::where('isActive', true)->where('id', '!=', $projectDb->id)->where('status', $projectDb->status)->take(3)->get()->map(fn(\App\Models\Project $p) => [
                'id' => $p->slug,
                'title' => $p->getTranslation('title', app()->getLocale()),
                'type' => $p->category ?: __('Infrastructure'),
                'image' => $p->heroImage ? (\Illuminate\Support\Str::startsWith($p->heroImage, '/') ? $p->heroImage : \Illuminate\Support\Facades\Storage::url($p->heroImage)) : '/images/projects/Thumbnail-5.jpg'
            ])->toArray()
        ];
    });

    if ($project) {

        // Ensure at least 4 images so you can see the layout and "Load More" functionality
        if (count($project['images']) < 4) {
            $fallbacks = [
                '/images/projects/Thumbnail-2.jpg',
                '/images/projects/Thumbnail-3.jpg',
                '/images/projects/Thumbnail-4.jpg',
                '/images/projects/Thumbnail-5.jpg'
            ];
            foreach ($fallbacks as $fallback) {
                if (count($project['images']) >= 4)
                    break;
                $project['images'][] = $fallback;
            }
        }
    } else {
        // Keep internal fallback for development if DB is empty
        $project = [
            'id' => $slug,
            'title' => __('Ministry of Economy & Finance Building Expansion'),
            'type' => __('Government Office Building'),
            'location' => __('Phnom Penh, Cambodia'),
            'status' => __('Completed'),
            'date' => __('Oct 2026'),
            'client' => __('MEF'),
            'built_area' => __('50,000 SQM'),
            'contract_value' => __('$120.5M'),
            'year' => __('2023 - 2026'),
            'heroImage' => '/images/projects/Thumbnail-1.jpg',
            'narrative' => [
                'background' => __('A definitive case study on administrative centralization and public infrastructure integration for the Royal Government of Cambodia.'),
                'objectives' => __('To deliver a state-of-the-art office complex with Grade A specifications, ensuring maximum energy efficiency and seamless integration of governmental systems.'),
                'design_concept' => __('The architectural design focuses on a "Solid Foundation" theme, utilizing heavy reinforced concrete with a glass facade that symbolizes transparency and strength.')
            ],
            'scope' => [__('General Contracting'), __('Structural Engineering'), __('MEP Systems Integration'), __('Interior Fit-out')],
            'challenges' => [['challenge' => __('Strict government security protocols.'), 'solution' => __('Developed a specialized vetting and access control system.')]],
            'images' => ['/images/projects/Thumbnail-2.jpg', '/images/projects/Thumbnail-3.jpg', '/images/projects/Thumbnail-4.jpg'],
            'related' => [
                ['id' => '1', 'title' => __('National Bank HQ'), 'type' => __('Government'), 'image' => '/images/projects/Thumbnail-5.jpg'],
                ['id' => '2', 'title' => __('Khleang Toeuk WTP'), 'type' => __('Infrastructure'), 'image' => '/images/projects/Thumbnail-2.jpg'],
                ['id' => '3', 'title' => __('Mekong River Bank'), 'type' => __('Infrastructure'), 'image' => '/images/projects/Thumbnail-3.jpg']
            ]
        ];
    }
@endphp

<x-layouts.app :title="$project['title'] . ' | Portfolio'" :description="'Kimmex project showcase: ' . $project['title']">

    <div class="bg-white min-h-screen text-titan-navy font-sans antialiased" x-data="{ 
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
        <div class="fixed top-0 left-0 w-full z-[100] transition-transform duration-500"
            :class="scrolled ? 'translate-y-0' : '-translate-y-full'">
            <div class="h-1 bg-gray-100 w-full relative">
                <div class="h-full bg-titan-red absolute left-0 top-0 transition-all duration-150"
                    :style="'width: ' + progress + '%'"></div>
            </div>
            <div class="bg-white/95 backdrop-blur-md border-b border-gray-100 h-12 flex items-center px-6">
                <div class="max-w-[1400px] mx-auto w-full flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <span
                            class="text-[9px] font-black text-titan-red uppercase tracking-widest hidden md:block">{{ __('Project:') }}</span>
                        <span
                            class="text-[11px] font-black text-titan-navy truncate max-w-[200px] md:max-w-md uppercase tracking-tight">{{ $project['title'] }}</span>
                    </div>
                    <div class="flex items-center gap-6">
                        <a href="/projects"
                            class="w-8 h-8 bg-titan-navy text-white rounded-lg flex items-center justify-center hover:bg-titan-red transition-all"><x-lucide-arrow-left
                                class="w-4 h-4" /></a>
                    </div>
                </div>
            </div>
        </div>

        <!-- --- PREMIUM NARRATIVE HERO --- -->
        <header class="relative w-full h-[75vh] min-h-[600px] overflow-hidden bg-titan-navy flex items-center justify-center">
            @if($project['heroImage'])
                <img src="{{ $project['heroImage'] }}" alt="{{ $project['title'] }}"
                    class="absolute inset-0 w-full h-full object-cover opacity-100 animate-slow-zoom" />
            @else
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_30%_20%,var(--color-kmd-navy-light)_0%,var(--color-kmd-navy)_100%)]"></div>
            @endif
            
            {{-- Deep multi-stage gradient for maximum text contrast --}}
            <div class="absolute inset-0 bg-gradient-to-b from-titan-navy/60 via-transparent to-titan-navy/90"></div>
            <div class="absolute inset-0 bg-black/30"></div>

            <div class="relative z-10 text-center max-w-6xl px-6" x-data="{ shown: false }"
                x-init="setTimeout(() => shown = true, 100)">

                
                <h1 :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10'"
                    class="transition-all duration-1000 delay-300 font-black text-white uppercase tracking-tighter leading-[0.9] mb-8"
                    style="font-size: clamp(2rem, 5vw, 3.5rem) !important; color: white !important;">
                    {{ $project['title'] }}
                </h1>
                
                <div :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-6'"
                    class="transition-all duration-700 delay-500 flex items-center justify-center gap-6 text-white font-bold uppercase tracking-[0.4em] text-xs md:text-sm">
                    <div class="h-[1px] w-12 bg-titan-red"></div>
                    <div class="flex items-center gap-3">
                        <x-lucide-map-pin class="w-4 h-4 text-titan-red" />
                        {{ $project['location'] }}
                    </div>
                    <div class="h-[1px] w-12 bg-titan-red"></div>
                </div>
            </div>

        </header>

        <!-- --- MAIN CONTENT SPLIT --- -->
        <section class="py-24 px-6 max-w-[1400px] mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-16">

                <!-- LEFT: CONTENT -->
                <div class="lg:col-span-8">
                    <!-- Description -->
                    <div class="mb-16 reveal-up">
                        <h2 class="text-2xl font-black text-titan-navy mb-8 flex items-center gap-3">
                            <x-lucide-help-circle class="w-6 h-6 text-titan-red" /> {{ __('Project Overview') }}
                        </h2>
                        <div class="space-y-8 text-lg text-titan-navy/70 leading-relaxed">
                            <div>
                                <h3 class="text-titan-navy font-bold text-sm uppercase tracking-widest mb-2">
                                    {{ __('The Background') }}
                                </h3>
                                <div class="prose prose-sm xl:prose-base max-w-none text-titan-navy/70">
                                    {!! $project['narrative']['background'] !!}
                                </div>
                            </div>
                            @if(!empty($project['narrative']['objectives']))
                                <div class="mt-8">
                                    <h3 class="text-titan-navy font-bold text-sm uppercase tracking-widest mb-2">
                                        {{ __('Objectives') }}
                                    </h3>
                                    <div class="prose prose-sm xl:prose-base max-w-none text-titan-navy/70">
                                        {!! $project['narrative']['objectives'] !!}
                                    </div>
                                </div>
                            @endif
                            @if(!empty($project['narrative']['design_concept']))
                                <div class="mt-8">
                                    <h3 class="text-titan-navy font-bold text-sm uppercase tracking-widest mb-2">
                                        {{ __('Design Concept') }}
                                    </h3>
                                    <div class="prose prose-sm xl:prose-base max-w-none text-titan-navy/70">
                                        {!! $project['narrative']['design_concept'] !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Scope -->
                    @if(!empty($project['scope']))
                        <div class="mb-16 bg-gray-50 p-10 rounded border border-titan-navy/10 reveal-up">
                            <h2 class="text-2xl font-black text-titan-navy mb-8 flex items-center gap-3">
                                <x-lucide-activity class="w-6 h-6 text-titan-red" /> {{ __('Scope of Work') }}
                            </h2>
                            <div class="prose prose-sm xl:prose-base max-w-none text-titan-navy/70 
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
                                    {!! $project['scope'] !!}
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Narrative -->
                    @if(!empty($project['narrative']['engineering_narrative']))
                        <div class="reveal-up">
                            <h2 class="text-2xl font-black text-titan-navy mb-8 flex items-center gap-3">
                                <x-lucide-alert-triangle class="w-6 h-6 text-titan-red" />
                                {{ __('Engineering Challenges & Solutions') }}
                            </h2>
                            <div class="prose prose-sm xl:prose-base max-w-none text-titan-navy/70">
                                {!! $project['narrative']['engineering_narrative'] !!}
                            </div>
                        </div>
                    @endif
                </div>

                <!-- RIGHT: KEY FACTS SIDEBAR -->
                <div class="lg:col-span-4">
                    <div class="bg-white p-8 rounded shadow-2xl border border-gray-100 sticky top-32">
                        <h3 class="text-xl font-black text-titan-navy mb-8 pb-4 border-b border-gray-100">
                            {{ __('Project Data') }}
                        </h3>

                        <div class="space-y-6">
                            <div class="group">
                                <span
                                    class="block text-xs font-bold text-titan-navy/70 uppercase tracking-widest mb-1 group-hover:text-titan-red transition-colors">{{ __('Client') }}</span>
                                <div class="flex items-center gap-3 font-bold text-titan-navy text-lg">
                                    <x-lucide-user
                                        class="w-5 h-5 text-gray-300 group-hover:text-titan-red transition-colors" />
                                    {{ $project['client'] }}
                                </div>
                            </div>

                            <div class="group">
                                <span
                                    class="block text-xs font-bold text-titan-navy/70 uppercase tracking-widest mb-1 group-hover:text-titan-red transition-colors">{{ __('Location') }}</span>
                                <div class="flex items-center gap-3 font-bold text-titan-navy text-lg">
                                    <x-lucide-map-pin
                                        class="w-5 h-5 text-gray-300 group-hover:text-titan-red transition-colors" />
                                    {{ $project['location'] }}
                                </div>
                            </div>

                            @if($project['built_area'])
                                <div class="group">
                                    <span
                                        class="block text-xs font-bold text-titan-navy/70 uppercase tracking-widest mb-1 group-hover:text-titan-red transition-colors">{{ __('Built Area') }}</span>
                                    <div class="flex items-center gap-3 font-bold text-titan-navy text-lg">
                                        <x-lucide-maximize
                                            class="w-5 h-5 text-gray-300 group-hover:text-titan-red transition-colors" />
                                        {{ $project['built_area'] }}
                                    </div>
                                </div>
                            @endif

                            <div class="group">
                                <span
                                    class="block text-xs font-bold text-titan-navy/70 uppercase tracking-widest mb-1 group-hover:text-titan-red transition-colors">{{ __('Year & Status') }}</span>
                                <div class="flex items-center gap-3 font-bold text-titan-navy text-lg">
                                    <x-lucide-calendar
                                        class="w-5 h-5 text-gray-300 group-hover:text-titan-red transition-colors" />
                                    {{ $project['year'] }} <span class="text-xs font-black text-titan-navy/40 ml-2 uppercase tracking-widest">{{ $project['status'] }}</span>                            </div>
                        </div>

                        <!-- CENTERED SOCIAL SHARING -->
                        <div class="reveal-up pt-12 border-t border-gray-100 mt-12 flex flex-col items-center gap-6">
                            <div class="text-[10px] font-black text-titan-navy/20 uppercase tracking-[0.4em]">{{ __('Share this Project') }}</div>
                            <div class="flex items-center gap-4">
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" rel="noopener"
                                    class="w-12 h-12 bg-social-facebook rounded flex items-center justify-center text-white hover:brightness-110 transition-all transform hover:-translate-y-1 shadow-lg group/fb">
                                    <x-lucide-facebook class="w-5 h-5 transition-transform group-hover/fb:scale-110" />
                                </a>
                                <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(url()->current()) }}" target="_blank" rel="noopener"
                                    class="w-12 h-12 bg-social-linkedin rounded flex items-center justify-center text-white hover:brightness-110 transition-all transform hover:-translate-y-1 shadow-lg group/li">
                                    <x-lucide-linkedin class="w-5 h-5 transition-transform group-hover/li:scale-110" />
                                </a>
                                <a href="https://t.me/share/url?url={{ urlencode(url()->current()) }}&text={{ urlencode($project['title']) }}" target="_blank" rel="noopener"
                                    class="w-12 h-12 bg-social-telegram rounded flex items-center justify-center text-white hover:brightness-110 transition-all transform hover:-translate-y-1 shadow-lg group/tg">
                                    <x-lucide-send class="w-5 h-5 transition-transform group-hover/tg:scale-110" />
                                </a>
                                <div x-data="{ 
                                    copied: false, 
                                    copyLink() {
                                        navigator.clipboard.writeText(window.location.href);
                                        this.copied = true;
                                        setTimeout(() => this.copied = false, 2000);
                                    }
                                }" class="relative">
                                    <button @click="copyLink()"
                                        class="w-12 h-12 bg-white border border-gray-100 rounded flex items-center justify-center text-titan-navy hover:bg-titan-navy hover:text-white transition-all transform hover:-translate-y-1 shadow-lg group/link">
                                        <x-lucide-link class="w-5 h-5" x-show="!copied" />
                                        <x-lucide-check class="w-5 h-5 text-green-500" x-show="copied" x-cloak />
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>

        <!-- --- GALLERY SECTION --- -->
        @if(count($project['images']) > 0)
            <section class="bg-titan-navy py-24 px-6 text-white"
                x-data="{ lightboxOpen: false, lightboxIndex: 0, images: {{ json_encode($project['images']) }} }">
                <div class="max-w-[1400px] mx-auto">
                    <h2 class="text-3xl font-black mb-12 border-l-4 border-titan-red pl-6">{{ __('Project Gallery') }}</h2>
                    <div class="grid grid-cols-1 md:grid-cols-6 gap-6">
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
                                    class="rounded-lg overflow-hidden group cursor-pointer relative w-full h-full {{ $gridClass }}">
                                    <img src="{{ $img }}" alt="Gallery {{ $i + 1 }}"
                                        class="absolute inset-0 w-full h-full object-cover {{ !($i === 2 && $count > 3) ? 'group-hover:scale-110' : '' }} transition-transform duration-700" loading="lazy" />

                                    @if($i === 2 && $count > 3)
                                        <div
                                            class="absolute inset-0 bg-titan-navy/80 hover:bg-titan-navy/90 transition-colors duration-500 flex flex-col items-center justify-center z-10">
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

                <!-- LIGHTBOX -->
                <div x-show="lightboxOpen" x-transition.opacity @keydown.escape.window="lightboxOpen = false"
                    @keydown.arrow-right.window="lightboxIndex = (lightboxIndex + 1) % images.length"
                    @keydown.arrow-left.window="lightboxIndex = (lightboxIndex - 1 + images.length) % images.length"
                    class="fixed inset-0 z-[9999] bg-black/95 backdrop-blur-xl flex items-center justify-center"
                    style="display: none;">

                    <button @click="lightboxOpen = false"
                        class="absolute top-6 right-6 z-50 w-12 h-12 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition-colors">
                        <x-lucide-x class="w-6 h-6 text-white" />
                    </button>

                    <button @click="lightboxIndex = (lightboxIndex - 1 + images.length) % images.length"
                        class="absolute left-6 z-50 w-14 h-14 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition-colors">
                        <x-lucide-chevron-left class="w-6 h-6 text-white" />
                    </button>

                    <button @click="lightboxIndex = (lightboxIndex + 1) % images.length"
                        class="absolute right-6 z-50 w-14 h-14 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition-colors">
                        <x-lucide-chevron-right class="w-6 h-6 text-white" />
                    </button>

                    <div class="max-w-7xl max-h-[85vh] px-24 py-12">
                        <img :src="images[lightboxIndex]"
                            class="max-w-full max-h-[85vh] object-contain rounded-lg shadow-2xl" />
                    </div>

                    <div
                        class="absolute bottom-6 left-1/2 -translate-x-1/2 bg-white/10 backdrop-blur-sm px-6 py-3 rounded-full border border-white/10">
                        <span class="text-white/80 text-sm font-bold">
                            <span x-text="lightboxIndex + 1"></span> / <span x-text="images.length"></span>
                        </span>
                    </div>
                </div>
            </section>
        @endif

        <!-- --- RELATED PROJECTS --- -->
        @if(count($project['related']) > 0)
            <section class="py-24 px-6 max-w-[1400px] mx-auto">
                <div class="flex justify-between items-end mb-12">
                    <h2 class="text-3xl font-black text-titan-navy">{{ __('Similar Projects') }}</h2>
                    <a href="/projects"
                        class="font-bold text-titan-red hover:underline flex items-center gap-2 text-sm uppercase tracking-widest">
                        {{ __('View All') }} <x-lucide-arrow-right class="w-4 h-4" />
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    @foreach($project['related'] as $p)
                        <a href="/projects/{{ $p['id'] }}" class="block group">
                            <div class="aspect-[4/3] rounded-lg overflow-hidden mb-4 relative">
                                <img src="{{ $p['image'] }}" alt="{{ $p['title'] }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy" />
                                <div
                                    class="absolute top-4 left-4 bg-titan-navy text-white text-[10px] font-bold uppercase tracking-widest px-3 py-1 rounded">
                                    {{ $p['type'] }}
                                {{ $p['title'] }}
                            </h3>
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

        @supports (view-timeline-name: --revealing) {
            .reveal-up {
                animation: revealUp both;
                animation-timeline: view();
                animation-range: entry 5% cover 25%;
            }
        }
    </style>

</x-layouts.app>