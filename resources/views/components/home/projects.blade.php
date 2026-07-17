@php
    $fallbackImage = '/images/webp/projects/Thumbnail-5.webp';
    $locale = app()->getLocale();
    $projects = \Illuminate\Support\Facades\Cache::remember('home_projects_array_'.$locale, now()->addHours(12), function() use ($fallbackImage, $locale) {
        $projectsDb = \App\Models\Project::where('isActive', true)
            ->with('projectCategory')
            ->orderBy('isFeatured', 'desc')
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        return $projectsDb->map(function ($p) use ($fallbackImage, $locale) {
            return [
                'slug' => $p->slug,
                'image' => \App\Support\PublicStorage::urlIfExists($p->heroImage, $fallbackImage),
                'type' => $p->projectCategory ? $p->projectCategory->localizedName($locale) : ($p->category ?: __('Infrastructure')),
                'title' => $p->getTranslation('title', $locale),
                'location' => $p->getTranslation('location', $locale),
                'status' => strtoupper($p->status->value ?? $p->status ?? 'COMPLETED'),
            ];
        })->toArray();
    });

    if (empty($projects)) {
        $projects = [
            ['slug' => 'mef', 'image' => '/images/webp/projects/Thumbnail-1.webp', 'type' => __('Government'), 'title' => __('Ministry of Economy Building'), 'location' => __('Phnom Penh'), 'status' => __('COMPLETED')],
            ['slug' => 'water', 'image' => '/images/webp/projects/Thumbnail-2.webp', 'type' => __('Infrastructure'), 'title' => __('Water Treatment Plant'), 'location' => __('Siem Reap'), 'status' => __('COMPLETED')],
            ['slug' => 'bank', 'image' => '/images/webp/projects/Thumbnail-3.webp', 'type' => __('Commercial'), 'title' => __('Commercial Bank HQ'), 'location' => __('Phnom Penh'), 'status' => __('ONGOING')],
        ];
    }
@endphp

<section class="py-12 md:py-16 bg-gray-50">
    <div class="max-w-[1280px] mx-auto px-6">

        {{-- Header --}}
        <div x-data="{ shown: false }" x-intersect.once="shown = true"
            :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
            class="flex flex-nowrap items-center justify-between gap-3 md:gap-6 mb-12 transition-all duration-1000">
            <div class="flex flex-nowrap items-center gap-3 md:gap-5 min-w-0">
                <div class="flex items-center gap-3">
                    <div class="hidden sm:block w-10 h-[2px]" style="background: var(--primary-color, #E31E24);"></div>
                    <span class="font-bold uppercase tracking-[0.12em] sm:tracking-[0.2em] text-[10px] sm:text-xs whitespace-nowrap" style="color: var(--primary-color, #E31E24);">{{ __('Our Portfolio') }}</span>
                </div>
                <h2 class="text-xl sm:text-3xl md:text-4xl font-heading font-black text-gray-900 tracking-tight whitespace-nowrap">
                    {{ __('Featured Projects') }}
                </h2>
            </div>
            <a href="/projects"
                class="inline-flex shrink-0 items-center gap-1 sm:gap-2 font-bold uppercase tracking-[0.08em] sm:tracking-wider text-[10px] sm:text-xs whitespace-nowrap group transition-colors"
                style="color: var(--primary-color, #E31E24);">
                {{ __('View All Projects') }}
                <x-lucide-arrow-right class="w-3.5 h-3.5 sm:w-4 sm:h-4 group-hover:translate-x-1 transition-transform" />
            </a>
        </div>

        {{-- Projects Grid: 1 large + 2 smaller --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            {{-- Featured (first project - large) --}}
            @if(isset($projects[0]))
                <div x-data="{ shown: false }" x-intersect.once="shown = true"
                    :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                    class="transition-all duration-1000 lg:row-span-2">
                    <a href="/projects/{{ $projects[0]['slug'] }}" class="group block h-full">
                        <div class="relative overflow-hidden rounded-2xl h-full min-h-[400px] lg:min-h-full" style="background: #0B2B5C;">
                            <img src="{{ $projects[0]['image'] }}" alt="{{ $projects[0]['title'] }}"
                                class="object-cover w-full h-full absolute inset-0 group-hover:scale-105 transition-transform duration-700" loading="lazy" decoding="async" />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent z-10"></div>
                            <div class="absolute top-5 left-5 z-20">
                                <span class="text-white text-[10px] font-bold uppercase tracking-widest px-3 py-1.5 rounded-md" style="background: var(--primary-color, #E31E24);">
                                    {{ $projects[0]['type'] }}
                                </span>
                            </div>
                            <div class="absolute bottom-0 left-0 right-0 p-7 md:p-9 z-20">
                                <h3 class="text-2xl md:text-3xl font-heading font-black mb-3 leading-tight" style="color: #FFFFFF;">
                                    {{ $projects[0]['title'] }}
                                </h3>
                                <div class="flex items-center gap-4 text-sm" style="color: rgba(255,255,255,0.6);">
                                    <span class="flex items-center gap-1.5">
                                        <x-lucide-map-pin class="w-3.5 h-3.5" />
                                        {{ $projects[0]['location'] }}
                                    </span>
                                    <span class="flex items-center gap-1.5">
                                        <x-lucide-check-circle-2 class="w-3.5 h-3.5" />
                                        {{ $projects[0]['status'] }}
                                    </span>
                                </div>
                            </div>
                            <div class="absolute top-5 right-5 w-10 h-10 bg-white rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all transform translate-x-4 group-hover:translate-x-0 z-20">
                                <x-lucide-arrow-right class="w-4 h-4 text-gray-900" />
                            </div>
                        </div>
                    </a>
                </div>
            @endif

            {{-- Side projects (2nd and 3rd) --}}
            @foreach(array_slice($projects, 1, 2) as $index => $p)
                <div x-data="{ shown: false }" x-intersect.once="shown = true"
                    style="transition-delay: {{ ($index + 1) * 100 }}ms"
                    :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                    class="transition-all duration-1000">
                    <a href="/projects/{{ $p['slug'] }}" class="group block h-full">
                        <div class="relative overflow-hidden rounded-2xl h-full min-h-[240px]" style="background: #0B2B5C;">
                            <img src="{{ $p['image'] }}" alt="{{ $p['title'] }}"
                                class="object-cover w-full h-full absolute inset-0 group-hover:scale-105 transition-transform duration-700" loading="lazy" decoding="async" />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/20 to-transparent z-10"></div>
                            <div class="absolute top-4 left-4 z-20">
                                <span class="text-white text-[10px] font-bold uppercase tracking-widest px-2.5 py-1 rounded-md" style="background: var(--primary-color, #E31E24);">
                                    {{ $p['type'] }}
                                </span>
                            </div>
                            <div class="absolute bottom-0 left-0 right-0 p-6 z-20">
                                <h3 class="text-xl font-heading font-bold mb-2 leading-tight" style="color: #FFFFFF;">
                                    {{ $p['title'] }}
                                </h3>
                                <div class="flex items-center gap-3 text-xs" style="color: rgba(255,255,255,0.55);">
                                    <span class="flex items-center gap-1">
                                        <x-lucide-map-pin class="w-3 h-3" />
                                        {{ $p['location'] }}
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <x-lucide-check-circle-2 class="w-3 h-3" />
                                        {{ $p['status'] }}
                                    </span>
                                </div>
                            </div>
                            <div class="absolute top-4 right-4 w-9 h-9 bg-white rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all transform translate-x-3 group-hover:translate-x-0 z-20">
                                <x-lucide-arrow-right class="w-3.5 h-3.5 text-gray-900" />
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>
