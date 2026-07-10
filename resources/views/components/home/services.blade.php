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
                'desc' => $s->summary ?: \Illuminate\Support\Str::limit(strip_tags($s->description), 150),
                'features' => array_slice($mappedFeatures, 0, 3),
                'slug' => $s->slug
            ];
        })->toArray();
    });

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
    <div class="max-w-[1200px] mx-auto px-6">
        <div x-data="{ shown: false }" x-intersect.once="shown = true"
            :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
            class="max-w-3xl mx-auto mb-14 text-center transition-all duration-1000">
            <span class="text-titan-red font-bold uppercase tracking-widest text-sm mb-4 block">{{ __('Our Services') }}</span>
            <h2 class="text-2xl md:text-3xl font-heading font-black text-titan-navy mb-4">
                {{ __('Comprehensive Construction Solutions') }}
            </h2>
            <p class="text-titan-navy/60 text-base md:text-lg leading-relaxed">
                {{ __('We bring design, construction, MEP, and infrastructure delivery together under one accountable team.') }}
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6 lg:gap-8">
            @foreach($services as $index => $s)
                @php
                    $features = array_slice($s['features'] ?? [], 0, 3);
                @endphp
                <div x-data="{ shown: false }" x-intersect.once="shown = true"
                    style="transition-delay: {{ $index * 100 }}ms"
                    :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                    class="transition-all duration-1000">
                    <a href="/services/{{ $s['slug'] }}"
                        class="group block h-full rounded border border-gray-100 bg-white overflow-hidden shadow-sm hover:shadow-[0_20px_50px_rgba(0,0,0,0.08)] hover:-translate-y-1.5 transition-all duration-500">
                        <div class="p-8 md:p-10 min-h-[300px] relative overflow-hidden">
                            <div class="absolute top-0 right-0 p-6 opacity-5 group-hover:opacity-10 group-hover:scale-110 transition-all duration-700">
                                <x-dynamic-component :component="$s['icon']" class="text-titan-navy w-20 h-20" />
                            </div>

                            <div class="relative z-10 flex h-full flex-col">
                                <div class="flex items-center justify-between mb-8">
                                    <div class="w-14 h-14 rounded-full border border-gray-100 bg-gray-50 flex items-center justify-center group-hover:border-titan-red group-hover:bg-titan-red/5 transition-all duration-500">
                                        <x-dynamic-component :component="$s['icon']" class="text-titan-red transition-colors w-6 h-6" stroke-width="1.5" />
                                    </div>
                                </div>

                                <h3 class="text-2xl font-heading font-black text-titan-navy group-hover:text-titan-red transition-colors mb-4 {{ app()->getLocale() === 'km' ? 'font-khmer' : 'uppercase tracking-tighter' }}">
                                    {{ $s['title'] }}
                                </h3>
                                <p class="text-titan-navy/60 mb-6 text-sm leading-relaxed">
                                    {{ $s['desc'] }}
                                </p>

                                <ul class="space-y-3 pt-6 border-t border-gray-100 mt-auto">
                                    @foreach($features as $f)
                                        <li class="text-xs font-bold uppercase tracking-widest text-titan-navy/45 group-hover:text-titan-navy/80 transition-colors">
                                            {{ $f }}
                                        </li>
                                    @endforeach
                                </ul>

                                <div class="mt-6 inline-flex items-center gap-2 text-[10px] font-bold uppercase tracking-[0.18em] text-titan-red">
                                    {{ __('Learn More') }}
                                    <x-lucide-arrow-right class="w-4 h-4 group-hover:translate-x-1 transition-transform" />
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>

        <div x-data="{ shown: false }" x-intersect.once="shown = true"
            :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
            class="text-center mt-12 transition-all duration-1000">
            <a href="/services"
                class="inline-flex items-center gap-2 bg-titan-navy text-white px-8 py-4 font-bold uppercase tracking-widest text-sm hover:bg-titan-red transition-all rounded shadow-md hover:shadow-lg">
                {{ __('View All Services') }} <x-lucide-arrow-right class="w-4 h-4" />
            </a>
        </div>
    </div>
</section>
