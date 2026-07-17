@php
    $lang = app()->getLocale() === 'km' ? 'kh' : app()->getLocale();
    $services = \Illuminate\Support\Facades\Cache::remember('home_services_array_'.app()->getLocale(), now()->addHours(12), function() use ($lang) {
        $servicesDb = \App\Models\Service::where('isActive', true)->orderBy('orderIndex')->limit(4)->get();
        return $servicesDb->map(function (\App\Models\Service $s) use ($lang) {
            $features = is_array($s->features) ? $s->features : [];
            $mappedFeatures = array_map(function ($f) use ($lang) {
                return is_array($f) ? ($f[$lang] ?? $f['en'] ?? '') : $f;
            }, $features);
            return [
                'icon' => $s->icon ?: 'lucide-hammer',
                'title' => $s->getTranslation('title', app()->getLocale()),
                'desc' => $s->summary ?: \Illuminate\Support\Str::limit(strip_tags($s->description), 120),
                'features' => array_slice($mappedFeatures, 0, 3),
                'slug' => $s->slug
            ];
        })->toArray();
    });

    if (empty($services)) {
        $services = [
            ['icon' => 'lucide-pen-tool', 'title' => __('Design & Build'), 'desc' => __('End-to-end management from blueprints to final walkthrough.'), 'features' => [__('Architectural Planning'), __('3D Modeling'), __('Turnkey Solutions')], 'slug' => 'design-and-build'],
            ['icon' => 'lucide-hammer', 'title' => __('Construction'), 'desc' => __('Premium civil construction services specializing in robust concrete work.'), 'features' => [__('High-Rise Buildings'), __('Commercial Spaces'), __('Quality Assurance')], 'slug' => 'construction'],
            ['icon' => 'lucide-settings', 'title' => __('MEP Systems'), 'desc' => __('Mechanical, Electrical, and Plumbing systems for modern infrastructure.'), 'features' => [__('HVAC Installations'), __('Electrical Grids'), __('Smart Building')], 'slug' => 'mep-systems'],
            ['icon' => 'lucide-truck', 'title' => __('Infrastructure'), 'desc' => __('Roadways, bridges, and public utilities designed to last.'), 'features' => [__('Slope Protection'), __('Water Treatment'), __('Road Paving')], 'slug' => 'infrastructure'],
        ];
    }
@endphp

<section class="py-12 md:py-16 bg-gray-50">
    <div class="max-w-[1280px] mx-auto px-6">

        {{-- Header --}}
        <div x-data="{ shown: false }" x-intersect.once="shown = true"
            :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
            class="flex flex-nowrap items-center justify-between gap-3 md:gap-6 mb-14 transition-all duration-1000">
            <div class="flex flex-nowrap items-center gap-3 md:gap-5 min-w-0">
                <div class="flex items-center gap-3">
                    <div class="hidden sm:block w-10 h-[2px]" style="background: var(--primary-color, #E31E24);"></div>
                    <span class="font-bold uppercase tracking-[0.12em] sm:tracking-[0.2em] text-[10px] sm:text-xs whitespace-nowrap" style="color: var(--primary-color, #E31E24);">{{ __('Our Services') }}</span>
                </div>
                <h2 class="text-xl sm:text-3xl md:text-4xl font-heading font-black text-gray-900 tracking-tight whitespace-nowrap">
                    {{ __('What We Do Best') }}
                </h2>
            </div>
            <a href="/services"
                class="inline-flex shrink-0 items-center gap-1 sm:gap-2 font-bold uppercase tracking-[0.08em] sm:tracking-wider text-[10px] sm:text-xs whitespace-nowrap group transition-colors"
                style="color: var(--primary-color, #E31E24);">
                {{ __('All Services') }}
                <x-lucide-arrow-right class="w-3.5 h-3.5 sm:w-4 sm:h-4 group-hover:translate-x-1 transition-transform" />
            </a>
        </div>

        {{-- Services Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 lg:gap-6">
            @foreach($services as $index => $s)
                <div x-data="{ shown: false }" x-intersect.once="shown = true"
                    style="transition-delay: {{ $index * 80 }}ms"
                    :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                    class="transition-all duration-700">
                    <a href="/services/{{ $s['slug'] }}"
                        class="group block h-full bg-white rounded-xl border border-gray-100 overflow-hidden hover:shadow-[0_20px_50px_-12px_rgba(0,0,0,0.1)] hover:-translate-y-1 transition-all duration-500">
                        <div class="p-7 md:p-9">
                            {{-- Icon + Number --}}
                            <div class="flex items-center justify-between mb-6">
                                <div class="w-12 h-12 rounded-xl flex items-center justify-center transition-all duration-300 group-hover:shadow-md"
                                     style="background: color-mix(in srgb, var(--primary-color, #E31E24) 10%, transparent);">
                                    <x-dynamic-component :component="$s['icon']" class="w-5 h-5 transition-colors" style="color: var(--primary-color, #E31E24);" stroke-width="1.8" />
                                </div>
                                <span class="text-4xl font-black text-gray-100 group-hover:text-gray-200 transition-colors select-none">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                            </div>

                            {{-- Title --}}
                            <h3 class="text-xl font-heading font-black text-gray-900 group-hover:text-titan-red transition-colors mb-3 {{ app()->getLocale() === 'km' ? 'font-khmer' : 'tracking-tight' }}">
                                {{ $s['title'] }}
                            </h3>

                            {{-- Description --}}
                            <p class="text-gray-500 text-sm leading-relaxed mb-6">
                                {{ $s['desc'] }}
                            </p>

                            {{-- Features --}}
                            <div class="flex flex-wrap gap-2 mb-6">
                                @foreach($s['features'] as $f)
                                    @if(!empty($f))
                                    <span class="text-[11px] font-semibold px-2.5 py-1 rounded-md bg-gray-50 text-gray-500 border border-gray-100 group-hover:border-gray-200 transition-colors">{{ $f }}</span>
                                    @endif
                                @endforeach
                            </div>

                            {{-- Arrow link --}}
                            <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider transition-all duration-300 group-hover:gap-3"
                                 style="color: var(--primary-color, #E31E24);">
                                {{ __('Learn More') }}
                                <x-lucide-arrow-right class="w-3.5 h-3.5" />
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>
