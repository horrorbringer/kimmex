<x-layouts.app title="Services" description="Explore our comprehensive construction and engineering services provided by Kimmex.">

@push('head')
<script type="application/ld+json">
{!! json_encode(['@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => [
    ['@type' => 'ListItem', 'position' => 1, 'name' => __('Home'), 'item' => url('/')],
    ['@type' => 'ListItem', 'position' => 2, 'name' => __('Services'), 'item' => url('/services')],
]], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
<script type="application/ld+json">
{!! json_encode(['@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => [
    ['@type' => 'Question', 'name' => 'What construction services does Kimmex provide?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'Kimmex provides comprehensive construction services including Design & Build, Civil Construction, MEP Systems (Mechanical, Electrical, Plumbing), Project Management, Infrastructure Development, and Engineering Consultancy across Cambodia.']],
    ['@type' => 'Question', 'name' => 'What is Kimmex\'s construction methodology?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'Kimmex follows a 5-step methodology: Consultation & Analysis, Planning & Design, Construction Execution, Quality Control, and Handover & Support. Each phase ensures quality, safety, and on-time delivery.']],
    ['@type' => 'Question', 'name' => 'What sectors does Kimmex serve?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'Kimmex serves Government, Education, Commercial, and Infrastructure sectors. We have completed over 150 projects including government buildings, schools, commercial complexes, and road/bridge infrastructure.']],
]], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endpush

@php
$lang = $lang ?? (app()->getLocale() === 'km' ? 'kh' : app()->getLocale());
@endphp

<div class="bg-white text-gray-900">

    <!-- ═══ 1. MODERN LIGHT HERO ═══ -->
    <section class="relative overflow-hidden border-b border-slate-200 bg-slate-50">
        <div class="absolute inset-0">
            <img src="/images/webp/projects/Thumbnail-1.webp" alt="{{ __('Our Services') }}"
                class="w-full h-full object-cover opacity-30 scale-105 object-center" style="opacity: 0.3 !important;" decoding="async" loading="eager" fetchpriority="high" />
            <div class="absolute inset-0 bg-gradient-to-r from-slate-50 via-slate-50/85 to-transparent"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-slate-50/90 via-transparent to-slate-50/40"></div>
        </div>

        <div class="relative z-10 w-full max-w-[1280px] mx-auto px-6 pb-12 pt-28 sm:px-6 md:pb-16 md:pt-36">
            {{-- Breadcrumb --}}
            <nav class="flex items-center gap-2 text-xs mb-4 text-slate-400 font-medium">
                <a href="/" class="hover:text-titan-red transition-colors">{{ __('Home') }}</a>
                <x-lucide-chevron-right class="w-3.5 h-3.5 text-slate-300" />
                <span class="text-titan-navy font-semibold">{{ __('Services') }}</span>
            </nav>

            {{-- Eyebrow --}}
            <div class="flex items-center gap-2.5 mb-2.5">
                <span class="h-[2px] w-8 bg-titan-red"></span>
                <span class="text-[10px] sm:text-xs font-bold uppercase tracking-[0.2em] text-titan-red">
                    {{ __('Capabilities & Expertise') }}
                </span>
            </div>

            {{-- Title --}}
            <h1 class="font-heading font-black text-titan-navy text-2xl sm:text-4xl md:text-5xl leading-tight tracking-tight mb-3 {{ app()->getLocale() === 'km' ? 'font-khmer leading-snug' : '' }}">
                {{ __('Our Services') }}
            </h1>

            {{-- Description --}}
            <p class="max-w-2xl text-slate-600 text-xs sm:text-sm md:text-base leading-relaxed font-normal">
                {{ __('Comprehensive construction and engineering solutions delivering excellence across Cambodia.') }}
            </p>
        </div>
    </section>

    <!-- ═══ SERVICES LIST ═══ -->
    <section class="py-16 md:py-24">
        <div class="max-w-[1280px] mx-auto px-6">

            <div x-data="{ shown: false }" x-intersect.once="shown = true"
                :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                class="transition-all duration-1000 mb-12">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-[2px] bg-titan-red"></div>
                    <span class="font-bold uppercase tracking-[0.2em] text-xs text-titan-red">{{ __('What We Do') }}</span>
                </div>
                <h2 class="text-3xl md:text-4xl font-heading font-black text-titan-navy tracking-tight mb-3">{{ __('Our Expertise') }}</h2>
                <p class="text-slate-500 text-sm md:text-base max-w-2xl leading-relaxed">
                    {{ __('From concept to completion, we bring design, construction, and project management under one accountable team.') }}
                </p>
            </div>

            <div class="space-y-6">
                @foreach($services as $i => $service)
                    <div x-data="{ shown: false }" x-intersect.once="shown = true"
                        style="transition-delay: {{ $i * 80 }}ms"
                        :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-6'"
                        class="transition-all duration-700">
                        <a href="/services/{{ $service['id'] }}"
                            class="group grid grid-cols-1 md:grid-cols-12 bg-white rounded-2xl border border-slate-200/80 overflow-hidden hover:border-titan-red/30 hover:shadow-[0_20px_50px_-15px_rgba(11,43,92,0.12)] transition-all duration-500">

                            {{-- Image --}}
                            <div class="md:col-span-5 relative h-56 md:h-auto overflow-hidden {{ $i % 2 === 1 ? 'md:order-last' : '' }}">
                                <img src="{{ $service['image'] }}" alt="{{ $service['title'][$lang] ?? '' }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" loading="lazy" decoding="async" />
                                <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent md:bg-gradient-to-{{ $i % 2 === 1 ? 'l' : 'r' }} md:from-transparent md:to-black/10"></div>
                            </div>

                            {{-- Content --}}
                            <div class="md:col-span-7 p-7 md:p-10 flex flex-col justify-center">
                                <div class="flex items-center gap-4 mb-4">
                                    <div class="w-11 h-11 rounded-xl flex items-center justify-center bg-titan-red/10 group-hover:bg-titan-red group-hover:!text-white transition-all duration-300 group-hover:shadow-md">
                                        <x-dynamic-component :component="$service['icon'] ?? 'lucide-hammer'" class="w-5 h-5 text-titan-red group-hover:!text-white transition-colors" stroke-width="1.8" />
                                    </div>
                                    <div>
                                        <span class="text-[10px] font-black uppercase tracking-wider text-slate-300">{{ sprintf('%02d', $i + 1) }}</span>
                                        <h3 class="text-xl md:text-2xl font-heading font-bold text-titan-navy group-hover:text-titan-red transition-colors tracking-tight leading-snug {{ app()->getLocale() === 'km' ? 'font-khmer' : '' }}">
                                            {{ $service['title'][$lang] ?? '' }}
                                        </h3>
                                    </div>
                                </div>

                                <p class="text-slate-600 text-sm md:text-base leading-relaxed mb-5 max-w-xl">
                                    {{ \Illuminate\Support\Str::limit($service['desc'][$lang] ?? '', 180) }}
                                </p>

                                @php $features = is_array($service['features']) ? $service['features'] : []; @endphp
                                @if(count($features) > 0)
                                    <div class="flex flex-wrap gap-2 mb-6">
                                        @foreach(array_slice($features, 0, 5) as $feature)
                                            @php $featureName = is_array($feature) ? ($feature['name'] ?? ($feature[$lang] ?? '')) : $feature; @endphp
                                            @if(!empty($featureName))
                                                <span class="text-[11px] font-semibold px-3 py-1 rounded-full bg-slate-50 text-slate-600 border border-slate-200/80 group-hover:border-titan-red/20 transition-colors">{{ __($featureName) }}</span>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif

                                <div class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-titan-red group-hover:gap-3 transition-all duration-300">
                                    {{ __('View Details') }}
                                    <x-lucide-arrow-right class="w-4 h-4" />
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- ═══ METHODOLOGY ═══ -->
    <section class="py-16 md:py-20 bg-slate-50 border-y border-slate-200">
        <div class="max-w-[1280px] mx-auto px-6">
            <div class="text-center max-w-2xl mx-auto mb-12" x-data="{ shown: false }" x-intersect.once="shown = true"
                :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'" class="transition-all duration-700">
                <div class="flex items-center justify-center gap-3 mb-3">
                    <div class="w-8 h-[2px] bg-titan-red"></div>
                    <span class="font-bold uppercase tracking-[0.2em] text-xs text-titan-red">{{ __('How We Work') }}</span>
                    <div class="w-8 h-[2px] bg-titan-red"></div>
                </div>
                <h2 class="text-3xl md:text-4xl font-heading font-black text-titan-navy tracking-tight mb-3">{{ __('Our Methodology') }}</h2>
                <p class="text-slate-500 text-sm md:text-base leading-relaxed">
                    {{ __('A disciplined 5-stage engineering workflow ensuring quality compliance, budget control, and seamless project execution.') }}
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 {{ $processGridColsClass ?? 'lg:grid-cols-5' }} gap-5">
                @foreach($process as $i => $s)
                    <div x-data="{ shown: false }" x-intersect.once="shown = true"
                        style="transition-delay: {{ $i * 80 }}ms"
                        :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-6'"
                        class="transition-all duration-500">
                        <div class="bg-white rounded-xl border border-slate-200 p-6 h-full hover:border-titan-red/30 hover:shadow-md transition-all duration-300 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between mb-5">
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-titan-red/10 text-titan-red">
                                        <x-dynamic-component :component="$s['icon']" class="w-5 h-5" stroke-width="1.8" />
                                    </div>
                                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">
                                        {{ __('Step') }} {{ $s['step'] }}
                                    </span>
                                </div>

                                <h3 class="text-base font-bold text-titan-navy mb-2 leading-snug {{ app()->getLocale() === 'km' ? 'font-khmer text-base' : '' }}">
                                    {{ is_array($s['title']) ? ($s['title'][$lang] ?? $s['title']['en'] ?? '') : $s['title'] }}
                                </h3>
                                <p class="text-xs md:text-[13px] text-slate-500 leading-relaxed">
                                    {{ is_array($s['desc']) ? \Illuminate\Support\Str::limit($s['desc'][$lang] ?? $s['desc']['en'] ?? '', 120) : \Illuminate\Support\Str::limit($s['desc'], 120) }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- ═══ SECTORS ═══ -->
    @if(!empty($sectors))
        <section class="py-16 md:py-20">
            <div class="max-w-[1280px] mx-auto px-6">
                <div x-data="{ shown: false }" x-intersect.once="shown = true"
                    :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                    class="transition-all duration-700 mb-10">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-[2px] bg-titan-red"></div>
                        <span class="font-bold uppercase tracking-[0.2em] text-xs text-titan-red">{{ __('Industries') }}</span>
                    </div>
                    <h2 class="text-3xl md:text-4xl font-heading font-black text-titan-navy tracking-tight">{{ __('Sectors We Serve') }}</h2>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-5">
                    @foreach($sectors as $i => $sector)
                        <div x-data="{ shown: false }" x-intersect.once="shown = true"
                            style="transition-delay: {{ $i * 60 }}ms"
                            :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-6'"
                            class="transition-all duration-500">
                            <div class="group relative aspect-[3/4] rounded-xl overflow-hidden cursor-default bg-titan-navy">
                                <img src="{{ $sector['image'] }}" alt="{{ $sector['title'][$lang] ?? ($sector['title']['en'] ?? '') }}"
                                    class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 opacity-90" loading="lazy" decoding="async" />
                                <div class="absolute inset-0 bg-gradient-to-t from-titan-navy/95 via-titan-navy/40 to-transparent"></div>
                                <div class="absolute bottom-0 left-0 right-0 p-5">
                                    <div class="w-8 h-8 rounded-lg bg-white/15 backdrop-blur-sm flex items-center justify-center mb-3 border border-white/20">
                                        <x-dynamic-component :component="$sector['icon']" class="w-4 h-4 text-white" stroke-width="1.8" />
                                    </div>
                                    <h3 class="!text-base md:!text-xl font-bold !text-white leading-tight {{ app()->getLocale() === 'km' ? 'font-khmer leading-snug' : '' }}">
                                        {{ $sector['title'][$lang] ?? ($sector['title']['en'] ?? '') }}
                                    </h3>
                                    <div class="w-6 h-[2px] bg-titan-red mt-2 group-hover:w-10 transition-all duration-300"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- ═══ CTA ═══ -->
    <section class="py-16 md:py-20 bg-titan-navy text-white">
        <div class="max-w-[1280px] mx-auto px-6">
            <div class="flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="text-center lg:text-left max-w-xl">
                    <h2 class="!text-xl md:!text-2xl lg:!text-3xl font-heading font-black leading-tight tracking-tight mb-3 !text-white">
                        {{ __('Ready to Start Your') }}
                        <span class="text-titan-red">{{ __('Next Project?') }}</span>
                    </h2>
                    <p class="leading-relaxed text-white/70 !text-sm md:!text-base">
                        {{ __('Partner with us for engineering excellence and construction that defines the future of Cambodia.') }}
                    </p>
                </div>
                <div class="flex flex-row gap-3.5 shrink-0">
                    <a href="/contact"
                        class="group flex items-center justify-center gap-2 px-2 md:px-10 py-3.5 rounded-lg font-bold uppercase tracking-wider !text-xs md:!text-sm bg-titan-red text-white hover:bg-white hover:!text-titan-navy transition-colors duration-200">
                        {{ __('Get a Free Quote') }}
                        <x-lucide-arrow-right class="w-4 h-4 group-hover:translate-x-1 transition-transform" />
                    </a>
                    <a href="/projects"
                        class="flex items-center justify-center gap-2 px-2 md:px-10 py-3.5 rounded-lg font-bold uppercase tracking-wider !text-xs md:!text-sm border border-white/30 text-white hover:bg-white/10 transition-colors duration-200">
                        <x-lucide-folder-open class="w-4 h-4" />
                        {{ __('View Portfolio') }}
                    </a>
                </div>
            </div>
        </div>
    </section>

</div>

</x-layouts.app>
