@php
    $brandProfile = \App\Models\SystemSetting::get('brand_identity', []);
    $orgProfile = \App\Models\SystemSetting::get('organization_profile', []);
    $locale = app()->getLocale();
    $localeKey = $locale === 'kh' ? 'km' : $locale;
    $brand = $brandProfile[$localeKey] ?? ($brandProfile['en'] ?? []);
    $org = $orgProfile[$localeKey] ?? ($orgProfile['en'] ?? []);

    $story = $brand['company_story'] ?? __("With over 25 years of experience, we have established ourselves as Cambodia's most trusted construction partner, delivering projects that stand the test of time.");
@endphp

<section class="py-24 bg-gray-50">
    <div class="max-w-[1300px] mx-auto px-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">

            {{-- Left: Text Content --}}
            <div x-data="{ shown: false }" x-intersect.once="shown = true"
                :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                class="transition-all duration-1000">

                <span class="text-accent-orange font-black uppercase tracking-[0.2em] text-xs mb-5 block">
                    {{ __('About Kimmex') }}
                </span>

                <h2 class="text-5xl md:text-6xl font-black text-gray-900 leading-[1.05] mb-8">
                    {{ __('Experience &') }}<br>{{ __('Excellence') }}
                </h2>

                <p class="text-gray-500 text-lg leading-relaxed mb-10 max-w-md whitespace-pre-line">
                    {{ $story }}
                </p>

                <a href="/about"
                    class="inline-flex items-center gap-2 text-accent-orange font-black uppercase tracking-widest text-xs hover:gap-4 transition-all duration-300 group">
                    {{ __('Learn More About Us') }}
                    <x-lucide-arrow-right class="w-4 h-4 group-hover:translate-x-1 transition-transform duration-300" />
                </a>
            </div>

            {{-- Right: 2x2 Image Grid with Badge --}}
            <div x-data="{ shown: false }" x-intersect.once="shown = true"
                :class="shown ? 'opacity-100 translate-x-0' : 'opacity-0 translate-x-12'"
                class="transition-all duration-1000 delay-200 relative">

                <div class="grid grid-cols-2 gap-4">
                    <div class="aspect-[4/3] rounded-2xl overflow-hidden shadow-md">
                        <img src="/images/projects/Thumbnail-2.jpg" alt="Project"
                            class="w-full h-full object-cover hover:scale-105 transition-transform duration-700" />
                    </div>
                    <div class="aspect-[4/3] rounded-2xl overflow-hidden shadow-md">
                        <img src="/images/projects/Thumbnail-3.jpg" alt="Project"
                            class="w-full h-full object-cover hover:scale-105 transition-transform duration-700" />
                    </div>
                    <div class="aspect-[4/3] rounded-2xl overflow-hidden shadow-md">
                        <img src="/images/projects/Thumbnail-4.jpg" alt="Project"
                            class="w-full h-full object-cover hover:scale-105 transition-transform duration-700" />
                    </div>
                    <div class="aspect-[4/3] rounded-2xl overflow-hidden shadow-md">
                        <img src="/images/projects/Thumbnail-5.jpg" alt="Project"
                            class="w-full h-full object-cover hover:scale-105 transition-transform duration-700" />
                    </div>
                </div>

                {{-- 25+ Years Badge -- centered at grid intersection --}}
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-10
                            bg-accent-orange text-white rounded-2xl shadow-xl p-5
                            flex flex-col items-center justify-center text-center min-w-[110px]">
                    <span class="text-3xl font-black leading-none">25+</span>
                    <span
                        class="text-[9px] font-black uppercase tracking-widest mt-1 leading-tight">{{ __('Years of') }}<br>{{ __('Excellence') }}</span>
                </div>

            </div>

        </div>
    </div>
</section>