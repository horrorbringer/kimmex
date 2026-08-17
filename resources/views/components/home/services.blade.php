@props(['services' => null])

@php
    $services = $services ?? app(\App\Services\HomePageService::class)->getServices();
@endphp

<section class="relative overflow-hidden bg-[#f8fafc] py-20 md:py-28">
    <div class="pointer-events-none absolute inset-x-0 top-0 h-72 bg-[radial-gradient(ellipse_at_top,rgba(227,30,36,0.1),transparent_68%)]"></div>
    <div class="relative mx-auto max-w-[1280px] px-5 sm:px-6">
        <div x-data="{ shown: false }" x-intersect.once="shown = true"
            :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
            class="mb-12 grid gap-6 border-b border-slate-200 pb-10 transition-all duration-700 ease-out motion-reduce:transition-none lg:mb-16 lg:grid-cols-[1.15fr_0.85fr] lg:items-end lg:gap-16">
            <div>
                <div class="mb-5 flex items-center gap-3">
                    <span class="h-px w-12 bg-titan-red"></span>
                    <span class="text-xs font-black uppercase tracking-[0.22em] text-titan-red">{{ __('Our Services') }}</span>
                </div>
                <h2 class="max-w-2xl text-balance font-heading text-3xl font-black tracking-tight text-titan-navy md:text-5xl">{{ __('What We Do Best') }}</h2>
            </div>
            <div class="lg:pb-1">
                <p class="mb-6 max-w-lg text-base leading-7 text-slate-600">{{ __('Integrated expertise for every stage of your project, from first concept to final delivery.') }}</p>
                <a href="/services" class="group inline-flex min-h-11 items-center gap-2 text-xs font-black uppercase tracking-[0.16em] text-titan-red transition-colors duration-200 hover:text-titan-navy focus-visible:rounded focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-titan-red focus-visible:ring-offset-4">
                    {{ __('Explore all services') }}
                    <x-lucide-arrow-right class="h-4 w-4 transition-transform duration-200 ease-out group-hover:translate-x-1 motion-reduce:transform-none" />
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-5 md:grid-cols-2 md:gap-6 lg:gap-7">
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
                        aria-label="{{ __('View :service service details', ['service' => $s['title']]) }}"
                        class="group relative flex h-full min-h-[220px] cursor-pointer flex-col overflow-hidden rounded-3xl border border-slate-200 bg-white/95 p-7 shadow-[0_14px_38px_-30px_rgba(15,23,42,0.5)] transition-[border-color,box-shadow] duration-300 ease-out hover:border-titan-red/45 hover:shadow-[0_26px_56px_-30px_rgba(15,23,42,0.4)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-titan-red focus-visible:ring-offset-4 focus-visible:ring-offset-slate-50 md:min-h-[250px] md:p-9">
                        <span class="absolute inset-x-0 top-0 h-1 bg-titan-red opacity-0 transition-opacity duration-300 group-hover:opacity-100 group-focus-visible:opacity-100"></span>
                        <span aria-hidden="true" class="pointer-events-none absolute right-7 top-6 font-heading text-5xl font-black leading-none tracking-tighter text-slate-100 transition-colors duration-300 group-hover:text-titan-red/10 md:right-9 md:top-8 md:text-6xl">
                            {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                        </span>
                        <div class="relative flex h-full flex-col">
                            <h3 class="mb-7 max-w-[80%] text-balance font-heading text-2xl font-black leading-tight tracking-tight text-titan-navy transition-colors duration-200 group-hover:text-titan-red {{ app()->getLocale() === 'km' ? 'font-khmer text-3xl' : '' }}">
                                {{ $s['title'] }}
                            </h3>
                            <ul class="mt-auto grid gap-2.5 border-t border-slate-200 pt-6 sm:grid-cols-2" role="list">
                                @foreach($s['features'] as $f)
                                    <li class="flex items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-3 text-sm font-semibold leading-snug text-slate-700 transition-colors duration-200 group-hover:border-titan-red/15 group-hover:bg-white">
                                        <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-titan-red/10 text-titan-red transition-colors duration-200 group-hover:bg-titan-red group-hover:text-white">
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
