@props(['aboutData' => null])

@php
    $aboutData = $aboutData ?? app(\App\Services\HomePageService::class)->getAboutData();
    $story = $aboutData['story'];
    $tagline = $aboutData['tagline'];
    $aboutLargeImage = $aboutData['aboutLargeImage'];
    $aboutTopImage = $aboutData['aboutTopImage'];
    $aboutBottomImage = $aboutData['aboutBottomImage'];
    $aboutLargeImageSrcset = $aboutData['aboutLargeImageSrcset'];
    $aboutTopImageSrcset = $aboutData['aboutTopImageSrcset'];
    $aboutBottomImageSrcset = $aboutData['aboutBottomImageSrcset'];
@endphp

<section class="py-12 md:py-16 bg-white overflow-hidden">
    <div class="max-w-[1280px] mx-auto px-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20 items-center">

            {{-- Left: Staggered Image Grid --}}
            <div x-data="{ shown: false }" x-intersect.once="shown = true"
                :class="shown ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-10'"
                class="transition-all duration-700 ease-out motion-reduce:transition-none relative">

                <div class="grid grid-cols-12 gap-3 md:gap-4">
                    {{-- Large left image --}}
                    <div class="col-span-7 row-span-2">
                        <div class="aspect-[3/4] rounded-2xl overflow-hidden shadow-xl">
                            <img src="{{ $aboutLargeImage }}" @if (filled($aboutLargeImageSrcset)) srcset="{{ $aboutLargeImageSrcset }}" @endif
                                sizes="(min-width: 1024px) 26vw, 58vw" width="810" height="1080" alt="{{ __('Construction project') }}"
                                class="w-full h-full object-cover hover:scale-[1.03] transition-transform duration-700 ease-out motion-reduce:transform-none" loading="lazy" decoding="async" />
                        </div>
                    </div>
                    {{-- Top right --}}
                    <div class="col-span-5">
                        <div class="aspect-[4/3] rounded-2xl overflow-hidden shadow-lg">
                            <img src="{{ $aboutTopImage }}" @if (filled($aboutTopImageSrcset)) srcset="{{ $aboutTopImageSrcset }}" @endif
                                sizes="(min-width: 1024px) 19vw, 42vw" width="1718" height="1291" alt="{{ __('Construction project') }}"
                                class="w-full h-full object-cover hover:scale-[1.03] transition-transform duration-700 ease-out motion-reduce:transform-none" loading="lazy" decoding="async" />
                        </div>
                    </div>
                    {{-- Bottom right --}}
                    <div class="col-span-5 relative">
                        <div class="aspect-[4/3] rounded-2xl overflow-hidden shadow-lg relative">
                            <img src="{{ $aboutBottomImage }}" @if (filled($aboutBottomImageSrcset)) srcset="{{ $aboutBottomImageSrcset }}" @endif
                                sizes="(min-width: 1024px) 19vw, 42vw" width="1434" height="1080" alt="{{ __('Construction project') }}"
                                class="w-full h-full object-cover hover:scale-[1.03] transition-transform duration-700 ease-out motion-reduce:transform-none" loading="lazy" decoding="async" />
                        </div>

                        {{-- Floating experience badge (Compact, anchored directly to bottom-right image) --}}
                        <div class="absolute -bottom-3 -right-2 md:-bottom-3 md:right-0 z-20 bg-white rounded-xl shadow-md px-3 py-2 flex items-center gap-2.5">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0" style="background: linear-gradient(135deg, var(--primary-color, #E31E24) 0%, #B31419 100%); color: #fff;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/><path d="M4 22h16"/><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"/><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"/><path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"/></svg>
                            </div>
                            <div>
                                <div class="text-base font-black text-gray-900 leading-none tracking-tight">25+</div>
                                <div class="text-[9px] font-bold uppercase tracking-wider text-gray-400 mt-0.5 whitespace-nowrap">{{ __('Years Experience') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right: Text Content --}}
            <div x-data="{ shown: false }" x-intersect.once="shown = true"
                :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-3'"
                class="transition-all duration-700 ease-out delay-150 motion-reduce:transition-none">

                <div class="flex items-center gap-3 mb-5">
                    <div class="w-10 h-[2px]" style="background: var(--primary-color, #E31E24);"></div>
                    <span class="font-bold uppercase tracking-[0.2em] text-xs" style="color: var(--primary-color, #E31E24);">{{ __('About Us') }}</span>
                </div>

                <h2 class="!text-[13px] md:!text-xl font-heading font-black text-gray-900 leading-tight !mb-3 tracking-tight">
                    {{ $tagline }}
                </h2>

                <p class="text-gray-500 text-base md:text-lg leading-relaxed mb-8 whitespace-pre-line">
                    {{ $story }}
                </p>

                {{-- Key highlights --}}
                <div class="grid grid-cols-2 gap-4 mb-10">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 rounded-full" style="background: var(--primary-color, #E31E24);"></div>
                        <span class="text-sm font-semibold text-gray-700">{{ __('Quality Assurance') }}</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 rounded-full" style="background: var(--primary-color, #E31E24);"></div>
                        <span class="text-sm font-semibold text-gray-700">{{ __('150+ Projects') }}</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 rounded-full" style="background: var(--primary-color, #E31E24);"></div>
                        <span class="text-sm font-semibold text-gray-700">{{ __('500+ Staff') }}</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 rounded-full" style="background: var(--primary-color, #E31E24);"></div>
                        <span class="text-sm font-semibold text-gray-700">{{ __('Zero-Incident Policy') }}</span>
                    </div>
                </div>

                <a href="/about"
                    class="inline-flex items-center gap-3 font-bold uppercase tracking-wider text-sm group transition-[gap] duration-300 ease-out"
                    style="color: var(--primary-color, #E31E24);">
                    {{ __('Learn More About Us') }}
                    <x-lucide-arrow-right class="w-4 h-4 group-hover:translate-x-1.5 transition-transform duration-300 ease-out motion-reduce:transform-none" />
                </a>
            </div>
        </div>
    </div>
</section>
