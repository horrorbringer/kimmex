@php
    $lang = app()->getLocale() === 'km' ? 'kh' : app()->getLocale();
    $services = \Illuminate\Support\Facades\Cache::remember('home_services_array_v2_'.app()->getLocale(), now()->addHours(12), function() use ($lang) {
        $servicesDb = \App\Models\Service::where('isActive', true)->orderBy('orderIndex')->get();
        return $servicesDb->map(function (\App\Models\Service $s) use ($lang) {
            $features = is_array($s->features) ? $s->features : [];
            $mappedFeatures = array_map(function ($f) use ($lang) {
                if (! is_array($f)) {
                    return $f;
                }

                $feature = $f['name'] ?? $f[$lang] ?? $f['en'] ?? '';

                return is_array($feature) ? ($feature[app()->getLocale()] ?? $feature['en'] ?? '') : $feature;
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

<section class="relative overflow-hidden bg-slate-50 py-20 md:py-28">
    <div class="pointer-events-none absolute inset-x-0 top-0 h-56 bg-[radial-gradient(ellipse_at_top,rgba(227,30,36,0.08),transparent_70%)]"></div>
    <div class="relative mx-auto max-w-[1280px] px-5 sm:px-6">
        <div x-data="{ shown: false }" x-intersect.once="shown = true"
            :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
            class="mb-12 grid gap-6 border-b border-slate-200 pb-10 transition-all duration-700 ease-out motion-reduce:transition-none lg:mb-16 lg:grid-cols-[1.15fr_0.85fr] lg:items-end lg:gap-16">
            <div>
                <div class="mb-5 flex items-center gap-3">
                    <span class="h-px w-12 bg-titan-red"></span>
                    <span class="text-xs font-black uppercase tracking-[0.22em] text-titan-red">{{ __('Our Services') }}</span>
                </div>
                <h2 class="max-w-2xl font-heading text-3xl font-black tracking-tight text-titan-navy md:text-5xl">{{ __('What We Do Best') }}</h2>
            </div>
            <div class="lg:pb-1">
                <p class="mb-6 max-w-lg text-base leading-7 text-slate-600">{{ __('Integrated expertise for every stage of your project, from first concept to final delivery.') }}</p>
                <a href="/services" class="group inline-flex min-h-11 items-center gap-2 text-xs font-black uppercase tracking-[0.16em] text-titan-red transition-colors duration-200 hover:text-titan-navy focus-visible:rounded focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-titan-red focus-visible:ring-offset-4">
                    {{ __('Explore all services') }}
                    <x-lucide-arrow-right class="h-4 w-4 transition-transform duration-200 ease-out group-hover:translate-x-1 motion-reduce:transform-none" />
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-5 md:grid-cols-2 md:gap-6">
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
                        class="group relative block h-full cursor-pointer overflow-hidden rounded-3xl border border-slate-200 bg-white p-6 shadow-[0_12px_35px_-28px_rgba(15,23,42,0.45)] transition-[border-color,box-shadow] duration-300 ease-out hover:border-titan-red/40 hover:shadow-[0_22px_50px_-28px_rgba(15,23,42,0.32)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-titan-red focus-visible:ring-offset-4 md:p-8">
                        <span class="absolute inset-x-0 top-0 h-1 bg-titan-red opacity-0 transition-opacity duration-300 group-hover:opacity-100 group-focus-visible:opacity-100"></span>
                        <div>
                            <h3 class="mb-7 font-heading text-2xl font-black leading-tight tracking-tight text-titan-navy transition-colors duration-200 group-hover:text-titan-red {{ app()->getLocale() === 'km' ? 'font-khmer text-3xl' : '' }}">
                                {{ $s['title'] }}
                            </h3>
                            <ul class="grid gap-3 border-t border-slate-200 pt-6 sm:grid-cols-2" role="list">
                                @foreach($s['features'] as $f)
                                    <li class="flex items-center gap-3 rounded-xl bg-slate-50 px-3.5 py-3 text-sm font-semibold leading-snug text-slate-700 ring-1 ring-inset ring-slate-200 transition-colors duration-200 group-hover:bg-white group-hover:ring-titan-red/15">
                                        <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-titan-red/10 text-titan-red">
                                            <x-lucide-check class="h-3.5 w-3.5" stroke-width="2.5" />
                                        </span>
                                        {{ $f }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>
