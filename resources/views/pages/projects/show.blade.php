@php
    // Use the $slug passed from the router to fetch the project
    $projectDb = \App\Models\Project::where('slug', $slug)->first();

    if ($projectDb) {
        $project = [
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
            'heroImage' => $projectDb->heroImage
                ? (\Illuminate\Support\Str::startsWith($projectDb->heroImage, '/') ? $projectDb->heroImage : \Illuminate\Support\Facades\Storage::url($projectDb->heroImage))
                : '/images/projects/Thumbnail-1.jpg',

            'narrative' => [
                'background' => $projectDb->getTranslation('background', app()->getLocale()) ?: $projectDb->getTranslation('description', app()->getLocale()),
                'objectives' => $projectDb->getTranslation('objectives', app()->getLocale()) ?: '',
                'design_concept' => $projectDb->getTranslation('designConcept', app()->getLocale()) ?: ''
            ],

            'scope' => is_array($projectDb->scopeContributions) ? $projectDb->scopeContributions : [
                __('General Contracting'),
                __('Structural Engineering'),
                __('MEP Systems Integration')
            ],

            'challenges' => is_array($projectDb->challenges) && count($projectDb->challenges) > 0 ? $projectDb->challenges : [
                [
                    'challenge' => __('High-density urban site constraints.'),
                    'solution' => __('Implemented a just-in-time logistics system for material delivery.')
                ]
            ],

            'images' => $projectDb->images->map(fn($img) => \Illuminate\Support\Str::startsWith($img->url, '/') ? $img->url : \Illuminate\Support\Facades\Storage::url($img->url))->toArray(),
            'related' => \App\Models\Project::where('id', '!=', $projectDb->id)->where('status', $projectDb->status)->take(3)->get()->map(fn(\App\Models\Project $p) => [
                'id' => $p->slug,
                'title' => $p->getTranslation('title', app()->getLocale()),
                'type' => $p->category ?: __('Infrastructure'),
                'image' => $p->heroImage ? (\Illuminate\Support\Str::startsWith($p->heroImage, '/') ? $p->heroImage : \Illuminate\Support\Facades\Storage::url($p->heroImage)) : '/images/projects/Thumbnail-5.jpg'
            ])->toArray()
        ];

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

    <div class="bg-white min-h-screen text-titan-navy relative">
        <!-- --- HERO SECTION --- -->
        <section class="relative h-[70vh] bg-titan-navy flex items-end">
            <div class="absolute inset-0">
                <img src="{{ $project['heroImage'] }}" alt="{{ $project['title'] }}"
                    class="w-full h-full object-cover opacity-70" />
                <div class="absolute inset-0 bg-gradient-to-t from-titan-navy via-titan-navy/20 to-transparent"></div>
            </div>

            <div class="relative z-10 w-full max-w-[1400px] mx-auto px-6 pb-20">
                <a href="/projects"
                    class="inline-flex items-center gap-2 text-white/70 hover:text-white transition-colors font-bold uppercase tracking-widest text-xs mb-8">
                    <x-lucide-arrow-left class="w-3.5 h-3.5" /> {{ __('Back to Portfolio') }}
                </a>
                <div x-data="{ show: false }" x-init="setTimeout(() => show = true, 100)"
                    :class="show ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                    class="transition-all duration-1000 transform">
                    <span
                        class="bg-titan-red text-white px-4 py-1 rounded-sm text-xs font-bold uppercase tracking-widest mb-4 inline-block">
                        {{ $project['type'] }}
                    </span>
                    <h1 class="text-5xl md:text-7xl font-black text-white mb-4 tracking-tight leading-none">
                        {{ $project['title'] }}
                    </h1>
                    <p class="text-xl md:text-2xl text-white/80 font-light flex items-center gap-3">
                        <x-lucide-map-pin class="w-5 h-5 text-titan-red" /> {{ $project['location'] }}
                    </p>
                </div>
            </div>
        </section>

        <!-- --- MAIN CONTENT SPLIT --- -->
        <section class="py-24 px-6 max-w-[1400px] mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-16">

                <!-- LEFT: CONTENT -->
                <div class="lg:col-span-8">
                    <!-- Description -->
                    <div class="mb-16">
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
                                <div>
                                    <h3 class="text-titan-navy font-bold text-sm uppercase tracking-widest mb-2">
                                        {{ __('Objectives') }}
                                    </h3>
                                    <div class="prose prose-sm xl:prose-base max-w-none text-titan-navy/70">
                                        {!! $project['narrative']['objectives'] !!}
                                    </div>
                                </div>
                            @endif
                            @if(!empty($project['narrative']['design_concept']))
                                <div>
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
                    @if(count($project['scope']) > 0)
                        <div class="mb-16 bg-gray-50 p-10 rounded-xl border border-titan-navy/10">
                            <h2 class="text-2xl font-black text-titan-navy mb-8 flex items-center gap-3">
                                <x-lucide-activity class="w-6 h-6 text-titan-red" /> {{ __('Scope of Work') }}
                            </h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($project['scope'] as $s)
                                    <div
                                        class="flex items-center gap-3 p-4 bg-white rounded-lg shadow-sm border border-transparent hover:border-titan-red/20 transition-all">
                                        <x-lucide-check-circle-2 class="w-5 h-5 text-titan-red flex-shrink-0" />
                                        <span class="font-bold text-titan-navy">{{ $s }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Challenges -->
                    @if(!empty($project['challenges']) && count($project['challenges']) > 0)
                        <div>
                            <h2 class="text-2xl font-black text-titan-navy mb-8 flex items-center gap-3">
                                <x-lucide-alert-triangle class="w-6 h-6 text-titan-red" />
                                {{ __('Key Challenges & Solutions') }}
                            </h2>
                            <ul class="space-y-6">
                                @foreach($project['challenges'] as $index => $c)
                                    @if(is_string($c))
                                        <li class="flex gap-4">
                                            <div
                                                class="w-8 h-8 rounded-full bg-titan-navy/5 flex items-center justify-center shrink-0 font-bold text-titan-navy text-sm">
                                                {{ $index + 1 }}
                                            </div>
                                            <div class="pt-1">
                                                <p class="text-titan-navy/70 leading-relaxed">{{ $c }}</p>
                                            </div>
                                        </li>
                                    @elseif(is_array($c))
                                        <li class="flex gap-4">
                                            <div
                                                class="w-8 h-8 rounded-full bg-titan-navy/5 flex items-center justify-center shrink-0 font-bold text-titan-navy text-sm">
                                                {{ $index + 1 }}
                                            </div>
                                            <div class="pt-1">
                                                @if(isset($c['challenge']))
                                                    <p class="font-bold text-titan-navy mb-1">{{ $c['challenge'] }}</p>
                                                @endif
                                                @if(isset($c['solution']))
                                                    <p class="text-titan-navy/70 leading-relaxed">{{ $c['solution'] }}</p>
                                                @endif
                                            </div>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>

                <!-- RIGHT: KEY FACTS SIDEBAR -->
                <div class="lg:col-span-4">
                    <div class="bg-white p-8 rounded-xl shadow-2xl border border-gray-100 sticky top-32">
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
                                    {{ $project['year'] }} <span
                                        class="text-xs px-2 py-1 rounded text-white {{ $project['status'] === __('Completed') || $project['status'] === 'Completed' || strtolower($project['status']) === 'completed' ? 'bg-green-600' : 'bg-orange-500' }}">{{ $project['status'] }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-10 pt-8 border-t border-gray-100" x-data="{
                            copied: false,
                            copyLink() {
                                navigator.clipboard.writeText(window.location.href);
                                this.copied = true;
                                setTimeout(() => this.copied = false, 2000);
                            },
                            shareNative() {
                                if (navigator.share) {
                                    navigator.share({
                                        title: '{{ $project['title'] }}',
                                        url: window.location.href
                                    });
                                }
                            }
                        }">
                            <div
                                class="text-[10px] font-black text-titan-navy/30 uppercase tracking-[0.3em] mb-5 text-center">
                                {{ __('Share via Network') }}</div>
                            <div class="flex items-center justify-between gap-3">
                                <!-- Facebook -->
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}"
                                    target="_blank"
                                    class="w-12 h-12 rounded-xl bg-gray-50 flex items-center justify-center text-titan-navy hover:bg-[#1877F2] hover:text-white transition-all shadow-sm group">
                                    <x-lucide-facebook class="w-5 h-5" />
                                </a>
                                <!-- LinkedIn -->
                                <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(url()->current()) }}"
                                    target="_blank"
                                    class="w-12 h-12 rounded-xl bg-gray-50 flex items-center justify-center text-titan-navy hover:bg-[#0A66C2] hover:text-white transition-all shadow-sm group">
                                    <x-lucide-linkedin class="w-5 h-5" />
                                </a>
                                <!-- Telegram -->
                                <a href="https://t.me/share/url?url={{ urlencode(url()->current()) }}&text={{ urlencode($project['title']) }}"
                                    target="_blank"
                                    class="w-12 h-12 rounded-xl bg-gray-50 flex items-center justify-center text-titan-navy hover:bg-[#229ED9] hover:text-white transition-all shadow-sm group">
                                    <x-lucide-send class="w-5 h-5" />
                                </a>
                                <!-- Copy Link -->
                                <button @click="copyLink()"
                                    class="flex-1 h-12 rounded-xl bg-titan-navy text-white flex items-center justify-center gap-2 hover:bg-titan-red transition-all shadow-lg text-[10px] font-black uppercase tracking-widest relative overflow-hidden">
                                    <span x-show="!copied" class="flex items-center gap-2">
                                        <x-lucide-link class="w-4 h-4" /> {{ __('Copy Link') }}
                                    </span>
                                    <span x-show="copied" x-cloak class="flex items-center gap-2 text-white">
                                        <x-lucide-check class="w-4 h-4" /> {{ __('Copied!') }}
                                    </span>
                                </button>
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
                                        class="absolute inset-0 w-full h-full object-cover {{ !($i === 2 && $count > 3) ? 'group-hover:scale-110' : '' }} transition-transform duration-700" />

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
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                                <div
                                    class="absolute top-4 left-4 bg-titan-navy text-white text-[10px] font-bold uppercase tracking-widest px-3 py-1 rounded-sm">
                                    {{ $p['type'] }}
                                </div>
                            </div>
                            <h3
                                class="text-xl font-bold text-titan-navy group-hover:text-titan-red transition-colors line-clamp-1">
                                {{ $p['title'] }}
                            </h3>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

    </div>

</x-layouts.app>