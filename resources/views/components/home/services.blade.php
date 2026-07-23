@php
    $lang = app()->getLocale() === 'km' ? 'kh' : app()->getLocale();
    $services = \Illuminate\Support\Facades\Cache::remember('home_services_array_'.app()->getLocale(), now()->addHours(12), function() use ($lang) {
        $servicesDb = \App\Models\Service::where('isActive', true)->orderBy('orderIndex')->get();
        return $servicesDb->map(function (\App\Models\Service $s) use ($lang) {
            $features = is_array($s->features) ? $s->features : [];
            $mappedFeatures = array_map(function ($f) use ($lang) {
                return is_array($f) ? ($f[$lang] ?? $f['en'] ?? '') : $f;
            }, $features);
            return [
                'title' => $s->getTranslation('title', app()->getLocale()),
                'features' => array_values(array_filter($mappedFeatures)),
                'slug' => $s->slug
            ];
        })->toArray();
    });

    if (empty($services)) {
        $services = [
            ['title' => __('Design & Build'), 'features' => [__('Architectural Planning'), __('3D Modeling'), __('Turnkey Solutions')], 'slug' => 'design-and-build'],
            ['title' => __('Construction'), 'features' => [__('High-Rise Buildings'), __('Commercial Spaces'), __('Quality Assurance')], 'slug' => 'construction'],
            ['title' => __('MEP Systems'), 'features' => [__('HVAC Installations'), __('Electrical Grids'), __('Smart Building')], 'slug' => 'mep-systems'],
            ['title' => __('Infrastructure'), 'features' => [__('Slope Protection'), __('Water Treatment'), __('Road Paving')], 'slug' => 'infrastructure'],
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
                @php($isLastOddService = count($services) % 2 === 1 && $index === count($services) - 1)
                <div x-data="{ shown: false }" x-intersect.once="shown = true"
                    style="transition-delay: {{ $index * 100 }}ms"
                    :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-6'"
                    @class([
                        'transition-all duration-700 ease-out motion-reduce:transition-none',
                        'md:col-span-2 md:w-[calc(50%-0.5rem)] md:justify-self-center' => $isLastOddService,
                    ])>
                    <a href="/services/{{ $s['slug'] }}"
                        class="group relative block h-full overflow-hidden rounded-2xl border border-titan-navy/10 bg-[#F7F9FC] p-6 transition-[border-color,box-shadow] duration-500 ease-out hover:border-titan-red/30 hover:shadow-[0_18px_45px_-22px_rgba(11,43,92,0.28)] md:p-8">
                        <span class="absolute left-0 top-0 h-full w-1 bg-titan-red/0 transition-colors duration-300 group-hover:bg-titan-red"></span>
                        <div>
                            <h3 class="mb-6 font-heading text-2xl font-black tracking-tight text-titan-navy transition-colors duration-300 group-hover:text-titan-red {{ app()->getLocale() === 'km' ? 'font-khmer text-3xl' : '' }}">
                                {{ $s['title'] }}
                            </h3>
                            <div class="grid gap-3 border-t border-titan-navy/8 pt-5 sm:grid-cols-2">
                                @foreach($s['features'] as $f)
                                    <span class="flex items-center gap-2 rounded-lg bg-white px-3 py-2 text-sm font-semibold text-titan-navy/65 ring-1 ring-titan-navy/8">
                                        <x-lucide-check class="h-4 w-4 shrink-0 text-titan-red" />
                                        {{ $f }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>
