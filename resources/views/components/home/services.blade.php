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

<section class="bg-white py-16 md:py-24">
    <div class="max-w-[1280px] mx-auto px-6">
        <div x-data="{ shown: false }" x-intersect.once="shown = true"
            :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
            class="mb-12 grid gap-6 border-b border-titan-navy/10 pb-10 transition-all duration-700 ease-out motion-reduce:transition-none lg:mb-14 lg:grid-cols-[1.15fr_0.85fr] lg:items-end lg:gap-16">
            <div>
                <div class="mb-5 flex items-center gap-3">
                    <span class="h-px w-10 bg-titan-red"></span>
                    <span class="text-xs font-bold uppercase tracking-[0.2em] text-titan-red">{{ __('Our Services') }}</span>
                </div>
                <h2 class="font-heading text-3xl font-black tracking-tight text-titan-navy md:text-5xl">{{ __('What We Do Best') }}</h2>
            </div>
            <div class="lg:pb-1">
                <p class="mb-5 max-w-lg text-base leading-relaxed text-titan-navy/60">{{ __('Integrated expertise for every stage of your project, from first concept to final delivery.') }}</p>
                <a href="/services" class="group inline-flex items-center gap-2 text-xs font-bold uppercase tracking-[0.16em] text-titan-red">
                    {{ __('Explore all services') }}
                    <x-lucide-arrow-right class="h-4 w-4 transition-transform duration-300 ease-out group-hover:translate-x-1 motion-reduce:transform-none" />
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:gap-5">
            @foreach($services as $index => $s)
                <div x-data="{ shown: false }" x-intersect.once="shown = true"
                    style="transition-delay: {{ $index * 100 }}ms"
                    :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-6'"
                    class="transition-all duration-700 ease-out motion-reduce:transition-none">
                    <a href="/services/{{ $s['slug'] }}"
                        class="group relative block h-full overflow-hidden rounded-2xl border border-titan-navy/10 bg-[#F7F9FC] p-6 transition-[border-color,box-shadow] duration-500 ease-out hover:border-titan-red/30 hover:shadow-[0_18px_45px_-22px_rgba(11,43,92,0.28)] md:p-8">
                        <span class="absolute left-0 top-0 h-full w-1 bg-titan-red/0 transition-colors duration-300 group-hover:bg-titan-red"></span>
                        <div class="flex items-start justify-between gap-5">
                            <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-white text-titan-red shadow-sm ring-1 ring-titan-navy/5 transition-[background-color,color,box-shadow] duration-300 group-hover:bg-titan-red group-hover:text-white group-hover:shadow-[0_10px_22px_-12px_rgba(227,30,36,0.75)]">
                                <x-dynamic-component :component="$s['icon']" class="h-6 w-6" stroke-width="1.8" />
                            </div>
                            <span class="font-heading text-5xl font-black leading-none text-titan-navy/10">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                        </div>

                        <div class="mt-8">
                            <h3 class="mb-3 font-heading text-2xl font-black tracking-tight text-titan-navy transition-colors duration-300 group-hover:text-titan-red {{ app()->getLocale() === 'km' ? 'font-khmer text-3xl' : '' }}">
                                {{ $s['title'] }}
                            </h3>
                            <p class="mb-7 text-sm leading-relaxed text-titan-navy/60">
                                {{ $s['desc'] }}
                            </p>

                            <div class="mb-7 flex flex-wrap gap-2 border-t border-titan-navy/8 pt-5">
                                @foreach($s['features'] as $f)
                                    @if(!empty($f))
                                        <span class="rounded-full bg-white px-3 py-1 text-[11px] font-semibold text-titan-navy/55 ring-1 ring-titan-navy/8">{{ $f }}</span>
                                    @endif
                                @endforeach
                            </div>

                            <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-[0.14em] text-titan-red">
                                {{ __('Learn More') }}
                                <x-lucide-arrow-right class="h-3.5 w-3.5 transition-transform duration-300 ease-out group-hover:translate-x-1 motion-reduce:transform-none" />
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>
