@php
    /** @var \Illuminate\Database\Eloquent\Collection|\App\Models\Project[] $projectsDb */
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
                'image' => ($p->heroImage && (\Illuminate\Support\Str::startsWith($p->heroImage, '/') ? file_exists(public_path($p->heroImage)) : \App\Support\PublicStorage::exists($p->heroImage)))
                    ? (\Illuminate\Support\Str::startsWith($p->heroImage, '/') ? $p->heroImage : \App\Support\PublicStorage::url($p->heroImage))
                    : $fallbackImage,
                'type' => $p->projectCategory ? $p->projectCategory->localizedName($locale) : ($p->category ?: __('Infrastructure')),
                'title' => $p->getTranslation('title', $locale),
                'location' => $p->getTranslation('location', $locale),
                'status' => strtoupper($p->status->value ?? $p->status ?? 'COMPLETED'),
            ];
        })->toArray();
    });

    // Fallback if no projects in DB
    if (empty($projects)) {
        $projects = [
            ['slug' => 'mef', 'image' => '/images/webp/projects/Thumbnail-1.webp', 'type' => __('Government'), 'title' => __('Ministry of Economy Building'), 'location' => __('Phnom Penh'), 'status' => __('COMPLETED')],
            ['slug' => 'water', 'image' => '/images/webp/projects/Thumbnail-2.webp', 'type' => __('Infrastructure'), 'title' => __('Water Treatment Plant'), 'location' => __('Siem Reap'), 'status' => __('COMPLETED')],
            ['slug' => 'bank', 'image' => '/images/webp/projects/Thumbnail-3.webp', 'type' => __('Commercial'), 'title' => __('Commercial Bank HQ'), 'location' => __('Phnom Penh'), 'status' => __('ONGOING')],
        ];
    }
@endphp

<section class="py-24 bg-gray-50">
    <div class="max-w-[1400px] mx-auto px-6">
        <div x-data="{ shown: false }" x-intersect.once="shown = true"
            :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
            class="flex flex-row justify-center items-baseline gap-3 md:gap-6 mb-10 md:mb-16 text-center transition-all duration-1000">
            <div class="min-w-0 flex items-baseline gap-2">
                <span
                    class="text-titan-red font-bold uppercase tracking-widest text-[8px] sm:text-[10px] md:text-sm block whitespace-nowrap">{{ __('Our Portfolio') }}</span>
                <h2 class="text-base sm:text-2xl md:text-4xl font-heading font-black text-titan-navy leading-tight whitespace-nowrap">{{ __('Featured Projects') }}</h2>
            </div>
            <a href="/projects"
                class="inline-flex items-center gap-1 md:gap-2 text-titan-red font-bold uppercase tracking-widest text-[8px] sm:text-[10px] md:text-sm hover:text-titan-navy transition-colors whitespace-nowrap shrink-0">
                {{ __('View All Projects') }} <x-lucide-arrow-right class="w-4 h-4" />
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($projects as $index => $p)
                <div x-data="{ shown: false }" x-intersect.once="shown = true"
                    style="transition-delay: {{ $index * 100 }}ms"
                    :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                    class="transition-all duration-1000">
                    <a href="/projects/{{ $p['slug'] }}" class="group block h-full">
                        <div class="relative overflow-hidden rounded shadow-lg h-80 w-full bg-titan-navy">
                            <img src="{{ $p['image'] }}" alt="{{ $p['title'] }}"
                                class="object-cover w-full h-full group-hover:scale-110 transition-transform duration-700" loading="lazy" decoding="async" />
                            <div
                                class="absolute inset-0 bg-gradient-to-t from-titan-navy via-titan-navy/20 to-transparent z-10">
                            </div>
                            <div class="absolute top-4 left-4 z-20">
                                <span
                                    class="bg-titan-red text-white text-xs font-bold uppercase tracking-widest px-3 py-1 rounded">
                                    {{ $p['type'] }}
                                </span>
                            </div>
                            <div class="absolute bottom-0 left-0 right-0 p-6 z-20">
                                <h3
                                    class="!text-white text-2xl font-heading font-bold mb-2 group-hover:!text-titan-red transition-colors">
                                    {{ $p['title'] }}
                                </h3>
                                <div class="flex items-center gap-4 text-white/60 text-sm">
                                    <span class="flex items-center gap-1"><x-lucide-map-pin class="w-3.5 h-3.5" />
                                        {{ $p['location'] }}</span>
                                    <span class="flex items-center gap-1"><x-lucide-check-circle-2 class="w-3.5 h-3.5" />
                                        {{ $p['status'] }}</span>
                                </div>
                            </div>
                            <div
                                class="absolute top-4 right-4 w-10 h-10 bg-white rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all transform translate-x-4 group-hover:translate-x-0 z-20">
                                <x-lucide-arrow-right class="text-titan-navy w-[18px] h-[18px]" />
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>
