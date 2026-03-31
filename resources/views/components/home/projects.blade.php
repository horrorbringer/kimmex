@php
    $projectsDb = \App\Models\Project::orderBy('isFeatured', 'desc')
        ->orderBy('created_at', 'desc')
        ->take(3)
        ->get();

    $projects = $projectsDb->map(function ($p) {
        return [
            'slug' => $p->slug,
            'image' => $p->heroImage ? \Illuminate\Support\Facades\Storage::url($p->heroImage) : '/images/projects/Thumbnail-1.jpg',
            'type' => $p->projectCategory ? $p->projectCategory->getTranslation('name', app()->getLocale()) : ($p->category ?: __('Infrastructure')),
            'title' => $p->getTranslation('title', app()->getLocale()),
            'location' => $p->getTranslation('location', app()->getLocale()),
            'status' => strtoupper($p->status->value ?? $p->status ?? 'COMPLETED'),
        ];
    })->toArray();

    // Fallback if no projects in DB
    if (empty($projects)) {
        $projects = [
            ['slug' => 'mef', 'image' => '/images/projects/Thumbnail-1.jpg', 'type' => __('Government'), 'title' => __('Ministry of Economy Building'), 'location' => __('Phnom Penh'), 'status' => __('COMPLETED')],
            ['slug' => 'water', 'image' => '/images/projects/Thumbnail-2.jpg', 'type' => __('Infrastructure'), 'title' => __('Water Treatment Plant'), 'location' => __('Siem Reap'), 'status' => __('COMPLETED')],
            ['slug' => 'bank', 'image' => '/images/projects/Thumbnail-3.jpg', 'type' => __('Commercial'), 'title' => __('Commercial Bank HQ'), 'location' => __('Phnom Penh'), 'status' => __('ONGOING')],
        ];
    }
@endphp

<section class="py-24 bg-gray-50">
    <div class="max-w-[1400px] mx-auto px-6">
        <div x-data="{ shown: false }" x-intersect.once="shown = true"
            :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
            class="flex flex-col md:flex-row justify-between items-end mb-16 transition-all duration-1000">
            <div>
                <span
                    class="text-accent-orange font-bold uppercase tracking-widest text-sm mb-4 block">{{ __('Our Portfolio') }}</span>
                <h2 class="text-4xl font-heading font-black text-titan-navy">{{ __('Featured Projects') }}</h2>
            </div>
            <a href="/projects"
                class="mt-6 md:mt-0 inline-flex items-center gap-2 text-accent-orange font-bold uppercase tracking-widest text-sm hover:text-titan-navy transition-colors">
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
                        <div class="relative overflow-hidden rounded-2xl shadow-lg h-80 w-full">
                            <img src="{{ $p['image'] }}" alt="{{ $p['title'] }}"
                                class="object-cover w-full h-full group-hover:scale-110 transition-transform duration-700" />
                            <div
                                class="absolute inset-0 bg-gradient-to-t from-titan-navy via-titan-navy/20 to-transparent z-10">
                            </div>
                            <div class="absolute top-4 left-4 z-20">
                                <span
                                    class="bg-accent-orange text-white text-xs font-bold uppercase tracking-widest px-3 py-1 rounded">
                                    {{ $p['type'] }}
                                </span>
                            </div>
                            <div class="absolute bottom-0 left-0 right-0 p-6 z-20">
                                <h3
                                    class="text-white text-2xl font-heading font-bold mb-2 group-hover:text-accent-orange transition-colors">
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