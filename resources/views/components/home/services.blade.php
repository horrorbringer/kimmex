@php
    $lang = app()->getLocale() === 'km' ? 'kh' : app()->getLocale();
    $servicesDb = \App\Models\Service::where('isActive', true)->orderBy('orderIndex')->limit(4)->get();

    $services = $servicesDb->map(function (\App\Models\Service $s) use ($lang) {
        $features = is_array($s->features) ? $s->features : [];
        $mappedFeatures = array_map(function ($f) use ($lang) {
            return is_array($f) ? ($f[$lang] ?? $f['en'] ?? '') : $f;
        }, $features);

        return [
            'icon' => $s->icon ?: 'lucide-hammer',
            'title' => $s->getTranslation('title', app()->getLocale()),
            'desc' => $s->summary ?: \Illuminate\Support\Str::limit(strip_tags($s->description), 150),
            'features' => array_slice($mappedFeatures, 0, 3),
            'slug' => $s->slug
        ];
    })->toArray();

    // Fallback if empty
    if (empty($services)) {
        $services = [
            ['icon' => 'lucide-pen-tool', 'title' => __('Design & Build'), 'desc' => __('End-to-end management of your construction project from blueprints to final walkthrough.'), 'features' => [__('Architectural Planning'), __('3D Modeling'), __('Turnkey Solutions')], 'slug' => 'design-and-build'],
            ['icon' => 'lucide-hammer', 'title' => __('Construction'), 'desc' => __('Premium civil construction services across Cambodia specializing in robust concrete work.'), 'features' => [__('High-Rise Buildings'), __('Commercial Spaces'), __('Quality Assurance')], 'slug' => 'construction'],
            ['icon' => 'lucide-settings', 'title' => __('MEP Systems'), 'desc' => __('Integration of Mechanical, Electrical, and Plumbing systems for modern infrastructure.'), 'features' => [__('HVAC Installations'), __('Electrical Grids'), __('Smart Building')], 'slug' => 'mep-systems'],
            ['icon' => 'lucide-truck', 'title' => __('Infrastructure'), 'desc' => __('Roadways, bridges, and public utilities designed to withstand the test of time.'), 'features' => [__('Slope Protection'), __('Water Treatment'), __('Road Paving')], 'slug' => 'infrastructure'],
        ];
    }
@endphp

<section class="py-24 bg-white">
    <div class="max-w-[1400px] mx-auto px-6">
        <div x-data="{ shown: false }" x-intersect.once="shown = true"
            :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
            class="text-center max-w-2xl mx-auto mb-16 transition-all duration-1000">
            <span
                class="text-accent-orange font-bold uppercase tracking-widest text-sm mb-4 block">{{ __('Our Services') }}</span>
            <h2 class="text-4xl font-heading font-black text-titan-navy mb-4">
                {{ __('Comprehensive Construction Solutions') }}
            </h2>
            <p class="text-titan-navy/50 text-lg">{{ __('From design to completion') }}</p>
        </div>

        <div class="flex flex-wrap justify-center gap-0">
            @foreach($services as $index => $s)
                <div x-data="{ shown: false }" x-intersect.once="shown = true"
                    style="transition-delay: {{ $index * 100 }}ms"
                    :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                    class="w-full sm:w-1/2 lg:w-1/4 bg-white p-10 group hover:shadow-[0_20px_50px_rgba(0,0,0,0.08)] hover:-translate-y-2 transition-all duration-500 border border-gray-100 border-b-4 border-b-transparent hover:border-b-accent-orange h-full relative overflow-hidden rounded-xl">
                    <div
                        class="absolute top-0 right-0 p-6 opacity-5 group-hover:opacity-10 group-hover:scale-110 transition-all duration-700">
                        <x-dynamic-component :component="$s['icon']" class="text-titan-navy w-20 h-20" />
                    </div>
                    <div class="flex items-center justify-between mb-8">
                        <div
                            class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center group-hover:bg-accent-orange group-hover:shadow-lg group-hover:shadow-accent-orange/30 transition-all duration-300">
                            <x-dynamic-component :component="$s['icon']"
                                class="text-titan-navy group-hover:text-white transition-colors w-7 h-7" />
                        </div>
                    </div>
                    <h3
                        class="text-2xl font-heading font-black text-titan-navy group-hover:text-accent-orange transition-colors mb-4 uppercase tracking-tighter">
                        {{ $s['title'] }}
                    </h3>
                    <p class="text-titan-navy/60 mb-8 text-sm leading-relaxed">{{ $s['desc'] }}</p>
                    <ul class="space-y-3 pt-8 border-t border-gray-100">
                        @foreach($s['features'] as $f)
                            <li
                                class="flex items-center gap-3 text-xs font-bold uppercase tracking-widest text-titan-navy/40 group-hover:text-titan-navy/80 transition-colors">
                                <div
                                    class="w-1.5 h-1.5 bg-accent-orange rounded-full group-hover:scale-150 transition-transform">
                                </div> {{ $f }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>

        <div x-data="{ shown: false }" x-intersect.once="shown = true"
            :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
            class="text-center mt-12 transition-all duration-1000">
            <a href="/services"
                class="inline-flex items-center gap-2 bg-titan-navy text-white px-8 py-4 font-bold uppercase tracking-widest text-sm hover:bg-accent-orange transition-all rounded-lg">
                {{ __('View All Services') }} <x-lucide-arrow-right class="w-4 h-4" />
            </a>
        </div>
    </div>
</section>