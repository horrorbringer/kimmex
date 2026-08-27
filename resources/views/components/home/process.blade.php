@props(['processes' => null])

@php
    $processes = $processes ?? app(\App\Services\HomePageService::class)->getProcess();
@endphp

<section class="bg-white py-12 md:py-16">
    <div class="max-w-[1280px] mx-auto px-6">
        <div x-data="{ shown: false }" x-intersect.once="shown = true"
            :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
            class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 md:gap-6 border-b border-titan-navy/10 pb-8 md:pb-10 transition-all duration-700 ease-out motion-reduce:transition-none">
            <div class="flex items-center gap-2 sm:gap-3 md:gap-4 min-w-0 shrink-0">
                <span class="hidden sm:block h-[2px] w-6 sm:w-8 md:w-10 bg-titan-red shrink-0"></span>
                <span class="text-[9px] sm:text-xs font-bold uppercase tracking-wider sm:tracking-widest text-titan-red whitespace-nowrap shrink-0">
                    {{ __('Our Process') }}
                </span>
                <span class="text-gray-300 hidden sm:inline">&bull;</span>
                <h2 class="!text-lg sm:!text-2xl md:!text-3xl font-heading font-black text-titan-navy tracking-tight whitespace-nowrap">
                    {{ __('How We Deliver') }}
                </h2>
            </div>
            <p class="text-xs sm:text-sm md:text-base leading-relaxed text-titan-navy/60 lg:text-right max-w-xl">
                {{ __('A proven methodology that ensures quality, safety, and on-time delivery.') }}
            </p>
        </div>

        {{-- Process Steps Grid --}}
        <div class="mt-10 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:mt-12 lg:grid-cols-4 lg:gap-6">
            @foreach($processes as $index => $s)
                <article x-data="{ shown: false }" x-intersect.once="shown = true"
                    style="transition-delay: {{ $index * 100 }}ms"
                    :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-6'"
                    class="group relative flex flex-col justify-between rounded-2xl border border-titan-navy/10 bg-white p-6 sm:p-7 shadow-xs transition-all duration-300 ease-out hover:-translate-y-1 hover:border-titan-red/30 hover:bg-slate-50/60 hover:shadow-lg hover:shadow-titan-navy/5">
                    
                    {{-- Red Accent Line --}}
                    <span class="absolute top-0 left-6 h-[3px] w-8 rounded-full bg-titan-red transition-all duration-300 ease-out group-hover:w-16"></span>

                    <div>
                        {{-- Step Watermark & Icon --}}
                        <div class="mb-6 flex items-center justify-between">
                            <span class="font-heading text-4xl sm:text-5xl font-black leading-none text-titan-navy/15 transition-colors duration-300 group-hover:text-titan-red/30">
                                {{ $s['step'] }}
                            </span>
                            <div class="flex h-11 w-11 items-center justify-center rounded-xl border border-titan-navy/10 bg-titan-navy/[0.03] text-titan-red transition-all duration-300 ease-out group-hover:scale-105 group-hover:border-titan-red group-hover:bg-titan-red group-hover:!text-white group-hover:shadow-md group-hover:shadow-titan-red/20">
                                <x-dynamic-component :component="$s['icon']" class="h-5 w-5" stroke-width="1.8" />
                            </div>
                        </div>

                        {{-- Title --}}
                        <h3 class="mb-2.5 font-heading text-lg font-black tracking-tight text-titan-navy transition-colors duration-300 group-hover:text-titan-red {{ app()->getLocale() === 'km' ? 'font-khmer text-xl' : '' }}">
                            {{ $s['title'] }}
                        </h3>

                        {{-- Description --}}
                        <p class="text-xs sm:text-sm leading-relaxed text-titan-navy/65">
                            {{ $s['desc'] }}
                        </p>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
