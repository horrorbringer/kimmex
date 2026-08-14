<x-layouts.app :title="__('HTML Sitemap & Directory')" :description="__('Complete human-readable sitemap and navigation directory of all KIM MEX Construction pages, services, projects, news, careers, and resources.')">

    <div class="bg-gray-50/60 min-h-screen text-titan-navy pb-24"
         x-data="{
            searchQuery: '',
            matches(text) {
                if (!this.searchQuery.trim()) return true;
                return String(text || '').toLowerCase().includes(this.searchQuery.toLowerCase().trim());
            }
         }">

        <!-- ═══ CLEAN & LIGHT HERO SECTION ═══ -->
        <section class="bg-white border-b border-gray-200/80 pt-28 sm:pt-36 pb-12 sm:pb-16 relative">
            <div class="max-w-[1280px] mx-auto px-6">
                <!-- Breadcrumbs -->
                <nav class="flex items-center gap-2 text-xs text-gray-400 mb-5">
                    <a href="/" class="hover:text-titan-navy transition-colors">{{ __('Home') }}</a>
                    <x-lucide-chevron-right class="w-3.5 h-3.5" />
                    <span class="text-gray-700 font-semibold">{{ __('Sitemap') }}</span>
                </nav>

                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-red-50 text-titan-red text-[11px] font-bold uppercase tracking-wider mb-4 border border-red-100">
                    <x-lucide-map class="w-3.5 h-3.5" />
                    {{ __('Website Directory') }}
                </div>

                <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6">
                    <div>
                        <h1 class="font-heading font-black uppercase leading-tight text-3xl sm:text-4xl md:text-5xl text-titan-navy mb-3">
                            {{ __('Site') }} <span class="text-titan-red">{{ __('Map') }}</span>
                        </h1>
                        <p class="text-gray-500 text-sm sm:text-base max-w-2xl leading-relaxed">
                            {{ __('Explore the complete directory of KIM MEX Construction & Investment. Easily find services, engineering projects, news updates, career openings, and corporate documents.') }}
                        </p>
                    </div>

                    <!-- XML Sitemap Callout Badge -->
                    <div class="shrink-0">
                        <a href="/sitemap.xml" target="_blank"
                           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 hover:bg-gray-100 text-gray-700 text-xs font-bold transition-all group shadow-sm">
                            <x-lucide-code class="w-4 h-4 text-titan-red group-hover:rotate-12 transition-transform" />
                            <span>{{ __('XML Sitemap (For Search Engines)') }}</span>
                            <x-lucide-external-link class="w-3.5 h-3.5 text-gray-400 group-hover:text-titan-navy transition-colors" />
                        </a>
                    </div>
                </div>

                <!-- Instant Search Bar -->
                <div class="mt-8 max-w-2xl">
                    <div class="relative">
                        <x-lucide-search class="w-4 h-4 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none" />
                        <input type="text"
                               x-model="searchQuery"
                               placeholder="{{ __('Search any page, service, project, or article...') }}"
                               class="w-full h-11 pl-11 pr-24 bg-gray-50/80 border border-gray-200 rounded-xl text-sm text-gray-900 placeholder:text-gray-400 focus:outline-none focus:bg-white focus:border-titan-navy focus:ring-2 focus:ring-titan-navy/10 transition-all" />
                        <button x-show="searchQuery" @click="searchQuery = ''"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400 hover:text-gray-700 px-2 py-1 bg-gray-200 rounded-md font-bold transition-colors">
                            {{ __('Clear') }}
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- ═══ DIRECTORY SECTIONS ═══ -->
        <section class="max-w-[1280px] mx-auto px-6 mt-10">

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8 items-start">

                <!-- ── 1. MAIN & CORPORATE ── -->
                <div class="bg-white rounded-2xl border border-gray-200/80 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.03)] p-6 sm:p-7 hover:border-gray-300 hover:shadow-md transition-all"
                     x-show="matches('home about careers contact privacy sitemap legal corporate')">
                    <div class="flex items-center gap-3 pb-4 mb-5 border-b border-gray-100">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-titan-navy bg-blue-50/80 border border-blue-100/60 shrink-0">
                            <x-lucide-building-2 class="w-5 h-5" />
                        </div>
                        <div>
                            <h2 class="font-heading font-black text-base text-titan-navy uppercase tracking-wide">{{ __('Corporate & Main') }}</h2>
                            <p class="text-[11px] text-gray-400 font-medium">{{ __('Core company pages') }}</p>
                        </div>
                    </div>

                    <ul class="space-y-3 text-sm">
                        <li>
                            <a href="/" class="group flex items-center justify-between text-gray-700 hover:text-titan-red font-semibold transition-colors">
                                <span class="flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-titan-red"></span>
                                    {{ __('Home') }}
                                </span>
                                <x-lucide-arrow-up-right class="w-3.5 h-3.5 text-gray-300 group-hover:text-titan-red transition-colors" />
                            </a>
                        </li>
                        <li>
                            <a href="/about" class="group flex items-center justify-between text-gray-700 hover:text-titan-red font-semibold transition-colors">
                                <span class="flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-300 group-hover:bg-titan-red transition-colors"></span>
                                    {{ __('About Us & Company History') }}
                                </span>
                                <x-lucide-arrow-up-right class="w-3.5 h-3.5 text-gray-300 group-hover:text-titan-red transition-colors" />
                            </a>
                        </li>
                        <li>
                            <a href="/services" class="group flex items-center justify-between text-gray-700 hover:text-titan-red font-semibold transition-colors">
                                <span class="flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-300 group-hover:bg-titan-red transition-colors"></span>
                                    {{ __('Services Hub') }}
                                </span>
                                <x-lucide-arrow-up-right class="w-3.5 h-3.5 text-gray-300 group-hover:text-titan-red transition-colors" />
                            </a>
                        </li>
                        <li>
                            <a href="/projects" class="group flex items-center justify-between text-gray-700 hover:text-titan-red font-semibold transition-colors">
                                <span class="flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-300 group-hover:bg-titan-red transition-colors"></span>
                                    {{ __('Projects Portfolio') }}
                                </span>
                                <x-lucide-arrow-up-right class="w-3.5 h-3.5 text-gray-300 group-hover:text-titan-red transition-colors" />
                            </a>
                        </li>
                        <li>
                            <a href="/news" class="group flex items-center justify-between text-gray-700 hover:text-titan-red font-semibold transition-colors">
                                <span class="flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-300 group-hover:bg-titan-red transition-colors"></span>
                                    {{ __('News & Announcements') }}
                                </span>
                                <x-lucide-arrow-up-right class="w-3.5 h-3.5 text-gray-300 group-hover:text-titan-red transition-colors" />
                            </a>
                        </li>
                        <li>
                            <a href="/careers" class="group flex items-center justify-between text-gray-700 hover:text-titan-red font-semibold transition-colors">
                                <span class="flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-300 group-hover:bg-titan-red transition-colors"></span>
                                    {{ __('Careers & Opportunities') }}
                                </span>
                                <x-lucide-arrow-up-right class="w-3.5 h-3.5 text-gray-300 group-hover:text-titan-red transition-colors" />
                            </a>
                        </li>
                        @if($hasPublicDocs)
                        <li>
                            <a href="/documents" class="group flex items-center justify-between text-gray-700 hover:text-titan-red font-semibold transition-colors">
                                <span class="flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-300 group-hover:bg-titan-red transition-colors"></span>
                                    {{ __('Document Center') }}
                                </span>
                                <x-lucide-arrow-up-right class="w-3.5 h-3.5 text-gray-300 group-hover:text-titan-red transition-colors" />
                            </a>
                        </li>
                        @endif
                        <li>
                            <a href="/contact" class="group flex items-center justify-between text-gray-700 hover:text-titan-red font-semibold transition-colors">
                                <span class="flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-300 group-hover:bg-titan-red transition-colors"></span>
                                    {{ __('Contact & Inquiries') }}
                                </span>
                                <x-lucide-arrow-up-right class="w-3.5 h-3.5 text-gray-300 group-hover:text-titan-red transition-colors" />
                            </a>
                        </li>
                        <li>
                            <a href="/privacy-policy" class="group flex items-center justify-between text-gray-700 hover:text-titan-red font-semibold transition-colors">
                                <span class="flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-300 group-hover:bg-titan-red transition-colors"></span>
                                    {{ __('Privacy Policy') }}
                                </span>
                                <x-lucide-arrow-up-right class="w-3.5 h-3.5 text-gray-300 group-hover:text-titan-red transition-colors" />
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- ── 2. SERVICES & CAPABILITIES ── -->
                <div class="bg-white rounded-2xl border border-gray-200/80 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.03)] p-6 sm:p-7 hover:border-gray-300 hover:shadow-md transition-all"
                     x-show="matches('service engineering construction ' + @js(collect($services)->pluck('title')->implode(' ')))">
                    <div class="flex items-center justify-between pb-4 mb-5 border-b border-gray-100">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-titan-red bg-red-50 border border-red-100/60 shrink-0">
                                <x-lucide-wrench class="w-5 h-5" />
                            </div>
                            <div>
                                <h2 class="font-heading font-black text-base text-titan-navy uppercase tracking-wide">{{ __('Our Services') }}</h2>
                                <p class="text-[11px] text-gray-400 font-medium">{{ count($services) }} {{ __('Disciplines') }}</p>
                            </div>
                        </div>
                        <a href="/services" class="text-xs font-bold text-titan-red hover:underline">{{ __('View All') }}</a>
                    </div>

                    <ul class="space-y-3 text-sm">
                        @foreach($services as $service)
                            <li x-show="matches(@js($service['title']))">
                                <a href="{{ $service['url'] }}" class="group flex items-start justify-between gap-2 text-gray-700 hover:text-titan-red transition-colors">
                                    <span class="flex items-start gap-2">
                                        <span class="w-1.5 h-1.5 rounded-full bg-titan-red mt-1.5 shrink-0"></span>
                                        <span class="font-semibold">{{ $service['title'] }}</span>
                                    </span>
                                    <x-lucide-arrow-right class="w-3.5 h-3.5 text-gray-300 group-hover:text-titan-red transition-colors shrink-0 mt-0.5" />
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- ── 3. CAREERS & POSITIONS ── -->
                <div class="bg-white rounded-2xl border border-gray-200/80 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.03)] p-6 sm:p-7 hover:border-gray-300 hover:shadow-md transition-all"
                     x-show="matches('career job hiring position ' + @js(collect($jobs)->pluck('title')->implode(' ')))">
                    <div class="flex items-center justify-between pb-4 mb-5 border-b border-gray-100">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-emerald-600 bg-emerald-50 border border-emerald-100/60 shrink-0">
                                <x-lucide-briefcase class="w-5 h-5" />
                            </div>
                            <div>
                                <h2 class="font-heading font-black text-base text-titan-navy uppercase tracking-wide">{{ __('Careers') }}</h2>
                                <p class="text-[11px] text-gray-400 font-medium">{{ count($jobs) }} {{ __('Open Positions') }}</p>
                            </div>
                        </div>
                        <a href="/careers" class="text-xs font-bold text-titan-red hover:underline">{{ __('View All') }}</a>
                    </div>

                    @if(count($jobs) > 0)
                        <ul class="space-y-3 text-sm">
                            @foreach($jobs as $job)
                                <li x-show="matches(@js($job['title'] . ' ' . $job['dept']))">
                                    <a href="{{ $job['url'] }}" class="group flex items-start justify-between gap-2 text-gray-700 hover:text-titan-red transition-colors">
                                        <span class="flex items-start gap-2">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mt-1.5 shrink-0"></span>
                                            <div>
                                                <span class="font-semibold block leading-snug">{{ $job['title'] }}</span>
                                                <span class="text-[11px] text-gray-400">{{ $job['dept'] }} @if($job['location'])· {{ $job['location'] }}@endif</span>
                                            </div>
                                        </span>
                                        <x-lucide-arrow-right class="w-3.5 h-3.5 text-gray-300 group-hover:text-titan-red transition-colors shrink-0 mt-0.5" />
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-xs text-gray-400 italic py-2">{{ __('No active openings at this time.') }}</p>
                        <a href="/careers" class="inline-flex items-center gap-1.5 text-xs font-bold text-titan-red mt-2">
                            {{ __('Explore career opportunities') }} →
                        </a>
                    @endif
                </div>

                <!-- ── 4. PROJECTS & PORTFOLIO ── -->
                <div class="bg-white rounded-2xl border border-gray-200/80 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.03)] p-6 sm:p-7 hover:border-gray-300 hover:shadow-md transition-all md:col-span-2"
                     x-show="matches('project portfolio construction building infrastructure ' + @js(collect($projectCategories)->pluck('name')->implode(' ')))">
                    <div class="flex items-center justify-between pb-4 mb-5 border-b border-gray-100">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-amber-600 bg-amber-50 border border-amber-100/60 shrink-0">
                                <x-lucide-hard-hat class="w-5 h-5" />
                            </div>
                            <div>
                                <h2 class="font-heading font-black text-base text-titan-navy uppercase tracking-wide">{{ __('Projects Portfolio') }}</h2>
                                <p class="text-[11px] text-gray-400 font-medium">{{ __('Key developments & categorized landmarks') }}</p>
                            </div>
                        </div>
                        <a href="/projects" class="text-xs font-bold text-titan-red hover:underline">{{ __('Explore All Projects') }}</a>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($projectCategories as $pCat)
                            <div x-show="matches(@js($pCat['name']))" class="bg-gray-50/70 rounded-xl p-4 border border-gray-100">
                                <a href="{{ $pCat['url'] }}" class="font-bold text-xs uppercase tracking-wider text-titan-navy hover:text-titan-red flex items-center justify-between mb-3 group">
                                    <span class="flex items-center gap-1.5">
                                        <x-lucide-folder class="w-3.5 h-3.5 text-titan-red" />
                                        {{ $pCat['name'] }}
                                    </span>
                                    <span class="text-[10px] bg-white px-2 py-0.5 rounded-full text-gray-500 border border-gray-200">
                                        {{ count($pCat['projects']) }}
                                    </span>
                                </a>

                                <ul class="space-y-2">
                                    @foreach(collect($pCat['projects'] ?? [])->take(5) as $proj)
                                        <li x-show="matches(@js($proj['title']))">
                                            <a href="{{ $proj['url'] }}" class="text-xs text-gray-600 hover:text-titan-red font-medium flex items-center justify-between group">
                                                <span class="line-clamp-1">{{ $proj['title'] }}</span>
                                                <x-lucide-chevron-right class="w-3 h-3 text-gray-300 group-hover:text-titan-red transition-colors shrink-0" />
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- ── 5. NEWS & MEDIA ── -->
                <div class="bg-white rounded-2xl border border-gray-200/80 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.03)] p-6 sm:p-7 hover:border-gray-300 hover:shadow-md transition-all"
                     x-show="matches('news article story updates announcement media ' + @js(collect($newsArticles)->pluck('title')->implode(' ')))">
                    <div class="flex items-center justify-between pb-4 mb-5 border-b border-gray-100">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-blue-600 bg-blue-50 border border-blue-100/60 shrink-0">
                                <x-lucide-newspaper class="w-5 h-5" />
                            </div>
                            <div>
                                <h2 class="font-heading font-black text-base text-titan-navy uppercase tracking-wide">{{ __('News & Insights') }}</h2>
                                <p class="text-[11px] text-gray-400 font-medium">{{ __('Articles & Updates') }}</p>
                            </div>
                        </div>
                        <a href="/news" class="text-xs font-bold text-titan-red hover:underline">{{ __('View All') }}</a>
                    </div>

                    <!-- News Categories -->
                    @if(count($newsCategories) > 0)
                        <div class="flex flex-wrap gap-1.5 mb-4">
                            @foreach($newsCategories as $nCat)
                                <a href="{{ $nCat['url'] }}" class="text-[10px] font-bold px-2.5 py-1 rounded-md bg-gray-100 hover:bg-titan-navy hover:text-white text-gray-600 transition-colors">
                                    {{ $nCat['name'] }}
                                </a>
                            @endforeach
                        </div>
                    @endif

                    <ul class="space-y-3 text-sm">
                        @foreach(collect($newsArticles)->take(6) as $article)
                            <li x-show="matches(@js($article['title']))">
                                <a href="{{ $article['url'] }}" class="group flex items-start justify-between gap-2 text-gray-700 hover:text-titan-red transition-colors">
                                    <span class="flex items-start gap-2">
                                        <span class="w-1.5 h-1.5 rounded-full bg-titan-red mt-1.5 shrink-0"></span>
                                        <div>
                                            <span class="font-semibold line-clamp-1 block leading-snug">{{ $article['title'] }}</span>
                                            <span class="text-[10px] text-gray-400">{{ $article['date'] }} · {{ $article['category'] }}</span>
                                        </div>
                                    </span>
                                    <x-lucide-arrow-right class="w-3.5 h-3.5 text-gray-300 group-hover:text-titan-red transition-colors shrink-0 mt-0.5" />
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- ── 6. DOCUMENTS & COMPLIANCE ── -->
                @if($hasPublicDocs && count($documents) > 0)
                <div class="bg-white rounded-2xl border border-gray-200/80 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.03)] p-6 sm:p-7 hover:border-gray-300 hover:shadow-md transition-all md:col-span-3"
                     x-show="matches('document file download pdf catalogue ' + @js(collect($documents)->pluck('title')->implode(' ')))">
                    <div class="flex items-center justify-between pb-4 mb-5 border-b border-gray-100">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-purple-600 bg-purple-50 border border-purple-100/60 shrink-0">
                                <x-lucide-file-text class="w-5 h-5" />
                            </div>
                            <div>
                                <h2 class="font-heading font-black text-base text-titan-navy uppercase tracking-wide">{{ __('Document & Resource Center') }}</h2>
                                <p class="text-[11px] text-gray-400 font-medium">{{ __('Downloadable company brochures, compliance profiles & reports') }}</p>
                            </div>
                        </div>
                        <a href="/documents" class="text-xs font-bold text-titan-red hover:underline">{{ __('All Documents') }}</a>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        @foreach($documents as $doc)
                            <a href="{{ $doc['url'] }}" x-show="matches(@js($doc['title']))"
                               class="group p-3.5 rounded-xl border border-gray-100 bg-gray-50/50 hover:border-titan-red/30 hover:bg-white transition-all flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-white border border-gray-200/60 flex items-center justify-center text-gray-400 group-hover:text-titan-red transition-colors shrink-0">
                                    <x-lucide-file class="w-4 h-4" />
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-bold text-gray-900 group-hover:text-titan-red transition-colors line-clamp-1">{{ $doc['title'] }}</p>
                                    <p class="text-[10px] text-gray-400">{{ $doc['category'] }}</p>
                                </div>
                                <x-lucide-download class="w-3.5 h-3.5 text-gray-300 group-hover:text-titan-red transition-colors shrink-0" />
                            </a>
                        @endforeach
                    </div>
                </div>
                @endif

            </div>
        </section>
    </div>

</x-layouts.app>
