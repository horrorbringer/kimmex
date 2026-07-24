@php
    $brandProfile = \App\Models\SystemSetting::get('brand_identity', []);
    $orgProfile = \App\Models\SystemSetting::get('organization_profile', []);
    $locale = app()->getLocale();
    $localeKey = $locale === 'kh' ? 'km' : $locale;
    $brand = $brandProfile[$localeKey] ?? ($brandProfile['en'] ?? []);
    $org = $orgProfile[$localeKey] ?? ($orgProfile['en'] ?? []);

    $story = $brand['company_story'] ?? __("With over 25 years of experience, we have established ourselves as Cambodia's most trusted construction partner, delivering projects that stand the test of time.");
    $tagline = $org['tagline'] ?? __("Cambodia's Premier Construction Partner");
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
                            <img src="/images/webp/projects/Thumbnail-2.webp" alt="{{ __('Construction project') }}"
                                class="w-full h-full object-cover hover:scale-[1.03] transition-transform duration-700 ease-out motion-reduce:transform-none" loading="lazy" decoding="async" />
                        </div>
                    </div>
                    {{-- Top right --}}
                    <div class="col-span-5">
                        <div class="aspect-[4/3] rounded-2xl overflow-hidden shadow-lg">
                            <img src="/images/webp/projects/Thumbnail-3.webp" alt="{{ __('Construction project') }}"
                                class="w-full h-full object-cover hover:scale-[1.03] transition-transform duration-700 ease-out motion-reduce:transform-none" loading="lazy" decoding="async" />
                        </div>
                    </div>
                    {{-- Bottom right --}}
                    <div class="col-span-5">
                        <div class="aspect-[4/3] rounded-2xl overflow-hidden shadow-lg relative">
                            <img src="/images/webp/projects/Thumbnail-4.webp" alt="{{ __('Construction project') }}"
                                class="w-full h-full object-cover hover:scale-[1.03] transition-transform duration-700 ease-out motion-reduce:transform-none" loading="lazy" decoding="async" />
                        </div>
                    </div>
                </div>

                {{-- Floating experience badge --}}
                <div class="absolute -bottom-4 -right-2 md:bottom-6 md:right-0 z-20 bg-white rounded-xl shadow-[0_10px_40px_-10px_rgba(0,0,0,0.15)] p-4 md:p-5 border border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-lg flex items-center justify-center" style="background: var(--primary-color, #E31E24); color: #fff;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/><path d="M4 22h16"/><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"/><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"/><path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"/></svg>
                        </div>
                        <div>
                            <div class="text-2xl font-black text-gray-900 leading-none">25+</div>
                            <div class="text-[10px] font-bold uppercase tracking-wider text-gray-400 mt-0.5">{{ __('Years Experience') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right: Text Content --}}
            <div x-data="{ shown: false }" x-intersect.once="shown = true"
                :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                class="transition-all duration-700 ease-out delay-150 motion-reduce:transition-none">

                <div class="flex items-center gap-3 mb-5">
                    <div class="w-10 h-[2px]" style="background: var(--primary-color, #E31E24);"></div>
                    <span class="font-bold uppercase tracking-[0.2em] text-xs" style="color: var(--primary-color, #E31E24);">{{ __('About Us') }}</span>
                </div>

                <h2 class="text-3xl md:text-4xl font-heading font-black text-gray-900 leading-tight mb-6 tracking-tight">
                    {{ $tagline }}
                </h2>

                <p class="text-gray-500 text-base md:text-lg leading-relaxed mb-8 whitespace-pre-line">
                    {{ $story }}
                </p>

                {{-- Key highlights --}}
                <div class="grid grid-cols-2 gap-4 mb-10">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 rounded-full" style="background: var(--primary-color, #E31E24);"></div>
                        <span class="text-sm font-semibold text-gray-700">{{ __('ISO 9001 Certified') }}</span>
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
