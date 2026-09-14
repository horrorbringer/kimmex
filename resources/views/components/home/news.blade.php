@props(['allNews' => null])

@php
    $allNews = $allNews ?? app(\App\Services\HomePageService::class)->getNews();
@endphp

<section class="py-12 md:py-16 bg-gray-50">
    <div class="max-w-[1200px] mx-auto px-6">
        <div x-data="{ shown: false }" x-intersect.once="shown = true"
            :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
            class="flex flex-wrap justify-center items-center gap-4 md:gap-6 mb-10 md:mb-16 text-center transition-all duration-700 ease-out motion-reduce:transition-none">
            <div class="flex items-center gap-3 sm:gap-4">
                {{-- Category pill badge --}}
                <span class="inline-flex items-center gap-1.5 bg-titan-red text-white text-[9px] sm:text-[10px] font-black uppercase tracking-[0.15em] px-3 py-1 rounded-full shrink-0 shadow-sm shadow-titan-red/30">
                    <span class="hidden sm:inline-block w-1 h-1 rounded-full bg-white/70"></span>
                    {{ __('News & Updates') }}
                </span>
                <h2 class="!text-lg sm:!text-2xl md:!text-3xl font-heading font-black text-titan-navy leading-tight whitespace-nowrap">{{ __('Latest Insights') }}</h2>
            </div>
            <a href="/news"
                class="group inline-flex items-center gap-2 text-titan-red font-bold uppercase tracking-wider text-[9px] sm:text-[10px] border border-titan-red/40 hover:border-titan-red hover:bg-titan-red hover:!text-white px-3 sm:px-4 py-1.5 rounded-full transition-all duration-300 whitespace-nowrap shrink-0">
                {{ __('View All News') }} <x-lucide-arrow-right class="w-3 h-3 sm:w-3.5 sm:h-3.5 group-hover:translate-x-0.5 transition-transform duration-300 ease-out motion-reduce:transform-none" />
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($allNews as $index => $news)
                <div x-data="{ shown: false }" x-intersect.once="shown = true"
                    style="transition-delay: {{ $index * 100 }}ms"
                    :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                    class="transition-all duration-700 ease-out motion-reduce:transition-none">
                    <a href="/news/{{ $news['id'] }}"
                        class="group cursor-pointer bg-white rounded overflow-hidden shadow-sm hover:shadow-xl transition-shadow duration-500 ease-out h-full flex flex-col">
                        <div class="aspect-[16/10] relative overflow-hidden bg-titan-navy">
                            <div class="absolute top-4 left-4 z-10 inline-flex items-center gap-1.5 bg-titan-navy/80 backdrop-blur-sm text-white text-[9px] font-black uppercase tracking-[0.15em] px-3 py-1 rounded-full border border-white/15">
                                <span class="w-1 h-1 rounded-full bg-white/60 shrink-0"></span>
                                {{ $news['category'] }}
                            </div>
                            <img src="{{ $news['image'] }}" alt="{{ $news['title'] }}"
                                class="object-cover w-full h-full group-hover:scale-[1.03] transition-transform duration-700 ease-out motion-reduce:transform-none" loading="lazy" decoding="async" />
                        </div>
                        <div class="p-6 flex flex-col flex-grow">
                            <div
                                class="text-xs font-bold uppercase tracking-widest text-titan-navy/40 mb-3 flex items-center gap-2">
                                <x-lucide-calendar class="w-3.5 h-3.5" /> {{ $news['date'] }}
                            </div>
                            <h3
                                class="!text-xl font-heading font-bold text-titan-navy group-hover:text-accent-orange transition-colors duration-300 ease-out leading-tight mb-4">
                                {{ $news['title'] }}
                            </h3>
                            <span class="text-sm font-bold text-accent-orange flex items-center gap-2 mt-auto">
                                {{ __('Read Story') }} <x-lucide-arrow-right class="w-3.5 h-3.5 transition-transform duration-300 ease-out group-hover:translate-x-1 motion-reduce:transform-none" />
                            </span>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>
