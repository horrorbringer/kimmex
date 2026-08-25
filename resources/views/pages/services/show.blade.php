@php
        /** @var string $slug */
        $lang = app()->getLocale() === 'km' ? 'kh' : app()->getLocale();
        $pageTitle = $service['title'][$lang] ?? $service['title']['en'] ?? __('Service Details');
        $pageDesc = \Illuminate\Support\Str::limit(strip_tags($service['summary'][$lang] ?? $service['summary']['en'] ?? ''), 160)
            ?: __('Detailed information about Kimmex construction services.');
        $canonicalUrl = route('services.show', ['slug' => $service['id']]);

        $roadmap = [
            [
                'step' => '01',
                'icon' => 'lucide-search',
                'title' => ['en' => 'Consultation', 'kh' => 'ការប្រឹក្សា'],
                'desc' => [
                    'en' => 'Understanding your vision, budget, and feasibility analysis.',
                    'kh' => 'ការយល់ដឹងពីចក្ខុវិស័យ ថវិកា និងការវិភាគសមិទ្ធភាព។',
                ],
            ],
            [
                'step' => '02',
                'icon' => 'lucide-pen-tool',
                'title' => ['en' => 'Design & Strategy', 'kh' => 'រចនា និងយុទ្ធសាស្រ្ត'],
                'desc' => [
                    'en' => 'Creating architectural blueprints and detailed strategy.',
                    'kh' => 'ការបង្កើតប្លង់ស្ថាបត្យកម្ម និងយុទ្ធសាស្រ្តលម្អិត។',
                ],
            ],
            [
                'step' => '03',
                'icon' => 'lucide-hammer',
                'title' => ['en' => 'Construction', 'kh' => 'សាងសង់'],
                'desc' => [
                    'en' => 'Quality-controlled construction execution on-site.',
                    'kh' => 'ការអនុវត្តសាងសង់ប្រកបដោយការគ្រប់គ្រងគុណភាព។',
                ],
            ],
            [
                'step' => '04',
                'icon' => 'lucide-check-circle-2',
                'title' => ['en' => 'Handover', 'kh' => 'ប្រគល់ជូន'],
                'desc' => [
                    'en' => 'Final inspection, documentation, and key handover.',
                    'kh' => 'ការត្រួតពិនិត្យចុងក្រោយ ឯកសារ និងការប្រគល់សោ។',
                ],
            ],
        ];

        $valueProp = [
            [
                'icon' => 'lucide-handshake',
                'title' => ['en' => 'Single Point of Contact', 'kh' => 'ចំណុចទំនាក់ទំនងតែមួយ'],
                'desc' => [
                    'en' => 'Streamlined communication and accountability.',
                    'kh' => 'ការប្រាស្រ័យទាក់ទង និងការទទួលខុសត្រូវមានប្រសិទ្ធភាព។',
                ],
            ],
            [
                'icon' => 'lucide-clock',
                'title' => ['en' => 'Faster Timeline', 'kh' => 'ពេលវេលាលឿនរហ័ស'],
                'desc' => [
                    'en' => 'Overlapping design and construction phases.',
                    'kh' => 'ការត្រួតគ្នានៃដំណាក់កាលរចនា និងការសាងសង់។',
                ],
            ],
            [
                'icon' => 'lucide-dollar-sign',
                'title' => ['en' => 'Cost Certainty', 'kh' => 'ភាពប្រាកដប្រជាថ្លៃដើម'],
                'desc' => [
                    'en' => 'Reduced change orders and accurate budgeting.',
                    'kh' => 'កាត់បន្ថយការផ្លាស់ប្តូរ និងរៀបចំថវិកាបានត្រឹមត្រូវ។',
                ],
            ],
            [
                'icon' => 'lucide-shield-check',
                'title' => ['en' => 'Quality Assurance', 'kh' => 'ធានាគុណភាព'],
                'desc' => [
                    'en' => 'Professional teams ensuring design-intent alignment.',
                    'kh' => 'ក្រុមការងារប្រកបដោយវិជ្ជាជីវៈធានាបាននូវការរចនាស្របតាមគោលដៅ។',
                ],
            ],
        ];

    @endphp

<x-layouts.app :title="$pageTitle" :description="$pageDesc" :image="$service['image']" :canonical="$canonicalUrl">
    @push('head')
        <script type="application/ld+json">
            {!! json_encode([
                '@context' => 'https://schema.org',
                '@type' => 'BreadcrumbList',
                'itemListElement' => [
                    ['@type' => 'ListItem', 'position' => 1, 'name' => __('Home'), 'item' => route('home')],
                    ['@type' => 'ListItem', 'position' => 2, 'name' => __('Services'), 'item' => route('services.index')],
                    ['@type' => 'ListItem', 'position' => 3, 'name' => $pageTitle, 'item' => $canonicalUrl],
                ],
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
        </script>
    @endpush

    <div class="min-h-screen bg-slate-50 text-titan-navy">

        <!-- === 1. PREMIUM HERO === -->
        <section class="relative overflow-hidden border-b border-slate-200 bg-slate-50">
            <div class="absolute inset-0">
                @if ($service['image'])
                    <img src="{{ $service['image'] }}" alt="{{ $service['title'][$lang] }}"
                        class="w-full h-full object-cover opacity-90 scale-105" decoding="async" loading="eager" fetchpriority="high" />
                @else
                    <div
                        class="w-full h-full bg-[radial-gradient(circle_at_30%_20%,var(--color-kmd-navy)_0%,var(--color-kmd-navy)_100%)]">
                        <div class="absolute inset-0 opacity-20" style="background-image: url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%23ffffff\" fill-opacity=\"0.1\"%3E%3Cpath d=\"M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2v-4h4v-2h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2v-4h4v-2H6z\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
                    </div>
                @endif
                
                <div class="absolute inset-0 bg-gradient-to-r from-white via-white/94 to-white/45"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-white/90 via-white/35 to-white/55"></div>
            </div>

            <div class="relative z-10">
                <div class="mx-auto max-w-[1280px] px-5 pb-14 pt-28 sm:px-6 md:pb-20 md:pt-36 lg:pb-24">
                    <a href="/services"
                        class="group mb-8 inline-flex min-h-10 items-center gap-2.5 rounded !text-[10px] md:!text-xs font-black uppercase tracking-[0.22em] text-slate-500 transition-colors duration-200 hover:text-titan-red focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-titan-red focus-visible:ring-offset-4">
                        <x-lucide-arrow-left class="h-3.5 w-3.5 transition-transform duration-200 group-hover:-translate-x-1 motion-reduce:transform-none" />
                        {{ __('Back to Services') }}
                    </a>

                    <div class="grid items-center gap-10 lg:grid-cols-[1.05fr_0.95fr] lg:gap-16">
                        <div class="max-w-2xl">
                            <div class="mb-5 inline-flex items-center gap-2.5 rounded-full border border-titan-red/15 bg-white/90 px-3.5 py-1.5 shadow-sm">
                                <x-dynamic-component :component="$service['icon'] ?? 'lucide-building'" class="h-4 w-4 text-titan-red" />
                                <span class="!text-[10px] md:!text-xs font-black uppercase tracking-[0.24em] text-titan-navy/70">
                                    {{ __('Service Details') }}
                                </span>
                            </div>

                            <h1 class="mb-5 font-heading !text-2xl sm:!text-3xl md:!text-4xl lg:!text-5xl font-black leading-tight tracking-tight text-titan-navy {{ $lang === 'kh' ? 'font-khmer leading-snug' : '' }}">
                                {{ $service['title'][$lang] }}
                            </h1>

                            <x-page-view-count class="mb-5 text-titan-navy/55" />

                            <p class="max-w-xl !text-sm md:!text-base font-medium leading-relaxed text-slate-600 md:leading-relaxed">
                                {{ $service['summary'][$lang] ?? $service['summary']['en'] }}
                            </p>

                            <div class="mt-8 flex flex-wrap gap-3">
                                <a href="#scope-of-work"
                                    class="inline-flex min-h-11 items-center gap-2 rounded-lg bg-titan-red px-5 !text-xs md:!text-sm font-bold uppercase tracking-wider text-white transition-all duration-200 hover:bg-titan-navy shadow-sm hover:shadow">
                                    <x-lucide-list class="h-4 w-4" />
                                    {{ __('View Scope') }}
                                </a>
                                <a href="/contact"
                                    class="inline-flex min-h-11 items-center gap-2 rounded-lg border border-slate-300 bg-white px-5 !text-xs md:!text-sm font-bold uppercase tracking-wider text-titan-navy transition-all duration-200 hover:border-titan-navy hover:bg-titan-navy hover:!text-white shadow-sm">
                                    <x-lucide-phone class="h-4 w-4" />
                                    {{ __('Contact Us') }}
                                </a>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4">
                            <div x-data="{ 
                                previewOpen: false, 
                                shareOpen: false, 
                                copied: false,
                                copyUrl() {
                                    navigator.clipboard.writeText(window.location.href);
                                    this.copied = true;
                                    setTimeout(() => this.copied = false, 2000);
                                }
                            }" 
                            class="relative min-h-[260px] overflow-hidden rounded-2xl border border-white/80 bg-titan-navy shadow-lg sm:min-h-[320px] lg:min-h-[380px] group">
                                @if ($service['image'])
                                    <img src="{{ $service['image'] }}" alt="{{ $service['title'][$lang] }}"
                                        class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-105" loading="eager" decoding="async" />
                                    <div class="absolute inset-0 bg-gradient-to-t from-titan-navy/90 via-titan-navy/25 to-transparent"></div>
                                @else
                                    <div class="absolute inset-0 bg-[radial-gradient(circle_at_30%_20%,rgba(227,30,36,0.15)_0%,transparent_50%)]"></div>
                                @endif

                                {{-- Top Floating Actions: Preview & Share --}}
                                <div class="absolute top-4 right-4 z-20 flex items-center gap-2">
                                    @if ($service['image'])
                                        <button type="button" 
                                            @click="previewOpen = true" 
                                            class="h-8 sm:h-9 px-2.5 sm:px-3 rounded-full bg-titan-navy/70 hover:bg-titan-navy text-white backdrop-blur-md border border-white/20 text-[11px] sm:text-xs font-bold inline-flex items-center gap-1.5 transition-all shadow-md hover:scale-105 active:scale-95 cursor-pointer" 
                                            title="{{ __('Preview Cover Image') }}">
                                            <x-lucide-maximize-2 class="w-3.5 h-3.5 text-titan-red" />
                                            <span>{{ __('Preview') }}</span>
                                        </button>
                                    @endif

                                    {{-- Share Dropdown --}}
                                    <div class="relative" @click.outside="shareOpen = false">
                                        <button type="button" 
                                            @click="shareOpen = !shareOpen" 
                                            class="h-8 sm:h-9 px-2.5 sm:px-3 rounded-full bg-titan-navy/70 hover:bg-titan-navy text-white backdrop-blur-md border border-white/20 text-[11px] sm:text-xs font-bold inline-flex items-center gap-1.5 transition-all shadow-md hover:scale-105 active:scale-95 cursor-pointer"
                                            title="{{ __('Share Service') }}">
                                            <x-lucide-share-2 class="w-3.5 h-3.5 text-titan-red" />
                                            <span>{{ __('Share') }}</span>
                                        </button>

                                        {{-- Share Dropdown Menu --}}
                                        <div x-show="shareOpen" 
                                            x-transition:enter="transition ease-out duration-200"
                                            x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                                            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                            x-transition:leave="transition ease-in duration-150"
                                            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                            x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                                            class="absolute right-0 mt-2 w-48 rounded-xl bg-white/95 backdrop-blur-md p-2 shadow-xl border border-gray-100 text-titan-navy z-30 space-y-1"
                                            style="display: none;">
                                            
                                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" 
                                                target="_blank" rel="noopener"
                                                class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-semibold text-slate-700 hover:bg-social-facebook/10 hover:text-social-facebook transition-colors">
                                                <x-social-icon network="facebook" class="w-4 h-4 text-social-facebook" />
                                                <span>Facebook</span>
                                            </a>
                                            <a href="https://t.me/share/url?url={{ urlencode(url()->current()) }}&text={{ urlencode($service['title'][$lang] ?? '') }}" 
                                                target="_blank" rel="noopener"
                                                class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-semibold text-slate-700 hover:bg-social-telegram/10 hover:text-social-telegram transition-colors">
                                                <x-social-icon network="telegram" class="w-4 h-4 text-social-telegram" />
                                                <span>Telegram</span>
                                            </a>
                                            <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(url()->current()) }}" 
                                                target="_blank" rel="noopener"
                                                class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-semibold text-slate-700 hover:bg-social-linkedin/10 hover:text-social-linkedin transition-colors">
                                                <x-social-icon network="linkedin" class="w-4 h-4 text-social-linkedin" />
                                                <span>LinkedIn</span>
                                            </a>
                                            <button type="button" 
                                                @click="copyUrl()" 
                                                class="w-full flex items-center justify-between px-3 py-2 rounded-lg text-xs font-semibold text-slate-700 hover:bg-titan-red/10 hover:text-titan-red transition-colors text-left cursor-pointer">
                                                <div class="flex items-center gap-2.5">
                                                    <x-lucide-link class="w-4 h-4 text-titan-red" />
                                                    <span x-text="copied ? '{{ __('Copied!') }}' : '{{ __('Copy Link') }}'"></span>
                                                </div>
                                                <x-lucide-check x-show="copied" class="w-3.5 h-3.5 text-emerald-600" />
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="absolute bottom-0 left-0 right-0 flex items-end justify-between gap-4 p-6 md:p-7">
                                    <div>
                                        <div class="!text-[10px] md:!text-xs font-black uppercase tracking-[0.18em] text-white/70 mb-1">
                                            {{ __('Specialized Capability') }}
                                        </div>
                                        <div class="text-white font-black !text-lg sm:!text-xl md:!text-2xl tracking-tight {{ $lang === 'kh' ? 'font-khmer leading-snug' : '' }}">
                                            {{ $service['title'][$lang] }}
                                        </div>
                                    </div>
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full border border-white/20 bg-white/10 backdrop-blur-sm">
                                        <x-lucide-arrow-up-right class="w-4 h-4 text-white" />
                                    </div>
                                </div>

                                {{-- Lightbox / Full Image Preview Modal --}}
                                @if ($service['image'])
                                    <template x-teleport="body">
                                        <div x-show="previewOpen" 
                                            x-transition:enter="transition ease-out duration-300"
                                            x-transition:enter-start="opacity-0"
                                            x-transition:enter-end="opacity-100"
                                            x-transition:leave="transition ease-in duration-200"
                                            x-transition:leave-start="opacity-100"
                                            x-transition:leave-end="opacity-0"
                                            @keydown.escape.window="previewOpen = false"
                                            class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/90 backdrop-blur-md p-4 sm:p-6"
                                            style="display: none;">
                                            
                                            <div class="relative max-w-5xl w-full bg-slate-900 rounded-2xl overflow-hidden shadow-2xl border border-white/10"
                                                @click.outside="previewOpen = false">
                                                
                                                {{-- Modal Header --}}
                                                <div class="flex items-center justify-between px-5 py-4 border-b border-white/10 bg-slate-950/80">
                                                    <div class="text-white font-bold text-sm sm:text-base truncate pr-4">
                                                        {{ $service['title'][$lang] }}
                                                    </div>
                                                    <button type="button" 
                                                        @click="previewOpen = false"
                                                        class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors shrink-0 cursor-pointer">
                                                        <x-lucide-x class="w-4 h-4" />
                                                    </button>
                                                </div>

                                                {{-- Image View --}}
                                                <div class="p-2 sm:p-4 bg-black flex items-center justify-center max-h-[75vh] overflow-hidden">
                                                    <img src="{{ $service['image'] }}" alt="{{ $service['title'][$lang] }}" class="max-h-[70vh] w-auto max-w-full object-contain rounded-lg shadow-md" />
                                                </div>

                                                {{-- Modal Footer with Share links --}}
                                                <div class="px-5 py-3.5 bg-slate-950/80 border-t border-white/10 flex flex-wrap items-center justify-between gap-3 text-xs">
                                                    <span class="text-white/60 font-medium">{{ __('Share this service photo:') }}</span>
                                                    <div class="flex items-center gap-2">
                                                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" rel="noopener" class="w-8 h-8 rounded-lg bg-social-facebook text-white flex items-center justify-center hover:opacity-90 transition-opacity" title="Facebook">
                                                            <x-social-icon network="facebook" class="w-4 h-4" />
                                                        </a>
                                                        <a href="https://t.me/share/url?url={{ urlencode(url()->current()) }}&text={{ urlencode($service['title'][$lang] ?? '') }}" target="_blank" rel="noopener" class="w-8 h-8 rounded-lg bg-social-telegram text-white flex items-center justify-center hover:opacity-90 transition-opacity" title="Telegram">
                                                            <x-social-icon network="telegram" class="w-4 h-4" />
                                                        </a>
                                                        <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(url()->current()) }}" target="_blank" rel="noopener" class="w-8 h-8 rounded-lg bg-social-linkedin text-white flex items-center justify-center hover:opacity-90 transition-opacity" title="LinkedIn">
                                                            <x-social-icon network="linkedin" class="w-4 h-4" />
                                                        </a>
                                                        <button type="button" @click="copyUrl()" class="h-8 px-3 rounded-lg bg-white/10 hover:bg-white/20 text-white font-medium inline-flex items-center gap-1.5 transition-colors cursor-pointer">
                                                            <x-lucide-link class="w-3.5 h-3.5 text-titan-red" />
                                                            <span x-text="copied ? '{{ __('Copied!') }}' : '{{ __('Copy Link') }}'"></span>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- === STICKY SECTION QUICK NAVIGATION === -->
        <nav class="sticky top-20 z-30 bg-white/95 backdrop-blur-md border-b border-slate-200 shadow-sm">
            <div class="mx-auto max-w-[1280px] px-5 sm:px-6 flex items-center gap-2 overflow-x-auto py-2.5 no-scrollbar !text-[11px] md:!text-xs font-bold uppercase tracking-wider text-slate-600">
                <a href="#service-overview" class="px-3.5 py-1.5 rounded-full hover:bg-slate-100 hover:text-titan-red transition-colors whitespace-nowrap">
                    {{ __('Overview') }}
                </a>
                @if (!empty($service['scopeItems'] ?? []))
                    <a href="#scope-of-work" class="px-3.5 py-1.5 rounded-full hover:bg-slate-100 hover:text-titan-red transition-colors whitespace-nowrap">
                        {{ $lang === 'kh' ? 'វិសាលភាព' : 'Scope' }}
                    </a>
                @endif
                <a href="#process-roadmap" class="px-3.5 py-1.5 rounded-full hover:bg-slate-100 hover:text-titan-red transition-colors whitespace-nowrap">
                    {{ $lang === 'kh' ? 'ដំណើរការ' : 'Process' }}
                </a>
                <a href="#why-choose-us" class="px-3.5 py-1.5 rounded-full hover:bg-slate-100 hover:text-titan-red transition-colors whitespace-nowrap">
                    {{ $lang === 'kh' ? 'គុណតម្លៃ' : 'Why Choose Us' }}
                </a>
                @if ($featuredProjects !== [])
                    <a href="#featured-projects" class="px-3.5 py-1.5 rounded-full hover:bg-slate-100 hover:text-titan-red transition-colors whitespace-nowrap">
                        {{ __('Featured Projects') }}
                    </a>
                @endif
                <a href="/contact" class="ml-auto px-4 py-1.5 rounded-full bg-titan-red text-white hover:bg-titan-navy transition-colors whitespace-nowrap">
                    {{ __('Inquire Now') }}
                </a>
            </div>
        </nav>

        <!-- === 2. SERVICE OVERVIEW === -->
        <section id="service-overview" class="mx-auto max-w-[1280px] px-5 py-16 sm:px-6 md:py-20 scroll-mt-24 md:scroll-mt-28">
            <div class="mx-auto max-w-4xl rounded-2xl border border-slate-200 bg-white p-6 text-center shadow-sm md:p-10 lg:p-12">
                <div x-data="{ shown: false }" x-intersect.once="shown = true"
                    :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                    class="transition-all duration-700">
                    <div>
                        <div class="flex items-center justify-center gap-3 mb-3">
                            <div class="w-8 h-[2px] bg-titan-red"></div>
                            <span class="font-bold uppercase tracking-[0.2em] !text-xs text-titan-red">{{ __('Overview') }}</span>
                            <div class="w-8 h-[2px] bg-titan-red"></div>
                        </div>
                        <h2 class="mb-6 font-heading !text-xl sm:!text-2xl md:!text-3xl font-black leading-tight text-titan-navy {{ $lang === 'kh' ? 'font-khmer leading-snug' : '' }}">
                            {{ $lang === 'kh' ? 'ការកំណត់ឡើងវិញនូវ' : 'Redefining' }} {{ $service['title'][$lang] }}
                        </h2>
                        @php($serviceDescription = $service['description'][$lang] ?? $service['description']['en'] ?? '')
                        <div
                            class="service-rich-content prose prose-slate mx-auto max-w-3xl text-justify text-base leading-8 text-slate-700 md:text-lg md:leading-9">
                            @if (str_contains($serviceDescription, '<'))
                                {!! str($serviceDescription)->sanitizeHtml() !!}
                            @else
                                {!! nl2br(e($serviceDescription)) !!}
                            @endif
                        </div>
                    </div>

                    @if (!empty($service['idealFor'][$lang] ?? ''))
                        <div class="mx-auto mt-10 max-w-2xl rounded-xl border border-slate-200 border-l-4 border-l-titan-red bg-slate-50 p-5 shadow-sm md:p-6 text-left">
                            <h3 class="mb-2.5 flex items-center gap-2.5 !text-sm md:!text-base font-bold text-titan-navy {{ $lang === 'kh' ? 'font-khmer' : '' }}">
                                <div class="w-8 h-8 rounded-lg bg-titan-red/10 flex items-center justify-center text-titan-red">
                                    <x-lucide-target class="w-4 h-4" />
                                </div>
                                {{ $lang === 'kh' ? 'ស័ក្តិសមសម្រាប់' : 'Ideal For' }}
                            </h3>
                            <div class="prose prose-sm prose-slate mx-auto max-w-none text-slate-600 !text-xs md:!text-sm leading-relaxed {{ $lang === 'kh' ? 'font-khmer' : '' }}">
                                {{ $service['idealFor'][$lang] }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </section>

        <!-- === 3. SCOPE OF WORK === -->
        @if (!empty($service['scopeItems'] ?? []))
            <section id="scope-of-work" class="relative overflow-hidden border-y border-slate-200 bg-white py-16 text-titan-navy md:py-20 scroll-mt-24 md:scroll-mt-28">
                <div class="relative z-10 mx-auto max-w-[1280px] px-5 sm:px-6">
                    <div x-data="{ shown: false }" x-intersect.once="shown = true"
                        :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                        class="mx-auto mb-12 max-w-2xl text-center transition-all duration-700">
                        <div class="flex items-center justify-center gap-3 mb-3">
                            <div class="w-8 h-[2px] bg-titan-red"></div>
                            <span class="font-bold uppercase tracking-[0.2em] !text-xs text-titan-red">{{ $lang === 'kh' ? 'វិសាលភាពការងារ' : 'Scope of Work' }}</span>
                            <div class="w-8 h-[2px] bg-titan-red"></div>
                        </div>
                        <h2 class="font-heading !text-xl sm:!text-2xl md:!text-3xl font-black text-titan-navy">
                            {{ $lang === 'kh' ? 'សេវាកម្មដ៏ទូលំទូលាយ' : 'Comprehensive Coverage' }}
                        </h2>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 md:gap-5">
                        @foreach ($service['scopeItems'] as $i => $item)
                            <div x-data="{ shown: false }" x-intersect.once="shown = true"
                                style="transition-delay: {{ $i * 60 }}ms"
                                :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-6'"
                                class="group flex h-full items-start gap-4 rounded-xl border border-slate-200 bg-white p-5 shadow-sm hover:border-titan-red/30 hover:shadow-md transition-all duration-300 md:gap-4 md:p-5">
                                <div
                                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-titan-red/10 text-titan-red group-hover:bg-titan-red group-hover:!text-white transition-colors">
                                    <x-lucide-check-circle-2 class="w-4 h-4 stroke-[2.2]" />
                                </div>
                                <span
                                    class="pt-1 !text-sm md:!text-base font-bold leading-relaxed text-titan-navy group-hover:text-titan-red transition-colors {{ $lang === 'kh' ? 'font-khmer' : '' }}">{{ $item[$lang] ?? $item['en'] ?? '' }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        <!-- === 4. PROCESS / HOW WE DELIVER === -->
        <section id="process-roadmap" class="py-16 md:py-20 px-5 md:px-6 bg-slate-50 border-b border-slate-200 scroll-mt-24 md:scroll-mt-28">
            <div class="max-w-[1280px] mx-auto">
                <div x-data="{ shown: false }" x-intersect.once="shown = true"
                    :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                    class="text-center max-w-2xl mx-auto mb-12 transition-all duration-700">
                    <div class="flex items-center justify-center gap-3 mb-3">
                        <div class="w-8 h-[2px] bg-titan-red"></div>
                        <span class="font-bold uppercase tracking-[0.2em] !text-xs text-titan-red">{{ $lang === 'kh' ? 'ដំណើរការរបស់យើង' : 'Our Process' }}</span>
                        <div class="w-8 h-[2px] bg-titan-red"></div>
                    </div>
                    <h2 class="!text-xl sm:!text-2xl md:!text-3xl font-heading font-black text-titan-navy mb-3">
                        {{ $lang === 'kh' ? 'មាគ៌ាឆ្ពោះទៅរកភាពជោគជ័យ' : 'The Path to Success' }}
                    </h2>
                    <p class="text-slate-500 !text-xs sm:!text-sm md:!text-base leading-relaxed">
                        {{ $lang === 'kh' ? 'វិធីសាស្រ្តដែលមានរចនាសម្ព័ន្ធ និងតម្លាភាពដើម្បីធានាភាពជោគជ័យនៃគម្រោងរបស់អ្នក។' : 'A transparent, structured approach to ensure your project\'s success.' }}
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-5 relative z-10">
                    @foreach ($roadmap as $i => $step)
                        <div x-data="{ shown: false }" x-intersect.once="shown = true"
                            style="transition-delay: {{ $i * 80 }}ms"
                            :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-6'"
                            class="transition-all duration-500 flex flex-col">
                            <div class="bg-white rounded-xl border border-slate-200 p-6 h-full hover:border-titan-red/30 hover:shadow-md transition-all duration-300 flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center justify-between mb-5">
                                        <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-titan-red/10 text-titan-red">
                                            <x-dynamic-component :component="$step['icon']" class="w-5 h-5" stroke-width="1.8" />
                                        </div>
                                        <span class="!text-[10px] md:!text-xs font-black uppercase tracking-wider text-slate-400">
                                            {{ __('Step') }} {{ $step['step'] }}
                                        </span>
                                    </div>

                                    <h3 class="!text-base md:!text-lg font-bold text-titan-navy mb-2 leading-snug {{ $lang === 'kh' ? 'font-khmer text-base' : '' }}">
                                        {{ $step['title'][$lang] }}
                                    </h3>
                                    <p class="!text-xs md:!text-[13px] text-slate-500 leading-relaxed">
                                        {{ $step['desc'][$lang] }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- === 5. KEY BENEFITS === -->
        <section id="why-choose-us" class="max-w-6xl mx-auto py-12 md:py-16 px-5 md:px-6 scroll-mt-24 md:scroll-mt-28">
            <div x-data="{ shown: false }" x-intersect.once="shown = true"
                :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                class="text-center mb-10 md:mb-14 transition-all duration-700">
                <div class="flex items-center justify-center gap-3 mb-3">
                    <div class="w-8 h-[2px] bg-titan-red"></div>
                    <span class="font-bold uppercase tracking-[0.2em] !text-xs text-titan-red">{{ $lang === 'kh' ? 'ហេតុអ្វីជ្រើសរើសយើង' : 'Why Choose Us' }}</span>
                    <div class="w-8 h-[2px] bg-titan-red"></div>
                </div>
                <h2 class="text-xl md:text-2xl font-bold text-titan-navy">
                    {{ $lang === 'kh' ? 'គុណតម្លៃដែលផ្តល់ជូន' : 'Value Delivered' }}
                </h2>
            </div>

            <div class="grid max-w-6xl grid-cols-1 gap-5 mx-auto md:grid-cols-2 md:gap-6 xl:grid-cols-4">
                @foreach ($valueProp as $i => $benefit)
                    <div x-data="{ shown: false }" x-intersect.once="shown = true"
                        style="transition-delay: {{ $i * 80 }}ms"
                        :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-6'"
                        class="bg-white min-h-[230px] p-6 md:p-8 text-center rounded-xl shadow-sm hover:shadow-md transition-all duration-300 border border-slate-200 group h-full">
                        <div
                            class="w-12 h-12 bg-gray-50 border border-gray-100 rounded-full flex items-center justify-center mx-auto mb-7 group-hover:border-titan-red group-hover:bg-titan-red/5 transition-all duration-300">
                            <x-dynamic-component :component="$benefit['icon']"
                                class="w-6 h-6 text-titan-red" stroke-width="1.5" />
                        </div>
                        <h3
                            class="text-xl md:text-2xl font-bold text-titan-navy mb-3 group-hover:text-titan-red transition-colors {{ $lang === 'kh' ? 'font-khmer leading-snug' : '' }}">
                            {{ $benefit['title'][$lang] }}
                        </h3>
                        <p class="text-slate-600 leading-relaxed !text-xs md:!text-sm">
                            {{ $benefit['desc'][$lang] }}
                        </p>
                    </div>
                @endforeach
            </div>
        </section>

        <!-- === 6. FEATURED PROJECTS === -->
        @if ($featuredProjects !== [])
        <section id="featured-projects" class="py-14 md:py-20 bg-slate-100/70 text-titan-navy px-5 md:px-6 border-t border-slate-200 scroll-mt-24 md:scroll-mt-28">
            <div class="max-w-[1280px] mx-auto">
                <div x-data="{ shown: false }" x-intersect.once="shown = true"
                    :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                    class="flex flex-col md:!flex-row justify-between items-start md:items-end gap-5 mb-10 md:mb-12 border-b border-slate-200 pb-6 transition-all duration-700">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-8 h-[2px] bg-titan-red"></div>
                            <span class="text-titan-red font-bold uppercase tracking-widest !text-xs">{{ $lang === 'kh' ? 'ស្នាដៃ' : 'Portfolio' }}</span>
                        </div>
                        <h2 class="!text-xl sm:!text-2xl md:!text-3xl font-heading font-black text-titan-navy">{{ __('Featured Projects') }}</h2>
                    </div>
                    <a href="/projects"
                        class="px-5 py-2.5 bg-titan-navy hover:bg-titan-red text-white transition-colors font-bold uppercase tracking-wider !text-xs md:!text-sm flex items-center gap-2 rounded-lg shadow-sm">
                        {{ $lang === 'kh' ? 'មើលគម្រោងទាំងអស់' : 'View All Projects' }} <x-lucide-arrow-right
                            class="w-4 h-4 text-white" />
                    </a>
                </div>

                <div class="flex flex-wrap justify-center gap-5 md:gap-6">
                    @foreach ($featuredProjects as $i => $project)
                        <div x-data="{ shown: false }" x-intersect.once="shown = true"
                            style="transition-delay: {{ $i * 80 }}ms"
                            :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-6'"
                            class="w-full md:w-[calc(50%-0.75rem)] transition-all duration-500">
                            <a href="{{ route('projects.show', ['slug' => $project['slug']]) }}"
                                class="group relative aspect-[4/3] sm:aspect-[16/9] overflow-hidden rounded-xl cursor-pointer block shadow-sm hover:shadow-md transition-shadow h-full border border-slate-200">
                                <img src="{{ $project['image'] }}" alt="{{ $project['title'] }}"
                                    class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                    loading="lazy" decoding="async" />
                                <div
                                    class="absolute inset-0 bg-gradient-to-t from-titan-navy/95 via-titan-navy/40 to-transparent">
                                </div>

                                <div class="absolute bottom-0 left-0 p-5 md:p-6 w-full">
                                    <div>
                                        <span
                                            class="inline-block bg-titan-red text-white !text-[10px] md:!text-xs font-black uppercase tracking-widest px-2.5 py-1 rounded-md mb-2 shadow-sm">{{ $project['category'] }}</span>
                                        <h3 class="!text-base sm:!text-lg md:!text-xl font-bold !text-white mb-1.5 leading-snug">
                                            {{ $project['title'] }}
                                        </h3>
                                        <div class="flex items-center gap-1.5 text-white/80 !text-xs md:!text-sm">
                                            <x-lucide-map-pin class="w-3.5 h-3.5 text-titan-red" />
                                            {{ $project['location'] }}
                                        </div>
                                    </div>
                                </div>

                                <div
                                    class="absolute top-4 right-4 w-9 h-9 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200 border border-white/20">
                                    <x-lucide-arrow-right class="w-4 h-4 text-white" />
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
        @endif

        <!-- === FOOTER CTA === -->
        <section class="bg-slate-50 px-5 py-16 text-center sm:px-6 md:py-20">
            <div
                class="relative mx-auto max-w-3xl overflow-hidden rounded-2xl bg-titan-navy p-8 md:p-12 text-white">
                <div x-data="{ shown: false }" x-intersect.once="shown = true"
                    :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                    class="transition-all duration-700 relative z-10">
                    <h2 class="mb-3 font-heading !text-xl sm:!text-2xl md:!text-3xl font-black !text-white">
                        {{ $lang === 'kh' ? 'រួចរាល់សម្រាប់ការចាប់ផ្តើម?' : 'Ready to start?' }}
                    </h2>
                    <p class="mx-auto mb-7 max-w-xl !text-sm md:!text-base font-normal leading-relaxed text-white/75">
                        {{ $lang === 'kh' ? 'ទាក់ទងក្រុមការងារជំនាញរបស់យើងថ្ងៃនេះ សម្រាប់ការពិគ្រោះយោបល់ និងការសិក្សាសមិទ្ធភាពដោយឥតគិតថ្លៃ។' : 'Contact our expert team today for a free consultation and feasibility study.' }}
                    </p>
                    <a href="/contact"
                        class="group inline-flex items-center gap-2 rounded-lg bg-titan-red px-8 py-3.5 !text-xs md:!text-sm font-bold uppercase tracking-wider text-white transition-colors duration-200 hover:bg-white hover:!text-titan-navy">
                        {{ $lang === 'kh' ? 'ស្នើសុំការប្រឹក្សា' : 'Request Quote' }} <x-lucide-arrow-right
                            class="w-4 h-4 group-hover:translate-x-1 transition-transform" />
                    </a>
                </div>
            </div>
        </section>
    </div>

</x-layouts.app>
