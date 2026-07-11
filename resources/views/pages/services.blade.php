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
    ['@type' => 'Question', 'name' => 'Is Kimmex ISO certified?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'Yes, Kimmex is ISO 9001:2015 certified for Quality Management. We maintain strict zero-incident safety policies and 100% building code compliance on all projects.']],
]], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endpush

@php
$lang = app()->getLocale() === 'km' ? 'kh' : app()->getLocale();

$services = \Illuminate\Support\Facades\Cache::remember('services_index_data', now()->addHours(12), function() {
    $servicesDb = \App\Models\Service::where('isActive', true)->orderBy('orderIndex')->get();
    return $servicesDb->map(function($service) {
        return [
            "id" => $service->slug,
            "icon" => $service->icon ?: 'lucide-hammer',
            "title" => ["en" => $service->getTranslation('title', 'en'), "kh" => $service->getTranslation('title', 'km')],
            "desc" => [
                "en" => strip_tags($service->getTranslation('description', 'en')),
                "kh" => strip_tags($service->getTranslation('description', 'km'))
            ],
            "image" => \App\Support\PublicStorage::urlIfExists($service->image, "/images/webp/projects/Thumbnail-1.webp"),
            "features" => is_array($service->features) ? $service->features : []
        ];
    })->toArray();
});

if (empty($services)) {
    $services = [
        ["id" => "design-and-build", "icon" => "lucide-pen-tool", "title" => ["en" => "Design & Build", "kh" => "រចនា និងសាងសង់"], "desc" => ["en" => "End-to-end construction solutions from architectural design through to project completion.", "kh" => "ដំណោះស្រាយសំណង់ពីការរចនាស្ថាបត្យកម្មរហូតដល់ការបញ្ចប់គម្រោង។"], "image" => "/images/webp/projects/Thumbnail-1.webp", "features" => [["name" => "Detail Design"], ["name" => "Civil Work"], ["name" => "MEP Work"]]],
        ["id" => "construction", "icon" => "lucide-hammer", "title" => ["en" => "Construction", "kh" => "សាងសង់"], "desc" => ["en" => "Premium civil construction services across Cambodia specializing in robust concrete work.", "kh" => "សេវាកម្មសំណង់ស៊ីវិលលំដាប់ខ្ពស់។"], "image" => "/images/webp/projects/Thumbnail-2.webp", "features" => [["name" => "High-Rise Buildings"], ["name" => "Commercial Spaces"], ["name" => "Quality Assurance"]]],
        ["id" => "project-management", "icon" => "lucide-clipboard-check", "title" => ["en" => "Project Management", "kh" => "ការគ្រប់គ្រងគម្រោង"], "desc" => ["en" => "Expert oversight ensuring on-time delivery, quality control, and safety compliance.", "kh" => "ការត្រួតពិនិត្យជំនាញធានាការផ្តល់ទាន់ពេល និងសុវត្ថិភាព។"], "image" => "/images/webp/projects/Thumbnail-3.webp", "features" => [["name" => "Scheduling"], ["name" => "Quality Control"], ["name" => "Safety"]]],
        ["id" => "consultants", "icon" => "lucide-lightbulb", "title" => ["en" => "Consultants", "kh" => "ទីប្រឹក្សា"], "desc" => ["en" => "Professional consulting services including project feasibility and structural analysis.", "kh" => "សេវាកម្មប្រឹក្សាវិជ្ជាជីវៈ។"], "image" => "/images/webp/projects/Thumbnail-4.webp", "features" => [["name" => "Feasibility"], ["name" => "Design Consulting"], ["name" => "Analysis"]]],
    ];
}


$process = \Illuminate\Support\Facades\Cache::remember('services_process_array_'.app()->getLocale(), now()->addHours(12), function() {
    $processDb = \App\Models\MethodologyStep::where('isActive', true)->orderBy('orderIndex')->get();
    return $processDb->map(function($step, $index) {
        return [
            "step" => str_pad($index + 1, 2, '0', STR_PAD_LEFT),
            "icon" => $step->icon ?: 'lucide-check-circle',
            "title" => ["en" => $step->getTranslation('title', 'en'), "kh" => $step->getTranslation('title', 'km')],
            "desc" => ["en" => trim(strip_tags($step->getTranslation('description', 'en'))), "kh" => trim(strip_tags($step->getTranslation('description', 'km')))]
        ];
    })->toArray();
});

if (count($process) < 3) {
    $process = [
        ["step" => "01", "icon" => "lucide-users", "title" => ["en" => "Consultation", "kh" => "ការពិគ្រោះ"], "desc" => ["en" => "We clarify project goals and requirements.", "kh" => "យើងកំណត់គោលដៅគម្រោង។"]],
        ["step" => "02", "icon" => "lucide-ruler", "title" => ["en" => "Planning & Design", "kh" => "ការរៀបចំផែនការ"], "desc" => ["en" => "Design direction, timeline, and budget.", "kh" => "ទិសដៅរចនា កាលវិភាគ និងថវិកា។"]],
        ["step" => "03", "icon" => "lucide-hard-hat", "title" => ["en" => "Execution", "kh" => "ការអនុវត្ត"], "desc" => ["en" => "Construction moves according to plan.", "kh" => "សំណង់ដំណើរការតាមផែនការ។"]],
        ["step" => "04", "icon" => "lucide-shield-check", "title" => ["en" => "Quality Control", "kh" => "ត្រួតពិនិត្យគុណភាព"], "desc" => ["en" => "Each stage checked against standards.", "kh" => "រាល់ដំណាក់កាលត្រូវបានពិនិត្យ។"]],
        ["step" => "05", "icon" => "lucide-check-circle-2", "title" => ["en" => "Handover", "kh" => "ការប្រគល់"], "desc" => ["en" => "Final inspection and handover.", "kh" => "ការត្រួតពិនិត្យ និងប្រគល់។"]],
    ];
}

$sectors = [
    ["title" => ["en" => "Government", "kh" => "រដ្ឋាភិបាល"], "image" => "/images/webp/projects/Thumbnail-1.webp", "icon" => "lucide-landmark"],
    ["title" => ["en" => "Education", "kh" => "អប់រំ"], "image" => "/images/webp/projects/Thumbnail-2.webp", "icon" => "lucide-graduation-cap"],
    ["title" => ["en" => "Commercial", "kh" => "ពាណិជ្ជកម្ម"], "image" => "/images/webp/projects/Thumbnail-3.webp", "icon" => "lucide-building"],
    ["title" => ["en" => "Infrastructure", "kh" => "ហេដ្ឋារចនាសម្ព័ន្ធ"], "image" => "/images/webp/projects/Thumbnail-6.webp", "icon" => "lucide-route"],
];
@endphp


<div class="bg-white text-gray-900">

    <!-- ═══ HERO ═══ -->
    <section class="relative h-[380px] md:h-[440px] flex items-end overflow-hidden" style="background: #0B2B5C;">
        <div class="absolute inset-0">
            <img src="/images/webp/projects/Thumbnail-1.webp" alt="{{ __('Our Services') }}"
                class="w-full h-full object-cover opacity-40" loading="eager" decoding="async" fetchpriority="high" />
            <div class="absolute inset-0 bg-gradient-to-t from-[#071A33]/95 via-[#0B2B5C]/50 to-transparent"></div>
            <div class="absolute inset-0 bg-gradient-to-r from-[#071A33]/50 via-transparent to-transparent"></div>
        </div>
        <div class="relative z-10 w-full max-w-[1280px] mx-auto px-6 pb-12 md:pb-16">
            <nav class="flex items-center gap-2 text-xs mb-5" style="color: rgba(255,255,255,0.5);">
                <a href="/" class="hover:text-white transition-colors">{{ __('Home') }}</a>
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                <span style="color: rgba(255,255,255,0.9);">{{ __('Services') }}</span>
            </nav>
            <h1 class="font-heading font-[900] uppercase leading-[1] tracking-tight mb-4"
                style="font-size: clamp(2rem, 5vw, 3.2rem); color: #FFFFFF;">
                {{ __('Our Services') }}
            </h1>
            <p class="max-w-lg leading-relaxed" style="color: rgba(255,255,255,0.6); font-size: 1rem;">
                {{ __('Comprehensive construction and engineering solutions delivering excellence across Cambodia.') }}
            </p>
        </div>
    </section>


    <!-- ═══ SERVICES LIST ═══ -->
    <section class="py-20 md:py-28">
        <div class="max-w-[1280px] mx-auto px-6">

            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-14" x-data="{ shown: false }" x-intersect.once="shown = true"
                :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'" class="transition-all duration-1000">
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-[2px]" style="background: var(--primary-color, #E31E24);"></div>
                        <span class="font-bold uppercase tracking-[0.2em] text-xs" style="color: var(--primary-color, #E31E24);">{{ __('What We Do') }}</span>
                    </div>
                    <h2 class="text-3xl md:text-4xl font-heading font-black text-gray-900 tracking-tight">{{ __('Our Expertise') }}</h2>
                </div>
                <p class="text-gray-500 text-sm md:text-base max-w-md leading-relaxed">
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
                            class="group grid grid-cols-1 md:grid-cols-12 bg-white rounded-2xl border border-gray-100 overflow-hidden hover:border-gray-200 hover:shadow-[0_20px_60px_-15px_rgba(0,0,0,0.08)] transition-all duration-500">

                            {{-- Image --}}
                            <div class="md:col-span-5 relative h-56 md:h-auto overflow-hidden {{ $i % 2 === 1 ? 'md:order-last' : '' }}">
                                <img src="{{ $service['image'] }}" alt="{{ $service['title'][$lang] ?? '' }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" loading="lazy" decoding="async" />
                                <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent md:bg-gradient-to-{{ $i % 2 === 1 ? 'l' : 'r' }} md:from-transparent md:to-black/5"></div>
                            </div>

                            {{-- Content --}}
                            <div class="md:col-span-7 p-7 md:p-10 flex flex-col justify-center">
                                <div class="flex items-center gap-4 mb-5">
                                    <div class="w-11 h-11 rounded-xl flex items-center justify-center transition-all duration-300 group-hover:shadow-md"
                                         style="background: color-mix(in srgb, var(--primary-color, #E31E24) 10%, transparent);">
                                        <x-dynamic-component :component="$service['icon'] ?? 'lucide-hammer'" class="w-5 h-5" style="color: var(--primary-color, #E31E24);" stroke-width="1.8" />
                                    </div>
                                    <div>
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-gray-300">{{ sprintf('%02d', $i + 1) }}</span>
                                        <h3 class="text-xl md:text-2xl font-heading font-black text-gray-900 group-hover:text-titan-red transition-colors tracking-tight {{ app()->getLocale() === 'km' ? 'font-khmer' : '' }}">
                                            {{ $service['title'][$lang] ?? '' }}
                                        </h3>
                                    </div>
                                </div>

                                <p class="text-gray-500 text-sm md:text-base leading-relaxed mb-6 max-w-xl">
                                    {{ \Illuminate\Support\Str::limit($service['desc'][$lang] ?? '', 180) }}
                                </p>

                                @php $features = is_array($service['features']) ? $service['features'] : []; @endphp
                                @if(count($features) > 0)
                                    <div class="flex flex-wrap gap-2 mb-6">
                                        @foreach(array_slice($features, 0, 5) as $feature)
                                            @php $featureName = is_array($feature) ? ($feature['name'] ?? ($feature[$lang] ?? '')) : $feature; @endphp
                                            @if(!empty($featureName))
                                                <span class="text-[11px] font-semibold px-3 py-1 rounded-full bg-gray-50 text-gray-500 border border-gray-100">{{ __($featureName) }}</span>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif

                                <div class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-wider group-hover:gap-3 transition-all duration-300"
                                     style="color: var(--primary-color, #E31E24);">
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
    <section class="py-20 md:py-28 bg-gray-50 border-y border-gray-100">
        <div class="max-w-[1280px] mx-auto px-6">
            <div class="text-center mb-14" x-data="{ shown: false }" x-intersect.once="shown = true"
                :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'" class="transition-all duration-1000">
                <div class="flex items-center justify-center gap-3 mb-5">
                    <div class="w-8 h-[2px]" style="background: var(--primary-color, #E31E24);"></div>
                    <span class="font-bold uppercase tracking-[0.2em] text-xs" style="color: var(--primary-color, #E31E24);">{{ __('How We Work') }}</span>
                    <div class="w-8 h-[2px]" style="background: var(--primary-color, #E31E24);"></div>
                </div>
                <h2 class="text-3xl md:text-4xl font-heading font-black text-gray-900 tracking-tight">{{ __('Our Methodology') }}</h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-{{ min(count($process), 5) }} gap-5">
                @foreach($process as $i => $s)
                    <div x-data="{ shown: false }" x-intersect.once="shown = true"
                        style="transition-delay: {{ $i * 100 }}ms"
                        :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                        class="transition-all duration-700 group">
                        <div class="bg-white rounded-xl border border-gray-100 p-6 h-full hover:border-gray-200 hover:shadow-lg transition-all duration-300 relative overflow-hidden">
                            {{-- Large step number background --}}
                            <div class="absolute -top-2 -right-1 text-6xl font-black text-gray-50 select-none leading-none">{{ $s['step'] }}</div>

                            <div class="relative z-10">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center mb-4 transition-all duration-300 group-hover:shadow-md"
                                     style="background: color-mix(in srgb, var(--primary-color, #E31E24) 10%, transparent);">
                                    <x-dynamic-component :component="$s['icon']" class="w-4.5 h-4.5" style="color: var(--primary-color, #E31E24);" stroke-width="1.8" />
                                </div>
                                <p class="text-[10px] font-bold uppercase tracking-wider text-gray-300 mb-1.5">{{ __('Step') }} {{ $s['step'] }}</p>
                                <h3 class="text-sm font-bold text-gray-900 mb-2 group-hover:text-titan-red transition-colors {{ app()->getLocale() === 'km' ? 'font-khmer text-base' : '' }}">
                                    {{ is_array($s['title']) ? ($s['title'][$lang] ?? $s['title']['en'] ?? '') : $s['title'] }}
                                </h3>
                                <p class="text-xs text-gray-400 leading-relaxed">
                                    {{ is_array($s['desc']) ? \Illuminate\Support\Str::limit($s['desc'][$lang] ?? $s['desc']['en'] ?? '', 100) : \Illuminate\Support\Str::limit($s['desc'], 100) }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>


    <!-- ═══ SECTORS ═══ -->
    <section class="py-20 md:py-28">
        <div class="max-w-[1280px] mx-auto px-6">
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-12" x-data="{ shown: false }" x-intersect.once="shown = true"
                :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'" class="transition-all duration-1000">
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-[2px]" style="background: var(--primary-color, #E31E24);"></div>
                        <span class="font-bold uppercase tracking-[0.2em] text-xs" style="color: var(--primary-color, #E31E24);">{{ __('Industries') }}</span>
                    </div>
                    <h2 class="text-3xl md:text-4xl font-heading font-black text-gray-900 tracking-tight">{{ __('Sectors We Serve') }}</h2>
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-5">
                @foreach($sectors as $i => $sector)
                    <div x-data="{ shown: false }" x-intersect.once="shown = true"
                        style="transition-delay: {{ $i * 80 }}ms"
                        :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                        class="transition-all duration-700">
                        <div class="group relative aspect-[3/4] rounded-2xl overflow-hidden cursor-default">
                            <img src="{{ $sector['image'] }}" alt="{{ $sector['title'][$lang] ?? '' }}"
                                class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" loading="lazy" decoding="async" />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                            <div class="absolute bottom-0 left-0 right-0 p-5 md:p-6">
                                <div class="w-9 h-9 rounded-lg bg-white/10 backdrop-blur-sm flex items-center justify-center mb-3 border border-white/10">
                                    <x-dynamic-component :component="$sector['icon']" class="w-4 h-4" style="color: #FFFFFF;" stroke-width="1.8" />
                                </div>
                                <h3 class="text-sm md:text-base font-bold leading-tight {{ app()->getLocale() === 'km' ? 'font-khmer' : '' }}" style="color: #FFFFFF;">
                                    {{ $sector['title'][$lang] ?? '' }}
                                </h3>
                                <div class="w-6 h-[2px] mt-2 group-hover:w-10 transition-all duration-300" style="background: var(--primary-color, #E31E24);"></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>


    <!-- ═══ CTA ═══ -->
    <section class="py-20 md:py-24" style="background: linear-gradient(135deg, #071A33, #0B2B5C);">
        <div class="max-w-[1280px] mx-auto px-6">
            <div class="flex flex-col lg:flex-row items-center justify-between gap-10">
                <div class="text-center lg:text-left max-w-xl">
                    <h2 class="text-3xl md:text-4xl font-heading font-black leading-tight tracking-tight mb-4" style="color: #FFFFFF;">
                        {{ __('Ready to Start Your') }}
                        <span style="color: var(--primary-color, #E31E24);">{{ __('Next Project?') }}</span>
                    </h2>
                    <p class="leading-relaxed" style="color: rgba(255,255,255,0.55); font-size: 1rem;">
                        {{ __('Partner with us for engineering excellence and construction that defines the future of Cambodia.') }}
                    </p>
                </div>
                <div class="flex flex-col sm:flex-row gap-4 shrink-0">
                    <a href="/contact"
                        class="group flex items-center justify-center gap-2.5 px-8 py-4 rounded-xl font-bold uppercase tracking-wider text-sm transition-all duration-300 shadow-lg hover:shadow-xl hover:-translate-y-0.5"
                        style="background: var(--primary-color, #E31E24); color: #FFFFFF;">
                        {{ __('Get a Free Quote') }}
                        <x-lucide-arrow-right class="w-4 h-4 group-hover:translate-x-1 transition-transform" />
                    </a>
                    <a href="/projects"
                        class="flex items-center justify-center gap-2.5 px-8 py-4 rounded-xl font-bold uppercase tracking-wider text-sm transition-all duration-300"
                        style="border: 2px solid rgba(255,255,255,0.15); color: #FFFFFF;">
                        <x-lucide-folder-open class="w-4 h-4" />
                        {{ __('View Portfolio') }}
                    </a>
                </div>
            </div>
        </div>
    </section>

</div>

</x-layouts.app>
